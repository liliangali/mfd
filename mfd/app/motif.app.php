<?php
use Cyteam\Config\Config;
/**
 * 栏目管理
 *
 *
 * @access protected
 * @author cyrus <2621270755@qq.com>
 * @date 2016.5.17
 * @return void
 */
class MotifApp extends BackendApp
{
    var $_recommend_mod;
    var $sites;
	var $locations=array();
	var $_motif_mod;
	var $_motif_group_mod;
	var $_motif_rel_content_mod;
	var $url_rule_values;
	var $url_rule_names;
    function __construct()
    {
        $this->Motif();
    }

    function Motif()
    {
        parent::BackendApp();
		$this->_motif_mod = & m('motif');
        $this->_motif_group_mod = & m('motifgroup');
        $this->_motif_rel_content_mod=&m('motifrelcontent');
		$this->locations = $this->_motif_group_mod->find(array(
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
	 * 栏目列表
	 *
	 *
	 * @access protected
	 * @author cyrus  <2621270755@qq.com>
	 * @return void
	 */
    public function index()
    {
		//print_r($this->_customs_mod);exit;
        $conditions = '';
		$siteid=isset($_GET['site'])?$_GET['site']:0;
		$location_id=isset($_GET['location'])?$_GET['location']:0;
        $page = $this->_get_page(30);
        if(!empty($siteid)){
        	$conditions="and site_id={$siteid} ";
        }
        if(!empty($location_id)){
        	$conditions.="and location_id={$location_id}";
        }
        $items = $this->_motif_mod->find(array(
            'conditions' => '1=1 and is_delete=0 '.$conditions ,
            'limit' => $page['limit'],
            'order' => "location_id ASC,sort_order ASC",
            'count' => true,
        ));
        $max_sort_order=$this->_motif_mod->get(array(
        		'fields'=>"max(sort_order) as s",
        		'conditions'=>'is_delete=0'
        ));
//print_exit($items);
		$locations=i_array_column($this->locations,'name','id');
        foreach($items as $key=>$val){
            $location = $val['location_id'];
            $site_id=$val['site_id'];
            $items[$key]['site']=$this->sites[$site_id];
            $items[$key]['location'] = $locations[$location];
            unset($items[$key]['site_id']);
            if($val['sort_order']==$max_sort_order['s']){
            	$items[$key]['top']=1;
            }
        }
        $this->assign('view_type', $this->_view_type);
		$this->assign('items', $items);
        $page['item_count'] = $this->_motif_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        //发布平台
        $this->assign('sites',$this->sites);
        //发布位置
        if($siteid){
        	$locations=array();
        	foreach ($this->locations as $k=>$v){
        		if($v['site_id']==$siteid){
        			$locations[$k]=$v['name'];
        		}
        	}
        }
        $this->assign('locations',$locations);
		$this->assign('filtered',$conditions?1:0);        
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display('motif/motif.index.html');
    }
	//END function
	
	/**
	 * 添加栏目
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
	    	$switches=array(1=>'启用',0=>'禁用');
	    	$shows=array(1=>'是',0=>'否');
	    	$this->assign('switches',$switches);
	    	$this->assign('shows',$shows);
            $this->assign("sites", $this->sites);
            $this->assign('url_rule_values',$this->url_rule_values);
            $this->assign('url_rule_names',$this->url_rule_names);
			$this->assign('view_type', $this->_view_type);
	    	$this->display('motif/motif.info.html');
    	} else {
    		//栏目关联内容
    		$motif_rc=array();
    		$i=1;
    		$key='';
    		foreach ($_POST as $k=>$v){
    			if(is_numeric(substr($k,-1))){
    				$i=substr($k,-1);
    				$key=substr($k,0,-1);
    				if($key){
    					$motif_rc[$i][$key]=$v;
    				}
    				$i++;
    			}
    		}
    		//栏目关联内容 END
			$data=array();
			$title=!empty($_POST['title'])?trim($_POST['title']):'';
			$tswitch=isset($_POST['tswitch'])?intval($_POST['tswitch']):1;
			$sswitch=isset($_POST['sswitch'])?intval($_POST['sswitch']):1;
			$subhead=!empty($_POST['subhead'])?trim($_POST['subhead']):'';
			$subhead_link=!empty($_POST['subhead_link'])?trim($_POST['subhead_link']):'';
			$site_id=!empty($_POST['site_id'])?intval($_POST['site_id']):'';
			$location_id=!empty($_POST['location_id'])?intval($_POST['location_id']):'';
			$sort_order=!empty($_POST['sort_order'])?intval($_POST['sort_order']):0;
			$is_show=isset($_POST['is_show'])?intval($_POST['is_show']):1;
			$data = array(
				'title'  => $title,
				'title_switch' => $tswitch,
				'subhead'=>$subhead,
				'subhead_switch'=>$sswitch,
				'subhead_link'=>$subhead_link,
				'site_id'=>$site_id,
				'location_id'=>$location_id,
				'sort_order'  => $sort_order,
				'is_show'      => $is_show,
				'add_time'    => gmtime(),
			);
			$id = $this->_motif_mod->add($data);
			
			if($id) {
				//将栏目关联内容添加到数据库中
				foreach ($motif_rc as $k=>$v){
					$v['parent_id']=$id;
					$this->_motif_rel_content_mod->add($v);
				}
				$this->show_message('栏目添加成功!', 'back_list', 'index.php?app=motif');
			} else {
				$this->show_message('栏目添加失败!', 'back_list', 'index.php?app=motif');
			}
    	}
    }
    //END function

	/**
	*编辑栏目
	*
	*@author cyrus <2621270755@qq.com>
	*@date 2016年5月13日
	*@return 
	*/
     function edit()
    {
    	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    	
    	$data     = $this->_motif_mod->find($id);
    	if (empty($data)){
    		$this->show_warning('栏目不存在！');
    		return;
    	}

    	if (!IS_POST) {
    		$this->import_resource(array('script' => 'inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
    				'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
    		$data['img']=stripslashes($data['img']);
	    	$data    =   current($data);
//            print_exit($data);
			//栏目关联内容
			$motif_rc=$this->_motif_rel_content_mod->find(array(
					'conditions'=>"parent_id={$id}",
					'fields'=>'*',
					'order'=>'sort_order,id'
			));
			$motif_rc=array_values($motif_rc);
			foreach ($motif_rc as $k=>$v){
				$motif_rc[$k]['imgname']="img{$k}";
			}
			
			$this->assign('motif_rc',$motif_rc);
	    	$template_name = $this->_get_template_name();
	    	$style_name    = $this->_get_style_name();
	    	//默认选项
	    	$switches=array(1=>'启用',0=>'禁用');
	    	$shows=array(1=>'是',0=>'否');
	    	$this->assign('switches',$switches);
	    	$this->assign('shows',$shows);
	    	$this->assign('sites',$this->sites);
	    	$this->assign('url_rule_values',$this->url_rule_values);
            $this->assign('url_rule_names',$this->url_rule_names);
	    	//根据站点获取可选位置
	    	$locations=$this->locations;
	    	foreach ($locations as $k=>$v){
	    		if($v['site_id']!=$data['site_id']){
	    			unset($locations[$k]);
	    		}
	    	}
	    	$locations=i_array_column($locations,'name','id');
            $this->assign("locations",$locations);

	    	$this->assign('data',$data);
    		$this->display('motif/motif.info.html');
    	} else {
    		//栏目关联内容
    		$motif_rc=array();
    		$motif_rc_edit=array();
    		$motif_rc_add=array();
    		$cid='';
    		$i=1;
    		$key='';
    		//剩余关联内容id
    		$surplus_ids=!empty($_POST['did'])?$_POST['did']:'';
    		foreach ($_POST as $k=>$v){
    			if(is_numeric(substr($k,-1))){
    				$i=substr($k,-1);
    				$key=substr($k,0,-1);
    				if($key){
    					$motif_rc[$i][$key]=$v;
    				}
    				$i++;
    			}
    		}
    		foreach ($motif_rc as $k=>$v){
    			if($v['id']){
    				$cid=$v['id'];
    				unset($v['id']);
    				$motif_rc_edit[$cid]=$v;
    			}else{
    				$motif_rc_add[]=$v;
    			}
    		}
    		//栏目关联内容 END
			$data=array();
			$title=!empty($_POST['title'])?trim($_POST['title']):'';
			$tswitch=isset($_POST['tswitch'])?intval($_POST['tswitch']):1;
			$sswitch=isset($_POST['sswitch'])?intval($_POST['sswitch']):1;
			$subhead=!empty($_POST['subhead'])?trim($_POST['subhead']):'';
			$subhead_link=!empty($_POST['subhead_link'])?trim($_POST['subhead_link']):'';
			$site_id=!empty($_POST['site_id'])?intval($_POST['site_id']):'';
			$location_id=!empty($_POST['location_id'])?intval($_POST['location_id']):'';
			$sort_order=!empty($_POST['sort_order'])?intval($_POST['sort_order']):0;
			$is_show=isset($_POST['is_show'])?intval($_POST['is_show']):1;
			$data = array(
				'title'  => $title,
				'title_switch' => $tswitch,
				'subhead'=>$subhead,
				'subhead_switch'=>$sswitch,
				'subhead_link'=>$subhead_link,
				'site_id'=>$site_id,
				'location_id'=>$location_id,
				'sort_order'  => $sort_order,
				'is_show'      => $is_show,
				'edit_time'    => gmtime(),
			);

			
    		$res = $this->_motif_mod->edit($id, $data);
    		//栏目添加新增关联内容
    		foreach ($motif_rc_add as $k=>$v){
    			$v['parent_id']=$id;
    			$this->_motif_rel_content_mod->add($v);
    		}
    		//栏目编辑关联内容
    		foreach ($motif_rc_edit as $k=>$v){
    			$this->_motif_rel_content_mod->edit($k,$v);
    		}
    		//栏目删除关联内容
    		if($surplus_ids){
    			if(substr($surplus_ids, 0,1)==','){
    				$surplus_ids=substr($surplus_ids, 1);
    			}
    			$del_ids=explode(',', $surplus_ids);
    			$this->_motif_rel_content_mod->drop($del_ids);
    		}
			$this->show_message('栏目编辑成功!', 'back_list', 'index.php?app=motif');
    	}
    }
    //END function

    /**
    *删除栏目(非物理删除)
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

        if (!$this->_motif_mod->edit($id,array('is_delete'=>1)))
        {
            $this->show_warning($this->_motif_mod->get_error());
            return;
        }

        $this->show_message('栏目删除成功');
    }
    //END function
    
    /**
     * 栏目置顶
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
    	$res = $this->_motif_mod->stick($id);
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
   *栏目位置列表
   *
   *@author cyrus <2621270755@qq.com>
   *@date 2016年5月13日
   *@return 
   */
    function group_list(){
    	$conditions='';
    	$conditions .= $this->_get_query_conditions(array(
    			array(
    					'field' => 'site_id',         //可搜索字段title
    					'equal' => '=',          //等价关系,可以是LIKE, =, <, >, <>
    					'assoc' => 'AND',           //关系类型,可以是AND, OR
    					'name'  => 'site',         //GET的值的访问键名
    					'type'  => 'int',        //GET的值的类型
    			),

    	));
        $page = $this->_get_page(30);
        $locations = $this->_motif_group_mod->find(array(
            'conditions' => '1=1 '.$conditions ,
            'limit' => $page['limit'],
            'order' => "id ASC",
            'count' => true,
        ));
        $this->assign('sites',$this->sites);
        foreach ($locations as $key=>$val){
        	$temp_id=$val['site_id'];
        	$locations[$key]['site']=$this->sites[$temp_id];
        }
        $this->assign('locations', $locations);
        $page['item_count'] = $this->_motif_group_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));
        $this->display('motif/motif_location.index.html');
    }
    //END function

    /**
    *添加栏目位置
    *
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月13日
    *@return 
    */
    function add_group(){
        if(!$_POST){
        	$this->assign('sites',$this->sites);
            $this->display('motif/motif_location.info.html');
        }else{
            $name  = $_POST['name'];
            $code=$this->check_code_unique($_POST['code'])?$_POST['code']:'';
            $site_id=$_POST['site_id'];
            if(!$code){
            	return $this->show_warning('位置标记已被使用，请换用其他标记');
            }
            $data  = array(
            	'site_id'=>$site_id,
            	'code'=>$code,
                'name' => $name,
                'add_time'=> gmtime(),
            );
            $id = $this->_motif_group_mod->add($data);
            if($id) {
                $this->show_message('位置添加成功!', 'back_list', 'index.php?app=motif&act=group_list');
            } else {
                $this->show_warning('位置添加失败!');
            }
        }

    }
    //END function
    
    /**
    *编辑栏目位置
    *
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月13日
    *@return 
    */
    function edit_group()
    {
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

        $data     = $this->_motif_group_mod->find($id);
        if (empty($data)){
            $this->show_warning('栏目位置不存在！');
            return;
        }

        if (!IS_POST) {
            $this->import_resource(array('script' => 'inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
            $data    =   current($data);
            $this->assign('data',$data);
            $this->assign('sites',$this->sites);
            $this->display('motif/motif_location.info.html');
        } else {
        	$code=$this->check_code_unique($_POST['code'],$_POST['id'])?$_POST['code']:'';
        	if(!$code){
        		return  $this->show_warning('该标记已被使用，请换用其他标记');
        	}
            $data = array(
                'name'        => $_POST['name'],
            	'code'=>$code,
//             	'site_id'=>$_POST['site_id'],
            	'add_time'    => gmtime(),
            );

            $res = $this->_motif_group_mod->edit($id, $data);
            $this->show_message('位置编辑成功!', 'back_list','index.php?app=motif&act=group_list');
        }
    }
    //END function

    /**
    *删除栏目位置
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
        if (!$this->_motif_group_mod->drop($ids))
        {
            $this->show_warning($this->_motif_group_mod->get_error());
            return;
        }

        $this->show_message('位置删除成功');
    }
    //END function

    
    /**
    *位置标记为唯一标识不可重复（前台链接使用）
    *@version 1.0
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月16日
    *@return 
    */
    function ajax_code_unique()
    {
    	$id=empty($_POST['id'])?'':trim($_POST['id']);
    	$code=empty($_POST['code'])?'':trim($_POST['code']);
    	if($code){
    		$location=$this->_motif_group_mod->find(
    				array("conditions"=>"code='$code'")
    				);
    		if($id){
    			if($location && !$location[$id]){
    				echo ecm_json_encode(false);
    				return ;
    			}else{
    				echo ecm_json_encode(true);
    				return ;
    			}
    		}else{
    			if($location){
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
    *栏目位置标记为唯一标识不可重复
    *@version 1.0
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月16日
    *@return 
    */
    function check_code_unique($code,$id='')
    {
    	if($code){
    		$location=$this->_motif_group_mod->find(
    				array("conditions"=>"code='$code'")
    				);
    		if($id){
    			if($location && !$location[$id]){
    				return false ;
    			}else{
    				return true;
    			}
    		}else{
    			if($location){
    				return false;
    			}else{
    				return true;
    			}
    		}
    	}else{
    		return ;
    	}
    	return ;
    }
    //END function
    /**
     *ajax根据站点获取位置
     *
     *@author cyrus <2621270755@qq.com>
     *@date 2016年5月13日
     *@param  $_POST['pid']
     *@return
     */
    function ajax_get_locations() {
    	$siteid = $_POST ['siteid'];
    	if (! $siteid && !isset($siteid)) {
    		return $this->json_error ( '失败' );
    	}
    
    	$list = $this->_motif_group_mod->get_options_html ( $siteid);
    	$this->json_result ( $list );
    	return;
    }
    //END function
    
    /**
    *ajax异步加载栏目关联内容页面
    *@access public
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月20日
    *@version 1.0
    *@return 
    */
    function load_add_content(){
    
    	$num=intval($_GET['num'])+1;
    	if(!$num){
    		$this->json_error('传参失败');
    	}
    	$img="img{$num}";
    	$this->assign('img',$img);
    	$this->assign("num", $num);
    	$this->assign('url_rule_values',$this->url_rule_values);
    	$this->assign('url_rule_names',$this->url_rule_names);
    	$content = $this->_view->fetch("motif/content.add.html");
    	$this->json_result($content);
    	die();
    }
    //END function
}

?>