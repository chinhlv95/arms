<?php

namespace App\Repositories\WorkTime;

use App\Models\Project;
use App\Repositories\EloquentRepository;
use DB;
use function foo\func;
use Illuminate\Support\Facades\Auth;
use App\Models\ProjectUser;
use App\Models\Tag;
use App\Models\TagProjectUser;
use App\Helper;
use App\Config;

class WorkTimeRepository extends EloquentRepository implements WorkTimeRepositoryInterface {

    /**
     * @author TheuNT
     * Get Model
     */
    public function getModel() {
        return \App\Models\ProjectUser::class;
    }

    public $offSetDate;

    /**
     * @author tienhv6815
     * @todo get list worktime by Date | limit : 7 days
     * @param $limit
     * @return array
     */
    public function getListWorkTimeDateByDateGoBack($limit) {
        $items = DB::table('worktimes')
            ->select('working_date')
            ->where('user_id', '=', Auth::id())
            ->where('working_date','<=',date('Y-m-d'))
            ->orderby('working_date', 'DESC')
            ->groupBy('working_date')
            ->limit($limit)
            ->get();
        $result = [];
        foreach ($items as $item) {
            $result[] = $item->working_date;
        }
        return $result;
    }

    /**
     * @author tienhv6815
     * @todo get list worktime by Date | limit : 7 days
     * @param $limit
     * @return array
     */
    public function getListByDateGoBack($limit) {
        $listWorkTimes =  $this->getListWorkTimeDateByDateGoBack($limit);
        $items = DB::table('worktimes as U')
            ->leftJoin('projects as P', 'U.project_id', '=', 'P.id')
            ->leftJoin('tag_project_user as TU', 'P.id', '=', 'TU.project_user_id')
            ->leftJoin('tags as T', 'T.id', '=', 'TU.tag_id')
            ->select('U.id', 'U.project_id', 'P.name', 'U.working_date', 'U.work_time')
            ->where('U.user_id', '=', Auth::id())
            ->whereIn('U.working_date', $listWorkTimes)->orderby('U.working_date', 'DESC')
            ->get();
        return $items;
    }

    /**
     * @update tienhv6815
     * @todo get list by date
     * @author TheuNT
     * @param $limit
     * @return array
     */
    public function getList($limit) {

        //get number items worktime by number date => now 5 entry
        $items = $this->getListByDateGoBack($limit);
        //set array color for all projects
        $projects_id_color = Project::select('id')->get();
        $projects_array_colors = [];

        foreach ($projects_id_color as $key => $color)
        {
            array_push($projects_array_colors, $color->id);
        }

        //set result array data
        $data = ['data' => [], 'color' => [], 'total_project_work_time' => 0];

        //if !empty $items => set array data
        if (!empty($items)) {

            foreach ($items as $k => $item) {

                //set date by local
                $date = app()->getLocale() === 'vi' ? date('d-m-Y') : date('Y-m-d');
                //set date yesterday by local
                $dataYesterday = app()->getLocale() === 'vi' ? date('d-m-Y', strtotime('-1 day')) : date('Y-m-d', strtotime('-1 day'));
                //set date format by local
                $dateFomat = app()->getLocale() === 'vi' ? date('d-m-Y', strtotime($item->working_date)) : date('Y-m-d', strtotime($item->working_date));

                //set key data with date for Today and Yesterday
                switch ($dateFomat) {
                    case $date :
                        $key_data = "Today";
                        break;
                    case $dataYesterday :
                        $key_data = "Yesterday";
                        break;
                    default:
                        $key_data = $dateFomat;
                        break;
                }

                //get tag by project user ( worktime ) id
                $item->tags = $this->getTagByWorkTimeId($item->id);

                if (!isset($data['data'][$key_data])) {
                    $data['data'][$key_data] = [
                        'projects' => [],
                        'total' => 0
                    ];
                }

                $data['data'][$key_data]['projects'][] = $item;
                //set total worktime
                $data['data'][$key_data]['total'] += $item->work_time;
            }

            $data['color'] = Helper::renderColors($projects_array_colors);
        }

        //count total project this user has work_time
        $allItems = $items = DB::table('worktimes as U')
                ->where('U.user_id', '=', Auth::id())
                ->get();

        $data['total_project_work_time'] = count($allItems);

        return $data;
    }

    /**
     * @todo get tag by worktime
     * @author DungHT
     * @param $id | worktime id
     * @return array data
     */
    public function getTagByWorkTimeId($id) {
        $item = DB::table('tags as T')
                ->select('T.tag_name')
                ->leftJoin('tag_project_user as TU', 'TU.tag_id', '=', 'T.id')
                ->where('TU.project_user_id', '=', $id)
                ->get();

        $tags = [];
        $tag_name = [];
        
        $tag_color = Helper::renderColors(count($item->toArray()));
        
        if ($item) {
            foreach ($item as $tag) {
                $tag_name[] = $tag->tag_name;
            }
        }
        array_push($tags, ['color' => $tag_color, 'name' => $tag_name]);
        return $tags;
    }

