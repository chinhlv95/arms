<?php

namespace App\Http\Controllers;

use App\Repositories\ProjectManager\ProjectManagerRepositoryInterface;
use App\Repositories\Client\ClientRepositoryInterface;
use Illuminate\Http\Request;
use App\Repositories\AssignProject\AssignProjectRepositoryInterface;
use Excel;
use App\Helper;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class ProjectManagerController extends Controller
{
	/**
	 *
	 * @var $projectManagerRepository|\App\Repositories\ProjectManagerRepositoryInterface
	 * @var $clientRepository|\App\Repositories\ClientRepositoryInterface
	 * @var $clientRepository|\App\Repositories\AssignProjectRepositoryInterface
	 */
	protected $projectManagerRepository;
	protected $clientRepository;
	protected $assignedProject;

	/**
	 * @author SonNA
	 * @param ProjectManagerRepositoryInterface $projectManagerRepository
	 * @todo constructor
	 */
	public function __construct(ProjectManagerRepositoryInterface $projectManagerRepository, AssignProjectRepositoryInterface $assignedProject, ClientRepositoryInterface $clientRepository){
		$this->projectManagerRepository = $projectManagerRepository;
        $this->assignedProject = $assignedProject;
		$this->clientRepository = $clientRepository;
	}

	/**
	 * @author SonNA
	 * @todo get list project
	 * @return resource/view/projects/index
	 */
	public function index(){
		$entries = json_encode(config('contains.NUMBER_ENTRIES'));
		$project_resource = json_encode(config('contains.ID_PROJECT_RESOURCE'));
		
		return view('projects.index', compact('entries','project_resource'));
	}

	/**
	 * @author SonNA
	 * @todo get all project
	 * @return json string
	 */
	public function getListProject(Request $request){
		$projects = $this->projectManagerRepository->getPaginate($request->key)->toArray();
		foreach($projects as $key => $value)
		{
		    $projects[$key]['members'] = [];
		    $members = $this->projectManagerRepository->getMemberWithId($projects[$key]['project_id']);
			$projects_jp = $this->projectManagerRepository->getProjectJapanByProjectId($projects[$key]['project_id']);

		    if(count($members) > 0){
		         $projects[$key]['members'] = $members->toArray();
		    }

			if(count($projects_jp) > 0){
				$projects[$key]['projects_jp'] = $projects_jp->toArray();
			}
		}

		return response()->json($projects);
		
	}

	/**
	 * @author SonNA
	 * @todo create project
	 * @return resource/view/projects/create
	 */
	public function create(){
		$clients = $this->clientRepository->getAll()->toArray();
		
		return view('projects.create', compact('clients'));
	}

	/**
	 * @author SonNA
	 * @todo save project
	 */
	public function store(Request $request){

		$request['plan_start_time'] = !empty($request['plan_start_time']) ? date('Y-m-d H:i:s', strtotime($request->plan_start_time)) : null;
		$request['plan_end_time'] = !empty($request['plan_end_time']) ? date('Y-m-d H:i:s', strtotime($request->plan_end_time)) : null;
		$request['actual_start_time'] = !empty($request['actual_start_time']) ? date('Y-m-d H:i:s', strtotime($request->actual_start_time)) : null;
		$request['actual_end_time'] = !empty($request['actual_end_time']) ? date('Y-m-d H:i:s', strtotime($request->actual_end_time)) : null;
		
		$query = $this->projectManagerRepository->create($request->all());

		if(!empty($query)){

			return $request->session()->flash('message',[
                'level' => 'success',
                'title' => __('project.list_project.label_dialog_title'),
                'content' => __('project.message.add_project_success')
            ]);
		}
		return $request->session()->flash('message',[
                'level' => 'danger',
                'title' => __('project.list_project.label_dialog_title'),
                'content' => __('project.message.add_project_error')
        ]);
	}

	/**
	 * @author SonNA
	 * @todo get edit project
	 */
	public function edit(Request $request){
		
		$project = $this->projectManagerRepository->findProjectById($request->id)->toJson();
		$clients = $this->clientRepository->getAll()->toArray();

		return view('projects.update', compact('project', 'clients'));
	}

	/**
	 * @author SonNA
	 * @todo post update project
	 */
	public function update(Request $request){

		$request['plan_start_time'] = !empty($request['plan_start_time']) ? date('Y-m-d H:i:s', strtotime($request->plan_start_time)) : null;
		$request['plan_end_time'] = !empty($request['plan_end_time']) ? date('Y-m-d H:i:s', strtotime($request->plan_end_time)) : null;
		$request['actual_start_time'] = !empty($request['actual_start_time']) ? date('Y-m-d H:i:s', strtotime($request->actual_start_time)) : null;
		$request['actual_end_time'] = !empty($request['actual_end_time']) ? date('Y-m-d H:i:s', strtotime($request->actual_end_time)) : null;

		$query = $this->projectManagerRepository->update($request->id, $request->all());

		if(!empty($query)){

			return $request->session()->flash('message',[
                'level' => 'success',
                'title' => __('project.list_project.label_dialog_title'),
                'content' => __('project.message.update_project_success')
            ]);
		}
		return $request->session()->flash('message',[
                'level' => 'danger',
                'title' => __('project.list_project.label_dialog_title'),
                'content' => __('project.message.update_project_error')
        ]);
	}

	/**
	 * @author SonNA
	 * @todo delete project
	 * @return boolean
	 */
	public function delete(Request $request){
		$query = $this->projectManagerRepository->delete($request->project_id);
		
		return $query ? 'success' : 'delete_fail';
	}
	

	/**
	 * @author huent6810
	 * @todo save infor after assigned member to project
	 */
	public function saveAssignMember(Request $request)
	{
	    // get current list member assigned in project 
	    $member = $this->projectManagerRepository->getMemberWithId($request->id_project)->toArray();
	    $arr_current = array_pluck($member, 'id');
	    // compare list member in $request and $arr_current: find member was inserted
	    if(count($request->list_member) > 0){
	        for($i = 0; $i < count($request->list_member); $i++){
	            if(gettype(array_search($request->list_member[$i], $arr_current)) == 'boolean'){
	                $this->assignedProject->insert($request->id_project, $request->list_member[$i]);
	            }
	        }
	    }
	    // compare list member in $request and $arr_current: find member was removed
	    if(count($arr_current) > 0){
	        for($i = 0; $i < count($arr_current); $i++){
	            if(gettype(array_search($arr_current[$i], $request->list_member)) == 'boolean'){
	                $this->assignedProject->remove($request->id_project, $arr_current[$i]);
	            }
	        }
	    }
	}
	
	/**
	 * @author huent6810
	 * @param Request $request
	 * @return boolean
	 */
	public function deleteMemberAssigned(Request $request)
	{
	    $result = $this->assignedProject->remove($request->id_project, $request->id_user);
	    return $result ? 'sucess' : '';
	}

	/**
	 * @author Sonna6229
	 * @param Request $request
	 * @return boolean
	 */
	 public function mappingProject(Request $request)
	 {
		$state = true;
		
		if(count($request->data) > 0)
		{
			foreach($request->data as $item)
			{
				$result = $this->projectManagerRepository->mappingProject($item, $item['arms_project_id']);

				if(!$result)
				{
					$state = false;
				}
			}
		} 
		return $state ? 'sucess' : '';
	 }

	/**
	* @author SonNA6229
	* @todo import Mayes project
	* (non-PHPdoc)
	* @see \App\Repositories\ProjectManager\ProjectManagerRepositoryInterface::importProject()
	*/
	private $errorRows = [];
	private $logFile = 'mapping_project.log';

	public function importProject(Request $request)
	{
		if($request->hasFile('importfile'))
		{
			$path = $request->file('importfile')->getRealPath();
			$data = Excel::load($path, function($reader) {
				$reader->formatDates(true);
			})->get()->toArray();
							
			if(array_key_exists('プロジェクトコード', $data[0]))
			{
				foreach($data as $key => $value)
				{
					if(
						empty($data[$key]['プロジェクトコード']) || 
						( !empty($data[$key]['受注日']) && !Helper::checkDateFormat($data[$key]['受注日'])) ||
						(!Helper::checkDateFormat($data[$key]['着手日']) && !empty($data[$key]['着手日'])) || 
						(!Helper::checkDateFormat($data[$key]['納品日']) && !empty($data[$key]['納品日'])) || 
						(!Helper::checkDateFormat($data[$key]['検収予定日']) && !empty($data[$key]['検収予定日'])) || 
						(!Helper::checkDateFormat($data[$key]['完了予定日']) && !empty($data[$key]['完了予定日'])) || 
						strlen((string)$data[$key]['得意先コード']) > 4 || empty($data[$key]['得意先コード']) || empty($data[$key]['得意先名称']) 
					)
					{
						array_push($this->errorRows, $data[$key]);
					}
				}
				
				$data = array_values($data);
				
				if(count($this->errorRows) > 0 )
				{
					if(Storage::disk('public')->exists($this->logFile))
					{
						Storage::disk('public')->delete($this->logFile);
					}
					
					Storage::disk('public')->put($this->logFile, '');

					foreach($this->errorRows as $key => $value)
					{
						Storage::disk('public')->append($this->logFile, 'Time log:'. date("Y-m-d H:i:s") ."\r\n");
						Storage::disk('public')->append($this->logFile, 'Line ('. (string)$this->errorRows[$key][0] .'):'."\r\n");
				
						if($this->errorRows[$key]['プロジェクトコード'] == null)
						{
							Storage::disk('public')->append($this->logFile, '- プロジェクトコード is required'."\r\n");
						}
						if($this->errorRows[$key]['得意先名称'] == null)
						{
							Storage::disk('public')->append($this->logFile, '- 得意先名称 is required'."\r\n");
						}
						if($this->errorRows[$key]['得意先コード'] == null)
						{
							Storage::disk('public')->append($this->logFile, '- 得意先コード is empty.'."\r\n");
						}
						if($this->errorRows[$key]['受注日'] != null && !Helper::checkDateFormat($this->errorRows[$key]['受注日']))
						{
							Storage::disk('public')->append($this->logFile, '- 受注日 must invalid (YYYY-mm-dd)'."\r\n");
						}
						if($this->errorRows[$key]['着手日'] != null && !Helper::checkDateFormat($this->errorRows[$key]['着手日']))
						{
							Storage::disk('public')->append($this->logFile, '- 着手日 must invalid (YYYY-mm-dd);'."\r\n");
						}
						if($this->errorRows[$key]['納品日'] != null && !Helper::checkDateFormat($this->errorRows[$key]['納品日']))
						{
							Storage::disk('public')->append($this->logFile, '- 納品日 must invalid (YYYY-mm-dd);'."\r\n");
						}
						if($this->errorRows[$key]['検収予定日'] != null && !Helper::checkDateFormat($this->errorRows[$key]['検収予定日']))
						{
							Storage::disk('public')->append($this->logFile, '- 検収予定日 must invalid (YYYY-mm-dd);'."\r\n");
						}
						if($this->errorRows[$key]['完了予定日'] != null && !Helper::checkDateFormat($this->errorRows[$key]['完了予定日']))
						{
							Storage::disk('public')->append($this->logFile, '- 完了予定日 must invalid (YYYY-mm-dd);'."\r\n\r\n");
						}
						if(strlen($this->errorRows[$key]['得意先コード']) > 4)
						{
							Storage::disk('public')->append($this->logFile, '- 得意先コード must is greater than or equal to 4 character;'."\r\n\r\n");
						}
					}
					
				
					array_push($data, ['has_error' => true]);
				}
		
				return response()->json($data);
			}
			else{
				return 'TmpInvalid';
			}
		}else{
			return 'notFile';
		}
	}

	/**
	 * @author SonNA6229
	 * @todo import Mayes project - Export log file
	 * @return message error
	 */
	public function ExportLogFile()
	{
		if(Storage::disk('public')->exists($this->logFile)){
			return response()->download(storage_path('app/public/'.$this->logFile));
		}
		return 'Log directory does not exist';
	}
}

