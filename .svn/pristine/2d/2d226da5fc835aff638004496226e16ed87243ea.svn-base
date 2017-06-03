<?php

/* 订单 order */
class OrderModel extends BaseModel
{
    var $table  = 'order';
    var $alias  = 'order_alias';
    var $prikey = 'order_id';
    var $_name  = 'order';
    var $_relation  = array(
        // 一个订单有一个实物商品订单扩展
//         'has_orderextm' => array(
//             'model'         => 'orderextm',
//             'type'          => HAS_ONE,
//             'foreign_key'   => 'order_id',
//             'dependent'     => true
//         ),
        // 一个订单有多个订单商品
        'has_ordergoods' => array(
            'model'         => 'ordergoods',
            'type'          => HAS_MANY,
            'foreign_key'   => 'order_id',
            'dependent'     => true
        ),
    		'has_members' => array(
    				'model' => 'member',
    				'type' => HAS_ONE,
    				'foreign_key' => 'user_id',
    				'refer_key' => 'user_id',
    				'dependent'     => true
    		),
    		
            // 一个订单有多个订单套装商品
        'has_suitgoods' => array(
            'model'         => 'ordersuit',
            'type'          => HAS_MANY,
            'foreign_key'   => 'order_id',
            'dependent'     => true
        ),
        
        // 一个订单有多个订单商品
        'print_ordergoods' => array(
            'model'         => 'ordergoods',
            'type'          => HAS_ONE,
            'foreign_key'   => 'order_id',
            'dependent'     => true
        ),
        
        // 一个订单有多个订单日志
//        'has_orderlog' => array(
//            'model'         => 'orderlog',
//            'type'          => HAS_MANY,
//            'foreign_key'   => 'order_id',
//            'dependent'     => true
//        ),
        'belongs_to_store'  => array(
            'type'          => BELONGS_TO,
            'reverse'       => 'has_order',
            'model'         => 'store',
        ),
        'belongs_to_user'  => array(
            'type'          => BELONGS_TO,
            'reverse'       => 'has_order',
            'model'         => 'member',
        ),
    	

        //ns up 删除2个不需要的关联
        //一个订单可以先择一个服务点m
        // 'has_figurem' => array(
        //     'model'         => 'figure_orderm',
        //     'type'          => HAS_ONE,
        //     'foreign_key'   => 'order_id',
        //     'dependent'     => true
        // ),
        
        //一个订单可以先择一个服务点
        // 'has_orderfigure' => array(
        //     'model'         => 'orderfigure',
        //     'type'          => HAS_ONE,
        //     'foreign_key'   => 'order_id',
        //     'dependent'     => true
        // ),
        
    );
    
    /**
     *状态
     *@author  liang.li
     */
    function getStatus()
    {
        $arr[0] = "已取消" ;
        $arr[11] = "待付款" ;
        $arr[20] = "已支付" ;
        $arr[30] = "已发货" ;
        $arr[40] = "已完成" ;
        $arr[61] = "备货中" ;
        return $arr;
    }
    
    
    /**
     * 会员点击已完成，更改订单及需求状态
     * 
     * @param $sn 订单sn
     * @version 1.0.0 (Jan 4, 2015)
     * @author Ruesin
     */
    function _order_finish($sn = ''){
        if(!$sn)return false;
        $order = $this->get("order_sn = '{$sn}'");
        if($order['source'] == 2)return false;
        
        $transaction = $this->beginTransaction();
        
        $res = $this->edit("order_sn = '{$sn}' AND status = '".STORE_SHIPPED."' ", array('status'=>ORDER_FINISHED));
        if(!$res)return false;
        $dmd = &m('demand');
        $res = $dmd->edit("md_id = '{$order['source_id']}'",array('status'=>4));
        if(!$res)return false;
        
        $this->commit($transaction);
        return true;
    }
    
