<?php

/* 订单 order */
class OrderModel extends BaseModel
{

    var $table = 'order';

    var $alias = 'order_alias';

    var $prikey = 'order_id';

    var $_name = 'order';

    var $_relation = array(
        // 一个订单有一个实物商品订单扩展
        'has_orderextm' => array(
            'model' => 'orderextm',
            'type' => HAS_ONE,
            'foreign_key' => 'order_id',
            'dependent' => true
        ),
        // 一个订单有多个订单商品
        'has_ordergoods' => array(
            'model' => 'ordergoods',
            'type' => HAS_MANY,
            'foreign_key' => 'order_id',
            'dependent' => true
        ),
        
        // 一个订单有多个订单商品
        'has_ordersuit' => array(
            'model' => 'ordersuit',
            'type' => HAS_MANY,
            'foreign_key' => 'order_id',
            'dependent' => true
        ),
        // 一个订单有多个订单日志
        'has_orderlog' => array(
            'model' => 'orderlog',
            'type' => HAS_MANY,
            'foreign_key' => 'order_id',
            'dependent' => true
        ),
        'belongs_to_store' => array(
            'type' => BELONGS_TO,
            'reverse' => 'has_order',
            'model' => 'store'
        ),
        'belongs_to_user' => array(
            'type' => BELONGS_TO,
            'reverse' => 'has_order',
            'model' => 'member'
        ),
        
        // 一个订单可以先择一个服务点
        'has_orderfigure' => array(
            'model' => 'orderfigure',
            'type' => HAS_ONE,
            'foreign_key' => 'order_id',
            'dependent' => true
        )
    );

    /**
     * 会员点击已完成，更改订单及需求状态
     * 
     * @param $sn 订单sn            
     * @version 1.0.0 (Jan 4, 2015)
     * @author Ruesin
     */
    function _order_finish($sn = '')
    {
        if (! $sn)
            return false;
        $order = $this->get("order_sn = '{$sn}'");
        if ($order['source'] == 2)
            return false;
        
        $transaction = $this->beginTransaction();
        
        $res = $this->edit("order_sn = '{$sn}' AND status = '" . STORE_SHIPPED . "' ", array(
            'status' => ORDER_FINISHED
        ));
        if (! $res)
            return false;
        $dmd = &m('demand');
        $res = $dmd->edit("md_id = '{$order['source_id']}'", array(
            'status' => 4
        ));
        if (! $res)
            return false;
        
        $this->commit($transaction);
        return true;
    }
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
     *
     * @author liang.li <1184820705@qq.com>
     *         @2015年5月27日
     */
    function submit($order_id)
    {
        $return['code'] = 0;
        $return['msg'] = "";
        $info = $this->get_info($order_id);
        if (!$info) 
        {
            $return['msg'] = "此订单不存在";
            return $return;
        }
        
         if ($info['is_back_money'] == 1) //===== 此订单已经返现完成 =====
         {
             $return['code'] = 1;
             return $return;
         }
         
        $user_id = $info['user_id'];
       
        // *******************订单解放冻结的余额和麦富迪币********************
        $res = $this->backFrozen($info);
        if (!$res['status']) {
            $return['msg'] = $res['msg'];
            return $return;
        }
       
        // *******************订单返现或积分给邀请人********************
        $res = $this->backInviter($info);
        if (!$res['status']) {
            $return['msg'] = $res['msg'];
            return $return;
        }
        
        // ************* 订单返积分（给自己） ****************
        $res = $this->backSelfPoint($info);
        if (!$res['status']) {
            $return['msg'] = $res['msg'];
            return $return;
        }
        
        // ************* 订单满额送抵用券（给自己） ****************
        $res = $this->backSelfDebit($info);
        if (!$res['status']) {
            $return['msg'] = $res['msg'];
            return $return;
        }
        // ************* 给量体派工量体师添加量体费 ****************
        $res = $this->backFigureDebit($info);
        if (!$res['status']) 
        {
            $return['msg'] = $res['msg'];
            return $return;
        } 
        $finished_time = time();
        if (! $this->edit($order_id, array('is_back_money' => 1,'finished_time'=>$finished_time))) // ===== 修改订单回执码 下次再修改订单状态 任何积分和返现都不再操作 =====
        {
            $return['msg'] = "修改订单回执码错误！请联系管理员";
            return $return;
        }
         
        $return['code'] = 1;
        return $return;
    }

