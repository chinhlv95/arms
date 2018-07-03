<?php
namespace App\Repositories\Role;

use App\Repositories\EloquentRepository;
use App\Config;

class RoleRepository extends EloquentRepository implements RoleRepositoryInterface{
    /**
     * Get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Role::class;
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\Role\RoleRepositoryInterface::get()
     */
    public function get()
    {
        $result = $this->_model->leftJoin('role_user', 'roles.id', '=', 'role_user.role_id')
                            ->select('roles.*')
                            ->orderBy('role_name', 'asc')
                            ->distinct('roles.id')
                            ->get();
        return $result;
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\Role\RoleRepositoryInterface::findRoleByUserId()
     */
    public function findRoleByUserId($id)
    {
        $result = $this->_model->leftJoin('role_user', 'roles.id', '=', 'role_user.role_id')
        ->select('roles.*', 'role_user.user_id')
        ->where('role_user.user_id', $id)
        ->get();
        
        return $result;
    }
}