    /**
     * 事务 发货单保存
     * @param $data
     * @return boolean
     */
    function ondeliver($data,$admin){
        
//        if(!$data['logi_no'] || !$data['order_id'] || empty($data['post_fee']))return false;
        if(!$data['logi_no'] || !$data['order_id'] )return false;

        $order = $this->get("order_id = '{$data['order_id']}'");

        if(!$order)return false;

        //只有 已支付 或是 未发货状态下 才可以发货
        if (!in_array($order['status'], [ORDER_ACCEPTED,ORDER_STOCKING])){
            return false;
        }

        $transaction = $this->beginTransaction();
        
        //发货单数据
        $_data['tid'] = $order['order_sn'];
        $_data['user_id'] = $order['user_id'];
        $_data['post_fee'] = $data['post_fee'];
        $_data['logi_no'] = $data['logi_no'];
        $_data['logi_id'] = $data['deliver_id'];
        $_data['logi_name'] = $data['deliver_name'];
        $_data['area_ids'] = $order['ship_area_id'];
        $_data['area_names'] = $order['ship_area'];
        $_data['receiver_name'] = $data['ship_name'];
        $_data['receiver_city'] = $data['region_id_0'];
        $_data['receiver_district'] = $data['region_id_1'];
        $_data['receiver_state'] = $data['region_id_2'];
        $_data['receiver_address'] = $data['ship_addr'];
        $_data['receiver_zip'] = $data['ship_zip'];
        $_data['receiver_mobile'] = $data['ship_mobile'];
        $_data['receiver_phone'] = $data['ship_tel'];
        $_data['t_begin'] = time();
        $_data['memo'] = $data['memo'];
        $_data['op_name']  = $admin;
        
        $res = $this->edit("order_id = '{$data['order_id']}' AND (status = '".ORDER_ACCEPTED."' or status = '".ORDER_STOCKING."')", array('status'=>ORDER_SHIPPED));
        if(!$res)return false;
        $dr = &m('delivery');
        $res = $dr->add($_data);
        if(!$res)return false;
        
        $this->commit($transaction);
        return $res;
    }
    
