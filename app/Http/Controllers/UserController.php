<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
	/*
	 * Return the users view with all users
	 */
	public function index() {
		$users = User::get();
		return view('admin.users.users')->with('users', $users);
	}

	/*
	 * Set the verified field on a user
	 */
    public function updatePermissions(User $user) {
    	// Check if ther user exists
    	if (!$user || $user->id === 1) {
    		return redirect()->back()->with('error', "There was a problem updating the user's permissions.");
    	}

    	// Toggle the verified field
    	$user->verified = !$user->verified;
    	$user->save();

        $val = '';

        // Make the flash message a little nicer
        if ($user->verified) {
            $val = 'verified the user.';
        } else {
            $val = 'revoked the user\'s permissions.';
        }

    	return redirect()->back()->with('success', 'Successfully ' . $val);
    }

    /*
     * Remove the specified user
     */
    public function delete(User $user) {
    	// Check if user exists
    	if (!$user || $user->id === 1) {
    		return redirect()->back()->with('error', "There was a problem removing the user.");
    	}

    	// Delete the user
    	$user->delete();

    	return redirect()->back()->with('success', "Successfully removed the user.");
    }
}
