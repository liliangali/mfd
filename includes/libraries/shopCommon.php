<?php
/**
 * 购物公共类
 * 
 * @author ruesin
 */
//require ( "shopBase.php" );
class ShopCommon extends Object//ShopBase
{
    public $_user_id;
    	
    function __construct($user_id = 0)
    {
        $this->ShopCommon($user_id);
    }
    function ShopCommon($user_id = 0)
    {
        $this->_user_id = $user_id ? $user_id : (isset($_SESSION['user_info']['user_id']) ? $_SESSION['user_info']['user_id'] : 0);
    }
    
    public static function user_id(){
        return isset($_SESSION['user_info']['user_id']) ? $_SESSION['user_info']['user_id'] : 0;
    }
    
    
    /**
     * VIP合伙人收取量体费
     *
     * 1.判断用户等级. 创业者直接取登录名; 消费者取关联创业者的登录名; 消费者无关联创业者的直接return;
     * 2.根据创业者查出关联门店的性质.  麦富迪门店直接return; 自营门店继续; 无门店信息return.
     * 3.合并量体信息;  全是标准码(无量体)直接return;量体的看门店id是否为.
     *
     * 5: 现有量体
     *  免费：量体数据来源于与当前登录用户关联的门店下的量体师录入的（即VIP合伙人自己的量体师提交的数据），或者平台量体师录入但第二次使用的；
     *  收费：量体数据来源于麦富迪平台的量体师提供、且第一次使用时付费；
     * 2: 附近门店
     *  免费：VIP合伙人的门店（即：后台门店性质为“自营门店”的门店）；
     *  收费：麦富迪平台的门店（即：后台门店性质为“麦富迪门店”的门店）；
     * 6: 指定量体师
     *  免费：VIP合伙人门店下单独承接量体任务的量体师；
     *  收费：麦富迪平台的单独承接量体任务的量体师；
     *  
     *  
     * 2016-04-23 14:35:04 Bgn
     *  ## VIP合伙人 本身打折就够多了，但是他有可能没有门店，又想收量体费，做了个处理
     *  if (!$server && Members::visitor('member_lv_id') != 9) return ; // VIP 合伙人 没有关联门店 使用 平台的量体资源 麦富迪门店 时收费
     *  VIP合伙人只能看到平台资源，没有关联门店的，给定死个门店ID = 0
     *  $server['idserve'] = isset($server['idserve']) ? $server['idserve'] : 0;
     * 2016-04-23 14:35:18 End
     *
     * @date 2016年1月18日 上午8:41:17
     *
     * @author Ruesin
     */
    public static function figureVip(&$cart,$user){
    
        $cart['figure_fee'] = 0;
    
        //合并量体数据
        self::figureMerge($cart);
    
        if (empty($cart['figure'])) return ;
    
        if ($user['member_lv_id'] > 1){
            $dzName = $user['user_name'];
            $cart['cy_dis_count'] = 0;
        }else{
            $mMemInvite = &m('memberinvite');
            $inviter = $mMemInvite->get(array(
                //'include'  => array(),
                'join'=> 'has_one_to_user',
                'conditions' => "member_invite.invitee = '{$user['user_id']}'",
                'fields'     => 'member.user_name,member.member_lv_id',
            ));
            if (empty($inviter)){
                $cart['cy_dis_count'] = 0;
                return ;
            }
            
            $mLv = &m('memberlv');
            $inviterLv = $mLv->get(" member_lv_id = '{$inviter['member_lv_id']}'");
            $cart['cy_dis_count'] = intval($inviterLv['cy_dis_count']);
            
            $dzName = $inviter['user_name'];
        }
        $mServe = &m('serve');
        $server = $mServe->get("mobile = '{$dzName}' AND storetype = 2");
        
        //if (!$server) return ;
        if (!$server && $user['member_lv_id'] != 9) return ;
        // 没有关联门店
        //使用 平台的量体资源 麦富迪门店 时收费
        $server['idserve'] = isset($server['idserve']) ? $server['idserve'] : 0;
    
        foreach ($cart['figure'] as $row){
            if ($row['type_id'] == 5){
                if ($row['historyInfo']['service_mode'] == 3 && $row['historyInfo']['id_serve'] != $server['idserve'] && $row['historyInfo']['is_first'] == 0 ){
                    $cart['figure_fee'] += 100;
                    $cart['figure_his'][$row['history_id']] = $row['history_id'];
                }
                if ($row['historyInfo']['service_mode'] == 3 && $row['historyInfo']['is_first'] == 0 ){
                    $cart['figure_has'][$row['history_id']] = $row['history_id'];
                }
            }else{
                if ($row['server_id'] != $server['idserve']){
                    $cart['figure_fee'] += 100;
                }
            }
        }
        
    }
    