    /**
    *@author liang.li <1184820705@qq.com>
    *@2015年5月27日
    */
    function submit($order_id,$admin_id) 
    {
        
        $return['code'] = 0;
        $return['msg'] = "";
        $info = $this->get_info($order_id);
        if (!$info) 
        {
            $return['msg'] = "此订单不存在";
            return $return;
        }
         if ($info['is_back_money'] == 1) //=====  此订单已经返现完成  =====
        {
            $return['code'] = 1;
            return $return;
        } 
        $user_id = $info['user_id'];
      
        //$res = $this->backOrder($order_id, $user_id, $admin_id); //=====  订单完成给自己返现  一期暂时屏蔽 如以后要启用此功能 请先在setting里面加上响应的配置   =====
        
       //*******************订单返现或积分给邀请人********************  
       if(!$this->backInviter($info, $admin_id))
       {
           $return['msg'] = "给邀请人返现或者返积分失败！请联系管理员";
           return $return;
       }
        
        //************* 订单返积分（给自己） ****************
        if(!$this->backSelfPoint($info,$admin_id))
        {
            $return['msg'] = "给自己返积分失败！请联系管理员";
            return $return;
        }
       
        //************* 订单满额送抵用券（给自己） ****************
        if(!$this->backSelfDebit($info,$admin_id))
        {
            $return['msg'] = "给自己返积分失败！请联系管理员";
            return $return;
        }
        //************* 给量体派工量体师添加量体费  ****************
        if(!$this->backFigureDebit($info,$admin_id))
        {
            $return['msg'] = "给自己返积分失败！请联系管理员";
            return $return;
        }
       
       
        if(!$this->edit($order_id,array('is_back_money'=>1))) //=====  修改订单回执码 下次再修改订单状态 任何积分和返现都不再操作  =====
        {
            $return['msg'] = "修改订单回执码错误！请联系管理员";
            return $return;
        }
        
        $return['code'] = 1;
        return $return;
        
    }
    
    
    /**
    *订单返现或积分给邀请人
    *@author liang.li <1184820705@qq.com>
    *@2015年6月16日
    */
    function backInviter($order_info,$admin_id) 
    {
        //*******************订单返现或积分给邀请人********************
        $config = $this->getCronAll();
        $user_id = $order_info['user_id'];
        $order_id = $order_info['order_id'];
        $member_invite_mod = m('memberinvite');
        $m_mod = m('member');
        $order_cash_mod = m('ordercashlog');
        $invite_info = $member_invite_mod->get("invitee = $user_id AND type=0 ");
        if (!$invite_info['inviter'])
        {
            return true;
        }
        $inviter_id = $invite_info['inviter'];
        
        $order_list = $this->find(array(
                'conditions' => "user_id = $user_id AND status != 0",
                'order' => "order_id asc",
        ));
        $a = 0;
        $share = 0;
        $share_point = 0;
        $cut = $config['cut'];
// echo array_keys($config['cutp'])[0];exit;
        foreach ($order_list as $key => $value)
        {
            $a = $a + 1;
            if ($cut[$a] && ($key == $order_id))//=====   当前订单  是第a次订单   取得第a次的返现比例  =====
            {
               $share = $cut[$a];
                break;
            }

            $cutp = array_keys($config['cutp']);
            $val = $cutp[0];
            if ( ($a >= $val) && ($key == $order_id))
            {
                $share_point = $config['cutp'][$val];
                break;
            }

        }
//   echo $share_point;exit;
        if ($share)
        {
            $price = $order_info['final_amount'] + $order_info['money_amount'];
            $money = $price * ($share/100);
            $_data['order_id'] = $order_id;
            $_data['user_id'] = $inviter_id; //=====      给邀请人加积分或者返现  =====
            $_data['share'] = $share;
            $_data['cash_money'] = $money;
            $_data['add_time'] = time();
            $_data['order_money'] = $price;
            $_data['cate'] = 1;
            $_data['admin_id']  = $admin_id;
            if (!$order_cash_mod->add($_data))
            {
               return false;
            }
            $res = $m_mod->setInc($inviter_id,'money',$money);
            if (!$res)
            {
                return false;
            }
        }

        if ($share_point)
        {
            $price = $order_info['final_amount'] + $order_info['money_amount'];
            $point = $price * ($share_point/100);
            $_data['order_id'] = $order_id;
            $_data['user_id'] = $inviter_id; //=====      给邀请人加积分或者返现  =====
            $_data['share'] = $share_point;
            $_data['cash_money'] = $point;
            $_data['add_time'] = time();
            $_data['order_money'] = $final_amount;
            $_data['cate'] = 1;
            $_data['is_point'] = 1;
            $_data['admin_id']  = $admin_id;
            if (!$order_cash_mod->add($_data))
            {
                return false;
            }
            $res = $m_mod->setInc($inviter_id,'point',$point);
            
            //=====  增加积分从新规划会员的等级  =====
            reloadMember($inviter_id);
            if (!$res)
            {
                return false;
            }
        }
        return true;
    }
    
    /**
    *订单返积分和余额给邀请人
    *@author liang.li <1184820705@qq.com>
    *@2015年6月8日
    */
    function backInviter_bak($order_info,$admin_id) 
    {
//    echo 22;exit;
        $user_id = $order_info['user_id'];
        $order_id = $order_info['order_id'];
        $member_invite_mod = m('memberinvite');
        $invite_info = $member_invite_mod->get("invitee = $user_id");
// dump($user_id);
        if (!$invite_info['inviter'])
        {
            return true;
        }
        $inviter_id = $invite_info['inviter'];
        $order_status = ORDER_CANCELED;
        $order_list = $this->find(array(
            'conditions' => "user_id = $user_id AND status != $order_status ",
            'order' => "order_id asc",
            'limit' => 4,
        ));
// dump($order_list);        
        //=====   订单返现分为第一笔 第二笔 第三笔 第四笔和第四笔以后  所以这里要首先确定此订单是第几笔订单  =====
        $step = 0;
        if ($order_list) 
        {
            foreach ($order_list as $key => $value) 
            {
                $step = $step + 1;
                if ($order_id == $key) 
                {
                    break;
                }
            }
        }
// echo $step;exit;        
        if ($step == 1) 
        {
            $res = $this->backInviterOne($order_info,$inviter_id, $admin_id);
        }
        elseif ($step == 2 || $step == 3)
        {
            $res = $this->backInviterTOT($order_info,$inviter_id,$step, $admin_id);
        }
        else 
        {
            $res = $this->backInviterF($order_info,$inviter_id, $admin_id);
        }
        
        
        return true;
    }
    
