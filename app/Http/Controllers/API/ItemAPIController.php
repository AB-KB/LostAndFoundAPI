<?php

namespace App\Http\Controllers\API;

use App\Events\NewItemEvent;
use App\Exceptions\InvalidDataGivenException;
use App\Exceptions\ItemNotFoundException;
use App\Http\Requests\API\CreateItemAPIRequest;
use App\Http\Requests\API\UpdateItemAPIRequest;
use App\Models\Item;
use App\Repositories\ItemRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\OpenClaimRequest;
use App\Http\Resources\ItemResource;
use App\Models\Category;
use App\Models\ItemMessageThread;
use App\Models\Matches;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

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

        $pagination = $this->itemRepository->allQuery()
            ->when(isset($request->status), fn ($qb) => $qb->whereStatus($request->status))
            ->when(isset($request->type), fn ($qb) => $qb->whereType($request->type))
            ->when(isset($request->category_id), fn ($qb) => $qb->where("category_id", $request->category_id))
            ->latest()
            ->paginate(
                perPage: 10,
                columns: ["*"]
            );

        $pagination->through(function (Item $item) {
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
        $input = $request->only((new Item())->getFillable());
        $input["added_by"] = Auth::id();
        if (empty($input["image"]) || trim($input["image"]) == "") {
            return response()->json(["message" => __("Image is invalid")], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        /** @var Item */
        $item = $this->itemRepository->create($input);

        NewItemEvent::dispatch($item);

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
        $image = $request->file("image");
        if ($image) {

            $item->image = $image;
            $item->save();
        }

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


    public function openClaim(OpenClaimRequest $request, int $id)
    {

        try {
            $text = $request->claim;
            $userId = Auth::id();

            $item = Item::find($id);
            if (is_null($item)) {

                throw new ItemNotFoundException(__("Item not found"));
            }

            $threadExists =  ItemMessageThread::where([
                ["item_id", $id],
                ["user_id", $userId],
            ])->exists();
            if ($threadExists) {

                throw new InvalidDataGivenException(__("Thread already exists"));
            }


            DB::transaction(function () use ($userId, $text, $id) {

                $thread = ItemMessageThread::create([
                    "item_id" => $id,
                    "user_id" => $userId,
                ]);

                $thread->messages()->create([
                    "text" => $text,
                    "is_from_admin" => false,
                    "admin_read" => false,
                    "normal_user_read" => true,
                ]);
            });

            return $this->sendSuccess(__("Claim was opened"));
        } catch (Exception $th) {

            return $this->sendExceptionError($th);
        }
    }

    public function getClaim(int $id)
    {

        try {
            $userId = Auth::id();
            $item = Item::find($id);
            if (is_null($item)) {

                throw new ItemNotFoundException(__("Item not found"));
            }

            $thread =  ItemMessageThread::where([
                ["item_id", $id],
                ["user_id", $userId],
            ])->first();
            if (is_null($thread)) {

                throw new ItemNotFoundException(__("Thread not found"));
            }

            $result = [
                "id" => $thread->id,
                "item" => $thread->item->only(["id", "name", "type"]),
                "user" => $thread->user->only(["id", "name"]),
                "lastMessage" => $thread->lastMessage->only(["id", "text"]),
            ];

            return $this->sendResponse($result, __("Claim"));
        } catch (Exception $th) {

            return $this->sendExceptionError($th);
        }
    }


    public function getMatches(int $id)
    {

        $userId = Auth::id();
        $item = Item::find($id);
        if (is_null($item)) {

            throw new ItemNotFoundException(__("Item not found"));
        }


        $matches = Matches::where("item_id", $item->id)->with("withItem")->get();

        return $matches->map(function (Matches $match) {

            return [
                "item_details" => new ItemResource($match->withItem),
                "percentage" => $match->percentage
            ];
        });
    }
}
