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

Auth::routes();

Route::get('/', 'HomeController@index');

Route::get('/posts', 'PostController@index');
Route::post('/post', 'PostController@store');
Route::delete('/post/{post}', 'PostController@destroy');

Route::post('/post/edit/{post}', 'PostController@edit');
Route::put('/post/edit/{post}', 'PostController@save');

Route::get('/post/tag/{tag}', 'PostController@getPostsByTag');
Route::get('/post/edit/tags', 'PostController@editTags');
Route::delete('/post/edit/tag/{tag}', 'PostController@destroyTag');

Route::delete('/post/edit/img/{image}', 'PostController@destroyImage');