    /**
    *第一笔订单调用此方法
    *@author liang.li <1184820705@qq.com>
    *@2015年6月8日
    */
    function backInviterOne($order_info,$inviter_id,$admin_id) 
    {
        $chen_limit = array('0006','0016');//=====   衬衣id  =====
        $order_id = $order_info['order_id'];
        $model_setting = &af('settings');
        $order_cash_mod = m('ordercashlog');
        $m_mod = m('member');
        
        $setting = $model_setting->getAll(); // 载入系统设置数据
        
        $o_chenyi1 = $setting['o_chenyi1'];
        $o_1 = $setting['o_1'];
        
       //=====  获取此订单的所有商品信息  ===== 
       $order_goods_mod = m('ordergoods');
       $order_suit_mod = m('ordersuit');
       
       $order_goods_list = $order_goods_mod->find(array(
           'conditions' => "order_id = $order_id",
       ));
       $order_suit_list = $order_suit_mod->find(array(
           'conditions' => "order_id = $order_id",
           'index_key' => "goods_id",
       ));
       
       //=====  格式化order_goods_list  =====
       $oder_goods_suit = array();
       if ($order_goods_list) 
       {
           foreach ($order_goods_list as $key => $value) 
           {
               $order_goods_suit[$value['suit_id']][] = $value ;
           }
       }
       
       
       
       $is_chen = 0;
       $price = 0;//=====  记录除衬衣以外的其他商品的总价  =====
       $chen_price = 0;
       $chen_amout = 0;
       if ($order_goods_list) 
       {
           foreach ($order_goods_list as $key => $value) 
           {
               if (in_array($value['cloth'], $chen_limit)) 
               {
                   if ($value['suit_id'] == 0) //=====   说明是diy的衬衣   =====
                   {
                       $chen_amout = $chen_amout + $value['subtotal'];
                   }
                   else //=====  套装里面的衬衣 要关联order_goods_suit表来处理  =====
                   {
                       if (!(count($order_goods_suit[$value['suit_id']]) > 1) ) //=====  如果套装里面不止一个衬衣 那么这个订单将不减套装里面衬衣的价格 如果只有一个衬衣则订单要减去套装的价格 ===== 
                       {
                           $chen_amout = $chen_amout + $order_suit_list[$value['suit_id']]['subtotal'];
                       }
                   }
                   
                   $is_chen = 1;
                   $chen_price = $chen_price  + ($o_chenyi1 * $value['quantity']); //=====  每买一个衬衣这个价钱就要加一次  =====
                   continue;
               }
           }
       }
       
//   dump($o_1);    
       $price = $order_info['final_amount'] + $order_info['money_amount'] - $chen_amout; //=====  排除单个衬衣的钱  =====
       
       $price = $price * ($o_1/100);
       if ($is_chen) 
       {
           $price = $price + $chen_price;
       }
       
       $_data['order_id'] = $order_id;
       $_data['order_sn'] = $order_info['order_sn'];
       $_data['user_id'] = $inviter_id;
       $_data['share'] = $o_1;
       $_data['cash_money'] = $price;
       $_data['add_time'] = time();
       $_data['order_money'] = $order_info['goods_amount'];
       $_data['admin_id'] = $admin_id;
       if (!$order_cash_mod->add($_data))
       {
           return false;
       }
       
       $res = $m_mod->setInc($inviter_id,'money',$price); //=====   加余额  =====
       if ($res === false)
       {
           return false;
       }
       
       return true;
       
    }
    