    /**
     * 去重合并量体方式
     *
     * @date 2016年1月18日 上午8:52:27
     *
     * @author Ruesin
     */
    public static function figureMerge(&$cart){
    
        $sData = $hIds = $cart['figure'] = array();
        
        foreach ($cart['object'] as $key=>$row){
    
            if (!$row['figure']) continue;
    
            $figure = $row['figure'];
    
            if ($figure['type_id'] == 5)
            {
                $figure['customer_mobile'] = isset($figure['customer_mobile']) ? $figure['customer_mobile'] : '';
                $figure['customer_name'] = isset($figure['customer_name']) ? $figure['customer_name'] : '';
            }
            elseif ($figure['type_id'] == 2 || $figure['type_id'] == 6)
            {
                $figure['customer_mobile'] = isset($figure['phone']) ? $figure['phone'] : '';
                $figure['customer_name'] = isset($figure['customer_name']) ? $figure['customer_name'] : (isset($figure['realname']) ? $figure['realname'] : '');
            }
    
            $figure['md5key'] = md5(trim($figure['customer_mobile']).trim($figure['customer_name']));
            $figure['ident'][$key] = $key;
    
            if (!isset($sData[$figure['md5key']])){
                if ($figure['type_id'] == 5){
                    $hIds[$figure['md5key']] = $figure['history_id'];
                }
                $sData[$figure['md5key']] = $figure;
            }else{
                $sData[$figure['md5key']]['ident'][$key] = $key;
            }
        }

        if (empty($sData)) return ;
    
        if (!empty($hIds)){
            
            $mCustomerFigure = &m('customer_figure');
            $history = $mCustomerFigure->find(db_create_in($hIds,'figure_sn'));
            
            foreach ($sData as &$val){
                if ($val['type_id'] == 5){
                    $val['server_id']   = $history[$val['history_id']]['id_serve'];
                    $val['historyInfo'] = $history[$val['history_id']];
                }
            }
        }
    
        $cart['figure']  = $sData;
    }
    
    /**
     * 优惠券
     *
     * @date 2015年12月4日 下午3:23:06
     *
     * @author Ruesin
     */
    public static function debits($acart = array(),$index = 'id'){
        if(empty($acart)) return array();
//       echo '<pre>';print_r($acart);exit;
        $cloths = $acart['cloth_quan'];
        $order_amount = $acart['order_amount'];
//        echo '<pre>';print_r($order_amount);exit;

        foreach ((array)$cloths as $k=>$v)
        {
//            $cloth =  explode(',', $v);
            $cloth[] = $v;
        }
// echo '<pre>';print_r($cloth);exit;
        
//        $mDbt = &m('debit');
        $mDbt = &m('voucher');
        $time = time();
        if (isset($_GET['v5']) && !empty($_GET['v5']))
        {
           echo "优惠券查询：  user_id = '".self::user_id()."' AND is_used = '0' AND is_invalid = '0' AND expire_time >'{$time}' AND (debit_t_id ={$cloth[0]} or cate in (".implode(',',$cloth).")) \r\n\t";
        }
        $str = " binding_user_id = '".self::user_id()."' AND use_status = 0 AND '{$order_amount}' >= order_money  AND end_time >'{$time}' AND (category = '1,2' OR category in (".implode(',',$cloth)."))";
        $debits = $mDbt->find(" binding_user_id = '".self::user_id()."' AND use_status = 0 AND '{$order_amount}' >= order_money  AND end_time >'{$time}' AND (category = '1,2' OR category in (".implode(',',$cloth)."))");
//    echo '<pre>';print_r($str);exit;
     
     
        $count = count($cloth);
        $debit = array();
        foreach ($debits as $dKey=>$dRow){
            $dRow['category_r'] = $dRow['category'];
            $dRow['category'] = explode(',', $dRow['category']);
           // array_push($dRow['category'], $dRow['debit_t_id']);
            if (count(array_unique(array_merge($cloth,$dRow['category']))) < ($count+count($dRow['category'])) ){
                $debit[$dRow[$index]] = $dRow;
            }
        }
//        echo '<pre>';print_r($debit);exit;
        
        return $debit;
    }
    /*
     * 优惠活动方式
     * 2017-4-20
     * shaozz
     * */
  
