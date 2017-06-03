<?php

namespace App\Api\V1\Controllers\Role;

use App\Api\V1\Controllers\BaseController;
use App\Api\V1\Transformers\RegionTransformer;
use App\Models\Region;
use App\Serializer\CustomSerializer;
use App\RoleType;
use Illuminate\Http\Request;
use Swagger\Annotations as SWG;
use App\Api\V1\Transformers\RoleTypeTransformer;
use SmsManager;
use Validator;


class RoleController extends BaseController {

    /** 
     * @SWG\Get(
     * path="/role/role_type/{grade}",
     * summary="获取角色类型",
     * tags={"Roles"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="grade", in="path", required=true, description="类型等级", type="integer"),
     * @SWG\Response(
     *   response=401,
     *   description="token过期"
     * ),
     * @SWG\Response(
     *   response=400,
     *   description="token无效"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="获取角色类型成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取角色类型失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */

    public function getRoleType($grade) {
        $grade = (integer)($grade);
        $roleTypes= RoleType::getRoleType($grade);
        return $this->response->collection($roleTypes, new RoleTypeTransformer);
    }

    /**
     * @SWG\Get(
     * path="/role/role_type/{grade}",
     * summary="地区列表",
     * tags={"Roles"},
     * @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     * @SWG\Parameter(name="grade", in="path", required=true, description="类型等级", type="integer"),
     * @SWG\Response(
     *   response=401,
     *   description="token过期"
     * ),
     * @SWG\Response(
     *   response=400,
     *   description="token无效"
     * ),
     * @SWG\Response(
     *   response=406,
     *   description="无效的请求值"
     * ),
     * @SWG\Response(
     *   response=200,
     *   description="获取角色类型成功"
     * ),
     * @SWG\Response(
     *   response=500,
     *   description="获取角色类型失败"
     * ),
     * @SWG\Response(
     *   response="default",
     *   description="an ""unexpected"" error"
     * )
     * )
     */
    public function region(Request $request) {
        $validator = Validator::make($request->all(), [
            'rid'=>"required|integer",
        ]);
        if($validator->fails())
        {
            return $this->response->error($validator->errors()->first(), 1);
        }
        $list = Region::getList($request->rid);
        return $this->response->item($list, new RegionTransformer(),[],function ($resource, $fractal) {
            $fractal->setSerializer(new CustomSerializer);
        });
    }

    public function pcode(Request $request)
    {
        $result = SmsManager::validateFields();
        if(!$result['success'])
        {
            if($result['type'] == 'check_mobile_unique')
            {
                $response = array(
                    'msg' => '此手机号已经注册',
                    'status' => 1
                );
                return response()->json($response);
            }
            else
            {
                $response = array(
                    'msg' => '发送失败',
                    'status' => 1
                );
                return response()->json($response);
            }
        }
        $result = SmsManager::requestVerifySms();
        if(!$result['success'])
        {
            $response = array(
                'msg' => '短信发送失败',
                'status' => 1
            );
            return response()->json($response);
        }
        $response = array(
            'msg' => '短信发送成功',
            'status' => 200
        );
        return response()->json($response);
    }
}
