<?php

namespace App\Http\Controllers;

use Hash;
use Illuminate\Http\Request;

class AccountController extends Controller
{
	/*
	 * Returns the account view
	 */
	public function index() {
		return view('admin.account.account');
	}

	/*
	 * Updates user password
	 */
    public function updatePassword(Request $request) {
    	// Validate the request input
    	$this->validate($request, [
    		'old' => 'required',
    		'new' => 'required|min:6|confirmed',
    	]);

    	// Setup some variables
    	$old = $request->old;
    	$new = $request->new;

    	// Check the inputted old password to the user's current password
    	if (!Hash::check($old, $request->user()->password)) {
    		return redirect()->back()->with('error', 'Old password incorrect. Try again.');
    	}

    	// Update the user's password with a newly hashed password
    	$request->user()->fill([
			'password' => Hash::make($new)
		])->save();

    	// Redirect back with message
		return redirect()->back()->with('success', 'Successfully updated password.');
    }
}
