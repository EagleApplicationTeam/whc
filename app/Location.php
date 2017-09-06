<?php

namespace App;

use App\Event;
use Illuminate\Database\Eloquent\Model;

/*
 * This model contains the latitude and longitude data
 * of an Event model. This model belongs to an Event model.
 */
class Location extends Model
{
	protected $fillable = ['lat', 'lng'];
}