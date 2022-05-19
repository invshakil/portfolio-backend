<?php


namespace App\Repositories\Projects;


use App\Models\Project;
use Artisan;

class ProjectsRepository
{
    private $model;

    public function __construct(Project $project)
    {
        $this->model = $project;
    }

    public function create($request)
    {
        $image_url = $this->storeImage($request, $currentProject = '');
        $data = $this->storeData($request, $image_url);
        return $this->model->create($data);
    }

    private function storeImage($request, $currentProject): string
    {
        $image = $request->image;
        if ($request->hasFile('image')) {
            $image_ext = $image->getClientOriginalExtension();
            $image_full_name = 'cover_' . $request->input('name') . '.' . $image_ext;
            $upload_path = 'projects/images/';
            $image_url = $upload_path . $image_full_name;
            $image->move($upload_path, $image_full_name);
        } else {
            $image_url = $currentProject->image;
        }
        return $image_url;
    }

    private function storeData($request, $image_url): array
    {
        return [
            'service_id' => $request->input('service_id'),
            'name' => $request->input('name'),
            'demo_link' => $request->input('demo_link'),
            'tags' => $request->input('tags'),
            'description' => $request->input('description'),
            'image' => $image_url,
        ];
    }


    public function getById(int $id)
    {
        return $this->model->find($id);
    }

    public function update($request, int $id)
    {
        Artisan::call('cache:clear');
        $project=$this->model->where('id', $id)->first();
        $image_url = $this->storeImage($request, $currentProject = $project);
        $data = $this->storeData($request, $image_url);

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
        return $this->model::with('service')
            ->when(\request()->has('search'), function ($q) {
                $q->where('name', 'LIKE', '%' . \request('search') . '%');
            })
            ->when(request()->has('service'), function ($q) {
                $q->whereHas('service', function ($sq) {
                    $sq->where('services.name', \request('service'));
                });
            })
            ->orderBy('id','desc')
            ->paginate($perPage);
    }
}
