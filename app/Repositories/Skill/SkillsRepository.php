<?php


namespace App\Repositories\Skill;


use App\Models\Skill;
use Artisan;

class SkillsRepository
{
    private $model;

    public function __construct(Skill $skill)
    {
        $this->model = $skill;
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

    public function paginate($perPage = 10)
    {
        return $this->model->paginate($perPage);
    }
}
