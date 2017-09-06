<?php

namespace App;

use App\Location;
use Illuminate\Database\Eloquent\Model;

/*
 * This model contains the data for each individual marker.
 * Data includes name, address, and website URL. This model
 * has a one-to-one relationship with a single location model.
 */
class Event extends Model
{
	/*
	 * Returns the associated Location of the Event
	 */
    public function location() {
    	return $this->belongsTo(Location::class);
    }
}
