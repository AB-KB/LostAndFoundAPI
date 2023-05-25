<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends AppBaseController
{



    public function profile()
    {

        $user = Auth::user();

        $pendingItems = Item::where("added_by", $user->id)->where("status", "pending")->count();
        $processedItems = Item::where("added_by", $user->id)->where("status", "processed")->count();

        return $this->sendResponse([
            "name" => $user->name,
            "role" => $user->role,
            "email" => $user->email,
            "phone_number" => $user->phone_number,
            "pendingItems" => $pendingItems,
            "processedItems" => $processedItems,
        ], __("User profile"));
    }


    public function list()
    {

        $pagination = User::with("village.cell.sector.district")
            ->paginate();

        $pagination->through(function (User $user) {

            $village = $user->village;
            $cell = $village->cell;
            $sector = $cell->sector;
            $district = $sector->district;
            $address = $district->name . "/" . $sector->name . "/" . $cell->name . "/" . $village->name;

            return [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "phone_number" => $user->phone_number,
                "address" => $address,
                "role" => $user->role,
            ];
        });


        return $this->sendPaginatedResponse($pagination, __("List of our users"));
    }
}
