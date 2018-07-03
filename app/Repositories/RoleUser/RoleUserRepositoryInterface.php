<?php
namespace App\Repositories\RoleUser;

interface RoleUserRepositoryInterface{
    
    /**
     * @author huent6810
     * remove user's role
     * @param int $id
     */
    public function remove($userId, $roleId);
    
    /**
     * @author huent6810
     * insert/add new role for user
     * @param int $userId
     * @param int $roleId
     */
    public function insert($userId, $roleId);
}