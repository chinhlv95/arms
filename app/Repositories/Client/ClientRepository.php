<?php
namespace App\Repositories\Client;

use App\Repositories\EloquentRepository;
use DB;

class ClientRepository extends EloquentRepository implements ClientRepositoryInterface{
    
    /**
     * @author huent6810
     * Get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Client::class;
    }

    /**
	 * @author SonNA
	 * Get client list
	 * @return string
	 */
	public function getPaginate($key = null){
		
		$query = $this->_model
				->orderBy('id','DESC')
				->when($key, function($query) use ($key){
					return $query->where('name', 'LIKE', '%'.$key.'%')
								 ->orWhere('code', 'LIKE', '%'.$key.'%');
				})
				->get();
		
		return $query ? $query : null;
	}

	/**
	 * @author SonNA
	 * find by client code
	 * @return string
	 */
	 public function findClientByCode($code){
		 $number = $this->_model->where('code', $code)->get()->count();

		 return $number;
	 }
	 
	 /**
	  * @author huent6810
	  * (non-PHPdoc)
	  * @see \App\Repositories\Client\ClientRepositoryInterface::getWithUser()
	  */
	 public function getWithUser($user)
	 {
	     $result = $this->_model->leftJoin('projects','clients.id', '=', 'projects.client_id')
	                   ->leftJoin('assigned_project', 'assigned_project.project_id', '=', 'projects.id')
	                   ->where('assigned_project.user_id', $user)
	                   ->select('clients.id', 'clients.name')
	                   ->distinct('clients.id')
	                   ->get();
	     return $result;
	 }
}