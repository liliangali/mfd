<?php

/* 申请角色日志 member */
class ApplylogModel extends BaseModel
{
    var $table  = 'apply_log';
    var $prikey = 'user_id';
    var $_name  = 'applylog';

    var $_relation = array();
}

?>