    public static function active_lists($goods_id='0',$type='0'){
       
        $goods_prorule_mod =& m('goods_prorule');
        $member_mod =& m('member');
        $goods_mod = m('goods');
        $link_mod =& m('goods_prorel');
        $goodslink_mod = &m('goods_prolink');
        //商品信息
        $goodsinfos=$goods_mod->get(array(
            'conditions'=>"goods_id= '$goods_id'",
        ));
        //获得全部正在进行的规则
        $times=time();
        $user_id=self::user_id();
        $userinfo = $member_mod->get(array(
            'conditions' =>"user_id= '{$user_id}'"
        
        ));
        $memberlv=$userinfo['member_lv_id'];
     
        $rules_lists = $goods_prorule_mod->find(array(
            'conditions' =>"is_open=1 AND FIND_IN_SET(1,site_id) AND starttime < $times AND endtime > $times AND   FIND_IN_SET({$memberlv},member_lv_id)"
            
        ));
       
        //获得属于商品的规则
        $rule_on=array();
      if($rules_lists){
         foreach($rules_lists as $key=>$val){
            
             if($val['favorable']=='1'){
                 //商品类型
                 $goods_link1= $goodslink_mod->get(array(
                 'conditions'=>"rules_id='{$val['id']}' AND favorable_id=1",
                 ));
                if($goods_link1['favorable_value'] ==$goodsinfos['type_id']){
                    $rule_on[]=$val;
                }
             }elseif($val['favorable']=='2'){
                 //指定商品
                 $goods_link2= $link_mod->get(array(
                 'conditions'=>"d_id='{$val['id']}' AND c_id={$goodsinfos['goods_id']}  ",
                 ));
                 if($goods_link2){
                     $rule_on[]=$val;
                 }
             }elseif($val['favorable']=='3'){
                 //商品分类
                 $goods_link3= $goodslink_mod->get(array(
                 'conditions'=>"rules_id='{$val['id']}' AND favorable_id=3",
                 ));
                 if($goods_link3['favorable_value'] ==$goodsinfos['type_id']){
                     $rule_on[]=$val;
                 }
             }elseif($val['favorable']=='4'){
                //所有商品
                 $rule_on[]=$val;
             }
         }
      }
      return $rule_on;
    }
    /**
     * 酷卡
     *
     * @date 2015年12月4日 下午3:30:26
     *
     * @author Ruesin
     */
    public static function kukas($index = 'id'){
        
        $mKuka = &m('special_code');
        
        $kukas = $mKuka->find(array('conditions' => " to_id = '".self::user_id()."' AND is_used = '1' AND is_order = '0' AND cate > 19 AND expire_time > ".time(),'index_key'=>$index));
        
        return $kukas;
    }
    
    /**
     * 支付方式列表
     *
     * @date 2015年12月4日 下午3:35:05
     *
     * @author Ruesin
     */
    public static function payments($type = 'pc'){
        
        $mPayment = &m("payment");
        
        if ($type == 'pc'){
            $where = " AND ismobile = 0 ";
        }elseif ($type == 'wap'){
            $where = " AND ismobile = 1 ";
        }
        
        return $mPayment->find(array(
            				'conditions' => "enabled=1 AND payment_code <> 'wxpay' ".$where,
            				'order'      => "sort_order DESC",
                            'index_key'  => 'payment_code',
        ));
    }
    
