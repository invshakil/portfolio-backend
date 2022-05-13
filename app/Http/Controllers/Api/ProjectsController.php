<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Projects\ProjectsRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectsController extends ApiController
{
    public $projectsRepository;

    public function __construct(ProjectsRepository $projectsRepository)
    {
        $this->projectsRepository = $projectsRepository;
    }

    /**
     * Returns All categories
     * @param Request $request
     * @return JsonResponse
     */

    public function index(Request $request): JsonResponse
    {
        if ($request->input('page') == '*') {
            return $this->successResponse($this->projectsRepository->all(['id', 'name'], false), true);
        } else {
            return $this->successResponse($this->projectsRepository->paginate($request->input('perPage')), true);
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
            'name' => 'required|unique:projects,name'
        ]);

        try {
            $category = $this->projectsRepository->create($request);

            return $this->successResponse($category);
        } catch (Exception $exception) {
            $this->errorLog($exception, 'api');

            return $this->failResponse($exception->getMessage());
        }

    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->successResponse($this->projectsRepository->getById($id));
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

        $category = $this->projectsRepository->update($request, $id);

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
        $this->projectsRepository->delete($id);

        return $this->successResponse();
    }
}
