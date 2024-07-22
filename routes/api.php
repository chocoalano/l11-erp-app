<?php

use App\Http\Controllers\Api\AboutUsController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\AwardController;
use App\Http\Controllers\Api\CarouselController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\JobPositionController;
use App\Http\Controllers\Api\MetaSeoController;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReasonController;
use App\Http\Controllers\Api\SosmedController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ValuesController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'compro'], function () {
    Route::get('seo/{page}', [MetaSeoController::class, 'show']);
    Route::get('about-us', [AboutUsController::class, 'showAllActive']);
    Route::get('about-us/introduction', [AboutUsController::class, 'showIntroductionActive']);
    Route::get('carousel', [CarouselController::class, 'showAllActive']);
    Route::get('values', [ValuesController::class, 'showAllActive']);
    Route::get('product', [ProductController::class, 'showAllActive']);
    Route::get('product/{slug}', [ProductController::class, 'showFromSlug']);
    Route::get('reason', [ReasonController::class, 'showAllActive']);
    Route::get('article', [ArticleController::class, 'showAllActive']);
    Route::get('awards', [AwardController::class, 'showAllActive']);
    Route::get('awards/{slug}', [AwardController::class, 'showFromSlug']);
    Route::get('certificate', [CertificateController::class, 'showAllActive']);
    Route::get('certificate/{slug}', [CertificateController::class, 'showFromSlug']);
    Route::get('partner', [PartnerController::class, 'showAllActive']);
    Route::get('sosmed', [SosmedController::class, 'showAllActive']);
    Route::get('contact', [SosmedController::class, 'showAllContactActive']);
});
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthenticationController::class, 'login']);
    Route::get('profile', [AuthenticationController::class, 'profile'])->middleware('auth:sanctum');
    Route::post('profile', [AuthenticationController::class, 'profile_update'])->middleware('auth:sanctum');
    Route::post('logout', [AuthenticationController::class, 'logout'])->middleware('auth:sanctum');
});
Route::middleware('auth:sanctum')->group( function () {
    Route::resource('/attendance', AttendanceController::class);
});
Route::resource('/users', UserController::class);
Route::resource('/organizations', OrganizationController::class);
Route::resource('/job-position', JobPositionController::class);
Route::post('sync-attendance', [AttendanceController::class, 'integratedFromMachine']);
