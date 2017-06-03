<?php
/**
 *    宠物管理
 */
class My_petApp extends MemberbaseApp
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
        import("pet.lib");

        $pet = new Pet($this->visitor->get("user_id"));
        $list = $pet->_list();
        $this->assign('petlist', $list);


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
        
        $this->_config_seo('title', '我的麦富迪 - 宠物管理');
        $this->display('my_pet.index.html');
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
    function pet(){
        $userId = $this->visitor->get("user_id");
         if(strlen($_POST['data']['consignee'])  > 18){
                $this->json_error("宠物字数不能超过6位！");
                return;
         }
        if (isset($_POST['data']['region_list']) && $_POST['data']['region_list']){
            $model_pettype =& m('pettype');
            $_types = $model_pettype->find(array(
                    'conditions' => "type_id IN ({$_POST['data']['region_list']})",
                    'fields'     => "type_id, type_name",
            ));
            //验证地区
            if (array_diff(i_array_column($_types, 'type_name'),explode("\t", $_POST['data']['region_name']))){
                $this->json_error("选择地区错误！");
                return;
            }
            $_POST['data']['region_id'] = $_POST['data']['region_list'];
            unset($_POST['data']['region_list']);
            unset($_POST['data']['text']);
            $iData = array();
            $iData = $_POST['data'];
            $iData['user_id'] = $userId;
            $model_pet = &m("pet");
            if (!$model_pet->add($iData)){
                $this->json_error("添加宠物错误！");
                return;
            }
            import("pet.lib");
            $pet = new pet($userId);
            $list = $pet->_list();
            $this->assign('petlist', $list);
            $content = $this->_view->fetch($this->_template_file."pet/list.html");
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
    function uPet(){
        if ($_POST['data']['pet_id']){
            if(strlen($_POST['data']['name'])  > 18){
                $this->json_error("宠物字数不能超过6位！");
                return;
            }
            $model_pettype =& m('pettype');
            $_types = $model_pettype->find(array(
                    'conditions' => "type_id IN ({$_POST['data']['region_list']})",
                    'fields'     => "type_id, type_name",
            ));

            //验证地区
            if (array_diff(i_array_column($_types, 'type_name'),explode("\t", $_POST['data']['region_name']))){
                $this->json_error("update_params_error");
                return;
            }
            $_POST['data']['region_id'] = $_POST['data']['region_list'];
            unset($_POST['data']['region_list']);
            unset($_POST['data']['text']);
            $iData = array();
            $iData = $_POST['data'];
            $model_pet = &m("pet");
            $info = $model_pet->get($_POST['data']['pet_id']);
            if (!$info){
                $this->json_error("update_params_error");
                return;
            }
            //判断下用户是否已经修改.
            if (!array_diff($iData, array_intersect_assoc($iData,$info))){
                if($iData['gender'] == $info['gender']){
                    $this->json_result('','success');
                    return; 
                }
            }
            if (!$model_pet->edit("pet_id = ".$iData['pet_id'],$iData)){
                $this->json_error("update_params_error");
                return;
            }
            $userId = $this->visitor->get("user_id");
            import("pet.lib");
            $pet = new pet($userId);
            $list = $pet->_list();
            $this->assign('petlist', $list);
            $content = $this->_view->fetch($this->_template_file."pet/list.html");
            $this->json_result($content);
        }
    
    }
    /**
     * 弹层模版
     * @author Tab
     * @return json
     */
    function getPet(){
        $userId = $this->visitor->get("user_id");
        $pet_id = isset($_POST['pet_id']) ? intval($_POST['pet_id']) : '';  
        import("pet.lib");
        $pet = new pet($userId);
        
        if ($pet_id){
            $_def = $pet->defPet($pet_id);
            $model_pettype =& m('pettype');
            $_types = $model_pettype->find(array(
                    'conditions' => "type_id IN ({$_def['region_id']})",
                    'fields'     => "type_id, type_name",
            ));    
            $typeData = explode(',', $_def['region_id']);
            $typesA =$this->getUA(0);
            $this->assign('typesA', $typesA);
            if (isset($typeData[0])){
                $typesB = $this->getUA($typeData[0]);
                $this->assign('typesB', $typesB);
            }
            if (isset($typeData[1])){
                $typesC = $this->getUA($typeData[1]);
                $this->assign('typesC', $typesC);
            }
            $this->assign('dfregions', $_types);
            $this->assign('data', $_def);
            $this->assign('defdata', $typeData);
        }else{
            $typesA =$this->getUA(0);
            $this->assign('typesA', $typesA);
        }
        $content = $this->_view->fetch($this->_template_file."pet/edit.html");
        $this->json_result(array(
                'content'      =>  $content,
        ),'success');
    }

    /**
     * 宠物种类 - 种类格式化
     * @author Tab
     * @return json
     */
    function getUA($id){
        $model_pettype =& m('pettype');
        $types = $model_pettype->get_list($id);
        if ($types)
        {
            $tmp  = array();
            foreach ($types as $key => $value)
            {
                $tmp[$key] = $value['type_name'];
            }
            $types = $tmp;
        }
        return $types;
    }

    /**
     * 删除
     * @author Tab
     * @return json
     */
    function dropPet(){
        $userId = $this->visitor->get("user_id");
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if(!$id){
            $this->json_error('update_params_error');
            return;
        }
        import("pet.lib");
        $pet = new pet($userId);
        $res = $pet->drop($id);
        if(!$res){
            $this->json_error($pet->get_error());
            return;
        }

        $list = $pet->_list();
        $this->assign('petlist', $list);
        
        $content = $this->_view->fetch($this->_template_file."pet/list.html");
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