<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MasterDataController;
use App\Http\Controllers\Api\MahasiswaController;
use App\Http\Controllers\Api\KrsController;
use App\Http\Controllers\Api\KhsController;
use App\Http\Controllers\Api\PembayaranUktController;

Route::prefix('v1')->group(function () {

    // Dashboard SIAKAD
    Route::get('/dashboard', [
        DashboardController::class,
        'index',
    ]);

    // Master data: daftar, detail, tambah, ubah, hapus
    Route::get('/master/{resource}', [
        MasterDataController::class,
        'index',
    ]);

    Route::post('/master/{resource}', [
        MasterDataController::class,
        'store',
    ]);

    Route::get('/master/{resource}/{id}', [
        MasterDataController::class,
        'show',
    ]);

    Route::patch('/master/{resource}/{id}', [
        MasterDataController::class,
        'update',
    ]);

    Route::delete('/master/{resource}/{id}', [
        MasterDataController::class,
        'destroy',
    ]);

    // Mahasiswa
    Route::get('/mahasiswa', [
        MahasiswaController::class,
        'index',
    ]);

    Route::post('/mahasiswa', [
        MahasiswaController::class,
        'store',
    ]);

    Route::get('/mahasiswa/{npm}', [
        MahasiswaController::class,
        'show',
    ]);

    Route::patch('/mahasiswa/{npm}', [
        MahasiswaController::class,
        'update',
    ]);

    // KRS
    Route::get('/krs', [
        KrsController::class,
        'index',
    ]);

    Route::post('/krs', [
        KrsController::class,
        'store',
    ]);

    Route::get('/krs/{krs}', [
        KrsController::class,
        'show',
    ]);

    Route::post('/krs/{krs}/detail', [
        KrsController::class,
        'addDetail',
    ]);

    Route::delete('/krs/{krs}/detail/{detail}', [
        KrsController::class,
        'removeDetail',
    ]);

    Route::patch('/krs/{krs}/status', [
        KrsController::class,
        'updateStatus',
    ]);

    // KHS
    Route::get('/khs/{npm}/{kodeSemester}', [
        KhsController::class,
        'show',
    ]);

    // Pembayaran UKT
    Route::get('/pembayaran-ukt', [
        PembayaranUktController::class,
        'index',
    ]);

    Route::post('/pembayaran-ukt', [
        PembayaranUktController::class,
        'store',
    ]);

    Route::patch('/pembayaran-ukt/{id}', [
        PembayaranUktController::class,
        'update',
    ]);
});