<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Models\Anggota;
use App\Models\RiwayatProses;
use Illuminate\Http\Request;
use App\Exports\PengajuanExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\Log;
use Barryvdh\DomPDF\Facade\Pdf;


class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengajuan::with(['anggota','riwayatTerakhir']);

        // ================= SEARCH =================
        if ($request->search) {
            $query->whereHas('anggota', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('cif', 'like', '%' . $request->search . '%'); // 🔥 tambah CIF
            });
        }

        // ================= FILTER STATUS =================
        if ($request->status && $request->status != '') {
            $query->where('status', strtolower($request->status)); // 🔥 lebih ringan
        }

        // ================= FILTER TANGGAL =================

// default bulan berjalan
$tanggalAwal = $request->tanggal_awal
    ?? now()->startOfMonth()->format('d/m/Y');

$tanggalAkhir = $request->tanggal_akhir
    ?? now()->endOfMonth()->format('d/m/Y');

try {

    $awal = Carbon::createFromFormat(
        'd/m/Y',
        $tanggalAwal
    )->format('Y-m-d');

    $akhir = Carbon::createFromFormat(
        'd/m/Y',
        $tanggalAkhir
    )->format('Y-m-d');

} catch (\Exception $e) {

    $awal = now()->startOfMonth()->format('Y-m-d');
    $akhir = now()->endOfMonth()->format('Y-m-d');

}

$query->whereBetween('tanggal_pengajuan', [$awal, $akhir]);

        // ================= AMBIL DATA =================
        $data = $query
    ->orderBy('tanggal_pengajuan', 'desc')
    ->paginate(10)
    ->withQueryString();

        // ================= HITUNG UMUR =================
        foreach ($data as $d) {

           $last = $d->riwayatTerakhir;

           if (in_array(strtolower($d->status), ['pencairan','ditolak','dibatalkan'])) {

    $tanggalAcuan = $last
        ? $last->tanggal
        : $d->tanggal_pengajuan;

} else {

    $tanggalAcuan = now();

}

$hari = (int) Carbon::parse($d->tanggal_pengajuan)
    ->startOfDay()
    ->diffInDays(
        Carbon::parse($tanggalAcuan)->startOfDay()
    );
            $d->hari = $hari;
            if ($hari == 0) {

    if (in_array(strtolower($d->status), ['pencairan','ditolak','dibatalkan'])) {
        $d->hari_label = '0 hari';
    } else {
        $d->hari_label = 'Hari ini';
    }

} else {

    $d->hari_label = $hari . ' hari';

}

            if ($hari <= 7) {
                $d->warna = 'success';
            } elseif ($hari <= 30) {
                $d->warna = 'warning';
            } else {
                $d->warna = 'danger';
            }

            $d->terlambat = $hari > 30;
        }

        // ================= COUNT =================
       $total = (clone $query)->count();

        $disetujui = (clone $query)
    ->where('status', 'pencairan')
    ->count();