    /**
     *第二笔和第三笔订单调用此方法
     *@author liang.li <1184820705@qq.com>
     *@2015年6月8日
     */
    function backInviterTOT($order_info,$inviter_id,$step, $admin_id)
    {
        $model_setting = &af('settings');
        $order_cash_mod = m('ordercashlog');
        $m_mod = m('member');
        
        $setting = $model_setting->getAll(); // 载入系统设置数据
        $order_id = $order_info['order_id'];
        
        if ($step == 2) 
        {
            $o = $setting['o_2'];
        }
        else 
        {
            $o = $setting['o_3'];
        }
        
        $price = 0;//=====  商品的总价  =====
        $price = $order_info['final_amount'] + $order_info['money_amount'];
        $price = $price * ($o/100);
// dump($price);        
        $_data['order_id'] = $order_id;
        $_data['order_sn'] = $order_info['order_sn'];
        $_data['user_id'] = $inviter_id;
        $_data['share'] = $o;
        $_data['cash_money'] = $price;
        $_data['add_time'] = time();
        $_data['order_money'] = $order_info['goods_amount'];
        $_data['admin_id'] = $admin_id;
        if (!$order_cash_mod->add($_data)) 
        {
            return false;
        }
        
        $res = $m_mod->setInc($inviter_id,'money',$price); //=====   加余额  =====
        if ($res === false)
        {
            return false;
        }
        return true;
    }
    
    /**
     *第四笔及以后订单调用此方法
     *@author liang.li <1184820705@qq.com>
     *@2015年6月8日
     */
    function backInviterF($order_info,$inviter_id,$admin_id)
    {
        $model_setting = &af('settings');
        $order_cash_mod = m('ordercashlog');
        $m_mod = m('member');
        
        $setting = $model_setting->getAll(); // 载入系统设置数据
        $order_id = $order_info['order_id'];
        $o = $setting['o_4'];
        
        $point = $order_info['point_g'] * ($o/100);
        $_data['order_id'] = $order_id;
        $_data['order_sn'] = $order_info['order_sn'];
        $_data['user_id'] = $inviter_id;
        $_data['share'] = $o;
        $_data['cash_money'] = $price;
        $_data['add_time'] = time();
        $_data['is_point'] = 1;
        $_data['order_money'] = $order_info['goods_amount'];
        $_data['admin_id'] = $admin_id;
        if (!$order_cash_mod->add($_data))
        {
            return false;
        }
        
        $res = $m_mod->setInc($inviter_id,'point',$point); //=====   加积分  =====
        if ($res === false)
        {
            return false;
        }
        return true;
    }
    
    
    /**
     *订单返积分(给自己 1:1返回)
     *@author liang.li <1184820705@qq.com>
     *@2015年6月8日
     */
    function backSelfPoint($order_info,$admin_id)
    {
        $m_mod = m('member');
        $order_cash_mod = m('ordercashlog');
        $user_id = $order_info['user_id'];
        $m_info = $m_mod->get_info($user_id);
        if (!$m_info || $m_info['member_lv_id'] == 1) 
        {
            return true;
        }
        #TODD 要判断当前用户是否是创业者(申请成为创业者并且通过审核)
        
        $_data['order_id'] = $order_id;
        $_data['order_sn'] = $order_info['order_sn'];
        $_data['user_id'] = $user_id;
        $_data['share'] = 100;
        $_data['cash_money'] = $order_info['point_g'];
        $_data['add_time'] = time();
        $_data['is_point'] = 1;
        $_data['order_money'] = $order_info['goods_amount'];
        $_data['admin_id'] = $admin_id;
        if (!$order_cash_mod->add($_data))
        {
            return false;
        }
        
        
        $res = $m_mod->setInc($user_id,'point',$order_info['point_g']); //=====   加积分  =====
        reloadMember($user_id);
        
        if ($res === false)
        {
            return false;
        }
        return true;
        
    }
    
