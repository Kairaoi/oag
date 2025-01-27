<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class CustomBaseRepository implements RepositoryContract
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * Set the model class name.
     *
     * @return string
     */
    abstract public function model();

    public function __construct()
    {
        $this->model = app($this->model());
    }

    /**
     * Get the model instance.
     *
     * @return Model
     */
    protected function getModelInstance(): Model
    {
        if (!$this->model) {
            $this->model = app($this->model());
        }
        return $this->model;
    }

    /**
     * {@inheritDoc}
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->getModelInstance()->all($columns);
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return $this->getModelInstance()->count();
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): Model
    {
        return $this->getModelInstance()->create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function update(int $id, array $data): Model
    {
        // Retrieve the model instance by ID
        $model = $this->getModelInstance()->find($id);

        // Check if the model exists
        if (!$model) {
            throw new \Exception("Model not found.");
        }

        // Update the model with the provided data
        $model->update($data);

        // Return the updated model instance
        return $model;
    }

    /**
     * {@inheritDoc}
     */
    public function createMultiple(array $data): bool
    {
        return $this->getModelInstance()->insert($data);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(): bool
    {
        return $this->getModelInstance()->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function deleteById($id): bool
    {
        return $this->getModelInstance()->destroy($id) > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteMultipleById(array $ids): int
    {
        return $this->getModelInstance()->destroy($ids);
    }

    /**
     * {@inheritDoc}
     */
    public function first(array $columns = ['*']): ?Model
    {
        return $this->getModelInstance()->first($columns);
    }

    /**
     * {@inheritDoc}
     */
    public function get(array $columns = ['*']): Collection
    {
        return $this->getModelInstance()->get($columns);
    }

    /**
     * {@inheritDoc}
     */
    public function getById($id, array $columns = ['*']): ?Model
    {
        return $this->getModelInstance()->find($id, $columns);
    }

    /**
     * {@inheritDoc}
     */
    public function getByColumn($item, $column, array $columns = ['*']): ?Model
    {
        return $this->getModelInstance()->where($column, $item)->first($columns);
    }

    /**
     * {@inheritDoc}
     */
    public function paginate($limit = 25, array $columns = ['*'], $pageName = 'page', $page = null): LengthAwarePaginator
    {
        return $this->getModelInstance()->paginate($limit, $columns, $pageName, $page);
    }

    /**
     * {@inheritDoc}
     */
    public function updateById($id, array $data, array $options = []): bool
    {
        return $this->getModelInstance()->where('id', $id)->update($data, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function limit($limit)
    {
        return $this->getModelInstance()->limit($limit);
    }

    /**
     * {@inheritDoc}
     */
    public function orderBy($column, $direction = 'asc')
    {
        return $this->getModelInstance()->orderBy($column, $direction);
    }

    /**
     * {@inheritDoc}
     */
    public function where($column, $value, $operator = '=')
    {
        return $this->getModelInstance()->where($column, $operator, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function whereIn($column, $values)
    {
        return $this->getModelInstance()->whereIn($column, $values);
    }

    /**
     * {@inheritDoc}
     */
    public function with($relations)
    {
        return $this->getModelInstance()->with($relations);
    }
}
