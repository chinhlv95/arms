<?php
namespace App\Repositories\ProjectManager;
/**
 * Project manager repository
 * User: son_jp
 * Date: 10/4/2017
 * Time: 2:43 PM
 */
interface ProjectManagerRepositoryInterface{
	/**
	 * @author SonNA
	 * Get paginate
	 * @return string
	 */
	public function getPaginate($key);

	/**
	 * @author SonNA
	 * Delete project
	 * @return string
	 */
	public function delete($id);

	/**
	 * @author SonNA
	 * Find project by ID
	 * @return string
	 */
	public function findProjectById($id);

	/**
	 * @author SonNA
	 * Get mapped project JP by project_id
	 * @return string
	 */
	public function getProjectJapanByProjectId($id);
	
	/**
	 * @author huent6810
	 * get all member assigned in project with project_id
	 * @param int $id
	 */
	public function getMemberWithId($id);

	/**
	 * @author SonNA6229
	 * @todo import Mayes project
	 * (non-PHPdoc)
	 * @see \App\Repositories\ProjectManager\ProjectManagerRepositoryInterface::importProject()
	 */
	 public function mappingProject($param, $project_id);
}