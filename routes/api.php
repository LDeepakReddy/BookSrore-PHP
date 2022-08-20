<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\UserController;
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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::post('forgotPassword', [PasswordController::class, 'forgotPassword']);
Route::group(['middleware' => ['jwt.verify']], function () {
    Route::get('logout', [UserController::class, 'logout']);
    Route::get('get_user', [UserController::class, 'get_user']);
    Route::post('resetPassword', [PasswordController::class, 'resetPassword']);

    
    Route::post('addingBook', [BookController::class, 'addingBook']);
    Route::post('updateBookById', [BookController::class, 'updateBookById']);
    Route::post('deleteBookById', [BookController::class, 'deleteBookById']);
    Route::post('addQuantityToExistingBook', [BookController::class, 'addQuantityToExistingBook']);
    Route::get('displayAllBooks', [BookController::class, 'displayAllBooks']);
    Route::get('sortPriceLowToHigh', [BookController::class, 'sortPriceLowToHigh']);
    Route::get('sortPriceHighToLow', [BookController::class, 'sortPriceHighToLow']);
    Route::post('searchBookByKeyword', [BookController::class, 'searchBookByKeyword']);

    Route::post('addBookToCartByBookId', [CartController::class, 'addBookToCartByBookId']);
    Route::post('deleteBookByCartId', [CartController::class, 'deleteBookByCartId']);
    Route::get('getAllBooksInCart', [CartController::class, 'getAllBooksInCart']);
    Route::post('increamentBookQuantityInCart', [CartController::class, 'increamentBookQuantityInCart']);
    Route::post('decrementBookQuantityInCart', [CartController::class, 'decrementBookQuantityInCart']);
    
});