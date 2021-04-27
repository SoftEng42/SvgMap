<?php

use App\Http\Controllers\Api\FileController;
use App\Models\SvgFile;
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


Route::post('/upload', [FileController::class, 'upload']);
Route::post('/delete', [FileController::class, 'delete']);
Route::get('/list', [FileController::class, 'list']);
Route::get('/image', [FileController::class, 'image']);
