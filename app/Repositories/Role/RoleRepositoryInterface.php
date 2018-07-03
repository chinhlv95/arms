<?php
namespace App\Repositories\Role;

interface RoleRepositoryInterface{
    /**
     * @author huent6810
     * get all role show in popup when add role for user
     */
    public function get();
    
    /**
     * @author huent6810
     * get all user's role with user_id
     * @param int $id
     */
    public function findRoleByUserId($id);
}