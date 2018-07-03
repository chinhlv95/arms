<?php
namespace App\Repositories\ProjectMember;

use App\Repositories\EloquentRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Message;
use GuzzleHttp\Message\Response;
use DB;
use App\Config;

class ProjectMemberRepository extends EloquentRepository implements ProjectMemberRepositoryInterface{

    /**
     * @author huent6810
     * Get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\ProjectUser::class;
    }
    
    /**
     * (non-PHPdoc)
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::checkExitWithMtoolEntryId()
     */
    public function checkExitWithMtoolEntryId($mtool_entry_id)
    {
        $result = $this->_model->where('mtool_entry_id', $mtool_entry_id)
                            ->where('id_resource', config('contains.RESOURCE_DATA')['mtool'])
                            ->get()->toArray();
        return count($result);
    }
    
    /**
     * (non-PHPdoc)
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::checkExit()
     */
    public function checkExit($project_id, $user_id)
    {
        $result = $this->_model->where('project_id', $project_id)->where('user_id', $user_id)->where('id_resource', config('contains.RESOURCE_DATA')['mtool'])->first();
        if($result)
        {
            return 1;
        }
        return 0;
    }
    
    /**
     * (non-PHPdoc)
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::updateSynch()
     */
    public function updateSynch($mtool_entry_id, array $data)
    {
        $result = $this->_model->where('mtool_entry_id', $mtool_entry_id)
                            ->where('id_resource', config('contains.RESOURCE_DATA')['mtool'])
                            ->update($data);
        if($result) {
            return $result;
        }
        return false;
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::getWithProjectId()
     */
    public function getWithProjectId($project_id, $dateFrom, $dateTo)
    {
        $result = $this->_model->join('users', 'worktimes.user_id', '=', 'users.id')
                        ->whereBetween('working_date', [date('Y-m-d',strtotime($dateFrom)), date('Y-m-d',strtotime($dateTo))])
                        ->select('users.username as username',DB::raw('SUM(worktimes.work_time) as work_time'))
                        ->where('worktimes.project_id', '=', $project_id)
                        ->groupBy('worktimes.user_id','users.username')
                        ->orderBy('work_time', 'desc')
                        ->get();
        if($result){
            return $result;
        }
        else
            return false;
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::getWithProjectIdUserId()
     */
    public function getWithProjectIdUserId($project_id, $user_id, $dateFrom, $dateTo)
    {
        $result = $this->_model->join('users', 'worktimes.user_id', '=', 'users.id')
                        ->whereBetween('working_date', [date('Y-m-d',strtotime($dateFrom)), date('Y-m-d',strtotime($dateTo))])
                        ->select('users.username as username',DB::raw('SUM(worktimes.work_time) as work_time'))
                        ->where('worktimes.project_id', '=', $project_id)
                        ->where('worktimes.user_id', $user_id)
                        ->groupBy('worktimes.user_id','users.username')
                        ->orderBy('work_time', 'desc')
                        ->get();
        if($result){
            return $result;
        }
        else
            return false;
    }
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::deleteWithProjectId()
     */
    public function deleteWithProjectId($project_id)
    {
        $result = $this->_model->where('project_id', $project_id)->get();
        if($result){
            $this->_model->where('project_id', $project_id)->delete();
        }
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\ProjectMember\ProjectMemberRepositoryInterface::findByProjectId()
     */
    public function findAllByProjectId($project_id)
    {
        $result = $this->_model->where('project_id', $project_id)->get();
        if($result){
            return $result;
        }
        else return [];
    }
}