<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\PdfTemplateController;
use App\Http\Controllers\PdfExportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/home', [HomeController::class, 'index'])->name('home');
});

// PDF Template Routes
Route::resource('pdf-templates', PdfTemplateController::class);
Route::get('generate-pdf/{templateId}/invoice/{modelId}', [PdfTemplateController::class, 'generatePdf'])
    ->name('generate-pdf');

// Debug route for PDF generation
Route::get('debug-pdf/{templateId}/invoice/{modelId}', [PdfTemplateController::class, 'debugPdf'])
    ->name('debug-pdf');

// Test WKHTMLTOPDF installation
Route::get('test-wkhtmltopdf', [PdfTemplateController::class, 'testWkhtmltopdf'])
    ->name('test-wkhtmltopdf');

// DomPDF Export Routes (NEW - Primary routes)
Route::get('export-pdf/{templateId}/invoice/{modelId}', [PdfExportController::class, 'generatePdf'])
    ->name('export-pdf');

// Debug route for DomPDF export
Route::get('debug-export/{templateId}/invoice/{modelId}', [PdfExportController::class, 'debugPdf'])
    ->name('debug-export');

// Test Arabic text processing
Route::get('test-arabic', [PdfExportController::class, 'testArabicText'])
    ->name('test-arabic');