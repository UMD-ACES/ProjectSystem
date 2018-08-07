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
    $user = \App\User::get();

    if($user->isStudent())
    {
        return redirect()->route('Student.Home');
    }

    if($user->isAdmin())
    {
        return redirect()->route('Admin.Home');
    }

})->name('home');


/* CAS */
Route::get('logout', function() {
    \Illuminate\Support\Facades\Session::flush();
    \Illuminate\Support\Facades\Auth::logout();
    \Subfission\Cas\Facades\Cas::logout();
})->name('logout');

/* Authenticated as an Admin */
Route::group(['prefix' => 'admin',
              'as' => 'Admin.',
              'middleware' => [
                  'admin'
              ],
            ], function() {

    Route::get('home', function() {
        return view('welcome');
    })->name('Home');


    /* Instructor Setup */
    Route::get('setup/form', 'SetupInstructorController@setupForm')
        ->name('Setup.Form');

    Route::get('setup/reset', 'SetupInstructorController@reset')
        ->name('Setup.Reset');

    Route::get('setup/refresh', 'SetupInstructorController@refresh')
        ->name('Setup.Refresh');

    Route::post('admin/setup', 'SetupInstructorController@setup')->name('Setup');


    /* Peer Evaluations */
    Route::resource('peer_evaluations', 'PeerEvaluationsInstructorController');

    /* Meeting Minutes */
    Route::resource('meeting_minutes', 'MeetingMinutesInstructorController');


});

/* Authenticated as a student and is setup */
Route::group(['prefix' => 'student',
              'as' => 'Student.',
              'middleware' => [
                  'student',
                  'student.isReady'
              ],
], function() {

    /* Home */
    Route::get('home', function() {
        return view('welcome');
    })->name('Home');


    /* Peer Evaluations */
    Route::resource('peer_evaluations', 'PeerEvaluationsStudentController');

    /* Meeting Minutes */
    Route::resource('meeting_minutes', 'MeetingMinutesStudentController');
});

/* Authenticated as a student */
Route::group(['prefix' => 'student',
              'as' => 'Student.Setup',
              'middleware' => [
                  'student',
              ],
], function() {

    /* Student Setup */
    Route::get('setup', 'SetupStudentController@setupForm')->name('Form');
    Route::post('setup', 'SetupStudentController@setup')->name('Setup');
});


















