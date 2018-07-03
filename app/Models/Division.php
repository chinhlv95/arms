<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $table= "divisions";

	public $timestamps = false;

	protected $fillable = ['id', 'name', 'parent_id', 'portal_id', 'id_resource'];
}
