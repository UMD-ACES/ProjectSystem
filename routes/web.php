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

    \App\Incident::report($user, 'No access to the application');
    return view('unauthorized');
})->name('home');

Route::get('/unauthorized', function() {
   return view('unauthorized');
})->name('unauthorized');


/* CAS */
Route::get('logout', function() {
    \Illuminate\Support\Facades\Session::flush();
    \Illuminate\Support\Facades\Auth::logout();
    \Subfission\Cas\Facades\Cas::logout();
})->name('logout');


/* Authenticated as an Admin */
Route::group(['prefix' => 'admin/setup',
              'as' => 'Admin.Setup.',
              'middleware' => [
                  'admin',
              ],
], function() {
    /* System Setup */
    Route::get('form', 'SetupInstructorController@setupForm')
        ->name('Form');

    Route::get('reset', 'SetupInstructorController@reset')
        ->name('Reset');

    Route::get('refresh', 'SetupInstructorController@refresh')
        ->name('Refresh');

    Route::post('setup', 'SetupInstructorController@setup')->name('Store');
});



/* Authenticated as an Admin */
Route::group(['prefix' => 'admin',
              'as' => 'Admin.',
              'middleware' => [
                  'admin',
                  'system.isReady',
              ],
            ], function() {
    /* Home */
    Route::get('home', function() {
        return view('welcome');
    })->name('Home');

    /* Peer Evaluations */
    Route::resource('peer_evaluations', 'PeerEvaluationsInstructorController');

    /* Meeting Minutes */
    Route::resource('meeting_minutes', 'MeetingMinutesInstructorController');

    /* Technical Logs */
    Route::resource('technical_logs', 'TechnicalLogsInstructorController');
});

/* Authenticated as a student and is setup */
Route::group(['prefix' => 'student',
              'as' => 'Student.',
              'middleware' => [
                  'student',
                  'system.isReady',
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

    /* Technical Logs */
    Route::resource('technical_logs', 'TechnicalLogsStudentController');
});

/* Authenticated as a student */
Route::group(['prefix' => 'student',
              'as' => 'Student.Setup.',
              'middleware' => [
                  'student',
                  'system.isReady',
              ],
], function() {

    /* Student Setup */
    Route::get('setup', 'SetupStudentController@setupForm')->name('Form');
    Route::post('setup', 'SetupStudentController@setup')->name('Store');
});


















