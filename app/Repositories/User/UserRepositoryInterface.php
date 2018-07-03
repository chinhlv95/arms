<?php
namespace App\Repositories\User;

interface UserRepositoryInterface{

    /**
     * @author SonNA6229
     * Get user profile
     * @param: id
     */
    public function getProfile($id);
    
    /**
     * @author: HueNT
     * check mail/username exits when create new user 
     * @param string
     */
    public function checkUniqueCreate($field, $param);
    
    /**
     * @author: HueNT
     * check mail/username exits when update new user
     * @param unknown $id
     * @param unknown $param
     */
    public function checkUniqueForUpdate($id, $field, $param);

    /**
     * @author HueNT
     * get list user with paginate
     * @param number $perpage
     * @param number $resource
     */
    public function getPaginate($perpage, $resource);
    
    /**
     * 
     * @author HueNT
     * search user information with key word
     * @param string $key
     */
    public function search($key);
 
    /**
     * @author SonNA6229
     * Update password
     * @param: $email, $param
     */
    public function updatePassword($email, $param);
    
    /**
     * @author HueNT
     * setup paniagate default to get data
     * @param unknown $item
     */
    public function setPaginate($item);

    /**
     * @author SonNA
     * find or fail user by UID
     * @param string UID
     */
    public function findByUID($uid);
    
    /**
     * @author huent6810
     * insert item to db
     * @param array $attributes
     */
    public function insertGetId(array $attributes);
    
    /**
     * @author huent6810
     * find user with custom field
     * @param unknown $field
     * @param unknown $value
     */
    public function findByField($field, $value);
    
    /**
     * @author huent6810
     * get all user with all resource_id, no paginate
     */
    public function getAllResource();
    
    /**
     * @author huent6810
     * get all members of manager's division
     * @param int $id
     */
    public function getMemberOfDivision($id);
    
    /**
     * @author huent6810
     * @param array $data - contain list members was soft deleted
     */
    public function activeAccount($data);
}
