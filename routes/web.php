<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnggotaController;

/*
|--------------------------------------------------------------------------
| REDIRECT AWAL
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| PENGAJUAN - VIEW
|--------------------------------------------------------------------------
*/

Route::middleware(['auth','role:admin,staf,pengawas'])->group(function () {

    Route::get('/pengajuan', [PengajuanController::class, 'index'])
        ->name('pengajuan.index');

    Route::get('/detail/{id}', [PengajuanController::class, 'detail'])
        ->name('pengajuan.detail');

    Route::get('/export', [PengajuanController::class, 'export'])
        ->name('pengajuan.export');
        
    Route::get('/export-pdf', [PengajuanController::class, 'exportPdf'])
        ->name('pengajuan.exportPdf');
});

/*
|--------------------------------------------------------------------------
| PENGAJUAN - INPUT & UPDATE
|--------------------------------------------------------------------------
*/

Route::middleware(['auth','role:admin,staf'])->group(function () {

    Route::get('/create', [PengajuanController::class, 'create'])
        ->name('pengajuan.create');

    Route::post('/store', [PengajuanController::class, 'store'])
        ->name('pengajuan.store');

    Route::post('/update-proses', [PengajuanController::class, 'updateProses'])
        ->name('pengajuan.update');

    Route::get('/pengajuan/{id}/edit', [PengajuanController::class, 'edit'])
        ->name('pengajuan.edit');

    Route::put('/pengajuan/{id}', [PengajuanController::class, 'update'])
        ->name('pengajuan.updateData');

});
/*
|--------------------------------------------------------------------------
| USER MANAGEMENT (ADMIN ONLY)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth','role:admin'])->group(function () {

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');

    Route::get('/users/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users/update/{id}', [UserController::class, 'update'])->name('users.update');

    Route::get('/users/delete/{id}', [UserController::class, 'delete'])->name('users.delete');
});

/*
|--------------------------------------------------------------------------
| AUTH (LOGIN, LOGOUT, DLL)
|--------------------------------------------------------------------------
*/
Route::get('/anggota/create', function () {
    return view('anggota.create');
})->name('anggota.create');

Route::get('/search-anggota', function (\Illuminate\Http\Request $request) {
    $q = $request->q;

    return \App\Models\Anggota::where('cif', 'like', "%$q%")
        ->orWhere('nama', 'like', "%$q%")
        ->limit(5)
        ->get();
});

Route::get('/anggota/create', [AnggotaController::class, 'create'])->name('anggota.create');
Route::post('/anggota/store', [AnggotaController::class, 'store'])->name('anggota.store');

require __DIR__.'/auth.php';

Route::get('/cek-anggota/{cif}', [PengajuanController::class, 'cekAnggota']);
