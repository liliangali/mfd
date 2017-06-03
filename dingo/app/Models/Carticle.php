<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carticle extends Model
{


    public static function getAll($request)
    {
        $condition[] = ['id','>=','1'];
        if(isset($request['title']) && $request['title'])
        {
            $condition[] = ['title','like',$request['title'].'%'];
        }
        return Carticle::where($condition)->orderBy("id","DESC")->paginate($request['page_size'])->toArray();
    }

    public static function deleteArticle($request)
    {

        if(!$request['id'])
        {
            return;
        }
        Carticle::whereIn('id',explode(',',$request['id']))->delete();
    }
}


