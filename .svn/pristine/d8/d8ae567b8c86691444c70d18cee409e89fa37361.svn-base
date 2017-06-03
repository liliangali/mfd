<?php
use Cyteam\Shop\Type\FdiyBase;
use Cyteam\Shop\Type;
use Cyteam\Shop\Type\Types;
use Cyteam\Goods\Orders;
/* 申请品牌商 */
class VueApp extends MallbaseApp
{


    public function mesf()
    {
        $goods = Types::createObj("fdiy");
        $res = $goods->mesf(5231);
        
        echo '<pre>';print_r($res);exit;
        
//echo '<pre>';print_r($res);exit;972422197291
    }

    function index()
    {
     $this->display('vue.html');

    }

    function mstatus()
    {
        $return['statusCode'] = 1;
        $return['msg'] = '参数错误';
        $order_id = $_REQUEST['order_id'];
        $ms = $_REQUEST['ms'];
        $order_mod = m('order');
        if(!$order_id || !$ms)
        {
            echo json_encode($return);
            return;
        }
        $info = $order_mod->get_info($order_id);
        if($info['status'] != 20)
        {
            $return['msg'] = '订单不是支付状态';
            echo json_encode($return);
            return;
        }

        if($info['mes_status'] != 1)
        {
            $return['msg'] = '订单尚未推送到mes';
            echo json_encode($return);
            return;
        }
        
        $order_mod->edit($order_id,['mes_order'=>$ms]);
        $return['statusCode'] = 0;
        $return['msg'] = '成功';
        echo json_encode($return);
        return;
//echo '<pre>';print_r($res);exit;

    }

    function https_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        $output = json_decode($output,true);
        return $output;
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