    /**
     * 服务门店地区列表
     *
     * @date 2015年12月4日 下午3:40:05
     *
     * @author Ruesin
     */
    public static function getServeRegion(){
        
        $region_mod = m('region');
        $region_list = $region_mod->find("parent_id > 2 AND is_serve = 1"); //这里有问题  直辖市下的服务点定位在区里的 如果下拉只出最后一个，那么会只出个区的
        $zxs = array(3=>'北京市',22=>'天津市',41=>'上海市',61=>'重庆市');
        foreach ($region_list as &$row){
            if(isset($zxs[$row['parent_id']])){
                $row['region_name'] = $zxs[$row['parent_id']] .' '.$row['region_name'];
            }
        }
        
        return $region_list;
    }
    
    /**
     * 会员信息
     *
     * @date 2015年12月8日 下午1:04:01
     *
     * @author Ruesin
     */
    public static function userInfo($user_id = 0){
    
        $user_id = $user_id ? $user_id : self::user_id();
    
        $mMember = &m('member');
        $mInfo = $mMember->get($user_id);
        $mInfo['user_name'] = addslashes($mInfo['user_name']);
        $mInfo['coin']  = intval($mInfo['coin']) > 0 ? intval($mInfo['coin']) : 0;
        $mInfo['money'] = intval($mInfo['money']) > 0 ? intval($mInfo['money']) : 0;
    
        return $mInfo;
    }
    
    /**
     * 会员地址列表
     *
     * @date 2015年12月8日 下午1:04:10
     *
     * @author Ruesin
     */
    public static function addressList($user_id = 0){
    
        $user_id = $user_id ? $user_id : self::user_id();
    
        $mAddr = &m('address');
        return $mAddr->find(" user_id = '{$user_id}' ");
    
    }
    
    /**
     * 购物车cookies
     *
     * @date 2015年12月7日 上午8:41:53
     *
     * @author Ruesin
     */
    public static function cartCookieGet($key = ''){
        return $_SESSION['_cart']['check'];
    
        $cart = md5(self::user_id().'_cart');
    
        $data = $_COOKIE[$cart];
    
        $return = $data ? unserialize(stripslashes_deep($data)) : array();
    
        if ($key)
            return isset($return[$key]) ? $return[$key] : array();
    
        return $return;
    
    }
    
    /**
     * 设置购物车cookies
     *
     * @date 2015年12月8日 下午1:06:15
     *
     * @author Ruesin
     */
    public static function cartCookieSet($key,$val){
    
        $cart = md5(self::user_id().'_cart');
    
        $data = self::cartCookieGet();
    
        $data[$key] = $val;
    
        setcookie($cart, serialize($data), time()+86400);
    }
    
    
    /**
     * 订单信息(任何逻辑都放在前一步,此方法只用来计算)
     *
     * @date 2015年12月7日 上午11:23:43
     *
     * @author Ruesin
     */
    public static function totalData($order,$cart){
    
        if (isset($cart['figure_fee']) && $cart['figure_fee'] > 0){
            $cart['order_amount'] += $cart['figure_fee'];
            $cart['final_amount'] += $cart['figure_fee'];
        }
        
        $cart['money_amount'] = $cart['coin'] = $cart['kuka_fee'] = $cart['debit_fee'] = 0;
        
        //余额
        if(isset($order['dedu']['money']) && $order['dedu']['money'] > 0){
            $cart['money_amount']  = $order['dedu']['money'];
            $cart['final_amount'] -= $cart['money_amount'];
        }
    
        // 麦富迪币
        if (isset($order['dedu']['coin']) && $order['dedu']['coin'] > 0){
            $cart['coin']         = $order['dedu']['coin'];
            $cart['final_amount'] -= $cart['coin'];
        }
    
        //酷卡
        if(isset($order['kuka']) && !empty($order['kuka'])){
            $cart['kuka_fee'] = 0;
            foreach ($order['kuka'] as $kuVal){
                $cart['discount']    += $kuVal['work_num'];
                $cart['kuka_fee']    += $kuVal['work_num'];
            }
            $cart['final_amount'] -= $cart['kuka_fee'];
        }
    
        //优惠券
        if (!empty($order['debit']) && isset($order['debit']['money']) && intval($order['debit']['money']) > 0){
            
//            echo '<pre>';print_r($cart);exit;
            
            $cart['discount']     += $order['debit']['money'];
            $cart['debit_fee']     = $order['debit']['money'];
            $cart['final_amount'] -= $cart['debit_fee'];
        }
         
        $cart['final_amount'] = $cart['final_amount'] >= 0 ? $cart['final_amount'] : 0;
    
        //使用抵扣券不给积分
        $cart['point_g'] = ($cart['final_amount'] + $cart['money_amount']) * 0.1;
    
        return $cart;
    }
    
