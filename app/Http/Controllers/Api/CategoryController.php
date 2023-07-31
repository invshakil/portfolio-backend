<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Category\CategoryRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class CategoryController extends ApiController
{
    public $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Returns All categories
     * @param Request $request
     * @return JsonResponse
     */

    public function index(Request $request): JsonResponse
    {
        if ($request->input('page') == '*') {
            return $this->successResponse($this->categoryRepository->all(['id', 'name'], false), true);
        } else {
            return $this->successResponse($this->categoryRepository->paginate($request->input('perPage')), true);
        }
    }

    /**
     * Creates Category & Returns created category
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|unique:categories,name'
        ]);

        try {
            $data = $request->all();
            $category = $this->categoryRepository->create($data);

            return $this->successResponse($category);
        } catch (Exception $exception) {
            $this->errorLog($exception, 'api');

            return $this->failResponse($exception->getMessage());
        }

    }

    public function show($slug): JsonResponse
    {
        try {
            return $this->successResponse($this->categoryRepository->getById($slug));
        } catch (Exception $exception) {
            $this->errorLog($exception, 'api');

            return $this->failResponse($exception->getMessage());
        }
    }

    public function getWithArticles($slug): JsonResponse
    {
        try {
            return $this->successResponse($this->categoryRepository->getById($slug));
        } catch (Exception $exception) {
            $this->errorLog($exception, 'api');

            return $this->failResponse($exception->getMessage());
        }
    }

    /**
     * Updates Category & Returns updated category
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => 'required'
        ]);

        $category = $request->all();
        $category = $this->categoryRepository->update($category, $id);

        return $this->successResponse($category);
    }

    /**
     * Deletes Category & Returns boolean
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->categoryRepository->delete($id);

        return $this->successResponse();
    }
}
