<?php
use Cyteam\Message\ZhichiMessage;
/**
 *    专家在线控制器
 *
 *    @author    Garbin
 *    @usage    none
 */
class ProfessorApp extends MallbaseApp
{
    function __construct()
    {
       parent::__construct();
       
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
        $this->_view->template_dir = PROJECT_PATH . "/view/".LANG."/mall/{$template_name}/";
        $this->_view->compile_dir  = ROOT_PATH . "/temp/compiled/mall/{$template_name}/";
        $this->_view->res_base     = SITE_URL . "/view/".LANG."/mall/{$template_name}/styles/{$style_name}";
    }
    /*code  String  响应码（1000:成功;1001:失败;1002:access_token校验失败;）
        data    JsonNode    实时数据信息
        adminSize   Integer 在线客服数量
        onlineUserSize  Integer 在线用户数量
        waitUserSize    Integer 排队用户数量
        robotUserSize   Integer 与机器人会话数量
        adminList   list    在线客服列表
        id  String  客服id
        groupName   list    客服所在技能组列表
        count   Long    客服实时接待用户数量
        email   String  客服邮箱
        status  String  客服状态1-在线，2-忙碌
        name    String  客服姓名
    */
    function online()
    {
        $zhichi=new ZhiChiMessage();
        $access_token=$zhichi->getAccessToken();
        $actual_data=$zhichi->getActualData($access_token);
        $data=json_decode($actual_data,true);
        $scripts='';
        if($data['status']){
            $adminList=$data['data']['adminList'];

            if($adminList){
                //如果用户登录拼接用户信息
                if($this->visitor->has_login){
                    $userInfo = $this->visitor->get();
                    $str="&partnerId={$userInfo['user_id']}&uname={$userInfo['nickname']}&realname={$userInfo['user_name']}&tel={$userInfo['phone_mob']}&face={$userInfo['avatar']}";
                }else{
                    $str='';
                }
                foreach ($adminList as $key => $value) {
                    if(empty($value['groupId'])){
                        unset($adminList[$key]);
                        continue;
                    }
//                    $groupId=$value['groupId'][0];
                    $groupId="8e421d82d4384f169a5af4c98d3c6299";
                    $adminList[$key]['url']="http://www.sobot.com/chat/h5/index.html?sysNum=2b17cdee375a475e963aeed478c37fbf{$str}&modulType=2&groupId={$groupId}";
                }
            }else{
                return $this->show_warning('暂无专家在线');
            }
        }
        $this->assign('title','专家在线');
        $this->assign('adminList',$adminList);
        return $this->display('professor.online.html');
        $this->assign('title','RCTAILOR-酷客中心-地址管理');
        $this->assign('title_info','地址管理');
        /* 取得列表数据 */
        import("address.lib");
        $address = new Address($this->visitor->get("user_id"));
        $addresses = $address->_list();
        
        //var_dump($addresses);
       // var_dump($this->visitor->get('def_addr'));

        //默认地址
        $this->assign('def_addr',$this->visitor->get('def_addr'));
        //print_r($_SESSION['user_info']['def_addr']);
        $this->assign('count_addresses',count($addresses));
        $this->assign('addresses', $addresses);

     
        /* 当前用户中心菜单 */
         
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('my_address'));
        
        $this->assign('ac', ACT);
        $this->assign('type', 'user');

        //ns add 获取用户信息
        $user = $this->visitor->get();
        $this->assign('user',$user);
        
        /* 头像 add by xiao5 START */
        require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
        $objAvatar = new Avatar();
        
        //获取头像
        $avatar = $objAvatar->avatar_show($this->visitor->get('user_id'),'big');
        $this->assign('avatar', $avatar);
        
        /* 当前用户中心菜单 */
        $this->assign('_member_menu', $this->_get_member_menu('user'));
        $this->assign('_curitem', 'my_address');
        
