<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\AbstractPaginator;
use InfyOm\Generator\Utils\ResponseUtil;

/**
 * @OA\Server(url="/api")
 * @OA\Info(
 *   title="InfyOm Laravel Generator APIs",
 *   version="1.0.0"
 * )
 * This class should be parent class for other API controllers
 * Class AppBaseController
 */
class AppBaseController extends Controller
{
    public function sendPaginatedResponse(LengthAwarePaginator $result, $message)
    {


        $meta = collect($result)->except(["data"]);
        $data = collect($result)->only(["data"]);

        return response()
            ->json([
                'success' => true,
                'data'    => $data["data"],
                'meta'    => $meta,
                'message' => $message,
            ]);
    }

    public function sendResponse($result, $message)
    {
        return response()->json(ResponseUtil::makeResponse($message, $result));
    }

    public function sendError($error, $code = 404)
    {
        return response()->json(ResponseUtil::makeError($error), $code);
    }

    public function sendSuccess($message)
    {
        return response()->json([
            'success' => true,
            'message' => $message
        ], 200);
    }


    public function sendExceptionError(Exception $e)
    {

        [$message, $status] = getHttpMessageAndStatusCodeFromException($e);

        return $this->sendError($message, $status);
    }
}
