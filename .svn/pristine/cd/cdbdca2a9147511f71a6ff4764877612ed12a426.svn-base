<?php
/* 申请品牌商 */
class ApplyApp extends MallbaseApp
{


    function curlPost($url, $postData)
    {
        $data = empty ( $postData ) ? array () : $postData;
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        return $return;
    }

    public function wxx()
    {
        require_once "weipay/lib/WxPay.Api.php";
        require_once "weipay/WxPay.JsApiPay.php";
        require_once 'weipay/log.php';            //=====  创建支付订单  =====
        $tools = new JsApiPay();
       $openid = "oywp_wXzwaptiO96ZzoA_TSBckb4";
        $mod = m('accesstoken');
        $access_token = $mod->get_token();
//        $t  = "LN5GQaufn8mxApn1dB8FcC0pXQF54x9Fgj4IM23C_KbTlDH4O-2zvesPqsRhYn03EctDljXFAXtKtNEtR-wRzKdsDiPf49Bsej8Uxd2-idNkEgwDuUDKl9vW_zy7MOaMHVVgACALZZ";
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
        $res = https_request($url);
        $nickname = $res['nickname'];
//晒黑的bear'
        $nickname = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $nickname);
//echo '<pre>';print_r($nickname);exit;


//echo '<pre>';print_r($res);exit;



