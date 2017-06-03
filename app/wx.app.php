<?php
use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\Text;
use Cyteam\Goods\Mp;
/* 申请品牌商 */
class Wxapp extends MallbaseApp
{
    public $option;
    function __construct()
    {
        return false;

        parent::__construct();
        $this->option =  array(
            'token' => APP_TOKEN, //填写第三方的key
            'encodingaeskey' => APP_ACE, //填写第三方加密用的EncodingAESKey
            'component_appid' => APP_ID, //填写第三方的app id
            'component_appsecret' => APPSECRET_ID, //填写第三方的密钥
            'authorizer_appid' => '',//根据需要初始化
            'component_verify_ticket' => "",//根据需要初始化11
        );

    }

    function index()
    {
        $wxconfig = m('wxconfig');
        $mpObj = new Mp($this->option);
        $mpObj->valid();
        $component_verify_ticket = $mpObj->getRev()->getRevComponentVerifyTicket();
        $info  = $wxconfig->get(array(
            'conditions' => "wxcode = 'tikct' ",
        ));
        if($info)
        {
            if($info['wxvalue'] == $component_verify_ticket)
            {
                return ;
            }
            $wxconfig->edit($info['id'],['wxvalue' => $component_verify_ticket]);
        }
        else
        {
            $wxconfig->add(['wxcode'=>'tikct','wxvalue'=>$component_verify_ticket,'add_time'=>time()]);
        }
        $component_access_token = $mpObj->checkComponentAuth();
        $info  = $wxconfig->get(array(
            'conditions' => "wxcode = 'component_access_token' ",
        ));
        if($info)
        {
            $wxconfig->edit($info['id'],['wxvalue' => $component_access_token]);
        }
        else
        {
            $wxconfig->add(['wxcode'=>'component_access_token','wxvalue'=>$component_access_token,'add_time'=>time()]);
        }

        $pre_auth_code = $mpObj->getPreAuthCode();
        $info  = $wxconfig->get(array(
            'conditions' => "wxcode = 'auth_code' ",
        ));
        if($info)
        {
            $wxconfig->edit($info['id'],['wxvalue' => $pre_auth_code]);
        }
        else
        {
            $wxconfig->add(['wxcode'=>'auth_code','wxvalue'=>$pre_auth_code,'add_time'=>time()]);
        }

    }

    public function wxthree()
    {
        $wxconfig = m('wxconfig');
        $info  = $wxconfig->get("wxcode = 'auth_code' ");
        if(!$info)
        {
            die('code无法获取');
        }
        $code = $info['wxvalue'];
        $your_own_redirect_uri = SITE_URL."wx-wxcode.html";
        $url = "https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=".$this->option['component_appid']."&pre_auth_code=$code&redirect_uri=$your_own_redirect_uri";
        $this->assign('url',$url);
        $this->display('wxthree.html');
    }

    public function wxcode()
    {
        $wxconfig = m('wxconfig');
        $info  = $wxconfig->get(array(
            'conditions' => "wxcode = 'component_access_token' ",
        ));
        $component_access_token = $info['wxvalue'];
        $mpObj = new Mp($this->option);
        $mpObj->component_access_token = $component_access_token;
        $mpObj->getAuthRefreshToken();
        $authorizer_access_token = $mpObj->authorizer_access_token;
        $authorizer_refresh_token = $mpObj->authorizer_refresh_token;
        $authorizer_appid = $mpObj->authorizer_appid;

        $info  = $wxconfig->get(array(
            'conditions' => "wxcode = 'authorizer_access_token' ",
        ));
        if($info)
        {
            $wxconfig->edit($info['id'],['wxvalue' => $authorizer_access_token]);
        }
        else
        {
            $wxconfig->add(['wxcode'=>'authorizer_access_token','wxvalue'=>$authorizer_access_token,'add_time'=>time()]);
        }
        $info  = $wxconfig->get(array(
            'conditions' => "wxcode = 'authorizer_refresh_token' ",
        ));
        if($info)
        {
            $wxconfig->edit($info['id'],['wxvalue' => $authorizer_refresh_token]);
        }
        else
        {
            $wxconfig->add(['wxcode'=>'authorizer_refresh_token','wxvalue'=>$authorizer_refresh_token,'add_time'=>time()]);
        }
        $info  = $wxconfig->get(array(
            'conditions' => "wxcode = 'authorizer_appid' ",
        ));
        if($info)
        {
            $wxconfig->edit($info['id'],['wxvalue' => $authorizer_appid]);
        }
        else
        {
            $wxconfig->add(['wxcode'=>'authorizer_appid','wxvalue'=>$authorizer_appid,'add_time'=>time()]);
        }


    }



