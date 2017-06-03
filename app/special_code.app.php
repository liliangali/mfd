<?php

/**
 * 酷卡控制器
 *
 * @author yushw
 *
 */
class Special_codeApp extends MemberbaseApp
{
    var $_fabricbook_mod;

    function __construct()
    {
		parent::__construct();
		header("Content-Type:text/html;charset=" . CHARSET);
		Lang::load(lang_file('common'));

        $path = ROOT_PATH."/data/config/special_code.php";
        file_exists($path) && $this->_cate = include $path;

    }
    /**
     * 特权码页面
     * yusw
     * @return void
     */
    function index()
    {
        //        echo '<pre>';var_dump($_SESSION);die;
        $this->special_info();
        $this->_config_seo('title','我的麦富迪 - 我的特权码');
        $this->display('special_code.index.html');
    }
    /**
     *  特权码
     *
     * @param  array $arr 消息数组信息
     *
     * @return  string
     */
    public function special_code()
    {
        //没有登陆
        if (!$this->visitor->has_login)
        {
            $this->json_error("请先登录再进行操作");
            return;
        }


//        $return = array();
//        $_meminvite_mod = &m('memberinvite');
//        $member_mod  = m('member');
        $_special_code_mod = & m('special_code');
        $sn =isset($_REQUEST['sn'])?trim($_REQUEST['sn']):'';
        $user_info=$this->visitor->get();



        $lv_name = '';
        $sign = 1;
        $user_id = $user_info['user_id'];
        if($user_info['member_lv_id']>1) $sign=0;



        //bd码的才能 进行到这一步
        //        $bd_info = $_meminvite_mod->get("invitee = '$user_id' and type=1");
        //        if(empty($bd_info)){
        //            $return = array(
        //                'statusCode' => 0,
        //                'error' => array(
        //                    'errorCode' => 102,
        //                    'msg'       => '未绑定BD码,不能参加此活动',
        //                ),
        //            );
        //            return $json->encode($return);
        //        }


        if(!$sn){

            $this->json_error("特权码不能为空");
            return;
        }



        //        $path = ROOT_PATH."/data/config/special_code.php";
        //        file_exists($path) && $this->_cate = include $path;

        $info = $_special_code_mod->get('cate<20 and sn="'.strtoupper($sn).'"');//转变大写
        $cate_name = $this->_cate[$info['cate']]['name'];
        $member_lv_id=$this->_cate[$info['cate']]['member_lv_id'];


        if(empty($info)){
            $this->json_error($cate_name.'无效');
            return;
        }


        if($info['expire_time']<time()){
            $this->json_error( $cate_name.'已经过期');
            return;
        }

        if($info['is_used']==1){
            $this->json_error($cate_name.'已被使用');
            return;
        }

        if($user_info['member_lv_id']>$member_lv_id){
            $this->json_error('当前用户等级大于特权码等级');
            return;
        }

        // 线上发放的码     绑定到了用户
        if($info['to_id'] !=' ' && $info['to_id'] !=$user_id){
            $this->json_error('无权使用此'.$cate_name.'！');
            return;
        }


        //cate in(1,2)  现在只有2种码  两种码互斥   <20
        $info2 = $_special_code_mod->get('is_used=1 and cate<20 and  to_id='.$user_id);

        if($info2){
            $this->json_error( '当前用户已经使用过一次同类型特权码！');
            return;
        }

        $data = array(
            'to_id'=> $user_info['user_id'],//$user_id
            'user_name'=>$user_info['user_name'],
            'is_used'=>1,
            'source'=>'mfd|pc',
            'to_time'=>time(),

        );
        $ret = $_special_code_mod->edit('cate<20 and sn="'.$sn.'"',$data);

        if(!$ret){
            $this->json_error( '系统异常！');
            return;
        }


        switch ($info['cate']){
            case 1:
                //越级码
                $lv_name = change_lv(4,$user_id,$user_info['invite'],$sign);
                break;
            case 2:
                //初级码
                $lv_name = change_lv(2,$user_id,$user_info['invite'],$sign);
                break;
            default:
                break;
        }


        $result = array(
            //                'point'=>$point,
            'member_lv_id'=>$member_lv_id,
            'member_lv_name'=>$lv_name,
            'name'=>$cate_name,
            'description'=>$this->_cate[$info['cate']]['description'],
        );


        $this->json_result($result);

    }

    /**
     *  特权码 详细信息
     *
     * @param  array $arr 消息数组信息
     *
     * @return  string
     */
    function special_info(){

        //没有登陆
        if (!$this->visitor->has_login)
        {
            $this->assign('special_info','暂无数据');
            return false;
        }

        $return = array();
        $member_mod  = & m('member');
        $_special_code_mod = & m('special_code');
        $user_info=$this->visitor->get();
        $user_id = $user_info['user_id'];

        //        $path = ROOT_PATH."/data/config/special_code.php";
        //        file_exists($path) && $this->_cate = include $path;

        //cate <20  暂时
        $info = $_special_code_mod->find(array(
            'index_key' =>'',
            'conditions'=>'cate<20 and is_used=1 and to_id="'.$user_info['user_id'].'"',
            'fields'=>'sn,cate,user_name,expire_time,to_time'
        ));

        if(empty($info)){
            $this->assign('special_info','');
            return false;

        }
        foreach($info as $k=>$v){
            $info[$k]['cate_name'] = $this->_cate[$v['cate']]['name'];
            $info[$k]['description'] = $this->_cate[$v['cate']]['description'];
        }
//        unset($info[0]['cate']);
//echo '<pre>';var_dump($info);die;
        $this->assign('special_info',$info);
    }




}

?>