<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\PdfController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get("/", [ApiController::class, 'index']);
Route::post("/compress", [PdfController::class, 'compress']);
Route::post("/merge", [PdfController::class, 'merge']);
Route::post("/image-to-pdf", [PdfController::class, 'image2Pdf']);
Route::post("/office-to-pdf", [PdfController::class, 'office2Pdf']);
Route::post("/pdf-to-images", [PdfController::class, 'pdf2Image']);
Route::post("/rotate-pdf", [PdfController::class, 'rotatePdf']);
Route::post("/html-to-pdf", [PdfController::class, 'html2Pdf']);
Route::post("/lock-pdf", [PdfController::class, 'lockPdf']);
Route::post("/unlock-pdf", [PdfController::class, 'unlockPdf']);
Route::post("/split-pdf", [PdfController::class, 'splitPdf']);
Route::post("/split-pdf-merge", [PdfController::class, 'splitPdfMerge']);
Route::post("/repair-pdf", [PdfController::class, 'repairPdf']);
Route::post("/watermark-pdf", [PdfController::class, 'watermarkPdf']);
Route::post("/watermark-pdf-image", [PdfController::class, 'watermarkPdfImage']);
