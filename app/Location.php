<?php

namespace App;

use App\Event;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
	/*
	 * Returns the associated Event of the Location
	 */
    public function event() {
    	return $this->hasOne(Event::class);
    }
}
