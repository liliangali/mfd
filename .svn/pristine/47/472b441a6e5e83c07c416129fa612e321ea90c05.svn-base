<?php

/* 后台操作日志 operation.app.php */
class OperationApp extends BackendApp
{
    var $_operation_mod;
    var $_operationlog_mod;
    var $_operate_type;
    function __construct()
    {
        $this->OperationApp();
    }

    function OperationApp()
    {
        parent::__construct();
        $this->_operation_mod =& m('operatorloglogs');
        $this->_operationlog_mod=&m('operatorlog');
        $this->_operate_type=array(
        		'0'=>'请选择',
        		'add'=>'新建',
        		'edit'=>'修改',
        		'drop'=>'删除',
        		'land'=>'登陆'
        );
    }
    function index()
    {
    	include (ROOT_PATH.'/mfd/includes/operation.inc.php');
    	Lang::load(lang_file('admin/admin'));
//     	var_dump($_GET);
//     	//var_dump($_GET['field_module']);
//     	echo '<br/>';
    	 if (empty($_GET['field_module'])){
    	 	$field_module=$_GET['field_module'];
    	 	$oprate_key='';
    	 	$_GET['field_module']='';
    	 }else{
    	 	$field_module=$_GET['field_module'];
    	 	$_GET['field_module']=LANG::get($_GET['field_module']);
    	 	$oprate_key="operate_key like '%{$_GET['field_module']}";
    	 }
    	 //var_dump($_GET['field_module']);
    	 if(empty($_GET['field_submodule'])){
    	 	$field_submodule=$_GET['field_submodule'];    	 
    	 	$_GET['field_submodule']='';
    	 }else{
    	 	$field_submodule=$_GET['field_submodule'];    	 	
    	 	$_GET['field_submodule']=LANG::get($_GET['field_submodule']);
    	 	$oprate_key.=":{$_GET['field_submodule']}";
    	 }
    	 if(empty($_GET['field_feature'])){
    	 	$field_feature=$_GET['field_feature'];
    	 	$_GET['field_feature']='';
    	 }else{
    	 	$field_feature=$_GET['field_feature'];
    	 	$_GET['field_feature']=LANG::get($_GET['field_feature']);
    	 	$oprate_key.=":{$_GET['field_feature']}";
    	 }
    	 //var_dump($_GET['field_submodule']);
    	 if(empty($_GET['operate_type'])){
    	 	$oprate_type=$_GET['operate_type'];
    	 	$_GET['operate_type']='';
    	 }else {
    	 	$oprate_type=$_GET['operate_type'];
    	 }
    	 if($oprate_key){
    	 	$oprate_key.="%'";
    	 }
    	//查询条件
    	$conditions = $this->_get_query_conditions(array(
    			array(
    					'field' => 'username',
    					'name'  => 'field_value',
    					'equal' => 'like',
    			),
    			array(
    					'field'=>'operate_type',
    					'name'	=>'operate_type',
    					'equal'=>'='
    			),
    			array(
    					'field'=>'dateline',
    					'name'=>'add_time_from',
    					'equal'=>'>=',
    					'handler'=>'strtotime',
    			),
    			array(
    					'field'=>'dateline',
    					'name'=>'add_time_to',
    					'equal'=>'<=',
    					'handler'=>'strtotime_end'
    			),
    	));
    	if(!empty($oprate_key)){
    		$conditions.=" AND {$oprate_key}";
    	}
/*     	if($) */
     	$_GET['field_module']=$field_module;
    	$_GET['field_submodule']=$field_submodule;
    	$_GET['field_feature']=$field_feature;
    	$_GET['operate_type']=$oprate_type; 
    	$page = $this->_get_page();
    	$users = $this->_operation_mod->find(array(
    			'fields' => 'this.*',
    			'conditions' => '1=1' . $conditions,
    			'limit' => $page['limit'],
    			'order' => "id desc",
    			'count' => true,
    	));
//     	echo '<br/>';
//      	var_dump($conditions);
    	foreach ($users as $key => $val)
    	{
    		$users[$key]['dateline']=date('Y-m-d H:i:s',$val['dateline']);
    		$users[$key]['operate_type']=$this->_operationlog_mod->typedata{$val['operate_type']};
    	}
    	
    	$module=array(0=>'请选择');
    	while (list($k,$v)=each($menu_data)){
    		$module[$k]=Lang::get($k);
    	}
    	$html=array(0=>'请选择');
    	if(!empty($_GET['field_module'])){
    		while (list($k,$v)=each($menu_data{$field_module})){
    			$html[$k]=Lang::get($k);
    		}
    	}
    	$feature_items=array(0=>'请选择');
    	if(!empty($_GET['field_submodule'])){
    		if(is_array($menu_data{$field_module}{$field_submodule})){
    			while(list($k,$v)=each($menu_data{$field_module}{$field_submodule})){
    				$feature_items[$k]=Lang::get($k);
    			}
    		}
    	}
    	$this->assign('module',$module);
    	$this->assign('submodule',$html);
    	$this->assign('feature',$feature_items);
    	$this->assign('operate_type',$this->_operate_type);
    	$this->assign('users', $users);
    	$page['item_count'] = $this->_operation_mod->getCount();
    	$this->_format_page($page);
    	$this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
    	$this->assign('page_info', $page);
    	
    	$this->assign('tname', $this->_typename);
    	/* 导入jQuery的表单验证插件 */
        $this->import_resource(array('script' => 'inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                                      'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
    	$this->assign('query_fields', array(
    			'username' => LANG::get('username'),
    			'operate_key'     => LANG::get('operate_key'),
    	));
    	$this->assign('sort_options', array(
    			'reg_time DESC'   => LANG::get('reg_time'),
    			'last_login DESC' => LANG::get('last_login'),
    			'logins DESC'     => LANG::get('logins'),
    	));
    	 
    	$this->display('operatorlog.index.html');
    }
    function view()
    {
    	//查询条件
    	$conditions = $this->_get_query_conditions(array(
    			array(
    					'field' => $_GET['field_name'],
    					'name'  => 'field_value',
    					'equal' => 'like',
    			),
    	));
    
    
    	$page = $this->_get_page();
    	$users = $this->_operation_mod->find(array(
    			'fields' => 'this.*',
    			'conditions' => '1=1' . $conditions,
    			'limit' => $page['limit'],
    			'order' => "id desc",
    			'count' => true,
    	));
    	//         foreach ($fashion_list as $key=>$value){
    	//             $fashion_list[$key]['pubdate']=date('Y-m-d H:i:s',$value['pubdate']);
    	//         }
    	foreach ($users as $key => $val)
    	{
    		$users[$key]['dateline']=date('Y-m-d H:i:s',$val['dateline']);
    		if ($val['priv_store_id'] == 0 && $val['privs'] != '')
    		{
    			$users[$key]['if_admin'] = true;
    		}
    		$users[$key]['memo']=stripslashes($val['memo']);
    	}
    
    	$this->assign('users', $users);
    	$page['item_count'] = $this->_operation_mod->getCount();
    	$this->_format_page($page);
    	$this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
    	$this->assign('page_info', $page);
    	$this->assign('tname', $this->_typename);
    	/* 导入jQuery的表单验证插件 */
    	$this->import_resource(array(
    			'script' => 'jqtreetable.js,inline_edit_admin.js',
    			'style'  => 'res:style/jqtreetable.css'
    	));
    	$this->assign('query_fields', array(
    			'username' => LANG::get('username'),
    			'operate_key'     => LANG::get('operate_key'),
    	));
    	$this->assign('sort_options', array(
    			'reg_time DESC'   => LANG::get('reg_time'),
    			'last_login DESC' => LANG::get('last_login'),
    			'logins DESC'     => LANG::get('logins'),
    	));
    	$this->display('operatorlog_logs.index.html');
    }
    function info(){
    	$id=isset($_GET['id'])?$_GET['id']:'';
    	if(empty($id)){
    		$this->show_warning('id为空');
    		return ;
    	}
    	$info=$this->_operationlog_mod->get($id);
    	$info['dateline']=date('Y-m-d H:i:s',$info['dateline']);
    	$info['operate_type']=$this->_operationlog_mod->typedata{$info['operate_type']};
    	$info['forward_data']=unserialize($info['forward_data']);
    	$info['modify_data']=unserialize($info['modify_data']);
    	$this->assign('info',$info);
    	$this->display('operatorlog.info.html');
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
            $this->assign('tname', $this->_typename);
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
                'default_lv'   => $_POST['default_lv'],
                'experience'   => $_POST['experience'],
				'lv_type'   => $_POST['lv_type'],
//                 'reg_time'  => gmtime(),
            );
            
            $lv_id = $this->_operation_mod->add($data);
            if (!$lv_id)
            {
            	$this->show_warning($this->_operation_mod->get_error());
            	return 0;
            }


            if (!empty($_FILES['lv_logo']))
            {
                $portrait = $this->_upload_logo($lv_id);
                if ($portrait === false)
                {
                    return;
                }

                $portrait && $this->_operation_mod->edit($lv_id, array('lv_logo' => $portrait));
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

        $info = $this->_operation_mod->get("name='{$lv_name}'");
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
//     	$info = $this->_operation_mod->get("default_lv='1' and lv_type='".$_POST['ty']."'");
    	$info = $this->_operation_mod->get_default_level(array("lv_type"=>$_REQUEST['ty']));
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
            $user = $this->_operation_mod->get_info($id);
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
                'default_lv'   => trim($_POST['default_lv']),
                'experience'   => trim($_POST['experience']),
                'name'   => trim($_POST['name']),
            );
            
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
            $this->_operation_mod->edit($id, $data);

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

    
        if (!$this->_operation_mod->drop($ids))
        {
            $this->show_warning($this->_operation_mod->get_error());

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
    function get_submodule(){
    	$module=$_POST['module'];
    	$html="<option value='0' selected>请选择</option>";
    	if (empty($module)){
    		return $this->json_result($html);
    	}
    	include (ROOT_PATH.'/mfd/includes/operation.inc.php');
    	Lang::load(lang_file('admin/admin'));
    	
    	foreach ($menu_data{$module} as $k=>$v){
    		$html.="<option value=".$k.">".Lang::get($k)."</option>";
    	}
    	$this->json_result($html);
    }
    function get_feature(){
    	$module=$_POST['module'];
    	$submodule=$_POST['submodule'];
    	$html="<option value='0' selected>请选择</option>";
    	if (empty($module) || empty($submodule)){
    		return $this->json_result($html);
    	}
    	include (ROOT_PATH.'/mfd/includes/operation.inc.php');
    	Lang::load(lang_file('admin/admin'));
    	 
    	foreach ($menu_data{$module}{$submodule} as $k=>$v){
    		$html.="<option value=".$k.">".Lang::get($k)."</option>";
    	}
    	$this->json_result($html);
    }
}

?>
