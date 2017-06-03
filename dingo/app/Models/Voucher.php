<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    public $table = 'voucher';


    public static function regDebit($user_id,$user_name)
    {

        $setting = include  env('WEB_ROOT_DIR').'/data/settings.inc.php';
        if($setting['forder'] == 0 || !$user_id || !$user_name)
        {
            return;
        }
        $time = time();
        if($time >= $setting['forder_start_time'] && $time <= $setting['forder_end_time'])
        {
            if($setting['forder_money'] <= 0)
            {
                return;
            }
            $data['name'] = $setting['forder_name'];
            $data['money'] = $setting['forder_money'];
            $data['create_time'] = time();
            $data['start_time'] = time();
            $data['end_time'] = time() + $setting['forder_days']*24*60*60;
            $data['binding_time'] = time();
            $data['binding_user_id'] = $user_id;
            $data['binding_username'] = $user_name;
            $data['category'] = $setting['forder_category'];
            $data['source'] = 2;
            $data['order_money'] = $setting['forder_order_money'];
            Voucher::insert($data);
        }
    }

    public static function dogDebit($user_id,$user_name)
    {
        if(Voucher::where([['binding_user_id','=',$user_id],['source','=',3]])->first()) //已经送过券了
        {
            return false;
        }
        $time = Carbon::now(config('app.timezone'))->timestamp;
        $data['name'] = "狗民网专属券(满50可用)";
        $data['money'] = 20;
        $data['create_time'] = $time;
        $data['start_time'] = $time;
        $data['end_time'] = $time + 30*24*60*60;
        $data['binding_time'] = $time;
        $data['binding_user_id'] = $user_id;
        $data['binding_username'] = $user_name;
        $data['category'] = '1,2';
        $data['source'] = 3;
        $data['order_money'] = 50;
        return Voucher::insert($data);
    }
}
