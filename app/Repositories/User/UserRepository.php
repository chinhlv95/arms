<?php
namespace App\Repositories\User;

use App\Repositories\EloquentRepository;
use App\Config;
use App\User;
use DB;

class UserRepository extends EloquentRepository implements UserRepositoryInterface{

    /**
     * Get model
     * @return string
     */
    public function getModel()
    {
        return \App\User::class;
    }

    /**
     * Get User profile
     * @return data
     */
    public function getProfile($id)
    {
        return $this->_model->find($id);
    }
    
    /**
     * check mail/username exits for create user
     * @see \App\Repositories\User\UserRepositoryInterface::checkMailExits()
     * @return number
     */
    public function checkUniqueCreate($field, $param)
    {
        $result = $this->_model->where($field, $param)->get();
        $checkInTrash = $this->_model->onlyTrashed()->where($field, $param)->get();
        
        return count($result) + count($checkInTrash);
    }
    
    /**
     * check mail/username exits for update user: old mail(pass), new mail(check as when create user)
     * @param int $id
     * @param string $param
     * @return number
     */
    public function checkUniqueForUpdate($id, $field, $param)
    {
        $result = $this->_model->select($field)->whereNotIn('id', [$id])->get()->toArray();
        $checkInTrash = $this->_model->onlyTrashed()->select($field)->whereNotIn('id', [$id])->get()->toArray();
        
        $arrData = array_merge(array_pluck($result, $field), array_pluck($checkInTrash, $field));
        
        return in_array($param, $arrData) ? 1 : 0;
    }
    
    /**
     * get list user with panigate
     * @see \App\Repositories\User\UserRepositoryInterface::getPaginate()
     */
    public function getPaginate($perpage, $resource)
    {
        $items = $this->_model->where('id_resource', $resource)->latest()->paginate($perpage);
        
        return $this->setPaginate($items);
    }
    
    /**
     * setup paniagate default to get data
     * @see \App\Repositories\User\UserRepositoryInterface::setPaginate()
     * @param object $item
     */
    public function setPaginate($items)
    {
        $response = [
                'pagination' => [
                'total' => $items->total(),
                'per_page' => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem()
            ],
            'data' => $items
        ];
        return $response;
    } 
    
    /**
     * search user's information with keysearch
     * @see \App\Repositories\User\UserRepositoryInterface::search()
     */
    public function search($key){
        $items = $this->_model->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                ->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
                ->select('users.*', 'roles.role_name', 'roles.id AS role_id')
                ->where('username', 'like','%' . $key . '%')
                ->orWhere('email', 'like','%' . $key . '%')
                ->orWhere('fullname', 'like','%' . $key . '%')
                ->orWhere('member_code', 'like','%' . $key . '%')
                ->latest()
                ->get();
        
        return $items;
    }
    
    /**
     * @author SonNA6229
     * Update password
     * @param: $email, $param
     */
    public function updatePassword($email, $param){
        return $this->_model->where('email', $email)
            ->update($param);
    }

    /**
     * @author SonNA
     * find or fail user by UID
     * @param string UID
     */
    public function findByUID($uid){
        return User::withTrashed()->where('username', $uid)->first();
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\User\UserRepositoryInterface::insertGetId()
     */
    public function insertGetId(array $attributes)
    {
        return $this->_model->insertGetId($attributes);
    }
    
    /**
     * (non-PHPdoc)
     * @see \App\Repositories\User\UserRepositoryInterface::findByField()
     */
    public function findByField($field, $value)
    {
        return $this->_model->where($field, $value)->first();
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\User\UserRepositoryInterface::getAllResource()
     */
    public function getAllResource()
    {
        $data = [];
        $data['active'] = $this->_model->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
                    ->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
                    ->select('users.*', 'roles.role_name', 'roles.id AS role_id')
                    ->orderBy('users.id', 'asc')
                    ->get()->toArray();
        $data['deactive'] = $this->_model->onlyTrashed()->get()->toArray();
        return $data;
    }
    
    /**
     * @author huent6810
     * get all members of manager's division
     * @see \App\Repositories\User\UserRepositoryInterface::getMemberManaged()
     */
    protected $_members = [];
    
    public function getMemberOfDivision($id)
    {
        $items = $this->_model->where('manage_id', $id)->get()->toArray();
        if (count($items)) {
            foreach ($items as $item) {
                $this->_members[] = $item['id'];
                if ($item['id'] != $id) {
                    $this->getMemberOfDivision($item['id']);
                }
            }
        }
        return $this->_members;
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\User\UserRepositoryInterface::activeAccount()
     */
    public function activeAccount($data)
    {
        $check = $this->_model->onlyTrashed()->whereIn('id', $data)->get();
        if($check){
            $this->_model->withTrashed()
                ->whereIn('id', $data)
                ->restore();
        }
    }
}



