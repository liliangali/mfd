<?php

class Rcmtm
{
    function gStatusByJson($data){
        $params = json_decode($data->params,1);
        if(!$params){
            return $this->json_result('-1', 'params error!');
        }
        
        $pK = key($params);
        $pV = $params[$pK][0];
        
        if(md5('mfdapi'.$pK.$pV) != $data->token){
            return $this->json_result('-1', 'params error');
        }

        $mOrderGoods = m('ordergoods');
        
        $success = $failed = array();
        foreach ($params as $key=>$row){
            
            $All = $hasIds = $needIds = array();
            
            $All = $mOrderGoods->find(array('conditions' => db_create_in($row,'rcmtm_id'),'index_key' => 'rcmtm_id'));
            
            if(count($All) != count($row)){
                foreach ($row as $k=>$v){
                    if(!$All[$v]){      //无数据
                        $noData[] = $v;
                    }else{
                        $hasIds[$v] = $v;
                    }
                }
                
            }else{
                $hasIds = $row;
            }
            
            if(!$hasIds){
                continue;
            }
            
            foreach ($hasIds as $val){
                if ($All[$val]['mtm_status'] == $key){
                    $noNeed[] = $val;
                }else{
                    echo $val;
                    $needIds[] = $val;
                }
            }

            if(!$needIds){
                continue;
            }
            
            $res = $mOrderGoods->edit(db_create_in($needIds,'rcmtm_id'),array("mtm_status" => $key));
            
            if($res <= 0 ){
                $failed = array_merge($failed,$needIds);
            }else{
                $success = array_merge($success,$needIds);
            }
            
        }
        
        if(!$success){
            return $this->json_result('0',array('noData'=>$noData,'noNeed'=>$noNeed,'failed'=>$failed));
        }else{
            return $this->json_result('1',array('noData'=>$noData,'noNeed'=>$noNeed,'success'=>$success));
        }
        
    }
    
    function gStatusById($data){

        $ids = explode(',', $data->ids);
        if(!$ids){
            return $this->json_result('-1', 'params error!');
        }
        if(md5('mfdapi'.$data->status.$ids[0]) != $data->token){
            return $this->json_result('-1', 'params error');
        }
        
        $mOrderGoods = m('ordergoods');
        
        $All = $mOrderGoods->find(array('conditions' => db_create_in($ids,'rcmtm_id'),'index_key' => 'rcmtm_id'));
        
        $noData = $needIds = $noNeed = array();
        
        if(count($All) != count($ids)){
            foreach ($ids as $k=>$v){
                if(!$All[$v]){      //无数据
                    $noData[] = $v;
                }else{
                    $hasIds[$v] = $v;
                }
            }
            if(!$hasIds){
                return $this->json_result('0',array('noData'=>$noData));  //全部失败
            }
            
        }else{
            $hasIds = $ids;
        }
        
        foreach ($hasIds as $val){
            if ($All[$val]['mtm_status'] == $data->status){
                $noNeed[] = $val;
            }else{
                $needIds[] = $val;
            }
        }
        
        if(!$needIds){
            return $this->json_result('0',array('noData'=>$noData,'noNeed'=>$noNeed));
        }
        
        $res = $mOrderGoods->edit(db_create_in($needIds,'rcmtm_id'),array("mtm_status" => $data->status));
        
        if($res <= 0 ){
            return $this->json_result('0',array('noData'=>$noData,'noNeed'=>$noNeed,'failed'=>$needIds));
        }else{
            return $this->json_result('1',array('noData'=>$noData,'noNeed'=>$noNeed,'success'=>$needIds));
        }
        
    }
    
