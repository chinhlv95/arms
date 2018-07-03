<?php
namespace App\Repositories\Report;

interface ReportRepositoryInterface{
    
    /**
     * @author huent6810
     * get list project with paginate
     * @param number $perpage
     */
    public function getPaginate($perpage);
    
    /**
     * @author huent6810
     * set paginate
     * @param object $item
     */
    public function setPaginate($items);
    
    /**
     * check project when synch data to insert/update to db
     * @author huent6810
     * @param int $project_id
     */
    public function checkExits($project_id);
    
    /**
     * @author huent6810
     * find project with intergreated_project_id
     * @param int $intergreated_project_id
     */
    public function findWithIntergreatedId($intergreated_project_id);
    
    /**
     * @author huent6810
     * search project with name
     * @param string $key
     * @param int $paginate
     */
    public function search($key, $paginate);
    
    /**
     * @author huent6810
     * only filter list project with dateFrom and dateEnd
     * @param datetime $from dateFrom
     * @param datetime $to dateEnd
     */
    public function getFilter($project = null, $dateFrom, $dateTo);
    
    /**
     * @author huent6810
     * @param datetime $from
     * @param datetime $to
     * @param string $key
     * @param int $perpage
     */
    public function getSearchFilter($from, $to, $key, $perpage);
    
    /**
     * @author huent6810
     * get data with date
     * @param datetime $dateFrom
     * @param datetime $dateTo
     */
    public function getWithDate($project, $dateFrom, $dateTo);
    
    /**
     * @author huent6810
     * get list project with array Id
     * @param array $data
     */
    public function getWithId($data = null);
    
    /**
     * @huent6810
     * get list project with array clientId
     * @param string $client
     */
    public function getWithClient($client = null);
    
    /**
     * @author huent6810
     * get project with tagId
     * @param array $tag
     */
    public function getWorktimeWithTag($tag = null);
    
    /**
     * @author huent6810
     * get project with projectId
     * @param array $project
     */
    public function getWorktimeWithId($project = null);
    
    /**
     * @author huent6810
     * @param string $member
     */
    public function getProjecWithUser($member = null);
    
    /**
     * @author huent6810
     * @param int $resource
     */
    public function getWithIdResource($resource);
}