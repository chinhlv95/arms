<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Message;
use GuzzleHttp\Message\Response;
use App\Config;
use Folklore\Image\Exception\Exception;
use App\Repositories\ProjectMember\ProjectMemberRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\ProjectMember\ProjectMemberRepositoryInterface;
use App\Repositories\AssignProject\AssignProjectRepositoryInterface;
use App\Repositories\TagProjectUser\TagWorktimeRepositoryInterface;
use App\Models\ProjectProjectJP;
use App\Repositories\Report\ReportRepositoryInterface;

/**
 * command get data projects from API
 * @author huent6810
 *
 */
class crawlerProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synch:projects';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchrnozation data from Mtool';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ProjectMemberRepositoryInterface $project_member, 
                                UserRepositoryInterface $user,
                                AssignProjectRepositoryInterface $assignProject,
                                TagWorktimeRepositoryInterface $tag_worktime, 
                                ReportRepositoryInterface $project)
    {
        parent::__construct();
        
        $this->project = $project;
        $this->projectmember = $project_member;
        $this->user = $user;
        $this->assignProject = $assignProject;
        $this->url = config('contains.API_RESOURCE'); // get link API
        $this->tagWorktime = $tag_worktime;
        $this->resource = config('contains.RESOURCE_DATA');
        $this->_access_token = config('contains._ACCESS_TOKEN');
    }
   
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new Client();
        
        $response = $client->get($this->url['project_mtool'].'?access_token='.$this->_access_token);
        $result = json_decode((string) $response->getBody(), true);
        if(count($result) > 0){
            $arrProject = [];
            // crawler new data
            for($i = 0; $i < count($result); $i++)
            {
                $data = [
                    'name' => $result[$i]['name'],
                    'actual_start_time' => $result[$i]['actual_start_date'] != null && $result[$i]['actual_start_date'] != '0000-00-00' ? date('Y-m-d',strtotime($result[$i]['actual_start_date'])) : null,
                    'actual_end_time' => $result[$i]['actual_end_date'] != null && $result[$i]['actual_end_date'] != '0000-00-00' ? date('Y-m-d',strtotime($result[$i]['actual_end_date'])) : null,
                    'intergreated_project_id' => (int)($result[$i]['project_id']),
                    'id_project_resource' => $this->resource['mtool'],
                    'id_mtool_resource' => $result[$i]['source_id'] ? $result[$i]['source_id'] : null,
                    'key' => $result[$i]['project_key'] ? $result[$i]['project_key'] : null
                ];
                // synch data for projects table
                $id = $this->project->checkExits($data['intergreated_project_id']);
                
                if($id != 0) { // exists in database => update record
                    $this->project->update($id, $data);
                }
                else // create
                    $this->project->create($data);
                    // after update table projects => update data for table project_user
                $this->updateProjectUser((int)($result[$i]['project_id']));
                
                $project_with_intergreated_id = $this->project->findWithIntergreatedId((int)($result[$i]['project_id']));
                if($project_with_intergreated_id){
                    array_push($arrProject, $project_with_intergreated_id->id);
                }
            }
            // remove all items in ARMS which does not exits in Mtool
            $this->cleanDataARMS($arrProject);
        }
    }
    
    /**
     * synch data project-user from api
     * @auhtor huent6810 
     * @param int $project_id
     */
    public function updateProjectUser($project_id)
    {
        $project = $this->project->findWithIntergreatedId($project_id);
        $client = new Client();
        $response = $client->get($this->url['project_user_mtool'] . $project_id .'?access_token='.$this->_access_token);
        $result = json_decode((string) $response->getBody(), true);

        if(count($result) > 0)
        {
            for($i = 0; $i < count($result); $i++)
            {
                $data = [
                    'username' => $result[$i]['first_name'],
                    'fullname' => $result[$i]['first_name'] .' '. $result[$i]['last_name'],
                    'password' => bcrypt('12345678'), // default
                    'email' => null,
                    'id_resource' => $this->resource['mtool'], // 0:LDap, 1:ARMS System, 2: Mtool
                    'intergreated_user_id' => $result[$i]['id']
                ];
                // check email => get username
                if($result[$i]['email'] != null)
                {
                    $arr = explode("@",$result[$i]['email']);
                    $data['email'] = $result[$i]['email'];
                    $data['username'] = $arr[0];
                }
                // check unique username 
                $checkUniqueUsename = $this->user->checkUniqueCreate('username', $data['username']);  // 1: exists ->update intergreated_user_id; 0: not exists-> create new
            if($checkUniqueUsename == 0){
                    // process insert users table then insert projects_user table
                    $id_inserted = $this->user->insertGetId($data);
                    $dataProjectMember = [
                        'project_id' => $project->id,
                        'user_id' => $id_inserted,
                        'work_time' => $result[$i]['actual_hour'],
                        'working_date' => $result[$i]['spent_at'],
                        'mtool_entry_id' => $result[$i]['entry_id']
                    ];
                    $checkAssigned = $this->assignProject->checkExist($project->id, $id_inserted);
                    if($checkAssigned == 0){// assign new
                        $this->assignProject->insert($project->id, $id_inserted);
                    }
                    $this->projectmember->create($dataProjectMember);
                }
                if($checkUniqueUsename == 1)
                {
                    // process get id_user then check record in project_user
                    $user = $this->user->findByField('username', $data['username']);
                    if($user)
                    {
                        $user_id = $user->id;
                        $this->user->update($user_id, ['intergreated_user_id' => $data['intergreated_user_id']]);
                        $check = $this->projectmember->checkExitWithMtoolEntryId($result[$i]['entry_id']);
                        $dataProjectMember = [
                            'project_id' => $project->id,
                            'user_id' => $user_id,
                            'work_time' => $result[$i]['actual_hour'],
                            'working_date' => $result[$i]['spent_at'],
                            'mtool_entry_id' => $result[$i]['entry_id']
                        ];
                        // assign member for project
                        $checkAssigned = $this->assignProject->checkExist($project->id, $user_id);
                        if($checkAssigned == 0){// assign new
                            $this->assignProject->insert($project->id, $user_id);
                        } 
                        // input worktime for user
                        if($check > 0) // update
                        {
                            $this->projectmember->updateSynch($result[$i]['entry_id'], [
                                'work_time' => $result[$i]['actual_hour'], 
                                'working_date' => $result[$i]['spent_at']
                            ]);
                        }
                        else // create
                        {
                            $this->projectmember->create($dataProjectMember);
                        }
                    }
                }
            }
        }
    }
    
    /**
     * @author huent6810
     * remove all items in ARMS which does not exits in Mtool
     * @param array $data: $data is all projects exist in Mtool, can't remove/delete
     */
    public function cleanDataARMS($data)
    {
        $projects_arms = $this->project->getWithIdResource($this->resource['mtool']);
        if(count($projects_arms) > 0){
            for($i = 0; $i < count($projects_arms); $i++){
                $index = array_search($projects_arms[$i]->id, $data);
                if(gettype($index) == 'boolean'){// $projects_arms[$i] has removed or deleted in Mtool
                    $worktime = $this->projectmember->findAllByProjectId($projects_arms[$i]->id);
                    // delete foreign in tag_project_user first
                    if(count($worktime)){
                        for ($j = 0; $j < count($worktime); $j++){
                            $this->tagWorktime->deleteWithWorktime($worktime[$j]->id);
                        }
                    }
                    // delete foreign in project_user
                    $this->projectmember->deleteWithProjectId($projects_arms[$i]->id);
                    // delete foreign in assigned_project
                    $this->assignProject->deleteWithProjectId($projects_arms[$i]->id);
                    // delete foreign in project_project_jp table
                    $project_jp = ProjectProjectJP::where('project_id', $projects_arms[$i]->id)->first();
                    if($project_jp){
                        ProjectProjectJP::where('project_id', $projects_arms[$i]->id)->delete();
                    }
                    // delete record in table projects
                    $this->project->delete($projects_arms[$i]->id);
                }
            }
        }
    }
}
