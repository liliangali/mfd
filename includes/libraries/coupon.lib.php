<?php
/**
 * 优惠券接口
 * @author Ruesin
 */
/**
 * 从原来的接口中删掉的
 * getCouponById 直接 $m->get($id)
 * getCouponList 直接 $m->findAll()
 */
class Coupon extends Object{
    var $_coupon_mod;
    var $_couponsn_mod;
    function __construct($options = null){
        $this->Coupon();
    }
    function Coupon(){
        $this->_coupon_mod          =& m("coupon");
        $this->_couponsn_mod      	=& m('couponsn');
    }
    /**
     * 得到唯一的优惠券编号
     * @params null
     * @return string 优惠券编号
     */
    public function gen_id($pre){
        $i = rand(0,9999);
        do{
            if(9999==$i){
               $i=0;
            }
            $i++;
            $num = date('YmdHis').str_pad($i,4,'0',STR_PAD_LEFT);
            $sn=$pre.$num;
            $row=$this->_coupon_mod->get($sn);//判断是否唯一
        }while($row);
        return $sn;
    }
    /**
     * 给用户分配一个优惠卷
     * @param int $coupon_id
     * @return array $result
     * @author Ruesin
     */
    function addCoupon($cpn_id=0,$uid=0,$msg='',$type='cpn'){
        if($uid == 0 ) return false;
        if($type=='exp'){
            $pre    = 'EXP0';
            $cpn_id = 0;
        }else{
            $coupon = $this->_coupon_mod->get($cpn_id);
            $time = time();
            if($coupon['cpn_status'] == '0' || $coupon['start_time'] > $time || $coupon['end_time'] < $time)
                return false;
            $sn = $coupon['cpn_prefix'];
        }
        $sn = $this->gen_id($pre);

        $data = array(
            'coupon_sn'=>$sn,
            'create_time'=>time(),
            'cpn_id'=>$cpn_id,
            'uid'=>$uid,
            'up_time'=>0,
            'use_time'=>0,
            'claim'=>1,
            'type' => $type,
            'msg' => $msg,
        );
        return $this->_couponsn_mod->add($data);
    }
    
    /**
     * 获取优惠券列表
     * @param $uid 会员id
     * @param $type 优惠券类型
     * @param $status 优惠券使用状态
     * @return array
     * @author Ruesin
     */
    function getUserCouponList($uid = 0,$status = -1,$claim = 1,$limit='',$type=''){
        $cpns = array();
        $conditons =" 1 = 1 ";

        if($status != -1){
            $conditons .= " AND cpnsn.status = {$status} ";
        }
        
        if($type != ''){
            $conditons .= " AND cpnsn.type = '{$type}' ";
        }

        $conditons .=" AND cpnsn.uid = {$uid}  AND cpnsn.claim = '{$claim}' ";
    
        $cpns = $this->_couponsn_mod->findAll(array(
            'limit' =>$limit,
            'fields'=>'cpnsn.*,cpn.*',
            'join' =>"has_coupon",
            'conditions' => $conditons,
        ));
        if(!empty($cpns)){
        	foreach ($cpns as $key => $row){
        		if($row['cpn_id'] == '' && $row['type'] == 'exp'  ){
        			$cpns[$key]['cpn_name']  = '体验券可抵消部分商品金额!';
        			$cpns[$key]['cpn_money'] = 0;
        		}
        	}
        }
        return $cpns;
    }
    
    /**
     * 用户使用一个优惠卷
     * @param $uid 会员id
     * @param $coupon_sn 优惠券编码
     * @return boolean
     * @author Ruesin
     */
    function useCoupon($uid,$coupon_sn){
        $data = array(
            'use_time'=>time(),
            'status'=>1,
        );
        return $this->_couponsn_mod->edit(" coupon_sn = '$coupon_sn' AND uid = '$uid' ",$data);
    }

    function getCoupon($uid = 0, $sn = '', $amount = 0 , $gid = ''){
//         $uid   = 185;
//         $sn    = '201407250251113997,201407250251117018';
//         $amout = 3000;
//         $gid   = '15,16,17,19';
        //==============================

        $time = time();
        $mCpn =&m('couponsn');
        $mCst =&m('customs');
        
        $return['data'] = array();
        $return['msg']  = '';
        
        $snsn = explode(',', $sn);
        
        //勾选体验券时  连表查询 无法使用,  直接报 未匹配到优惠券
        
        $snArr = $mCpn->findAll(array(
            'fields'=>'cpnsn.*,cpn.*',
            'join' =>"has_coupon",
            'conditions' => db_create_in($snsn,'cpnsn.coupon_sn')." AND cpnsn.uid = {$uid}", //AND cpnsn.cpn_id = cpn.cpn_id
        ));
        
        if($sn == '')return $return;
        
        if(empty($snArr)) {
            
        	$return['msg'] = '未匹配到优惠券!';
        	return $return;
        	
        }
        
        $gdArr = $mCst->findAll(array(
            'conditions' => " cst_id in ({$gid}) AND cst_exp = '1'",
            'order'      => ' cst_price ASC ',
        ));
        foreach ($snArr as $row){
            
            if($row['status'] != 0){
                $msg  .= " {$row['cpn_name']}({$row['coupon_sn']}) 无效,";
                continue;
            }
            
            if($row['type'] == 'exp'){

                if(!empty($gdArr)){
                    
                    $expArr = array_shift($gdArr);
                    
                    $return['data'][$row['coupon_sn']]['money'] = $expArr['cst_price'];
                    $return['data'][$row['coupon_sn']]['name']  = '体验券可抵消部分商品金额!';
                    $return['data'][$row['coupon_sn']]['sn']    = $row['coupon_sn'];
                    
                    continue;
                    
                }else{
                    
                    $msg  .= "没有参加体验活动的商品 , {$row['cpn_name']}({$row['coupon_sn']}) 无效,";
                    continue;
                    
                }
                
            }else{
                
                if($time < $row['start_time'] || $time > $row['end_time']){
                    $msg  .= " {$row['cpn_name']}({$row['coupon_sn']}) 不在使用时间内,";
                    continue;
                }
                
                
                
            }
            
            $return['data'][$row['coupon_sn']]['money'] = $row['cpn_money'];
            $return['data'][$row['coupon_sn']]['name']  = $row['cpn_name'];
            $return['data'][$row['coupon_sn']]['sn']    = $row['coupon_sn'];
            
            
        }
        
        $return['msg'] = $msg;
        return $return;
    }

    
}