    /**
     * 记录订单日志
     *
     * @date 2015年12月7日 下午4:03:44
     *
     * @author Ruesin
     */
    public static function recordLogs($options){
        $_behaviors = array('create','update','cancel','autoCancel','payment','production','delivery','quickPrice','pushPro','pushDel');
    
        $status = array(
                11 => '待付款',
                12 => '待量体',
                20 => '已付款',
                60 => '生产中',
                61 => '备货中',
                30 => '已发货',
                40 => '已完成',
                41 => '返修中',
                0 => '已取消',
                43 => '订单异常',
        );
    
        foreach ($options as $ops) {
    
            $from = isset($ops['from']) ? (isset($status[$ops['from']]) ? $status[$ops['from']] : $ops['from'] ) : '';
            $to   = isset($ops['to']) ? (isset($status[$ops['to']]) ? $status[$ops['to']] : $ops['to'] ) : '';
    
            if(!isset($ops['text'])){
                switch ($ops['behavior']){
                    case 'create':
                        $ops['text'] = "订单创建成功!";
                        break;
                    case 'update':
                        $ops['text'] = "订单状态从[{$from}]更新到[{$to}]!";
                        break;
                    case 'cancel':
                        $ops['text'] = "订单从[{$from}]状态取消!";
                        break;
                    case 'autoCancel':
                        $ops['text'] = "订单自动取消!";
                        break;
                    case 'payment':
                        $ops['text'] = "订单支付成功!";
                        break;
                    case 'production':
                        $ops['text'] = "订单已进入生产中!";
                        break;
                    case 'delivery':
                        $ops['text'] = '订单已发货!';
                        break;
                    case 'quickPrice':
                        $ops['text'] = '订单快速修改价格成功!';
                        break;
                    default:
                        $ops['text'] = "hacking!";
                        break;
                }
            }
            $save[] = array(
                    'order_id'     => $ops['order_id'],
                    'op_id'        => $ops['op_id'],
                    'op_name'      => $ops['op_name'],
                    'alttime'      => time(),
                    'behavior'     => $ops['behavior'],
                    'log_text'     => $ops['text'],
                    'from_status'  => isset($ops['from'])?$ops['from']:'',
                    'to_status'    => isset($ops['to']) ? $ops['to'] :'',
                    'op_ip'        =>  real_ip(),//\Yii::$app ->request->getUserIP(),//real_ips(),
                    'remark'       => isset($ops['remark']) ? $ops['remark'] : '',
            );
        }
        $mOrderlogs = &m('orderlogs');
        $res = $mOrderlogs->add(addslashes_deep($save));
        if ($res) return true;
    
        return false;
    }
    
