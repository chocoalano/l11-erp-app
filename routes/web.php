<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/format-excel-user-import', [UserController::class, 'downloadFormatExcelImport'])->name('download.user.format.excel');
Route::post('/format-excel-user-import', [UserController::class, 'importFormatExcelImport'])->name('import.user.format.excel');
