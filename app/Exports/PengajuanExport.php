<?php

namespace App\Exports;

use App\Models\Pengajuan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PengajuanExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Pengajuan::with(['anggota','riwayatTerakhir']);

        // ================= SEARCH =================
        if ($this->request->search) {
            $query->whereHas('anggota', function ($q) {
                $q->where('nama', 'like', '%' . $this->request->search . '%')
                  ->orWhere('cif', 'like', '%' . $this->request->search . '%');
            });
        }

        // ================= STATUS =================
        if ($this->request->status) {
            $query->where('status', strtolower($this->request->status));
        }

        // ================= TANGGAL =================
        if ($this->request->tanggal_awal && $this->request->tanggal_akhir) {

    $awal = Carbon::createFromFormat(
    'd/m/Y',
    $this->request->tanggal_awal
)->format('Y-m-d');

$akhir = Carbon::createFromFormat(
    'd/m/Y',
    $this->request->tanggal_akhir
)->format('Y-m-d');

    $query->whereBetween('tanggal_pengajuan', [$awal, $akhir]);
}
        return $query->get()->map(function ($p) {

            // ================= TGL PENGAJUAN =================
            $tglPengajuan = $p->tanggal_pengajuan
                ? Carbon::parse($p->tanggal_pengajuan)->format('d/m/Y')
                : '-';

            // ================= TGL SELESAI =================
            $tglSelesai = '-';

           if (in_array(strtolower($p->status), ['pencairan','ditolak','dibatalkan'])) {
                $tglSelesai = optional($p->riwayatTerakhir)->tanggal
                    ? Carbon::parse($p->riwayatTerakhir->tanggal)->format('d/m/Y')
                    : '-';
            }

           // ================= UMUR =================
if (in_array(strtolower($p->status), ['pencairan','ditolak','dibatalkan'])) {

    $tanggalAcuan = optional($p->riwayatTerakhir)->tanggal
        ?? $p->tanggal_pengajuan;

} else {

    $tanggalAcuan = now();

}

$hari = (int) Carbon::parse($p->tanggal_pengajuan)
    ->startOfDay()
    ->diffInDays(
        Carbon::parse($tanggalAcuan)->startOfDay()
    );

$umur = $hari == 0
    ? 'Hari ini'
    : $hari . ' hari';

return [
    "'" . ($p->anggota->cif ?? '-'),
    $p->anggota->nama ?? '-',
    $tglPengajuan,
    $tglSelesai,
    $p->jumlah_pengajuan,
    $this->label($p->status),
    $umur,
];

        });
    }

    // ================= HEADER =================
    public function headings(): array
{
    $periode = '';

    if ($this->request->tanggal_awal && $this->request->tanggal_akhir) {

        try {

            $awal = Carbon::createFromFormat(
    'd/m/Y',
    $this->request->tanggal_awal
)->format('d/m/Y');

$akhir = Carbon::createFromFormat(
    'd/m/Y',
    $this->request->tanggal_akhir
)->format('d/m/Y');

            $periode = 'Tanggal ' . $awal . ' s.d ' . $akhir;

        } catch (\Exception $e) {

            $periode = '';

        }

    }

    return [
        ['KOPERASI CU BETANG ASI'],
        ['KANTOR CABANG TAHASAK BATU SEPAN'],
        ['LAPORAN PENGAJUAN KREDIT DIATAS SIMPANAN'],
        [$periode],
        [],
        [
            'CIF',
            'Nama',
            'Tgl Pengajuan',
            'Tgl Selesai',
            'Jumlah',
            'Status',
            'Umur'
        ]
    ];
}

    // ================= STYLE =================
    public function styles(Worksheet $sheet)
    {
        // MERGE JUDUL
       $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');
        $sheet->mergeCells('A3:G3');
        $sheet->mergeCells('A4:G4');

        // STYLE JUDUL
      $sheet->getStyle('A1:G4')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => 'center',
            ],
        ]);

        // HEADER TABLE
       $sheet->getStyle('A6:G6')->applyFromArray([
            'font' => ['bold' => true],
        ]);

        // FORMAT RUPIAH
      $sheet->getStyle('E7:E1000')
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        return [];
    }

    private function label($status)
    {
        $map = [
            'pengajuan masuk' => 'Pengajuan Masuk',
            'berkas' => 'Berkas Lengkap',
            'survey' => 'Survey Lapangan',
            'rapat' => 'Rapat Tim Kredit',
            'persetujuan' => 'Persetujuan Anggota',
            'pencairan' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'dibatalkan' => 'Dibatalkan'
        ];

        return $map[strtolower($status)] ?? $status;
    }
}