    /**
    *订单满额送抵用券
    *@author liang.li <1184820705@qq.com>
    *@2015年6月9日
    */
    function backSelfDebit($order_info,$admin_id) 
    {
        $model_setting = &af('settings');
        $debit_mod = m('debit');
        $debit_order_o = $setting['debit_order_o'];
        
        $money = $order_info['final_amount'] + $order_info['money_amount'];
        if ($money < $debit_order_o) 
        {
            return true;
        }
        
        $setting = $model_setting->getAll(); // 载入系统设置数据
        $order_id = $order_info['order_id'];
        $data['money']    =  $setting['debit_num_o'];
        $data['debit_name'] = $setting['debit_name_o'];
        $data['debit_sn'] = $order_info['order_sn'].date("Ymd");
        $data['user_id'] = $order_info['user_id'];
        $data['source'] = "order";
        $data['add_time'] = time();
        $data['cate'] = $setting['debit_type_o'];
        $debit_cate_o = $setting['debit_cate_o'];
        
        $debit_time_o = $setting['debit_time_o'];
        
        if ($debit_cate_o == 1) //=====  指定天数  =====
        {
            $expire_time = time() + $debit_time_o * 24 * 60 * 60;
        }
        else //=====  指定有效日期  ===== 
        {
            $expire_time = $debit_time_o;
        }
        $data['expire_time']  = $expire_time;
        if (!$debit_mod->add($data)) 
        {
            return false;
        }
        return true;
    }
    
    /**
    *量体师增加量体费
    *@author liang.li <1184820705@qq.com>
    *@2015年6月11日
    */
    function backFigureDebit($order_info,$admin_id) 
    {
        $figure_money = 0;
        $figuire_orderm_mod = m('figureorderm');
        $m_mod = m('member');
        $order_cash_mod = m('ordercashlog');
        $user_id = $order_info['user_id'];
        $order_id = $order_info['order_id'];
        
        $info = $figuire_orderm_mod->get("order_id = $order_id");
        if (!$info) 
        {
            return true;
        }
        if ($info['liangti_state'] != 2 ) //=====  量体不是已完成状态  =====
        {
            return true;
        }
        
        $user_id = 0;
        if ($info['liangti_id'] == 0) //=====  此数据是店长录的  ===== 
        {
            $mod = m('serve');
            $serve_info = $mod->get_info($info['server_id']);
            if ($serve_info) 
            {
                $user_id = $serve_info['userid'];
            }
           
        }
        else //=====  此数据是量体师录入  =====
        {
            $user_id = $info['liangti_id'];
        }
        $m_info = $m_mod->get_info($user_id);
        if (!$m_info) 
        {
            return true;
        }
        $_data['order_id'] = $order_info['order_id'];
        $_data['order_sn'] = $order_info['order_sn'];
        $_data['user_id'] = $user_id;
        $_data['share'] = -1;
        $_data['cash_money'] = $figure_money;
        $_data['add_time'] = time();
        $_data['order_money'] = $order_info['goods_amount'];
        $_data['admin_id'] = $admin_id;
        if (!$order_cash_mod->add($_data)) 
        {
            return false;
        }
        
        if ($figuire_orderm_mod->edit($info['id'],array('liangti_state' => 3)) === false)  //=====  修改量体表的状态  =====
        {
            return false;
        }
        
        $res = $m_mod->setInc($user_id,'money',$figure_money); //=====   加积分  =====
        if ($res === false)
        {
            return false;
        }
        
        return true;
    }
    
    
    /**
    *返现日志
    *@author liang.li <1184820705@qq.com>
    *@2015年5月28日
    */
    function  orderCashLog($order_info,$admin_id) 
    {
        $mod = m('ordercashlog');
        $mod->add($_data);
    }
    
