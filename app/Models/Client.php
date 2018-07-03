<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table= "clients";
    
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['id', 'code', 'name','id_resource','created_at','updated_at'];

    /**
     * Get the projects for the client.
     */
    public function projects()
    {
        return $this->hasMany('App\Models\Project');
    }
}
