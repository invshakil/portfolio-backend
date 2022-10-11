<?php


namespace App\Repositories\Page;

use App\Models\Keyword;
use App\Models\News;
use App\Models\Page;
use Illuminate\Http\Request;

class PageRepository
{
    private $model;

    public function __construct(Page $page)
    {
        $this->model = $page;
    }

    /**
     * @param $request
     * @return mixed
     */

    public function saveNews(Request $request)
    {
        $image_url = $this->storeImage($request, $currentFeatured = '');

        return News::create([
            "title" => $request->input('title'),
            "description" => $request->input('description'),
            'published' => filter_var($request->input('published'), FILTER_VALIDATE_BOOLEAN),
            'image' => $image_url,
        ]);
    }

    private function storeImage($request, $currentFeatured): string
    {
        $image = $request->image;
        if ($request->hasFile('image')) {
            $image_ext = $image->getClientOriginalExtension();
            $image_full_name = 'featured_' . $request->input('title') . '.' . $image_ext;
            $upload_path = 'featured/images/';
            $image_url = $upload_path . $image_full_name;
            $image->move($upload_path, $image_full_name);
        } else {
            $image_url = $currentFeatured->image;
        }
        return $image_url;
    }

    /**
     * @param Request $request
     * @param int $id
     * @return mixed
     */
    public function updateNews(Request $request, int $id)
    {
        $news = News::findOrFail($id);

        $data = [
            "title" => $request->input('title'),
            "description" => $request->input('description'),
            'published' => filter_var($request->input('published'), FILTER_VALIDATE_BOOLEAN),
        ];
        return $news->update($data);
    }

    public function all(array $columns = [])
    {
        return count($columns) ? $this->model->select($columns)->orderBy('id')->get() : $this->model->orderBy('id')->get();
    }

    public function allNews(array $columns = [])
    {
        return count($columns) ? News::select($columns)->orderBy('id')->get() : News::orderBy('id')->get();
    }

    public function paginate($perPage = 10)
    {
        return $this->model->latest()
            ->when(request()->has('is_published'), function ($q) {
                $q->where('published', (bool)request('is_published'));
            })
            ->when(\request()->has('search'), function ($q) {
                $q->where('title', 'LIKE', '%' . \request('search') . '%');
            })
            ->paginate($perPage);
    }

    public function paginateNews($perPage = 10)
    {
        return News::latest()
            ->when(request()->has('is_published'), function ($q) {
                $q->where('published', (bool)request('is_published'));
            })
            ->when(\request()->has('search'), function ($q) {
                $q->where('title', 'LIKE', '%' . \request('search') . '%');
            })
            ->paginate($perPage);
    }

}
