<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Education\EducationRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EducationController extends ApiController
{
    public $educationRepository;

    public function __construct( EducationRepository $educationRepository)
    {
        $this->educationRepository = $educationRepository;
    }

    /**
     * Returns All categories
     * @param Request $request
     * @return JsonResponse
     */

    public function index(Request $request): JsonResponse
    {
        if ($request->input('page') == '*') {
            return $this->successResponse($this->educationRepository->all(['id', 'name'], false), true);
        } else {
            return $this->successResponse($this->educationRepository->paginate($request->input('perPage')), true);
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
            'institute' => 'required|unique:educations,institute'
        ]);

        try {
            $data = $request->all();
            $category = $this->educationRepository->create($data);

            return $this->successResponse($category);
        } catch (Exception $exception) {
            $this->errorLog($exception, 'api');

            return $this->failResponse($exception->getMessage());
        }

    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->successResponse($this->educationRepository->getById($id));
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
            'institute' => 'required'
        ]);

        $category = $request->all();
        $category = $this->educationRepository->update($category, $id);

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
        $this->educationRepository->delete($id);

        return $this->successResponse();
    }
}