    /**
     * @author TheuNT
     * @param $id | project id
     * @todo return last project update to set selected in vue template | name, id
     */
    public function getProjectById($id) {
        $items = DB::table('projects')
                ->select('id', 'name')
                ->where('id', '=', $id)
                ->first();

        return isset($items->name) ? $items->name : null;
    }

    /**
     * @author TheuNT
     * @param $id | project id
     * @todo return arms project list
     * @return array data
     */
    public function getProject(){

        $items = DB::table('projects')
                ->select('name','id')
                ->get();

        return $items;
    }

    /**
     * @author TheuNT
     * @todo check time % 0.25
     * @param $time
     * @return boolean
     */
    public function checkTime($time) {
        if (!$time || $time < 0 || $time % 25 != 0) {
            return false;
        }
        return true;
    }

    /**
     * @author TheuNT
     * @todo check selected project or not
     * @param $time
     * @return boolean
     */
    public function checkProject($param) {
        
        if (!(int) $param) {
            return false;
        }
        $item = DB::table('projects')
                        ->select('id')
                        ->where('id', '=', $param)->count();
        return $item;
    }

    public $errors = [];
    protected $_data = [];

    /**
     * @author TheuNT
     * @todo Add data to array create worktime
     * @param $time
     * @return boolean
     */
    public function setDataAddNew($data = []) {
        $this->_data = $data;
        return $this;
    }

    /**
     * @author TheuNT
     * @todo Validate create worktime
     * @return boolean
     */
    public function validateWorkTime() {
        
        if (empty($this->_data)) {
            return false;
        }

        //check hours is must be a multiple of 0.25
        $validateTime = $this->checkTime(isset($this->_data['entry_time']) ? $this->_data['entry_time'] * 100 : null);
        if (!$validateTime) {
            $this->errors['working_time'] = __('worktime.persional_time.error_entry_time');
        }

        $validateProject = $this->checkProject(isset($this->_data['project_id']) ? $this->_data['project_id'] : null);
        if (!$validateProject) {
            $this->errors['select_project'] = __('worktime.persional_time.error_project_time');
        }

        return ($validateTime && $validateProject);
    }

    /**
     * @author TheuNT
     * @todo Create project | create worktime task
     * @return last insert id
     */

    protected $_project_user_add = null;

    public function createProject() {
        $item = new ProjectUser();

        $date = date('Y-m-d', strtotime($this->_data['working_date']));
        $resource = config('contains.ID_RESOURCE');

        $item->project_id = $this->_data['project_id'];
        $item->user_id = Auth::user()->id;
        $item->id_resource = 1; //Created by ARMS System
        $item->work_time = $this->_data['entry_time'];
        $item->working_date = $date;

        if ($item->save()) {
            $this->_project_user_add = $item->id;
        }
    }

    /**
     * @author TheuNT
     * @todo Create tag worktime | create worktime tag, tag_project_user table
     */

    protected $_tags_add = [];

    public function createTagProjectUser() {
        if (empty($this->_tags_add) || !$this->_project_user_add) {
            return false;
        }
        foreach ($this->_tags_add as $tag) {
            $item = new TagProjectUser;
            $item->project_user_id = $this->_project_user_add;
            $item->tag_id = $tag;
            $item->save();
        }
    }

    /**
     * @author TheuNT
     * @todo Create tag worktime | tags table
     */
    public function createTag() {
        if (!isset($this->_data['tags']) || empty($this->_data['tags'])) {
            return false;
        }

        foreach ($this->_data['tags'] as $tag) {
            $item = Tag::where('tag_name', '=', $tag)->first();
            
            if (!$item) {
                $item = new Tag;
                $item->tag_name = $tag;
                $item->save();
            }

            if (isset($item->id)) {
                $this->_tags_add[] = $item->id;
            }
        }
    }


    /**
     * @author TheuNT
     * @todo Create worktime
     * @return boolean
     */
    public function createWorkTime() {
        if (!$this->validateWorkTime()) {
            return false;
        }

        $this->createTag();
        $this->createProject();
        $this->createTagProjectUser();

        return $this->getListByDate();
    }

    /**
     * @author TheuNT
     * @todo Get list worktime by ranger date | 7 days
     * @return array
     */
    public function getListByDate() {
        if (!isset($this->_data['working_date'])) {
            return [];
        }

        $back_date = date('Y-m-d', strtotime($this->offSetDate . ' -7 days'));
        $items = DB::table('worktimes as U')
            ->leftJoin('projects as P', 'U.project_id', '=', 'P.id')
            ->leftJoin('tag_project_user as TU', 'P.id', '=', 'TU.project_user_id')
            ->leftJoin('tags as T', 'T.id', '=', 'TU.tag_id')
            ->select('P.name', 'U.id', 'U.working_date', 'U.work_time')
            ->where('U.user_id', '=', Auth::id())
            ->where('U.id_resource', 1)
            ->where('U.working_date', '=', date('Y-m-d', strtotime($this->_data['working_date'])))
            ->get();

        $data = [];

        if (!empty($items)) {


            foreach ($items as $item) {
                $date = app()->getLocale() === 'vi' ? date('d-m-Y') : date('Y-m-d');
                $dataYesterday = app()->getLocale() === 'vi' ? date('d-m-Y', strtotime('-1 day')) : date('Y-m-d', strtotime('-1 day'));
                $dateFomat = app()->getLocale() === 'vi' ? date('d-m-Y', strtotime($item->working_date)) : date('Y-m-d', strtotime($item->working_date));

                switch ($dateFomat) {
                    case $date :
                        $key_data = "Today";
                        break;
                    case $dataYesterday :
                        $key_data = "Yesterday";
                        break;
                    default:
                        $key_data = $dateFomat;
                        break;
                }

                $item->tags = $this->getTagByWorkTimeId($item->id);

                if (!isset($data[$key_data])) {
                    $data[$key_data] = [$item];
                } else {
                    $data[$key_data][] = $item;
                }
            }
        }

        return $data;
    }