    function gUpdateStatus($data){
        
        $mOrderLogData = &m('ordermtmlogdata');
        $mOrderLogData->add(array('params'=>$data,'addtime'=>time()));
        
        $params = json_decode($data,1);
        
//         $params = array(
//                 "DeliveryNo" => "null",
//                 "WaybillNo" => "1Z8633X90491096603",
//                 "ShippingCode" => "LGNE151012083432636",
//                 "DeliveryDate" => "2015-10-26 00:00:00.0",
//                 "OrderNo" => "ASRMTL23928",
//                 "Porxy" => "QC2C",
//                 "Status" => "10030",
//                 "SysCode" => "NI1510090079",
//         );
        
        if(!$params){
            return $this->json_result('-1', 'params error!');
        }
        
        if('QC2C' != $params['Porxy']){
            return $this->json_result('-1', 'user error!');
        }



        $status = array('10030'=>'制版','10031'=>'生产','10032'=>'备货','10034'=>'已发货','10033'=>'已发货');
        if(!$status[$params['Status']]){
            return $this->json_result('-1', 'status error!');
        }
        
//         [OrderNo] => TEST15070713  //订单号
//         [Status] => 10030  //状态：10030 制版  10031生产  10032  备货  10034  已发货(后改成 10033)
//         [Porxy] => SADA    //用户名（QC2C）
//         [SysCode] => NI1507310259  //系统单号
//         [DeliveryDate] => 2015-08-20 //交货日期

        $sData = array(
                'rcmtm_id' => isset($params['OrderNo']) ? $params['OrderNo'] : '',
                'status'  => $params['Status'],
                'user'    => $params['Porxy'],
                'SysCode' => isset($params['SysCode']) ? $params['SysCode'] : '',
                'delivery_date' => isset($params['DeliveryDate']) ? strtotime($params['DeliveryDate']) : '',
                
                'shippingcode'  => isset($params['ShippingCode']) ? $params['ShippingCode'] : '',
                'deliveryno'    => isset($params['DeliveryNo']) ? $params['DeliveryNo'] : '',
                'waybillno'     => isset($params['WaybillNo']) ? $params['WaybillNo'] : '',
                'params' => $data,
                'addtime' => time(),
        );

        if (isset($params['OrderNo']) && $params['OrderNo'] != '' && $params['OrderNo'] != null && $params['OrderNo']){
            $mOrderGoods = &m('ordergoods');
            $goods = $mOrderGoods->get("rcmtm_id = '{$params['OrderNo']}'");
            
            if(!$goods){
                $sData['order_id'] = 0;
                $sData['order_goods_id'] = 0;
                $sData['behavior']   = 'noData';
            
                $this->_record_logs($sData);
            
                return $this->json_result('0',array('noData'=>$params['OrderNo']));
            }
            
            $sData['order_id']       = $goods['order_id'];
            $sData['order_goods_id'] = $goods['rec_id'];
            
            if($goods['mtm_status'] ==  $params['Status']){
            
                $sData['behavior']   = 'noNeed';
                $this->_record_logs($sData);
            
                return $this->json_result('0',array('noNeed'=>$params['OrderNo']));
            }
            
            $res = $mOrderGoods->edit(" rcmtm_id = '{$params['OrderNo']}' ",array("mtm_status" => $params['Status']));
            
            if($res <= 0){
            
                $sData['behavior']   = 'failed';
                $this->_record_logs($sData);
            
                return $this->json_result('0',array('failed'=>$params['OrderNo']));
            }
        }
        
        if ($params['Status'] == '10030' || $params['Status'] == '10031'){
            
        }elseif ($params['Status'] == '10033'){
            if(isset($params['ShippingCode']) && $params['ShippingCode'] != '' && $params['ShippingCode'] && $params['ShippingCode'] != null && isset($params['WaybillNo']) && $params['WaybillNo'] != '' && $params['WaybillNo'] != null && $params['WaybillNo']){

                $mOrder = &m('order');
                //$orders = $mOrder->get(" express = '{$params['ShippingCode']}'");
                //if($orders['waybillno'] == '' || $orders['waybillno'] == null || !$orders['waybillno'] || $orders['waybillno'] != $params['WaybillNo']){
                    $edit =  $mOrder->edit(" express = '{$params['ShippingCode']}' " ,array( 'status' => 30 ,'waybillno' => $params['WaybillNo'] ,'ship_time'=>time()));
                    
                    if ($edit > 0){
                        $orders = $mOrder->find(" express = '{$params['ShippingCode']}' ");
                        
                        $phoneNum = $order_sns = '';
                        $user_id = 0;
                        foreach ($orders as $row){
                            $phoneNum = isset($row['ship_mobile']) ? $row['ship_mobile'] : '';
                            $user_id = $row['user_id'];
                            $order_sns .= $row['order_sn'].'，';
                            $shipName = isset($row['ship_name']) ? $row['ship_name'] : '';
                            
                            get_client_finance($user_id,$row['order_sn'],1,intval($row['final_amount']+$row['money_amount']+$row['coin']),array('正常商品订单，收货人'.$shipName.'，余额：'.$row['money_amount'].'，麦富迪币：'.$row['coin'].'，第三方：'.$row['final_amount'].'。'));
                        }
                        //$msg = "您的订单（".$order_sns."）已发货，物流单号".$params['WaybillNo']."，请注意签收，有质量问题请及时联系在线客服或拨打400-169-8836。";
                        $msg = "订单（".$order_sns."）已发货，运单号".$params['WaybillNo']."，若有问题请联系客服400-169-8836。";
                        if ($phoneNum != ''){
                            $rs   = SendSms($phoneNum, $msg, 'rcmtm_ship', '1');
                        }
                        if ($user_id){
                            sendSystem($user_id, '13', '订单通知', $msg);
                        }
                        
                        
                    }


                //返修
                $order_serve_mod = & m('orderserve');
                $order_serve_info_mod = & m('orderserveinfo');
                $order_serve_mod->edit(" send_wl_sn = '{$params['ShippingCode']}' " ,array( 'status' =>11 ,'waybillno' => $params['WaybillNo']));

                $order_serve  = $order_serve_mod->get(" send_wl_sn = '{$params['ShippingCode']}' ");
                $fx_ret= $order_serve_info_mod->edit("s_id = '{$order_serve['id']}' " ,array( 'info_status' =>11));


                if ($fx_ret){
                    //$msg = "您的返修订单（".$order_serve['order_sn']."）已发货，物流单号".$params['WaybillNo']."，请注意签收，有质量问题请及时联系在线客服或拨打400-169-8836。";
                    $msg = "订单（".$order_serve['order_sn']."）已发货，运单号".$params['WaybillNo']."，若有问题请联系客服400-169-8836。";
                    if ($order_serve['phone_mob'] != ''){
                        SendSms($phoneNum, $msg, 'rcmtm_ship', '1');
                    }
                    if ($user_id){
                        sendSystem($user_id, '17', '返修订单通知', $msg);
                    }
                }

                //}
            }
        }
        
        $sData['order_id']       = isset($goods['order_id']) ? $goods['order_id'] : '';
        $sData['order_goods_id'] = isset($goods['rec_id'])   ? $goods['rec_id'] : '';
        
        $sData['behavior']   = 'success';
        $this->_record_logs($sData);
        
        return $this->json_result('1',array('success'=>$params['OrderNo']));
        
    }

