<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
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

//No authorization
Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthenticationController::class, 'login']);
    Route::post('/logout', [AuthenticationController::class, 'logout']);
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/{book_id}', [BookController::class, 'show'])->name('books.show');
    Route::get('books/search/{criteria}/{query}', [BookController::class, 'index_filtered'])->name('books.show_filtered');
});

//Admin
Route::prefix('v1')->middleware(['auth:sanctum', 'ability:Administrator'])->group(function (){
    Route::post('/users', [UserController::class, 'register'])->name('users.register');
    Route::delete('/users/{user_id}', [UserController::class, 'delete'])->name('users.delete');


});

//Librarian
Route::prefix('v1')->middleware(['auth:sanctum', 'ability:Librarian'])->group(function (){
    Route::post('/books', [BookController::class, 'store'])->name('books.store');
    Route::put('/books/{book_id}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book_id}', [BookController::class, 'destroy'])->name('books.destroy');
    Route::get('/books/give/{book_id}/{user_id}', [BookController::class, 'give'])->name('books.give');
    Route::get('/books/take/{book_id}/{user_id}', [BookController::class, 'take'])->name('books.take');

});

//User
Route::prefix('v1')->middleware(['auth:sanctim', 'ability:User'])->group(function (){
    Route::get('/books/reserve/{book_id}', [BookController::class, 'reserve'])->name('books.reserve');
    Route::get('/books/unreserve/{book_id}', [BookController::class, 'unreserve'])->name('books.unreserve');
});

