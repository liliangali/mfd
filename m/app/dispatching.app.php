<?php
/**
 *    裁缝派工量体控制器
 */
class DispatchingApp extends MemberbaseApp{
    var $store_dispatching_mod;
	 function __construct()
    {
        $this->DispatchingApp();
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

    function DispatchingApp()
    {
        parent::__construct();
        $this->_store_id  = intval($this->visitor->get('manage_store'));
        $this->_store = $this->visitor->get('s');
        $this->store_dispatching_mod =& m('store_dispatching');
    }
    function index()
    {
        /* 导入jQuery的表单验证插件 */
        $this->import_resource(array('script' => 'mlselection.js,jquery.plugins/jquery.validate.ad.js'));
        //获取地区
        $region_mod =& m('region');
        $this->assign('site_url', site_url());
        $this->assign('regions', $region_mod->get_options(0));


        $page   =   $this->_get_page(10);    //获取分页信息
        //获取信息
        $store_dispatching = $this->store_dispatching_mod->find(array(
            'conditions'=>'store_id='.$this->_store_id,
            'count' => true,
            'limit' => $page['limit'],
            'order' => 'add_time desc'
        ));
        $page['item_count'] = $this->store_dispatching_mod->getCount();
        $this->_format_page($page);
        $this->assign('store_dispatching', $store_dispatching);
        $this->assign('page_info', $page);          //将分页信息传递给视图，用于形成分页条

        /* 头像 add ns */
        require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
        $objAvatar = new Avatar();
        //获取头像
        $user = $this->visitor->get();
        $this->assign('user',$user);
        $avatar = $objAvatar->avatar_show($user['user_id'],'big');
        $this->assign('avatar', $avatar);
        $this->assign('type','user');


        
        $this->assign('ac', ACT);
        /* 当前用户中心菜单 */
        $this->assign('_member_menu', $this->_get_member_menu('user'));
        $this->assign('_curitem', 'dispatching');
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('my_order'));
        $this->display('dispatching.index.html');
        
        
    }
    function add(){
        if($_POST){
            $data = array(
            'store_id' => $this->_store_id,
            'store_name' => $this->_store[$this->_store_id.'_'.$this->_store_id]['store_name'],
            'owner_name' => $_POST["owner_name"],
            'sex' => $_POST["sex"],
            'tel' => $_POST["tel"],
            'region_id' => $_POST["region_id"],
            'region_name' => $_POST["region_name"],
            'address' => $_POST["address"],
            'date' => $_POST["date"],
            'apply_date' => $_POST["apply_date"],
            'description' => $_POST["description"],
            'status ' => 0,
            'add_time' => time()
            );
            $id = $this->store_dispatching_mod->add($data);
            if($id){
                echo ecm_json_encode(true);
                return;
            }else{
                echo ecm_json_encode(false);
                return;
            }
        }
        
    }

    //修改作品
    function edit(){
    }

    //删除裁缝作品
    function drop(){
        $args = $this->get_params();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    }




   
}

?>
