<?php

namespace App;

use App\Location;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
	/*
	 * Returns the associated Location of the Event
	 */
    public function location() {
    	return $this->belongsTo(Location::class);
    }
}
