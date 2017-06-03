<?php
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 */
namespace Cyteam\Goods;

use Cyteam\Shop\Type\FdiyBase;
use Cyteam\Shop\Type\Types;

class Orders
{
    
    function __construct($param = []){
        
    }
    
    /**
    *详情
    *@author  liang.li
    */
    function getOrderInfo($orderId) 
    {
        $orderMdl = m('order');
        $orderGoodsMdl = m('ordergoods');
        $mod_fbcategory = &m("fbcategory");
        $orderInfo = $orderMdl->get_info($orderId);
        if(!$orderInfo)
        {
            return;
        }
        
        //===== 格式化  =====
        if ($orderInfo['invoice_com'] == 3) 
        {
            $orderInfo['invoice']['content'] = json_decode($orderInfo['invoice_title'],true);
        }

        //=====  手机号  =====
        if(!$orderInfo['ship_mobile'])
        {
            $orderInfo['ship_mobile'] = $orderInfo['ship_tel'];
        }

        //=====  手机号  =====
        if(!$orderInfo['ship_tel'])
        {
            $orderInfo['ship_tel'] = $orderInfo['ship_mobile'];
        }


        $status = $orderMdl->getStatus();
        //=====  获取物流信息  =====

        $logni_no = '';
        if($orderInfo['shipping_id'])
        {
            $shiiping_mod = m("shipping");
            $shiiping_info = $shiiping_mod->get("shipping_id=".$orderInfo['shipping_id']);
            $code = $shiiping_info['code'];
            $logni_no = $orderInfo['waybillno'];
        }
        if($logni_no)
        {
            $delres = $this->shipping($logni_no,$code);
            $deliveryList['wuliu'] = $delres;
        }
        $deliveryList['logi_name'] = $shiiping_info['shipping_name'];
        $deliveryList['logi_no'] = $logni_no;
        $orderInfo['delivery'] = $deliveryList;
        $orderInfo['me_data'] = unserialize($orderInfo['me_data']);

        $orderInfo['status_name'] = $status[$orderInfo['status']];
        if($orderInfo['status'] == 20 && $orderInfo['mes_order']) //已经支付 尚未发货
        {
            $orderInfo['status_name'] = $orderInfo['mes_order'];
        }

        $orderGoodsList = $orderGoodsMdl->find(array(
            'conditions' => "order_id = $orderId",
        ));

        $goods = Types::createObj("fdiy");
        $feed_mod =& m('feedamount');
        $type_list = $feed_mod->getType();
        $time_list = $feed_mod->getTime();
        $body_list = $feed_mod->getBody();
        $run_list  = $feed_mod->getRun();

        foreach ($orderGoodsList as $key => &$value)
        {

            $params = $value['params'];
    
            
            $params_arr = json_decode($params,true);

            $code = $params_arr['oGoods']['bn'];
            
            if($value['type'] == 'fdiy')
            {
                if($value['dog_name'])
                {
                    $value['goods_name'] = $value['dog_name']."的专属狗粮";
                }
                else
                {
                    $value['goods_name'] = "订制化主粮";
                }
                $fidybase = new FdiyBase();
                $code = $fidybase->fmoatCode($params_arr);

            }
            $value['code'] = $code;

            if ($value['params'])
            {
                $value['params'] = json_decode($value['params'],true);
                if ($value['type'] == 'custom')
                {
                    if ($value['params']['oProducts']['spec_info'])
                    {
                        $spe = [];
                        $spec_info_arr = explode("、", $value['params']['oProducts']['spec_info']);
                        foreach ((array)$spec_info_arr as $key1 => $value1)
                        {
                            $specInfo = explode("：", $value1);
                            $spec['p_name'] = $specInfo[0];
                            $spec['s_name'] = $specInfo[1];
                            $spe[] = $spec;
                        }
                    }
                    $value['spe_name'] = $spe;
                }
                else
                {
                    $pstr =  $this->_format_params($value['items']);

                    $value['spe_name'] = trim($pstr,"-");
                    
                    //=====  饲喂量建议  =====
                    if ($value['items'])
                    {
                        $fb_bz_info = $mod_fbcategory->find(array(
                            'conditions' => "cate_id IN ({$value['items']})",
                            'index_key' => "parent_id",
                        ));
                        $dog_list = $mod_fbcategory->get_list(21);

                        foreach ((array)$dog_list as $index => $item)
                        {
                            if(isset($fb_bz_info[$index]))
                            {
                                $fb_bz_info[21] = $fb_bz_info[$index];
                                unset($fb_bz_info[$index]);
                            }
                        }
                        
                        //=====  计算功效  =====
                        $gongxiao_str = $value['params'][1];
                        $gongxiao_arr = explode(":",$gongxiao_str);
                        $gongxiao = trim($gongxiao_arr[1],'"');
                        //=====  计算饲喂量  =====
                        $feed_arr['gongxiao'] = $gongxiao;//=====  功效  =====
                        $feed_arr['cat_id'] = $fb_bz_info[21]['cate_id'];//=====  犬种id  =====
                        $feed_arr['age_id'] = $fb_bz_info[34]['cate_id'];//=====  犬期id  =====
                        $feed_arr['is_feed'] = 0;//=====  计算 =====
                        $feed_arr['body_condition'] = $value['body_condition'];
                        $feed_arr['run_time'] = $value['run_time'];
                        $feed_arr['weight'] = $value['weight'];
                        $feed_arr['time_id'] = $value['time_id'];
                        $feed_arr['dog_nums'] = $value['dog_nums'];
                        $price_arr = $goods->fmoatFeed($feed_arr);
                        $value['feed_list'] = $price_arr;
                        if($body_list[$value['body_condition']])
                        {
                            $value['body_name'] = $body_list[$value['body_condition']];
                        }
                        if($run_list[$value['run_time']])
                        {
                            $value['run_name'] = $run_list[$value['run_time']];
                        }
                        if($time_list[$value['time_id']])
                        {
                            $value['time_name'] = $time_list[$value['time_id']];
                        }
                    }

                }
    
            }
        }
      $orderInfo['order_goods'] = $orderGoodsList; 
      return $orderInfo;
    
    }

