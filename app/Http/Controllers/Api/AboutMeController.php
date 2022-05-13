<?php

namespace App\Http\Controllers\Api;

use App\Repositories\AboutMe\AboutMeRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AboutMeController extends ApiController
{
    public $aboutMeRepository;

    public function __construct(AboutMeRepository $aboutMeRepository)
    {
        $this->aboutMeRepository = $aboutMeRepository;
    }

    /**
     * Returns All categories
     * @param Request $request
     * @return JsonResponse
     */

    public function index(Request $request): JsonResponse
    {
        if ($request->input('page') == '*') {
            return $this->successResponse($this->aboutMeRepository->all(['id', 'name'], false), true);
        } else {
            return $this->successResponse($this->aboutMeRepository->paginate($request->input('perPage')), true);
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
            'key' => 'required|unique:about_mes,key',
            'value' => 'required'
        ]);

        try {
            $data=$request->all();
            $category = $this->aboutMeRepository->create($data);

            return $this->successResponse($category);
        } catch (Exception $exception) {
            $this->errorLog($exception, 'api');

            return $this->failResponse($exception->getMessage());
        }

    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->successResponse($this->aboutMeRepository->getById($id));
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
            'key' => 'required'
        ]);

        $data=$request->all();
        $service = $this->aboutMeRepository->update($data, $id);

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
        $this->aboutMeRepository->delete($id);

        return $this->successResponse();
    }
}
