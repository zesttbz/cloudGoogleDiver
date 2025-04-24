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

Route::get('/', 'HomeController@home')->name('home');
Route::get('/file/{code}', 'HomeController@file');
Route::get('/file/download/{code}', 'HomeController@updateDownloadFile');

Route::post('/login', 'Auth\LoginController@login');
Route::post('/register', 'Auth\RegisterController@register');
Route::get('/logout', 'Auth\LoginController@logout');

Route::group(['prefix' => 'dashboard'], function()
{
    Route::get('/', 'UserController@dashboard');

    Route::get('/upload', 'UserController@showUploadForm');
    // Route::post('/upload', 'UserController@upload');
    Route::post('/upload', 'UserController@uploadClient');
      
    
    //Route::get('/profile', 'UserController@profile');
    
    Route::get('/profile/{name?}', ['as' => 'profile', 'uses' => 'ProfileController@show']);
	Route::post('/profile/{name?}', ['as' => 'profile', 'uses' => 'ProfileController@update']);
  
  

    Route::get('/file/{fileId}', 'UserController@deleteFile');
});

Route::group(['prefix' => 'admin'], function()
{
    Route::get('/', 'AdminController@dashboard');

    Route::get('/users', 'AdminController@users');
    Route::get('/users/delete/{userId}', 'AdminController@deleteUser');
  
  	Route::get('/users/edit/{user}', ['as' => 'admin-users-edit', 'uses' =>  'AdminController@edituser']);
    Route::post('/users/edit/{user}', ['as' => 'admin-users-edit', 'uses' =>  'AdminController@updateuser']);

    Route::get('/files', 'AdminController@files');
    Route::get('/files/delete/{fileId}', 'AdminController@deleteFile');

    Route::get('/google-accounts', 'AdminController@googleAccounts');
    Route::post('/google-accounts', 'AdminController@addGoogleAccount');
    Route::get('/google-accounts/update/{accountId}', 'AdminController@updateGoogleAccount');
    Route::get('/google-accounts/delete/{accountId}', 'AdminController@deleteGoogleAccount');
});
