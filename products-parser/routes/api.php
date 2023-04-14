<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Models\ImportsHistoric;

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

Route::resource('products', ProductController::class);

//API/Cron Details
Route::get('/', function () {
    $historic = ImportsHistoric::whereNotNull('log')->orderByDesc('updated_at')->first();
    return [
        'API Version' => 1,
        'Crontab Log' => $historic->log,
        'Crontab Last Execution' => date_format($historic->updated_at, 'd-m-Y H:i:s'),
    ];
});