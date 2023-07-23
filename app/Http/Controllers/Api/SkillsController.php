<?php

namespace App\Http\Controllers\Api;

use App\Models\Skill;
use App\Repositories\Projects\ProjectsRepository;
use App\Repositories\Services\ServicesRepository;
use App\Repositories\Skill\SkillsRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkillsController extends ApiController
{
    public $skillsRepository;

    public function __construct(SkillsRepository $skillsRepository)
    {
        $this->skillsRepository = $skillsRepository;
    }

    /**
     * Returns All categories
     * @param Request $request
     * @return JsonResponse
     */

    public function index(Request $request): JsonResponse
    {
        if ($request->input('page') == '*') {
            return $this->successResponse($this->skillsRepository->all(['id', 'name'], false), true);
        } else {
            return $this->successResponse($this->skillsRepository->paginate($request->input('perPage')), true);
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
            'title' => 'required|unique:skills,title',
            'accuracy' => 'required',
        ]);

        try {
            $data=$request->all();
            $category = $this->skillsRepository->create($data);

            return $this->successResponse($category);
        } catch (Exception $exception) {
            $this->errorLog($exception, 'api');

            return $this->failResponse($exception->getMessage());
        }

    }

    public function show(int $id): JsonResponse
    {
        try {
            return $this->successResponse($this->skillsRepository->getById($id));
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
        $data=$request->all();
        $service = $this->skillsRepository->update($data, $id);

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
        $this->skillsRepository->delete($id);

        return $this->successResponse();
    }
}