    /**
     * 记录财务报表
     *
     * @date 2015年12月7日 下午3:51:53
     *
     * @author Ruesin
     */
    public static function recordFinance($order,$type = 'pay'){

        $mClientFinance = &m('clientfinancedetail');
        $has = $mClientFinance->get(array(
                'conditions' => " user_id = '{$order['user_id']}'",
                'order'      => ' add_time DESC',
                'limit'      => ' 1 '
        ));
        
        $start_balance = 0;
        
        if (!empty($has)){
            $start_balance = $has['end_balance'];
        }
        
        if ($type == 'pay'){
            $type  = 2;
            $minus = 5;
            $trans_amount = $order['final_amount'];
            $mark = '+';
            $end_balance = $start_balance + $trans_amount;
            $abstract = '正常商品订单，第三方：'.$order['final_amount'].'。';
        }else{
            $type = 1;
            $minus = 1;
            $trans_amount = $order['final_amount']+$order['money_amount']+$order['coin'];
            $mark = '-';
            $end_balance = $start_balance - $trans_amount;
            $abstract = '正常商品订单，余额：'.$order['money_amount'].'，麦富迪币：'.$order['coin'].'，第三方：'.$order['final_amount'].'。';
        }
        
        
        $sData = [
                'user_id'    => $order['user_id'],
                'finance_sn' => $order['order_sn'],
                'type'       => $type,  // 1 发货 2 支付
                'minus'      => $minus,  // 1 发货 5 支付
                //'deal_node'  => '',
                'start_balance' => $start_balance,   //  last end_balance  按 add_time || 0
                'end_balance'   => $end_balance,   // 发货 start_balance - (money+coin+ali)  / 支付 start_balance + ali  / 0
                'mark'       => '-',//发货 -   支付  +
                'add_time'   => time(),
                'trans_amount' => $trans_amount,  //发货 (money+coin+ali)  / 支付 ali / 0
                'abstract' => $abstract,   //发货 正常商品订单，余额：1000，麦富迪币：1000，第三方：1000。 || 支付
        ];
        
        $res = $mClientFinance->add($sData);
        
        if ($res <= 0){
            return false;
        }
        return true;
    }



    /**
     * 订单满额 送麦券 一张
     *
     * @author tangsj 2016-05-26
     * @return result( 1 成功 0 失败) auth-认证(1 成功 0 失败) invite-邀请（1成功 0失败） coin-酷币（1成功 2失败）
     */
    public static function give_debit($amount, $user_id) {
        $store_allow = include ROOT_PATH . '/data/settings.inc.php';
        $_cate_mod = & bm ( 'gcategory', array (
            '_store_id' => 0
        ) );

        $debit_mod = &m ( "debit" );




        if (! empty ( $store_allow ['debit_cate_o'] ) && ! empty ( $store_allow ['debit_time_o'] ) && ! empty ( $store_allow ['debit_name_o'] ) && ! empty ( $store_allow ['debit_num_o'] ) && ! empty ( $store_allow ['debit_type_o'] ) && ! empty ( $store_allow ['debit_order_o'] ) && ! empty ( $store_allow ['debit_open_o'] )) {
            if ($store_allow ['debit_cate_o'] == 1) {
                $expire_time = strtotime ( '+' . $store_allow ['debit_time_o'] . ' days' ) - date ( 'Z' );
            } else {
                $expire_time = $store_allow ['debit_time_o'];
            }
            $gcategories = $_cate_mod->get_child_cateid ( $store_allow ['debit_type_o'] );
            if (empty ( $gcategories )) {
                $gcategories = $store_allow ['debit_type_o'];
            }

            if ($amount >= $store_allow ['debit_order_o']) {

                $data = array (
                    'debit_name' => $store_allow ['debit_name_o'],
                    'debit_t_id' => $store_allow ['debit_type_o'],
                    'debit_sn' => time () . createNonceStr ( 8 ),
                    'money' => $store_allow ['debit_num_o'],
                    'user_id' => $user_id,
                    'source' => 'order',
                    'add_time' => time (),
                    'cate' => $gcategories,
                    'expire_time' => $expire_time
                );

                $debit_mod->add ( $data );
            }
        }
    }

