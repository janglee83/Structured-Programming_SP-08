<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class BaseRepository implements RepositoryInterface
{
    public $model;

    public function __construct()
    {
        $this->makeModel();
    }

    public function all()
    {
        return $this->model->all();
    }

    public function paginate($limit = null, $columns = ['*'])
    {
//        $limit = is_null($limit) ? config('repository.pagination.limit', 10) : $limit;

        return $this->model->paginate($limit, $columns);
    }

    public function find($id, $columns = ['*'])
    {
        return $this->model->findOrFail($id, $columns);
    }

    public function findWithoutRedirect($id, $columns = ['*'])
    {
        return $this->model->find($id, $columns);
    }

    public function findOrFail($id)
    {
        try {
            $result = $this->model->findOrFail($id);
        } catch (\Exception $e) {
            throw new ModelNotFoundException(__('messages.not_found'), 0);
        }

        return $result;
    }

    public function findOrFailWithTrashed($id, $columns = ['*'])
    {
        return $this->model->withTrashed()->findOrFail($id);
    }

    public function create(array $input)
    {
        return $this->model->create($input);
    }

    public function update(array $input, $id)
    {
        $model = $this->model->findOrFail($id);
        $model->fill($input);
        $model->save();

        return $model;
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function multipleDelete(array $ids)
    {
        return $this->model->destroy(array_values($ids));
    }

    public function latest($id)
    {
        return $this->model->latest('id');
    }

    abstract public function model();

    public function makeModel()
    {
        $this->model = app()->make($this->model());
    }

    public function updateOrCreate(array $arrayFind, $arrayCreate = ['*'])
    {
        return $this->model->updateOrCreate($arrayFind, $arrayCreate);
    }

    public function insertMany($data)
    {
        return count($data) > 0 ? $this->model->insert($data) : null;
    }

    public function select($columns = ['*'])
    {
        return $this->model->select($columns)->get();
    }
}
