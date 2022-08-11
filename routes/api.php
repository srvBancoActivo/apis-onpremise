<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OnPremiseController;

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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('consulta/movimientos',[OnPremiseController::class,'consulta_movimientos']);
Route::post('transferencias/interna',[OnPremiseController::class,'transferencias_internas']);
Route::post('mesa-de-cambio',[OnPremiseController::class,'mesa_de_cambio']);
