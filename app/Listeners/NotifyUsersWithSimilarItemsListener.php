<?php

namespace App\Listeners;

use App\Events\NewItemEvent;
use App\Models\Matches;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class NotifyUsersWithSimilarItemsListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewItemEvent  $event
     * @return void
     */
    public function handle(NewItemEvent $event)
    {


        $item  = $event->item;
        $name = $item->name;
        $additional_info = $item->additional_info;
        $category = $item->category;
        $adds = [];
        foreach ($additional_info as $key => $value) {
            $adds[] = "jaro_winkler(JSON_EXTRACT(additional_info, '$.$key'), '$value')";
        }

        if ($item->isFoundType()) {

            $matches = collect(DB::select(
                "SELECT
                            id, name,added_by,(
                                (jaro_winkler_similarity(name, ?) +
                                ?
                            ) / (1 + ?)) * 100 AS match_percentage
                        FROM items

                        WHERE
                            `type` = 'lost' AND category_id = ?
                        ORDER BY match_percentage DESC
                        LIMIT 10",
                [
                    $name,
                    implode("+", $adds),
                    count($adds),
                    $category->id
                ]
            ))->map(function ($i) use ($item) {

                return [
                    "item_id" => $item->id,
                    "with_item_id" => $i->id,
                    "percentage"=> $i->match_percentage,
                    "created_at" => now(),
                    "updated_at" => now(),
                ];
            });


            Matches::insert($matches->toArray());
        } else {

            $matches = collect(DB::select(
                "SELECT
                            id, name,added_by,(
                                (jaro_winkler_similarity(name, ?) +
                                ?
                            ) / (1 + ?)) * 100 AS match_percentage
                        FROM items

                        WHERE
                            `type` = 'found' AND category_id = ?
                        ORDER BY match_percentage DESC
                        LIMIT 10",
                [
                    $name,
                    implode("+", $adds),
                    count($adds),
                    $category->id
                ]
            ))->map(function ($i) use ($item) {

                return [
                    "item_id" => $item->id,
                    "with_item_id" => $i->id,
                    "percentage"=> $i->match_percentage,
                    "created_at" => now(),
                    "updated_at" => now(),
                ];
            });


            Matches::insert($matches->toArray());
        }
    }
}
