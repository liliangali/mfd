<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);
require_once "weipay/lib/WxPay.Api.php";
require_once 'weipay/lib/WxPay.Notify.php';
require_once 'weipay/log.php';

/**
 *    支付网关通知接口
 *
 *    @author    Garbin
 *    @usage    none
 */
class PaynotifywApp extends MallbaseApp
{
    /**
     *    支付完成后返回的URL，在此只进行提示，不对订单进行任何修改操作,这里不严格验证，不改变订单状态
     *
     *    @author    Garbin
     *    @return    void
     */
    function index()
    {
        $logHandler= new CLogFileHandler("logs/".date('Y-m-d-H:i:s').'.log');
        Log::Init($logHandler, 15);
// file_put_contents("logs/log.log", json_encode($logHandler));
// m('error')->add(array('content'=>1)); 
// m('error')->add(array('content'=>json_encode($_POST,true)));    
        Log::DEBUG("begin notify");
        $notify = new PayNotifyCallBack();
        $notify->Handle(false);
        //$data = $notify->GetValues();

      // file_put_contents("logs/log.log", json_encode($data));
    }
}

class PayNotifyCallBack extends WxPayNotify
{
    //查询订单
    public function Queryorder($transaction_id)
    {
        $input = new WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = WxPayApi::orderQuery($input);
        //Log::DEBUG("query:" . json_encode($result));
        if(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS")
        {
            return true;
        }
        return false;
    }

    //重写回调处理函数
    public function NotifyProcess($data, &$msg)
    {
//  m('error')->add(array('content'=>3));
        //Log::DEBUG("call back:" . json_encode($data));
        $notfiyOutput = array();
        
        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            return false;
        }
// m('error')->add(array('content'=>4));
        //查询订单，判断订单真实性
        if(!$this->Queryorder($data["transaction_id"])){
            $msg = "订单查询失败";
            return false;
        }
        
        //=====  查询订单状态  =====
        $order_mod = m('order');
        $order_info = $order_mod->get("out_trade_sn = '{$data['out_trade_no']}' ");
        if (!$order_info) 
        {
            $msg = "此订单不存在";
            return false;
        }
        if ($order_info['status'] != ORDER_PENDING)
        {
            $msg = "此订单状态不是待付款";
            return false;
        }
        if($order_info['has_measure'] == '1')
        {
            $order_status['target'] = ORDER_WAITFIGURE;   //待量体
        }
        else
        {
            $order_status['target'] = ORDER_ACCEPTED;   //生产中
        }
        
        
// m('error')->add(array('content'=>5));
// m('error')->add(array('content'=>json_encode($data,true)));
        
        //改变订单状态 
       /*  if($order_info['has_measure'] == '1'){
				        $order_status = ORDER_WAITFIGURE;   //待量体
				    }else{
				        $order_status = ORDER_ACCEPTED;   //已付款
				    } */
        $changeRes = $this->_change_order_status($order_info['order_id'], $order_info['extension'], $order_status);
        if ($changeRes){
            $this->lastSomeThing($order_info['order_id']);
                  echo true;
            imports("orderLogs.lib");
            $oLogs = new OrderLogs();
            $oLogs->_record(array(
                'order_id' => $order_info['order_id'],
                'op_id'    => $order_info['user_id'],
                'op_name'  => $order_info['user_name'],
                'behavior' => 'create',
                'remark'   => '支付宝支付成功，支付单号为:'.$order_info['out_trade_sn'],
            ));
        }
        
        
        
       /*  $order_type  =& ot("normal");
        $res =$order_type->respond_notify($data["out_trade_no"]);    //响应通知 */
        return $changeRes;
    }
    
    /**
     *    改变订单状态
     *
     *    @author    yhao.bai
     *    @param     int $order_id
     *    @param     string $order_type
     *    @param     array  $notify_result
     *    @return    void
     */
    function _change_order_status($order_id, $order_type = "normal", $notify_result)
    {
        /* 将验证结果传递给订单类型处理 */
        $order_type  =& ot($order_type);
        return $order_type->respond_notify($order_id, $notify_result);    //响应通知
    
    }
    
    public function lastSomeThing($order_id = ''){
    
// m('error')->add(array('content'=>7));
        $mFom = &m('figureorderm');
        $fom = $mFom->get("order_id = '{$order_id}'");
        if($fom && $fom['server_id']){
// m('error')->add(array('content'=>8));
            $mServer = &m('serve');
            $sv = $mServer->get($fom['server_id']);
    
            $mMem = &m('member');
            $dz = $mMem->get($sv['userid']);
    
            if($dz['user_token']){
                //量体订单提交之后发消息
                include(ROOT_PATH . '/includes/xinge/xinggemeg.php');
                $push = new XingeMeg();
                //$noon = array('am'=>'上午','pm'=>'下午');
                //$msg = "尊敬的客户，您预约{$fom['time']} {$noon[$fom['time_noon']]}到{$sv['serve_name']}量体，请做好出行安排，店铺地址：{$sv['serve_address']}，联系电话：{$sv['store_mobile']}【麦富迪】";
                $push->toMasterXinApp($dz['user_token'], '【mfd】预约量体提醒', '你有新的订单需要指派，订单号 - '.$fom['order_sn'], array('url_type'=>'figure', 'location_id'=>$fom['id']));
                //$push->toMasterXinApp($dz['user_token'], '【mfd】预约量体提醒', $msg, array('url_type'=>'figure', 'location_id'=>$fom['id']));
            }
            if($fom['phone'] || preg_match('/^1[34578][0-9]{9}$/', $fom['phone'])){
                $noon = array('am'=>'上午','pm'=>'下午');
    
                $sms_msg = "您预约".$fom['time'].$noon[$fom['time_noon']]."到".$sv['serve_name']."量体，地址：".$sv['serve_address']."，电话：".$sv['store_mobile'];
                $rs = SendSms($fom['phone'], $sms_msg);
    
                if($rs && $rs > 0){
                    $mFom->edit("order_sn= '{$fom['order_sn']}' AND has_sms = '0'" , array('has_sms' => '1'));
                }
            }
    
        }
    }
    
}
?>