    /**
     * 订单返收益或积分给邀请人
     * 
     * @author liang.li <1184820705@qq.com>
     *         @2015年6月16日
     */
    function backInviter($order_info)
    {
        $earr['status'] = 0;
        $tarr['status'] = 1;
        // *******************订单返现或积分给邀请人********************
        $user_id = $order_info['user_id'];
        $order_id = $order_info['order_id'];
        $member_invite_mod = m('memberinvite');
        $member_lv_mod = m('memberlv');
        $m_mod = m('member');
        $order_cash_mod = m('ordercashlog');
        $order_mod = m('order');
        $order_goods_mod = m('ordergoods');
        $order_suit_mod = m('ordersuit');
        $order_debit_mod = m('orderdebit');
        $order_kuka_mod = m('orderkuka');
        //=====  如果此会员已经是创业者则停止给上线返积分和收益  =====
        $user_info = $m_mod->get_info($user_id);
        if ($user_info['member_lv_id'] > 1) 
        {
            return $tarr;
        }
        //=====  当order表的cy_dis_count为-1的时候 说明是老数据 否则走新规则  =====
        if ($order_info['cy_dis_count'] == -1) 
        {
            $invite_info = $member_invite_mod->get("invitee = $user_id AND type=0 ");
            if (! $invite_info['inviter']) {
                return $tarr;
            }
            $inviter_id = $invite_info['inviter'];
            $inviter_info = $m_mod->get_info($inviter_id);//=====  创业者信息  =====
            $lv_id = $inviter_info['member_lv_id'];
            $lv_info = $member_lv_mod->get_info($lv_id);
            $cy_dis_count = $lv_info['cy_dis_count'];
        }
        else 
        {
            $cy_dis_count = $order_info['cy_dis_count'];
        }
       
        if (!$cy_dis_count) 
        {
            return $tarr;
        }
        
        $activity_price = 0;//=====  活动款总的商品价格  =====
        $sale_price = 0;//=====  销售价的总的商品价格  =====
        $order_debit_price = 0;//=====   抵用券的总的价格  =====
        $order_kuka_price = 0;//=====   抵用券的总的价格  =====
        $maiyizengyi_price = 0; //=====  买一赠一的价格  =====
        //=====  diy的活动款  =====
        $order_goods_diy_list = $order_goods_mod->find(array(
            'conditions' => "type = 'diy' AND order_id = $order_id",
            'fields'     => "subtotal,activity",
        ));
        if ($order_goods_diy_list) 
        {
            foreach ($order_goods_diy_list as $key => $value) 
            {
                if ($value['activity'] > 0) 
                {
                    if ($value['activity'] = 1) //=====  买一赠一  =====
                    {
                        if ($value['price'] > $maiyizengyi_price) 
                        {
                            $maiyizengyi_price = $value['price'];
                        }
                    }
                    else 
                    {
                       $activity_price += $value['subtotal'];
                    }
                    
                }
                else 
                {
                    $sale_price += $value['subtotal'];
                }
            }
        }
        
        //=====  suit的活动款  =====
        $order_suit_list = $order_suit_mod->find(array(
            'conditions' => "order_id = $order_id ",
            'fields'     => "subtotal,activity",
        ));
        if ($order_suit_list) 
        {
            foreach ($order_suit_list as $key => $value) 
            {
                if ($value['activity'] > 0)
                {
                    if ($value['activity'] = 1)//=====  买一赠一  =====
                    {
                        if ($value['price'] > $maiyizengyi_price)
                        {
                            $maiyizengyi_price = $value['price'];
                        }
                    }
                    else
                    {
                        $activity_price += $value['subtotal'];
                    }
                }
                else
                {
                    $sale_price += $value['subtotal'];
                }
            }
        }
        
        $activity_price += $maiyizengyi_price;
        
        //=====  酷券的价格  =====
        $order_debit_list = $order_debit_mod->find(array(
            'conditions' => "order_id = $order_id",
            'fields'     => "d_money",
        ));
        if ($order_debit_list) 
        {
            foreach ($order_debit_list as $key => $value) 
            {
                $order_debit_price += $value['d_money'];
            }
        }
        
        //=====  酷卡的价格  =====
        $order_kuka_list = $order_kuka_mod->find(array(
            'conditions' => "order_id = $order_id",
            'fields'     => "k_money",
        ));
        if ($order_kuka_list)
        {
            foreach ($order_kuka_list as $key => $value)
            {
                $order_kuka_price += $value['k_money'];
            }
        }
        
        $sale_price_real = $sale_price - $order_debit_price - $order_kuka_price - $order_info['coin'];
        //=====  非活动款和活动款的总的价格*返收益的百分比  =====
        //$share_price = $sale_price_real * ($cy_dis_count/10) + $activity_price * (PROFIT_h/100);
        $share_price = ($order_info['final_amount'] + $order_info['money_amount'])*($cy_dis_count/10); //=====  15-12-29  收益活动款也算在内  =====
// return $share_price;
        //=====  返收益记录log  =====
        if ($share_price > 0) 
        {
            $data['name'] = '订单返收益';
            $data['order_id'] = $order_id;
            $data['order_sn'] = $order_info['order_sn'];
            $data['user_id'] = $inviter_id;
            $data['add_time'] = gmtime();
            $data['type'] = 0;
            $data['mark'] = "+";
            $data['share'] = $cy_dis_count;
            $data['cash_money']  = $share_price;
            $data['cate'] = 1;
            $data['minus'] = 0;
            $data['order_money'] = $order_info['goods_amount'];
            // echo $share_price;exit;
            if (!$order_cash_mod->add($data))
            {
                $earr['msg'] = "收益入log表失败";
                return $earr;
            }
            
            if (!$m_mod->setInc($inviter_id, 'profit', $share_price))
            {
                $earr['msg'] = "收益入账户失败";
                return $earr;
            }
        } 
        
        //=====  返积分记录log  =====
        if (floor($order_info['point_g'])) 
        {
            $data_p['name'] = '订单返积分';
            $data_p['order_id'] = $order_id;
            $data_p['order_sn'] = $order_info['order_sn'];
            $data_p['user_id'] = $inviter_id;
            $data_p['add_time'] = gmtime();
            $data_p['type'] = 1;
            $data_p['share'] = ORDER_POINT;
            // $data_p['cash_money']  = (int)($order_info['point_g'] * (ORDER_POINT/100));
            $data_p['cash_money']  = floor($order_info['point_g']);
            $data_p['cate'] = 1;
            $data_p['order_money'] = $order_info['goods_amount'];
            if (!$order_cash_mod->add($data_p))
            {
                $earr['msg'] = "积分入log失败";
                return $earr;
            }
            if (!$m_mod->setInc($inviter_id, 'point', floor($order_info['point_g'])))
            {
                $earr['msg'] = "积分入账户失败";
                return $earr;
            }
            
            // ==============加入站内信 STAART========
            // 给邀请人加麦富迪积分后，加入系统通知并app推送
            $order_mem_info = $m_mod->get($user_id);
            $title_mes = '您的拓展积分' . $data_p['cash_money'] . '已到账，请查收！';
            $content_mes = '尊敬的麦富迪用户，您拓展的客户' . $order_mem_info['user_name'] . '的订单('. $order_info['order_sn'] .')已完成，您将获得' . $data_p['cash_money'] . '麦富迪积分，已发放至您的积分账户！您的拓展下单客户越多，拓展积分越多，等级越高，享受的购物折扣越低。请密切关注您的麦富迪积分。';
            
            sendSystem($inviter_id, '1', $title_mes, $content_mes);
            // ==============加入站内信 END========
            
            // ===== 增加积分从新规划会员的等级 =====
            reloadMember($inviter_id);
        }
        
        
       
        return $tarr;
    }
    
