<?php

/* 会员等级控制器 */
class LvApp extends BackendApp
{
    var $_user_mod;
	var $_account_mod;
    function __construct()
    {
        $this->UserApp();
    }

    function UserApp()
    {
        parent::__construct();
        $this->_user_mod =& m('member');
        $this->_lv_mod =& m('memberlv');
        $this->_account_mod =& m('account');
    }

    function index()
    {
    	//查询条件
        $conditions = $this->_get_query_conditions(array(
            array(
                'field' => $_GET['field_name'],
                'name'  => 'field_value',
                'equal' => 'like',
            ),
        ));
        //更新排序
        if (isset($_GET['sort']) && !empty($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
             $sort  = 'user_id';
             $order = 'asc';
            }
        }
        else
        {
            if (isset($_GET['sort']) && empty($_GET['order']))
            {
                $sort  = strtolower(trim($_GET['sort']));
                $order = "";
            }
            else
            {
                $sort  = 'member_lv_id';
                $order = 'asc';
            }
        }
        $page = $this->_get_page(30);
		$page['limit'] = 50;
        $users = $this->_lv_mod->find(array(
            'fields' => 'this.*',
            'conditions' => '1=1' . $conditions,
            'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        ));
        foreach ($users as $key => $val)
        {
//            if($key ==2)$users[$key]['name'] = '麦富迪达人（未实名认证）';
//            if($key ==3)$users[$key]['name'] = '麦富迪达人（已实名认证）';

            if ($val['priv_store_id'] == 0 && $val['privs'] != '')
            {
                $users[$key]['if_admin'] = true;
            }
            $users[$key]['bd_dis_count'] = $val['bd_dis_count']*10;
            $users[$key]['cy_dis_count'] = $val['cy_dis_count']*10;
        }
       
        $this->assign('users', $users);
        $page['item_count'] = $this->_lv_mod->getCount();
        $this->_format_page($page);
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('page_info', $page);
        $this->assign('tname', $this->_lv_mod->_typename);
        /* 导入jQuery的表单验证插件 */
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));
        $this->assign('query_fields', array(
            'user_name' => LANG::get('user_name'),
            'email'     => LANG::get('email'),
            'real_name' => LANG::get('real_name'),
        ));
        $this->assign('sort_options', array(
            'reg_time DESC'   => LANG::get('reg_time'),
            'last_login DESC' => LANG::get('last_login'),
            'logins DESC'     => LANG::get('logins'),
        ));
      
        $this->display('lv.index.html');
    }

    function add()
    {
        if (!IS_POST)
        {
            $this->assign('user', array(
                'default_lv' => 0,
            ));
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $ms =& ms();
            $this->assign('set_avatar', $ms->user->set_avatar());
            $this->assign('tname', $this->_lv_mod->_typename);
            $this->display('lv.form.html');
        }
        else
        {
            $lv_name = trim($_POST['name']);

            if (strlen($lv_name) < 3 || strlen($lv_name) > 25)
            {
                $this->show_warning('lv_name_length_error');

                return;
            }

            /* 保存本地资料 */
            $data = array(
                'name' => $_POST['name'],
                'dis_count'    => $_POST['dis_count'],
                'bd_dis_count' => $_POST['bd_dis_count'],
                'cy_dis_count' => $_POST['cy_dis_count'],
                'default_lv'   => $_POST['default_lv'],
                'experience'   => $_POST['experience'],
				'lv_type'   => $_POST['lv_type'],
//                 'reg_time'  => gmtime(),
            );
            
            $lv_id = $this->_lv_mod->add($data,false,0);
            if (!$lv_id)
            {
            	$this->show_warning($this->_lv_mod->get_error());
            	return 0;
            }
			
            /* 后台会员等级添加操作记录 */
            import('operationlog.lib');
            $type='add';
            $operate_log=new OperationLog();
            $mark="管理员".$_SESSION['admin_info']['user_name']."新增会员等级:";
            $tmp='';
            $member_type=$this->_lv_mod->_typename;
            if(!empty($_POST['name'])){
            	$tmp.=",等级名称（{$_POST['name']}）";
            }
            if(!empty($_POST['lv_type'])){
            	$tmp.=",等级类型（{$member_type[$_POST['lv_type']]}）";
            }
            if(!empty($_POST['bd_dis_count'])){
            	$bd_dis_count=sprintf('%%',$_POST['bd_dis_count']*10);
            	$tmp.=",BD提成比例（".strval($_POST['bd_dis_count']*10).'%'."）";
            }
            
            if(!empty($_POST['dis_count'])){
                $dis_count=sprintf('%%',$_POST['dis_count']*10);
                $tmp.=",折扣或提成比例（".strval($_POST['dis_count']*10).'%'."）";
            }
            if(isset($_POST['default_lv'])){
            	if($_POST['default_lv']){
            		$tmp.=",设置为默认等级";
            	}
            	$tmp.=",不设置为默认等级";
            }
            if(!empty($_POST['experience'])){
            	$tmp.=",等级所需经验值或业绩({$_POST['experience']})";
            }
            if(!empty($_FILES['lv_logo'])){
            	$tmp.=",添加等级logo";
            }
            $mark.=ltrim($tmp,',');
            $operate_log->operation_log('',$type,$mark);
			/* end */
            
            if (!empty($_FILES['lv_logo']))
            {
                $portrait = $this->_upload_logo($lv_id);
                if ($portrait === false)
                {
                    return;
                }

                $portrait && $this->_lv_mod->edit($lv_id, array('lv_logo' => $portrait),0);
            }


            $this->show_message('add_ok',
                'back_list',    'index.php?app=lv',
                'continue_add', 'index.php?app=lv&amp;act=add'
            );
        }
    }

