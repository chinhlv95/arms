<?php
namespace App\Repositories\Client;

interface ClientRepositoryInterface{
    
    /**
	 * @author SonNA
	 * Get client list
	 * @return string
	 */
    public function getPaginate($key);

	/**
	 * @author SonNA
	 * find by client code
	 * @return string
	 */
	 public function findClientByCode($code);
	 
	 /**
	  * @author huent6810
	  * get client with member has role is member
	  * @param int $user
	  */
	 public function getWithUser($user);
}