    /**
     * 冻结资金解除
     *
     * @author liang.li <1184820705@qq.com>
     * @2015年10月28日
     */
    function backFrozen($order_info)
    {
        $earr['status'] = 0;
        $tarr['status'] = 1;
        $m_mod = m('member');
        $order_cash_mod = m('ordercashlog');
        $user_id = $order_info['user_id'];
        $m_info = $m_mod->get_info($user_id);
        if (! $m_info) 
        {
            return $tarr;
        }
        
        if ($order_info['money_amount'] > 0) //=====  冻结余额  =====
        {
            if ($m_info['frozen'] < $order_info['money_amount']) 
            {
                $earr['msg'] = "异常！账户冻结余额小于订单所使用的余额";
                return $earr;
            }
            
            $res = $m_mod->setDec($user_id, 'frozen', $order_info['money_amount']); // =====  减冻结余额 =====
            if ($res === false) 
            {
                $earr['msg'] = "账户减冻结余额失败";
                return $earr;
            }
        }
        if ($order_info['coin'] > 0) //=====  冻结麦富迪币  =====
        {
            if ($m_info['freezes_coin'] < $order_info['coin'])
            {
                $earr['msg'] = "异常！账户冻结麦富迪币小于订单所使用的麦富迪币";
                return $earr;
            }
            
            $res = $m_mod->setDec($user_id, 'freezes_coin', $order_info['coin']); // =====  减冻结余额 =====
            if ($res === false)
            {
                $earr['msg'] = "账户减冻结麦富迪币失败";
                return $earr;
            }
        }
        return $tarr;
    }

