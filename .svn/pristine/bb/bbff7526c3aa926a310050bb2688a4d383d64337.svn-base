<?php
/**
 *    报价记录控制器
 *    ns add
 */
class Demand_offerApp extends MemberbaseApp{
    public $_mod_demanditem;
    public $_mod_demand;
    public $_mod_demandoffer;
	 function __construct()
    {
        $this->_mod_demanditem = &m('demanditem');
        $this->_mod_demand = &m('demand');
        $this->_mod_demandoffer = &m('demandoffer');
        $this->Demand_offerApp();
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

    function Demand_offerApp()
    {
        parent::__construct();
    }
    
    //默认
    function index()
    {   
        //ns add 获取信息
        $page = $this->_get_page(10);    //获取分页信息
        
        $status = $_GET['status'] ? $_GET['status'] : '';
        $con = '';
        if($status){
            $con = " AND status ='{$status}' ";
        }
        $ofs = $this->_mod_demandoffer->find(array(
                'conditions' => "1 = 1 AND cf_id = '{$this->visitor->get('user_id')}'  ".$con,
                'count' => true,
                'limit' => $page['limit'],
                'order' => 'sub_time DESC',
        ));
        //ns add 添加个判断
        if($ofs){
            foreach ($ofs as $row){
                $ids[] = $row['md_id'];
                $offers[$row['md_id']] = $row; 
                $list[] = $this->_mod_demand->get(array(
                    'conditions' => "md_id=".$row['md_id'],
                ));
            }

            $mMem = &m('member');
            foreach ($list as &$row){
                $row['params'] = unserialize($row['params']);
                $row['offer'] = $offers[$row['md_id']];
                $ms = $mMem->get("user_id = '{$row['user_id']}'");
                $row['uname'] = $ms['nickname'];
            }

            
            $page['item_count'] = $this->_mod_demandoffer->getCount();
            $this->_format_page($page);
            if(!empty($status)){
                $page['parameter'] = '?status='.$status;
            }

            $this->assign('page_info', $page);          //将分页信息传递给视图，用于形成分页条


        }
        //中标
        $zb = $this->_mod_demandoffer->find(array(
        'conditions' => "1 = 1 AND cf_id = '{$this->visitor->get('user_id')}'  AND status ='2' ",
        ));
        //所有
        $sy = $this->_mod_demandoffer->find(array(
        'conditions' => "1 = 1 AND cf_id = '{$this->visitor->get('user_id')}' ",
        ));
        $this->assign('allCount',count($sy));
        $this->assign('zbCount',count($zb));   
        


        $this->assign('list',$list);
        $this->assign('status', $status);

        //ns add 获取左边菜单、头像信息
        $user = $this->visitor->get();
        // 头像
        require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
        $objAvatar = new Avatar();
        //获取头像
        $avatar = $objAvatar->avatar_show($user['user_id'],'big');
        $this->assign('avatar', $avatar);
        $this->assign('type','tailor');
        $this->assign('user',$user);
        $this->assign('ac', ACT);
        /* 当前用户中心菜单 */
        $this->assign('_member_menu', $this->_get_member_menu('user'));
        $this->assign('_curitem', 'demand_offer');
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('demand_offer'));
        
        $this->display('demand_offer.index.html');
    }
}

?>
