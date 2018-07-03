<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Report\ReportRepositoryInterface;
use Illuminate\Support\Facades\Artisan;
use Folklore\Image\Exception\Exception;
use App\Repositories\ProjectMember\ProjectMemberRepositoryInterface;
use App\Helper;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Tag\TagRepositoryInterface;
use App\Repositories\Client\ClientRepositoryInterface;
use Excel;
use App\Repositories\Role\RoleRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Holiday\HolidayRepository;

class ReportController extends Controller
{
    /**
     * 
     * @var ReportRepositoryInterface|\App\Repositories\RepositoryInterface
     */
    protected $reportRepository;
    
    /**
     * 
     * @param ReportRepositoryInterface $reportRepository
     */
    public function __construct(ReportRepositoryInterface $reportRepository, 
                                ProjectMemberRepositoryInterface $projectMemberRepository,
                                UserRepositoryInterface $user,
                                TagRepositoryInterface $tag,
                                ClientRepositoryInterface $client,
                                RoleRepositoryInterface $role,
                                HolidayRepository $holiday)
    {
        $this->project = $reportRepository;
        $this->projectMember = $projectMemberRepository;
        $this->user = $user;
        $this->tag = $tag;
        $this->client = $client;
        $this->role = $role;
        $this->_holiday = $holiday;
    }
    
    /**
     * @author huent6810
     * @todo view manager project screen
     * @return Ambigous <\Illuminate\View\View, \Illuminate\Contracts\View\Factory>
     */
    public function index()
    {
    	return view('project/index');
    }
    
    /**
     * @author huent6810
     * @todo get all project
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request)
    {
        if($this->checkRole($request->auth) == 1 || $this->checkRole($request->auth) == 2){
            $response = $this->project->getAll();
        }
        else 
            $response = $this->project->getProjecWithUser([$request->auth]);
        return response()->json($response, 200);
    }
    
    /**
     * @author huent6810
     * @todo crawler data from API
     * @return number
     */
    public function synchData(Request $request)
    {
        set_time_limit(0);
        
        $command = Artisan::call('synch:projects');
        if($command == 0){
            
            $request->session()->flash('message',[
                    'level' => 'success',
                    'title' => __('worktime.title'),
                    'content' => __('worktime.synch_success')
                ]);
            return $command;
        }
        $request->session()->flash('message',[
            'level' => 'danger',
            'title' => __('worktime.title'),
            'content' => __('worktime.synch_error')
            ]);
        return 1;
        
    }
    
    /**
     * @author huent6810
     * @todo only filter list projects with dateFrom and dateEnd
     * @param datetime $from
     * @param datetime $to
     * @return \Illuminate\Http\JsonResponse
     */
    public function filter(Request $request)
    {
        $response = $this->getWithConditionCheckbox($request);
        // set color piechart
        if($response){
            $color = Helper::renderColors(count($response['data']));
            for($i = 0; $i < count($response['data']); $i++)
                {
                    $response['data'][$i]['color'] = '#'.$color[$i];
                }
        }
        
        return response()->json($response, 200);
    }
    
