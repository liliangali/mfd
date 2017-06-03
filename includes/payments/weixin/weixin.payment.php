<?php
/**
 * PC端微信扫码支付
 * 
 * @date 2015-9-16 下午1:26:21
 * @author Ruesin
 */
class WeixinPayment extends BasePayment
{
    
    // =======【基本信息设置】=====================================
    //
    /**
     * TODO: 修改这里配置为您自己申请的商户信息
     * 微信公众号信息配置
     *
     * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
     *
     * MCHID：商户号（必须配置，开户邮件中可查看）
     *
     * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
     * 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
     *
     * APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），
     * 获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
     * 
     * @var string
     */
    const APPID = 'wxe6318bcee2d90af6';
    const MCHID = '1269191601';
    const KEY = 'abcdefghijklmnopqrstuvwxyz123456';
    const APPSECRET = '16b7e879793c28efc5feb5ca854ed304';
    
    // =======【证书路径设置】=====================================
    /**
     * TODO：设置商户证书路径
     * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
     * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
     * 
     * @var path
     */
    const SSLCERT_PATH = '../cert/apiclient_cert.pem';
    const SSLKEY_PATH = '../cert/apiclient_key.pem';
    
    // =======【curl代理设置】===================================
    /**
     * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
     * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
     * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
     * 
     * @var unknown_type
     */
    const CURL_PROXY_HOST = "0.0.0.0"; // "10.152.18.220";
    const CURL_PROXY_PORT = 0; // 8080;
                               
    // =======【上报信息配置】===================================
    /**
     * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
     * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
     * 开启错误上报。
     * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
     * 
     * @var int
     */
    const REPORT_LEVENL = 1;
    
    const NOTIFY_URL = '/paynotifys-weixin.html';//'http://paysdk.weixin.qq.com/example/notify.php';
    
    public static $_error = '';
    public static $_msg = 'OK'; //响应信息
    
    
    /**
     * 创建支付单
     * 
     * @date 2015-9-16 下午1:26:09
     * @author Ruesin
     */
    function createBill($order){
        $payment_sn = $this->get_payment_sn();
        $bill = array(
                'payment_sn' => $payment_sn, //支付单号
                'amount'     => $order['final_amount'], //支付金额
                'member_id'  => $order['user_id'],  //会员id
                //'status'     => '', //返回状态
                'account'    => self::APPID,  //支付帐号
                'ip'         => real_ip(), //IP地址
                'start_time' => time(), //支付开始时间
                //'end_time' => '', //完成时间
                'order_sn'   => $order['order_sn'],
                'pay_id'     => $order['payment_id'],
                'pay_code'   => $order['payment_code'],
                'pay_name'   => $order['payment_name'],
                //'buyer_name' => '', //付款账户
        );
         
        $this->out_trade = $payment_sn;   ///外部交易号
        $res = $this->_mod_bills->add($bill);
        if(!$res){
            return false;
        }
        $res = $this->_mod_order->edit("order_id='{$order['order_id']}'" ,array("out_trade_sn" => $payment_sn));
        if(!$res){
            return false;
        }
        return true;
    }

