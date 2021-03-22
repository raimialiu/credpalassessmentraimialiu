<?php

use App\Http\Controllers\exchangerate;
use App\Http\Controllers\LoanController;
use App\Http\Middleware\authenticate;
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

Route::prefix('user')->group(function(){
    Route::post('create', [exchangerate::class, 'RegisterNewUser']);
});

Route::prefix('exchange')->middleware(authenticate::class)->group(function(){
    Route::post('/latest', [exchangerate::class, 'GetExchangeRate']);
    Route::get('/notauthorized', function($request){
        return response(json_encode(['status'=>false, 'message'=>'unauthorized']), 401)
                ->header('Content-Type', 'application/json');
    })->name('notallowed')->withoutMiddleware(authenticate::class);
});

Route::prefix('loan')->group(function(){

    Route::post('/repayment', [LoanController::class, 'GetRepaymentSchedule']);

});
