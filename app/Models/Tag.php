<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
	protected $table= "tags";

	public $timestamps = false;

	protected $fillable = ['id', 'tag_name'];

	/**
     * The tag that belong to the project user ( work time ).
     */
	public function ProjectUsers(){
		return $this->belongsToMany('App\Models\ProjectUser', 'tag_project_user', 'tag_id', 'project_user_id');
	}
}