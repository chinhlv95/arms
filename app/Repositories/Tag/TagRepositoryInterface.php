<?php
namespace App\Repositories\Tag;

interface TagRepositoryInterface{
    /**
     * @author huent6810
     * get list of tag wit project_id
     * @param array $project
     */
    public function getWithProject($project = null, $dateFrom, $dateTo);
    
    /**
     * @author huent6810
     * get list tag with array id
     * @param string $tag
     */
    public function getWithId($tag = null);

    /**
     * @author theunt check unique tag
     * @param $name
     * @param $id
     * @return bool
     */
    public function  checkIsExistedTag($name, $id);
}