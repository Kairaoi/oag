<?php

namespace App\Repositories;

interface RepositoryContract
{
    public function model();
    public function all(array $columns = ['*']);
    public function count(): int;
    public function create(array $data);
    public function update(int $id, array $data); 
    public function paginate($limit = 25, array $columns = ['*'], $pageName = 'page', $page = null);
    public function updateById($id, array $data, array $options = []);
    public function limit($limit);
    public function orderBy($column, $direction = 'asc');
    public function where($column, $value, $operator = '=');
    public function whereIn($column, $values);
    public function with($relations);
}
