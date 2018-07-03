<?php
namespace App\Repositories\TagProjectUser;

interface TagWorktimeRepositoryInterface{
    /**
     * @author huent6810
     * get list of tag_project_user with project_user_id
     * @param int $project_user_id
     */
    public function findWithWorktime($project_user_id);
    
    /**
     * @author huent6810
     * tag_project_user with project_user_id
     * @param int $project_user_id
     */
    public function deleteWithWorktime($project_user_id);
}