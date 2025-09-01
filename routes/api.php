<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// PDF Export API Routes
Route::middleware('auth:api')->group(function () {
    Route::get('export-pdf/{templateId}/invoice/{modelId}', [App\Http\Controllers\PdfExportController::class, 'generatePdf']);
    Route::get('debug-export/{templateId}/invoice/{modelId}', [App\Http\Controllers\PdfExportController::class, 'debugPdf']);
});
