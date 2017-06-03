<?php

class AccesstokenModel extends BaseModel
{
    var $table  = 'access_token';
    var $prikey = 'id';
    var $_name  = 'accesstoken';

    /* 表单自动验证 */
    var $_autov = array(

    );

    /* 关系列表 */
    var $_relation  = array(


    );

    public function settoken()
    {
        require_once ROOT_PATH."/h5/weipay/lib/WxPay.Config.php";
        $token = "";
        $appid = WxPayConfig::APPID;
        $appsecret = WxPayConfig::APPSECRET;
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
        $res = https_request($url);
        if($res['access_token'] && $res['expires_in'])
        {
            $token = $res['access_token'];
            $expire_time = time() + $res['expires_in']  - 30;//提前一分钟就刷新 accesstoken
            $this->edit("id = 1",['token'=>$res['access_token'],'expire_time'=>$expire_time]);
        }
        return $token;
    }

    public function get_token()
    {
        require_once ROOT_PATH."/h5/weipay/lib/WxPay.Config.php";
        $appid = WxPayConfig::APPID;
        $appsecret = WxPayConfig::APPSECRET;
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
        $token = '';
        $token_info = $this->get("id = 1");
        if($token_info)
        {
            $expire_time = $token_info['expire_time'];
            if(time() > $expire_time) //过期
            {
                $res = https_request($url);
                if($res['access_token'] && $res['expires_in'])
                {
                    $token = $res['access_token'];
                    $expire_time = time() + $res['expires_in']  - 30;//提前一分钟就刷新 accesstoken
                    $this->edit("id = 1",['token'=>$res['access_token'],'expire_time'=>$expire_time]);
                }

            }
            else
            {
                $token = $token_info['token'];
            }

        }
        else
        {
            $res = https_request($url);
            if($res['access_token'] && $res['expires_in'])
            {
                $expire_time = time() + $res['expires_in']  - 30;//提前一分钟就刷新 accesstoken
                $token = $res['access_token'];
                $this->add(['id'=>1,'token'=>$res['access_token'],'expire_time'=>$expire_time]);
            }
        }
        return $token;
    }




}

?>