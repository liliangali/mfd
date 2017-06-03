<?php
/**
 *    面料查询控制器
 */
class Fabric_queryApp extends MemberbaseApp{
    var $part_mod;
	 function __construct()
    {
        $this->Fabric_queryApp();
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

    function Fabric_queryApp()
    {
        parent::__construct();
        $this->_store_id  = intval($this->visitor->get('manage_store'));
        $this->part_mod =& m('part');
        $this->part_attr_mod =& m('partattr');
    }
    function index()
    {
        $attr = $_GET['attr'] ? $_GET['attr'] : '';


        $conditions = $this->_get_query_conditions(array(
            array(
                'field' => 'goods_sn',
                'name'  => 'goods_sn',
                'equal' => 'like',
            ),
        ));
        if($attr){
            $attr_list = $this->part_attr_mod->find(array('conditions'=>"attr_id=26 AND attr_value='".$attr."'"));
            if($attr_list){
                foreach ($attr_list as $v) {
                    $b .= $v['part_id'].',';
                }
                $b = rtrim($b,',');
                $conditions .= " and part_id IN (".$b.")";
            }else{
                //给他一个不能查询出来的数据
                $conditions .= " and part_id < 0";
            }
        }

        $page   =   $this->_get_page(10);    //获取分页信息
        //获取信息
        $part_list = $this->part_mod->find(array(
            'conditions'=>'fabric_id !=0 AND state=1 AND is_on_sale=1'.$conditions,
            'count' => true,
            'limit' => $page['limit'],
            'order' => 'part_id desc'
        ));
        $ids = array();
        foreach ($part_list as $key => $value) {
            $ids[] = $value['part_id'];
        }

        $arr = fbinfo($ids);
        
        foreach($part_list as $key => $val){
            $part_list[$key]['children'] = $arr['fabs'][$key];
        }

        $page['item_count'] = $this->part_mod->getCount();
        $this->_format_page($page);

        $this->assign('part_list', $part_list);
        $this->assign('page_info', $page);          //将分页信息传递给视图，用于形成分页条

        $this->assign('attrs', $arr['attrs']);

        /* 头像 add ns */
        require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
        $objAvatar = new Avatar();
        //获取头像
        $user = $this->visitor->get();
        $avatar = $objAvatar->avatar_show($user['user_id'],'big');
        $this->assign('avatar', $avatar);
        $this->assign('type','tailor');
        $this->assign('user',$user);
        $this->assign('ac', ACT);
        /* 当前用户中心菜单 */
        $this->assign('_member_menu', $this->_get_member_menu('user'));
        $this->assign('_curitem', 'fabric_query');
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('fabric_apply'));
        $this->display('fabric_query.index.html');
    }

   
}

?>
