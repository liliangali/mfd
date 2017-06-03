<?php

/* 推荐安装 */
class MemberInviteModel extends BaseModel
{
    var $table  = 'member_invite';
    var $prikey = 'id';
    var $_name  = 'member_invite';
    
        var $_relation = array(
        //g_id可能需要设置为外键

        'belongs_to_generalize' => array(
            'model'             => 'generalize',
            'type'              => BELONGS_TO,
            'foreign_key'       => 'g_id',
            'reverse'           => 'has_g_member',
        ),
        
         'belongs_to_member' => array(
            'model'             => 'member',
            'type'              => BELONGS_TO,
            'foreign_key'       => 'user_id',
            'reverse'           => 'has_member',
        ),
            
        // 一个认证属于一个会员
        'belongs_to_user' => array(
            'model'         => 'member',
            'type'          => BELONGS_TO,
            'foreign_key'   => 'invitee',
            'reverse'       => 'has_invi',
        ),
            
            'has_one_to_user' => array(
                'model'         => 'member',
                'type'          => HAS_ONE,
                'foreign_key'   => 'user_id',
                'refer_key' => 'inviter'
            ),
        
        
  
        
 
     );
}

?>