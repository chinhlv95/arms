<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectProjectJP extends Model
{
    protected $table = 'project_project_jp';
    
    public $timestamps = false;
    
    protected $fillable = ['project_id','project_jp_id'];
}
