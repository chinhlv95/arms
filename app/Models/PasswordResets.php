<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResets extends Model
{
				public $timestamps = false;

				/**
					* The attributes that are mass assignable.
					*
					* @var array
					*/
				protected $fillable = [
								'email', 'token', 'created_at','status'
				];


}