    /**
     *获取后台的配置信息
     *@author liang.li <1184820705@qq.com>
     *@2015年5月28日
     */
    function getCronAll()
    {
        $credit_mod = & m('creditscore');
        $list_s = $credit_mod->find(array(
            'conditions' => "class REGEXP '^s_' ",
        ));
    
        $arr_s = array();
        if ($list_s)
        {
            foreach ($list_s as $key => $value)
            {
                $val = explode("_", $value['class']);
                $arr_s[$value['integral']] = $val[1];
            }
            ksort($arr_s);
        }
    
        $list_d = $credit_mod->find(array(
            'conditions' => "class REGEXP '^d_' ",
        ));
        $arr_d = array();
        if ($list_d)
        {
            foreach ($list_d as $key => $value)
            {
                $val = explode("_", $value['class']);
                $arr_d[$value['integral']] = $val[1];
            }
            ksort($arr_d);
        }
    
    
        $list_cut = $credit_mod->find(array(
            'conditions' => "class REGEXP '^cut_' ",
        ));
        $arr_cut = array();
        if ($list_cut)
        {
            foreach ($list_cut as $key => $value)
            {
                $val = explode("_", $value['class']);
                $arr_cut[$val[1]] = $value['integral'];
            }
            ksort($arr_cut);
        }
    
        $list_cutp = $credit_mod->get(array(
            'conditions' => "class REGEXP '^cutp_' ",
        ));
        $arr_cutp = array();
        if ($list_cutp)
        {
            $val = explode("_", $list_cutp['class']);
            $arr_cutp[$val[1]] = $list_cutp['integral'];
        }
    
        //=====    积分  =====
        $list_selfp = $credit_mod->get(array(
            'conditions' => "class = 'selfp' ",
        ));
        $arr_selfp = array();
        if ($list_selfp)
        {
            $arr_selfp[] = $list_selfp['integral'];
        }
    
        $arr['s'] = $arr_s;
        $arr['d'] = $arr_d;
        $arr['cut'] = $arr_cut;
        $arr['cutp'] = $arr_cutp;
        $arr['selfp'] = $arr_selfp;
        return $arr;
    
    }
    
    
    /**
    *给自己返现
    *@author liang.li <1184820705@qq.com>
    *@2015年6月8日
    */
    function backOrder($order_id,$user_id,$admin_id) 
    {
        //=====  给自己返现  =====
        $m_mod = m('member');
        $m_info = $m_mod->get_info($user_id);
        if (!$m_info)
        {
            $return['msg'] =  '此订单对应的创业者不存在';
            return $return;
        }
        
        $way = $m_info['order_way'];
        $config = $this->getCronAll();
        //  dump($config);
        $final_amount = $info['final_amount'];
        //*************订单返现（给自己）**********************
        if ($way == 0) //=====   按单返现  =====
        {
            $share = 0;
            if ($config['s'])
            {
                foreach ($config['s'] as $key => $value)
                {
                    if ($final_amount >= $key)
                    {
                        $share = $value;
                    }
                }
        
            }
            if ($share)
            {
                $money = $final_amount * ($share/100);
        
                $_data['order_id'] = $order_id;
                $_data['user_id'] = $user_id;
                $_data['type'] = $way;
                $_data['share'] = $share;
                $_data['cash_money'] = $money;
                $_data['add_time'] = time();
                $_data['order_money'] = $final_amount;
                $_data['cate'] = 0;
                $_data['admin_id']  = $admin_id;
                $this->orderCashLog($_data);
                $res = $m_mod->setInc($user_id,'money',$money);
                if (!$res)
                {
                    return false;
                }
            }
             
             
        
        }
        else //=====  累加返现  =====
        {
            //=====  计算以往的累加订单  =====
            $amount = $this->get(array(
                'conditions' => "user_id = $user_id AND is_back_money",
                'fields' => "sum(final_amount) as aumount",
                'index_key' => '',
            ));
            //=====  加上本次订单的数目  =====
            $amount = $amount['aumount'] + $final_amount;
            //       dump($amount);
            $share = 0;
            if ($config['d'])
            {
                foreach ($config['d'] as $key => $value)
                {
                    if ($amount >= $key)
                    {
                        $share = $value;
                    }
                }
        
            }
        
            if ($share)
            {
                // echo $amount."__".$share;exit;
                $money = $final_amount * ($share/100);
        
                $_data['order_id'] = $order_id;
                $_data['user_id'] = $user_id;
                $_data['type'] = $way;
                $_data['share'] = $share;
                $_data['cash_money'] = $money;
                $_data['add_time'] = time();
                $_data['order_money'] = $final_amount;
                $_data['cate'] = 0;
                $_data['admin_id']  = $admin_id;
                $this->orderCashLog($_data);
                $res = $m_mod->setInc($user_id,'money',$money);
                if (!$res)
                {
                    return false;
                }
            }
        
             
        }
    }
    