    /**
     * @author huent6810
     * @todo check role user
     * @param int $user_id
     * @return number
     */
    public function checkRole($user_id)
    {
        $result = $this->role->findRoleByUserId($user_id);
        if(count($result) > 0){
            $arrRole = array_pluck($result, 'id');
            if(gettype(array_search(1, $arrRole)) != 'boolean'){ // 2: Manager, 1:Admin
                return 1; // admin
            }
            elseif (gettype(array_search(2, $arrRole)) != 'boolean'){
                return 2; // manager
            }
            else{
                return 0; // member
            }
        }
        else {// user is member
            return 0;
        }
    }
    /**
     * @author huent6810
     * @todo get data with condition in selectbox show in chart, table project in report, export file excel
     * if account login is manager or admin: can view report all of project with monthly or weekly
     * else if account is member: only view report of his/her worktime with monthl or weekly
     * @param Request $request
     * @return array
     */
    public function getWithConditionCheckbox(Request $request)
    {
        $project_accept = [];
        $flag = 1; // check role
        // check role, get all project which user can view in report
        if($this->checkRole($request->auth) == 1 || $this->checkRole($request->auth) == 2){
            $all_project = $this->project->getAll(); // manager or admin can view report of all projects
            if(count($all_project) > 0){
                $project_accept = array_pluck($all_project, 'id');
            }
        }
        else {// user is member
            $project_with_user_id = $this->project->getProjecWithUser([$request->auth]); // members can view report of their projects assigned
            if(count($project_with_user_id) > 0){
                $project_accept = array_pluck($project_with_user_id, 'id');
            }
            $flag = 0;
        }
        // get data
        if((int)$request->flag == 0){// change with date, event when click change month
            $response = $this->project->getWithDate($project_accept, $request->dateFrom, $request->dateTo);
        }
        else{ // change with condition select checkbox - event when click button apply
            if($request->project != null){
                $lstProjectCondition = explode(",", $request->project);
            }
            else
                $lstProjectCondition = [];
            
            $arr_intersect = [];// $arr_intersect = $lstProjectCondition intersect $project_accept
            for($i = 0; $i < count($lstProjectCondition); $i++){
                if(in_array($lstProjectCondition[$i], $project_accept)){
                    array_push($arr_intersect, $lstProjectCondition[$i]);
                }
            }
            $lstProjectCondition = $arr_intersect;
            // get list project with condition is tagId
            if($request->tag != null){
                if(count(explode(",", $request->tag)) > 0){
                    $projectWithTag = $this->project->getWorktimeWithTag($request->tag); // get project with tag
                    $arr_projectWithTag = [];
                    
                    if(count($projectWithTag) > 0){
                        for($i = 0; $i < count($projectWithTag); $i++){
                            if(in_array($projectWithTag[$i]->id, $project_accept) && !in_array($projectWithTag[$i]->id, $lstProjectCondition))
                            array_push($lstProjectCondition, $projectWithTag[$i]->id);
                        }
                    }
                }
            }
            // get items in $projectWithId which has project_id in client_id in $request->client
            $response = $this->project->getFilter($lstProjectCondition, $request->dateFrom, $request->dateTo);
        }
        // count standart time
        $list_holiday = $this->_holiday->getList();
        $holidays = count($list_holiday) > 0 ? array_pluck($list_holiday, 'day') : [];
        $workingday = Helper::getWorkingDays($request->dateFrom, $request->dateTo, $holidays);
        $standard_time_one_day = config('contains.STANDARD_TIME');
        
        // standard_time = standard_time/1day * total_member * days
        if(count($response['data']) > 0){
            for( $i = 0; $i < count($response['data']); $i++){
                $response['data'][$i]['standard_time'] = array_key_exists('total_member', $response['data'][$i])? $standard_time_one_day * $response['data'][$i]['total_member'] * $workingday : 0;
            }
        }
        return $response;
    }
    
    /**
     * @author huent6810
     * @todo get list tag
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllTag()
    {
        $response = $this->tag->getAll();
        return response()->json($response, 200);
    }
    
    /**
     * @author huent6810
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTagSelected(Request $request)
    {
        $response = $this->tag->getWithProject($request->project);
        return response()->json($response, 200);
    }
    
    /**
     * @author huent6810
     * @todo get list client
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClient(Request $request)
    {
        $response = $this->client->getAll();
        return response()->json($response, 200);
    }
    
    /**
     * @author huent6810
     * auto change selectboxes when one of conditions was change (ex: project changed => client &  tag changed)
     * @param Request $request
     * @return $arr data
     */
    public function synchCondition(Request $request)
    {
        $arr = [];
        $arr['project'] = [];
        $arr['client'] = [];
        $arr['tag'] = [];
        
        if($request->flag == 0){// change selectbox project
            $project = $this->project->getWithId($request->data);
            if(count($project) > 0){
                for($i = 0; $i < count($project); $i++){
                    $arr['client'] = $this->addArray($arr['client'], $project[$i]->clientid);
                }
            }
            $arr['project'] = array_map('intval', explode(",", $request->data));
        }
        else if($request->flag == 1){// change selectbox client
            $project = $this->project->getWithClient($request->data);
            if(count($project) > 0){
                for($i = 0; $i < count($project); $i++){
                    $arr['project'] = $this->addArray($arr['project'], $project[$i]->projectid);
                }
            }
            $arr['client'] = array_map('intval', explode(",", $request->data));
        }
        $arr_project_with_user = [];
        $arr_project_filte = [];
        if($this->checkRole(Auth::id()) == 0){
            $project_with_user = $this->project->getProjecWithUser([Auth::id()]);
            if(count($project_with_user) > 0){
                $arr_project_with_user = array_pluck($project_with_user, 'id');
            }
            for($i = 0; $i < count($arr['project']); $i++){
                if(in_array($arr['project'][$i], $arr_project_with_user)){
                    array_push($arr_project_filte, $arr['project'][$i]);
                }
            }
            $arr['project'] = $arr_project_filte;
        }
        return $arr;
    }
    
