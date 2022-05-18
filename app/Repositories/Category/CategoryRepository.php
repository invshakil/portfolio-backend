<?php


namespace App\Repositories\Category;


use App\Models\Category;
use Artisan;

class CategoryRepository implements CategoryInterface
{
    private $model;

    public function __construct(Category $category)
    {
        $this->model = $category;
    }

    public function create(array $data)
    {
        Artisan::call('cache:clear');

        return $this->model->create($data);
    }

    public function getById(int $id)
    {
        return $this->model->find($id);
    }

    public function update(array $data, int $id)
    {
        Artisan::call('cache:clear');
        return $this->model->where('id', $id)->update($data);
    }

    public function delete(int $id)
    {
        Artisan::call('cache:clear');

        return $this->model->where('id', $id)->delete();
    }

    public function all(array $columns = [], bool $fetchPublishedOnly = false)
    {
        return $this->model->when(count($columns), function ($q) use ($columns) {
            $q->select($columns);
        })->when($fetchPublishedOnly, function ($q) {
            $q->where('is_published', true);
        })->orderBy('id')->get();
    }

    public function paginate($perPage = 10)
    {
        return $this->model->orderBy('id', 'asc')->withCount('articles')->paginate($perPage);
    }
}
