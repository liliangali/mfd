<?php

namespace App\Api\V1\Controllers\User;

use App\Api\V1\Controllers\BaseController;
use App\Models\Cash;
use App\Models\Error;
use App\Models\MemberBank;
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

    public function show(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'page'=>"required|integer",
            'page_size'=>"required|integer",
        ]);
        if($validator->fails())
        {
            return $this->response->error($validator->errors()->first(), 400);
        }

        $condition[] = ['channel_pid','=',$user->user_id];
        if($request->user_name)
        {
            $condition[] = ['user_name','like',$request->user_name.'%'];
        }

        $users = User::where($condition)->orderBy('user_id', 'desc')->paginate($request->page_size);

        return $this->response->paginator($users, new UserTransformer,[],function ($resource, $fractal) {
            $fractal->setSerializer(new CustomSerializer);
        });
    }


    public function chl(Request $request) {

        $user = JWTAuth::parseToken()->authenticate();

        $validator = Validator::make($request->all(), [
            'page'=>"required|integer",
            'page_size'=>"required|integer",
        ]);
        if($validator->fails())
        {
            return $this->errorResponse($validator->errors()->first());
        }

        $condition[] = ['member_type','>',0];
        if($request->username)
        {
            $condition[] = ['user_name','like',$request->username.'%'];
        }

        if($request->satus == 1)//未审核
        {
            $condition[] = ['is_service','=',0];
        }
        elseif ($request->satus == 2)//已审核
        {
            $condition[] = ['is_service','>',0];
        }
        //'cstatus' => $user['is_service'],
        $users = User::leftJoin('channel_infos', 'member.user_id', '=', 'channel_infos.user_id')->where($condition)
            ->select('channel_infos.*','member.user_id','member.user_name','member.user_name','member.gender','member.last_login','member.is_service')
            ->orderBy('member.user_id', 'desc')->paginate($request->page_size);
        return $this->response->paginator($users, new UserTransformer,['key' => 'userss'],function ($resource, $fractal) {
            $fractal->setSerializer(new CustomSerializer);
        });
        //return $this->response->collection($users, new UserTransformer);
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
                return $this->errorResponse('user_not_found');
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return $this->errorResponse('token_invalid');
        } catch (JWTException $e) {
            return $this->errorResponse('token_absent');
        }
        $user->channelInfo;
        return $this->response->item($user, new UserTransformer,[],function ($resource, $fractal) {
            $fractal->setSerializer(new CustomSerializer);
        });
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
        //channel_pid
        $user_num = User::where("channel_pid",$user->user_id)->count();
        $order_num = Order::where("channel_pid",$user->user_id)->count();
        $order_money_sum =  Order::where("channel_pid",$user->user_id)->whereIn("status",[20,30,40])->sum("final_amount");
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
        $id = $request->id;
        $users = User::find($request->id);
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
    
    public function getBank(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $bank_info = MemberBank::getBankByUid($user->user_id);
        $bank_info['money'] = $user['money'];
        return $this->successResponse(['bank_info'=>$bank_info]);
    }

    public function saveBank(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'bank_card' => 'required|max:255',
            'bank_address' => 'required',
            'bank' => 'required',
            'card_name' => 'required',
        ]);
        if(MemberBank::where('user_id','!=',$user->user_id)->where(function ($query) use ($request) {
            $query->orWhere('bank_card' ,$request->bank_card);
        })->first())
        {
            return $this->errorResponse('此银行卡已绑定');
        }

        if ($validator->fails())
        {
            return $this->errorResponse($validator->errors()->first());
        }
        $request->user_id = $user->user_id;
        MemberBank::saveBank($request);
        return $this->successResponse();
    }

    public function getBankL()
    {
        return $this->successResponse(MemberBank::getBankList());
    }

    public function saveCash(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'phonecode' => 'required|max:255',
            'money' => 'required|max:255',
        ]);
        if ($validator->fails())
        {
            return $this->errorResponse($validator->errors()->first());
        }
        if(Cash::where("user_id",$user->user_id)->where('status','!=',1)->first())
        {
            return $this->errorResponse('您有提现申请尚未处理,请先处理上一条提现申请');
        }
        if(Cash::saveCash($request,$user))
        {
            return $this->successResponse();
        }
        return $this->errorResponse('申请失败');

    }

    public function getCash(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $validator = Validator::make($request->all(), [
            'page'=>"required|integer",
            'page_size'=>"required|integer",
        ]);
        if($validator->fails())
        {
            return $this->errorResponse($validator->errors()->first());
        }
        $request->user_id = $user->user_id;
        $cash = Cash::getCash($request);
        return $this->successResponse($cash);
    }
}