    /**
     * @author huent6810
     * @todo add/push an item in array
     * @param array $arr
     * @param int $value
     * @return array
     */
    public function addArray($arr, $value)
    {
        $index = array_search($value, $arr);
        if(gettype($index) == 'boolean'){
            array_push($arr,$value);
        }
        return $arr;
    }

    /**
     * @update tienhv update format excel
     * @author huent6810
     * @todo export data every week with format .xlsx
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function export(Request $request)
    {
        $data = $this->getDataForExcel($request);
        $arrayData              = $data['dataProjects'];
        $dataMembers            = array_unique($data['dataMembers']);
        $dataTotalTimeMembers   = $this->getDataTotal($data['dataTotalTimeMembers']);
        // create file => export data
        return Excel::create($request->dateFrom, function($excel) use ($arrayData,$dataMembers,$dataTotalTimeMembers) {
            $excel->sheet('Sheetname', function($sheet) use ($arrayData,$dataMembers,$dataTotalTimeMembers) {
                // set title collum
                $sheet->row(1, $this->getDefineHeaderExcel());
                $sheet->row(2, $this->getHeaderMemberFile($dataMembers));
                // set data
                $index = 3; $max_total_member = 0;
                for($i = 0; $i< count($arrayData); $i++){
                    if($max_total_member < count($arrayData[$i]['detailworktime']['username'])){
                        $max_total_member = count($arrayData[$i]['detailworktime']['username']);
                    }
                    $arr_row1 = $this->getArrayData($i, $arrayData[$i]);
                    foreach ($dataMembers as $member) {
                        $workTime = '';
                        for($j = 0; $j < count($arrayData[$i]['detailworktime']['username']); $j++){
                            if ($member == $arrayData[$i]['detailworktime']['username'][$j]) {
                                $workTime = $arrayData[$i]['detailworktime']['worktime'][$j];
                                break;
                            }
                        }
                        array_push($arr_row1, $workTime);
                    }
                    $sheet->row($index, $arr_row1);
                    $index ++;
                }
                $sheet->row($index,$dataTotalTimeMembers);
                //TODO set format excel
                //set bold
                $sheet->cells('A1:J1', function($cells) {
                    $cells->setFontWeight('bold');
                });
                $totalMember = count($dataMembers);
                //set border
                $arr_name_cell = Helper::getArrayNameFromNumber($totalMember + 9);
                $collum_name = $arr_name_cell[$totalMember + 8];
                $sheet->setBorder('A1:'.$collum_name.($index-1), 'thin');
                $sheet->setBorder('K1:'.$collum_name.(1), 'none');
                $arrayWidth = $this->defineWidth();
                //set Width
                for($i = 0; $i < count($arr_name_cell); $i++){
                    if (isset($arrayWidth[$arr_name_cell[$i]])) {
                        $sheet->setWidth($arr_name_cell[$i], $arrayWidth[$arr_name_cell[$i]]);
                    }
                }
                //position
                $sheet->setFreeze('J'. 2);

            });
        })->download($request->type);
    }

    /**
     * @author HueNT
     * @param $i, $dataProject
     * @return array
     */
    protected function getArrayData ($i,$dataProject) {
        return [
            $i + 1,
            $dataProject['project'],
            $dataProject['plan_start_time'],
            $dataProject['actual_start_time'],
            $dataProject['plan_end_time'],
            $dataProject['actual_end_time'],
            $dataProject['clientcode'],
            $dataProject['clientname'],
            $dataProject['totalworktime']
        ];
    }

    /**
     * @author HueNT
     * @todo get totol collumn
     * @param $dataTotalTimeMembers
     * @return array
     */
    protected function getDataTotal ($dataTotalTimeMembers) {
        $result = ['','','','','','','','',''];
        foreach ($dataTotalTimeMembers as $dataTotalTimeMember) {
            array_push($result,$dataTotalTimeMember);
        }
        return $result;
    }

