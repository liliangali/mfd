<?php

/**
 *    消息管理控制器
 *
 *    @author    daniel
 *    @usage    none
 */
class MessageTemplateApp extends BackendApp
{
    var $_messagetemplate_mod;
    var $_uploadedfile_mod;
	var $_lv_mod;
	var $_member_mod;
	var $_mt_app_url;
    function __construct()
    {
        $this->MessageTemplateApp();
    }

    function MessageTemplateApp()
    {
        parent::BackendApp();
        $this->_lv_mod =& m('memberlv');
        $this->_member_mod=&m('member');
        $this->member_lv = array(
        		'-1' => '所有会员',
        		'100' => '所有创业者',
        );
        
        $this->send_type = array(
        		'all' => '所有设备',
        		'ios' => 'IOS设备',
        		'android' => "安卓设备",
        
        );
        $user_lv=$this->_lv_mod->find(array(
        		'conditions'=>'1=1',
        		'fields'=>'*'
        ));
        $this->specials=array(
        	0=>'否',
        	1=>'是',
        );
        $this->_mt_app_url=array(
        		0=>array(
        				'text'=>'system',
        				'remark'=>'系统消息',
        		),
        		1=>array(
        				'text'=>'service',
        				'remark'=>'消息页面',
        		),
        		2=>array(
        				'text'=>'debit',
        				'remark'=>'酷卡',
        		),
        );
        $app_url=i_array_column($this->_mt_app_url, 'remark');
       foreach ($user_lv as $k=>$v){
       		$this->member_lv[$v['member_lv_id']]="&nbsp;{$v['name']}";
       }
       $this->assign('mt_app_url',$app_url);
        $this->assign('obj_type',$this->member_lv);
        $this->assign('send_type',$this->send_type);
        $this->assign('specials',$this->specials);
        $this->_messagetemplate_mod =& m('messagetemplate');
        $this->_uploadedfile_mod = &m('uploadedfile');
    }

