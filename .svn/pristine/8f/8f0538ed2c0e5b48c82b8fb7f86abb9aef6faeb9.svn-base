<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LaravelSms extends Model
{

    const REG_TEMP_ID = 'SMS_12910731';//注册短信模板ID


    public static function checkSms($phone,$code)
    {
        return true;
        $sms = LaravelSms::where("to",$phone)->where("fail_times",0)->orderBy("id","DESC")->first();
        $time = Carbon::now(config('app.timezone'))->timestamp;
        $smsdata = json_decode($sms->data,1);
        if(!$sms || !(($sms->sent_time + 300) >= $time)  || $smsdata['code'] != $code)
        {
            return false;
        }
        return true;
    }
}
