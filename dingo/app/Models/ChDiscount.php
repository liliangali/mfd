<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChDiscount extends Model
{
    //
    public static function saveDis($req)
    {
        $ch_discount = new ChDiscount();
        $ch = $ch_discount->where('type','=','default')->first();
        if($ch)
        {
            $ch_discount->where("type",'default')->update(['discount'=>$req->discount]);
        }
        else
        {
            $ch_discount->discount = $req->discount;
            $ch_discount->type = 'default';
            $ch_discount->save();
        }

    }
}