$ditolak = (clone $query)
    ->where('status', 'ditolak')
    ->count();

        return view('pengajuan.index', compact(
    'data',
    'total',
    'disetujui',
    'ditolak',
    'tanggalAwal',
    'tanggalAkhir'
));
    }

    public function create()
    {
        return view('pengajuan.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'cif' => 'required',
        'nama' => 'required',
        'tanggal_pengajuan' => 'required|date_format:d/m/Y',
        'jumlah' => 'required|numeric',
        'jenis' => 'required',
        'tujuan' => 'required',
    ]);

    // ================= ANGGOTA =================
    $anggota = Anggota::firstOrCreate(
        ['cif' => $request->cif],
        [
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'kelurahan' => $request->kelurahan,
            'kecamatan' => $request->kecamatan,
        ]
    );

    $anggota->update([
        'nama' => $request->nama,
        'alamat' => $request->alamat,
        'kelurahan' => $request->kelurahan,
        'kecamatan' => $request->kecamatan,
    ]);

    // ================= PENGAJUAN =================
    $pengajuan = Pengajuan::create([
        'anggota_id' => $anggota->id,
        'tanggal_pengajuan' => Carbon::createFromFormat('d/m/Y', $request->tanggal_pengajuan)->format('Y-m-d'),
        'jumlah_pengajuan' => $request->jumlah,
        'jenis_pinjaman' => $request->jenis,
        'tujuan_pinjaman' => $request->tujuan,
        'status' => 'pengajuan masuk'
    ]);

    // ================= RIWAYAT =================
    RiwayatProses::create([
        'pengajuan_id' => $pengajuan->id,
        'tahap' => 'pengajuan masuk',
        'tanggal' => now(),
        'keterangan' => 'Pengajuan dibuat',
        'user_id' => auth()->id()
    ]);

    // ================= LOG ================= ✅ TARUH DI SINI
    Log::create([
        'user_id' => auth()->id(),
        'aktivitas' => 'Menambah pengajuan CIF: ' . $anggota->cif
    ]);

    return redirect()->route('pengajuan.index')
        ->with('success', 'Pengajuan berhasil dibuat');
}

    public function detail($id)
    {
        $data = Pengajuan::with(['anggota','riwayat'])->findOrFail($id);

        $riwayat = RiwayatProses::where('pengajuan_id', $id)
            ->orderBy('tanggal', 'asc')
            ->get();

        $status = strtolower(trim($data->status));

        $tahapan = [
            'pengajuan masuk',
            'berkas',
            'survey',
            'rapat',
            'persetujuan',
            'pencairan'
        ];

        $currentIndex = array_search($status, $tahapan);

        $tahap_berikutnya = null;

        if ($currentIndex !== false && $currentIndex < count($tahapan) - 1) {
            $tahap_berikutnya = $tahapan[$currentIndex + 1];
        }

        if (in_array($status, ['ditolak','dibatalkan','pencairan'])) {
            $tahap_berikutnya = null;
        }

        // ================= STAT =================
        $total = Pengajuan::count();

        $disetujui = Pengajuan::where('status', 'pencairan')->count();

        $ditolak = Pengajuan::where('status', 'ditolak')->count();

        return view('pengajuan.detail', compact(
            'data','riwayat','tahap_berikutnya',
            'total','disetujui','ditolak'
        ));
    }

    public function updateProses(Request $request)
{
    $request->validate([
        'pengajuan_id' => 'required',
        'tahap' => 'required',
        'tanggal' => 'required|date_format:d/m/Y',
        'keterangan' => 'required',
        'keputusan' => 'nullable',
        'nilai_disetujui' => 'nullable'
    ]);

    $pengajuan = Pengajuan::findOrFail($request->pengajuan_id);

    // ================= FINAL =================
    if (in_array($pengajuan->status, [
        'pencairan',
        'ditolak',
        'dibatalkan'
    ])) {
        return back()->with('error', 'Proses sudah final');
    }

    $tanggal = Carbon::createFromFormat(
        'd/m/Y',
        $request->tanggal
    )->format('Y-m-d');

    // ================= RAPAT =================
    if ($pengajuan->status == 'rapat') {

        // ================= DITOLAK =================
        if ($request->keputusan == 'ditolak') {

            RiwayatProses::create([
                'pengajuan_id' => $pengajuan->id,
                'tanggal' => $tanggal,
                'tahap' => 'ditolak',
                'keterangan' => 'Permohonan ditolak saat rapat kredit',
                'user_id' => auth()->id()
            ]);

            $pengajuan->update([
                'status' => 'ditolak'
            ]);

            return back()->with('success', 'Pengajuan ditolak');
        }

        // ================= DISETUJUI =================
        if ($request->keputusan == 'pencairan') {

            $nilai = str_replace('.', '', $request->nilai_disetujui);

            // update nilai
            $pengajuan->update([
                'status' => 'persetujuan',
                'nilai_disetujui' => $nilai
            ]);

            // timeline rapat
            RiwayatProses::create([
                'pengajuan_id' => $pengajuan->id,
                'tanggal' => $tanggal,
                'tahap' => 'rapat',
               'keterangan' =>
    'Permohonan disetujui pada rapat tim kredit | Nilai Disetujui: Rp ' .
    number_format($nilai, 0, ',', '.'),
                'user_id' => auth()->id()
            ]);

            return back()->with('success', 'Pengajuan disetujui');
        }
    }

    // ================= PERSETUJUAN =================
    if ($pengajuan->status == 'persetujuan') {

        // anggota menolak
        if ($request->keputusan == 'ditolak') {

            RiwayatProses::create([
                'pengajuan_id' => $pengajuan->id,
                'tanggal' => $tanggal,
                'tahap' => 'dibatalkan',
                'keterangan' => 'Anggota menolak hasil keputusan kredit',
                'user_id' => auth()->id()
            ]);

            $pengajuan->update([
                'status' => 'dibatalkan'
            ]);

            return back()->with('success', 'Pengajuan dibatalkan');
        }

        // anggota setuju
if ($request->keputusan == 'pencairan') {

    // timeline persetujuan
    RiwayatProses::create([
        'pengajuan_id' => $pengajuan->id,
        'tanggal' => $tanggal,
        'tahap' => 'persetujuan',
        'keterangan' => 'Anggota menyetujui hasil keputusan kredit',
        'user_id' => auth()->id()
    ]);

    // timeline pencairan
    RiwayatProses::create([
        'pengajuan_id' => $pengajuan->id,
        'tanggal' => $tanggal,
        'tahap' => 'pencairan',
        'keterangan' => 'Kredit berhasil dicairkan',
        'user_id' => auth()->id()
    ]);

    $pengajuan->update([
        'status' => 'pencairan'
    ]);

    return back()->with('success', 'Kredit berhasil dicairkan');
}
}

    // ================= NORMAL FLOW =================
    RiwayatProses::create([
        'pengajuan_id' => $pengajuan->id,
        'tanggal' => $tanggal,
        'tahap' => $request->tahap,
        'keterangan' => $request->keterangan,
        'user_id' => auth()->id()
    ]);

    $pengajuan->update([
        'status' => $request->tahap
    ]);

    return back()->with('success', 'Proses berhasil disimpan');
}

    public function export(Request $request)
{
    return Excel::download(
        new PengajuanExport($request),
        'laporan_pengajuan.xlsx'
    );
}
public function exportPdf(Request $request)
{
    $query = Pengajuan::with('anggota');

    // FILTER TANGGAL
    if ($request->tanggal_awal && $request->tanggal_akhir) {

        $awal = Carbon::createFromFormat(
            'd/m/Y',
            $request->tanggal_awal
        )->format('Y-m-d');

        $akhir = Carbon::createFromFormat(
            'd/m/Y',
            $request->tanggal_akhir
        )->format('Y-m-d');

        $query->whereBetween('tanggal_pengajuan', [$awal, $akhir]);
    }

    $data = $query
        ->orderBy('tanggal_pengajuan', 'desc')
        ->get();

    $pdf = Pdf::loadView('pengajuan.pdf', [
        'data' => $data,
        'tanggal_awal' => $request->tanggal_awal,
        'tanggal_akhir' => $request->tanggal_akhir,
    ]);

    return $pdf->download('laporan_pengajuan.pdf');
}

