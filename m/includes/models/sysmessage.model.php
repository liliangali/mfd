<?php

/* 后台通知模型 */
class SysmessageModel extends BaseModel
{
    var $table  = 'sysmessage';
    var $prikey = 'id';
    var $alias  = 'msg';
    var $_name  = 'msg';
    var $temp; // 临时变量
}

?>