<?php

class QuanApp extends MemberbaseApp
{
	var $_newpromotion_mod;
 
    
    function __construct(){
        parent::__construct();
        $this->_newpromotion_mod =& m('newpromotion');
        $this->_youhuiquan_mod =& m('youhuiquan');
     
    }

    function index()
    {
        //获取全部优惠券
        $newpromotions=$this->_newpromotion_mod->find(array(
            'conditions'=>'yhcase=9 AND is_open=1   AND endtime >= '.time(),
        ));
        $this->assign('newpromotions',$newpromotions);
        $this->display('quan.html');
    }
    //领取优惠券
    function addquan(){
         $user_id=$_SESSION['user_info']['user_id'];
        $quanid=$_POST['id'];
        if(!$user_id){
            $this->json_error('请先登录！');
            exit;
        }
        //优惠券
        $youhuiquans=$this->_youhuiquan_mod->get(array(
            'conditions'=>"uid='{$user_id}' AND status = 0 AND quan_id='{$quanid}'",
        ));
        if($youhuiquans){
            $this->json_error('您已领取，不能重复领取哦~');
            exit;
        }else{
            //绑定优惠券
            $data=array(
                'quan_id'=>$quanid,
                'uid'    =>$user_id,
            );
            $this->_youhuiquan_mod->add($data);
            $this->json_result();
        }
        
    }


}

?>