    /**
     * mes发货
     */
    public function mefbn($order_id,$code,$bcode)
    {
        $return['statusCode'] = 1;
        $return['msg'] = '参数错误';
        if(empty($order_id) || empty($code) || empty($bcode))
        {
            return $return;
        }
        $order_mod = m('order');
        $shipping_mod = m('shipping');
        $shipping_info = $shipping_mod->get("code = '$bcode' ");
        if(!$shipping_info)
        {
            $return['msg'] = "物流编号有误";
            return $return;
        }
        $shipping_id = $shipping_info['shipping_id'];
        $order_info = $order_mod->get_info($order_id);
        if(($order_info['mes_status'] != 1))
        {
            $return['msg'] = "订单尚未推送到mes";
            return $return;
        }
        if(($order_info['status'] != 20))
        {
            $return['msg'] = "订单不是已支付状态";
            return $return;
        }
        $order_info=$order_mod->get($order_id);
        $data = array(
            'status'         => ORDER_SHIPPED,
            'ship_time'      => gmtime(),
            'waybillno'        => $code,
            'deliver_name'   => "mes",
            'shipping_id'   => $shipping_id,
        );
        $res = $order_mod->edit($order_id, $data,0);
        if($res !== false)
        {
            //发送系统通知
            //订单评论发送信鸽
            //订单评论发送信鸽

            $this->_record(array(
                'order_id' => $order_id,
                'from'     => 20,
                'to'       => 30,
                'op_id'    => 0,
                'op_name'  => 'mes',
                'behavior' => 'delivery',
                'remark'   => "mes返回物流单号",
            ));
            $return['statusCode'] = 0;
            $return['msg'] = "物流保存成功";

            fahuo($order_id);

            return $return;
        }
        else
        {
            $return['msg'] = "操作失败，请重试!";
            return $return;
        }

    }



    function _record($ops = array()){

        $status = array(
            11 => '待付款',
            12 => '待量体',
            20 => '已付款',
            60 => '生产中',
            61 => '备货中',
            30 => '已发货',
            40 => '已完成',
            41 => '返修中',
            0  => '已取消',
            43 => '订单异常',
            99 => '订单备注',
        );

        if(!$ops) return;
        $_behaviors = array('create','update','cancel','autoCancel','payment','production','delivery','quickPrice','pushPro','pushDel','remarks');
        if(!$ops['behavior'] || !in_array($ops['behavior'], $_behaviors)) return ;
        $from = isset($ops['from']) ? (isset($status[$ops['from']]) ? $status[$ops['from']] : $ops['from'] ) : '';
        $to   = isset($ops['to']) ? (isset($status[$ops['to']]) ? $status[$ops['to']] : $ops['to'] ) : '';
        if(!$ops['text']){
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
                case 'remarks':
                    $ops['text'] = '订单增加备注!';
                    break;
                default:
                    $ops['text'] = "hacking!";
                    break;
            }
        }