    /**
     *    消息索引
     *
     *    @author    daniel
     *    @return    void
     */
    function index()
    {
        /* 处理cate_id */
        $cate_id = !empty($_GET['parent_id'])? intval($_GET['parent_id']) : 0;
        $conditions='';
        if ($cate_id > 0) //取得该分类及子分类cate_id
        {
            $icategory_mod = & m('icategory');
            $cate_ids = $icategory_mod->get_descendant($cate_id);
            if (!$cate_ids)
            {
                $this->show_warning('no_this_icategory');
                return;
            }
            $conditions="1=1";
        }
        
        //!empty($cate_ids)&& $conditions = 'mt.parent_id ' . db_create_in($cate_ids);
        if(!empty($cate_ids)){
        	$cate_tmp=$cate_ids;
        	
        }
        $conditions .= $this->_get_query_conditions(array(
            array(
                'field' => 'mt_title',         //可搜索字段title
                'equal' => 'LIKE',          //等价关系,可以是LIKE, =, <, >, <>
                'assoc' => 'AND',           //关系类型,可以是AND, OR
                'name'  => 'title',         //GET的值的访问键名
                'type'  => 'string',        //GET的值的类型
            ),
        		array(
        				'field' => 'mt_type',         //可搜索字段title
        				'equal' => 'LIKE',          //等价关系,可以是LIKE, =, <, >, <>
        				'assoc' => 'AND',           //关系类型,可以是AND, OR
        				'name'  => 'mt_type',         //GET的值的访问键名
        				'type'  => 'string',        //GET的值的类型
        		),
        ));
        $page   =   $this->_get_page(30);   //获取分页信息
        
        $messagetemplates=$this->_messagetemplate_mod->find(array(
        'fields'   => 'mt.*,ica.cate_name as icn',
        'conditions'  =>"1=1".$conditions,
        'limit'   => $page['limit'],
        'join'    => 'belongs_to_icategory',
        'order'   => 'mt.add_time DESC', //必须加别名
        'count'   => true   //允许统计
        ));    //找出所有的消息模版
        $page['item_count']=$this->_messagetemplate_mod->getCount();   //获取统计数据
        foreach ($messagetemplates as $key =>$messagetemplate){
        	if(empty($messagetemplate['alter_time'])){
        		$messagetemplates[$key]['alter_time']  = 0; //修改时间
        	}
        	if(!empty($cate_tmp)){
        		$arr=explode(',', json_decode($messagetemplate['parent_id']));
        		$flag=0;
        		foreach ($arr as $k=>$v){
        			if(in_array($v, $cate_tmp)){
        				$flag+=1;
        			}
        		}
        		if(!$flag){
        			unset($messagetemplates[$key]);
        		}
        	}
        	
        }
        $this->_format_page($page);
        $this->import_resource(array('script' => 'inline_edit_admin.js'));
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('parents', $this->_get_options()); //分类树
        $this->assign('page_info', $page);   //将分页信息传递给视图，用于形成分页条
        $this->assign('messagetemplates', $messagetemplates);
     
        $this->display('message_template/message.index.html');
    }
     /**
     *    新增消息
     *
     *    @author    Hyber
     *    @return    void
     */
    function add()
    {
        if (!IS_POST)
        {
            /* 显示新增表单 */
            $cate_id = isset ($_GET['parent_id']) ? intval($_GET['parent_id']) : 0;//方便在某个分类下新增消息模版
            $messagetemplate = array('parent_id' => $cate_id);

            /* 消息模版模型未分配的附件 */
            $files_BELONG_PROCESS = $this->_uploadedfile_mod->find(array(
                'conditions' => 'store_id = 0 AND belong = ' . BELONG_PROCESS . ' AND item_id = 0',
                'fields' => 'this.file_id, this.file_name, this.file_path',
                'order' => 'add_time DESC'
            ));

            $this->assign("id", 0);
            $this->assign('belong', BELONG_PROCESS);

            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
            $this->assign('messagetemplate', $messagetemplate);
            $this->assign('files_BELONG_PROCESS', $files_BELONG_PROCESS);
            $cate=array();
            foreach ($this->_get_options() as $k=>$v){          	
            	$cate[$k]=str_replace('&nbsp;','', $v);
            }
            $this->assign('parents', $cate); //分类树
            $template_name = $this->_get_template_name();
            $style_name    = $this->_get_style_name();
            $this->assign('build_editor', $this->_build_editor(array(
                'name' => 'mt_content',
                'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
            )));
            $this->assign('build_upload', $this->_build_upload(array('belong' => BELONG_PROCESS, 'item_id' => 0))); // 构建swfupload上传组件
            $this->display('message_template/message.form.html');
        }
        else
        {
        	if(!$this->_messagetemplate_mod->unique($_POST['mt_type']))
        	{
        		$this->show_warning('该使用标记已存在，请使用其他名称');
        		return ;
        	}
        	$app_urls=$this->_mt_app_url;
            $data = array();
            $data['mt_title']      =   !empty($_POST['mt_title'])?$_POST['mt_title']:0;
            $parent_id    =   !empty($_POST['parent_id'])?$_POST['parent_id']:0;
            $data['mt_content'] =   !empty($_POST['mt_content'])?htmlspecialchars($_POST['mt_content']):0;
            $data['mt_type'] =!empty($_POST['mt_type'])?$_POST['mt_type']:0;
            $data['mt_module']=!empty($_POST['mt_module'])?$_POST['mt_module']:'';
            $data['mt_code']=!empty($_POST['mt_code'])?$_POST['mt_code']:'';
            $data['send_type']  = !empty($_POST['send_type']) ? $_POST['send_type'] :  'all';//发送设备类型
            $mt_pc_url = !empty($_POST['mt_pc_url']) ? rawurlencode($_POST['mt_pc_url']) : '';
            $mt_app_url=!empty($_POST['mt_app_url'])?$app_urls{$_POST['mt_app_url']}['text']:'system';
            $data['is_special']=!empty($_POST['is_special'])?$_POST['is_special']:0;//是否是特殊消息
            $obj_type  = !empty($_POST['obj_type']) ? $_POST['obj_type'] :  0;//发送类型
            $obj_val    = !empty($_POST['obj_val']) ? $_POST['obj_val'] :  0;
            if (!$data['mt_title']){
            	$this->show_warning('标题不能为空');            	
            	return;
            }
            if (!$parent_id){
            	$this->show_warning('请选择模版所属分类');
            	return;
            }else{
            	$data['parent_id']=$parent_id;
            }
            if (!$data['mt_content']){
            	$this->show_warning('模版内容不能为空');
            	return;
            }
            if (!$data['mt_type']){
            	$this->show_warning('使用标记不能为空');
            	return;
            }
            if($data['parent_id']>1){
            	$arr=array(
            			'pc_url'=>$mt_pc_url,
            			'app_url'=>$mt_app_url
            	);
            	$data['mt_url']=json_encode($arr);
            }else{
            	$data['mt_url']='';
            }
            $conditions = '1=1';
            if ($obj_type  == 0) {//指定会员
            	if (!$obj_val ) {
            		$this->show_warning('缺少必填参数');
            		return;
            	}
            	$zhiding_val = implode(',', $obj_val);
            
            	$conditions .= " AND user_id IN ($zhiding_val)";
            
            } elseif ($obj_type > 0) {//=====  指定会员类型  =====
            	if ($obj_type == 100) {
            		$conditions .= " AND  member_lv_id >= 2 ";
            	} elseif (in_array($obj_type, array(1,2,3,4,5))) {
            		$conditions .= " AND member_lv_id = $obj_type ";
            	}
            
            }
            $data['mt_send_member']=addslashes($conditions);
            $data['add_time']   =   gmtime();
            $id=$mt_id = $this->_messagetemplate_mod->add($data);
            if (!$id)  //获取mt_id
            {
                $this->show_warning($this->_messagetemplate_mod->get_error());

                return;
            }

            /* 附件入库 */
            if (isset($_POST['file_id']))
            {
                foreach ($_POST['file_id'] as $file_id)
                {
                    $this->_uploadedfile_mod->edit($file_id, array('item_id' => $mt_id));
                }
            }
            $this->show_message('add_messagetemplate_successed',
                'back_list',    'index.php?app=messagetemplate',
                'continue_add', 'index.php?app=messagetemplate&amp;act=add'
            );
        }
    }
     /**
     *    编辑消息模版
     *
     *    @author    Hyber
     *    @return    void
     */
    function edit()
    {
        $mt_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$mt_id)
        {
            $this->show_warning('no_such_messagetemplate');
            return;
        }
         if (!IS_POST)
        {
            /* 当前消息模版的附件 */
            $files_BELONG_PROCESS = $this->_uploadedfile_mod->find(array(
                'conditions' => 'store_id = 0 AND belong = ' . BELONG_PROCESS . ' AND item_id=' . $mt_id,
                'fields' => 'this.file_id, this.file_name, this.file_path',
                'order' => 'add_time DESC'
            ));

            $find_data     = $this->_messagetemplate_mod->get($mt_id);
            if (empty($find_data))
            {
                $this->show_warning('no_such_messagetemplate');

                return;
            }
             $find_data['mt_content']=htmlspecialchars_decode($find_data['mt_content']);
             $find_data['mt_send_member']=stripslashes($find_data['mt_send_member']);
             /* 消息跳转地址 pc app */

             if(!empty($find_data['mt_url']) && $find_data['parent_id']>1){
             	
             	$mt_url=(array)json_decode($find_data['mt_url']);
             	$url=$mt_url['pc_url'];
             	
             	$find_data['mt_pc_url']=rawurldecode($url);
             	$find_data['mt_app_url']=$mt_url['app_url'];
             	$app_urls=$this->_mt_app_url;
             	
             	foreach ($app_urls as $key=>$value){
             		if($value['text']==$mt_url['app_url']){
             			
             			$find_data['mt_app_url']=$key;
             			break;
             		}
             	}
             }
             /*  判断消息发送会员范围 */
             $obj_type='';
             $obj_val='';
             if(strpos($find_data['mt_send_member'],'>=')){
             	$obj_type=100;
             }else if(strpos($find_data['mt_send_member'],'member_lv_id =')){
             	$obj_type=trim(substr($find_data['mt_send_member'], strpos($find_data['mt_send_member'],'_id =')+5));
             }else if(strpos($find_data['mt_send_member'],'IN (')){
             	$obj_val=substr(rtrim($find_data['mt_send_member'],')'), strpos($find_data['mt_send_member'],'IN (')+4);
             }else if(preg_match('/^1=1$/', $find_data['mt_send_member'])){
             	$obj_type=-1;
             }
             if($obj_val){
             	$member=$this->_member_mod->find(array(
             			'fields'=>'*',
             			'conditions'=>"user_id in($obj_val)"
             	));
             	$obj_val=array();
             	foreach ($member as $k=>$v){
             		if(preg_match('/^1[3|4|5|7|8][0-9]\d{4,8}$/', $v['user_name'])){
             			$obj_val[]=array(
             					'phone'=>$v['user_name'],
             					'user_id'=>$v['user_id'],
             			);
             		}else if(preg_match('/^1[3|4|5|7|8][0-9]\d{4,8}$/', $v['phone_mob'])){
             			$obj_val[]=array(
             					'phone'=>$v['phone_mob'],
             					'user_id'=>$v['user_id'],
             			);
             		}else {
             			$obj_val[]=array(
             					'phone'=>'用户'.$v['user_id'].'手机号没查到',
             					'user_id'=>$v['user_id'],
             			);
             		}            	
             	}
             }
            
            $find_data['obj_type']=$obj_type;
            $find_data['obj_val']=$obj_val;
            //$find_data['mt_content']=stripslashes($find_data['mt_content']);
            //$messagetemplate    =   $find_data["{$key}"];
            $this->assign("id", $mt_id);
            $this->assign("belong", BELONG_PROCESS);
            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
            $cate=array();
            foreach ($this->_get_options() as $k=>$v){
            	$cate[$k]=str_replace('&nbsp;','', $v);
            }
            $this->assign('parents', $cate); //分类树
            $this->assign('files_BELONG_PROCESS', $files_BELONG_PROCESS);
            $this->assign('messagetemplate', $find_data);

            $template_name = $this->_get_template_name();
            $style_name    = $this->_get_style_name();
            $this->assign('build_editor', $this->_build_editor(array(
                'name' => 'mt_content',
                'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
            )));
            
            $this->assign('build_upload', $this->_build_upload(array('belong' => BELONG_PROCESS, 'item_id' => $mt_id))); // 构建swfupload上传组件
            $this->display('message_template/message.form.html');
        }
        else
        {
//         	var_dump($_POST);exit();
        	if(!$this->_messagetemplate_mod->unique($_POST['mt_type'], $mt_id))
        	{
        		$this->show_warning('该使用标记已存在，请使用其他名称');
        		return ;
        	}
        	$app_urls=$this->_mt_app_url;
            $data = array();
            $data['mt_title']      =   !empty($_POST['mt_title'])?$_POST['mt_title']:0;
            $parent_id    =   !empty($_POST['parent_id'])?$_POST['parent_id']:0;
            $data['mt_content'] =   !empty($_POST['mt_content'])?htmlspecialchars($_POST['mt_content']):0;
            $data['mt_type'] =!empty($_POST['mt_type'])?$_POST['mt_type']:0;
			$data['is_special']=!empty($_POST['is_special'])?$_POST['is_special']:0;
           $data['mt_module']=!empty($_POST['mt_module'])?$_POST['mt_module']:'';
            $data['mt_code']=!empty($_POST['mt_code'])?$_POST['mt_code']:'';
            $data['send_type']  = !empty($_POST['send_type']) ? $_POST['send_type'] :  'all';//发送设备类型
            $mt_pc_url = !empty($_POST['mt_pc_url']) ? rawurlencode($_POST['mt_pc_url']) : '';
            $mt_app_url=!empty($_POST['mt_app_url'])?$app_urls{$_POST['mt_app_url']}['text']:'system';
            $obj_type  = !empty($_POST['obj_type']) ? $_POST['obj_type'] :  0;//发送类型
            $obj_val    = !empty($_POST['obj_val']) ? $_POST['obj_val'] :  0;
            if (!$data['mt_title']){
            	$this->show_warning('标题不能为空');
            	return;
            }
            if (!$parent_id){
            	$this->show_warning('请选择模版所属分类');
            	return;
            }else{
            	$data['parent_id']=$parent_id;
            }
            if (!$data['mt_content']){
            	$this->show_warning('模版内容不能为空');
            	return;
            }
            if (!$data['mt_type']){
            	$this->show_warning('使用标记不能为空');
            	return;
            }
            if($data['parent_id']>1){
            	$arr=array(
            			'pc_url'=>$mt_pc_url,
            			'app_url'=>$mt_app_url
            	);
            	$data['mt_url']=json_encode($arr);
            }else{
            	$data['mt_url']='';
            }
            $conditions = '1=1';
            if ($obj_type  == 0) {//指定会员
            	if (!$obj_val ) {
            		$this->show_warning('缺少必填参数');
            		return;
            	}
            	$zhiding_val = implode(',', $obj_val);
            
            	$conditions .= " AND user_id IN ($zhiding_val)";
            
            } elseif ($obj_type > 0) {//=====  指定会员类型  =====
            	if ($obj_type == 100) {
            		$conditions .= " AND  member_lv_id >= 2 ";
            	} elseif (in_array($obj_type, array(1,2,3,4,5))) {
            		$conditions .= " AND member_lv_id = $obj_type ";
            	}
            
            }
            $data['mt_send_member']=addslashes($conditions);
            $data['alter_time'] =gmtime();
            //var_dump($data);exit();
            $rows=$this->_messagetemplate_mod->edit($mt_id, $data);
            if ($this->_messagetemplate_mod->has_error())
            {
                $this->show_warning($this->_messagetemplate_mod->get_error());

                return;
            }

            $this->show_message('edit_messagetemplate_successed',
                'back_list',        'index.php?app=messagetemplate',
                'edit_again',    'index.php?app=messagetemplate&amp;act=edit&amp;id=' . $mt_id);
        }
    }

    //异步修改数据
   function ajax_col()
   {
       $id     = empty($_GET['id']) ? 0 : intval($_GET['id']);
       $column = empty($_GET['column']) ? '' : trim($_GET['column']);
       $value  = isset($_GET['value']) ? trim($_GET['value']) : '';
       $data   = array();

       if (in_array($column ,array('mt_type', 'sort_order')))
       {
           $data[$column] = $value;
           if($column == 'mt_type')
           {           
           	if(!$this->_messagetemplate_mod->unique($value, $id))
           	{
           		echo ecm_json_encode(false);
           		return ;
           	}
           }
           $this->_messagetemplate_mod->edit($id, $data);
           if(!$this->_messagetemplate_mod->has_error())
           {
               echo ecm_json_encode(true);
           }
       }
       else
       {
           return ;
       }
       return ;
   }
    function drop()
    {
        $mt_ids = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$mt_ids)
        {
            $this->show_warning('no_such_messagetemplate');

            return;
        }
        $mt_ids=explode(',', $mt_ids);
        $message = 'drop_message_ok';