    /**
     * 支付成功 修改商品库存和已售的数量
     *
     * @author liang.li
     * @return
     */
    public static function f_goods($order_info)
    {
        $member_mod = &m ( "member" );
        $order_mod = m("order");
        //=====  如果是微信订单要发送消息  =====
        $member_info = $member_mod->get_info($order_info['user_id']);
        if($member_info['openid'] && $order_info['is_send'] == 0)
        {

            $mod = m('accesstoken');
            $access_token = $mod->get_token();
            $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
            $data['touser'] = $member_info['openid'];
            $data['template_id'] = 'GvVZ-3FqczJ8S9EgFgifPp8aVyR9KiQpRU3aNZZOQTo';
            $data['url'] = "http://h5.myfoodiepet.com/my_order-detail-".$order_info['order_id'].".html";
            $data['data']['first']['value'] = "尊敬的".$member_info['nickname']."您好";
            $data['data']['first']['color'] = "#173177";
            $data['data']['product']['value'] = "麦富迪狗粮";
            $data['data']['product']['color'] = "#173177";
            $data['data']['price']['value'] = $order_info['final_amount'];
            $data['data']['price']['color'] = "#173177";
            $data['data']['time']['value'] = date("Y年m月d日",$order_info['add_time']);
            $data['data']['time']['color'] = "#173177";
            $data['data']['remark']['value'] = "点击查看订单详情!";
            $data['data']['remark']['color'] = "#173177";
            $res = https_request($url,json_encode($data));
            if($res['errcode'] == 0)
            {
                $order_mod->edit($order_info['order_id'],['is_send'=>1]);
            }
            else
            {
                $access_token = $mod->settoken();
                $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
                $res = https_request($url,json_encode($data));
            }

        }

        $order_id = $order_info['order_id'];
        $order_goods_mod = m('ordergoods');
        $goods_mod = m('goods');
        $product_mod = m('products');
        $order_goods_list = $order_goods_mod->find(array(
            'conditions' => "order_id=".$order_id,
        ));

        foreach ($order_goods_list as $index => $item)
        {
            if ($item['type'] == "custom")
            {
                $quantity = $item['quantity'];
                $params = json_decode($item['params'],true);
                $product_id = $params['oProducts']['product_id'];
                $goods_id = $params['oProducts']['goods_id'];
                if ($product_id && $goods_id)
                {
                    $goods_mod->setInc("goods_id = $goods_id","buy_count",$quantity);
                    $product_mod->setDec("product_id = $product_id","store",$quantity);
                }

            }
        }
        //===== 修改member表相关信息 并且  =====
        $member_mod->setInc("user_id = {$order_info['user_id']}","order_num",1);
        $member_mod->setInc("user_id = {$order_info['user_id']}","final_amount_num",$order_info['final_amount']);
    }


    /**
     * mes推送成功 发微信消息 提醒上传标签图
     *
     * @author liang.li
     * @return
     */
    public static function f_wx($order_info,$item)
    {
        $member_mod = &m ( "member" );
        //=====  如果是微信订单要发送消息  =====
        $member_info = $member_mod->get_info($order_info['user_id']);
        if($member_info['openid'])
        {
            $mod = m('accesstoken');
            $access_token = $mod->get_token();
            $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
            $data['touser'] = $member_info['openid'];
            $data['template_id'] = 'UgrJMUtMBP4ErAoAccRXWbLT_uZddbDPIQV1byo-8w4';
            $data['url'] = "http://h5.myfoodiepet.com/my_order-orderimg-".$item['rec_id'].".html";

            $data['data']['first']['value'] = "[麦富迪高端定制]提醒您尽快为您的爱宠上传个性化标签图片";
            $data['data']['first']['color'] = "#173177";
            $data['data']['orderno']['value'] = $order_info['order_sn']."(子订单:".$item['rec_id'].")";
            $data['data']['orderno']['color'] = "#173177";

            $data['data']['amount']['value'] = $order_info['final_amount'];
            $data['data']['amount']['color'] = "#173177";

            $data['data']['remark']['value'] = "请您尽快上传个性化标签图片，一旦订单开始生产后，您将无法修改您订单的图片信息，谢谢!";
            $data['data']['remark']['color'] = "#173177";

            $res = https_request($url,json_encode($data));
            if($res['errcode'] == 0)
            {
                $order_goods_mod = m('ordergoods');
                $order_goods_mod->edit("rec_id = ".$item['rec_id'],['is_send'=>1]);
            }
            else
            {
                $access_token = $mod->settoken();
                $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
                $res = https_request($url,json_encode($data));
            }

        }
    }
    
