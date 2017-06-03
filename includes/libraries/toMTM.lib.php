<?php

class toMTM extends Object
{
    var $url;
    var $fabUrl;
    var $pubkey;
    var $prekey;
    function __construct()
    {
       $this->url    = "https://api.rcmtm.com:443/order-api/resources/orderService";
       $this->fabUrl = "https://api.rcmtm.com:443/order-api/resources/fyerp/";
       $this->pubkey = ROOT_PATH. '/data/public.pem';
       $this->prekey = ROOT_PATH. '/data/key.pem';
    }

    function sync($vars){
        $ch = curl_init();
    
        //curl_setopt($ch,CURLOPT_VERBOSE,'1');
        curl_setopt($ch,CURLOPT_TIMEOUT,30);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_URL, $this->url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLCERT,$this->pubkey);
    
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY,$this->prekey);
    
        $aHeader = array(
            "user:ALITAILOR",
            "pwd:". md5("ALITAILOR"),
            'lan:zh',
            'Content-Type:application/xml',
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
    
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
    
        $data = curl_exec($ch);
        if (curl_error($ch)){
            $rs = array('err'=>1,'msg'=>"curl error:".curl_error($ch) );//-1:代表CURL请求过程中的错误~
        }else{
            $rs = array('err'=>0,'data'=>json_decode($data,1));
        }
        curl_close($ch);
        //echo iconv("UTF-8","GBK", $data);
        return $rs;
    
    }
    
    function fabric($fabric){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_TIMEOUT,30);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_URL, $this->fabUrl.$fabric);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLCERT,$this->pubkey);
        
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY,$this->prekey);
        
        $aHeader = array(
            "user:ALITAILOR",
            "pwd:". md5("ALITAILOR"),
            'lan:zh',
            'Content-Type:application/xml',
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        
        curl_setopt($ch,CURLOPT_POST, 0);
        
        $data = curl_exec($ch);
        if (curl_error($ch)){
            $rs = array('code'=>10000,'msg'=>"curl error:".curl_error($ch) );//-1:代表CURL请求过程中的错误~
        }else{
            $rs = json_decode($data,1);
            if(!isset($rs["code"])){
                $_f = current($rs);
                $rs["code"] = "101";
                $rs['rs']  = $_f;
            }
        }
        curl_close($ch);
        //echo iconv("UTF-8","GBK", $data);
        return $rs;
    }
}
?>
