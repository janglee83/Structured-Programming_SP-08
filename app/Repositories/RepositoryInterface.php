<?php

namespace App\Repositories;

interface RepositoryInterface
{
    /**
     * Retrieve all data of repository
     */
    public function all();

    /**
     * Find data by id
     * @param $id
     * @param array $columns
     */
    public function find($id, $columns = ['*']);

    public function findOrFail($id);

    public function paginate($limit = null, $columns = ['*']);

    /**
     * Save a new entity in repository
     * @param array $input
     */
    public function create(array $input);

    public function update(array $input, $id);

    public function delete($id);

    public function select($columns = ['*']);
}
