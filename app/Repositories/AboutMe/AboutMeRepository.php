<?php


namespace App\Repositories\AboutMe;


use App\Models\AboutMe;
use Artisan;

class AboutMeRepository
{
    private $model;

    public function __construct(AboutMe $aboutMe)
    {
        $this->model = $aboutMe;
    }

    public function create($data)
    {
        return $this->model->create($data);
    }


    public function getById(int $id)
    {
        return $this->model->find($id);
    }

    public function update($data, int $id)
    {
        Artisan::call('cache:clear');
        $project=$this->model->where('id', $id)->first();

        return $project->update($data);
    }

    public function delete(int $id)
    {
        Artisan::call('cache:clear');

        return $this->model->where('id', $id)->delete();
    }

    public function all(array $columns = [])
    {
        return $this->model->when(count($columns), function ($q) use ($columns) {
            $q->select($columns);
        })->orderBy('id')->get();
    }

    public function paginate($perPage = 20)
    {
        return $this->model->all();
    }
}
