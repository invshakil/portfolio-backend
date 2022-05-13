<?php


namespace App\Repositories\Education;


use App\Models\Education;
use Artisan;

class EducationRepository
{
    private $model;

    public function __construct(Education $education)
    {
        $this->model = $education;
    }

    public function create(array $data)
    {
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
        return $this->model::latest()
            ->when(\request()->has('search'), function ($q) {
                $q->where('institute', 'LIKE', '%' . \request('search') . '%');
            })
            ->paginate($perPage);
    }
}
