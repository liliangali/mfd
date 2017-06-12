<?php

namespace App\Api\V1\Controllers\Auth;
use App\Models\ChannelInfo;
use App\Models\Region;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Api\V1\Controllers\BaseController;
use App\Models\ApiSign;
use App\Models\LaravelSms;
use App\Models\Order;
use App\Models\WxConfig;
use Carbon\Carbon;
use EasyWeChat\Foundation\Application;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;
use EasyWeChat\Message\Material;

class AuthController extends BaseController {

    /**
     * @SWG\Post(
     *   path="/auth/login",
     *   summary="用户登录",
     *   tags={"Auth"},
     *   @SWG\Response(
     *     response=200,
     *     description="token"
     *   ),
     *   @SWG\Parameter(name="email", in="query", required=true, type="string", description="登录邮箱"),
     *   @SWG\Parameter(name="password", in="query", required=true, type="string", description="登录密码"),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */

    public function authenticate(Request $request) {
        // grab credentials from the request
        $credentials = $request->only('username', 'password');
        $credentials['user_name'] = $credentials['username'];
        unset($credentials['username']);
        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return $this->errorResponse('用户名或密码错误');
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return $this->errorResponse('创建token时出错');
        }
        $user = User::where('user_name', $credentials['user_name'])->first()->toArray();
        if($user['member_type']  == 0)
        {
            return $this->errorResponse('您不是渠道人员');
        }
        elseif ($user['is_service'] == 0)
        {
            return $this->errorResponse('你的渠道申请尚未通过');
        }
        $user['token'] = $token;
        $user['username'] = $user['user_name'];
        $response = array(
            'token' => $token,
            'status' => 200,
            'userinfo' => $user
        );
        return response()->json($response);
    }

    //后台登陆
    public function aauthenticate(Request $request) {
        // grab credentials from the request
        $credentials = $request->only('username', 'password');
        $credentials['user_name'] = $credentials['username'];
        unset($credentials['username']);
        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                $response = array(
                    'msg' => '用户名或密码错误',
                    'status' => 1
                );
                return response()->json($response);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            $response = array(
                'msg' => '创建token时出错',
                'status' => 1
            );
            return response()->json($response);
        }

        $user = User::where('user_name', $credentials['user_name'])->first()->toArray();
        if($user['user_id'] != 1)
        {
            $response = array(
                'msg' => '权限不足',
                'status' => 1
            );
            return response()->json($response);
        }

        $user['token'] = $token;
        $user['username'] = $user['user_name'];
        $response = array(
            'token' => $token,
            'status' => 200,
            'userinfo' => $user
        );
        return response()->json($response);
    }

    /**
     * @param 获得 appid
     * @return \Illuminate\Http\JsonResponse
     */
    public function appid(Request $request,$remark,$all=0)
    {

        $api_sign_mod = new ApiSign();
        if($all == 100) //查看所有的appid
        {
           $list =  $api_sign_mod->get();
            return response()->json($list->toArray());
        }
        if($api_sign_mod->where('remark', $remark)->first())
        {
            $response = array(
                'error' => '备注重复',
                'status' => 300
            );
            return response()->json($response);
        }
        $appid = Str::random(16);
        $appsecret = Str::random(32);
        while ($api_sign_mod->where('appid', $appid)->first())
        {
            $appid = Str::random(32);
        }
        $api_sign_mod->appid = $appid;
        $api_sign_mod->appsecret = $appsecret;
        $api_sign_mod->remark = $remark;

        if($api_sign_mod->save())
        {
            $response = array(
                'result' => $api_sign_mod->toArray(),
                'status' => 200
            );
            return $response;
        }
        $response = array(
            'error' => '添加失败',
            'status' => 300
        );
        return response()->json($response);
    }

    /**
     * @param 用户上传图片
     * @return \Illuminate\Http\JsonResponse
     */
    public function img(Request $request,$oid,$rid=0)
    {
        $application = new Application(config("wechat.open_app_config"));
        $app = $application->open_platform->createAuthorizerApplication(config("wechat.app_id"), WxConfig::getRefreshToken());
        $qrcode = $app->qrcode;
        $temporary = $app->material_temporary;
        $manager = new ImageManager(array('driver' => 'imagick'));
        $imagick = new \Imagick();
        $condition = [
            ['order_id','=',$oid],
        ];
        $order_mod = new Order();
        $item = $order_mod->where($condition)->first();
        if(!$item)
        {
            $response = array(
                'error' => '无此订单',
                'status' => 300
            );
            return response()->json($response);
        }
        $user_id = $item->user_id;
        $order_goods_info = $order_mod->getOrderQrcode($item->order_id,$rid);
        $user_info = User::find($item->user_id);
        if(!$order_goods_info) //没有diy商品 不做处理
        {

            $response = array(
                'error' => '只有diy商品才可以获得获得海报',
                'status' => 301
            );
            return response()->json($response);
        }

        $result = $qrcode->temporary($user_id, 30 * 24 * 3600);
        $item->erweima_url = $qrcode->url($result->ticket);
        $client = new Client();
        //二维码图片本地化  人头像本地化     二维码图片resize大小 人图片resize大小
        try
        {
            Storage::disk('local')->put('tqrcode/'.$user_id.'q.png', $client->request('get',$item->erweima_url)->getBody()->getContents());
            $manager->make(storage_path('app/tqrcode/').$user_id.'q.png')->resize(130,130)->save();
//            if(!(Storage::disk('local')->exists('tqrcode/'.$user_id.'a.png')))
//            {
                Storage::disk('local')->put('tqrcode/'.$user_id.'a.png', $client->request('get',$user_info->avatar)->getBody()->getContents());
                $manager->make(storage_path('app/tqrcode/').$user_id.'a.png')->resize(90,90)->save();
//            }
        }
        catch (\GuzzleHttp\RequestException $e)
        {
            $response = array(
                'error' => 'ERROR异常',
                'status' => 302
            );
            return response()->json($response);
        }

        //狗图像resize大小
        $manager->make($order_goods_info['style'])->resize(290,290)->save(storage_path('app/tqrcode/').$user_id.'s.png');

        //diy图片做成圆形
        $imagick->readImage(storage_path('app/tqrcode/').$user_id.'s.png');
        $circle = new \Imagick();
        $circle->newImage(290, 290, 'none');
        $circle->setimageformat('png');
        $circle->setimagematte(true);
        $draw = new \ImagickDraw();
        $draw->setfillcolor('#ffffff');
        $draw->circle(290/2, 290/2, 290/2, 290);
        $circle->drawimage($draw);
        $imagick->setImageFormat( "png" );
        $imagick->setimagematte(true);
        $imagick->cropimage(290, 290, 0, 0);
        $imagick->compositeimage($circle, \Imagick::COMPOSITE_DSTIN, 0, 0);
        $imagick->writeImage(storage_path('app/tqrcode/').$user_id.'s.png');
        $imagick->destroy();

        //初始化空白版
        $imgblank = $manager->make(resource_path("assets/img/wx.png"));
        //合成人图像
        $imgblank->insert(storage_path('app/tqrcode/').$user_id.'a.png','top-left', 45,  480);
        //合成狗头
        $imgblank->insert(storage_path('app/tqrcode/').$user_id.'s.png','top-left', 175,  465);
        //合成二维码
        $imgblank->insert(storage_path('app/tqrcode/').$user_id.'q.png','top-left', 493,  435);
        //合成文字
        $imgblank->text($item->user_name, 90, 600, function($font) {
            $font->file(resource_path("assets/img/msyh.ttf"));
            $font->size(18);
            $font->align('center');
            $font->angle('top');
        });
        $imgblank->text('狗狗 : '.$order_goods_info['dog_name'], 30, 685, function($font) {
            $font->file(resource_path("assets/img/msyh.ttf"));
            $font->size(20);
            $font->align('left');
            $font->angle('top');
        });
        $imgblank->text('犬期 : '.$order_goods_info['quanqi_str'], 30, 720, function($font) {
            $font->file(resource_path("assets/img/msyh.ttf"));
            $font->size(20);
            $font->align('left');
            $font->angle('top');
        });
        $imgblank->text('生日 : '.$order_goods_info['dog_date'], 30, 750, function($font) {
            $font->file(resource_path("assets/img/msyh.ttf"));
            $font->size(20);
            $font->align('left');
            $font->angle('top');
        });
        $imgblank->text('功效 : '.$order_goods_info['gongxiao_str'], 30, 780, function($font) {
            $font->file(resource_path("assets/img/msyh.ttf"));
            $font->size(20);
            $font->align('left');
            $font->angle('top');
        });
        $jiyu1 = mb_substr($order_goods_info['dog_desc'],0,11);
        $jiyu2 = mb_substr($order_goods_info['dog_desc'],11);
        $imgblank->text('主人寄语', 518, 720, function($font) {
            $font->file(resource_path("assets/img/msyh.ttf"));
            $font->size(19);
            $font->align('left');
            $font->angle('top');
        });
        $imgblank->text($jiyu1, 445, 750, function($font) {
            $font->file(resource_path("assets/img/msyh.ttf"));
            $font->size(17);
            $font->align('left');
            $font->angle('top');
        });
        $imgblank->text($jiyu2, 400, 777, function($font) {
            $font->file(resource_path("assets/img/msyh.ttf"));
            $font->size(17);
            $font->align('left');
            $font->angle('top');
        });
        $new_url = $user_id.'_'.$item->order_id.time().'f'.Str::random(10).'.png';
        $imgblank->save(storage_path('app/public/tqrcode/').$new_url);
        //上传素材并发送消息
        $result = $temporary->uploadImage(storage_path('app/public/tqrcode/').$new_url);
        $mediaId = $result->media_id;
        $material = new Material('image', $mediaId);
        $app->staff->message($material)->to($user_info->openid)->send();
        $item->erweima_url = asset('storage/tqrcode/'.$new_url);
        $item->save();
    }


    /**
     * @param 用户上传图片
     * @return \Illuminate\Http\JsonResponse
     */
    public function imgup(Request $request)
    {
        $manager = new ImageManager(array('driver' => 'imagick'));
        $img =  $manager->make($_FILES['file']['tmp_name']);       // Image::make() 支持这种方式
        $dir = storage_path('app/public/upload/');
        $string = Carbon::now(config('app.timezone'))->timestamp.str_random(10).'.png';
        $img->save($dir.$string);
        $response = array(
            'img_url' =>asset('storage/upload/'.$string)
        );
        return $this->successResponse($response);
        $imagick = new \Imagick();
        //狗图像resize大小
        $manager->make(storage_path('app/tqrcode/s.png'))->resize(290,290)->save(storage_path('app/tqrcode/ss.png'));
        $manager->make(storage_path('app/tqrcode/q.png'))->resize(130,130)->save();
        $manager->make(storage_path('app/tqrcode/a.png'))->resize(90,90)->save();//人

        //diy图片做成圆形
        $imagick->readImage(storage_path('app/tqrcode/ss.png'));
        $circle = new \Imagick();
        $circle->newImage(290, 290, 'none');
        $circle->setimageformat('png');
        $circle->setimagematte(true);
        $draw = new \ImagickDraw();
        $draw->setfillcolor('#ffffff');
        $draw->circle(290/2, 290/2, 290/2, 290);
        $circle->drawimage($draw);
        $imagick->setImageFormat( "png" );
        $imagick->setimagematte(true);
        $imagick->cropimage(290, 290, 0, 0);
        $imagick->compositeimage($circle, \Imagick::COMPOSITE_DSTIN, 0, 0);
        $imagick->writeImage(storage_path('app/tqrcode/ss.png'));
        $imagick->destroy();


       
        //初始化空白版
        $imgblank = $manager->make(storage_path('app/tqrcode/wx.png'));
        //合成人图像
        $imgblank->insert(storage_path('app/tqrcode/a.png'),'top-left', 45,  480);
        //合成狗头
        $imgblank->insert(storage_path('app/tqrcode/ss.png'),'top-left', 175,  465);
        //合成二维码
        $imgblank->insert(storage_path('app/tqrcode/q.png'),'top-left', 493,  435);
        $strr = htmlspecialchars_decode("狗'仔");
        //合成文字
        $imgblank->text($strr, 90, 620, function($font) {
            $font->file(resource_path("assets/img/msyh.ttf"));
            $font->size(17);
            $font->align('center');
            $font->angle('top');
        });
        
        $imgblank->text('狗狗 : 安迪', 30, 685, function($font) {
            $font->file(resource_path("assets/img/msyh.ttf"));
            $font->size(20);
            $font->align('left');
            $font->angle('top');
        });
        $imgblank->text('犬期 : 成全期', 30, 720, function($font) {
            $font->file(resource_path("assets/img/msyh.ttf"));
            $font->size(20);
            $font->align('left');
            $font->angle('top');
        });
        $imgblank->text('生日 : 2017-09-08', 30, 750, function($font) {
            $font->file(resource_path("assets/img/msyh.ttf"));
            $font->size(20);
            $font->align('left');
            $font->angle('top');
        });
        $imgblank->text('功效 : 减肥瘦身,减肥瘦身,减肥瘦身,', 30, 780, function($font) {
            $font->file(resource_path("assets/img/msyh.ttf"));
            $font->size(20);
            $font->align('left');
            $font->angle('top');
        });
        $str = "365填的配合,谢谢有你相信你是我独一无二的选择";
        $jiyu1 = mb_substr($str,0,12);
        $jiyu2 = mb_substr($str,12);
        $imgblank->text('主人寄语', 518, 720, function($font) {
            $font->file(resource_path("assets/img/msyh.ttf"));
            $font->size(19);
            $font->align('left');
            $font->angle('top');
        });
        $imgblank->text($jiyu1, 445, 750, function($font) {
            $font->file(resource_path("assets/img/msyh.ttf"));
            $font->size(17);
            $font->align('left');
            $font->angle('top');
        });
        $imgblank->text($jiyu2, 400, 777, function($font) {
            $font->file(resource_path("assets/img/msyh.ttf"));
            $font->size(17);
            $font->align('left');
            $font->angle('top');
        });
        $imgblank->save(storage_path('app/tqrcode/f.png'));
        echo '<pre>';print_r(11);exit;
        
        //上传素材并发送消息
    }

    /**
     * @SWG\Post(
     *   path="/auth/register",
     *   summary="用户注册",
     *   tags={"Auth"},
     *   @SWG\Response(
     *     response=200,
     *     description="register success"
     *   ),
     *   @SWG\Parameter(name="name", in="query", required=true, type="string", description="用户名"),
     *   @SWG\Parameter(name="email", in="query", required=true, type="string", description="登录邮箱"),
     *   @SWG\Parameter(name="password", in="query", required=true, type="string", description="登录密码"),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|unique:member|max:255',
            'email' => 'required|unique:member|max:255',
            'pass' => 'required|max:12|min:6',
            'checkPass' => 'required|max:12|min:6',
            'phonecode' => 'required',
            'zuzhi' => 'required|max:255',
            'shangpu' => 'required|max:255',
            'option' => 'required',
            'address' => 'required',
            'imageUrl' => 'required',
        ],[
            'unique' => '此手机号已经注册!请勿重复注册',//自定义错误提示
        ]);
        if ($validator->fails())
        {
            return $this->errorResponse($validator->errors()->first());
        }
        if($request->get("pass") != $request->get("checkPass"))
        {
            return $this->errorResponse('两次密码不一致');
        }
        if(!(LaravelSms::checkSms($request->user_name,$request->phonecode)))
        {
            return $this->errorResponse('验证码错误或者已过期');
        }
        User::register($request->all());
        return $this->successResponse();
    }
    /**
     * @SWG\Post(
     *   path="/auth/resetPassword",
     *   summary="重置密码",
     *   tags={"Auth"},
     *   @SWG\Response(
     *     response=200,
     *     description="modify success"
     *   ),
     *   @SWG\Parameter(name="email", in="query", required=true, type="string", description="登录邮箱"),
     *   @SWG\Parameter(name="password", in="query", required=true, type="string", description="登录密码"),
     *   @SWG\Parameter(name="resetPassword", in="query", required=true, type="string", description="确认密码"),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function resetPassword(Request $request){
        $per = [
           'email'=>$request ->get('email'),
           'password'=>bcrypt($request ->get('password')),
       ];
        $peo = [
           'resetPassword'=>bcrypt($request ->get('resetPassword'))
        ];
        $userExist = User::findUserEmail($per['email']);
        if(empty($userExist)){
            $response = array(
                'error'=>'用户不存在',
                'status'=>400,
                );
            return response() -> json($response);
        }
        $user = User::changePassword($userExist['id'],$per['password']);
        if($user === false){
            return $this->errorResponse("重置密码失败");
        } else {
            return $this->successResponse("重置密码成功");
        }

    }
}
