<?php

namespace App\Api\V1\Controllers\Admin\User;

use App\Api\V1\Controllers\Admin\BaseController;
use App\Models\Cash;
use App\Models\ChDiscount;
use App\Models\Order;
use App\Serializer\CustomSerializer;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use Swagger\Annotations as SWG;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Api\V1\Transformers\UserTransformer;
use App\Api\V1\Transformers\UserPermissionTransformer;
use Validator;
/**
 * @SWG\Swagger(
 *   @SWG\Info(
 *     title="项目管理系统",
 *     version="1.0.0"
 *   ),
 *   @SWG\Tag(name="Auth", description="验证模块"),
 *   @SWG\Tag(name="Users", description="用户模块"),
 *   @SWG\Tag(name="Companys", description="公司模块"),
 *   @SWG\Tag(name="Departments", description="部门模块"),
 *   @SWG\Tag(name="Roles", description="角色模块"),
 *   @SWG\Tag(name="Projects", description="项目模块"),
 *   @SWG\Tag(name="Demands", description="需求模块"),
 *   @SWG\Tag(name="Groups", description="用户组模块"),
 *   @SWG\Tag(name="Pushs", description="消息推送模块"),
 *   schemes={"http"},
 *   host="pmsapi.turtletl.com",
 *   basePath="/api"
 * )
 */

class UserController extends BaseController {
    /**
     * @SWG\Get(
     *   path="/users/all",
     *   summary="显示所有用户",
     *   tags={"Users"},
     *   @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="all users"
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function userList(Request $request) {
        $validator = Validator::make($request->all(), [
            'page'=>"required|integer",
            'page_size'=>"required|integer",
        ]);
        if($validator->fails())
        {
            return $this->errorResponse($validator->errors()->first());
        }
        $users = User::getAll($request);
        return $this->successResponse($users);
    }


    /**
     * @SWG\Get(
     *   path="/users/one",
     *   summary="获取当前用户",
     *   tags={"Users"},
     *   @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     *   @SWG\Response(
     *     response=200,
     *     description="one user"
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function getAuthenticatedUser(Request $request) {

        try {

            $user = JWTAuth::parseToken()->authenticate();

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return $this->response->item($user, new UserTransformer());
        // the token is valid and we have found the user via the sub claim

//        return response()->json(compact('user'));
    }
    /**
     * @SWG\Get(
     *   path="/user/permission",
     *   summary="获取当前用户的权限",
     *   tags={"Users"},
     *   @SWG\Parameter(name="Authorization", in="header", required=true, description="用户凭证", type="string"),
     *   @SWG\Response(
     *     response=401,
     *     description="token过期"
     *   ),
     *   @SWG\Response(
     *     response=400,
     *     description="token无效"
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="不存在"
     *   ),
     *   @SWG\Response(
     *     response=406,
     *     description="无效的请求值"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="获取成功"
     *   ),
     *   @SWG\Response(
     *     response=500,
     *     description="获取失败"
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function getUserPermission()
    {

        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['user_not_found'], 404);
        }

        $userPermission = User::getUserGroup($user['id']);
        $userPermission['permission'] = json_decode($userPermission['permission']);
        return $this->response->item($userPermission, new UserPermissionTransformer());

    }

    public function getUserBasic()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $info = User::getBasic($user);

    }

    public function getUserFind(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'=>"required|integer",
        ]);
        if($validator->fails())
        {
            return $this->errorResponse($validator->errors()->first());
        }

        $users = User::find($request->id);
        $users->channelInfo;
        return $this->response->item($users, new UserTransformer,[],function ($resource, $fractal) {
            $fractal->setSerializer(new CustomSerializer);
        });
    }
    
    public function saveUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|max:255',
            'email' => 'max:255',
            'user_id' => 'required',
            'password' => 'max:12|min:6',
        ]);

        if(User::where('user_id','!=',$request->user_id)->where(function ($query) use ($request) {
            if($request->email)
            {
                $query->orWhere('user_name' ,$request->user_name)->orWhere('email' ,$request->email);
            }
            else
            {
                $query->orWhere('user_name' ,$request->user_name);
            }

        })->first())
        {
            return $this->errorResponse('用户名或者邮箱已经存在');
        }
        if ($validator->fails())
        {
            return $this->errorResponse($validator->errors()->first());
        }
        if(User::saveUser($request))
        {
            return $this->successResponse();
        }
    }
    
    public function saveDicount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'discount' => 'required',
            'min_money' => 'required',
            'max_money' => 'required',
        ]);
        if ($validator->fails())
        {
            return $this->errorResponse($validator->errors()->first());
        }
        ChDiscount::saveDis($request);
        return $this->successResponse();
    }

    public function getDicount()
    {
        return $this->successResponse(ChDiscount::getDis());
    }

    public function getCash(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page'=>"required|integer",
            'page_size'=>"required|integer",
        ]);
        if($validator->fails())
        {
            return $this->errorResponse($validator->errors()->first());
        }
        $request->is_admin = 1;
        $cash = Cash::getCash($request);
        return $this->successResponse($cash);
    }

    public function cashInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'=>"required|integer",
        ]);
        if($validator->fails())
        {
            return $this->errorResponse($validator->errors()->first());
        }
        $cash = Cash::cashById($request->id)->toArray();

        return $this->successResponse($cash);
    }

    public function saveCash(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'status' => 'required|integer',
            'id' => 'required|integer',
        ]);
        if ($validator->fails())
        {
            return $this->errorResponse($validator->errors()->first());
        }
        if($request->status == 0)
        {
            return $this->successResponse();
        }
        elseif ($request->status == 2 && !$request->msg)//审核失败必须填写失败原因
        {
            return $this->errorResponse('请填写审核失败原因');
        }
        if(Cash::checkCash($request,$user))
        {
            return $this->successResponse();
        }
        return $this->errorResponse('申请失败');

    }

}
