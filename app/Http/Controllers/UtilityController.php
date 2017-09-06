<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;

/*
 * This controller is responsible for displaying
 * the utility page of the application backend. This
 * controller is also responsible for generating the
 * links for each marker in the application database.
 */
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
