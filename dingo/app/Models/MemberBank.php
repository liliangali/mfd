<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberBank extends Model
{
    
    public static function bankList()
    {
        return [
            ['id'=>1,'name'=>'中国银行'],
            ['id'=>2,'name'=>'青岛银行'],
            ['id'=>3,'name'=>'民生银行'],
            ['id'=>4,'name'=>'工商银行'],
            ['id'=>5,'name'=>'农业银行'],
            ['id'=>6,'name'=>'建设银行'],
            ['id'=>7,'name'=>'邮政储蓄银行'],
            ['id'=>8,'name'=>'交通银行'],
            ['id'=>9,'name'=>'招商银行'],
            ['id'=>10,'name'=>'中信银行'],
            ['id'=>11,'name'=>'光大银行'],
            ['id'=>12,'name'=>'平安银行'],
            ['id'=>13,'name'=>'光发银行'],
            ['id'=>14,'name'=>'兴业银行'],
        ];
    }

    public static function getBankList()
    {
        $list = MemberBank::bankList();
        $rlist = collect($list)->map(function ($item,$key){
            $item['value'] = $item['name'];
            $item['label'] = $item['name'];
            unset($item['id']);
            unset($item['name']);
            return $item;
        })->toArray();
      return $rlist;
    }

    public static function saveBank($req)
    {
        $bank_arr = [
            'user_id' => $req->user_id,
            'card_name' => $req->card_name,
            'bank_card' => $req->bank_card,
            'bank_address' => $req->bank_address,
            'bank' => $req->bank,
        ];
        if(MemberBank::where('user_id',$req->user_id)->first())
        {
            MemberBank::where('user_id',$req->user_id)->update($bank_arr);
        }
        else
        {
            MemberBank::insert($bank_arr);
        }
    }

    public static  function getBankByUid($uid)
    {
        $bank_info = MemberBank::where('user_id',$uid)->first();
        if($bank_info)
        {
            return $bank_info->toArray();
        }
        return [];
    }
}
