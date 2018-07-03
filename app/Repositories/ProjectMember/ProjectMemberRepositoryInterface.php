<?php
namespace App\Repositories\ProjectMember;

interface ProjectMemberRepositoryInterface{
    /**
     * @author huent6810
     * check user exists in db when synch data from api
     * @param int $project_id
     * @param int $user_id
     */
    public function checkExit($project_id, $user_id);
    
    /**
     * update record when synch data from API 
     * @param int $mtool_entry_id
     * @param array $data
     */
    public function updateSynch($mtool_entry_id, array $data);
    
    /**
     * @author huent6810
     * get list member of project
     * @param int $project_id
     */
    public function getWithProjectId($project_id, $dateFrom, $dateTo);
    
    /**
     * @author huent6810
     * get user's worktime in a project with user_id and project_id
     * @param int $project_id
     * @param int $user_id
     * @param date $dateFrom
     * @param date $dateTo
     */
    public function getWithProjectIdUserId($project_id, $user_id, $dateFrom, $dateTo);
    
    /**
     * @author huent6810
     * delete worktime
     * @param int $project_id
     */
    public function deleteWithProjectId($project_id);
    
    /**
     * @author huent6810
     * find worktime
     * @param int $project_id
     */
    public function findAllByProjectId($project_id);
    
    /**
     * @author huent6810
     * @param int $mtool_entry_id
     */
    public function checkExitWithMtoolEntryId($mtool_entry_id);
}