    /**
     * 获取支付链接二维码
     * 
     * @date 2015-9-16 下午1:34:07
     * @author Ruesin
     */
    function get_payform($order_info, $type ="order")
    {
        $return_url = SITE_URL.'/paynotify-weixin-'.$order_info['order_sn'].'.html';
        if ($type == 'account') 
        {
            $return_url = SITE_URL.'/paynotify-weixin_'.$type.'-'.$order_info['order_sn'].'.html';
        }
        $start = time();
        $expire = $start+7200;
        $form =  array(
                'appid'        => self::APPID,  //公众账号ID
                'openid'       => '',  // 设置trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。下单前需要调用【网页授权获取用户信息】接口获取到用户的Openid。
                'mch_id'       => self::MCHID,//商户号
                'body'         => "麦富迪订单 - ".$order_info['order_sn'],   //商品或支付单简要描述
                'attach'       => '微信扫码支付订单。',   //附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
                'out_trade_no' => $this->_get_trade_sn(),  //设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
                'total_fee'    => ceil($order_info['final_amount'] * 100),    //订单总金额，只能为整数，详见支付金额
                'time_start'   => date('YmdHis',$start),  //订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。其他详见时间规则
                'time_expire'  => date('YmdHis',$expire),  //订单失效时间 time_expire时间过短，刷卡至少1分钟，其他5分钟
                'goods_tag'    => 'mfd',   //商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠
                'notify_url'   => $return_url,//'http://paysdk.weixin.qq.com/example/notify.php';,  //接收微信支付异步通知回调地址
                'trade_type'   => 'NATIVE',  // JSAPI，NATIVE，APP，详细说明见参数规定
                'product_id'   => $order_info['order_id'],  //设置trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
                'spbill_create_ip' => real_ip(),//$_SERVER['REMOTE_ADDR'],//终端ip
                'nonce_str'    => self::getNonceStr(),// 设置随机字符串，不长于32位。推荐随机数生成算法 // 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
                'sign'         => '', //设置签名，详见签名生成算法
        );
        //var_dump($form);
        $result = $this->unifiedOrder($form);
        if ($type == 'account') 
        {
            return urlencode($result["code_url"]);
        }
        echo ''.
        '<img alt="模式二扫码支付" src="http://paysdk.weixin.qq.com/example/qrcode.php?data='.urlencode($result["code_url"]).'" style="width:150px;height:150px;"/>';
        exit;
    }
    
    static function error($msg){
        header("Content-Type:text/html;charset=utf-8");
        echo $msg;
        die();
    }
    
