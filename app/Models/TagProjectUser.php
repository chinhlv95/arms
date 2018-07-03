<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagProjectUser extends Model
{
	protected $table= "tag_project_user";

	public $timestamps = false;

	protected $fillable = ['project_user_id', 'tag_id'];
}