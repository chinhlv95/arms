<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignedProject extends Model
{
     protected $table= "assigned_project";
     
     /**
      * The attributes that are mass assignable.
      *
      * @var array
      */
     protected $fillable = ['project_id', 'user_id'];
}