    /**
     * 订单返积分(给自己 1:1返回)
     * 
     * @author liang.li <1184820705@qq.com>
     *         @2015年6月8日
     */
    function backSelfPoint($order_info)
    {
        $earr['status'] = 0;
        $tarr['status'] = 1;
        $m_mod = m('member');
        $order_cash_mod = m('ordercashlog');
        $user_id = $order_info['user_id'];
        $m_info = $m_mod->get_info($user_id);
        if (! $m_info) 
        {
            return $tarr;
        }
        if (!floor($order_info['point_g'])) //=====  0则不执行下面代码  =====
        {
            return $tarr;
        }
        // TODD 要判断当前用户是否是创业者(申请成为创业者并且通过审核)
        $_data['name'] = '订单积分';
        $_data['order_id'] = $order_info['order_id'];
        $_data['order_sn'] = $order_info['order_sn'];
        $_data['user_id'] = $user_id;
        $_data['share'] = ORDER_POINT;
        //$_data['cash_money'] = (int)($order_info['point_g'] * (ORDER_POINT/100));
        $_data['cash_money'] = floor($order_info['point_g']);
        $_data['add_time'] = gmtime();
        $_data['type'] = 1;
        $_data['minus'] = 1;
        $_data['order_money'] = $order_info['goods_amount'];
        if (! $order_cash_mod->add($_data)) 
        {
            $earr['msg'] = "自己返积分入log失败";
            return $earr;
        }
        
        $res = $m_mod->setInc($user_id, 'point', floor($order_info['point_g'])); // ===== 加积分 =====
        
        if ($res === false) 
        {
            $earr['msg'] = "自己返积分入账户失败";
            return $earr;
        }
        
        // ==============加入站内信 STAART========
        // 订单返积分给自己，加入系统通知并app推送
        $title_mes = '您获得一笔订单积分，请查收！';
        $content_mes = '尊敬的麦富迪达人，您的订单' . $order_info['order_sn'] . '已完成，获得' . $_data['cash_money'] . '麦富迪积分，已发放至您的积分账户！您的订单越多，积分越多，等级越高，享受的购物折扣越低。请密切关注您的麦富迪积分。';
        
        sendSystem($user_id, '1', $title_mes, $content_mes);
        // ==============加入站内信 END========
        
        reloadMember($user_id);
        
        return $tarr;
    }

