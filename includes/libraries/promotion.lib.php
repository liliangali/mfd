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
//         $this->_coupon_face =& f('coupon');
    }
    //取得所有规则
    //pc段：1  app：2 wap 3
    function getGoodsPromotion(&$item,$platform='1')
    {
        if (!isset($item['goods_id'])) return ;
        if (!$item['user_id']) //===== 未登录 取级别最低的等级  =====
        {
            $memberLvMdl = m("memberlv");
            $memberLvList = $memberLvMdl->get("1=1");
            $member_lv_id = $memberLvList['member_lv_id'];
        }
        else 
        {
           $mMod = m('member');
           $mInfo = $mMod->get_info($item['user_id']);
           $member_lv_id =  $mInfo['member_lv_id'];
        }
        
       
        $mRule = & m('goods_prorule');
        $time = time();
        if($member_lv_id && $platform){
            $res = $mRule->find(array(
                'conditions' => "is_open=1 and find_in_set('".$member_lv_id."',member_lv_id) and find_in_set('".$platform."',site_id) and starttime < ".$time." and endtime > ".$time,
                'order'=>'level desc',
            ));
        }
    
        $defArr = ['4'=>'all','3'=>'category','1'=>'type','2'=>'goods',];
        //优惠条件(1:商品类型2指定商品3商品分类4所有商品)
        if($res){
            foreach ((array)$res as $row)
            {
                if (isset($defArr[$row['favorable']]))
                {
                    if (isset($rules[$defArr[$row['favorable']]]))
                    {
                        foreach ($rules[$defArr[$row['favorable']]] as $val)
                        {
                            if ($val['if_ex'] && 1 == $val['if_ex']) continue 2;
                        }
                    }
            
                    if ($row['if_ex'])
                    {
                        unset($rules[$defArr[$row['favorable']]]);
                    }
                    $rules[$defArr[$row['favorable']]][$row['level']] = $row;
            
                }
            } 
            foreach ($defArr as $k=>$v)
            {
                if (isset($rules[$v]))
                {
                    $key = array_search(max($rules[$v]), $rules[$v]);
                    $item['promotion'] = $rules[$v][$key];
                    $item['promotion']['ty'] = $v;
                    $item['promotion']['tid'] = $k;
                    break;
                }
        }
          
          
            }
            if ($item['promotion']['ty'] !== 'all')
            {
                    if(!$this->isPromotion($item,$item['promotion']))
                    {

                        $item['params']['oProducts']['fprice'] = $item['params']['oProducts']['price'];
                        unset($item['promotion']);
                        return ;
                    }
            }
   
            $data = $this->formatPromotion($item);
            $item['promotion']['yhcase_name'] = $data['name'];
            $item['params']['oProducts']['fprice'] = $item['params']['oProducts']['price'];
            $item['params']['oProducts']['price'] = $data['price'];
            $item['params']['oProducts']['is_pro'] = 1;
    }
    //根据类型判断是否有效
    function isPromotion($item,$info)
    {
        $isP = false;
        
        if ('type' == $info['ty'] || 'category' == $info['ty'])
        {
            //根据goods 获取 分类id 查询
            $where = ' 1 = 1 ';
            if ('type' == $info['ty'])
            {
                $where .= " and favorable_value = '".$item['params']['oGoods']['type_id']."' and favorable_id = '".$info['tid']."' ";
            }else{
                $mCgoods = & m('categorygoods');
                $cgs = $mCgoods->find(array(
                    'conditions' => " goods_id = '".$item['params']['oGoods']['goods_id']."'",
                ));
                if ($cgs)
                {
                    $cids = i_array_column($cgs, 'cate_id');
                    $where .= " and favorable_value in (".implode(',', $cids).") and  favorable_id = '".$info['tid']."' ";
                }
            }
            
            $mProrel = & m('goods_prolink');
            $res = $mProrel->find(array(
                'conditions' => $where,
            ));
            
            if ($res) $isP = true;
            
            if (isset($_GET['v5']) && !empty($_GET['v5']))
            {
                echo $where;
                var_dump($res);
                var_dump($isP);
            }
        }
        
        if ('goods' == $info['ty'])
        {
            $mProrel = & m('goods_prorel');
            $time = time();
            $res = $mProrel->find(array(
                'conditions' => "d_id='".$info['id']."' and c_id = '".$item['params']['oGoods']['goods_id']."'",
            ));
            if ($res) $isP = true;
        }
        return $isP;
    }
    
    //格式化规则
    function formatPromotion($info)
    {
        switch ($info['promotion']['yhcase'])
        {
            case 1:
                $_name = '符合条件的商品以固定折扣出售'.$info['promotion']['yhcase_value']/100;
                $_price = $info['params']['oProducts']['price'] * $info['promotion']['yhcase_value']/100;
                break;
            case 2:
                $_name = '符合条件的商品以固定价格出售'.$info['promotion']['yhcase_value'];
                $_price = $info['promotion']['yhcase_value'];
                break;
            case 3:
                $_name = '符合条件的商品减去固定折扣出售'.$info['params']['oProducts']['price'] * $info['params']['oProducts']['weight']* $info['promotion']['yhcase_value']/100;
                $_price = ($info['params']['oProducts']['price'])-($info['params']['oProducts']['price'] * $info['promotion']['yhcase_value']/100);
                break;
            case 4:
                $_name = '符合条件的商品减去固定价格出售'.$info['promotion']['yhcase_value'];
                $_price = ($info['params']['oProducts']['price'] )-($info['promotion']['yhcase_value']);
                break;
            case 5:
                $_name = '符合条件的商品免邮';
                $_price = ($info['params']['oProducts']['price']);
                break;
        }
        return ['name'=>$_name,'price'=>$_price];
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
