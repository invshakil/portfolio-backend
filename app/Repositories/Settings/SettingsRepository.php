<?php


namespace App\Repositories\Settings;


use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use Artisan;

class SettingsRepository
{
    private $model;

    public function __construct(Setting $setting)
    {
        $this->model = $setting;
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
        return $this->model::when(\request()->has('search'), function ($q) {
                $q->where('company_name', 'LIKE', '%' . \request('search') . '%');
            })
            ->when(\request()->has('current'), function ($q) {
                $q->where('current', \request('current'));
            })
            ->orderBy('id','desc')
            ->paginate($perPage);
    }
}
