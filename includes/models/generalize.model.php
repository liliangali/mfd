<?php

/* 主题与基本款关连表 */
class GeneralizeModel extends BaseModel
{
    var $table  = 'generalize';
    var $prikey = 'id';
    var $_name  = 'generalize';


    //获取组织 名称
    function get_g(){
        $g_arr = $this->find();
        $generalize = i_array_column($g_arr,'name');
        return $generalize;
    }
}

?>