        $save = array(
            'order_id' => $ops['order_id'],
            'op_id'    => $ops['op_id'],
            'op_name'  => $ops['op_name'],
            'alttime'  => gmtime(),
            'behavior' => $ops['behavior'],
            'log_text' => $ops['text'],
            'from_status'     => $ops['from'],
            'to_status'       => $ops['to'],
            'op_ip'    => real_ips(),
            'remark'   => $ops['remark'],

        );
        $mod_orderlogs = &m('orderlogs');
        return $mod_orderlogs->add($save,false,0);
    }



    public function shipping($logni_no,$code)
    {
        $post_data = array();
        $post_data["customer"] = 'BE3A5D9D03C941D8C2A8B5B7B6D88D8A';
        $key= 'fYgHSpmg2046' ;
        $post_data["param"] = '{"com":"'.$code.'","num":"'.$logni_no.'"}';
//  echo '<pre>';print_r($post_data);exit;
        $url='http://poll.kuaidi100.com/poll/query.do';
        $post_data["sign"] = md5($post_data["param"].$key.$post_data["customer"]);
        $post_data["sign"] = strtoupper($post_data["sign"]);
        $o="";
        foreach ($post_data as $k=>$v)
        {
            $o.= "$k=".urlencode($v)."&";		//默认UTF-8编码格式
        }

        $post_data=substr($o,0,-1);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
//         echo '<pre>';print_r($result);exit;
        $data = str_replace("\&quot;",'"',$result );

        $data = json_decode($data,true);
        $wuliu_data = $data['data'];
        return $wuliu_data;
    }
    
    
    /**
     *列表页
     *@author  liang.li
     */
    function getOrderList($conditions = "1=1",$limit = "1")
    {
        $orderMdl = m('order');
        $orderGoodsMdl = m('ordergoods');
        $deliveryMdl = m('delivery');
        $shippingMdl = m('shipping');
        $orderList = $orderMdl->find(array(
            'conditions' => $conditions,
            'limit' => $limit,
            'count' => true,
            'order' => "order_id DESC",
        ));
        $status = $orderMdl->getStatus();
        if (!$orderList)
        {
            return [];
        }
        foreach ($orderList as $index => $item)
        {
            //=====  手机号  =====
            if(!$item['ship_mobile'])
            {
                $orderList[$item['order_id']]['ship_mobile'] = $item['ship_tel'];
            }
            //=====  手机号  =====
            if(!$item['ship_tel'])
            {
                $orderList[$item['order_id']]['ship_tel'] = $item['ship_mobile'];
            }
        }
        $order_id_arr = i_array_column($orderList, "order_id");
        $order_sn_arr = i_array_column($orderList, "order_sn");
        
        $orderGoodsList = $orderGoodsMdl->find(array(
            'conditions' => db_create_in($order_id_arr,"order_id"),
        ));
        $deliveryList = $deliveryMdl->find(array(
            'conditions' => db_create_in($order_sn_arr,"tid"),
            'index_key'  => "tid",
        ));
        
        $shippingList = $shippingMdl->find();
        
        foreach ($orderGoodsList as $key => $value)
        {
            if (!$value) 
            {
                continue;
            }
            
            if ($value['type'] == "fdiy") 
            {
                $value['goods_image'] = SITE_URL.$value['goods_image'];
                $orderGoodsList[$key]['goods_image'] = SITE_URL.$value['goods_image'];
            }
            
            if ($value['params'])
            {
                $value['params'] = json_decode($value['params'],true);
                
               //  $orderList[$value['order_id']]['goods_id']=$value['params']['oProducts']['goods_id'];
               //  $value['goods_id']=$value['params']['oProducts']['goods_id'];
                if ($value['type'] == 'custom')
                {
                    if ($value['params']['oProducts']['spec_info'])
                    {
                        $spe = [];
                        $spec_info_arr = explode("、", $value['params']['oProducts']['spec_info']);
                        foreach ((array)$spec_info_arr as $key1 => $value1)
                        {
                            $specInfo = explode("：", $value1);
                            $spec['p_name'] = $specInfo[0];
                            $spec['s_name'] = $specInfo[1];
                            $spe[] = $spec;
                        }
                    }
                    $value['spe_name'] = $spe;
                }
                else
                {
                    //                   return $value;
                    $pstr =  $this->_format_params($value['items']);
//                    $spe = [];
//                    $s_name = "";
//                    $spec_info_arr = explode("、", $pstr);
//                    foreach ((array)$spec_info_arr as $key1 => $value1)
//                    {
//                        $specInfo = explode(":", $value1);
//                        $spec['p_name'] = $specInfo[0];
//                        $spec['s_name'] = $specInfo[1];
//                        $spe[] = $spec;
//                        $s_name .= $specInfo[1]."-";
//                    }
                    $value['spe_name'] = trim($pstr,"-");
                }
    
            }
            if ($value['comment'] == 0) 
            {
                $orderList[$value['order_id']]['comment'] = 1;
            }
            if ($orderList[$value['order_id']])
            {
                if($value['type'] == 'fdiy')
                {
                    if($value['dog_name'])
                    {
                        $value['goods_name'] = $value['dog_name']."的专属狗粮";
                    }
                    else
                    {
                        $value['goods_name'] = "订制化主粮";
                    }

                }
                //=====  订单状态名称  =====
                $orderList[$value['order_id']]['status_name'] = $status[$orderList[$value['order_id']]['status']];
                if($orderList[$value['order_id']]['status'] == 20 && $orderList[$value['order_id']]['mes_order']) //已经支付 尚未发货
                {
                    $orderList[$value['order_id']]['status_name'] = $orderList[$value['order_id']]['mes_order'];
                }

                $orderList[$value['order_id']]['delivery_name'] = "";
                $orderList[$value['order_id']]['delivery_code'] = "";
                $orderList[$value['order_id']]['delivery_no'] = "";
                if ($shippingList[$orderList[$value['order_id']]['shipping_id']])
                {
                    if($orderList[$value['order_id']]['waybillno']){
                        $orderList[$value['order_id']]['delivery_name'] = $shippingList[$orderList[$value['order_id']]['shipping_id']]['shipping_name'];
                        $orderList[$value['order_id']]['delivery_no']   = $orderList[$value['order_id']]['waybillno'];
                    }else{
                        $orderList[$value['order_id']]['delivery_name'] ='暂未发货';
                    }
                    // $orderList[$value['order_id']]['delivery_code'] = $shippingList[$deliveryList[$orderList[$value['order_id']]['order_sn']]['logi_id']]['code'];
                    $orderList[$value['order_id']]['delivery_code'] = $shippingList[$orderList[$value['order_id']]['shipping_id']]['code'];
                    
                    $orderList[$value['order_id']]['post_fee']  = 0;
                }


                
                if ( isset($deliveryList[$orderList[$value['order_id']]['order_sn']]) && $deliveryList[$orderList[$value['order_id']]['order_sn']]) 
                {
                   // return $shippingList[$deliveryList[$orderList[$value['order_id']]['order_sn']]['logi_id']]['code'];
                   if($deliveryList[$orderList[$value['order_id']]['order_sn']]['logi_no']){
                       $orderList[$value['order_id']]['delivery_name'] = $deliveryList[$orderList[$value['order_id']]['order_sn']]['logi_name'];
                       $orderList[$value['order_id']]['delivery_no']   = $deliveryList[$orderList[$value['order_id']]['order_sn']]['logi_no'];
                   }else{
                        $orderList[$value['order_id']]['delivery_name'] ='暂未发货';
                    }
                    // $orderList[$value['order_id']]['delivery_code'] = $shippingList[$deliveryList[$orderList[$value['order_id']]['order_sn']]['logi_id']]['code'];
                    $orderList[$value['order_id']]['delivery_code'] = $deliveryList[$orderList[$value['order_id']]['order_sn']]['corp_code'];
                  
                    $orderList[$value['order_id']]['post_fee']   = $deliveryList[$orderList[$value['order_id']]['order_sn']]['post_fee'];
                }
                
                //===== 发票  =====
                if ($orderList[$value['order_id']]['invoice_com'] == 3) 
                {
                    if (!is_array($orderList[$value['order_id']]['invoice_title'] )) 
                    {
                        $orderList[$value['order_id']]['invoice_title'] = json_decode($orderList[$value['order_id']]['invoice_title'],true);
                    }
                }
            
                $orderList[$value['order_id']]['quantity'] = isset($orderList[$value['order_id']]['quantity']) ? $orderList[$value['order_id']]['quantity'] : 0 + $value['quantity'];
                $orderList[$value['order_id']]['item'][] = $value;
            }
        }

        return array_values($orderList);
    }
    
    
    public function _format_params($pData)
    {
//        if (!$pData || !is_array($pData))
//        {
//            return ;
//        }

        $mFb = &m('fbcategory');
        $fbcategorys = $mFb->find(array('conditions'=>"cate_id IN($pData)" ,'index_key'=>'cate_id'));
        $return = "";
        foreach ((array)$fbcategorys as $index => $item)
        {
            $return .= $item['cate_name']."-";
        }
        return  rtrim($return,'-');
//echo '<pre>';print_r($return);exit;



        $data = $this->_fd($pData);


        
//        echo '<pre>';print_r($pData);exit;
        
//    echo '<pre>';print_r(db_create_in($data['ids'],'cate_id'));exit;
    
        if ($data['ids'])
        {
            $mFb = &m('fbcategory');
            $fbcategorys = $mFb->find(array('conditions'=>db_create_in($data['ids'],'cate_id') ,'index_key'=>'cate_id'));
        }
//        echo '<pre>';print_r($fbcategorys);exit;
        
        unset($data);
        $data = $this->_fd($pData,$fbcategorys);
// echo '<pre>';print_r($data);exit;
        
        // 		return $data;
        return $data['str'];
    }
    protected function _fd($pData,$data=array())
    {
        $ids = [];
        $str = '';
        foreach ((array)$pData as $item)
        {
            $rows = $r = [];
            $rows = explode(":", $item);
    
            if ($data && $data[$rows[0]])
            {
                $str .= $data[$rows[0]]['cate_name'].":";
            }
            $ids[] =$rows[0];
            $rows[1] = str_replace('"', '',$rows[1]);

            
            $r = explode(",", $rows[1]);
//            echo '<pre>';print_r($r);exit;
            foreach ((array)$r as $val)
            {
                $ids[] = $val;
                if ($data && $data[$val])
                {
                    $str .= $data[$val]['cate_name'].'、';
                }
            }
        }
    
        return ['ids'=>$ids,'str'=>rtrim($str,'、')];
    }
    
    /**
    *订单确认收货
    *@author  liang.li
    */
    function subOrder($orderId,$user_id,$source="APP") 
    {
        $orderMdl = m('order');
        $orderlogsMdl = m('orderlogs');
        $orderInfo = $orderMdl->get_info($orderId);
        if ($orderInfo['user_id'] != $user_id)
        {
            return false;
        }
        if ($orderInfo['status'] != 30)
        {
            return false;
        }
        $odata['order_id'] = $orderId;
        $odata['op_id'] = $user_id;
        $odata['op_name'] = $user_info['user_name'];
        $odata['alttime'] = time();
        $odata['behavior'] = "update";
        $odata['log_text'] = "订单在".$source."端确认收货成功";
        $odata['from_status'] = 30;
        $odata['to_status'] = 40;
        $orderlogsMdl->add($odata);
        $orderMdl->edit($orderId,['status'=>40]);
        return true;
    }
    
    /**
    *取消订单
    *@author  liang.li
    */
    function canlOrder($orderId,$user_id,$source="APP") 
    {
        $orderMdl = m('order');
        $orderlogsMdl = m('orderlogs');
        $orderInfo = $orderMdl->get_info($orderId);
        if ($orderInfo['user_id'] != $user_id)
        {
            return false;
        }
        if ($orderInfo['status'] != 11)
        {
            return false;
        }
        $odata['order_id'] = $orderId;
        $odata['op_id'] = $user_id;
        $odata['op_name'] = $user_info['user_name'];
        $odata['alttime'] = time();
        $odata['behavior'] = "update";
        $odata['log_text'] = "订单在".$source."端取消订单";
        $odata['from_status'] = 11;
        $odata['to_status'] = 0;
        $orderlogsMdl->add($odata);
        $orderMdl->edit($orderId,['status'=>0]);

        //取消订单返回抵扣券
        $order_debit_mod = m('orderdebit');
        $order_debit = $order_debit_mod->get("order_id=$orderId");
        if($order_debit)
        {
            $mDbt = &m('voucher');
            $debit_id = $order_debit['d_id'];
            if($debit_id)
            {
                $mDbt->edit($debit_id,['use_status'=>0,'order_id'=>0]);
            }
        }



        return true;
    }
    
    
}