<?php
namespace App\Repositories\TagProjectUser;

use App\Repositories\EloquentRepository;
use DB;

class TagWorktimeRepository extends EloquentRepository implements TagWorktimeRepositoryInterface{
    
    /**
     * @author huent6810
     * Get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\TagProjectUser::class;
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\TagProjectUser\TagWorktimeRepositoryInterface::findWithWorktime()
     */
    public function findWithWorktime($project_user_id)
    {
        $result = $this->_model->where('project_user_id', $project_user_id)->get();
        if($result){
            return $result;
        }
        else{
            return [];
        }
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\TagProjectUser\TagWorktimeRepositoryInterface::deleteWithWorktime()
     */
    public function deleteWithWorktime($project_user_id)
    {
        $result = $this->_model->where('project_user_id', $project_user_id)->get();
        if($result){
            $this->_model->where('project_user_id', $project_user_id)->delete();
        }
    }
}