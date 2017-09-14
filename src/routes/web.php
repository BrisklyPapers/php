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

Route::get('/', ['middleware' => 'cors', function () {
    return "";
}]);

Route::post('/document', '\App\Http\Controllers\Document\Upload@upload')->name('upload-document');

Route::get('/document/search/{id}', '\App\Http\Controllers\Document\Download@download')->name('download-document');
Route::get('/document/search/{id}/text', '\App\Http\Controllers\Document\Download@text');

Route::get('/document/search', '\App\Http\Controllers\Document\Search@search')
    ->name('search-document');

Route::get('/document/{id}/parse/text', '\App\Http\Controllers\Document\Parse@text')->name('parse-document-text');

Route::get('/tags', '\App\Http\Controllers\Tags@search')->name('search-tags');