<?php
use Cyteam\Config\Config;
/**
 * 轮播图管理
 *
 *
 * @access protected
 * @author cyrus <2621270755@qq.com>
 * @date 2016
 * @return void
 */
class ShufflingApp extends BackendApp
{
    var $_recommend_mod;
    var $sites;
	var $groups=array();
	var $_shuffling_mod;
	var $_shuffling_group_mod;
	var $url_rule_values;
	var $url_rule_names;
	var $_cate_mod;
    function __construct()
    {
        $this->Shuffling();
    }

    function Shuffling()
    {
        parent::BackendApp();
		$this->_shuffling_mod = & m('shuffling');
        $this->_shuffling_group_mod = & m('shufflinggroup');
		$this->groups = $this->_shuffling_group_mod->find(array(
            'conditions'=> '1=1',
                'fields' => '*',
                'order' => 'id ASC'
            )
        );
		$config=new Config();
		$setting = $config->get_config();
		$this->sites=$setting['sites'];
		$this->url_rule_values=i_array_column($setting['url_rule'], 'value');
		$this->url_rule_names=i_array_column($setting['url_rule'],'name');
    }

	/**
	 * 轮播图列表
	 *
	 *
	 * @access protected
	 * @author cyrus  <2621270755@qq.com>
	 * @return void
	 */
    function index()
    {
		//print_r($this->_customs_mod);exit;
        $conditions = '';
		$site_id=isset($_GET['site'])?$_GET['site']:0;
        $page = $this->_get_page(30);
        if($site_id){
        	$conditions="and site_id={$site_id}";
        }
        $items = $this->_shuffling_mod->find(array(
            'conditions' => '1=1 '.$conditions ,
            'limit' => $page['limit'],
            'order' => "groups ASC,sort_order ASC",
            'count' => true,
        ));
        $max_sort_order=$this->_shuffling_mod->get(array(
        		'fields'=>"min(sort_order) as s"
        ));
//print_exit($items);
		$groups=i_array_column($this->groups,'name','id');
        foreach($items as $key=>$val){
            $group = $val['groups'];
            $site_id=$val['site_id'];
            $items[$key]['site']=$this->sites[$site_id];
            $items[$key]['groups'] = $groups[$group];
            unset($items[$key]['site_id']);
            if($val['sort_order']==$max_sort_order['s']){
            	$items[$key]['top']=1;
            }
        }
        $this->assign('view_type', $this->_view_type);
		$this->assign('items', $items);
        $page['item_count'] = $this->_shuffling_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('sites',$this->sites);
       // $this->assign("cats", $this->disCats());
        
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display('style_recommend.index.html');
    }
	//END function
	
	/**
	 * 添加轮播图
	 *
	 *
	 * @access protected
	 * @author cyrus <2621270755@qq.com>
	 * @return void
	 */
    function add()
    {
    	if (!IS_POST) {
    		$this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
				
	    	$template_name = $this->_get_template_name();
	    	$style_name    = $this->_get_style_name();
            $this->assign("sites", $this->sites);
            $this->assign('url_rule_values',$this->url_rule_values);//url规则值
            $this->assign('url_rule_names',$this->url_rule_names);//url规则名
            $this->assign('status',1);
			$this->assign('view_type', $this->_view_type);
	    	$this->assign('build_editor', $this->_build_editor(array(
	    			'name' => 'content',
	    			'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
	    	)));
	    	$this->display('style_recommend.info.html');
    	} else {
			$data=array();
			$name=!empty($_POST['name'])?trim($_POST['name']):'';
			$title=!empty($_POST['title'])?trim($_POST['title']):'';
			$site_id=!empty($_POST['site_id'])?$_POST['site_id']:'';
			$group=!empty($_POST['group'])?$_POST['group']:'';
			$link_url=!empty($_POST['link_url'])?$_POST['link_url']:'';
			$is_blank=$_POST['is_blank'];
			$img=!empty($_POST['img'])?$_POST['img']:'';
			$sort_order=!empty($_POST['sort_order'])?$_POST['sort_order']:0;
			$status=isset($_POST['status'])?$_POST['status']:1;
			$data = array(
				'name'        => $name,
				'title' => $title,
				'site_id'=>$site_id,
				'groups'      => $group,
                'link_url'    => $link_url,
				'is_blank'	=>$is_blank,
				'img'         => addslashes($img),
				'sort_order'  => $sort_order,
				'status'      => $status,
				'add_time'    => gmtime(),
			);
			$id = $this->_shuffling_mod->add($data);
	
			if($id) {
				$this->show_message('轮播图添加成功!', 'back_list', 'index.php?app=shuffling');
			} else {
				$this->show_message('轮播图添加失败!', 'back_list', 'index.php?app=shuffling');
			}
    	}
    }
    //END function

