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
    return view('welcome');
})->name('home');

Route::get('admin/setup/form', 'SetupController@setupForm')->name('Admin.Setup.Form');
Route::get('admin/setup/reset', 'SetupController@reset')->name('Admin.Setup.Reset');
Route::get('admin/setup/refresh', 'SetupController@refresh')->name('Admin.Setup.Refresh');


Route::post('admin/setup', 'SetupController@setup')->name('Admin.Setup');

Route::resource('peer_evaluations_instructor', 'PeerEvaluationsInstructorController');

Route::resource('peer_evaluations', 'PeerEvaluationsStudentController');
