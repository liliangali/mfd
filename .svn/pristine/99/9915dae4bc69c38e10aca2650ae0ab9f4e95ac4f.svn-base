<?php

namespace App\Api\V1\Controllers\Wx;
use App\Api\V1\Controllers\BaseController;

use App\Models\AccessToken;
use App\Models\WxConfig;
use EasyWeChat\Foundation\Application;
use Illuminate\Http\Request;
use DB;
use Validator;
use JWTAuth;
use Swagger\Annotations as SWG;


class WxController extends BaseController {

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

    public function qrcode(Request $request) {
        $credentials = $request->only('qid','qtype');

        $options = [
            'open_platform' => [
                'app_id'   => env('OPEN_WECHAT_APPID'),
                'secret'   => env('OPEN_WECHAT_SECRET'),
                'token'    => env('OPEN_WECHAT_TOKEN'),
                'aes_key'  => env('OPEN_WECHAT_AES_KEY')
            ],
        ];
        $application = new Application($options);
        $app = $application->open_platform->createAuthorizerApplication(config("wechat.app_id"), WxConfig::getRefreshToken());
        $qrcode = $app->qrcode;
        if($credentials['qtype'] == 1)
        {
            $result = $qrcode->forever($credentials['qid']);
        }
        else
        {
            $result =  $qrcode->temporary($credentials['qid'], 30 * 24 * 3600);
        }
        $ticket = $result->ticket; // 或者 $result['ticket']
        $url = $qrcode->url($ticket);
        $return['url'] = $url;
        return response()->json($return);
    }
    
    public function token()
    {
        $token = AccessToken::getToken();
        if(!$token)
        {
            return $this->errorResponse('无法获得token,请联系技术人员');
        }
        return $this->successResponse(['token'=>$token]);
    }

}
