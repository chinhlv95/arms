<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectJP extends Model
{
    protected $table= "project_jp";
	
    protected $fillable = [
            'id', 'code', 'name','type','client_code','client_name' ,'order_date','start_date','delivery_date','acceptance_date','plan_completion_date','chief_staff','created_at','updated_at','deleted_at'
    ];
}
