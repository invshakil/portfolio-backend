<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Workplace\WorkplaceRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkplaceController extends ApiController
{
    public $workplaceRepository;

    public function __construct( WorkplaceRepository $workplaceRepository)
    {
        $this->workplaceRepository = $workplaceRepository;
    }

    /**
     * Returns All categories
     * @param Request $request
     * @return JsonResponse
     */

    public function index(Request $request): JsonResponse
    {
        if ($request->input('page') == '*') {
            return $this->successResponse($this->workplaceRepository->all(['id', 'name'], false), true);
        } else {
            return $this->successResponse($this->workplaceRepository->paginate($request->input('perPage')), true);
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
            'company_name' => 'required|unique:workplaces,company_name'
        ]);

        try {
            $data = $request->all();
            $workplace = $this->workplaceRepository->create($data);

            return $this->successResponse($workplace);
        } catch (Exception $exception) {
            $this->errorLog($exception, 'api');

            return $this->failResponse($exception->getMessage());
        }

    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->successResponse($this->workplaceRepository->getById($id));
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
            'company_name' => 'required'
        ]);

        $category = $request->all();
        $category = $this->workplaceRepository->update($category, $id);

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
        $this->workplaceRepository->delete($id);

        return $this->successResponse();
    }
}
