<?php

namespace App;

use App\Event;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
	protected $fillable = ['lat', 'lng'];
}
