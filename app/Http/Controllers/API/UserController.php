<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Models\Item;
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
            "pendingItems" => $pendingItems,
            "processedItems" => $processedItems,
        ], __("User profile"));
    }
}