    function gOrderServeStatus($data){
        $params = (array)simplexml_load_string($data);
        unset($params['comment']);
//        return $this->json_result('-1', $params);

        if(!$data){
            return $this->json_result('-1', 'params error!');
        }

        //约定状态
        $status = array('10031'=>'生产','10372'=>'审核失败（驳回）');

        if(!$status[$params['status']]){
            return $this->json_result('-1', 'status error!');
        }

        $order_serve_mod = & m('orderserve');
        $order_serve_info_mod = & m('orderserveinfo');

        if (!$params['repairOrderID']){
            return $this->json_result('-1', 'repairOrderID error!');
        }


        $sData = array(
            'rcmtm_id' => isset($params['repairOrderID']) ? $params['repairOrderID'] : '', //返修系统订单号
            'status'  => $params['status'], //驳回：10372审核失败   通过：10031生产-
            'delivery_date'=>isset($params['repairOrderMemo']) ? strtotime($params['repairOrderMemo']) : '',
            'params' => json_encode($params),
            'addtime' => time(),
        );


//         $params['Status'], //驳回：10372审核失败   通过：10031生产-
//         isset($params['repairOrderID']) ? $params['repairOrderID'] : '', //返修系统订单号
//        $params['repairOrderMemo'], //驳回原因 或 通过后返修订单交货日期
//        isset($params['repairPrice']) ? $params['repairPrice'] : '', //通过后的返修扣费价格
//         isset($params['repairPriceMemo']) ? strtotime($params['repairPriceMemo']) : '', //通过后的返修扣款备注
//         isset($params['repairZRPD']) ? $params['repairZRPD'] : '', //通过后的返修责任判定(返修责任判定 服装分类ecde：一级责任判定ecode_二级责任判定ecode套装的话逗号隔开绩效追加)


        $fx_info = $order_serve_info_mod->get("fx_sn = '{$params['repairOrderID']}'");
        $serve_order_arr = $serve_order_info_arr = array();
        //返回成功
        if($params['status'] =='10031'){
            $serve_order_arr['send_time']  = strtotime($params['repairOrderMemo']);//交货日期 这里加 相当于最后一条当前订单的交货日期

            $serve_order_info_arr['info_status'] = 8;  //对应成功   直接生产
            $serve_order_info_arr['price'] = $params['repairPriceMemo'];  //对应成功   直接生产
            $zr =explode(':',$params['repairZRPD']); //责任判定  目前只有一级责任判定
            $zr_arr =explode("_",$zr[1]);
            $serve_order_info_arr['zr']  = $zr_arr[0];

        }

        //返回失败
        if($params['status'] =='10372'){
            $serve_order_info_arr['info_status'] =3; //对应审核失败
            $serve_order_info_arr['fail_reason'] =$params['repairOrderMemo'];//驳回原因
        }



        if(!$fx_info){
            return $this->json_result('0',array('noData'=>$params['repairOrderID']));
        }


        //修改
        $res = $order_serve_info_mod->edit("fx_sn='{$params['repairOrderID']}'",$serve_order_info_arr);
        if(!$res){
            $sData['behavior']   = 'failed';
            $this->_record_fx_logs($sData);
            return $this->json_result('0',array('failed'=>$params['repairOrderID']));
        }
        //总的状态=
        $all_fx_info       = $order_serve_info_mod->get(array(
            'conditions'=>"s_id = '{$fx_info['s_id']}'",
            'fields'=>"min(info_status) as status",
        ));
        $serve_order_arr['status']=$all_fx_info['status'];  //状态 已经转换成我们自己的状态
        $order_serve_mod->edit($fx_info['s_id'],$serve_order_arr);


        $sData['order_id']       = isset($fx_info['order_id']) ? $fx_info['order_id'] : '';
        $sData['order_goods_id'] = isset($fx_info['rec_id'])   ? $fx_info['rec_id'] : '';
        $sData['behavior']   = 'success';



        $this->_record_fx_logs($sData);
        return $this->json_result('1',array('success'=>$params['repairOrderID']));
    }




    /**
     * 记录更新日志
     * 
     * @date 2015-8-7 上午8:38:18
     * @author Ruesin
     */
    function _record_logs($data =  array()){
        if(!$data) return;
        $mOmLogs = &m('ordermtmlogs');
        $mOmLogs->add($data);
    }


    /**
     * 记录更新日志
     *
     * @date 2015-8-7 上午8:38:18
     * @author Ruesin
     */
    function _record_fx_logs($data =array()){
        if(!$data) return;
        $mOmLogs = &m('orderfxmtmlogs');
        $mOmLogs->add($data);
    }


    function json_result($do = '',$msg = ''){
        $array = array(
        	    'done'=>$do,
                'msg' => $msg,
        );
        return json_encode($array);
    }

}

