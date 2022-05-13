<?php

namespace App\Http\Controllers\Api;

use App\Repositories\Projects\ProjectsRepository;
use App\Repositories\Services\ServicesRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServicesController extends ApiController
{
    public $servicesRepository;

    public function __construct(ServicesRepository $servicesRepository)
    {
        $this->servicesRepository = $servicesRepository;
    }

    /**
     * Returns All categories
     * @param Request $request
     * @return JsonResponse
     */

    public function index(Request $request): JsonResponse
    {
        if ($request->input('page') == '*') {
            return $this->successResponse($this->servicesRepository->all(['id', 'name'], false), true);
        } else {
            return $this->successResponse($this->servicesRepository->paginate($request->input('perPage')), true);
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
            'name' => 'required|unique:services,name'
        ]);

        try {
            $data=$request->all();
            $category = $this->servicesRepository->create($data);

            return $this->successResponse($category);
        } catch (Exception $exception) {
            $this->errorLog($exception, 'api');

            return $this->failResponse($exception->getMessage());
        }

    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->successResponse($this->servicesRepository->getById($id));
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

        $data=$request->all();
        $service = $this->servicesRepository->update($data, $id);

        return $this->successResponse($service);
    }

    /**
     * Deletes Category & Returns boolean
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->servicesRepository->delete($id);

        return $this->successResponse();
    }
}