/*         foreach ($mt_ids as $key=>$mt_id){
            $messagetemplate=$this->_messagetemplate_mod->find(intval($mt_id));
            $messagetemplate=current($messagetemplate);
            if($messagetemplate['code']!=null)
            {
                unset($mt_ids[$key]);  //有部分是系统消息模版 过滤掉
                $message = 'drop_ok_system_messagetemplate';
            }
            else
            {

            }
        } */
        if (!$mt_ids)
        {
            $message = 'system_messagetemplate'; //全是系统消息模版
            $this->show_warning($message);

            return;
        }
        if (!$this->_messagetemplate_mod->drop($mt_ids))    //删除
        {
            $this->show_warning($this->_messagetemplate_mod->get_error());

            return;
        }

        $this->show_message($message);
    }

    /* 更新排序 */
    function update_order()
    {
        if (empty($_GET['id']))
        {
            $this->show_warning('Hacking Attempt');
            return;
        }

        $ids = explode(',', $_GET['id']);
        $sort_orders = explode(',', $_GET['sort_order']);
        foreach ($ids as $key => $id)
        {
            $this->_messagetemplate_mod->edit($id, array('sort_order' => $sort_orders[$key]));
        }

        $this->show_message('update_order_ok');
    }

        /* 构造并返回树 */
    function &_tree($acategories)
    {
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($acategories, 'cate_id', 'parent_id', 'cate_name');
        return $tree;
    }
        /* 取得可以作为上级的消息模版分类数据 */
    function _get_options()
    {
        $mod_icategory = &m('icategory');
        $icategorys = $mod_icategory->get_list();
        $tree =& $this->_tree($icategorys);
        return $tree->getOptions();
    }

    /* 异步删除附件 */
    function drop_uploadedfile()
    {
        $file_id = isset($_GET['file_id']) ? intval($_GET['file_id']) : 0;
        if ($file_id && $this->_uploadedfile_mod->drop($file_id))
        {
            $this->json_result('drop_ok');
            return;
        }
        else
        {
            $this->json_error('drop_error');
            return;
        }
    }
    
/* 	function sendmessage(){
		$r=send_message('liangti_success',array(641),array('time'=>'2015年08月29日下午','address'=>'即墨市定制大街99号麦富迪001体验店','name'=>'马小玲','phone'=>'18677728336'));
		return $this->json_result($r,$r);
	} */
}

?>