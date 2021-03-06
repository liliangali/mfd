<?php

namespace App\Api\V1\Controllers\Order;
use App\Api\V1\Controllers\BaseController;
use App\Api\V1\Transformers\OrderTransformer;

use App\Serializer\CustomSerializer;
use Illuminate\Http\Request;
use App\Models\Order;
use DB;
use Validator;
use JWTAuth;
use Swagger\Annotations as SWG;


class OrderController extends BaseController {

    /**
     * @SWG\Get(
     * path="/ordr/all",
     * summary="获取公司下所有审核通过并分配了的项目",
     * tags={"Projects"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Response(
     *   response=401,
     *   description="token过期"
     * ),
     * @SWG\Response(
     *   response=400,
     *   description="token无效"
     * ),
     * @SWG\Response(
     *   response=404,
     *   description="用户没找到"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="获取成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function getAllOrder(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'page'=>"required|integer",
            'page_size'=>"required|integer",
        ]);
        if($validator->fails())
        {
            return $this->response->error($validator->errors()->first(), 400);
        }
        $all = $request->all();
        $all['chname'] = $user->user_name;
        $list = Order::getAll($all);
        return $this->response->paginator($list, new OrderTransformer,[],function ($resource, $fractal) {
            $fractal->setSerializer(new CustomSerializer);
        });
    }

}