     /**
     *按单返现
     *@author liang.li <1184820705@qq.com>
     *@2015年5月28日
     */
     function singleOrder($order_id) 
     {
         ;
     }
    
     /**
     *累加返现
     *@author liang.li <1184820705@qq.com>
     *@2015年5月28日
     */
     function sumOrder($order_id) 
     {
         ;
     }

    /**
     *    修改订单中商品的库存，可以是减少也可以是加回
     *
     *    @author    Garbin
     *    @param     string $action     [+:加回， -:减少]
     *    @param     int    $order_id   订单ID
     *    @return    bool
     */
    function change_stock($action, $order_id)
    {
        if (!in_array($action, array('+', '-')))
        {
            $this->_error('undefined_action');

            return false;
        }
        if (!$order_id)
        {
            $this->_error('no_such_order');

            return false;
        }

        /* 获取订单商品列表 */
        $model_ordergoods =& m('ordergoods');
        $order_goods = $model_ordergoods->find("order_id={$order_id}");
        if (empty($order_goods))
        {
            $this->_error('goods_empty');

            return false;
        }

        $model_goodsspec =& m('goodsspec');
        $model_goods =& m('goods');

        /* 依次改变库存 */
        foreach ($order_goods as $rec_id => $goods)
        {
            $model_goodsspec->edit($goods['spec_id'], "stock=stock {$action} {$goods['quantity']}");
            $model_goods->clear_cache($goods['goods_id']);
        }

        /* 操作成功 */
        return true;
    }
    
    
    
    /**
     *    获取订单未支付 未收货订单 待评价商品数量
     *
     *    @author    Garbin
     *    @param     int  userid  用户id
     *    @return    bool
     */
 
    
    function getOrderabout($user_id)
    {
         if (!$user_id)
        {
            $this->_error('no_such_userid');

            return false;
        }
        
        $order_mod = &m('order');
        //待支付
        $npaid = $order_mod->get(array(
        	'conditions'=>"user_id = $user_id AND extension != 'fabricbook' AND status =11",
        	'fields'		=>"count(*) as wz_num",
        ));
        //待发货
        $df = $order_mod->get(array(
            'conditions'=>"user_id = $user_id AND extension != 'fabricbook' AND status =20",
            'fields'		=>"count(*) as num",
        ));
        //已发货 
        $nreceipt = $order_mod->get(array(
        	'conditions'=>"user_id = $user_id AND extension != 'fabricbook' AND status = 30",
        	'fields'		=>"count(*) as wsh_num",
        ));
        //已取消
       $conditions = " AND status = 0 ";
       $wpj =  $order_mod->get(array(
          'conditions' =>"user_id = $user_id  AND extension != 'fabricbook' AND status = 0",
          'fields'     => "count(*) as t_wpj",
       	  'count'       => true,
      ));
   
        // 套装的未评价数量
   
        $arr = array('npaid'=>$npaid['wz_num'],'nreceipt'=>$nreceipt['wsh_num'],'num'=>$wpj['t_wpj'],'df'=>$df['num']);
        
        return  $arr;
    }
}

?>