public function edit($id)
{
    $data = Pengajuan::with('anggota')->findOrFail($id);
    return view('pengajuan.edit', compact('data'));
}
public function update(Request $request, $id)
{
    $pengajuan = Pengajuan::findOrFail($id);

    // 🔴 CEGAH EDIT JIKA SUDAH FINAL
   if (in_array($pengajuan->status, [
    'pencairan',
    'ditolak',
    'dibatalkan'
])) {
        return back()->with('error', 'Data tidak bisa diedit');
    }

    // ✅ VALIDASI
    $request->validate([
        'tanggal_pengajuan' => 'required',
        'jumlah' => 'required|numeric',
        'jenis' => 'required',
        'tujuan' => 'required',
    ]);

    // ✅ UPDATE DATA
    $pengajuan->update([
        'tanggal_pengajuan' => Carbon::createFromFormat(
    'd/m/Y',
    $request->tanggal_pengajuan
)->format('Y-m-d'),
        'jumlah_pengajuan' => $request->jumlah,
        'jenis_pinjaman' => $request->jenis,
        'tujuan_pinjaman' => $request->tujuan,
    ]);

    return redirect()->route('pengajuan.index')
        ->with('success', 'Data berhasil diupdate');
}

public function cekAnggota($cif)
{
    $anggota = Anggota::where('cif', $cif)->first();

    if (!$anggota) {
        return response()->json(null, 404);
    }

    return response()->json($anggota);
}

}