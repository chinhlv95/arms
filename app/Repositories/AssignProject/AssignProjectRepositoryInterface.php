<?php
namespace App\Repositories\AssignProject;

interface AssignProjectRepositoryInterface{
    /**
     * @author huent6810
     * check record exist
     * @param int $projectId
     * @param int $userId
     */
    public function checkExist($projectId, $userId);
    
    /**
     * @author huent6810
     * @param int $projectId
     * @param int $userId
     */
    public function insert($projectId, $userId);
    
    /**
     * @author huent6810
     * delete record in assigned_project with $project_id
     * @param int $project_id
     */
    public function deleteWithProjectId($project_id);
    
    /**
     * @author huent6810
     * delete record with all condition (project_id and user_id)
     * @param int $project_id
     * @param int $user_id
     */
    public function remove($project_id, $user_id);
}