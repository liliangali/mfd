<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public  $table = "region";
    public  $timestamps = false;//去掉update_time等三个字段

    public static function getList()
    {
        $p_list = collect(Region::where("parent_id",2)->orderBy("sort_order","DESC")->get(['region_name as label','region_id as value'])->toArray())->keyBy('value')->toArray();
        collect(Region::where("parent_id",'>',2)->orderBy("sort_order","DESC")->get(['region_name as label','region_id as value','parent_id'])->toArray())
            ->keyBy('value')->map(function ($item) use (&$p_list){
                if($p_list[$item['parent_id']])
                {
                    $item_t = $item;
                    unset($item_t['parent_id']);
                    $p_list[$item['parent_id']]['children'][] = $item_t;
                }
            });
        return collect($p_list)->values()->toArray();
    }
}
