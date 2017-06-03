<?php

/* 会员 member */
class AccountModel extends BaseModel
{
    var $table  = 'account_log';
    var $prikey = 'id';
    var $_name  = 'account';

    
    /**
    *后台调整冻结资金
    *@author liang.li <1184820705@qq.com>
    *@2015年6月9日
    */
    function submit($log) 
    {
       $mod = m('member');
       if (!$this->add($log))
       {
          return false; 
       }
       $money = $log['money'];
       $frozen = $log['frozen'];
       
       
       if($mod->setInc($log['user_id'],'money',$money) === false)
       {
           return false;
       }
       if($mod->setInc($log['user_id'],'frozen',$frozen) === false)
       {
           return false;
       }
       
       return true;
    }
}

?>