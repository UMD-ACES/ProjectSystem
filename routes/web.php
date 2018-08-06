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

/* CAS */
Route::get('logout', function() {
    \Illuminate\Support\Facades\Session::flush();
    \Illuminate\Support\Facades\Auth::logout();
    \Subfission\Cas\Facades\Cas::logout();
})->name('logout');


/* Setup */
Route::get('admin/setup/form', 'SetupController@setupForm')
    ->name('Admin.Setup.Form')
    ->middleware('admin');


Route::get('admin/setup/reset', 'SetupController@reset')
    ->name('Admin.Setup.Reset')
    ->middleware('admin');;

    Route::get('admin/setup/refresh', 'SetupController@refresh')
    ->name('Admin.Setup.Refresh')
    ->middleware('admin');

Route::post('admin/setup', 'SetupController@setup')->name('Admin.Setup')
    ->middleware('admin');

/* Peer Evaluations */

Route::resource('peer_evaluations_instructor', 'PeerEvaluationsInstructorController')
    ->middleware('admin');

Route::resource('peer_evaluations', 'PeerEvaluationsStudentController');

