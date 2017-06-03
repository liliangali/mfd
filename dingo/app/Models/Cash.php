<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cash extends Model
{
    public  $table = "cash";

    public function users()
    {
        return $this->belongsTo('App\User','user_id','user_id');
    }

    public static function saveCash($req,$user)
    {
        $bank_info = MemberBank::getBankByUid($user->user_id);
        if(!$bank_info)
        {
            return false;
        }
        if($req->money > $user->money)
        {
            return false;
        }

        DB::beginTransaction();
        try
        {
            $cash['user_id'] = $user->user_id;
            $cash['bank_address'] = $bank_info['bank_address'];
            $cash['bank_id'] = $bank_info['id'];
            $cash['bank_name'] = $bank_info['bank'];
            $cash['bank_card'] = $bank_info['bank_card'];
            $cash['cash_money'] = $req->money;
            if(!(Cash::insert($cash)))
            {
                DB::rollBack();
            }
            if(!($user->decrement('money',$req->money)))
            {
                DB::rollBack();
            }
            if(!($user->increment('frozen',$req->money)))
            {
                DB::rollBack();
            }
            DB::commit();
        }
        catch (Exception $e)
        {
            DB::rollBack();
        }
        $bank = MemberBank::getBankByUid($user->user_id);
        if(!$bank)
        {
            
        }
    }

    public static function getCash($request)
    {
        $condition = [];
        if(!isset($request->is_admin) && $request->user_id) //fei管理员
        {
           $condition[] = ['user_id',$request->user_id];
        }
        if($request->user_name)
        {
            $user_info = User::getByName($request->user_name);
            if(!$user_info)
            {
                return [];
            }
            $condition[] = ['user_id',$request->user_id];
        }
        if(isset($request->status))
        {
            $condition[] = ['status',$request->status];
        }

        $cash_list = Cash::where($condition)->orderBy('id', 'desc')->paginate($request->page_size)->toArray();
        $user_list = collect(User::whereIn('user_id', array_unique(collect($cash_list['data'])->pluck('user_id')->toArray()))->get()->makeHidden(User::hidenList()['default'])->toArray())->keyBy('user_id')->toArray();
        $cash_list['data'] = collect($cash_list['data'])->map(function ($item) use($user_list){
           if($user_list[$item['user_id']])
           {
               $item['uinfo'] = $user_list[$item['user_id']];
           }
           if($item['status'] == 0)
           {
               $item['status'] = 1;
           }
           else
           {
               $item['status'] = 2;
           }
           return $item;
        })->values();
        return $cash_list;
    }
}
