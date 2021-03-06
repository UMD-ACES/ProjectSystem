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
})->name('home')
    ->middleware('cas.auth');

Route::get('/unauthorized', function() {
   return view('unauthorized');
})->name('unauthorized')
    ->middleware('cas.auth');


/* CAS */
Route::get('logout', 'AuthController@logout')->name('logout');


/* Authenticated as an Admin */
Route::group(['prefix' => 'admin/setup',
              'as' => 'Admin.Setup.',
              'middleware' => [
                  'cas.auth',
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
                  'cas.auth',
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
    Route::get('peer_evaluations/individual_grades/form', 'PeerEvaluationsInstructorController@individual_grades_form')
        ->name('peer_evaluations.individual_grades.form');
    Route::post('peer_evaluations/individual_grades/compute', 'PeerEvaluationsInstructorController@individual_grades')
        ->name('peer_evaluations.individual_grades.compute');

    /* Meeting Minutes */
    Route::resource('meeting_minutes', 'MeetingMinutesInstructorController');

    /* Technical Logs */
    Route::resource('technical_logs', 'TechnicalLogsInstructorController');
});

/* Authenticated as a student and is setup */
Route::group(['prefix' => 'student',
              'as' => 'Student.',
              'middleware' => [
                  'cas.auth',
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
                  'cas.auth',
                  'student',
                  'system.isReady',
              ],
], function() {

    /* Student Setup */
    Route::get('setup', 'SetupStudentController@setupForm')->name('Form');
    Route::post('setup', 'SetupStudentController@setup')->name('Store');
});


















