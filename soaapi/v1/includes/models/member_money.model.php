<?php

class Member_moneyModel extends BaseModel
{
    var $table  = 'member_money';
    var $prikey = 'id';
    var $_name  = 'member_money';
    
    
    function change_money($user_id = 0, $change = 0, $type = 1, $operator = '', $reason ='' , $remark = '' ){
        
        $mMem = &m('member');
        $log =$this->get(array(
        	'conditions' => "user_id = '{$user_id}'",
        	'order'      => 'addtime DESC',
        ));
        
        if(!empty($log)){
            $money = $log['money'] + $change;
        }else{
            $money = $change;
        }
        if($money < 0){
            return false;
        }
        $transaction = $this->beginTransaction();
        $data = array(
                'user_id'      => $user_id,
                'money'        => $money,
                'change_money' => $change,
                'addtime'      => gmtime(),
                'reason'       => $reason,
                'remark'       => $remark,
                'type'         => $type,
                'operator'     => $operator,
        );
        
        $res = $this->add($data);
        if(!$res){
            $this->rollback();
            return false;
        }
        
        $mData = array(
                'money'  => $money,
        );
        
        if($type == 2 || $type == 3){
            $member = $mMem->get($user_id);
            $mData['frozen'] = $member['frozen'] - $change;
        }
        
        $res = $mMem->edit($user_id,$mData);
        
        if(!$res){
            $this->rollback();
            return false;
        }
        
        $this->commit(true);
        return true;
        
        
    }
}
