<?php
/**
 *    申请面料控制器
 */
class Fabric_applyApp extends MemberbaseApp{
    var $part_mod;
    var $store_fabric_mod;
    var $part_attribute_mod;
    var $part_attr_mod;
	 function __construct()
    {
        $this->Fabric_applyApp();
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

    function Fabric_applyApp()
    {
        parent::__construct();
        $this->_store_id  = intval($this->visitor->get('manage_store'));
        $this->part_mod =& m('part');
        $this->store_fabric_mod =& m('store_fabric');
        $this->part_attribute_mod =& m('partattribute');
        $this->part_attr_mod =& m('partattr');
    }
    function index()
    {
        $page   =   $this->_get_page(10);    //获取分页信息
        //获取信息
        $store_fabric = $this->store_fabric_mod->find(array(
            'conditions'=>'store_id='.$this->_store_id,
            'count' => true,
            'limit' => $page['limit'],
            'order' => 'add_time desc'
        ));
        $status = array(0=>'审核中',1=>'已采购',2=>'采购中',3=>'审核失败',4=>'已受理');
        foreach($store_fabric as $k=>$v){
            $store_fabric[$k]['status_name'] = $status[$v['status']];
        }

        $page['item_count'] = $this->store_fabric_mod->getCount();
        $this->_format_page($page);

        $this->assign('store_fabric', $store_fabric);
        $this->assign('page_info', $page);          //将分页信息传递给视图，用于形成分页条


        /* 头像 add ns */
        require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
        $objAvatar = new Avatar();
        //获取头像
        $user = $this->visitor->get();
        $this->assign('user',$user);
        $avatar = $objAvatar->avatar_show($user['user_id'],'big');
        $this->assign('avatar', $avatar);
        $this->assign('type','tailor');
        $this->assign('ac', ACT);
        /* 当前用户中心菜单 */
        $this->assign('_member_menu', $this->_get_member_menu('user'));
        $this->assign('_curitem', 'fabric_apply');
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('fabric_apply'));
        $this->display('fabric_apply.index.html');
    }
    function add(){
        if($_POST){
            $data = array(
              'store_id' => $this->_store_id,
              'goods_sn' => $_POST["goods_sn"],
              'num' => $_POST["num"],
              'date' => $_POST["date"],
              'owner_name' => $_POST["owner_name"],
              'phone' => $_POST["phone"],
              'description' => $_POST["description"],
              'add_time' => time()
            );
            $fabric_id = $this->store_fabric_mod->add($data);
            if($fabric_id){
                echo ecm_json_encode(true);
                return;
            }else{
                echo ecm_json_encode(false);
                return;
            }

        }
    }

    function edit(){
       
    }

    function drop(){
     
    }
    function check_sn(){
        $goods_sn = empty($_GET['goods_sn']) ? '' : trim($_GET['goods_sn']);
        $goods_sn_info = $this->part_mod->get(array('conditions'=>"goods_sn='".$goods_sn."' AND fabric_id !=0" ));
        if(!$goods_sn_info){
            echo ecm_json_encode(false);
            return;
        }
        echo ecm_json_encode(true);
    }

   
}

?>