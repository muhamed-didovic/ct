<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('home');
//});

//Auth::routes();
Route::get('/', 'ProductsController@index')->name('products.index');
Route::get('/list', 'ProductsController@list')->name('products.list');
Route::post('/products', 'ProductsController@store')->name('products.store');
//Route::get('/home', 'HomeController@index')->name('home');

