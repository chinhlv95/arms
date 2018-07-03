<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\RoleUser\RoleUserRepositoryInterface;
use App\Repositories\RoleUser\RoleUserRepository;

class RoleController extends Controller
{
    protected $roleRepository;
    protected $roleUserRepository;
    
    /**
     * @author huent6810
     * @param RoleRepositoryInterface $role
     */
    public function __construct(RoleRepositoryInterface $role, RoleUserRepositoryInterface $roleUser){
        $this->roleRepository = $role;
        $this->roleUserRepository = $roleUser;
    }
    
    /**
     * @author huent6810
     * @return array
     */
    public function getAll()
    {
        $result = $this->roleRepository->get();
        
        return $result;
    }
    
    /**
     * @author huent6810
     * @todo Update user role
     * @param Request $request
     */
    public function change(Request $request)
    {
        $roleCurrent = [];
        
        $data = $this->roleRepository->findRoleByUserId($request->id);
        for($i = 0; $i < count($data); $i++){
            array_push($roleCurrent, $data[$i]->id);
        }
        // find roles removed
        for($i = 0; $i < count($roleCurrent); $i++){
            $index = array_search($roleCurrent[$i], $request->data);
            if(gettype($index) == 'boolean')
            {
                $this->roleUserRepository->remove($request->id, $roleCurrent[$i]);
            }
        }
        // find roles inserted
        for($i = 0; $i < count($request->data); $i++){
            $index = array_search($request->data[$i], $roleCurrent);
            if(gettype($index) == 'boolean')
            {
                $this->roleUserRepository->insert($request->id, $request->data[$i]);
            }
        }
    }
}
