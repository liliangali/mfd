<?php

namespace App\Api\V1\Controllers\Fbcategory;
use App\Api\V1\Controllers\BaseController;
use App\Api\V1\Transformers\OrderTransformer;

use App\Models\FbCategory;
use Illuminate\Http\Request;
use App\Models\Order;
use DB;
use Validator;
use JWTAuth;
use Swagger\Annotations as SWG;


class FbcategoryController extends BaseController {

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

    public function getAll(Request $request) {
//        $validator = Validator::make($request->all(), [
//            'page'=>"required|integer",
//            'page_size'=>"required|integer",
//        ]);
//        if($validator->fails())
//        {
//            return $this->response->error($validator->errors()->first(), 400);
//        }

        $list = FbCategory::get()->toArray();
        $pid=[1,6,16,34,11];
        $return = [];
        foreach ($list as $index => $item)
        {
            if(in_array($item['parent_id'],$pid))
            {
                $return[$item['parent_id']][] = $item;
            }
        }
        echo '<pre>';print_r($return);exit;
        

        $list = Order::getAll($request->page_size);
        return $this->response->paginator($list, new OrderTransformer())->setStatusCode(200);

    }

}
