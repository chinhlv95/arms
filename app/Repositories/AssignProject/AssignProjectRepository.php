<?php
namespace App\Repositories\AssignProject;

use App\Repositories\EloquentRepository;

class AssignProjectRepository extends EloquentRepository implements AssignProjectRepositoryInterface{

    /**
     * @author huent6810
     * Get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\AssignedProject::class;
    }
    
    /**
     * @autho huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\AssignProject\AssignProjectRepositoryInterface::checkExist()
     */
    public function checkExist($projectId, $userId)
    {
        $result = $this->_model->where('project_id', $projectId)->where('user_id', $userId)->first();
        if($result){
            return 1;
        }
        else
            return 0;        
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\AssignProject\AssignProjectRepositoryInterface::insert()
     */
    public function insert($projectId, $userId)
    {
        $result = $this->_model->insert([
            'project_id' => $projectId,
            'user_id' => $userId
            ]);
        if(!$result)
            return false;
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\AssignProject\AssignProjectRepositoryInterface::deleteWithProjectId()
     */
    public function deleteWithProjectId($project_id)
    {
        $result = $this->_model->where('project_id', $project_id)->get();
        if($result){
            $this->_model->where('project_id', $project_id)->delete();
        }
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\AssignProject\AssignProjectRepositoryInterface::remove()
     */
    public function remove($project_id, $user_id)
    {
        $result = $this->_model->where('project_id', $project_id)->where('user_id', $user_id)->first();
        if($result){
            $this->_model->where('project_id', $project_id)->where('user_id', $user_id)->delete();
        }
        else{
            return false;
        }
    }
}