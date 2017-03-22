<?php

namespace App;

use App\Event;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    public function event() {
    	return $this->hasOne(Event::class);
    }
}
