<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;

class UtilityController extends Controller
{
    public function index() {
    	return view('admin.utility.index');
    }

    public function generateLinks() {
    	$events = Event::get();

    	return redirect('/utility')->with('links', $events);
    }
}
