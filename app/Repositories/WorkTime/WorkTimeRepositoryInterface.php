<?php 
namespace App\Repositories\WorkTime;

interface WorkTimeRepositoryInterface{
	/**
	 * @author TheuNT
	 * Check project time
	 * 
	 */
	public function checkTime($param);
	
	public function getList($page);
	
	public function getProject();
	
	public function checkProject($param);
	
	public function setDataAddNew($data = []);
	
	public function validateWorkTime();
	
	public function createProject();
	
	public function createTagProjectUser();
	
	public function createTag();
	
	public function createWorkTime();
	
	public function getListByDate();
	
	public function updateProject($id, $project_user);
	
	public function updateWorkTime($id , $work_time);
	
	public function updateTag($id, $tag);
	
	public function deleteWorkTime($id);
	
}
?>