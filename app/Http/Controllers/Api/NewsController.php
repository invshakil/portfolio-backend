<?php

namespace App\Http\Controllers\Api;

use App\Models\News;
use App\Repositories\Page\PageRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class NewsController extends ApiController
{
    public $pageRepository;

    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    public function index(Request $request): JsonResponse
    {
        return $this->successResponse($this->pageRepository->paginateNews($request->input('perPage')), true);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|unique:news,title'
        ]);

        \DB::beginTransaction();

        try {
            $page = $this->pageRepository->saveNews($request);
            \DB::commit();
            return $this->successResponse($page);
        } catch (Throwable $throwable) {
            \DB::rollBack();
            $this->errorLog($throwable, 'api');

            return $this->failResponse($throwable->getMessage());
        }
    }

    public function edit($slug): JsonResponse
    {
        try {
            $news = News::where('id', $slug)->firstOrFail();

            return $this->successResponse($news);
        } catch (Exception $exception) {
            $this->errorLog($exception, 'api');

            return $this->failResponse($exception->getMessage());
        }
    }

    public function update(Request $request, $id): JsonResponse
    {

        $category = $this->pageRepository->updateNews($request, $id);
        \Artisan::call('cache:clear');

        return $this->successResponse($category);
    }


    public function destroy($id)
    {
        $news = News::findOrFail($id);

        return $news->delete();
    }

    public function get(News $news): JsonResponse
    {
        $news = $this->pageRepository->allNews(['id', 'title']);
        $newsIds = News::where('published',1)->pluck('id');

        return $this->successResponse(['news' => $news, 'newsIds' => $newsIds]);
    }
    public function saveNewsStatus(Request $request): JsonResponse
    {

        foreach ($request->ids as $id) {
            News::where('id', $id)->update(['published' => 1]);
        }

        return $this->successResponse();
    }

    public function deleteNews($id): JsonResponse
    {

        News::where('id', $id)->update(['published' => 0]);

        return $this->successResponse();
    }
}
