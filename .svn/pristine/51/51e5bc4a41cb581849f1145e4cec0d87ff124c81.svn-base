<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WxConfig extends Model
{

    public  $timestamps = false;//去掉update_time等三个字段
    protected static function  getRefreshToken()
    {
        $wxconfig = WxConfig::where("wxcode","authorizer_refresh_token")->first();
        return $wxconfig->wxvalue;
    }
}