    /**
     * 校验面料库存
     * 
     * 总共的数量 * 单耗  库存  以面料为单位
     * 
     * 面料号为键 DBM739A ，品类CODE为二维键 0003 ，三维  'cloth' => '0003', 'fabric' => 'DBM739A','quantity' => 6
     * 
     *  $data = [
     *          'DBM739A' => [
     *                  '0003' => [
     *                          'cloth' => '0003',
     *                          'fabric' => 'DBM739A',
     *                          'quantity' => 6
     *                  ],
     *                  '0004' => [
     *                          'cloth' => 0004,
     *                          'fabric' => 'DBM739A',
     *                          'quantity' => 2
     *                  ]
     *          ],
     *          'DBP707A' => [
     *                  '0004' => [
     *                          'cloth' => 0004,
     *                          'fabric' => 'DBP707A',
     *                          'quantity' => 8
     *                  ],
     *                  '0003' => [
     *                          'cloth' => 0003,
     *                          'fabric' => 'DBP707A',
     *                          'quantity' => 3
     *                  ]
     *          ]
     *  ];
     *
     * @date 2015年12月9日 下午2:08:27
     *
     * @author Ruesin
     */
    public static function checkFabricStock($data){
        
        if (!$data) return '';
        
        $isDiy = 0;
        foreach ($data as $key => $row){
            $fbs[$row['product_id']] = $row['product_id'];
            $stc[$row['product_id']] = $row['quantity'];
            $isDiy = $row['type'] == 'fdiy' ? 1 : 0;
        }
        
        //diy商品 不存在库存
        if ($isDiy)
        {
            return true;
        }
        $mP = &m('products');
        $products = $mP->find(array('conditions'=>db_create_in($fbs,'product_id') ,'index_key'=>'product_id'));
     
        foreach ($stc as $key=>$val){
            if (!isset($products[$key])){
                return false;
            }
            if($products[$key]['store'] < $val){
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * 新版刺绣(面料下单)
     *
     * @date 2016年1月12日 下午4:28:08
     *
     * @author Ruesin
     */
    public static function embsNew(){

        ## 通过中文匹配查签名
        $mFdiyManagement = &m('fdiy_management');
        $top = $mFdiyManagement->find("cate_name = '签名'");
        foreach ($top as $tVal){
            $topIds[]  = $tVal['cate_id'];
            $pIds[] = $tVal['parent_id'];
        }
        
        ## 签名的父分类
        $parent = $mFdiyManagement->find(db_create_in($pIds,'cate_id'));
        
        ## 查出签名下面的分类
        $son = $mFdiyManagement->find(db_create_in($topIds,'parent_id'));
        foreach ($son as $sVal){
            $sIds[] = $sVal['cate_id'];
        }
        ## 查出签名分类下的数据
        $emb = $mFdiyManagement->find(db_create_in($sIds,'parent_id'));
        foreach ($emb as $e){
            $dIds[] = $e['did'];
        }
        
        $mFdiyDict = &m('fdiy_dict');
        $dict = $mFdiyDict->find(db_create_in($dIds,'id'));
        
        foreach ($emb as $eVal){
            $eVal['image'] = $eVal['did'] ? $dict[$eVal['did']]['small_img'] : '';
            $embs[$eVal['parent_id']][$eVal['cate_id']] = $eVal;
        }

        foreach ($son as $sv){
            $sv['cate_name'] = trim($sv['cate_name']);
            if ($sv['cate_name'] == '内容') $sv['cate_name'] = 'neirong';
            
            if (!isset($qm[$sv['parent_id']])){
                $qm[$sv['parent_id']] = $top[$sv['parent_id']];
            }

            $qm[$sv['parent_id']]['list'][$sv['cate_id']] = $sv;
            if (isset($embs[$sv['cate_id']]))
                $qm[$sv['parent_id']]['list'][$sv['cate_id']]['list'] = $embs[$sv['cate_id']];
        }
        import('shopConf');
        foreach ($qm as $q){
            $parent_id = $q['parent_id'];
            if (isset(ShopConf::$fDiyCloth[$parent[$parent_id]['cate_id']])){
                if (!isset(ShopConf::$fDiyCloth[$parent[$parent_id]['parent_id']])){
                    $pc = ShopConf::$fDiyCloth[$parent[$parent_id]['cate_id']];
                }else{
                    $pc = ShopConf::$fDiyCloth[$parent[$parent_id]['parent_id']];
                }
                $sc = ShopConf::$fDiyCloth[$parent[$parent_id]['cate_id']];
                $result[$pc][$sc] = $q;
            }
        }
        
        return $result;
        
    }
    
    
    
}
