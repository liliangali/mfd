<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Error extends Model
{
    public  $table = "error";
    public  $timestamps = false;//去掉update_time等三个字段
}
