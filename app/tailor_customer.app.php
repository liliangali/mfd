<?php
/**
 * 裁缝中心 app- 我的顾客
 * @author xiao5 <xiao5.china@gmail.com>
 * @version $Id: tailor_customer.app.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 www.alicaifeng.com
 * @package app
 */
class Tailor_customerApp extends MemberbaseApp{
    var $part_mod;
    var $store_fabric_mod;
    var $part_attribute_mod;
    var $part_attr_mod;
     function __construct()
    {
        $this->Tailor_customerApp();
    }
    
    /**
     * 重写模板文件
     * @return void
     * @access public
     * @see _config_view
     * @version 1.0.0 (2014-11-17)
     * @author yhao.bai
     */
    function _config_view()
    {
        parent::_config_view();
        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();
        $this->_view->template_dir = ROOT_PATH . "/view/".LANG."/mall/{$template_name}/user_center";
        $this->_view->compile_dir  = ROOT_PATH . "/temp/compiled/mall/{$template_name}/user_center";
        $this->_view->res_base     = SITE_URL . "/view/".LANG."/mall/{$template_name}/styles/{$style_name}";
    }

    function Tailor_customerApp()
    {
        parent::__construct();
        $this->_store_id  = intval($this->visitor->get('manage_store'));
        $this->customer_figure_mod =& m('customer_figure');
        $this->member_figure_mod =& m('member_figure');
        require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
        $this->objAvatar = new Avatar();
    }
    /**
     * 顾客列表
     * @access public
     * @version 1.0.0 (2014-12-12)
     * @author Xiao5
     */
    public function index()
    {
        $user = $this->visitor->get();
        if (!$user['has_store'])
        {
            /*  show_message('您还不是裁缝，请返回！');  */
            header("Location:apply.html");
        
        
        }
        $page   =   $this->_get_page(10);    //获取分页信息
        //获取信息
        $customer = $this->customer_figure_mod->find(array(
            'conditions'=>'storeid='.$this->_store_id,
            'count' => true,
            'limit' => $page['limit'],
            'order' => 'lasttime desc'
        ));
    

        if ($customer)
        {
            //获取 会员信息
            $uids = array();
            $uids = i_array_column($customer, 'userid');
            $member_mod  =& m('member');
            $members = $member_mod->findAll(array(
                    'conditions'    => db_create_in($uids,'user_id'),
                    'fields'        => 'user_name,nickname',
                    'count'         => true,
                    'limit'         => $page['limit'],
            ));
            
            foreach ($customer as &$v){
                $v['nickname'] = $members[$v['userid']]['nickname'];
            }
        }
        
        
        $page['item_count'] = $this->customer_figure_mod->getCount();
        $this->_format_page($page);

        $this->assign('customer', $customer);
        $this->assign('page_info', $page);          //将分页信息传递给视图，用于形成分页条


       
        $this->assign('user',$user);
        $this->assign('ac', ACT);
        $this->assign('type', 'user');
        
        //获取头像
        $avatar = $this->objAvatar->avatar_show($this->_store_id,'big');
        $this->assign('avatar', $avatar);
        
        /* 当前用户中心菜单 */
        $this->assign('_member_menu', $this->_get_member_menu('user'));
        $this->assign('_curitem', 'my_customer');
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('my_customer'));
        
        $this->assign('positions', $this->member_figure_mod->_positions());
        
