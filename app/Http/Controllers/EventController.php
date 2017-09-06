<?php

namespace App\Http\Controllers;

use App\Event;
use App\Location;
use Carbon\Carbon;
use Illuminate\Http\Request;

/*
 * This controller handles the logic of storing,
 * updating, and deleting map markers. This controller
 * is also responsible for supplying the frontend of 
 * the application with marker data.
 */
class EventController extends Controller
{
    /*
     * Returns the map creation view
     */
    public function get() {
        return view('admin.map.create');
    }

    /*
     * Returns all events that are live
     */
    public function clientIndex() {
        // Get all events
        $events = Event::where('live', true)->get();

        // Return them in the response
        return response()->json($events->load(['location']));
    }

    /*
     * Returns all events to the admin page
     */
    public function adminIndex() {
         // Get all events
        $events = Event::get();

        // Return them in the response
        return response()->json($events->load(['location']));
    }

    /*
     * Get event from request
     */
    public function getEvent(Event $event) {
        // If event could not be found, redirect back
        if (!$event) {
            return back();
        }

        return redirect('/')->with('event', $event);
    }

    /*
     * Get location from request
     */
    public function getLocation(Request $request) {
        $data = [
            'lat' => $request->lat,
            'lng' => $request->lng
        ];

        return redirect('/')->with(['location' => $data]);
    }

	/*
	 * Create a new event
	 */
    public function store(Request $request) {
    	// Create a new event object
    	$event = new Event;
    	$event->name = "New Event";
    	$event->body = "A description of the location.";
        $event->address = "";
    	$event->link = "http://example.com";
        $event->live = true;
        $event->priority = false;

    	// Create a new location object
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
        // Check if event exists
    	if (!$event) {
    		return response(null, 404);
    	}

        // Updat event information
    	$event->name = $request->name;
        $event->body = $request->body;
        $event->address = $request->address;
        $event->link = $request->link;
        $event->live = $request->live;
        $event->priority = $request->priority;

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
