<?php
namespace App\Repositories\Report;

use App\Repositories\EloquentRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Message;
use GuzzleHttp\Message\Response;
use App\Repositories\ProjectMember\ProjectMemberRepositoryInterface;
use DB;
use GuzzleHttp\json_encode;
use App\Helper;
use Illuminate\Support\Facades\Auth;

class ReportRepository extends EloquentRepository implements ReportRepositoryInterface{
    
    /**
     * @author huent6810
     * Get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Project::class;
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\Report\ReportRepositoryInterfaceInterface::getPaginate()
     */
    public function getPaginate($perpage)
    {   
        $items = $this->_model->leftJoin('worktimes', 'projects.id', '=', 'worktimes.project_id')
                                ->select(DB::raw('SUM(worktimes.work_time) as total_hour, projects.name, projects.id, COUNT( DISTINCT worktimes.user_id) as total_member'))
                                ->groupBy('projects.id', 'projects.name')
                                ->orderBy('total_hour','desc')
                                ->paginate($perpage);
        if(!$items){
            return false;
        }
        return $this->setPaginate($items);
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\Report\ReportRepositoryInterfaceInterface::synchSelectBox()
     */
    public function getWithId($project = null)
    {
        if($project == null){
           return $response = [];
        }
        $response = $this->_model->leftJoin('clients', 'projects.client_id', '=', 'clients.id')
                                ->select('projects.id AS projectid','clients.id AS clientid')
                                ->whereIn('projects.id', explode(",",$project))
                                ->get();
        return $response;
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\Report\ReportRepositoryInterfaceInterface::getProjecWithUser()
     */
    public function getProjecWithUser($member = null)
    {
        if(count($member) > 0){
            $response = $this->_model->leftJoin('assigned_project', 'projects.id', '=', 'assigned_project.project_id')
                    ->whereIn('assigned_project.user_id', $member)
                    ->select('projects.id', 'projects.name')
                    ->distinct('projects.id')
                    ->get();
        }
        else
            $response = [];
        return $response;
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\Report\ReportRepositoryInterfaceInterface::getWithClient()
     */
    public function getWithClient($client = null)
    {
        if($client == null){
            $response = $this->_model->select('projects.id AS projectid')
                    ->whereNull('projects.client_id')
                    ->get();
        }
        else
            $response = $this->_model->leftJoin('clients', 'projects.client_id', '=', 'clients.id')
                ->select('projects.id AS projectid','clients.id AS clientid')
                ->whereIn('clients.id', explode(",",$client))
                ->get();
        
        return $response;
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\Report\ReportRepositoryInterfaceInterface::getWorktimeWithId()
     */
    public function getWorktimeWithId($project = null)
    {
        if($project == null){
            return $response = [];
        }
        $response = $this->_model->leftJoin('clients', 'projects.client_id', '=', 'clients.id')
                                ->leftJoin('worktimes', 'projects.id', '=', 'worktimes.project_id')
                                ->select('projects.id AS projectid','clients.id AS clientid')
                                ->whereIn('projects.id', $project)
                                ->distinct('projects.id')
                                ->get();
        return $response;
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\Report\ReportRepositoryInterfaceInterface::getWorktimeWithTag()
     */
    public function getWorktimeWithTag($tag = null)
    {
        if($tag == null){
            $response = $this->_model->select('projects.id', 'projects.name')
                ->whereNotIn('projects.id', function($query) use ($tag){
                    $query->select('worktimes.project_id')
                        ->from('tag_project_user')
                        ->leftJoin('worktimes', 'worktimes.id','=', 'tag_project_user.project_user_id')
                        ->distinct('worktimes.project_id');
                })
                ->get();
        }
        else
            $response = $this->_model->leftJoin('worktimes', 'projects.id', '=', 'worktimes.project_id')
                ->select('projects.id', 'projects.name')
                ->whereIn('worktimes.id', function($query) use ($tag){
                    $query->select('tag_project_user.project_user_id')
                        ->distinct('tag_project_user.project_user_id')
                        ->from('tag_project_user')
                        ->whereIn('tag_project_user.tag_id', explode(",", $tag));
                })
                ->distinct('worktimes.project_id')
                ->get();
        
        return $response;
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\Report\ReportRepositoryInterfaceInterface::getFilter()
     */
    public function getFilter($project = null, $dateFrom, $dateTo)
    {
        if(count($project) == 0){
            $dataBarchart = []; 
            $items = [];
        }
        else{
            if(Helper::check_permission(1) == 1 || Helper::check_permission(2) == 1){// admin or manager
                // get data show in table report
                $items = $this->_model->leftJoin('worktimes', 'projects.id', '=', 'worktimes.project_id')
                    ->leftJoin('clients', 'projects.client_id', '=', 'clients.id')
                    ->whereIn('projects.id', $project)
                    ->whereBetween('working_date', [date('Y-m-d',strtotime($dateFrom)), date('Y-m-d',strtotime($dateTo))])
                    ->select(DB::raw('SUM(work_time) AS total_hour, projects.id, projects.name, actual_start_time, actual_end_time, plan_start_time, plan_end_time, projects.id_mtool_resource, projects.key, COUNT( DISTINCT worktimes.user_id) AS total_member, clients.id AS clientid, clients.code AS clientcode, clients.name AS clientname'))
                    ->groupBy('projects.id', 'projects.name', 'projects.id_mtool_resource', 'projects.key', 'actual_start_time', 'actual_end_time', 'plan_start_time', 'plan_end_time','clients.id', 'clients.code', 'clients.name')
                    ->orderBy('total_hour','desc')
                    ->get()->toArray();
                
                // get data for barchart. count work_time and groupBy with day
                $dataBarchart = $this->_model->leftJoin('worktimes', 'projects.id', '=', 'worktimes.project_id')
                    ->whereIn('worktimes.project_id', $project)
                    ->select(DB::raw('SUM(worktimes.work_time) AS total_hour, worktimes.working_date'))
                    ->whereBetween('working_date', [date('Y-m-d',strtotime($dateFrom)), date('Y-m-d',strtotime($dateTo))])
                    ->groupBy('worktimes.working_date')
                    ->orderBy('working_date','asc')
                    ->get();
            }
            else{// member
                // get data show in table report
                $items = $this->_model->leftJoin('worktimes', 'projects.id', '=', 'worktimes.project_id')
                    ->leftJoin('clients', 'projects.client_id', '=', 'clients.id')
                    ->whereIn('projects.id', $project)
                    ->where('worktimes.user_id', Auth::id())
                    ->whereBetween('working_date', [date('Y-m-d',strtotime($dateFrom)), date('Y-m-d',strtotime($dateTo))])
                    ->select(DB::raw('SUM(work_time) AS total_hour, projects.id, projects.name, actual_start_time, actual_end_time, plan_start_time, plan_end_time, projects.id_mtool_resource, projects.key, COUNT( DISTINCT worktimes.user_id) AS total_member, clients.id AS clientid, clients.code AS clientcode, clients.name AS clientname'))
                    ->groupBy('projects.id', 'projects.name', 'projects.id_mtool_resource', 'projects.key', 'actual_start_time', 'actual_end_time', 'plan_start_time', 'plan_end_time', 'clients.id', 'clients.code', 'clients.name')
                    ->orderBy('total_hour','desc')
                    ->get()->toArray();
                
                // get data for barchart. count work_time and groupBy with day
                $dataBarchart = $this->_model->leftJoin('worktimes', 'projects.id', '=', 'worktimes.project_id')
                    ->whereIn('worktimes.project_id', $project)
                    ->where('worktimes.user_id', Auth::id())
                    ->select(DB::raw('SUM(worktimes.work_time) AS total_hour, worktimes.working_date'))
                    ->whereBetween('working_date', [date('Y-m-d',strtotime($dateFrom)), date('Y-m-d',strtotime($dateTo))])
                    ->groupBy('worktimes.working_date')
                    ->orderBy('working_date','asc')
                    ->get();
            }
            // get info of projects selected which have not worktime in range date. In template will display worktime is 0(h)
            if(count($items) > 0){
                for($i = 0; $i < count($items); $i++){
                    $index = array_search($items[$i]['id'], $project);
                    if(gettype($index) != "boolean"){
                        array_splice($project, $index, 1);
                    }
                }
            }
            if(count($project) > 0){
                $items_null_data = $this->_model->select('projects.id', 'projects.name', 'projects.key', 'projects.id_mtool_resource')
                ->whereIn('projects.id', $project)
                ->get()->toArray();
                $items = array_merge($items, $items_null_data);
            }
        }
        $response = [];
        $response['chart'] =  $dataBarchart;
        $response['data'] = $items;
        return $response;
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\Report\ReportRepositoryInterfaceInterface::getWithDate()
     */
    public function getWithDate($project, $dateFrom, $dateTo)
    {
        $items = [];
        $dataBarchart = [];
       
        if(count($project) > 0){
            if(Helper::check_permission(1) == 1 || Helper::check_permission(2) == 1){// manager or admin
                $items = $this->_model->leftJoin('worktimes', 'projects.id', '=', 'worktimes.project_id')
                    ->leftJoin('clients', 'projects.client_id', '=', 'clients.id')
                    ->whereIn('worktimes.project_id', $project)
                    ->whereBetween('working_date', [date('Y-m-d',strtotime($dateFrom)), date('Y-m-d',strtotime($dateTo))])
                    ->select(DB::raw('SUM(work_time) AS total_hour, projects.id, projects.name, actual_start_time, actual_end_time, plan_start_time, plan_end_time, projects.id_mtool_resource, projects.key, COUNT( DISTINCT worktimes.user_id) AS total_member, clients.id AS clientid, clients.code AS clientcode, clients.name AS clientname'))
                    ->groupBy('projects.id','projects.name', 'projects.id_mtool_resource', 'projects.key', 'actual_start_time', 'actual_end_time', 'plan_start_time', 'plan_end_time', 'clients.id', 'clients.code', 'clients.name')
                    ->orderBy('total_hour','desc')
                    ->get()->toArray();
             
                // count work_time and groupBy with day of week
                $dataBarchart = $this->_model->leftJoin('worktimes', 'projects.id', '=', 'worktimes.project_id')
                    ->whereIn('worktimes.project_id', $project)
                    ->select(DB::raw('SUM(worktimes.work_time) AS total_hour, worktimes.working_date'))
                    ->whereBetween('working_date', [date('Y-m-d',strtotime($dateFrom)), date('Y-m-d',strtotime($dateTo))])
                    ->groupBy('worktimes.working_date')
                    ->orderBy('working_date','asc')
                    ->get();
            }
            else{ // member
                $items = $this->_model->leftJoin('worktimes', 'projects.id', '=', 'worktimes.project_id')
                    ->leftJoin('clients', 'projects.client_id', '=', 'clients.id')
                    ->whereIn('worktimes.project_id', $project)
                    ->where('worktimes.user_id', Auth::id())
                    ->whereBetween('working_date', [date('Y-m-d',strtotime($dateFrom)), date('Y-m-d',strtotime($dateTo))])
                    ->select(DB::raw('SUM(work_time) AS total_hour, projects.id, projects.name, actual_start_time, actual_end_time, plan_start_time, plan_end_time, projects.id_mtool_resource, projects.key, COUNT( DISTINCT worktimes.user_id) AS total_member, clients.id AS clientid, clients.code AS clientcode, clients.name AS clientname'))
                    ->groupBy('projects.id','projects.name', 'projects.id_mtool_resource', 'projects.key', 'actual_start_time', 'actual_end_time', 'plan_start_time', 'plan_end_time', 'clients.id', 'clients.code', 'clients.name')
                    ->orderBy('total_hour','desc')
                    ->get()->toArray();
                 
                // count work_time and groupBy with day of week
                $dataBarchart = $this->_model->leftJoin('worktimes', 'projects.id', '=', 'worktimes.project_id')
                    ->whereIn('worktimes.project_id', $project)
                    ->where('worktimes.user_id', Auth::id())
                    ->select(DB::raw('SUM(worktimes.work_time) AS total_hour, worktimes.working_date'))
                    ->whereBetween('working_date', [date('Y-m-d',strtotime($dateFrom)), date('Y-m-d',strtotime($dateTo))])
                    ->groupBy('worktimes.working_date')
                    ->orderBy('working_date','asc')
                    ->get();
            }
        }
        $response = [];
        
        $response['chart'] =  $dataBarchart;
        $response['data'] = $items;
        
        return $response;
    }
    
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\Report\ReportRepositoryInterfaceInterface::getSearchFilter()
     */
    public function getSearchFilter($from, $to, $key, $perpage)
    {
        $items = $this->_model->leftJoin('worktimes', 'projects.id', '=', 'worktimes.project_id')
                    ->select(DB::raw('SUM(worktimes.work_time) as total_hour, projects.name, projects.id, COUNT( DISTINCT worktimes.user_id) as total_member'))
                    ->groupBy('projects.id', 'projects.name')
                    ->whereBetween('working_date', [date('Y-m-d',strtotime($from)), date('Y-m-d',strtotime($to))])
                    ->where('projects.name', 'like', '%'.trim($key).'%')
                    ->orderBy('total_hour','desc')
                    ->paginate($perpage);
        if(!$items){
            return false;
        }
        return $this->setPaginate($items);
    }
    /**
     * @author huent6810
     * setup paniagate default to get data
     * @see \App\Repositories\Report\ReportRepositoryInterfaceInterface::setPaginate()
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
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\Report\ReportRepositoryInterfaceInterface::checkExits()
     */
    public function checkExits($project_id)
    {
        $result = $this->_model->where('intergreated_project_id', $project_id)->first();
        if($result)
        {
            return $result->id;
        }
        return 0;
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\Report\ReportRepositoryInterfaceInterface::findWithIntergreatedId()
     */
    public function findWithIntergreatedId($intergreated_project_id)
    {
        $result = $this->_model->where('intergreated_project_id', $intergreated_project_id)->first();
        if(!$result){
            return false;
        }
        return $result;
    }
    
    /**
     * @author huent6810
     * search project with name
     * (non-PHPdoc)
     * @see \App\Repositories\Report\ReportRepositoryInterfaceInterface::search()
     */
    public function search($key, $paginate)
    {
        $items = $this->_model->leftJoin('worktimes', 'projects.id', '=', 'worktimes.project_id')
                                ->select(DB::raw('SUM(worktimes.work_time) as total_hour, projects.name, projects.id, COUNT( DISTINCT worktimes.user_id) as total_member'))
                                ->where('projects.name', 'like', '%'.trim($key).'%')
                                ->groupBy('projects.id', 'projects.name')
                                ->orderBy('total_hour','desc')
                                ->paginate($paginate);
        if(!$items){
            return false;
        }
        return $this->setPaginate($items);
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\Report\ReportRepositoryInterfaceInterface::getWithIdResource()
     */
    public function getWithIdResource($resource)
    {
        $items = $this->_model->where('id_project_resource', $resource)->get();
        return $items;
    }
}