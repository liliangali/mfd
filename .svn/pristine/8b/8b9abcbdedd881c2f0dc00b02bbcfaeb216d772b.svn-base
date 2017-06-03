<?php

class RecommendApp extends MallbaseApp
{
    function __construct(){
        parent::__construct();
        header("Content-Type:text/html;charset=" . CHARSET);
    }

    function index()
    {   
        $member_mod = & m('member');
        $user_id = $this->visitor->get('user_id');
        $user_info = $member_mod->get("user_id ='$user_id'");
        
      
        
        $this->display('recommend.html');

    }

    function _get_hot_keywords()
    {
        $keywords = explode(',', conf::get('hot_search'));
        return $keywords;
    }

    function shareyqm(){
        $member_mod = & m('member');
        $user_id = $this->visitor->get('user_id');
        $user_info = $member_mod->get("user_id ='$user_id'");
        if ($this->visitor->has_login)
        {
            $this->show_warning('has_login');

            return;
        }
        $check_user = $this->formCheck();
        if ($check_user['err'])
        {
            $this->json_error($check_user['msg']);
            return;
        }

        if(check::isMobile($_POST['phone']))
        {
          /*  $phone = $_POST['phone'];
            $rand = rand(10000,99999);
            $config = ROOT_PATH . '/data/config/sms.php';
            $member_mod = & m('member');
            $user_info = $member_mod->get("user_name='$phone' ");
            $data = array('yqm'=>$rand,'userid'=>$user_info['user_id']);
            $yqm_mod = & m('yqm');
            $yqm_mod->add($data);*/
            $phone = $_POST['phone'];
            $member_mod = & m('member');
            $yqm_mod = & m('yqm');
            $user_info = $member_mod->get("user_name='$phone' ");
            $user_id = $user_info['user_id'];
            $yqm_info = $yqm_mod->get("userid ='$user_id'");
            $yqm = $yqm_info['yqm'];
    
            $config = ROOT_PATH . '/data/config/sms.php';
            if(is_file($config)){
                $list = include_once($config);
               
            }

            //发送短信
            $res = smsAuthCode($_POST['phone'],'yqm','yqm','get','pc','',array('yqm'=>$yqm));
            if($res['err'])
            {
                $this->json_error($res['msg']);
                return;
            }else
            {
                $this->json_result('success',1);
            }
         
        }else
        {
            $this->json_error('用户名不合法!');
            return;

        }
 
    }
    

    function formCheck(){
    	
        $model_member =& m('member');
        $user_name = trim($_POST['phone']);
        $pcode     = $_POST['code'];
     
        if (!check::isMobile($user_name))
        {
            return array('msg'=>'手机号码不合法!','err'=>1);
        }
        $info = $model_member->get("user_name='{$user_name}' AND serve_type=1");
    
        if(empty($info))
        {
            return array('msg'=>'创业者用户不存在!','err'=>1);
        }
        $sms_reg_tmp_mod =& m('sms_reg_tmp');
        $reset_time = SMS_FAIL_TIME;
        $conditions = array('conditions'=>" category = 'yq' and type = 'yq' and phone = '$user_name' and code='$pcode' ");
        $sms_log = $sms_reg_tmp_mod->get($conditions);
        if (!$sms_log['id'])
        {
            return array('msg'=>'请重新获取验证码!','err'=>1);
        }

        return array('msg'=>'','err'=>0,'data'=>$sms_log);

    }

        /**
     * ajax 获取验证码
     * @access public
     * @see ajaxcode
     * @version 1.0.0 (2015-05-19)
     * @author tangshoujian
     */
    function ajaxcode()
    {
       
        $mobile = $_POST['mobile'];
	    
        if( preg_match("/^1[3,5,8]\d{9}$/",$mobile)===1 )
        {
            $res = smsAuthCode($mobile,'yq','yq','get','pc','');
       
            if ($res['err'])
            {
                $this->json_error($res['msg']);
                return;
            }
            
        }else{
            $this->json_error('mobile_num_error');
            return ;
        }
        
        $this->json_result($rs,'successed');
        return ;
        
    }

}

?>