        $this->display('my_address.index.html');
    }

    function edit_def_addr(){
        $args = $this->get_params();
        $def_addr_id = isset($args[0]) ? intval($args[0]) : 0;
        if($def_addr_id){
            import("address.lib");
            $this->visitor->setDef("def_addr=1", $def_addr_id); //设置默认地址
            
			//更新缓存
            $_SESSION['user_info']['def_addr'] = $def_addr_id;
            $this->show_message('默认地址设置成功！');
        }else{
            $this->show_warning('no_such_address');
            return;
        }

    }

    /**
     *    添加地址
     *
     *    @author    Garbin
     *    @return    void
     */
    function add()
    {print_r('s');
        if (!IS_POST)
        {
            /* 当前位置 */
            /*$this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
                             LANG::get('my_address'), 'index.php?app=my_address',
                             LANG::get('add_address'));*/
            //$this->import_resource('mlselection.js, jquery.plugins/jquery.validate.js');
            $this->assign('form_act', '添加收货地址');
            $this->assign('act', 'add');
            $this->_get_regions();
            header('Content-Type:text/html;charset=' . CHARSET);
            $this->display('my_address.form.html');
        }
        else
        {
            /* 电话和手机至少填一项 */
            if (!$_POST['phone_tel'] && !$_POST['phone_mob'])
            {
                $this->pop_warning('phone_required');

                return;
            }

            $region_name = $_POST['region_name'];
            $data = array(
                'user_id'       => $this->visitor->get('user_id'),
                'consignee'     => $_POST['consignee'],
                'al_name'       => $_POST['al_name'],
                //'region_id'     => $_POST['region_id'],
                'region_id' => $_POST['region_list'],
                'region_name'   => $_POST['region_name'],
                'address'       => $_POST['address'],
                'zipcode'       => $_POST['zipcode'],
                'phone_tel'     => $_POST['phone_tel'],
                'phone_mob'     => $_POST['phone_mob'],
                'email'         => $_POST['email'],
            );
            $model_address =& m('address');
            if (!($address_id = $model_address->add($data)))
            {
                $this->pop_warning($model_address->get_error());

                return;
            }
            $this->pop_warning('ok', APP.'_'.ACT);
        }
    }
    function edit()
    {
        $addr_id = empty($_GET['addr_id']) ? 0 : intval($_GET['addr_id']);
        if (!$addr_id)
        {
            echo Lang::get("no_such_address");
            return;
        }
        if (!IS_POST)
        {
            $model_address =& m('address');
            $find_data     = $model_address->find("addr_id = {$addr_id} AND user_id=" . $this->visitor->get('user_id'));
            if (empty($find_data))
            {
                echo Lang::get('no_such_address');

                return;
            }
            $address = current($find_data);

            /* 当前位置 */
            $this->_curlocal(LANG::get('member_center'),    'index.php?app=member',
                             LANG::get('my_address'), 'index.php?app=my_address',
                             LANG::get('edit_address'));

            /* 当前用户中心菜单 */
            /*$this->_curitem('my_address');

            
            /* 当前所处子菜单 */
            header('Content-Type:text/html;charset=' . CHARSET);
            $this->_curmenu('edit_address');

            $this->assign('address', $address);
            //$this->import_resource('mlselection.js, jquery.plugins/jquery.validate.js');
            $this->assign('form_act', '修改收货地址');
            $this->assign('act', 'edit');
            $this->_get_regions();
            $this->display('my_address.form.html');
        }
        else
        {
            /* 电话和手机至少填一项 */
            if (!$_POST['phone_tel'] && !$_POST['phone_mob'])
            {
                $this->pop_warning('phone_required');

                return;
            }
            $data = array(
                'consignee'     => $_POST['consignee'],
                //'region_id'     => $_POST['region_id'],
                'region_id' => $_POST['region_list'],
                'al_name'       => $_POST['al_name'],
                'region_name'   => $_POST['region_name'],
                'address'       => $_POST['address'],
                'zipcode'       => $_POST['zipcode'],
                'phone_tel'     => $_POST['phone_tel'],
                'phone_mob'     => $_POST['phone_mob'],
                'email'         => $_POST['email'],
            );
            $model_address =& m('address');
            $model_address->edit("addr_id = {$addr_id} AND user_id=" . $this->visitor->get('user_id'), $data);
            if ($model_address->has_error())
            {
                $this->pop_warning($model_address->get_error());

                return;
            }
            $this->pop_warning('ok', APP.'_'.ACT);
        }
    }
    function drop()
    {
        $addr_id = isset($_GET['addr_id']) ? trim($_GET['addr_id']) : 0;
        if (!$addr_id)
        {
            $this->show_warning('no_such_address');

            return;
        }
        $ids = explode(',', $addr_id);//获取一个类似array(1, 2, 3)的数组
        $model_address  =& m('address');
        $drop_count = $model_address->drop("user_id = " . $this->visitor->get('user_id') . " AND addr_id " . db_create_in($ids));
        if (!$drop_count)
        {
            /* 没有可删除的项 */
            $this->show_warning('no_such_address');

            return;
        }

        if ($model_address->has_error())    //出错了
        {
            $this->show_warning($model_address->get_error());

            return;
        }

        $this->show_message('drop_address_successed');
    }
    function _get_regions()
    {
        $model_region =& m('region');
        $regions = $model_region->get_list(0);
        if ($regions)
        {
            $tmp  = array();
            foreach ($regions as $key => $value)
            {
                $tmp[$key] = $value['region_name'];
            }
            $regions = $tmp;
        }
        $this->assign('regions', $regions);
    }
    /**
     *    三级菜单
     *
     *    @author    Garbin
     *    @return    void
     */
    function _get_member_submenu()
    {
        $menus = array(
            array(
                'name'  => 'address_list',
                'url'   => 'index.php?app=my_address',
            ),
/*            array(
                'name'  => 'add_address',
                'url'   => 'index.php?app=my_address&act=add',
            ),*/
        );
/*        if (ACT == 'edit')
        {
            $menus[] = array(
                'name' => 'edit_address',
                'url'  => '',
            );
        }*/
        return $menus;
    }

    /* 获取地区id，全部以逗号分隔  */
    function _get_region_array($id)
    {
        $re_array = array();
        if(!isset($id) || empty($id))
        {
            return false;
        }
        $mod_region =& m('region');
        $cate = $this->$mod_region->get_list($_GET['id']);
        foreach ($cate as $key => $val)
        {
            $child = $this->_region_mod->get_list($val['region_id']);
            if (!$child || empty($child))
            {
                $cate[$key]['switchs'] = 0;
            }
            else
            {
                $cate[$key]['switchs'] = 1;
            }
        }
        return ;
    }



}

?>