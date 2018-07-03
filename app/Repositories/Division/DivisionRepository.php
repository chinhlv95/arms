<?php
namespace App\Repositories\Division;

use App\Repositories\EloquentRepository;
use App\Config;
use Illuminate\Support\Facades\DB;

class DivisionRepository extends EloquentRepository implements DivisionRepositoryInterface{
    /**
     * Get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\Division::class;
    }
    
    /**
     * @author huent6810
     * (non-PHPdoc)
     * @see \App\Repositories\Division\DivisionRepositoryInterface::findByPortalId()
     */
    public function findByPortalId($id)
    {
        $this->resource = config('contains.RESOURCE_DATA');
        $result = $this->_model->where('portal_id', $id)->where('id_resource', $this->resource['portal'])->first();
        if($result)
        {
            return $result->id;
        }
        return 0;
    }

    /**
     * get tree view all division
     * @author tienhv6815
     * @return array
     */
    public function getTreeViewDivision () {
        $results = [];
        $rootDivisions = $this->getListRootDivision();
        foreach ($rootDivisions as $rootDivision) {
            $rootDivision['children'] = $this->getAllDivisionByParentId($rootDivision['id']);
            $results[] = $rootDivision;
        }
        return $results;
    }

    /**
     * get list root division
     * @author tienhv6815
     * @return array
     */
    public function getListRootDivision () {
        $items = DB::table('divisions')->whereNull('parent_id')->get();
        $results = [];
        foreach  ($items as $item) {
            $results[] = [
                'id' => $item->id,
                'name' => $item->name,
            ];
        }
        return $results;
    }

    /**
     * get all division by parent id
     * @author tienhv6815s
     * @param $parentId
     * @return array
     */
    public function getAllDivisionByParentId ($parentId) {
        $items = DB::table('divisions')->where('parent_id','=', $parentId)->get();
        $results = [];
        foreach ($items as $item) {
            $tmp = ['id' => $item->id, 'name' => $item->name];
            $tmp['children'] = $this->getAllDivisionByParentId($item->id);
            $results[] = $tmp;
        }
        return $results;
    }

    /**
     * @author tienhv6815
     * @param $division
     * @return boolean
     */
    public function createNewDivision ($division) {
        $idResources = config('contains.RESOURCE_DATA');
        $division['id_resource'] = $idResources['arms_system'];
        return DB::table('divisions')->insert($division);
    }

    /**
     * @author tienhv6815
     * @param $id
     * @param array $division
     * @return bool|mixed
     */
    public function updateDivision($id, array $division)
    {
        try{
            $idResources = config('contains.RESOURCE_DATA');
            $division['id_resource'] = $idResources['arms_system'];
            DB::table('divisions')
                ->where('id', $id)
                ->update($division);
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    /**
     * @author tienhv6815
     * @param $name
     * @param $parentId
     * @param @id
     * @return mixed
     */
    public function checkIsExists ($name, $parentId, $id=null) {
        if($id) {
            return $items = DB::table('divisions')
                ->where('id','!=', $id)
                ->where('parent_id','=', $parentId)
                ->where('name', '=', $name)
                ->first();
        }
        return $items = DB::table('divisions')->where('parent_id','=', $parentId)->where('name', '=', $name)->first();
    }

    /**
     * @author tienhv6815
     * @param $parentId
     * @param array $result
     * @return array
     */
    public function getListDivisionIdByParentId($parentId,$result=[]) {
        $items = DB::table('divisions')->where('parent_id','=', $parentId)->get();
        foreach ($items as  $item) {
            if (!in_array($item->id,$result)) {
                $result[] = $item->id;
                $result = array_unique(array_merge($result,$this->getListDivisionIdByParentId($item->id,$result)));
            }
        }
        return $result;
    }

    /**
     * @author tienhv6815
     * @param $parentId
     * @return bool
     */
    public function deleteAllDivisionByParentId($parentId) {
        if ($parentId) {
            $ids = $this->getListDivisionIdByParentId($parentId,[]);
            $ids[] = (int)$parentId;
            DB::table('users')->whereIn('division_id',$ids)->update(['division_id'=>null]);
            if (DB::table('divisions')->whereIn('id',$ids)->delete()) {
                return true;
            }
                    }
        return false;
    }

    /**
     * @param $name
     * @param $id
     * @return bool
     */
    public function checkIsExisted($name, $id){
        $query = $this->_model->where('name', '=',$name);
        if ($id) {
            $query = $query->where('id', '!=', $id);
        }
        if ($query->first()) {
            return false;
        }
        return true;
    }
}