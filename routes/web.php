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

// Returns map view
Route::get('/', function() {
	return view('map');
});

// Returns events in JSON format
Route::get('/events', function() {
	$events = \App\Event::get();
	return response()->json($events->load(['location']));
});

// Protected Admin routes
Route::group(['middleware' => ['auth','verfied']], function() {
	// Returns home view
	Route::get('/home', 'HomeController@index');

	// Returns account view
	Route::get('/account', "AccountController@index");

	// Updates password
	Route::post('/account/psswd', "AccountController@updatePassword");

	// Returns users view with all users
	Route::get('/users', "UserController@index");

	// Updates the specified user's verified field
	Route::patch('/users/{user}', "UserController@updatePermissions");

	// Removes the specified user from the system
	Route::delete('/users/{user}', "UserController@delete");

	Route::get('/map', function() {
		return view('create');
	});

	/*
	 * Event routes
	 */
	Route::post("/event", "EventController@store");
	Route::patch("/event/{event}/location", "EventController@updateLocation");
	Route::patch("/event/{event}", "EventLocation@updateEvent");
	Route::delete("/event/{event}", "EventController@delete");
});
