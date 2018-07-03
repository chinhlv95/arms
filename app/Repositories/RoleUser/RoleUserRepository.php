<?php
namespace App\Repositories\RoleUser;

use App\Repositories\EloquentRepository;
use App\Config;

class RoleUserRepository extends EloquentRepository implements RoleUserRepositoryInterface{
    /**
     * Get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\RoleUser::class;
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\Role\RoleRepositoryInterface::remove()
     */
    public function remove($userId, $roleId)
    {
        $result = $this->_model->where('role_id', $roleId)
                ->where('user_id', $userId)->first();
        if($result){
            $this->_model->where('role_id', $roleId)
                ->where('user_id', $userId)
                ->delete();
        }
        else
            return false;
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\RoleUser\RoleUserRepositoryInterface::insert()
     */
    public function insert($userId, $roleId){
        $find = $this->_model->where('role_id', $roleId)->where('user_id', $userId)->get();
        if(count($find) == 0){
            $result = $this->_model->insert([
                'role_id' => $roleId,
                'user_id' => $userId
                ]);
            if(!$result)
                return false;
        }
    }
}