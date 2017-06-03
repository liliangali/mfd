<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public  $table = "region";
    public  $timestamps = false;//去掉update_time等三个字段

    public static function getList($pid)
    {
    
        $list = Region::where("parent_id","!=",0)->orderBy("sort_order","DESC")->get();
        $list = $list->toArray();
        $return = [];
        foreach ((array)$list as $index => $item)
        {
            $item_t['value'] = $item['region_id'];
            $item_t['label'] = $item['region_name'];
            if($item['parent_id'] == 2)
            {

                $return[$item['region_id']] = $item_t;
            }
            else
            {
                $return[$item['parent_id']]['children'][] = $item_t;
            }
        }
        return (object)array_values($return);
    }
}