	/**
	*编辑轮播图
	*
	*@author cyrus <2621270755@qq.com>
	*@date 2016年5月13日
	*@return 
	*/
    function edit()
    {
    	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    	
    	$data     = $this->_shuffling_mod->find($id);
    	if (empty($data)){
    		$this->show_warning('轮播图不存在！');
    		return;
    	}

    	if (!IS_POST) {
    		$this->import_resource(array('script' => 'inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
    				'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
    		$data['img']=stripslashes($data['img']);
	    	$data    =   current($data);
//            print_exit($data);

	    	$template_name = $this->_get_template_name();
	    	$style_name    = $this->_get_style_name();
	    	$this->assign('sites',$this->sites);
	    	$this->assign('url_rule_values',$this->url_rule_values);//url规则值
	    	$this->assign('url_rule_names',$this->url_rule_names);//url规则名
	    	$groups=$this->groups;
	    	foreach ($groups as $k=>$v){
	    		if($v['site_id']!=$data['site_id']){
	    			unset($groups[$k]);
	    		}
	    	}
	    	$groups=i_array_column($groups,'name','id');
            $this->assign("group",$groups);
	    	$this->assign('build_editor', $this->_build_editor(array(
	    			'name' => 'content',
	    			'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
	    	)));
			
	    	//链接地址是否存在分隔符
	    	$type=0;
	    	$data['cat_id']=0;
	    	if(strpos( $data['link_url'],'/')){
	    		
	    		$link_url=explode('/', $data['link_url']);
	    		$data['cat_id']=$link_url[1];
	    		foreach ($this->url_rule_values as $k=>$v){
	    			if(strstr($v,$link_url[0]) && strpos( $v,'/')){
	    				//根据静态链接获取固定url规则
	    				$data['match_url']=$v;
	    				$type=$k;
	    			}
	    		}
	    		switch ($type){
	    			case 1:
	    				$options=$this->_get_options('',3);
	    				break;
	    			default:
	    				$options='';
	    				break;
	    		}
	    		if($options){
	    			//获取链接ID对应的数据值列表
	    			$this->assign('options',$options);
	    		}
	    	}else{
	    		$data['match_url']=$data['link_url'];
	    	}
	    	$this->assign('data',$data);
    		$this->display('style_recommend.info.html');
    	} else {
    		$name=!empty($_POST['name'])?trim($_POST['name']):'';
    		$title=!empty($_POST['title'])?trim($_POST['title']):'';
    		$site_id=!empty($_POST['site_id'])?$_POST['site_id']:'';
    		$group=!empty($_POST['group'])?$_POST['group']:1;
    		$link_url=!empty($_POST['link_url'])?$_POST['link_url']:'';
    		$is_blank=$_POST['is_blank'];
    		$img=!empty($_POST['img'])?$_POST['img']:'';
    		$sort_order=!empty($_POST['sort_order'])?$_POST['sort_order']:0;
    		$status=isset($_POST['status'])?$_POST['status']:1;
    		$data = array(
    				'name'        => $name,
    				'title' => $title,
    				'site_id'=>$site_id,
    				'groups'      => $group,
    				'link_url'    => $link_url,
    				'is_blank'	=>$is_blank,
    				'img'         => addslashes($img),
    				'sort_order'  => $sort_order,
    				'status'      => $status,
    				'edit_time'    => gmtime(),
    		);

			
    		$res = $this->_shuffling_mod->edit($id, $data);
			$this->show_message('轮播图编辑成功!', 'back_list', 'index.php?app=shuffling');
    	}
    }
    //END function

    /**
    *删除轮播图
    *
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月13日
    *@return 
    */
    function drop()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('no_recommend_to_drop');
            return;
        }

        $ids = explode(',', $id);
        if (!$this->_shuffling_mod->drop($ids))
        {
            $this->show_warning($this->_shuffling_mod->get_error());
            return;
        }

        $this->show_message('轮播图删除成功');
    }
    //END function
    
    /**
     * 轮播图置顶
     *
     *
     * @access protected
     * @author cyrus <2621270755@qq.com>
     * @return void
     */
    function local_top() {
    	$lurl = $_SERVER ['HTTP_REFERER'];
    	$id = ! empty ( $_GET ['id'] ) ? $_GET ['id'] : 0;
    	if (! $id) {
    		$this->show_warning ( '参数错误' );
    		return;
    	}
    	$res = $this->_shuffling_mod->stick($id);
    	if (! $res ) {
    		$this->show_warning ( '置顶失败' );
    		return;
    	} else {
    		header ( "Location: {$lurl}" );
    		// window.location.href="index.html?app=fabri"
    	}
    }
    //END function

   /**
   *轮播图分组列表
   *
   *@author cyrus <2621270755@qq.com>
   *@date 2016年5月13日
   *@return 
   */
    function group_list(){

        $page = $this->_get_page(30);
        $groups = $this->_shuffling_group_mod->find(array(
            'conditions' => '1=1' ,
            'limit' => $page['limit'],
            'order' => "id ASC",
            'count' => true,
        ));
        $this->assign('sites',$this->sites);
        foreach ($groups as $key=>$val){
        	$temp_id=$val['site_id'];
        	$groups[$key]['site']=$this->sites[$temp_id];
        }
        $this->assign('groups', $groups);
        $page['item_count'] = $this->_shuffling_group_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display('shuffling_group.index.html');
    }
    //END function

