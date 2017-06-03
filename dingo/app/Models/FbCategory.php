<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FbCategory extends Model
{
    public $table = 'fbcategory';
    const SHENGZHANG = 34;
    const GONGXIAO = 1;
    const DOGTYPE = [39,22,23,24,60,73];
}
