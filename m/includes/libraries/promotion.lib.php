<?php
/**
 *  促销接口
 *	@author Ruesin
 */

if (!defined('IN_ECM')){
    die('Hacking attempt');
}

class Promotion extends Object{	
    var $_coupon_face;
    function __construct(){
        $this->Promotion();
    }

    function Promotion(){
        $this->_coupon_face =& f('coupon');
    }
    /**
     * 新注册会员送优惠
     * @param  $uid 会员id
     * @return boolean
     * @author Ruesin
     */
    function newMember($uid=0){
        if($uid==0)return false;
        $data=getConf('promotion','register');
        $time=time();
        if(!empty($data)){
            if($data['active']=='yes'){
                if($time>=$data['start_time'] && $time<=$data['end_time']){
                    if($data['numbers'] >= 1){
                        
                        $data['numbers']=$data['numbers']-1;
                        setConf('promotion','register',$data);
                        switch ($data['key']){
                        	case 'point' :
                        	    return setpoint($uid,$data['point']['value'],'add','register',$uid,'新会员送积分!','PC');
                        	    break;
                        	case 'coupon' :
                        	    return $this->_coupon_face->addCoupon($data['coupon']['value'],$uid,'新会员送优惠券!');
                        	    break;
                        	case 'exp' :
                        	    return  $this->_coupon_face->addCoupon(0,$uid,'新会员送体验券!','exp');
                        	    break;
                        	default:
                        	    return false;
                        }
                    }
                }
            }
        }
        return false;
    }
    /**
     * 订单活动
     * @param  
     * @return 
     * @author Ruesin
     */
    function orderPromotion($uid=0,$amount=0){
    	$sendwash   = $this -> sendWash($uid,$amount);
    	if($sendwash){
    		$return['sendwash'] = $sendwash;
    	}
    	$firstorder = $this -> firstOrder($uid,$amount);
    	if($firstorder){
    	    $return['firstwash'] = $firstorder;
    	}
    	return $return;
    }
    /**
     * 真实赠送
     * @param  
     * @return 
     * @author Ruesin
     */
    
    function realSend($orderData = array()){
//         $orderData = array(
//         	0 => array (
//             	    'order_sn'  => '123',
//                     'uid'       => '123',
//                     'msg'       => 'sdf',
//                     'sendtype'  => 'sdf',
//         	    )
//         );
//         $sendTypeArr = array('wash','cpn','exp');

        foreach ($orderData as $row){
            
            if(method_exists($this, $row['sendtype'])){
                $this->$row['sendtype']($row);
            }
        }

    } 
    
    /**
     * 真实赠送干洗
     * @param $orderData array
     * @return bool
     * @author Ruesin
     */
    
    function realSendWash($data = array()){
        
        if(empty($data)) return 0;
        
        $mWash   =& m('promotionwash');
        
        $uid      = $data['uid'];
        $order_sn = $data['order_sn'];
        $msg      = $data['msg'];
        
        
        return $mWash->addWash($uid,$order_sn,$msg); 

    }
    
    
    /**
     * 下单送干洗
     * @param  $uid 会员ID
     * @return boolean
     * @author Ruesin
     */
    function sendWash($uid=0,$amount=0){
        $gift = array(
        	'gift'     => '1',
            'discount' => '1',
            'msg'      => '下单送干洗!',
            'type'     => 'order',
            'money'    => 0,
            'sendtype' => 'realSendWash'
        );
        if( $uid == 0)return 0;
        $mWash =& m('promotionwash');
        $data=getConf('promotion','send');
        $time=time();
        if(!empty($data)){
            if($data['active']=='yes'){
                if($data['numbers'] >= 1){
                    
                    if($time>=$data['start_time'] && $time<=$data['end_time']){
                        
                        $data['numbers']=$data['numbers']-1;
                        setConf('promotion','send',$data);
                        
                        if($data['key']=='first'){
                            if($this->isFirstOrder($uid)){
                                //$id = $mWash->addWash($uid,$order_sn,'首次下单送干洗!');
                                //if($id){
                                	$gift['msg'] = '首次下单送干洗!';
                                	return $gift;
                                //}
                            }
                        }
                        
                        if($data['key']=='amount'){
                            if($amount>=$data['amount']['value']){
                                //$id = $mWash->addWash($uid,$order_sn,'订单满'.$data['amount']['value'].'元送干洗!');
                                //if($id){
                                    $gift['msg'] = '订单满'.$data['amount']['value'].'元送干洗!';
                                    return $gift;
                                //}
                            }
                        }
                    }
                } 
            }
        }
        return 0;
    }
    /**
     * 定制初体验
     * @param  $uid 会员ID
     * @return number
     * @author Ruesin
     */
    function firstOrder($uid=0,$amount = 0){
        if($uid==0)return false;
        $first = array(
            'gift'     => '0',
            'discount' => 1,
            'msg'      => '定制初体验，订单打折!',
            'type'     => 'order',
            'money'    => $amount,
            'sendtype' => ''
        );
        if(!$this->isFirstOrder($uid))return false;
        $data=getConf('promotion','first');
        $time=time();
        if(!empty($data)){
            if($data['active']=='yes'){
                if($data['numbers'] >= 1){
                    
                    if($time>=$data['start_time'] && $time<=$data['end_time']){
                        
                        $data['numbers']=$data['numbers']-1;
                        setConf('promotion','first',$data);
                        
                        $discount = $data['discount']['value'];
                        $first['discount'] = (float)$discount;
                        $first['money']    = (float)$discount/10 * $amount;
                        $first['msg']      = '定制初体验，订单打'.$discount.'折!';
                        return $first;
                        
//                         if($data['key']=='free'){
// //                             return 0;
//                             $first['discount'] = 0;
//                             $first['msg']      = '定制初体验,免单!';
//                             $first['money']    = 0;
//                             return $first;
//                         }
//                         if($data['key']=='half'){
// //                             return 0.5;
                            
//                             $first['discount'] = 0.5;
//                             $first['msg']      = '定制初体验,半价!';
//                             $first['money']    = $amount * 0.5;
//                             return $first;
                            
//                         }
                    }
                }
                
            }
        }
        return false;
    }

    /**
     * 判断是否为首次下单
     * @param  $uid 会员ID
     * @return boolean
     * @author Ruesin
     */
    function isFirstOrder($uid=0){
        if($uid==0)return false;
        $mOrder=& m('order');
        $arr=$mOrder->find(array(
            'conditions' => "buyer_id={$uid}",
        ));
        if(count($arr)<1){
            return true;
        }
        return false;
    }

    
}