        $this->display('tailor_customer.index.html');
    }
    /**
     * 新增顾客
     * @access public
     * @version 1.0.0 (2014-12-12)
     * @author Xiao5
     */
    function add(){
        if($_POST){
            $data = array(
                'storeid' => $this->_store_id,
                'customer_name' => $_POST["un"],
                'customer_mobile' => $_POST["up"],
                'lasttime' => gmtime()
            );
            
            /*处理量体数据 */
            if ($_POST['val'])
            {
                $vals = explode(',', $_POST['val']);
                foreach ($vals as $k=>$v)
                {
                    $row = array();
                    $row = explode('=', $v);
                    if (in_array($row[0], array_keys($this->member_figure_mod->_positions())))
                    {
                        $data[$row[0]] = "{$row[1]}";
                    }
                }
                
            }
            /* 处理关系 */
            if ($_POST['na'])
            {
                $nickname = $_POST['na'];
                $member_mod  =& m('member');
                $conditions = "nickname = '" . $nickname . "'";
                $uinfo = $member_mod->get(array('conditions' => $conditions));
                if (!$uinfo['user_id'])
                {
                    $this->json_error('昵称不存在!');
                    return ;
                }
                $data['userid'] = $uinfo['user_id'];
                
                $isexit = $this->customer_figure_mod->get(array('conditions' => "storeid = '" . $data['storeid'] . "' and userid='" . $data['userid']."'" ));
                
                if ($isexit['figure_sn'])
                {
                    $this->json_error('不能重复录入！');
                    return ;
                }
            }else{
                $res = check::isMobileCode('reg', 'reg', $_POST['up'],$_POST['code']);
                if($res){
                    $this->json_error('短信验证码错误');
                    return;
                }
                $member_mod =& m('member');
                $member_info = $member_mod->get(array('conditions'=>'user_name='.$_POST['up'].' and serve_type=1'));
                if($member_info){
                    $this->json_error('电话已经被占用');
                    return;
                }

                 //如果不存在。给简历个会员
                 /*会员默认等级*/
                 $member_lv_mod =& m('memberlv');
                 /* 类型 lv_type：'member', 'joining', 'supplier', 'service' | member默认等级 */
                 $m_lv = $member_lv_mod->get_default_level();
                 $nickname = $this->getCode(10);
                 $ms =& ms(); //连接用户中心
                 $data['userid'] = $ms->user->register($_POST['up'], substr($_POST['up'], -6), '',array('phone_mob'=>$_POST['up'],'nickname'=>$nickname,'member_lv_id'=>$m_lv['member_lv_id']));
                 if(!$data['userid']){
                    $member_mod =& m('member');
                    $memeber = $member_mod->get(array('conditions'=>'user_name='.$_POST['up'].' AND serve_type=0' ));
                    $data['userid'] = $memeber['user_id'];
                 }
            }
           
            $customer_id = $this->customer_figure_mod->add($data);
            if($customer_id){
                $this->json_result();
            }else{
                $this->json_error('添加顾客失败!');
                return ;
            }

        }
    }

    //ns add 发验证码
    function tel_code(){
       if($_GET['user_name']){
            $member_mod =& m('member');
            $memeber = $member_mod->get(array('conditions'=>'user_name='.$_GET['user_name'].' AND serve_type=0' ));
            if($memeber){
                $this->json_error('该顾客已注册会员，请以会员身份添加顾客!');
            }else{
               $res = smsAuthCode($_GET['user_name'],'reg','reg','get','pc');
                if ($res['err'])
                {
                    $this->json_error($res['msg']);
                }
                $this->json_result($res['msg']);

            }

       }else{
            $this->json_error('发送失败!');
       }
    }

    /**
     * 修改顾客
     * @access public
     * @version 1.0.0 (2014-12-12)
     * @author Xiao5
     */
    function edit(){
        if($_POST){
            $conditions =  array('conditions' => 'figure_sn='."'{$_POST['eid']}' and storeid=".$this->_store_id);
            $cinfo = $this->customer_figure_mod->get($conditions);
            if (!$cinfo['figure_sn'])
            {
                $this->json_error('请选择编辑的顾客信息!');
                return ;
            }
         
            $data = array(
                'storeid' => $this->_store_id,
                'customer_name' => $_POST["un"],
                'customer_mobile' => $_POST["up"],
                'lasttime' => gmtime()
            );
  
            /*处理量体数据 */
            if ($_POST['val'])
            {
                $vals = explode(',', $_POST['val']);
                foreach ($vals as $k=>$v)
                {
                    $row = array();
                    $row = explode('=', $v);
                    if (in_array($row[0], array_keys($this->member_figure_mod->_positions())))
                    {
                        $data[$row[0]] = "{$row[1]}";
                    }
                }
        
            }
             
            $customer_id = $this->customer_figure_mod->edit($cinfo['figure_sn'],$data);
            if($customer_id){
                $this->json_result();
            }else{
                $this->json_error('修改顾客失败!');
                return ;
            }
        
        }
    }
    /**
     * 重新录入顾客
     * @access public
     * @version 1.0.0 (2014-12-12)
     * @author Xiao5
     */
    function entry()
    {
        if($_POST){
              $conditions =  array('conditions' => 'figure_sn='."'{$_POST['eid']}' and storeid=".$this->_store_id);
            $cinfo = $this->customer_figure_mod->get($conditions);
          
            if (!$cinfo['figure_sn'])
            {
                $this->json_error('请选择编辑的顾客信息!');
                return ;
            }
             
            $data = array(
                'storeid' => $this->_store_id,
                'customer_name' => $_POST["un"],
                'customer_mobile' => $_POST["up"],
                'lasttime' => gmtime()
            );
        
            /*处理量体数据 */
            if ($_POST['val'])
            {
                $vals = explode(',', $_POST['val']);
                foreach ($vals as $k=>$v)
                {
                    $row = array();
                    $row = explode('=', $v);
                    if (in_array($row[0], array_keys($this->member_figure_mod->_positions())))
                    {
                        $data[$row[0]] = "{$row[1]}";
                    }
                }
        
            }
             
            $customer_id = $this->customer_figure_mod->edit($cinfo['figure_sn'],$data);
            if($customer_id){
                $this->json_result();
            }else{
                $this->json_error('录入顾客失败!');
                return ;
            }
        
        }
    }

	/**
     * 昵称验证
     * @access public
     * @version 1.0.0 (2015-03-10)
     */
    function  check_nickname(){
        $nickname = $_POST['na'];
        $member_mod  =& m('member');
        $conditions = "nickname = '" . $nickname . "'";
        $uinfo = $member_mod->get(array('conditions' => $conditions));
        if (!$uinfo['user_id'])
        {
            $this->json_error('! 昵称不存在');
            return ;
        }
    }

}