    function three()
    {



        $options = array(
            'token' => 'mfd123456', //填写第三方的key
            'encodingaeskey' => 'acdsafsafdsafadsfrewrewfsafadsfdsafdasfadfc', //填写第三方加密用的EncodingAESKey
            'component_appid' => 'wx8a56c1e9cae65a8a', //填写第三方的app id
            'component_appsecret' => '3fd152f7eb1c7b6a3d3683d28a02bd7a', //填写第三方的密钥
            'authorizer_appid' => '',//根据需要初始化
            'component_verify_ticket' => "ticket@@@We_ZgXjX_c5Jm_tty6XrOlAp9CtqBdvmNUSNY35_ymYTBTcqcqmUOjCqFsCiH1ls2zWWcbyTdKmThL840xM83Q",//根据需要初始化11
        );
        $mpObj = new Mp($options);

        $mpObj->text('rrrr');
        echo '<pre>';print_r($mpObj->_msg);exit;
        
        $mpObj->reply();

        $component_access_token = $mpObj->checkComponentAuth();
        $pre_auth_code = $mpObj->getPreAuthCode();
echo '<pre>33';print_r($component_access_token);echo '<br>';
        print_r($pre_auth_code);exit;

        //{"AppId":"wx8a56c1e9cae65a8a","Encrypt":"BcJwmLon0nK+r4eFUwoQrRaS/ieK8yiraIKxIMKPAuC7x3Yjc1iYXMP6Kmg4JZU+qMkiz0PNzZ4BcTzcaQkS5jY3ps2F/6acih8idAHciWEuDLf7tutBYwG3Th1oy5fiSKPCwxpoFgWZnWMn06inn+rNicY02L5H5f13ivyFkvbX2qWTaPRelIHHHJ1fmCdG+hP5GUMw479c61xl4Ldo0jgPzSMkGc+7TFEfmrocrdOqDTv8xDMEbU5csyqiEXdY/3veDJRD0x6mZPF3FyYpBze3HcGMEv4aUfGRTQmg7OUathLGqko9DbBwpAlQKaK9QLCQJcssB3Ynaz1Nnanhdr7naVa5VBRoy7Aj2L2EFQh/AtCm0Y7NQ6AVxETqq4rpM9/UqZHchXE2+38eD1RdigXbvnJxlZKsCbmmFyEemEvX1fNbjPzLk8SSFdHOH9HEtsfoYA+UL1c0pG4vyS253g=="}
        $a = 'a:34:{s:4:"USER";s:3:"www";s:4:"HOME";s:9:"/home/www";s:9:"FCGI_ROLE";s:9:"RESPONDER";s:15:"SCRIPT_FILENAME";s:48:"/home/wwwroot/mfdplatform.p.day900.com/index.php";s:12:"QUERY_STRING";s:160:"signature=4e19f27025815ad4649dca1b2c4819a831222d1e&timestamp=1490966694&nonce=1406758025&encrypt_type=aes&msg_signature=603c4b9c1c7f696c54204ac1ec3acfd9a3a45b72";s:14:"REQUEST_METHOD";s:4:"POST";s:12:"CONTENT_TYPE";s:8:"text/xml";s:14:"CONTENT_LENGTH";s:3:"571";s:11:"SCRIPT_NAME";s:10:"/index.php";s:11:"REQUEST_URI";s:162:"/?signature=4e19f27025815ad4649dca1b2c4819a831222d1e&timestamp=1490966694&nonce=1406758025&encrypt_type=aes&msg_signature=603c4b9c1c7f696c54204ac1ec3acfd9a3a45b72";s:12:"DOCUMENT_URI";s:10:"/index.php";s:13:"DOCUMENT_ROOT";s:38:"/home/wwwroot/mfdplatform.p.day900.com";s:15:"SERVER_PROTOCOL";s:8:"HTTP/1.0";s:14:"REQUEST_SCHEME";s:4:"http";s:17:"GATEWAY_INTERFACE";s:7:"CGI/1.1";s:15:"SERVER_SOFTWARE";s:12:"nginx/1.10.0";s:11:"REMOTE_ADDR";s:13:"100.97.174.91";s:11:"REMOTE_PORT";s:5:"35462";s:11:"SERVER_ADDR";s:12:"10.28.34.212";s:11:"SERVER_PORT";s:2:"80";s:11:"SERVER_NAME";s:16:"mfd.p.day900.com";s:15:"REDIRECT_STATUS";s:3:"200";s:13:"HTTP_REMOTEIP";s:13:"140.207.54.74";s:9:"HTTP_HOST";s:16:"mfd.p.day900.com";s:20:"HTTP_X_FORWARDED_FOR";s:13:"140.207.54.74";s:15:"HTTP_CONNECTION";s:5:"close";s:19:"HTTP_CONTENT_LENGTH";s:3:"571";s:15:"HTTP_USER_AGENT";s:11:"Mozilla/4.0";s:11:"HTTP_ACCEPT";s:3:"*/*";s:11:"HTTP_PRAGMA";s:8:"no-cache";s:17:"HTTP_CONTENT_TYPE";s:8:"text/xml";s:8:"PHP_SELF";s:10:"/index.php";s:18:"REQUEST_TIME_FLOAT";d:1490966694.46281;s:12:"REQUEST_TIME";i:1490966694;}';
       $b = 'a:5:{s:9:"signature";s:40:"b6edb873b6b12c608c88506fb4ce632cce0acbbf";s:9:"timestamp";s:10:"1490967317";s:5:"nonce";s:9:"965473953";s:12:"encrypt_type";s:3:"aes";s:13:"msg_signature";s:40:"1a3ac2e86a54017f0f7b6d522b48833348e4b71d";}';
        $c = '{"AppId":"wx8a56c1e9cae65a8a","Encrypt":"YJsXrv2ezJxXDUfQfG6XLSjg4qprmVL5yB/k1rxpS443XRgylpl4vRbXe5EWY6Y8jPOoNvKVVPoOLJXhHjyjv/osU6QIF4um4H1Ubm5GONcYFp1sl6cKsbI0djF+uaCvatXfnb+wa9F5vvSmat8AfqcvXGE5gZjNY9xfCJxtTOk0WgP31LxuxoEYOlKTpJnWeA6FyVtBBbDZ/YKbSPhvUH9A2/1Epl/dfdxnXricETI26HsX1wt0STQ0FQ4lOsnCqmHKO8GWOOkMTvFdBLqjhUOna5RTVsrhtYPazO+kvpIriGHUIge1OA02DDwuLHVlfxRzdec5NRg3oY2J2rbZc738huYHQWITlgNTgw7aDt1XIbspJ4kBblwonhpRmXp+krT0x3O+PzfOBAUs2lgxPZ0URt6uCNPX8aNVpfvYFSAJ0FI1RlP2hWXKW/4eON2VBcBrerz1EKd02iH9ofp7+w=="}';
        $c_arr = json_decode($c,1);
        $encrypted = $c_arr['Encrypt'];
        //使用BASE64对需要解密的字符串进行解码
        $ciphertext_dec = base64_decode($encrypted);

        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');

        $iv = substr($this->key, 0, 16);
        mcrypt_generic_init($module, $this->key, $iv);
        //解密
        $decrypted = mdecrypt_generic($module, $ciphertext_dec);
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);


