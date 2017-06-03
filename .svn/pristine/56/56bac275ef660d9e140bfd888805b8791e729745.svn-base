<?php
/**
 * 用户中心 app- 我的投诉
 * @author xiao5 <xiao5.china@gmail.com>
 * @version $Id: my_complaint.app.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 www.alicaifeng.com
 * @package app
 */
class My_complaintApp extends MemberbaseApp
{
    function __construct()
    {
        $this->My_complaintApp();
    }

    /**
     * 重写模板文件
     * @return void
     * @access public
     * @see _config_view
     * @version 1.0.0 (2014-12-17)
     * @author Xiao5
     */
    function _config_view()
    {
        parent::_config_view();
        $template_name = $this->_get_template_name();
        $style_name = $this->_get_style_name();
        $this->_view->template_dir = ROOT_PATH . "/view/" . LANG . "/mall/{$template_name}/user_center";
        $this->_view->compile_dir = ROOT_PATH . "/temp/compiled/mall/{$template_name}/user_center";
        $this->_view->res_base = SITE_URL . "/view/" . LANG . "/mall/{$template_name}/styles/{$style_name}";
    }

    function My_complaintApp()
    {
        parent::__construct();
    }

    /**
     * 会员中心 我发起的投诉
     * @return void
     * @access public
     * @version 1.0.0 (2015-1-18)
     * @author yusw
     */
    public function index()
    {
        // 当前用户中心菜单
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('my_complaint'));
        $this->assign('ac', ACT);
        $this->assign('type', 'user');

        $user = $this->visitor->get();
        $this->assign('user', $user);

        //获取头像
        require(ROOT_PATH . '/includes/avatar.class.php'); //基础控制器类
        $objAvatar = new Avatar();
        $avatar = $objAvatar->avatar_show($this->visitor->get('user_id'), 'big');
        $this->assign('avatar', $avatar);

        //获取数据
        $page = $this->_get_page(8);
        $_complaint_mod =& m('complaint');
        $complaints = $_complaint_mod->find(array(
            'conditions' => 'from_id = '.$user['user_id'],
            'limit' => $page['limit'],
            'order' => "complaint.id DESC",
            'count' => true,
        ));
        //获取统计数据
        $page['item_count'] = $_complaint_mod->getCount();
        $this->_format_page($page);

        // 当前用户中心菜单
        $this->assign('_member_menu', $this->_get_member_menu('user'));
        $this->assign('_curitem', 'my_complaint');

        $this->assign('complaints', $complaints);
        $this->assign("page_info", $page);
        $this->display('my_complaint.index.html');
    }

    /**
     * 举报用户
     * @return void
     * @access public
     * @version 1.0.0 (2015-1-20)
     * @author yusw
     */
    public function add()
    {
        //异步?
        $data = array();
        $user = $this->visitor->get();
        $store_name = $_REQUEST['store_name'];
        $data['content'] = htmlspecialchars($_REQUEST['content']);
        $data['img1_url'] = $_REQUEST['img1_url'];
        $data['img2_url'] = $_REQUEST['img2_url'];
        $data['img3_url'] = $_REQUEST['img3_url'];
        $data['img4_url'] = $_REQUEST['img4_url'];
        $data['from_id'] = $user['user_id'];
        $data['c_time'] = gmtime();

        //店铺id
        $_store_mod =& m('store');
        $store_id = $_store_mod->get(array(
            'conditions' => "store_name = '$store_name'",
            'fields' => 'store_id',
        ));
        $data['to_id'] = $store_id['store_id'];

        //入库
        $_complaint_mod =& m('complaint');
        $_complaint_mod->add($data);

        //发信息给品牌商 ysuw
        if($_complaint_mod->has_error()){
            $this->show_warning($_complaint_mod->get_error());
            return;
        }
        sendMessage(array('type'=>8,'to_user_id' =>$store_id['store_id'],'location_url' => "","c_content"=>$data['content']));
        $this->show_message('提交投诉成功！','go_back', '/my_complaint-index.html');
    }

    /**
     * 店铺昵称验证
     * @return void
     * @access public
     * @version 1.0.0 (2015-1-20)
     * @author yusw
     */
    public function check_store()
    {
        $store_name = $_REQUEST['store_name'];
        !isset($store_name) && $this->json_error('请输入店铺名称!');;

        //查询店铺是否存在
        $_store_mod =& m('store');
        $unique = $_store_mod->unique($store_name);
        if ($unique) {
            $this->json_error('店铺名称不存在!');
            return;
        }
        $this->json_result('ok');
    }



}

?>
