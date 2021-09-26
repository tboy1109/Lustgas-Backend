<?php

use App\Http\Controllers\ProductsController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::middleware('auth:sanctum')->prefix('v1')->group(function(){
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 321 page for exceptiong handling
    // Products
    // do we pull product by id? product:id?
    // Route::apiResource('authors', AuthorsController::class);
    // Route::get('/products', [ProductsController::class, 'index']);
    // // Route::get('/products', [ProductsController::class, 'update']);
    // Route::post('/products', [ProductsController::class, 'store']);
    // Route::get('/products/{product}', [ProductsController::class, 'show']);

    Route::apiResource('products', ProductsController::class);

});
