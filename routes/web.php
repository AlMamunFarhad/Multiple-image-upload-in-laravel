<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TempImageController;
use App\Http\Controllers\ProductImageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/products', [ProductController::class, 'index'])->name('products');
Route::get('/create/product', [ProductController::class, 'create'])->name('create.product');
Route::post('/store/product', [ProductController::class, 'store'])->name('store.product');
Route::get('/product/edit/{product}', [ProductController::class, 'edit'])->name('edit.product');
Route::post('/product/update/{product}', [ProductController::class, 'update'])->name('update.product');
Route::post('/temp-images', [TempImageController::class, 'store'])->name('temp.imagas.create');
Route::post('/product-images', [ProductImageController::class, 'store'])->name('product.imagas.create');
Route::delete('/product-images/{image}', [ProductImageController::class, 'destroy'])->name('product.imagas.delete');