        $m = m('member');
        $m->edit(5,['user_name'=>$nickname]);

echo '<pre>';print_r($access_token);print_r($res);exit;


//        $user_info = $tools->GetOpenid2();


    }




    function index()
    {


        import('shopCommon');

        $order_mod = m('order');
        $order_info = $order_mod->get_info(5494);
        ShopCommon::f_goods($order_info);

echo '<pre>';print_r(222);exit;


        $mod = m('accesstoken');
        $access_token = $mod->get_token();
//echo '<pre>';print_r($access_token);exit;

        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
//        $tid = get_tid('TM00247',$access_token);
//echo '<pre>';print_r($tid);exit;

        $data['touser'] = "oywp_wRDC_Y75BalRqdkiQCp7Ywc";
        $data['template_id'] = 'GvVZ-3FqczJ8S9EgFgifPp8aVyR9KiQpRU3aNZZOQTo';
        $data['url'] = "http://weixin.qq.com/download";
//        "first": {
//        "value":"恭喜你购买成功！",
//                       "color":"#173177"
//                   },
        $data['data']['first']['value'] = "您好，欢迎吃狗粮。";
        $data['data']['first']['color'] = "#173177";
        $data['data']['product']['value'] = "狗粮1";
        $data['data']['price']['value'] = "120.00";
        $data['data']['time']['value'] = "2015";
        $data['data']['remark']['value'] = "这里是备注";

        $res = https_request($url,json_encode($data));
//        echo '<pre>';print_r($res);exit;
        
        if($res['errcode'] == '40001');
        {
            $access_token = $mod->settoken();
            $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
            $res = https_request($url,json_encode($data));
        }



        $args = $this->get_params();
        $step = isset($args[0]) ? intval($args[0]) : 0;

         $store_id = $this->visitor->get('user_id');
        /* 判断是否开启了店铺申请 */
        if (!Conf::get('store_allow'))
        {
            header("Location:index.php/apply.step4.html");
            return;
        }

        /* 已申请过或已有不能再申请 */
        $store_mod =& m('store');
        $store = $store_mod->get($this->visitor->get('user_id'));
        if ($store)
        {
            if ($store['state'])
            {
                $this->display('apply/apply.step5.html');
                return;
            }
            else
            {
                if ($step != 2)
                {
                    $this->display('apply/apply.step4.html');
                    return;
                }
            }
        }
        $sgrade_mod =& m('sgrade');
        switch ($step)
        {
            case 0:
                $this->display('apply/apply.index.html');
                break;
            case 1:
                if (!IS_POST)
                {
                    //读取地区
                    $region_mod =& m('region');
                    $this->assign('site_url', site_url());
                    $this->assign('regions', $region_mod->get_options(0));

                    /* 导入jQuery的表单验证插件 */
                    $this->import_resource(array('script' => 'mlselection.js,jquery.plugins/jquery.validate.ad.js'));

                    $this->_config_seo('title', Lang::get('title_step2') . ' - ' . Conf::get('site_title'));
                    $this->assign('store', $store);
                    $scategory = $store_mod->getRelatedData('has_scategory', $this->visitor->get('user_id'));
                    if ($scategory)
                    {
                        $scategory = current($scategory);
                    }
                    $this->assign('scategory', $scategory);

                    $this->display('apply/apply.step2.html');
                }
                else
                {
                    $res = check::isMobileCode('tailor', 'tailor', $_POST['tel'],$_POST['code']);
                    if($res){
                        $this->show_warning('短信验证码错误');
                        return;
                    }
                    $member_mod =& m('member');
                    $member_info = $member_mod->get(array('conditions'=>'user_name='.$_POST['tel'].' and serve_type=1'));
                    if($member_info){
                        $this->show_warning('电话已经被占用，不能重复提交！');
                        return;
                    }
                    if (!$store_id) {
                        //如果不存在。给简历个会员
                         /*会员默认等级*/
                         $member_lv_mod =& m('memberlv');
                         /* 类型 lv_type：'member', 'joining', 'supplier', 'service' | member默认等级 */
                         $m_lv = $member_lv_mod->get_default_level();
                         $nickname = $this->getCode(10);
                         $ms =& ms(); //连接用户中心
                         $store_id = $ms->user->register($_POST['tel'], substr($_POST['tel'], -6), '',array('phone_mob'=>$_POST['tel'],'nickname'=>$nickname,'member_lv_id'=>$m_lv['member_lv_id']));
                         if(!$store_id){
                            $member_mod =& m('member');
                            $memeber = $member_mod->get(array('conditions'=>'user_name='.$_POST['tel'].' AND serve_type=0' ));
                            $store_id = $memeber['user_id'];
                         }
                    }
                    //添加属性
                    $store_attr_mod =& m('store_attr');
                    //先清空用户之前的信息
                    $store_attr_mod->drop('store_id='.$store_id);
                    $store_arrt_list = lang::get('store_arrt_list');
                    foreach($store_arrt_list as $k=>$sal){
                        foreach($sal as $sk=>$s){
                            if($_POST[$s['ch']]){
                                $store_attr_mod->add(array('type_id'=>$k,'content_id'=>$sk,'store_id'=>$store_id,'attr_name'=>$s['name']));
                            }
                        }
                    }
                    $store_mod  =& m('store');
                    $sgrade['need_confirm'] = 1;
                    //品牌商等级
                    $member_lv_mod =& m('memberlv');
                    /* 类型 lv_type：'member', 'joining', 'supplier', 'service' | member默认等级 */
                    $m_lv = $member_lv_mod->get_default_level(array('lv_type'=>'supplier'));
                    $data = array(
                        'store_id'     => $store_id,
                        'store_name'   => $_POST['store_name'],
                        'owner_name'   => $_POST['owner_name'],
                        'owner_card'   => $_POST['owner_card'],
                        'owner_sex'    => $_POST['sex'],  //称谓
                        'region_id'    => $_POST['region_id'],
                        'region_name'  => $_POST['region_name'],
                        'address'      => $_POST['address'],
                        'zipcode'      => $_POST['zipcode'],
                        'tel'          => $_POST['tel'],
                        //'im_qq'       => $_POST['im_qq'],
                        'sgrade'       => 1,  //等级-用于模板
                        'state'        => 0,
                        'add_time'     => gmtime(),
                        'description' => $_POST['description'],
                        'level'=>$m_lv['member_lv_id'],
                        'city_id' => $_POST['city_id']
                    );
                    $log_info = $this->visitor->get('user_id') ? 0 : 1;
                    $this->assign('log_info', $log_info);
                    //添加申请log表
                    $data_log = array('user_id' => $store_id,'apply_type' => 1,'status' => 0,);
                    $image = $this->_upload_image($store_id);
                    if ($this->has_error())
                    {
                        $this->show_warning($this->get_error());

                        return;
                    }
                    /* 判断是否已经申请过 */
                    $state = $this->visitor->get('state');
                    if ($state != '' && $state == STORE_APPLYING)
                    {
                        $store_mod->edit($store_id, array_merge($data, $image));
                    }
                    else
                    {
                        $store_mod->add(array_merge($data, $image));
                        //添加申请log表
                        $alog_mod =& m('applylog');
                        $alog_mod->add($data_log);
                    }

                    if ($store_mod->has_error())
                    {
                        $this->show_warning($store_mod->get_error());
                        return;
                    }
                    if ($sgrade['need_confirm'])
                    {
                        $this->display('apply/apply.step4.html');
                        return;
                    }
                }
                break;
            default:
                $this->display('apply/apply.html');
                break;
        }
    }

    function second()
    {
        /* 只有登录的用户才可申请 */
        if (!$this->visitor->has_login)
        {
            $this->login();
            return;
        }
        if(IS_POST)
        {
            $this->display('apply/apply.step3.html');
        }
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


}

?>