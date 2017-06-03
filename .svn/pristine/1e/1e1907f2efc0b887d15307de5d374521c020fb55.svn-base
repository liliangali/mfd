<?php

/* 礼物*/
class GiftModel extends BaseModel
{
    var $table  = 'gift';
    var $prikey = 'id';
    var $_name  = 'gift';

    var $_relation = array(

    );

    /**
     *添加红包
     *@param $data
     *@param $uid  用户id
     *@param $money 奖励金额
     *@author yusw
     *@2015年5月29日
     */
    function submit($data,$uid,$money)
    {

        //红包表
        $res = $this->add($data);

        if($res ===false){
            return false;
        }
        $member_mod =& m('member');
        $res = $member_mod->setInc("user_id=$uid",'money',$money);
        if($res === false){
            return false;
        }
        return true;
    }

}

?>