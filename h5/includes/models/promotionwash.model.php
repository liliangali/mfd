<?php
/*
 * 干洗
 */
class PromotionwashModel extends BaseModel{
    var $_name  = 'promotionwash';
    var $table  = 'promotion_wash';
    var $prikey = 'wash_sn';
    
    /**
     * 送干洗
     * @param  $uid 会员ID
     * @param  $msg 说明
     * @return addID
     * @author Ruesin
     */
    function addWash($uid,$order_sn,$msg='送干洗'){
//         $sn =  microtime(true)*10000;//14
        if($uid == '' || $uid == 0 || $order_sn == '' || $order_sn == 0){
        	return false;
        }
        $sn = $this->gen_id();
        $data = array(
                'wash_sn' => $sn,
                'uid'     => $uid,
                'order_sn'=> $order_sn,
                'status'  => 0,
                'add_time'=> time(),
                'use_time'=> 0,
                'msg' => $msg,
        );
        return $this->add($data);
    }
    /**
     * 使用干洗券
     * @param  $uid      会员ID
     * @param  $wash_sn  干洗码
     * @return boolean
     * @author Ruesin
     */
    function useWash($uid = 0,$wash_sn = ''){
        if($uid==0 || $wash_sn=='')return false;
        $data = array(
            'use_time'=>time(),
            'status'=>1,
        );
        return $this->edit(" wash_sn = '$wash_sn' AND uid = '$uid' AND status = '0' ",$data);
    }
    /**
     * 获取干洗码
     * @param  uid    会员ID
     * @param  status 状态 -1:全部 0:未使用 1:已使用
     * @return 
     * @author Ruesin
     */
    function getWash($uid=0,$status=-1){
        $cond=' 1 = 1 ';
        if($uid!=0){
            $cond.=' AND uid = '.$uid;
        }
    	if($status!=-1){
    	    $cond=' AND status = '. $status;
    	}
    	
    	$arr=$this->find(array(
    	        'conditions' => $cond,
    	));
    	return $arr;
    }
    /**
     * 得到唯一的优惠券编号
     * @params null
     * @return string 优惠券编号
     */
    function gen_id(){
        $i = rand(0,9999);
        do{
            if(9999==$i){
                $i=0;
            }
            $i++;
            $num = time().str_pad($i,4,'0',STR_PAD_LEFT);
            $row=$this->get($num);//判断是否唯一
        }while($row);
        return $num;
    }
    
    
}