    /**
     * @author TheuNT
     * @todo Update project
     * @param $id, $project_id
     * @return boolean
     */
    public function updateProject($id, $project_id) {
        $items = DB::table('worktimes')
                ->where('worktimes.id', '=', $id)
                ->where('worktimes.user_id', '=', Auth::id())
                ->update(['worktimes.project_id' => $project_id]);

        if ($items) {
            return $this->getProjectById($project_id);
        }

        return null;
    }

    /**
     * update tag
     * @param $id
     * @param array $tag
     * @return array
     */
    public function updateTag($id, $tag = array()) {
        $tag = array_unique($tag);
        $this->_data['tags'] = $tag;

        DB::table('tag_project_user')
            ->where('project_user_id', '=', $id)
            ->delete();

        if (!empty($this->_data['tags'])) {
            $this->createTag();
            $this->_project_user_add = $id;
            $this->createTagProjectUser();
        }

        $items = DB::table('tags as T')
                    ->leftJoin('tag_project_user as TU', 'T.id', '=', 'TU.tag_id')
                    ->leftJoin('worktimes as U', 'TU.project_user_id', '=', 'U.id')
                    ->select('T.tag_name')
                    ->where('working_date','=',date("Y-m-d")." 00:00:00")
                    ->get();

        $total = count($items);
        $tags = [];
        foreach ($items as $item) {
            $tags[] = $item->tag_name;
        }
        $color = Helper::renderColors($tags);
        $tagNames = [];
        $tagData = [];
        foreach ($tags as $k => $v){
            if (in_array($v,$tagNames)) {
                $tagData[$v] ++;
            }else{
                $tagNames[] = $v;
                $tagData[$v] = 1;
            }
        }
        $tag_data = [];
        foreach ($tagData as $i => $d) {
            $tag_data[] = [
                'tag_name' => $i,
                'tag_color' => "#".$color[$i],
                'tag_number' => $d
            ];
        }
        return [
            'total' => $total,
            'tag_data' => $tag_data
        ];
    }

    /**
     * update time in project user
     * @param  $id
     * @param  $work_time
     * @return string
     */
    public function updateWorkTime($id, $work_time) {
        if (!$this->checkTime($work_time * 100)) {
            return __('worktime.persional_time.error_entry_time');
        } else {
            $items = DB::table('worktimes')
                    ->where('id', '=', $id)
                    ->where('user_id', '=', Auth::id())
                    ->update(['work_time' => $work_time]);

            return $items;
        }
    }

    /**
     * @author SonNA
     * @param $id
     * @todo delete Work Time
     */
    public function deleteWorkTime($id) {
        $tagProjectUser = TagProjectUser::where('project_user_id', $id);
        $query = '';
        if ($tagProjectUser->count() > 0) {
            $query = $tagProjectUser->delete();

            $projectUser = ProjectUser::find($id);
            if ($projectUser->count() > 0) {
                $query = $projectUser->delete();
            }
        } else {
            $projectUser = ProjectUser::find($id);
            if ($projectUser->count() > 0) {
                $query = $projectUser->delete();
            }
        }

        if ($query) {
            $items = DB::table('tags as T')
                ->leftJoin('tag_project_user as TU', 'T.id', '=', 'TU.tag_id')
                ->leftJoin('worktimes as U', 'TU.project_user_id', '=', 'U.id')
                ->select('T.tag_name')
                ->where('working_date','=',date("Y-m-d")." 00:00:00")
                ->get();

            $total = count($items);
            $tags = [];
            foreach ($items as $item) {
                $tags[] = $item->tag_name;
            }
            $color = Helper::renderColors($tags);
            $tagNames = [];
            $tagData = [];
            foreach ($tags as $k => $v){
                if (in_array($v,$tagNames)) {
                    $tagData[$v] ++;
                }else{
                    $tagNames[] = $v;
                    $tagData[$v] = 1;
                }
            }
            $tag_data = [];
            foreach ($tagData as $i => $d) {
                $tag_data[] = [
                    'tag_name' => $i,
                    'tag_color' => "#".$color[$i],
                    'tag_number' => $d
                ];
            }
            return [
                'code' => $query,
                'result' => [
                    'total' => $total,
                    'tag_data' => $tag_data
                ]
            ];
        }
        return [
            'code' => $query,
        ];
    }

}
