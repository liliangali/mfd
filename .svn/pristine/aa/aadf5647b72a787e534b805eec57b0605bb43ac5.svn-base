<?php

namespace App\Models;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    public  $table = "access_token";
    public  $timestamps = false;//去掉update_time等三个字段


    public static function getToken()
    {
        $appid =config("wechat.app_id");
        $appsecret = config("wechat.secret");
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
        $token = '';
        $token_info = AccessToken::find(1);
        if($token_info)
        {
            $time = Carbon::now(config('app.timezone'))->timestamp;
            if($time > $token_info->expire_time) //过期
            {
                $client = new Client();
                $response = $client->get($url);
                $res = \GuzzleHttp\json_decode($response->getBody()->getContents(),1);
                if(isset($res['access_token']) && isset($res['expires_in']))
                {
                    $token = $res['access_token'];
                    $time = Carbon::now(config('app.timezone'))->timestamp;
                    $expire_time = $time + $res['expires_in']  - 30;//提前一分钟就刷新 accesstoken
                    $token_info->token = $res['access_token'];
                    $token_info->expire_time = $expire_time;
                    $token_info->save();
                }

            }
            else
            {
                $token = $token_info->token;
            }

        }
        return $token;
    }
}