    /**
     *
     * 统一下单，WxPayUnifiedOrder中out_trade_no、body、total_fee、trade_type必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayUnifiedOrder $inputObj
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    protected function unifiedOrder($input, $timeOut = 6)
    {
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        //检测必填参数
        if(!$input['out_trade_no']) 
            self::error("缺少统一支付接口必填参数out_trade_no！");
        if(!$input['body'])
            self::error("缺少统一支付接口必填参数body！");
        if(!$input['total_fee'])
            self::error("缺少统一支付接口必填参数total_fee！");
        if(!$input['trade_type'])
            self::error("缺少统一支付接口必填参数trade_type！");
        
        //关联参数
        if($input['trade_type'] == "JSAPI" && !$input['openid'])
            self::error("统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！");
        
        if($input['trade_type'] == "NATIVE" && !$input['product_id'])
            self::error("统一支付接口中，缺少必填参数product_id！trade_type为JSAPI时，product_id为必填参数！");
        
        $input['sign'] = $this->MakeSign($input);
        
        $xml = $this->ArrayToXml($input);
        
        $startTimeStamp = self::getMillisecond();//请求开始时间
        $timeOut = 30;
        $response = $this->postXmlCurl($xml, $url, false, $timeOut);
        
        $result = self::XmlToArray($response);
        //self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
        return $result;
    }
    /**
     * 生成签名
     */
    public function MakeSign($values)
    {
        //签名步骤一：按字典序排序参数
        ksort($values);
        $string = self::ToUrlParams($values);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".self::KEY;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }
    /**
     * 格式化参数格式化成url参数
     */
    public function ToUrlParams($values)
    {
        $buff = "";
        foreach ($values as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }
    
        $buff = trim($buff, "&");
        return $buff;
    }
    /**
     * 输出xml字符
     **/
    public function ArrayToXml($values)
    {
        if(!is_array($values) || count($values) <= 0)
            self::error("数组数据异常！");
         
        $xml = "<xml>";
        foreach ($values as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }
    
    /**
     * 获取毫秒级别的时间戳
     */
    private static function getMillisecond()
    {
        $time = explode ( " ", microtime () );
        $time = $time[1] . ($time[0] * 1000);
        $time2 = explode( ".", $time );
        $time = $time2[0];
        return $time;
    }
    
    /**
     * 以post方式提交xml到对应的接口url
     *
     * @param string $xml  需要post的xml数据
     * @param string $url  url
     * @param bool $useCert 是否需要证书，默认不需要
     * @param int $second   url执行超时时间，默认30s
     * @throws WxPayException
     */
    private function postXmlCurl($xml, $url, $useCert = false, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
    
        //如果有配置代理这里就设置代理
        if(self::CURL_PROXY_HOST != "0.0.0.0" && self::CURL_PROXY_PORT != 0){
            curl_setopt($ch,CURLOPT_PROXY, self::CURL_PROXY_HOST);
            curl_setopt($ch,CURLOPT_PROXYPORT, self::CURL_PROXY_PORT);
        }
        
        curl_setopt($ch,CURLOPT_URL, $url);
        //curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
        //curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);//严格校验2
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
        if($useCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, WxPayConfig::SSLCERT_PATH);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, WxPayConfig::SSLKEY_PATH);
        }
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            self::error("curl出错，错误码:$error");
        }
    }
    
    /**
     * 将xml转为array
     */
    public static function XmlToArray($xml)
    {
        if(!$xml)
            self::error('xml数据异常！');
        
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        
        if($values['return_code'] != 'SUCCESS')
            return $values;
        
        self::CheckSign($values);
        return $values;
    }
    
    /**
     * 检测签名
     */
    public function CheckSign($values)
    {
        if(!$values['sign'])
            self::error("签名错误！");
    
        $sign = self::MakeSign($values);
        if($values['sign'] == $sign)
            return true;
        
        self::error("签名错误！");
    }
    
    /**
     * 上报数据， 上报的时候将屏蔽所有异常流程
     */
    private static function reportCostTime($url, $startTimeStamp, $data)
    {
        //如果不需要上报数据
        if(self::REPORT_LEVENL == 0)
            return;
        return ;
        //如果仅失败上报
        if(self::REPORT_LEVENL == 1 &&
                array_key_exists("return_code", $data) &&
                $data["return_code"] == "SUCCESS" &&
                array_key_exists("result_code", $data) &&
                $data["result_code"] == "SUCCESS")
        {
            return;
        }
        	
        //上报逻辑
        $endTimeStamp = self::getMillisecond();
        $input = array(
                'interface_url' => $url,  //设置上报对应的接口的完整URL，类似：https://api.mch.weixin.qq.com/pay/unifiedorder对于被扫支付，为更好的和商户共同分析一次业务行为的整体耗时情况，对于两种接入模式，请都在门店侧对一次被扫行为进行一次单独的整体上报，上报URL指定为：https://api.mch.weixin.qq.com/pay/micropay/total关于两种接入模式具体可参考本文档章节：被扫支付商户接入模式其它接口调用仍然按照调用一次，上报一次来进行。
                'execute_time_' => $endTimeStamp - $startTimeStamp,//设置接口耗时情况，单位为毫秒
        );
        //返回状态码  SUCCESS/FAIL 通信标识，非交易标识，交易是否成功需要查看trade_state来判断
        if(array_key_exists("return_code", $data)){
            $input['return_code'] = $data["return_code"];
        }
        //返回信息 如非空，为错误原因签名失败参数格式校验错误
        if(array_key_exists("return_msg", $data)){
            $input['return_msg'] = $data["return_msg"];
        }
        //业务结果 SUCCESS/FAIL
        if(array_key_exists("result_code", $data)){
            $input['result_code'] = $data["result_code"];
        }
        //错误代码 ORDERNOTEXIST 订单不存在 SYSTEMERROR 系统错误
        if(array_key_exists("err_code", $data)){
            $input['err_code'] = $data["err_code"];
        }
        //错误代码描述
        if(array_key_exists("err_code_des", $data)){
            $input['err_code_des'] = $data["err_code_des"];
        }
        //商户订单号 设置商户系统内部的订单号,商户可以在上报时提供相关商户订单号方便微信支付更好的提高服务质量。
        if(array_key_exists("out_trade_no", $data)){
            $input['out_trade_no'] = $data["out_trade_no"];
        }
        //设备号 微信支付分配的终端设备号，商户自定义
        if(array_key_exists("device_info", $data)){
            $input['device_info'] = $data["device_info"];
        }
        self::report($input);
    }
    
    /**
     *
     * 测速上报，该方法内部封装在report中，使用时请注意异常流程
     * WxPayReport中interface_url、return_code、result_code、user_ip、execute_time_必填
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayReport $inputObj
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public static function report($input, $timeOut = 1)
    {
        $url = "https://api.mch.weixin.qq.com/payitil/report";
        //检测必填参数
        if(!array_key_exists('interface_url', $input))
            self::error("接口URL，缺少必填参数interface_url！");
        if(!array_key_exists('return_code', $input))
            self::error("返回状态码，缺少必填参数return_code！");
        if(!array_key_exists('result_code', $input))
            self::error("业务结果，缺少必填参数result_code！");
        if(!array_key_exists('user_ip', $input))
            self::error("访问接口IP，缺少必填参数user_ip！");
        if(!array_key_exists('execute_time_', $input))
            self::error("接口耗时，缺少必填参数execute_time_！");
        
        $input['appid']   = self::APPID;//公众账号ID
        $input['mch_id']  = self::MCHID;//商户号
        $input['user_ip'] = $_SERVER['REMOTE_ADDR'];//终端ip
        $input['time']    = date("YmdHis");//商户上报时间
        $input['nonce_str'] = self::getNonceStr();//随机字符串 设置随机字符串，不长于32位。推荐随机数生成算法
        
        
        $input['sign'] = $this->MakeSign($input);
        $xml = $this->ArrayToXml($input);
        
        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url, false, $timeOut);
        return $response;
    }
    
    /**
     *
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return 产生的随机字符串
     */
    public static function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }
    
    
    
    
    //================================================================//
    //================================================================//
    //================================================================//
    //================================================================//
    
    /**
     * 
     * 返回状态码 return_code  SUCCESS/FAIL,此字段是通信标识，非交易标识，交易是否成功需要查看result_code来判断
     * 返回信息 	return_msg   返回信息，如非空，为错误原因;签名失败;具体某个参数格式校验错误.
     * 公众账号ID  appid 微信分配的公众账号ID
     * 商户号 	mch_id 微信支付分配的商户号
     * 随机字符串   nonce_str   微信返回的随机字符串
     * 预支付ID  prepay_id 调用统一下单接口生成的预支付ID
     * 业务结果  result_code  SUCCESS/FAIL
     * 错误描述  err_code_des  当result_code为FAIL时，商户展示给用户的错误提
     * 签名   sign  返回数据签名，签名生成算法
     * @date 2015-10-22 下午4:54:35
     * @author Ruesin
     */ 
    
    public function verify_notify($order_info){
        
        $msg = "OK";
        //当返回false的时候，表示notify中调用NotifyCallBack回调失败获取签名校验失败，此时直接回复失败
//         $xml = '<xml>
// <appid><![CDATA[wxe6318bcee2d90af6]]></appid>
// <attach><![CDATA[这个是自定义数据]]></attach>
// <bank_type><![CDATA[CFT]]></bank_type>
// <cash_fee><![CDATA[1]]></cash_fee>
// <fee_type><![CDATA[CNY]]></fee_type>
// <is_subscribe><![CDATA[Y]]></is_subscribe>
// <mch_id><![CDATA[1269191601]]></mch_id>
// <nonce_str><![CDATA[1509070506s]]></nonce_str>
// <openid><![CDATA[ojDZowEJGBkFmT6RK4mtdVLW4k7c]]></openid>
// <out_trade_no><![CDATA[out_12313s12saa]]></out_trade_no>
// <result_code><![CDATA[SUCCESS]]></result_code>
// <return_code><![CDATA[SUCCESS]]></return_code>
// <sign><![CDATA[A3645FC4C0177F5737E53F88F279DC9B]]></sign>
// <time_end><![CDATA[20150916091528]]></time_end>
// <total_fee>1</total_fee>
// <trade_type><![CDATA[NATIVE]]></trade_type>
// <transaction_id><![CDATA[1004910550201509160893327834]]></transaction_id>
// </xml>';
        
        
        //$xml = file_get_contents("php://input");
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        
        $data = self::XmlToArray($xml);
        
        $result = true;
        //查询微信订单
        if(!array_key_exists("transaction_id", $data)){
            self::$_msg = "输入参数不正确";
            return false;
        }
        $input['transaction_id'] = $data["transaction_id"];
        if(self::orderQuery($input) == false){
            self::$_msg = "订单查询失败";
            return false;
        }
        if($data['result_code'] == 'FAIL'){
            self::$_msg = $data['err_code_des'];
            return false;
        }
        
        //本地验证 ↓ Bgn
        if ($order_info['out_trade_sn'] != $data['out_trade_no'] && !$order_info['out_trade_sns'][$data['out_trade_no']])
        {
            self::$_msg = "order_inconsistent";
            return false;
        }

        if (ceil($order_info['final_amount'] * 100) != $data['total_fee'])
        {
            self::$_msg = "price_inconsistent";
            return false;
        }
        //本地验证 ↑ End
        //更新支付单
		$billdata = array(
    			'status'     => $data['result_code'],
    			'end_time'	 => gmtime(),
				'buyer_name' => '',
    		);

        $this->updateBill($data['out_trade_no'], $billdata);
        $order_status = ORDER_ACCEPTED;   //已付款
        return array(
            'target'    =>  $order_status,
        );
        
        return true;
    }
        
    /**
     * 查询订单，WxPayOrderQuery中out_trade_no、transaction_id至少填一个
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     */
    public static function orderQuery($input)
    {
        $url = "https://api.mch.weixin.qq.com/pay/orderquery";
        //检测必填参数
        if(!$input['out_trade_no'] && !$input['transaction_id']){
            self::$_msg = "订单查询接口中，out_trade_no、transaction_id至少填一个！";
            return false;
        }
        
        $input['appid']   = self::APPID;//公众账号ID
        $input['mch_id']  = self::MCHID;//商户号
        $input['nonce_str'] = self::getNonceStr();//随机字符串 设置随机字符串，不长于32位。推荐随机数生成算法

        $input['sign'] = self::MakeSign($input);
        $xml = self::ArrayToXml($input);
    
        $startTimeStamp = self::getMillisecond();//请求开始时间
        $response = self::postXmlCurl($xml, $url);
        $result = self::XmlToArray($response);
        self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
        
        if(!array_key_exists("return_code", $result) || !array_key_exists("result_code", $result) || $result["return_code"] != "SUCCESS" || $result["result_code"] != "SUCCESS")
            return false;
        
        return true;
        //return $result;
    }
    
    /**
     * 回复通知 1、微信回调超时时间为2s，建议用户使用异步处理流程，确认成功之后立刻回复微信服务器 2、微信服务器在调用失败或者接到回包为非确认包的时候，会发起重试，需确保你的回调是可以重入
     */
    public function verify_result($result , $needSign = false){
        
        if($result == true){
            $return['return_code'] = 'SUCCESS';
            $return['return_msg'] = 'OK';
        } else {
            $return['return_code'] = 'FAIL';
            $return['return_msg'] = self::$_msg;
        }
        
        //如果需要签名
        if($needSign == true && $return['return_code'] == "SUCCESS")
        {
            $return['sign'] = $this->MakeSign($return);
        }
        echo $this->ArrayToXml($return);
    }

    //================================================================//
    //================================================================//
    //================================================================//
   
}

