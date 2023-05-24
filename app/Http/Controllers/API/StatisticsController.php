<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class StatisticsController extends AppBaseController
{



    public function activeMembers()
    {

        /** @var Collection */
        $users = User::withCount("ads")->orderBy("ads_count", "desc")->limit(20)->get();

        $others = $users->count() - 3;

        return $this->sendResponse([
            "images" => $users->take(3)->map(fn (User $user) => $user->getPublicLink("image")),
            "others" => $others < 0 ? 0 : $others,
        ], __("Active members"));
    }


    public function lostItems(){

        $totalLostItems = Item::where("type", "lost")->count();
        $totalPendingLostItems = Item::where("type", "lost")->where("status","pending")->count();
        $totalProcessedLostItems = Item::where("type", "lost")->where("status","processed")->count();


        return $this->sendResponse([
            "totalLostItems"=> $totalLostItems,
            "totalPendingLostItems"=> $totalPendingLostItems,
            "totalProcessedLostItems"=> $totalProcessedLostItems,
        ], __("Lost items stats"));
    }

    public function foundItems(){

        $totalFoundItems = Item::where("type", "found")->count();
        $totalPendingFoundItems = Item::where("type", "found")->where("status","pending")->count();
        $totalProcessedFoundItems = Item::where("type", "found")->where("status","processed")->count();


        return $this->sendResponse([
            "totalFoundItems"=> $totalFoundItems,
            "totalPendingFoundItems"=> $totalPendingFoundItems,
            "totalProcessedFoundItems"=> $totalProcessedFoundItems,
        ], __("Found items stats"));
    }


    public function overall(){

        $total = Item::count();
        $totalPending = Item::where("status","pending")->count();
        $totalProcessed = Item::where("status","processed")->count();

        return $this->sendResponse([
            "total"=> $total,
            "totalPending"=> $totalPending,
            "totalProcessed"=> $totalProcessed
        ], __("Overall statss"));
    }
}