    /**
     * 订单满额送抵用券
     * 
     * @author liang.li <1184820705@qq.com>
     *         @2015年6月9日
     */
    function backSelfDebit($order_info)
    {
        $earr['status'] = 0;
        $tarr['status'] = 1;
        $model_setting = &af('settings');
        $debit_mod = m('debit');
        $debit_order_o = $setting['debit_order_o'];
        
        $money = $order_info['final_amount'] + $order_info['money_amount'];
        if ($money < $debit_order_o) {
            return $tarr;
        }
        
        //=====  抵用券额度是0 不赠送券  =====
        if ($setting['debit_num_o'] <= 0) 
        {
             return $tarr;
        }
        
        $setting = $model_setting->getAll(); // 载入系统设置数据
        $order_id = $order_info['order_id'];
        $data['money'] = $setting['debit_num_o'];
        $data['debit_name'] = $setting['debit_name_o'];
        $data['debit_sn'] = $order_info['order_sn'] . date("Ymd");
        $data['user_id'] = $order_info['user_id'];
        $data['source'] = "order";
        $data['add_time'] = gmtime();
        $data['cate'] = $setting['debit_type_o'];
        $debit_cate_o = $setting['debit_cate_o'];
        
        $debit_time_o = $setting['debit_time_o'];
        
        if ($debit_cate_o == 1) // ===== 指定天数 =====
        {
            $expire_time = gmtime() + $debit_time_o * 24 * 60 * 60;
        } else // ===== 指定有效日期 =====
        {
            $expire_time = $debit_time_o;
        }
        $data['expire_time'] = $expire_time;
        if (! $debit_mod->add($data)) 
        {
            $earr['msg'] = "加入券表失败";
            return $earr;
        }
         return $tarr;
    }

    /**
     * 量体师增加量体费
     * 
     * @author liang.li <1184820705@qq.com>
     *         @2015年6月11日
     */
    function backFigureDebit($order_info)
    {
        $earr['status'] = 0;
        $tarr['status'] = 1;
        $serve_mode_money= array(  //=====  量体方式和费用配置  =====
            '1' => 100, //=====  上门量体  =====
            '2' => 100, //=====  门店  =====
            '3' => 100,//=====  线下采集  =====
            '4' => 0,//=====  后台录入  =====
            '6' => 100,//=====  指派量体师  =====
        );
        
        $figuire_orderm_mod = m('figureorderm');
        $custome_figure_mod = m('customer_figure');
        $order_figure_mod = m('orderfigure');
        $m_mod = m('member');
        $order_cash_mod = m('ordercashlog');
        $order_id = $order_info['order_id'];
        
        //=====  量体方式  =====
        $list = $order_figure_mod->find(array(
           'conditions' => "order_id = $order_id",
        ));
        if (!$list)
        {
            return $tarr;
        }
        $son_sn_add = "";//=====  到店或上门  =====
        $son_sn_collect = "";//=====  采集  =====
        $history_id = "";
        foreach ($list as $key => $value) 
        {
            if ($value['measure'] == 5)//=====  历史量体数据   ===== 
            {
                $son_sn_collect .= $value['son_sn'].",";
                $history_id .= $value['history_id'].",";
            }
             
            if ($value['measure'] != 5 && $value['liangti_state'] == 2) 
            {
                $son_sn_add .= $value['son_sn'].",";
               
            }
        }
        $son_sn_add = trim($son_sn_add,",");
        $son_sn_collect = trim($son_sn_collect,",");
        $history_id = trim($history_id,',');

        //=====  非历史量体数据（到店 上门 指定量体师）  =====
        if ($son_sn_add) 
        {
            $custom_figure_list = $custome_figure_mod->find(array(
                'conditions' => "son_sn IN($son_sn_add)",
            ));
            if ($custom_figure_list) 
            {
                foreach ($custom_figure_list as $key => $info) 
                {
                    $user_id = 0;
                    if ($info['liangti_id'] == 0) // ===== 此数据是店长录的 =====
                    {
                        $mod = m('serve');
                        $serve_info = $mod->get_info($info['id_serve']);
                        if ($serve_info)
                        {
                            $user_id = $serve_info['userid'];
                        }
                    }
                    else // ===== 此数据是量体师录入 =====
                    {
                        $user_id = $info['liangti_id'];
                    }
                    $m_info = $m_mod->get_info($user_id);
                    if (! $m_info)
                    {
                        continue;
                    }
                    $money = $serve_mode_money[$info['service_mode']];
                    if (!$money)
                    {
                        continue;
                    }
                    if ($custome_figure_mod->edit($key, array('liangti_state' => 3,'money'=>$money)) === false) // ===== 修改量体表的状态 =====
                    {
                        $earr['msg'] = "修改customer_figure量体表的状态失败";
                        return $earr;
                    }
                    if ($figuire_orderm_mod->edit("son_sn='{$info['son_sn']}'", array('liangti_state' => 3)) === false) // ===== 修改量体表的状态 =====
                    {
                        $earr['msg'] = "修改figuire_orderm量体表的状态失败";
                        return $earr;
                    }
                    
                    $res = $m_mod->setInc($user_id, 'money', $money); // ===== 加余额 =====
                    if ($res === false)
                    {
                        $earr['msg'] = "量体师或者店长加余额失败";
                        return $earr;
                    }
                    
                }
            }
        }
        //=====  历史量体数据 有可能是线下采集过来的  =====
        if ($history_id) 
        {
            $custom_figure_list = $custome_figure_mod->find(array(
                'conditions' => "figure_sn IN($history_id)",
            ));
            if ($custom_figure_list) 
            {
                foreach ($custom_figure_list as $key => $info) 
                {
                    if ($info['service_mode'] != 3 || $info['liangti_state'] != 0) //=====  必须是线下采集 并且liangti_state为3  =====
                    {
                        continue;
                    }
                    $user_id = 0;
                    if ($info['liangti_id'] == 0) // ===== 此数据是店长录的 =====
                    {
                        $mod = m('serve');
                        $serve_info = $mod->get_info($info['id_serve']);
                        if ($serve_info)
                        {
                            $user_id = $serve_info['userid'];
                        }
                    }
                    else // ===== 此数据是量体师录入 =====
                    {
                        $user_id = $info['liangti_id'];
                    }
      
                    $m_info = $m_mod->get_info($user_id);
                    if (! $m_info)
                    {
                        continue;
                    }
                    $money = $serve_mode_money[$info['service_mode']];
                    if (!$money)
                    {
                        continue;
                    }
                    if ($custome_figure_mod->edit($key, array('liangti_state' => 3,'money'=>$money)) === false) // ===== 修改量体表的状态 =====
                    {
                        $earr['msg'] = "修改customer_figure量体表的状态失败2";
                        return $earr;
                    }
                    $res = $m_mod->setInc($user_id, 'money', $money); // ===== 加余额 =====
                    if ($res === false)
                    {
                       $earr['msg'] = "量体师或者店长加余额失败2";
                        return $earr;
                    }
                }
            }
        }
        return $tarr;
    }
    
