<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCategoryAPIRequest;
use App\Http\Requests\API\UpdateCategoryAPIRequest;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\CategoryResource;

/**
 * Class CategoryAPIController
 */
class CategoryAPIController extends AppBaseController
{
    /** @var  CategoryRepository */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepo)
    {
        $this->categoryRepository = $categoryRepo;
    }

    /**
     * Display a listing of the Categories.
     * GET|HEAD /categories
     */
    public function index(Request $request): JsonResponse
    {
        $categories = $this->categoryRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(
            CategoryResource::collection($categories),
            __('messages.retrieved', ['model' => __('models/categories.plural')])
        );
    }

    /**
     * Store a newly created Category in storage.
     * POST /categories
     */
    public function store(CreateCategoryAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $category = $this->categoryRepository->create($input);

        return $this->sendResponse(
            new CategoryResource($category),
            __('messages.saved', ['model' => __('models/categories.singular')])
        );
    }

    /**
     * Display the specified Category.
     * GET|HEAD /categories/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Category $category */
        $category = $this->categoryRepository->find($id);

        if (empty($category)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/categories.singular')])
            );
        }

        return $this->sendResponse(
            new CategoryResource($category),
            __('messages.retrieved', ['model' => __('models/categories.singular')])
        );
    }

    /**
     * Update the specified Category in storage.
     * PUT/PATCH /categories/{id}
     */
    public function update($id, UpdateCategoryAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Category $category */
        $category = $this->categoryRepository->find($id);

        if (empty($category)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/categories.singular')])
            );
        }

        $category = $this->categoryRepository->update($input, $id);

        return $this->sendResponse(
            new CategoryResource($category),
            __('messages.updated', ['model' => __('models/categories.singular')])
        );
    }

    /**
     * Remove the specified Category from storage.
     * DELETE /categories/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Category $category */
        $category = $this->categoryRepository->find($id);

        if (empty($category)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/categories.singular')])
            );
        }

        $category->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/categories.singular')])
        );
    }
}
