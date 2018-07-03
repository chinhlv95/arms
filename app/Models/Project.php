<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
	protected $table= "projects";
	
    protected $fillable = [
            'name',
            'key',
            'total_hour', 
            'actual_start_time',
            'actual_end_time',
            'plan_start_time',
            'id_project_resource' ,
            'id_mtool_resource',
            'client_id',
            'intergreated_project_id',
            'plan_end_time'
        ];
    
    public function projectmember()
    {
        return $this->belongsToMany('App\User','worktimes','project_id','user_id');
    }

    /**
     * Get the client that owns the project.
     */
    public function client()
    {
        return $this->belongsTo('App\Models\Client');
    }
}
