<?php
/**
 *    银联在线支付ChinaPay接口
 *
 *    @author Ruesin
 */
class ChinapayPayment extends BasePayment{

    public $display_name = '银联在线支付ChinaPay';
    public $curname = 'CNY';
    public $ver = '1.0';
    public $platform = 'ispc'; ////当前支付方式所支持的平台
    public $supportCurrency = array("CNY"=>"156");// 扩展参数
    protected $fields = array();
    protected $callback_url;
    protected $submit_url;
    protected $submit_method;
    protected $submit_charset;
    protected $arrayCurrencyOptions = array(); /////支持的货币
    public $setting;
    public function __construct(){
        //parent::__construct();
        $this->callback_url  = '';
        $this->servercallback_url = '';
        $this->submit_url = 'https://payment.ChinaPay.com/pay/TransGet';
        if(defined('CHINAPAY')){
            $this->submit_url = "http://payment-test.ChinaPay.com/pay/TransGet";//测试
        }
        $this->submit_method = 'POST';
        $this->submit_charset = 'utf-8';
        
        $this->arrayCurrencyOptions = array(
                '1'=>'人民币',
                '2'=>'其他',
                '3'=>'商店默认货币',
                '4'=>'人民币与其他币种',
                '5'=>'新台币',
        );
        $this->setting = array(
                'pay_code'=>'chinapay',
                'pay_name'=>'银联在线支付ChinaPay',//支付方式名称
                'mer_id'=>'', //客户号
                'pub_Pk'=>'', //公钥文件
                'mer_key'=>'',//私钥文件
                'pay_fee'=>'',//交易费率
                'support_cur'=>'',//支持币种
                'pay_desc'=>'',//描述
                'pay_type'=>'',//支付类型(是否在线支付)
                'status'=>'',//是否开启此支付方式
        );
    }
    public function dopay($payment){
        
        $mer_id = $this->setting['mer_id'];  ////客户号
        
        $TransType = '0001';
        if (!$payment['t_begin'])
            $payment['t_begin'] = time();  ////时间
        $ordId = $this->intString(substr($mer_id, -5) . substr(date("YmdHis",$payment['t_begin']), -7), 16);////截取字符串 拼接够16位长度
        $payment['cur_money'] = $this->intString($payment['cur_money']*100, 12);
    
        $arr_validate_data = array(
                'merid' => $mer_id,
                'orderno' => $ordId,
                'amount' => $payment['cur_money'],
                'currencycode' => $this->supportCurrency[$payment['currency']],//// 扩展参数 array("CNY"=>"156");
                'transdate' => date("Ymd", $payment['t_begin']),////
                'transtype' => $TransType,////
                'payment_id'=>$payment['payment_id'],////
        );
        $chkvalue = $this->_get_mac($arr_validate_data, 'sign');  ////生成检查签名
    
        switch ($chkvalue){
            case '-100':
                $errinfo='环境变量"NPCDIR"未设置';
                break;
            case '-101':
                $errinfo='商户密钥文件不存在或无法打开';
                break;
            case '-102':
                $errinfo='密钥文件格式错误';
                break;
            case '-103':
                $errinfo='秘钥商户号和用于签名的商户号不一致';
                break;
            case '-130':
                $errinfo='用于签名的字符串长度为空';
                break;
            case '-111':
                $errinfo='没有设置秘钥文件路径，或者没有设置“NPCDIR”环境变量';
                break;
            default:
                break;
        }
        if ($errinfo){
            header("Content-Type:text/html;charset=utf-8");
            echo $errinfo;
            exit;
        }
        
        $this->fields['MerId'] = $mer_id;   //商户ID
        $this->fields['OrdId'] = $ordId;    //订单ID 对外
        $this->fields['TransAmt'] = $payment['cur_money'];//交易金额
        $this->fields['CuryId'] = '156';  ///货币代码
        $this->fields['TransDate'] = date("Ymd", $payment['t_begin']);//交易日期
        $this->fields['TransType'] = $TransType;//交易类型
        $this->fields['Version'] = '20070129';//接口版本号
        $this->fields['BgRetUrl'] = $this->servercallback_url;//后台交易接收URL，长度不要超过80个字节
        $this->fields['PageRetUrl'] = $this->callback_url;//页面交易接收URL，长度不要超过80个字节
        $this->fields['GateId'] = '';//支付网关号，可选
        $this->fields['Priv1'] = $payment['payment_id'];//商户私有域，可选，长度不要超过60个字节//todo:需要在订单生成的时候做转换，主要用于外币支付时,紧做显示用不参与交易
        $this->fields['ChkValue'] = $chkvalue;//CheckValue[256] - 即NetPayClient根据上述输入参数生成的商户数字签名，长度为256字节的字符串。 

        echo $this->get_html();exit;
    }

