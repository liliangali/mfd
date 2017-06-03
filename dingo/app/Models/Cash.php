<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cash extends Model
{
    public  $table = "cash";


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
            $cash['bank_address'] = $bank_info->bank_address;
            $cash['bank_id'] = $bank_info->id;
            $cash['bank_name'] = $bank_info->bank;
            $cash['bank_card'] = $bank_info->bank_card;
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
}
