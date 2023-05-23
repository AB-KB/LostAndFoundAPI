<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends AppBaseController
{

    public function getComminityDetails()
    {


        $result = DB::select("SELECT COUNT(u.id) as total, p.name FROM provinces p
        INNER JOIN districts d ON p.id = d.province_id
        INNER JOIN sectors s ON d.id = s.district_id
        INNER JOIN cells c ON s.id = c.sector_id
        INNER JOIN villages v ON c.id = v.cell_id
        INNER JOIN users u ON v.id = u.village_id
        GROUP BY p.id");


        return $this->sendResponse($result, __("success"));

    }
}
