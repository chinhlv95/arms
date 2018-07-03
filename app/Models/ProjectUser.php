<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectUser extends Model
{
    protected $table = 'worktimes';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id','project_id', 'user_id', 'id_resource','work_time','working_date', 'mtool_entry_id'
    ];

    /**
     * The Project users (work time) that belong to the tags.
     */
    public function tags(){
        return $this->belongsToMany('App\Models\Tag', 'tag_project_user', 'project_user_id', 'tag_id')->withPivot('tag_id');
    }
}
