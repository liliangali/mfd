<?php

namespace App\Http\Controllers;

use App\Models\WxConfig;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\Text;

class WxToolController extends Controller
{


    /**
     * 二维码
     */
    public function qrcode()
    {
        $options = [
            'open_platform' => [
                'app_id'   => env('OPEN_WECHAT_APPID'),
                'secret'   => env('OPEN_WECHAT_SECRET'),
                'token'    => env('OPEN_WECHAT_TOKEN'),
                'aes_key'  => env('OPEN_WECHAT_AES_KEY')
            ],
        ];
        $aapp = new Application($options);
        $openPlatform = $aapp->open_platform;//开发平台
        $app = $openPlatform->createAuthorizerApplication(config("wechat.app_id"), WxConfig::getRefreshToken());
        $temporary = $app->material_temporary;
        $result = $temporary->uploadImage("/home/wwwroot/mfdplatform.p.day900.com/static/img/wx2.png");  // 请使用绝对路径写法！除非你正确的理解了相对路径（好多人是没理解对的）！
    echo '<pre>';print_r($result);exit;
 echo '<pre>';print_r($app);exit;
        // 调用方式与普通调用一致。
    }
}