    /**
    * 生成检查签名
    * @param mixed $form 包含签名数据的数组
    * @param string $method 生成用途
    * @access private
    * @return string
    */
    private function _get_mac($data, $method='sign'){
        $MerPrk = $this->setting['mer_key'];   ////密钥
        $PubPk = $this->setting['pub_Pk'];    ////公钥
    
        if (strtoupper(substr(PHP_OS,0,3))=="WIN"){
            if (file_exists(DATA_DIR . '/cert/payment_plugin_chinapay/' . $MerPrk)&&file_exists(DATA_DIR . '/cert/payment_plugin_chinapay/' . $PubPk)){
                buildKey(DATA_DIR . '/cert/payment_plugin_chinapay/' . $MerPrk);    ////buildKey  生成密钥
            }
            if ($method=='sign'){
                $res = $this->_get_mac_sign($data, $chinapay);///???$chinapay
            }else{
                $res = $this->_get_mac_check($data, $chinapay);
            }
        }else{
            if (file_exists(DATA_DIR . '/cert/payment_plugin_chinapay/' . $MerPrk)&&file_exists(DATA_DIR . '/cert/payment_plugin_chinapay/' . $PubPk)){
                buildKey(DATA_DIR . '/cert/payment_plugin_chinapay/' . $MerPrk);   ////buildKey  生成密钥
            }
            if ($method=='sign')
                $res = $this->_get_mac_sign($data);    ////生成发送验证签名
            else
                $res = $this->_get_mac_check($data);
        }
        return $res;
    }
    
    /**
    * 生成检查签名
    * @param mixed $form 包含签名数据的数组
    * @param mixed $chinapay com组件对象
    * @access private
    * @return string
    */
    private function _get_mac_check($data, $chinapay=null){
		if (is_null($chinapay)){
		    $res = verifyTransResponse($data['merid'], $data['orderno'], $data['amount'], $data['currencycode'], $data['transdate'], $data['transtype'], $data['status'], $data['checkvalue']);
		}else{
		    $res = $chinapay->check($data['merid'], $data['orderno'], $data['amount'], $data['currencycode'], $data['transdate'], $data['transtype'], $data['status'], $data['checkvalue']);
		}
    return $res;
    }
    
    /**
    * 生成发送验证签名
    * @param mixed $form 包含签名数据的数组
    * @param mixed $chinapay com组件对象
    * @access private
    * @return string
    */
	private function _get_mac_sign($data, $chinapay=null){
		//商户号，订单号，交易金额，货币代码，交易日期，交易类型
        if (is_null($chinapay)){
            $chkvalue = sign($data['merid'].$data['orderno'].$data['amount'].$data['currencycode'].$data['transdate'].$data['transtype'].$data['payment_id']);
        }else{
            $chkvalue = sign($data['merid'].$data['orderno'].$data['amount'].$data['currencycode'].$data['transdate'].$data['transtype'].$data['payment_id']);
        }
        return $chkvalue;
    }
    
    
    /**
     * 支付后返回后处理的事件的动作
     * @params array - 所有返回的参数，包括POST和GET
     * @return null
     */
    public function callback(&$recv){
        #键名与pay_setting中设置的一致
        $mer_id = $this->setting['mer_id'];
        $ret['payment_id'] = $recv['Priv1'];
        $ret['account'] = $mer_id;
        $ret['bank'] = app::get('chinapay')->_('银联在线支付ChinaPay');
        $ret['pay_account'] = app::get('ectools')->_('付款帐号');
        $ret['currency'] = array_search($recv["currencycode"], $this->supportCurrency);
        $ret['money'] = intval($recv['amount'])/100;;
        $ret['paycost'] = '0.000';
        $ret['cur_money'] = intval($recv['amount'])/100;
        $ret['trade_no'] = '';
        $ret['t_payed'] = time();
        $ret['pay_app_id'] = "chinapay";
        $ret['pay_type'] = 'online';
        $ret['memo'] = '';
    
        if ($this->is_return_vaild($recv, '')){
            if ($recv['status']=="1001"){
                $message = "支付成功！";
                $ret['status'] = 'succ';
            }else{
                $message = "支付失败！";
                $ret['status'] = 'failed';
            }
        }else{
            $message = "验证签名错误！";
            $ret['status'] = 'invalid';
        }
    
        return $ret;
    }
    
    /**
    * 检验返回数据合法性
    * @param mixed $form 包含签名数据的数组
    * @param mixed $key 签名用到的私钥
    * @access private
    * @return boolean
    */
    public function is_return_vaild($form,$key){
        $res = $this->_get_mac($form, 'check');
        if ($res == '0')
            return true;
        #记录返回失败的情况
        if (SHOP_DEVELOPER){
    		logger::info($signstr);
        }
        return false;
    }
    
	/**
	 * 截取相应长度和本身字符串长度的差额对应的字符串
	 * @param string 被截取字符串
	 * @param int 长度
	 */
	private function intString($intvalue,$len){
        $intstr = strval($intvalue);
        for ($i = 1; $i <= $len-strlen($intstr); $i++){
            $tmpstr .= "0";
        }
        return $tmpstr . $intstr;
    }

    /**
     * 生成支付方式提交的表单的请求
     * @params null
     * @return string
     */
    protected function get_html()
    {
        // 简单的form的自动提交的代码。
        header("Content-Type: text/html;charset=".$this->submit_charset);
        $strHtml ="<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">
	<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en-US\" lang=\"en-US\" dir=\"ltr\">
	<head>
	</head><body><div>Redirecting...</div>";
        $strHtml .= '<form action="' . $this->submit_url . '" method="' . $this->submit_method . '" name="pay_form" id="pay_form">';
    
        // Generate all the hidden field.
        foreach ($this->fields as $key=>$value)
        {
            $strHtml .= '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
        }
    
        $strHtml .= '<input type="submit" name="btn_purchase" value="购买" style="display:none;" />';
        $strHtml .= '</form><script type="text/javascript">
					window.onload=function(){
						document.getElementById("pay_form").submit();
					}
				</script>';
        $strHtml .= '</body></html>';
        return $strHtml;
    }
    
}
