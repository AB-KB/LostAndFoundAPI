<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\Cell;
use App\Models\District;
use App\Models\Province;
use App\Models\Sector;
use App\Models\Village;

class GeoController extends AppBaseController
{

    public function provinces()
    {

        $provinces = Province::all();

        return $this->sendResponse($provinces->toArray(), __("Provinces"));
    }

    public function districts(int $province_id)
    {

        $districts = District::where("province_id", $province_id)
            ->get();

        return $this->sendResponse($districts->toArray(), __("Districts"));
    }

    public function sectors(int $district_id)
    {

        $sectors = Sector::where("district_id", $district_id)
            ->get();

        return $this->sendResponse($sectors->toArray(), __("Sectors"));
    }

    public function cells(int $sector_id)
    {

        $cells = Cell::where("sector_id", $sector_id)
            ->get();

        return $this->sendResponse($cells->toArray(), __("cells"));
    }

    public function villages(int $cell_id)
    {

        $villages = Village::where("cell_id", $cell_id)
            ->get();

        return $this->sendResponse($villages->toArray(), __("villages"));
    }
}
