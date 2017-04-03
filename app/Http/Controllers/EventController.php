<?php

namespace App\Http\Controllers;

use App\Event;
use App\Location;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /*
     * Returns the map creation view
     */
    public function get() {
        return view('admin.map.create');
    }

    /*
     * Returns all events
     */
    public function index() {
        // Get all events
        $events = \App\Event::get();

        // Return them in the response
        return response()->json($events->load(['location']));
    }

	/*
	 * Create a new event
	 */
    public function store(Request $request) {
    	// Create a new event
    	$event = new Event;
    	$event->name = "New Event";
    	$event->body = "A description of the event.";
        $event->address = "1234 Something Street";
    	$event->link = "www.something.com";

    	// Create a new location
    	$location = new Location;
    	$location->lat = $request->lat;
    	$location->lng = $request->lng;
    	$location->save();

    	// Attach location to event
    	$event->location()->associate($location);
    	$event->save();

    	// Respond with new event
    	return response()->json($event->load(['location']));
    }

    /*
     * Update an event's data
     */
    public function updateEvent(Request $request, Event $event) {
        // Validate request
        // $this->validate($request, [
        //     'name' => 'max:50',
        //     'body' => 'max:255',
        //     'address' => 'max:50',
        //     'link' => 'active_url'
        // ]);

        // Check if event exists
    	if (!$event) {
    		return response(null, 404);
    	}

        // Updat event information
    	$event->name = $request->name;
        $event->body = $request->body;
        $event->address = $request->address;
        $event->link = $request->link;

        // Save out the event
    	$event->save();

        // Return the event
    	return response()->json($event);
    }

    /*
     * Update an event's location
     */
    public function updateLocation(Request $request, Event $event) {
        // Check if event exists
    	if (!$event) {
    		return response()->json(["error" => "Error updating location"], 404);
    	}

        // Update event's location data
    	$event->location()->update([
    		'lat' => $request->lat,
    		'lng' => $request->lng
    	]);

        // Return event
  		return response()->json($event);
    }

    /*
     * Delete an event
     */
    public function delete(Request $request, Event $event) {
        // Check if event exists
    	if (!$event) {
    		return response()->json(null, 404);
    	}

        // Remove the event and it's associated location
    	$event->location->delete();
    	$event->delete();

        // Return an ok response
    	return response()->json(null, 204);
    }
}
