<?php

class EdbLib
{
    private $dbhost = 'edb_a83196';//主账号(正式时让EDB分配)
    private $appkey = 'b840ee29';//(正式时让EDB分配)
    private $appscret = 'fca2c05de0064cb88f753577422ea67f';//(正式时让EDB分配)
    private $token = 'a2e5033781484efe944ea0792a9c6dda';//(正式时让EDB分配)   //更换a2e5033781484efe944ea0792a9c6dda为5dba2179640e4b2d8ffbfb2ffc052590
    private $serviceUrl = 'http://vip3204.edb08.com.cn/rest/index.aspx';//服务器地址(正式时让EDB分配)
	//http://vip3006.edb09.net/rest/index.aspx
   //访问E店宝接口用到的信息如何获取？ --> http://vip13.edb08.com.cn/mongolog/EAOPHandler.ashx?_id=94b0473980374bfb9c8d395aec73dc67 
   // private $dbhost = 'edb_a99999';//主账号(正式时让EDB分配)
   // private $appkey = 'c184567b';//(正式时让EDB分配)
   // private $appscret = '90353b57f17a4bf6a11263f0545ddbdc';//(正式时让EDB分配)
   // private $token = 'e6513e432b724720ae6b6ab4155e6ccb';//(正式时让EDB分配)
   // private $serviceUrl = 'http://qimen.6x86.net:10537/restxin/index.aspx';//服务器地址(正式时让EDB分配)
    /*
     * 封装常用的参数
     * $method 接口名
     */
    public function edbGetCommonParams($method)
    {
        $params = array();
        $params['method'] = $method;
        $params['dbhost'] = $this->dbhost;
        $params['appkey'] = $this->appkey;
        $params['format'] = 'JSON';
        $params['timestamp'] = date('YmdHi');//timestamp 全小写
        $params['v'] = '2.0';
        $params['slencry'] = '1';
        $params['ip'] = '192.168.1.153';// 本机ip(没有限制时不用改)
        return $params;
    }

    /*
     * MD5加密
     * $params 要传递的参数是一个array(键-值对)
     * $appscret 由EDB提供的appscret
     * $token 由EDB提供的token
     */
    public function edbSignature($params)
    {
        $temArr = $params;
        $temArr['appscret'] = $this->appscret;
        $temArr['token'] = $this->token;
        ksort($temArr, SORT_STRING | SORT_FLAG_CASE); // 按照键名对关联数组进行升序排序
        $paramsStr = $this->appkey; // 把所有参数名和参数值串在一起
        foreach ($temArr as $key => $value) {
            if ($key && strlen($value) > 0) {
                $paramsStr = $paramsStr . $key . $value;
            }
        }
       // echo '<br/>加密内容：<br/>' . htmlentities($paramsStr) . '<br/><br/>';
        return strtoupper(md5($paramsStr));
    }

    /*
     * 调用EDB的服务
     * $url 由EDB提供服务器地址(可以在EDB客户端查看：帮助)
     * $params 要传递的参数是一个array(键-值对)
     * $appscret 由EDB提供的appscret
     * $token 由EDB提供的token
     */
    public function edbRequstPost($params)
    {	
      
		//echo '<br/>serviceUrl：' .$this->serviceUrl;
       // echo '<br/><br/>发送报文：<br/>' .htmlentities($this->getStr($params)) . '<br/><br/>';
        $params['sign'] = $this->edbSignature($params);
		if(isset($params['xmlValues']))$params['xmlValues']=urlencode($params['xmlValues']);
		//if($params['xmlValues']!=null)
		//if($params['xmlvalues']!=null)$params['xmlvalues']=urlencode($params['xmlvalues']);
        $data = http_build_query($params);  
      //  echo '<br/>实际发送参数：<br/>' . $data . '<br/><br/>';
        //$context = stream_context_create($opts);
        //$html = file_get_contents($this->serviceUrl, false, $context);
        $oCurl = curl_init();
        //var_dump($oCurl);exit;
        curl_setopt($oCurl, CURLOPT_URL, $this->serviceUrl);
        curl_setopt($oCurl, CURLOPT_POST, true);
        curl_setopt($oCurl,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS,$data);
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencodedrn',
            'Content-Length: ' . strlen($data))
        );
      
       $res = curl_exec($oCurl);
    
       $html = curl_exec($oCurl);
        //$aStatus  = curl_getinfo($oCurl);
        curl_close($oCurl);
        //if (intval($aStatus["http_code"]) == 200) {} 
		//echo '<br/>回应内容=' .setype($html, 'string');
		//var_dump($html);exit;
        return $html;
    }  
    
    private function getStr($params){
        $result='';
        foreach ($params as $key => $value) {
             $result = $result . $key .'='. $value.'&';
        }
        return substr($result, 0,strlen($result)-1);
    }
}
function object_array($results) {
    if(is_object($results)) {
        $results = (array)$results;
    } if(is_array($results)) {
        foreach($results as $key=>$value) {
            $results[$key] = object_array($value);
        }
    }
    return $results;
}
?>