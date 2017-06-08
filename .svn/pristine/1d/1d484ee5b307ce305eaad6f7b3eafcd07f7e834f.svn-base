<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChDiscount extends Model
{
    public static function saveDis($req)
    {
        ChDiscount::saveData('discount',$req->discount);
        ChDiscount::saveData('min_money',$req->min_money);
        ChDiscount::saveData('max_money',$req->max_money);
    }

    public static function saveData($type,$val)
    {
        $data['dval'] = $val;
        $data['type'] = $type;
        if(ChDiscount::where('type','=',$type)->first())
        {
            ChDiscount::where("type",$type)->update($data);
        }
        else
        {
            ChDiscount::insert($data);
        }
    }
    
    public static function getDis()
    {
        $ch = collect(ChDiscount::get()->toArray())->keyBy('type')->toArray();
        $return['discount'] = isset($ch['discount']) ? $ch['discount']['dval'] : '';
        $return['min_money'] = isset($ch['min_money']) ? $ch['min_money']['dval'] : '';
        $return['max_money'] = isset($ch['max_money']) ? $ch['max_money']['dval'] : '';
        return $return;
    }
}