    /**
     * @author HueNT
     * @todo function add list member to header excel
     * @param $memberNames
     * @return array $result
     */
    protected function getHeaderMemberFile($memberNames = []) {
        $result = ['','','','','','','','',''];
        $memberNames = array_unique($memberNames);
        foreach ($memberNames as $memberName) {
            $result[] = $memberName;
        }
        return $result;
    }

    /**
     * @author HueNT
     * @todo must define or config in config file
     * @return array column width
     */
    protected function defineWidth () {
        return array(
            'A'     =>  5,
            'B'     =>  30,
            'C'     =>  20,
            'D'     =>  20,
            'E'     =>  20,
            'F'     =>  15,
            'G'     =>  20,
            'H'     =>  15,
            'I'     =>  15
        );
    }

    /**
     * @author HueNT
     * @todo must define or config in config file
     * @return array
     */
    protected function getDefineHeaderExcel() {
        return  [
            '##',
            __('report.export.project_name'),
            __('report.export.plan_start_time'),
            __('report.export.actual_start_time'),
            __('report.export.plan_end_time'),
            __('report.export.actual_end_time'),
            __('report.export.client_code'),
            __('report.export.client_name'),
            __('report.export.worktime'),
            __('report.export.staff')
        ];
    }

    /**
     * @author HueNT
     * @param Request $request
     * @return array
     */
    protected function getDataForExcel(Request $request) {
        $result = [
            'dataProjects'      => [],
            'dataMembers'       => [],
        ];
        $arrayData              = [];
        $object                 = [];
        $dataMembers            = [];
        $dataTotalTimeMembers   = [];
        $response = $this->getWithConditionCheckbox($request);
        for($i = 0; $i < count($response['data']); $i++)
        {
            $object['number'] = $i + 1;
            $object['project'] = $response['data'][$i]['name'];
            $object['clientcode'] = array_key_exists('clientcode',$response['data'][$i]) ? $response['data'][$i]['clientcode'] : null;
            $object['clientname'] = array_key_exists('clientname',$response['data'][$i]) ? $response['data'][$i]['clientname'] : null;
            $object['actual_start_time'] = array_key_exists('actual_start_time',$response['data'][$i]) ? $response['data'][$i]['actual_start_time'] : null;
            $object['actual_end_time'] = array_key_exists('actual_end_time',$response['data'][$i]) ? $response['data'][$i]['actual_end_time'] : null;
            $object['plan_start_time'] = array_key_exists('plan_start_time',$response['data'][$i]) ? $response['data'][$i]['plan_start_time'] : null;
            $object['plan_end_time'] = array_key_exists('plan_end_time',$response['data'][$i]) ? $response['data'][$i]['plan_end_time'] : null;

            $object['detailworktime'] = [];
            $object['detailworktime']['username']= [];
            $object['detailworktime']['worktime']= [];

            if(array_key_exists('total_hour',$response['data'][$i])){
                if($this->checkRole($request->auth) == 1 || $this->checkRole($request->auth) == 2){
                    $list_member = $this->projectMember->getWithProjectId($response['data'][$i]['id'], $request->dateFrom, $request->dateTo);
                }
                else{
                    $list_member = $this->projectMember->getWithProjectIdUserId($response['data'][$i]['id'], Auth::id(), $request->dateFrom, $request->dateTo);
                }
                if($list_member){
                    for($j = 0; $j < count($list_member); $j++){
                        array_push($object['detailworktime']['username'], $list_member[$j]->username);
                        array_push($object['detailworktime']['worktime'], $list_member[$j]->work_time);
                        $dataMembers[] = $list_member[$j]->username;
                        if (array_key_exists($list_member[$j]->username,$dataTotalTimeMembers)) {
                            $dataTotalTimeMembers[$list_member[$j]->username] += $list_member[$j]->work_time;
                        }else {
                            $dataTotalTimeMembers[$list_member[$j]->username] = $list_member[$j]->work_time;
                        }
                    }
                }
            }
            $object['totalworktime'] = array_key_exists('total_hour',$response['data'][$i]) ? $response['data'][$i]['total_hour'] : 0;
            array_push($arrayData,$object);
        }
        $result['dataProjects']             = $arrayData;
        $result['dataMembers']              = $dataMembers;
        $result['dataTotalTimeMembers']     = $dataTotalTimeMembers;
        return $result;
    }
}
