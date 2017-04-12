<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;

class UtilityController extends Controller
{
	/*
	 * Return view
	 */
    public function index() {
    	return view('admin.utility.index');
    }

    /*
     * Return view with generated links
     */
    public function generateLinks() {
    	$events = Event::get();

    	return redirect('/utility')->with('links', $events);
    }
}
