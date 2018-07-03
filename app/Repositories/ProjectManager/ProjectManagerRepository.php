<?php
namespace App\Repositories\ProjectManager;
use App\Repositories\EloquentRepository;
use App\Models\ProjectJP;
use App\Models\Client;
use App\Models\ProjectProjectJP;

/**
 * Project manager repository
 * User: son_jp
 * Date: 10/4/2017
 * Time: 2:43 PM
 */

class ProjectManagerRepository extends EloquentRepository implements ProjectManagerRepositoryInterface{

	/**
	 * @author SonNA
	 * Get model
	 * @return string
	 */
	public function getModel(){
		return \App\Models\Project::class;
	}

	/**
	 * @author SonNA
	 * Get paginate
	 * @return string
	 */
	public function getPaginate($key){
		$query = $this->_model->leftJoin('clients', 'projects.client_id', '=', 'clients.id')
				->select('projects.name as project_name','clients.name as client_name', 'projects.actual_start_time', 'projects.actual_end_time', 'projects.plan_start_time', 'projects.plan_end_time', 'projects.client_id','projects.id_project_resource', 'projects.intergreated_project_id', 'projects.id as project_id')
				->when($key, function($query) use ($key){
					return $query->where('projects.name', 'LIKE', '%'.$key.'%')
								 ->orWhere('clients.name', 'LIKE', '%'.$key.'%');
				})
				->orderBy('projects.id','DESC')->get();
		
		return $query ? $query : null;
	}

	/**
	 * @author SonNA
	 * Delete project
	 * @return string
	 */
	public function delete($id){
		
		$project = $this->_model->find($id);
		if(count($project) > 0){

			$project_jp = ProjectProjectJP::where('project_id', $id);
			
			if(count($project_jp) > 0)
			{
				$project_jp->delete();
			}

			return $project ? $project->delete(): false;
		}
		return false;
	}
	/**
	 * @author SonNA
	 * Find project by ID
	 * @return string
	 */
	public function findProjectById($id){
		$result = $this->_model->find($id);
		return count($result) > 0 ? $result : null;
	}

	/**
	 * @author SonNA
	 * Get mapped project JP by project_id
	 * @return string
	 */
	public function getProjectJapanByProjectId($id){

		 $projects_jp = $this->_model->select('project_jp.id as project_jp_id', 'project_jp.code', 'project_jp.name')
									->join('project_project_jp', 'projects.id', '=' ,'project_project_jp.project_id')
									->join('project_jp', 'project_jp.id', '=', 'project_project_jp.project_jp_id')
									->where('projects.id', $id)
									->get();
		return $projects_jp;
	}
	
	/**
	 * @author huent6810
	 * (non-PHPdoc)
	 * @see \App\Repositories\ProjectManager\ProjectManagerRepositoryInterface::getMemberWithId()
	 */
	public function getMemberWithId($id)
	{
	    $members = $this->_model->join('assigned_project', 'projects.id', '=', 'assigned_project.project_id')
	                           ->join('users', 'users.id', '=', 'assigned_project.user_id')
	                           ->where('projects.id', $id)
	                           ->select('users.id', 'users.username', 'users.fullname', 'users.email')
	                           ->get();
	    return $members;
	}

	/**
	 * @author SonNA6229
	 * @todo mapping Mayes project
	 * (non-PHPdoc)
	 * @see \App\Repositories\ProjectManager\ProjectManagerRepositoryInterface::importProject()
	 */

	 public function mappingProject($param, $project_id)
	 {	
		$last_insert_project_jp_id = '';
		$last_insert_client_id = '';

		$data = [
			'code' => (string)$param['code'],
			'name' => (string)$param['name'],
			'type' => (string)$param['type'],
			'client_code' => (string)$param['client_code'],
			'client_name' => (string)$param['client_name'],
			'order_date' => !empty($param['order_date']) ? date('Y-m-d', strtotime(str_replace('/', '-',$param['order_date']))) : null,
			'start_date' => !empty($param['start_date']) ? date('Y-m-d', strtotime(str_replace('/', '-',$param['start_date']))) : null,
			'delivery_date' => !empty($param['delivery_date']) ? date('Y-m-d', strtotime(str_replace('/', '-',$param['delivery_date']))) : null,
			'acceptance_date' => !empty($param['acceptance_date']) ? date('Y-m-d', strtotime(str_replace('/', '-',$param['acceptance_date']))) : null,
			'plan_completion_date' => !empty($param['plan_completion_date']) ? date('Y-m-d', strtotime(str_replace('/', '-',$param['plan_completion_date']))) : null,
			'chief_staff' => (string)$param['chief_staff'],
		];
		
		
		// update or create Client
		if($data['client_code'] && $data['client_name'])
		{	
			$client = Client::where('code', $data['client_code']);
			if($client->count() > 0)
			{
				$client->update(['code' => $data['client_code'], 'name' => $data['client_name'], 'id_resource' => 1]);
			}
			else{
				$client = new Client;

				$client->code = $data['client_code'];
				$client->name = $data['client_name'];
				$client->id_resource = 1;
				$client->save();
			}
		}
		
		//update or create project JP
		$query = ProjectJP::firstOrCreate(['code' => (string)$param['code']]);
		$query->name = $data['name'];
		$query->type = $data['type'];
		$query->order_date = $data['order_date'];
		$query->start_date = $data['start_date'];
		$query->delivery_date = $data['delivery_date'];
		$query->acceptance_date = $data['acceptance_date'];
		$query->plan_completion_date = $data['plan_completion_date'];
		$query->chief_staff = $data['chief_staff'];
		$query->save();

		//get last insert or update Project JP and client
		$last_insert_project_jp_id = $query->id;
		
		if($last_insert_project_jp_id){

			$projectAsia = $this->_model->find($project_id);
			
			if($projectAsia)
			{
				ProjectProjectJP::updateOrCreate(['project_id' => $project_id,'project_jp_id' => $last_insert_project_jp_id]);
			}
		}

		return $last_insert_project_jp_id > 0 ? $last_insert_project_jp_id : null; 
	 }
}