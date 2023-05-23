<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\ItemRepository;
use Illuminate\Http\Request;
use Flash;

class ItemController extends AppBaseController
{
    /** @var ItemRepository $itemRepository*/
    private $itemRepository;

    public function __construct(ItemRepository $itemRepo)
    {
        $this->itemRepository = $itemRepo;
    }

    /**
     * Display a listing of the Item.
     */
    public function index(Request $request)
    {
        $items = $this->itemRepository->paginate(10);

        return view('items.index')
            ->with('items', $items);
    }

    /**
     * Show the form for creating a new Item.
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Store a newly created Item in storage.
     */
    public function store(CreateItemRequest $request)
    {
        $input = $request->all();

        $item = $this->itemRepository->create($input);

        Flash::success(__('messages.saved', ['model' => __('models/items.singular')]));

        return redirect(route('items.index'));
    }

    /**
     * Display the specified Item.
     */
    public function show($id)
    {
        $item = $this->itemRepository->find($id);

        if (empty($item)) {
            Flash::error(__('models/items.singular').' '.__('messages.not_found'));

            return redirect(route('items.index'));
        }

        return view('items.show')->with('item', $item);
    }

    /**
     * Show the form for editing the specified Item.
     */
    public function edit($id)
    {
        $item = $this->itemRepository->find($id);

        if (empty($item)) {
            Flash::error(__('models/items.singular').' '.__('messages.not_found'));

            return redirect(route('items.index'));
        }

        return view('items.edit')->with('item', $item);
    }

    /**
     * Update the specified Item in storage.
     */
    public function update($id, UpdateItemRequest $request)
    {
        $item = $this->itemRepository->find($id);

        if (empty($item)) {
            Flash::error(__('models/items.singular').' '.__('messages.not_found'));

            return redirect(route('items.index'));
        }

        $item = $this->itemRepository->update($request->all(), $id);

        Flash::success(__('messages.updated', ['model' => __('models/items.singular')]));

        return redirect(route('items.index'));
    }

    /**
     * Remove the specified Item from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $item = $this->itemRepository->find($id);

        if (empty($item)) {
            Flash::error(__('models/items.singular').' '.__('messages.not_found'));

            return redirect(route('items.index'));
        }

        $this->itemRepository->delete($id);

        Flash::success(__('messages.deleted', ['model' => __('models/items.singular')]));

        return redirect(route('items.index'));
    }
}
