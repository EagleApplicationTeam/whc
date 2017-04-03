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

/*
 * Authentication routes
 */
Auth::routes();

// Returns client side map view
Route::get('/', function() {
	return view('map');
});

// Returns events in JSON format
Route::get('/events', "EventController@clientIndex");

// Protected Admin routes
Route::group(['middleware' => ['auth','verfied']], function() {
	/*
	 * Account Routes
	 */
	// Returns account view
	Route::get('/account', "AccountController@index");
	// Updates password
	Route::post('/account/psswd', "AccountController@updatePassword");

	/*
	 * User Routes
	 */
	// Returns users view with all users
	Route::get('/users', "UserController@index");
	// Updates the specified user's verified field
	Route::patch('/users/{user}', "UserController@updatePermissions");
	// Removes the specified user from the system
	Route::delete('/users/{user}', "UserController@delete");

	/*
	 * Simply returns the admin event creator view
	 */
	Route::get('/map', "EventController@get");
	Route::get('/map/events', "EventController@adminIndex");

	/*
	 * Event routes
	 */
	// Stores a new event
	Route::post("/event", "EventController@store");
	// Updates an event's location
	Route::patch("/event/{event}/location", "EventController@updateLocation");
	// Updates an event's information
	Route::patch("/event/{event}", "EventController@updateEvent");
	// Deletes an event from the system
	Route::delete("/event/{event}", "EventController@delete");
});