    /*检查会员等级名称的唯一性*/
    function  check_name()
    {
          $lv_name = empty($_GET['name']) ? null : trim($_GET['name']);
          if (!$lv_name)
          {
              echo ecm_json_encode(false);
              return ;
          }

        $info = $this->_lv_mod->get("name='{$lv_name}'");
        if (!empty($info))
        {
            $this->_error('lv_name_exists');

            echo ecm_json_encode(false);
            return ;
        }

        echo ecm_json_encode(true);
        return ;
    }
    /*检查会员等级默认唯一性*/
    function check_defaultlv(){
//     	$info = $this->_lv_mod->get("default_lv='1' and lv_type='".$_POST['ty']."'");
    	$info = $this->_lv_mod->get_default_level(array("lv_type"=>$_REQUEST['ty']));
//     	var_dump($info);
    	$res = array('res'=>1);
    	if ($info){
    		echo ecm_json_encode(array('res'=>0,'name'=>$info['name']));
    		return ;
    	}
    	echo ecm_json_encode($res);
    	return ;
    }
    

    function edit()
    {
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!IS_POST)
        {
            /* 是否存在 */
            $user = $this->_lv_mod->get_info($id);
            if (!$user)
            {
                $this->show_warning('lv_empty');
                return;
            }


            $this->assign('user', $user);
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->display('lv.form.html');
        }
        else
        {
            $data = array(
                'dis_count'    => trim($_POST['dis_count']),
                'bd_dis_count' => trim($_POST['bd_dis_count']),
                'cy_dis_count' => $_POST['cy_dis_count'],
                'default_lv'   => trim($_POST['default_lv']),
                'experience'   => trim($_POST['experience']),
                'name'   => trim($_POST['name']),
            );
            $user = $this->_lv_mod->get_info($id);
             /* 后台会员等级修改操作记录 */
            import('operationlog.lib');
            $type='edit';
            $operate_log=new OperationLog();
            $mark="管理员".$_SESSION['admin_info']['user_name']."修改了会员等级:";
            $tmp='';
            $member_type=$this->_lv_mod->_typename;
            if($_POST['name']!=$user['name']){
            	$tmp.=",等级名称从{$user['name']}修改为{$_POST['name']}";
            }
/*             if($_POST['lv_type']!=$user['lv_type']){
            	$tmp.=",等级类型从{$member_type[$user['lv_type']]}修改为{$member_type[$_POST['lv_type']]}";
            } */
            if($_POST['dis_count']!=$user['dis_count']){
            	$dis_count=sprintf('%%',$_POST['dis_count']*10);
            	$tmp.=",折扣或提成比例从".strval($user['dis_count']*10).'%'."修改为".strval($_POST['dis_count']*10)."%";
            }
            if(!empty($_POST['bd_dis_count'])){
                $bd_dis_count=sprintf('%%',$_POST['bd_dis_count']*10);
                $tmp.=",BD提成比例（".strval($_POST['bd_dis_count']*10).'%'."）";
            }
            
            if($_POST['default_lv']!=$user['default_lv']){
            	if($_POST['default_lv']){
            		$tmp.=",修改为{$member_type[$user['lv_type']]}的默认等级";
            	}
            	$tmp.=",修改为{$member_type[$user['lv_type']]}非默认等级";
            }
            if($_POST['experience']!=$user['experience']){
            	$tmp.=",等级所需经验值或业绩从{$user['experience']}修改为{$_POST['experience']}";
            }
            $mark.=ltrim($tmp,',');
            $operate_log->operation_log('',$type,$mark);
			/* end */
            
            if (!empty($_FILES['lv_logo']['name']))
            {
                $portrait = $this->_upload_logo($id);
                if ($portrait === false)
                {
                    return;
                }
                $data['lv_logo'] = $portrait;
            }

            /* 修改本地数据 */
            $this->_lv_mod->edit($id, $data,0);

            $this->show_message('edit_ok',
                'back_list',    'index.php?app=lv',
                'edit_again',   'index.php?app=lv&amp;act=edit&amp;id=' . $id
            );
        }
    }

    function drop()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('no_lv_to_drop');
            return;
        }
     

        $ids = explode(',', $id);

    
        if (!$this->_lv_mod->drop($ids))
        {
            $this->show_warning($this->_lv_mod->get_error());

            return;
        }

        $this->show_message('drop_ok');
    }

    /**
     * 会员组logo
     *
     * @param int $user_id
     * @return mix false表示上传失败,空串表示没有上传,string表示上传文件地址
     */
    function _upload_logo($lv_id)
    {
        $file = $_FILES['lv_logo'];
        if ($file['error'] != UPLOAD_ERR_OK)
        {
            return '';
        }

        import('uploader.lib');
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE);
        $uploader->addFile($file);
        if ($uploader->file_info() === false)
        {
            $this->show_warning($uploader->get_error(), 'go_back', 'index.php?app=user&amp;act=edit&amp;id=' . $lv_id);
            return false;
        }

        $uploader->root_dir(ROOT_PATH);
        return $uploader->save('data/files/mall/lv_logo/' . ceil($lv_id / 500), $lv_id);
    }
}

?>
