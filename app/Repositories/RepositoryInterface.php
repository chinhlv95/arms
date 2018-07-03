<?php
namespace App\repositories;

interface RepositoryInterface{
    /**
     * @author: SonNA6229
     * Get all
     * @return mixed
     */
    public function getAll();

    /**
     * @author: SonNA6229
     * Get one
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * @author: SonNA6229
     * Create
     * @param array $attributes
     * @return mixed
     */
    public function create(array $attributes);

    /**
     * @author: SonNA6229
     * Update
     * @param $id
     * @param array $attributes
     * @return mixed
     */
    public function update($id, array $attributes);

    /**
     * @author: SonNA6229
     * Delete
     * @param $id
     * @return mixed
     */
    public function delete($id);
}