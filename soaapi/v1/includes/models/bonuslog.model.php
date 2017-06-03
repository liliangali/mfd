<?php

/* 会员 member */
class BonuslogModel extends BaseModel
{
    var $table  = 'bonus_log';
    var $prikey = 'id';
    var $_name  = 'bonuslog';
    
    
	/**
     * 执行拆红包操作
	 *
     * @param  int  $bonus_id 红包id
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @modify liuchao <280181131@qq.com>
     * @return void
     */
    function openBonusFun($bonus_id)
    {
		$return['code'] = 0;
        $return['msg'] = "";
		
		$bonus_log_mod  = m('bonuslog');
		$member_mod     = m('member');
		$order_cash_mod = m('ordercashlog');
		$bonusloginfo = $bonus_log_mod->get($bonus_id);
		if (!$bonusloginfo) {
            $return['msg'] = "此红包不存在";
            return $return;
        }
		
		if ($bonusloginfo['is_open'] == 1) {
            $return['msg'] = "当前红包已拆，不能重复拆红包！";
            return $return;
        }
		
		//修改红包状态
		$editBonus = $bonus_log_mod->edit($bonus_id, array('is_open' => 1));
		if (!$editBonus){
            $return['msg'] = "拆红包失败！";
            return $return;
        }
		
		//加入用户账户
		$member_info = $member_mod->get($bonusloginfo['user_id']);
		$cash_money  = 0;
		$type_do     = 'money';
		if($bonusloginfo['type']) {//1麦富迪币
			$cash_money = $member_info['coin'] + $bonusloginfo['cash_money'];
			$type_do = 'coin';
			$cash_type = 2;
		} else {//0:余额
			$cash_money = $member_info['money'] + $bonusloginfo['cash_money'];
			$type_do   = 'money';
			$cash_type = 4;
		}

		$edit_member = $member_mod->edit($bonusloginfo['user_id'], array($type_do => $cash_money));
		if (!$edit_member) {
            $return['msg'] = "拆红包失败！";
            return $return;
        }
		
		//加入日志
		$cashLog = array(
			'name'        => '拆红包',
			'user_id'     => $bonusloginfo['user_id'],
			'minus'       => 2,
			'cash_money'  => $bonusloginfo['cash_money'],
			'add_time'    => gmtime(),
			'order_money' => $cash_money,//帐号余额
			'type'        => $cash_type,
		);
		
        if (!$order_cash_mod->add($cashLog)) {
			$return['msg'] = "加入红包日志失败";
            return $return;
		}
		//发送系统消息
		$type_name = 'coin' ? '麦富迪币' : '元现金';
		sendSystem($bonusloginfo['user_id'], 14, '恭喜，您成功获得红包', '您成功获得红包,获得'.$bonusloginfo['cash_money'].$type_name) ;
		
		$return['code'] = 1;
        return $return;
	}
		
}

?>