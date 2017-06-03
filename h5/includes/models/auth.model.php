<?php
/**
 *实名认证模型
 * @author liuchao <280181131@qq.com>
 * @version 1.0
 * @copyright Copyright 2014 caifeng.com
 *
 */
class AuthModel extends BaseModel
{
    var $table  = 'auth';
    var $prikey = 'id';
    var $_name  = 'auth';

    var $_relation = array(
        // 一个认证属于一个会员
        'belongs_to_user' => array(
            'model'         => 'member',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'user_id',
            'reverse'       => 'has_auth',
        ),
    );
}


