<?php

namespace App\Http\Controllers;

use App\Event;
use App\Location;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function store(Request $request) {
    	// Create a new event
    	$event = new Event;
    	$event->name = "New Event";
    	$event->image_url = "Some url";
    	$event->link = "Some link";
    	$event->start_date = Carbon::now();
    	$event->end_date = Carbon::now();

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

    public function updateEvent(Request $request, Event $event) {

    }

    public function updateLocation(Request $request, Event $event) {
    	if (!$event) {
    		return response()->json(["error" => "Error updating location"], 404);
    	}

    	$event->location()->update([
    		'lat' => $request->lat,
    		'lng' => $request->lng
    	]);

  		return response()->json($event);
    }

    public function delete(Request $request, Event $event) {

    }
}
