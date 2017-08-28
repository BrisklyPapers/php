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

Route::get('/ajax', ['middleware' => 'cors', function () {
    return view('pages.home');
}]);

Route::get('/ajax/search', ['middleware' => 'cors', function () {
    return view('search');
}]);

Route::get('/ajax/upload', ['middleware' => 'cors', function () {
    return view('upload');
}]);

Route::post('/ajax/document', '\App\Http\Controllers\Document\Upload@upload')->name('upload-document')->middleware('cors');

Route::get('/ajax/document/search/{id}', '\App\Http\Controllers\Document\Download@download')->name('download-document');
Route::get('/ajax/document/search/{id}/text', '\App\Http\Controllers\Document\Download@text');

Route::get('/ajax/document/search', '\App\Http\Controllers\Document\Search@search')
    ->name('search-document')
    ->middleware('cors');

Route::get('/ajax/document/{id}/parse/text', '\App\Http\Controllers\Document\Parse@text')->name('parse-document-text');

Route::get('/ajax/tags', '\App\Http\Controllers\Tags@search')->name('search-tags');