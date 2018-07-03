<?php
namespace App\Repositories\Division;

interface DivisionRepositoryInterface{
    
    /**
     * @author huent6810
     * @param unknown $id
     */
    public function findByPortalId($id);

    /**
     * @author tienhv6815
     * @return mixed
     */
    public function getTreeViewDivision ();
}