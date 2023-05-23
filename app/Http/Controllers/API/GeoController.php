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

        return $this->sendResponse([
            "provinces" => $provinces->toArray(),
        ], __("Provinces"));
    }

    public function districts(int $province_id)
    {

        $districts = District::where("province_id", $province_id)
            ->all();

        return $this->sendResponse([
            "districts" => $districts->toArray(),
        ], __("Districts"));
    }

    public function sectors(int $district_id)
    {

        $sectors = Sector::where("district_id", $district_id)
            ->all();

        return $this->sendResponse([
            "sectors" => $sectors->toArray(),
        ], __("Sectors"));
    }

    public function cells(int $sector_id)
    {

        $cells = Cell::where("sector_id", $sector_id)
            ->all();

        return $this->sendResponse([
            "cells" => $cells->toArray(),
        ], __("cells"));
    }

    public function villages(int $cell_id)
    {

        $villages = Village::where("cell_id", $cell_id)
            ->all();

        return $this->sendResponse([
            "villages" => $villages->toArray(),
        ], __("villages"));
    }
}