        echo '<pre>';print_r(json_decode($c,1));exit;

        echo '<pre>';print_r(unserialize($a));print_r(unserialize($b));exit;
        
        //https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=$your_appid&
        //pre_auth_code=$your_pre_auth_code&redirect_uri=$your_own_redirect_uri
        $mpObj = new Mp($options);
        $mpObj->valid();
        $type = $mpObj->getRev()->getRevType();
        switch ($type) {
            case Mp::MSGTYPE_TEXT:
                $mpObj->text("hello, I'm wechat")->reply();
                # code...
                break;

            default:
                # code...
                break;
        }
        echo '<pre>';print_r($mpObj);exit;
        
    }



    function brand_id()
    {
        $brand_id = empty($_GET['brand_id']) ? 0 : intval($_GET['brand_id']);

        $brand_mod =& m('brand');
        if (!$brand_mod->get(array('conditions'=>'brand_id='.$brand_id.' AND store_id=0')))
        {
            echo ecm_json_encode(false);
            return;
        }
        echo ecm_json_encode(true);
    }


    function check_tel(){
        $tel = empty($_GET['tel']) ? '' : trim($_GET['tel']);
        $member_mod =& m('member');
        $member_info = $member_mod->get(array('conditions'=>'user_name='.$tel.' AND serve_type=1' ));
        if($member_info){
            echo ecm_json_encode(false);
            return;
        }
        echo ecm_json_encode(true);
    }

    //发验证码
    function tel_code(){
       $res = smsAuthCode($_GET['user_name'],'tailor','tailor','get','pc');
            if ($res['err'])
            {
                echo ecm_json_encode(false);
                return;
            }else{
                echo ecm_json_encode(true);
                return;
        }
    }



    function check_name()
    {
        $store_name = empty($_GET['store_name']) ? '' : trim($_GET['store_name']);
        $store_id = empty($_GET['store_id']) ? 0 : intval($_GET['store_id']);

        $store_mod =& m('store');
        if (!$store_mod->unique($store_name, $store_id))
        {
            echo ecm_json_encode(false);
            return;
        }
        setcookie('store_name', $store_name);
        echo ecm_json_encode(true);
    }


    function address(){
        $address = empty($_GET['address']) ? '' : trim($_GET['address']);
        setcookie('address', $address);
        echo ecm_json_encode(true);
    }

    function owner_name(){
        $owner_name = empty($_GET['owner_name']) ? '' : trim($_GET['owner_name']);
        setcookie('owner_name', $owner_name);
        echo ecm_json_encode(true);
    }

    function tel(){
        $tel = empty($_GET['tel']) ? '' : trim($_GET['tel']);
        setcookie('tel', $tel);
        echo ecm_json_encode(true);
    }

    function im_qq(){
        $tel = empty($_GET['im_qq']) ? '' : trim($_GET['im_qq']);
        setcookie('im_qq', $im_qq);
        echo ecm_json_encode(true);
    }


    /* 上传图片 */
    function _upload_image($store_id)
    {
        import('uploader.lib');
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE);
        $uploader->allowed_size(SIZE_STORE_CERT); // 400KB

        $data = array();
        for ($i = 1; $i <= 3; $i++)
        {
            $file = $_FILES['image_' . $i];
            if ($file['error'] == UPLOAD_ERR_OK)
            {
                if (empty($file))
                {
                    continue;
                }
                $uploader->addFile($file);
                if (!$uploader->file_info())
                {
                    $this->_error($uploader->get_error());
                    return false;
                }

                $uploader->root_dir(ROOT_PATH);
                $dirname   = 'data/files/mall/application';
                $filename  = 'store_' . $store_id . '_' . $i;
                $data['image_' . $i] = $uploader->save($dirname, $filename);
            }
        }
        return $data;
    }


    /**
    *回复创业顾问的信息
    *@author liang.li <1184820705@qq.com>
    *@2015年9月10日
    */
    function reload() 
    {
        return;
        $cy_mod = m('cy_reload_log');
        $m_mod = m('member');
        $special_log = m('special_log');
        $special_code = m('special_code');
        $member_invite = m('memberinvite');
        $cy_list = $cy_mod->find(array(
            'conditions' => "1=1 and type=0",
            'join'       => "belongs_to_user",
            'fields'     => "cy_reload_log.*,member.member_lv_id  as m_lv_id",
            'order' => "id desc"
        ));
        
        foreach ($cy_list as $key => &$value)
        {
            if ($value['invitee'])
            {
                $value['invitee'] = json_decode($value['invitee'],true);
            }
            //$value['special_code'] = json_decode($value['special_code'],true);
            if ($value['special_code'])
            {
                $value['special_code'] = json_decode($value['special_code'],true);
            }
            if ($value['inviter'])
            {
                $value['inviter'] = json_decode($value['inviter'],true);
            }
        }
 print_exit($cy_list);   
        foreach ($cy_list as $key => $value) 
        {
            //=====  member_invite表  =====
            if ($value['invitee']); 
            {
                
                 //=====  member_inviter表  =====
                $value['inviter'][] = $value['invitee'];
                if ($value['user_id'] = 1514)
                {
                    //                     echo $key;
                    print_exit($value);
                }  
                foreach ($value['inviter'] as $key1 => $value1) 
                {
                    
              
                    if ($value1) 
                    {
                        $data_inv = $value1;
                /* if (!$data_inv) 
                {
       print_exit($value)             ;
                } */
                        unset($data_inv['id']);
                        $inviter_info = $m_mod->get_info($value['invitee']['inviter']);
                        $data_inv['nickname'] = $inviter_info['nickname'];
                        $member_invite->add($data_inv);
                    }
                    
                }
            }
            
            //=====  apecial_code  表  =====
            if ($value['special_code'])
            {
                $sn = $value['special_code']['sn'];
                if (!$special_code->get("sn = '$sn' "))
                {
                    //=====   special_code表 =====
                    $data_spe = $value['special_code'];
                    unset($data_spe['id']);
                    $special_code->add($data_spe);
                    //=====  special_log num字段  =====
                    if ($value['special_code']['log_id'])
                    {
                        $special_log->edit($value['special_code']['log_id'],array('num'=>"num+1"));
                    }

                    if ($value['m_lv_id'] != 5)
                    {
                        //=====    会员等级  =====
                        if ($value['special_code']['cate'] == 1)
                        {
                            $m_mod->edit($value['user_id'],array('member_lv_id'=>4));
                        }
                        elseif ($value['special_code']['cate'] == 2)
                        {
                            $m_mod->edit($value['user_id'],array('member_lv_id'=>2));
                        }
                    }
                }
            }
            else 
            {
                if ($value['m_lv_id'] != 5)
                {
                    $m_mod->edit($value['user_id'],array('member_lv_id'=>2));
                }
                
            }
            
            //=====  如果special_code有数据  =====
            
            
            
        }
        
        
        
        
    print_exit($cy_list);
        
        
    }
    
    
    /**
    *content
    *@author liang.li <1184820705@qq.com>
    *@2016年1月8日
    */
    function xifucart() 
    {
        $cart_mod = m('cart');
        $cart_list = $cart_mod->find(array(
            'order' => 'rec_id DESC',
        ));
        $cart_f_list = array();
        foreach ($cart_list as $key => $value) 
        {
            if ($value['dis_ident'] && !$value['f_cloth']) 
            {
                $cart_f_list[$value['dis_ident']][] = $value;
            }
            if (!$value['dis_ident']) 
            {
                $cloth = $value['cloth'];
                $ident = $value['ident'];
                $cart_mod->edit($key,array('dis_ident'=>$ident,'f_cloth'=> $cloth));
            }
        }
        
//     print_exit($cart_f_list);    
        foreach ($cart_f_list as $key => $value) 
        {
            if (count($value) >= 2) //=====  套装  ===== 
            {
                $cart_mod->edit("dis_ident = '$key' ",array('f_cloth'=> '0001'));
            }
            else 
            {
//            print_exit($value);
                $cloth = $value[0]['cloth'];
                $cart_mod->edit("dis_ident = '$key' ",array('f_cloth'=> $cloth));
//           print_exit($cloth);
            }
        }
      print_exit($cart_f_list);  
      
        
    }

    /*修复数据 member_invite  yusw*/
    function xiufu(){
return;
        $cy_mod = m('cy_reload_log');
        $m_mod = m('member');
        $special_log = m('special_log');
        $special_code = m('special_code');
        $member_invite = m('memberinvite');
        $g_mod = m('generalize_member');

        $g_info = $member_invite->find("TYPE='1' AND nickname='' ");
        $g_info_ids = i_array_column($g_info,'inviter');
        $m_info = $member_invite->find("TYPE='0' AND nickname='' ");
        $m_info_ids = i_array_column($m_info,'inviter');

//        echo '<pre>';var_dump($g_info_ids);
        $g_nicknames = $g_mod->find(db_create_in($g_info_ids, 'id'));
        foreach($g_nicknames as $k=>$v){
            echo $k.'-[(1) inviter:id]-[nickname:'.$v['name'].']<br/>';
            $member_invite->edit('inviter='.$k,"nickname='".$v['name']."'");

        }

//        echo '<pre>';var_dump($g_nicknames);
        $m_nicknames = $m_mod->find(db_create_in($m_info_ids, 'user_id'));
        foreach($m_nicknames as $k=>$v){
            echo $k.'-[（0） inviter:id]-[nickname:'.$v['nickname'].']<br/>';
            $member_invite->edit('inviter='.$k,"nickname='".$v['nickname']."'");
        }
//        echo '<pre>';var_dump($m_nicknames);die;
        echo 'done';
        echo count($m_nicknames)+count($g_nicknames);

    }

}

?>