    /**
    *添加轮播图分组
    *
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月13日
    *@return 
    */
    function add_group(){
        if(!$_POST){
        	$this->assign('sites',$this->sites);
            $this->display('shuffling_group.info.html');
        }else{
            $name  = $_POST['name'];
            $site_id=$_POST['site_id'];
            $code=!empty($_POST['code'])?$_POST['code']:'';
            $data  = array(
            	'site_id'=>$site_id,
                'name' => $name,
            	'code'=>$code,
                'add_time'=> gmtime(),
            );
            $id = $this->_shuffling_group_mod->add($data);
            if($id) {
                $this->show_message('分组添加成功!', 'back_list', 'index.php?app=shuffling&act=group_list');
            } else {
                $this->show_message('分组添加失败!', 'back_list', 'index.php?app=shuffling&act=group_list');
            }
        }

    }
    //END function
    
    /**
    *编辑轮播图分组
    *
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月13日
    *@return 
    */
    function edit_group()
    {
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

        $data     = $this->_shuffling_group_mod->find($id);
        if (empty($data)){
            $this->show_warning('轮播图分组不存在！');
            return;
        }

        if (!IS_POST) {
            $this->import_resource(array('script' => 'inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
            $data    =   current($data);
            $this->assign('data',$data);
            $this->assign('sites',$this->sites);
            $this->display('shuffling_group.info.html');
        } else {
        	$code=!empty($_POST['code'])?$_POST['code']:'';
            $data = array(
                'name'        => $_POST['name'],
//             	'site_id'=>$_POST['site_id'],
            	'code'=>$code,
            	'add_time'    => gmtime(),
            );

            $res = $this->_shuffling_group_mod->edit($id, $data);
            $this->show_message('分组编辑成功!', 'back_list','index.php?app=shuffling&act=group_list');
        }
    }
    //END function

    /**
    *删除轮播图分组
    *
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月13日
    *@return 
    */
    function drop_group()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('no_recommend_to_drop');
            return;
        }

        $ids = explode(',', $id);
        if (!$this->_shuffling_group_mod->drop($ids))
        {
            $this->show_warning($this->_shuffling_group_mod->get_error());
            return;
        }

        $this->show_message('分组删除成功');
    }
    //END function

    /**
    *ajax根据站点获取分组
    *
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月13日
    *@param  $_POST['pid']
    *@return 
    */
    function ajax_get_groups() {
    	$pid = $_POST ['pid'];
    	if (! $pid && !isset($pid)) {
    		return $this->json_error ( '失败' );
    	}
    
    	$list = $this->_shuffling_group_mod->get_options_html ( $pid, 0 );
    	$this->json_result ( $list );
    	return;
    }
    //END function

    /**
     *位置标记为唯一标识不可重复（前台链接使用）
     *@version 1.0
     *@author cyrus <2621270755@qq.com>
     *@date 2016年5月19日
     *@return
     */
    function ajax_code_unique()
    {
    	$id=empty($_POST['id'])?'':trim($_POST['id']);
    	$code=empty($_POST['code'])?'':trim($_POST['code']);
    	if($code){
    		$group=$this->_shuffling_group_mod->find(
    				array("conditions"=>"code='$code'")
    				);
    		if($id){
    			if($group && !$group[$id]){
    				echo ecm_json_encode(false);
    				return ;
    			}else{
    				echo ecm_json_encode(true);
    				return ;
    			}
    		}else{
    			if($group){
    				echo ecm_json_encode(false);
    				return ;
    			}else{
    				echo ecm_json_encode(true);
    				return ;
    			}
    		}
    	}else{
    		return ;
    	}
    	return ;
    }
    //END function
    
    /**
    *-----------------------------------------------------------
    *获取链接url的数据列表及ID
    *-----------------------------------------------------------
    *@access public
    *@author cyrus <2621270755@qq.com>
    *@date 2016年8月6日
    *@version 1.0
    *@return 
    */
    function ajaxGetId(){
    	$type=empty($_REQUEST['type'])?$_REQUEST['type']:1;
    	if($type==1){
    		$category=$this->_get_options('',3);
    		
    		$con='<select name="cat_id" id="cat_id"  onchange="setCat(this)" >
                                <option value="0">请选择</option> ';
    		if($category){
    			foreach ($category as $k=>$v){
    				$con.='<option value="'.$k.'"  >'.$v.'</option>';
    			}
    		}
    		$con.='</select>';
//     		var_dump($con);
    		return $this->json_result($con,'成功获取商品分类id');
    	}
    	
    }
    
    /* 取得可以作为上级的商品分类数据 */
    function _get_options($except = NULL,$layer=MAX_LAYER)
    {
    	$_cate_mod = & bm('gcategory', array('_store_id' => 0));
    
    	$gcategories = $_cate_mod->get_list();
    	$tree =& $this->_tree($gcategories);
    
    	return $tree->getOptions($layer - 1, 0, $except);
    }
    
    /* 构造并返回树 */
    function &_tree($gcategories)
    {
    	import('tree.lib');
    	$tree = new Tree();
    	$tree->setTree($gcategories, 'cate_id', 'parent_id', 'cate_name');
    	return $tree;
    }

}

?>