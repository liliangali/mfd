<?php

namespace  App\Api\V1\Controllers\Admin;

use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;
date_default_timezone_set('Asia/Shanghai');
class BaseController extends Controller {
    use Helpers;

    /*
     * error resposne
     * @param message
     * @param status
     */

    protected function errorResponse($message = "", $status = 1) {
        
        $response = array(
            'msg' => $message,
            'status' => $status,
        );
        return response()->json($response);
    }

    /*
     * success response
     * @param message
     * @param status
     */

    protected function successResponse($data = [],$message = "请求成功", $status = 200) {
        $response = array(
            'data' => $data,
            'msg' => $message,
            'status' => $status,
        );
        return response()->json($response);
    }


}
