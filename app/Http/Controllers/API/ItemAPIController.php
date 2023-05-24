<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemAPIRequest;
use App\Http\Requests\API\UpdateItemAPIRequest;
use App\Models\Item;
use App\Repositories\ItemRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\ItemResource;
use Illuminate\Support\Facades\Auth;

/**
 * Class ItemAPIController
 */
class ItemAPIController extends AppBaseController
{
    /** @var  ItemRepository */
    private $itemRepository;

    public function __construct(ItemRepository $itemRepo)
    {
        $this->itemRepository = $itemRepo;
    }

    /**
     * Display a listing of the Items.
     * GET|HEAD /items
     */
    public function index(Request $request): JsonResponse
    {
        $pagination = $this->itemRepository->paginate(
            perPage: 10,
            columns: ["*"]
        );

        $pagination->through(function(Item $item){
            return new ItemResource($item);
        });

        return $this->sendPaginatedResponse(
            $pagination,
            __('messages.retrieved', ['model' => __('models/items.plural')])
        );
    }

    /**
     * Store a newly created Item in storage.
     * POST /items
     */
    public function store(CreateItemAPIRequest $request): JsonResponse
    {
        $input = $request->all();
        $input["added_by"] = Auth::id();

        $item = $this->itemRepository->create($input);

        return $this->sendResponse(
            new ItemResource($item),
            __('messages.saved', ['model' => __('models/items.singular')])
        );
    }

    /**
     * Display the specified Item.
     * GET|HEAD /items/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Item $item */
        $item = $this->itemRepository->find($id);

        if (empty($item)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/items.singular')])
            );
        }

        return $this->sendResponse(
            new ItemResource($item),
            __('messages.retrieved', ['model' => __('models/items.singular')])
        );
    }

    /**
     * Update the specified Item in storage.
     * PUT/PATCH /items/{id}
     */
    public function update($id, UpdateItemAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Item $item */
        $item = $this->itemRepository->find($id);

        if (empty($item)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/items.singular')])
            );
        }

        $item = $this->itemRepository->update($input, $id);

        return $this->sendResponse(
            new ItemResource($item),
            __('messages.updated', ['model' => __('models/items.singular')])
        );
    }

    /**
     * Remove the specified Item from storage.
     * DELETE /items/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Item $item */
        $item = $this->itemRepository->find($id);

        if (empty($item)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/items.singular')])
            );
        }

        $item->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/items.singular')])
        );
    }
}