    /**
    *
    *@author liang.li <1184820705@qq.com>
    *@2015年10月21日
    */
    function function_name() 
    {
        ;
    }

    /**
     * 获取后台的配置信息
     * 
     * @author liang.li <1184820705@qq.com>
     *         @2015年5月28日
     */
    function getCronAll()
    {
        $credit_mod = & m('creditscore');
        $list_s = $credit_mod->find(array(
            'conditions' => "class REGEXP '^s_' "
        ));
        
        $arr_s = array();
        if ($list_s) {
            foreach ($list_s as $key => $value) {
                $val = explode("_", $value['class']);
                $arr_s[$value['integral']] = $val[1];
            }
            sort($arr_s);
        }
        
        $list_d = $credit_mod->find(array(
            'conditions' => "class REGEXP '^d_' "
        ));
        $arr_d = array();
        if ($list_d) {
            foreach ($list_d as $key => $value) {
                $val = explode("_", $value['class']);
                $arr_d[$value['integral']] = $val[1];
            }
            ksort($arr_d);
        }
        
        $list_cut = $credit_mod->find(array(
            'conditions' => "class REGEXP '^cut_' "
        ));
        $arr_cut = array();
        if ($list_cut) {
            foreach ($list_cut as $key => $value) {
                $val = explode("_", $value['class']);
                $arr_cut[$val[1]] = $value['integral'];
            }
            ksort($arr_cut);
        }
        
        $list_cutp = $credit_mod->get(array(
            'conditions' => "class REGEXP '^cutp_' "
        ));
        $arr_cutp = array();
        if ($list_cutp) {
            $val = explode("_", $list_cutp['class']);
            $arr_cutp[$val[1]] = $list_cutp['integral'];
        }
        
        // ===== 积分 =====
        $list_selfp = $credit_mod->get(array(
            'conditions' => "class = 'selfp' "
        ));
        $arr_selfp = array();
        if ($list_selfp) {
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
     * 给自己返现
     * 
     * @author liang.li <1184820705@qq.com>
     *         @2015年6月8日
     */
    function backOrder($order_id, $user_id, $admin_id)
    {
        // ===== 给自己返现 =====
        $m_mod = m('member');
        $m_info = $m_mod->get_info($user_id);
        if (! $m_info) {
            $return['msg'] = '此订单对应的创业者不存在';
            return $return;
        }
        
        $way = $m_info['order_way'];
        $config = $this->getCronAll();
        // dump($config);
        $final_amount = $info['final_amount'];
        // *************订单返现（给自己）**********************
        if ($way == 0) // ===== 按单返现 =====
{
            $share = 0;
            if ($config['s']) {
                foreach ($config['s'] as $key => $value) {
                    if ($final_amount >= $key) {
                        $share = $value;
                    }
                }
            }
            if ($share) {
                $money = $final_amount * ($share / 100);
                
                $_data['order_id'] = $order_id;
                $_data['user_id'] = $user_id;
                $_data['type'] = $way;
                $_data['share'] = $share;
                $_data['cash_money'] = $money;
                $_data['add_time'] = gmtime();
                $_data['order_money'] = $final_amount;
                $_data['cate'] = 0;
                $_data['admin_id'] = $admin_id;
                $this->orderCashLog($_data);
                $res = $m_mod->setInc($user_id, 'money', $money);
                if (! $res) {
                    return false;
                }
            }
        } else // ===== 累加返现 =====
{
            // ===== 计算以往的累加订单 =====
            $amount = $this->get(array(
                'conditions' => "user_id = $user_id AND is_back_money",
                'fields' => "sum(final_amount) as aumount",
                'index_key' => ''
            ));
            // ===== 加上本次订单的数目 =====
            $amount = $amount['aumount'] + $final_amount;
            // dump($amount);
            $share = 0;
            if ($config['d']) {
                foreach ($config['d'] as $key => $value) {
                    if ($amount >= $key) {
                        $share = $value;
                    }
                }
            }
            
            if ($share) {
                // echo $amount."__".$share;exit;
                $money = $final_amount * ($share / 100);
                
                $_data['order_id'] = $order_id;
                $_data['user_id'] = $user_id;
                $_data['type'] = $way;
                $_data['share'] = $share;
                $_data['cash_money'] = $money;
                $_data['add_time'] = gmtime();
                $_data['order_money'] = $final_amount;
                $_data['cate'] = 0;
                $_data['admin_id'] = $admin_id;
                $this->orderCashLog($_data);
                $res = $m_mod->setInc($user_id, 'money', $money);
                if (! $res) {
                    return false;
                }
            }
        }
    }

    /**
     * 按单返现
     * 
     * @author liang.li <1184820705@qq.com>
     *         @2015年5月28日
     */
    function singleOrder($order_id)
    {
        ;
    }

    /**
     * 累加返现
     * 
     * @author liang.li <1184820705@qq.com>
     *         @2015年5月28日
     */
    function sumOrder($order_id)
    {
        ;
    }

    /**
     * 修改订单中商品的库存，可以是减少也可以是加回
     *
     * @author Garbin
     * @param string $action
     *            [+:加回， -:减少]
     * @param int $order_id
     *            订单ID
     * @return bool
     */
    function change_stock($action, $order_id)
    {
        if (! in_array($action, array(
            '+',
            '-'
        ))) {
            $this->_error('undefined_action');
            
            return false;
        }
        if (! $order_id) {
            $this->_error('no_such_order');
            
            return false;
        }
        
        /* 获取订单商品列表 */
        $model_ordergoods = & m('ordergoods');
        $order_goods = $model_ordergoods->find("order_id={$order_id}");
        if (empty($order_goods)) {
            $this->_error('goods_empty');
            
            return false;
        }
        
        $model_goodsspec = & m('goodsspec');
        $model_goods = & m('goods');
        
        /* 依次改变库存 */
        foreach ($order_goods as $rec_id => $goods) {
            $model_goodsspec->edit($goods['spec_id'], "stock=stock {$action} {$goods['quantity']}");
            $model_goods->clear_cache($goods['goods_id']);
        }
        
        /* 操作成功 */
        return true;
    }
	
	/**
	 * 订单取消的各操作
	 * 
	 * @date 2015-8-6 上午11:17:58
	 * @author Ruesin
	 */
	function cancelById($order_id = 0,$order = array()){
	    	    
	    if(!$order_id) return false;
	    
	    if(!$order){
	        $mOrder = &m("order");
	        $order = $mOrder->get($order_id);
	    }
	    
	    if(!$order) return false;
	    
	    imports("diys.lib");
	    $diys = new Diys();
	    $customs = $diys->_customs();
	    
	    
	    $mOgoods = &m('ordergoods');
	    $goods = $mOgoods->find("order_id = '{$order_id}'");
	    
	    //==== 库存返还
		$stc = array(); 
	    foreach ($goods as $row){
	        $stc[$row['fabric']] += $row['quantity']*($customs[$row['cloth']]['fabric_m']);
	    }
	    $mFabirc = &m('fabric');
	    foreach ($stc as $key=>$val){
	        $mFabirc->setInc(" CODE = '{$key}' AND is_sale = '1'",'STOCK',$val);
	    }
	    
	    $mMem = &m('member');
	    $member = $mMem->get($order['user_id']);
	    
	    //==== 余额返还
	    if($order['money_amount'] && intval($order['money_amount']) > 0){
	        
	        $money = $member['money'] + $order['money_amount'];
	        $frozen = $member['frozen'] - $order['money_amount'];
	        if($frozen >= 0){
	            $mMem->edit(" user_id = '{$order['user_id']}' ", array('money'=>$money,'frozen'=>$frozen));
	        }
	        
	        $cashLog[] = array(
	                'name'     => "订单取消返还余额(订单号：{$order['order_sn']})",
	                'order_id' => $order['order_id'],
	                'order_sn' => $order['order_sn'],
	                'user_id'  => $order['user_id'],
	                'minus'    => 3,
	                'cash_money' => $order['money_amount'],
	                'add_time'   => gmtime(),
	                'order_money' => $order['order_amount'],
	                'type'  => 4,
					'mark'  => '+',
	        );
	    }
	    
	    //处理麦富迪币
	    if($order['coin'] > 0){
	    
	        $coin  = $member['coin'] + $order['coin'];
	        $freezes_coin = $member['freezes_coin'] - $order['coin'];
	        if($money >= 0){
	            $mMem->edit(" user_id = '{$order['user_id']}' ", array('coin'=>$coin,'freezes_coin'=>$freezes_coin));
	        }
	        $cashLog[] = array(
	                'name'     => "订单取消返还麦富迪币 - {$order['order_sn']}",
	                'order_id' => $order['order_id'],
	                'order_sn' => $order['order_sn'],
	                'user_id'  => $order['user_id'],
	                'minus'    => 3,
	                'cash_money' => $order['coin'],
	                'add_time'   => gmtime(),
	                'order_money' => $order['order_amount'],
	                'type'  => 2,
					'mark'  => '+',
	        );
	    
	    }
	    
	    if($cashLog){
	        $mCashLog = &m('ordercashlog');
	        $mCashLog->add(addslashes_deep($cashLog));
	    }
	    
	    //==== 抵扣券
	    $mOd = &m('orderdebit');
	    $dbs = $mOd->find("order_id = '{$order_id}'");
	    if ($dbs){
	        foreach ($dbs as $row){
	            $dIds[$row['d_id']] = $row['d_id'];
	        }
	        $mDebit = &m('debit');
	        $mDebit->edit(db_create_in($dIds,'id'), array('is_used'=>'0'));
	    }
	    
	    //== 首单名额
	    $mOf = &m('orderfirstlog');
	    $mOf->edit("order_id = '{$order_id}'", array('is_active'=>'0'));
	    
	    //======== 买一赠一名额
	    if($order['one_num']){
	        $order_mod = &m('order');
	        $order_mod->edit("order_id = '{$order_id}' " , array('one_status'=>'0'));
	    }
	    
	    //=====  liang.li  流推送信息表状态改为3  =====
	    $order_mtm_logs_mod = m('ordermtmlogs');
	    $order_mtm_logs_mod->edit("order_id = '{$order_id}' " ,array('logistics'=>3));
	    
        
	    return true;
	}
	
	
}

?>
