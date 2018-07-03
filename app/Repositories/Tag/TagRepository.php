<?php
namespace App\Repositories\Tag;

use App\Repositories\EloquentRepository;
use DB;

class TagRepository extends EloquentRepository implements TagRepositoryInterface{
    
    /**
     * @author huent6810
     * Get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Tag::class;
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\Tag\TagRepositoryInterface::getTagSelected()
     */
    public function getWithProject($project = null, $dateFrom, $dateTo)
    {
        if($project == null){
            $items = $this->_model->leftJoin('tag_project_user', 'tags.id', '=', 'tag_project_user.tag_id')
                    ->leftJoin('worktimes', 'worktimes.id', '=', 'tag_project_user.project_user_id')
                    ->leftJoin('projects', 'projects.id', '=', 'worktimes.project_id')
                    ->whereNull('worktimes.project_id')
                    ->whereBetween('working_date', [date('Y-m-d',strtotime($dateFrom)), date('Y-m-d',strtotime($dateTo))])
                    ->distinct()
                    ->select('tags.id','tags.tag_name', 'projects.id AS project_id')
                    ->get();
        }
        else  
            $items = $this->_model->leftJoin('tag_project_user', 'tags.id', '=', 'tag_project_user.tag_id')
                    ->leftJoin('worktimes', 'worktimes.id', '=', 'tag_project_user.project_user_id')
                    ->leftJoin('projects', 'projects.id', '=', 'worktimes.project_id')
                    ->whereIn('worktimes.project_id', explode(",",$project))
                    ->whereBetween('working_date', [date('Y-m-d',strtotime($dateFrom)), date('Y-m-d',strtotime($dateTo))])
                    ->distinct()
                    ->select('tags.id','tags.tag_name', 'projects.id AS project_id')
                    ->get();
        return $items;
    }
    
    /**
     * (non-PHPdoc)
     * @see \App\Repositories\Tag\TagRepositoryInterface::getWithId()
     */
    public function getWithId($tag = null)
    {
        if($tag == null){
            $items = $this->_model->leftJoin('tag_project_user', 'tags.id', '=', 'tag_project_user.tag_id')
                ->leftJoin('worktimes', 'worktimes.id', '=', 'tag_project_user.project_user_id')
                ->leftJoin('projects', 'projects.id', '=', 'worktimes.project_id')
                ->whereNull('tags.id')
                ->distinct()
                ->select('tags.id','tags.tag_name', 'projects.id AS project_id')
                ->get();
        }
        else
            $items = $this->_model->leftJoin('tag_project_user', 'tags.id', '=', 'tag_project_user.tag_id')
                ->leftJoin('worktimes', 'worktimes.id', '=', 'tag_project_user.project_user_id')
                ->leftJoin('projects', 'projects.id', '=', 'worktimes.project_id')
                ->whereIn('tags.id', explode(",", $tag))
                ->distinct()
                ->select('tags.id','tags.tag_name', 'projects.id AS project_id')
                ->get();
        return $items;
    }

    /**
     * @author tienhv search by tag name
     * @param $key
     * @return \Illuminate\Support\Collection
     */
    public function search ($key) {
        return $this->_model->where('tag_name','like', "%".$key."%")->get();
    }

    /**
     * @author tienhv6815
     * @param $id
     * @return bool
     */
    public function removeTag ($id)
    {
        DB::beginTransaction();
        try {
            DB::table('tag_project_user')->where('tag_id', '=', $id)->delete();
            DB::table('tags')->where('id', '=', $id)->delete();
            DB::commit();
            return true;
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
    /**
     * @author theunt check unique tag
     * @param $name
     * @param $id
     * @return bool
     */
    public function  checkIsExistedTag($name, $id){
       $query = $this->_model->where('tag_name', '=',$name);
       if ($id) {
           $query = $query->where('id', '!=', $id);
       }
       if ($query->first()) {
            return false;
       }
       return true;
    }

    /**
     * @param $tagId
     * @return bool
     */
    public function checkTagUsing($tagId) {
        if(DB::table('tag_project_user')->where('tag_id', '=', $tagId)->first()) return true;
        return false;
    }
}