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

Route::get('/', function () {
    return view('pages.home');
});

Route::get('/search', function () {
    return view('search');
});

Route::get('/upload', function () {
    return view('upload');
});

Route::post('/document', '\App\Http\Controllers\Document\Upload@upload')->name('upload-document');

Route::get('/document/search/{id}', '\App\Http\Controllers\Document\Download@download')->name('download-document');

Route::get('/document/search', '\App\Http\Controllers\Document\Search@search')->name('search-document');