<?php

/**
 *    我的收货地址控制器
 *
 *    @author    Garbin
 *    @usage    none
 */
class My_addressApp extends MemberbaseApp
{
    /**
     * 重写模板文件
     * @return void
     * @access public
     * @see _config_view
     * @version 2.0.0 (2015-05-05)
     * @author nings
     */
    function _config_view()
    {
        parent::_config_view();
        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();
    }
    function index()
    {
        /* 取得列表数据 */
        import("address.lib");

        //默认地址
        $this->assign('def_addr',$this->visitor->get('def_addr'));
        //print_r($_SESSION['user_info']['def_addr']);
        $this->assign('count_addresses',count($addresses));

        $address = new Address($this->visitor->get("user_id"));
        $list = $address->_list();
        $this->assign('addresslist', $list);


        /* 当前用户中心菜单 */
        
        $this->assign('ac', ACT);
        $this->assign('app', APP);
        $this->assign('type', 'user_set');

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
        $this->assign('_member_menu', $this->_get_member_menu('user_set'));
        $this->assign('_curitem', 'my_address');
        
        $this->_config_seo('title', '我的麦富迪 - 地址管理');
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
     * 收货地址 -新增
     * @author Tab
     * @return json
     */
    function address(){
        $userId = $this->visitor->get("user_id");
         if(strlen($_POST['data']['consignee'])  > 18){
                $this->json_error("收件人字数不能超过6位！");
                return;
         }
        if(!check::isMobile($_POST['data']['phone_mob'])){
                $this->json_error("手机输入有误！");
                return;
        }
        if(!check::isPostNum($_POST['data']['zipcode'])){
                $this->json_error("邮编输入有误！");
                return;
        }
        if (isset($_POST['data']['region_list']) && $_POST['data']['region_list']){
            $model_region =& m('region');
            $_regions = $model_region->find(array(
                    'conditions' => "region_id IN ({$_POST['data']['region_list']})",
                    'fields'     => "region_id, region_name",
            ));
            //验证地区
            if (array_diff(i_array_column($_regions, 'region_name'),explode("\t", $_POST['data']['region_name']))){
                $this->json_error("选择地区错误！");
                return;
            }
            $_POST['data']['region_id'] = $_POST['data']['region_list'];
            unset($_POST['data']['region_list']);
            unset($_POST['data']['text']);
            $iData = array();
            $iData = $_POST['data'];
            $iData['user_id'] = $userId;
            $model_address = &m("address");
            if (!$model_address->add($iData)){
                $this->json_error("添加地址错误！");
                return;
            }
            import("address.lib");
            $address = new Address($userId);
            $list = $address->_list();
            $this->assign('addresslist', $list);
            $content = $this->_view->fetch($this->_template_file."address/list.html");
            $this->json_result($content);
            return;
        }
        
        $this->json_error("信息添加不全！");
        return;
    
    }
    /**
     * 收货地址 - 修改
     * @author Tab
     * @return json
     */
    function uAddress(){
        if ($_POST['data']['addr_id']){
            if(strlen($_POST['data']['consignee'])  > 18){
                $this->json_error("收件人字数不能超过6位！");
                return;
            }
            if(!check::isMobile($_POST['data']['phone_mob'])){
                    $this->json_error("手机输入有误！");
                    return;
            }
            if(!check::isPostNum($_POST['data']['zipcode'])){
                    $this->json_error("邮编输入有误！");
                    return;
            }
            $model_region =& m('region');
            $_regions = $model_region->find(array(
                    'conditions' => "region_id IN ({$_POST['data']['region_list']})",
                    'fields'     => "region_id, region_name",
            ));

            //验证地区
            if (array_diff(i_array_column($_regions, 'region_name'),explode("\t", $_POST['data']['region_name']))){
                $this->json_error("update_params_error");
                return;
            }
            $_POST['data']['region_id'] = $_POST['data']['region_list'];
            unset($_POST['data']['region_list']);
            unset($_POST['data']['text']);
            $iData = array();
            $iData = $_POST['data'];
            $model_address = &m("address");
            $info = $model_address->get($_POST['data']['addr_id']);
            
            if (!$info){
                $this->json_error("update_params_error");
                return;
            }
            
            //判断下用户是否已经修改.
            if (!array_diff($iData, array_intersect_assoc($iData,$info))){
                $this->json_result('','success');
                return; 
            }
            if (!$model_address->edit("addr_id = ".$iData['addr_id'],$iData)){
                $this->json_error("update_params_error");
                return;
            }
            $userId = $this->visitor->get("user_id");
            import("address.lib");
            $address = new Address($userId);
            $list = $address->_list();
            $this->assign('addresslist', $list);
            $content = $this->_view->fetch($this->_template_file."address/list.html");
            $this->json_result($content);
        }
    
    }
    /**
     * 收货地址 - 弹层模版
     * @author Tab
     * @return json
     */
    function getAddress(){
        $userId = $this->visitor->get("user_id");
        $addr_id = isset($_POST['addr_id']) ? intval($_POST['addr_id']) : '';
  
        import("address.lib");
        $address = new Address($userId);
        
        if ($addr_id){
            $_def = $address->defAddress($addr_id);
            $model_region =& m('region');
            $_regions = $model_region->find(array(
                    'conditions' => "region_id IN ({$_def['region_id']})",
                    'fields'     => "region_id, region_name",
            ));    
            $regionData = explode(',', $_def['region_id']);
            $regionsA =$this->getUA(0);
            $this->assign('regionsA', $regionsA);
            if (isset($regionData[0])){
                $regionsB = $this->getUA($regionData[0]);
                $this->assign('regionsB', $regionsB);
            }
            if (isset($regionData[1])){
                $regionsC = $this->getUA($regionData[1]);
                $this->assign('regionsC', $regionsC);
            }
            $this->assign('dfregions', $_regions);
            $this->assign('data', $_def);
            $this->assign('defdata', $regionData);
        }else{
            $regionsA =$this->getUA(0);
            $this->assign('regionsA', $regionsA);
        }
        $content = $this->_view->fetch($this->_template_file."address/edit.html");
        $this->json_result(array(
                'content'      =>  $content,
        ),'success');
    }

    /**
     * 收货地址 - 地区格式化
     * @author Tab
     * @return json
     */
    function getUA($id){
        $model_region =& m('region');
        $regions = $model_region->get_list($id);
        if ($regions)
        {
            $tmp  = array();
            foreach ($regions as $key => $value)
            {
                $tmp[$key] = $value['region_name'];
            }
            $regions = $tmp;
        }
        return $regions;
    }

    /**
     * 收货地址 - 删除
     * @author Tab
     * @return json
     */
    function dropAddr(){
        $userId = $this->visitor->get("user_id");
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if(!$id){
            $this->json_error('update_params_error');
            return;
        }
        import("address.lib");
        $address = new Address($userId);
        $res = $address->drop($id);
        if(!$res){
            $this->json_error($address->get_error());
            return;
        }

        $list = $address->_list();
        $this->assign('addresslist', $list);
        
        $content = $this->_view->fetch($this->_template_file."address/list.html");
        $this->json_result($content);
    }

    //ns add 默认地址
    function def_addr(){
        $def_addr_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if($def_addr_id){
            import("address.lib");
            $this->visitor->setDef("def_addr=".$def_addr_id, $def_addr_id); //设置默认地址
            //更新缓存
            $_SESSION['user_info']['def_addr'] = $def_addr_id;
            $this->json_result("设置默认成功！");
            return;
        }else{
                $this->json_error("设置默认地址失败！");
                return;
        }
    }

}

?>