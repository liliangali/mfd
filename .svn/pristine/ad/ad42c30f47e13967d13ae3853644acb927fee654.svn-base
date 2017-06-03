<?php

/* 主题控制器 */
class DissertationApp extends DisApp
{
	var $_dis_mod;
	var $_customs_mod;
    function __construct()
    {
    	parent::__construct();
    	$this->_dis_mod =& m('dissertation');
    	$this->_link_mod =& m('links');
    	$this->_customs_mod=& m('customs');
    }

    function index()
    {
        $title = isset($_GET['title']) ? trim($_GET['title']) : '';
        $conditions = '';
        if($title){
            $conditions .= " AND title LIKE '%".$title."%'";
        }
        

    	$cat =  isset($_GET['cat']) ? trim($_GET['cat']) : '';
    	if($cat){
    	    $conditions .= " AND cat = '{$cat}'";
    	}


        $page = $this->_get_page(20);
        $items = $this->_dis_mod->find(array(
            'conditions' => '1=1' . $conditions,
            'limit' => $page['limit'],
            'order' => "id DESC, sort_order ASC",
            'count' => true,
        ));
		
        $this->assign('items', $items);
        $page['item_count'] = $this->_dis_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);

        $this->assign('title', $title);
        
        $this->assign("cats", $this->disCats());
        
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display('dissertation.index.html');
    }
    
    function add()
    {

    	if (!IS_POST)
    	{
    		$this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
	    	
	    	
	    	$template_name = $this->_get_template_name();
	    	$style_name    = $this->_get_style_name();
	    	
	    	$this->assign("cats", $this->disCats());
	    	
	    	$this->assign('build_editor', $this->_build_editor(array(
	    			'name' => 'content',
	    			'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
	    	)));
	    	$this->display('dissertation.info.html');
    	}
    	else
    	{
    		
    		$stime = isset($_POST['start_time']) ? strtotime($_POST['start_time']) : 0;
    		$etime = isset($_POST['end_time']) ? strtotime($_POST['end_time']) : 0;
    		if($stime && $etime){
    			if($stime >= $etime){
    				$this->show_message("主题结束时间必须大于开始时间！");
    				return false;
    			}
    		}

    		
    			$data = array(
    				'title' => $_POST['title'],
    				'price' => $_POST['price'],
    				'start_time' => $stime,
    				'end_time' =>   $etime,
    				'cat'      => $_POST['cat'],
    				'brief'    => $_POST['brief'],
    				'design'   => $_POST['design'],
    				'is_hot'   => $_POST['is_hot'],
					'is_rec'   => $_POST['is_rec'],
    				'content'  => $_POST['content'],
    				'sort_order' => $_POST['sort_order'],
    				'small_img'  => $_POST["small_img"],
    				'middle_img' => $_POST["middle_img"],
    				'add_time'   => gmtime()
    			);
    			
    			$id = $this->_dis_mod->add($data);
    	
    			
    			if($id){
    				
    				if($_POST['linkid']){
    					$links = explode(",",$_POST['linkid']);
    					
    					foreach($links as $key => $val){
    						$this->_link_mod->add(array("d_id" => $id, "c_id" => $val, 'lorder' => $_POST['lorder'][$val]));
    					}
    					//$this->_link_mod->find(array("d_id" => $id));
    				}
    				
    				/* add by v5 生成基本款的 二维码 */
    				Qrcode('dissertation',$id,M_SITE_URL.'/index.php/dissertation-info-'.$id.'.html');
    				
    				$this->show_message('主题添加成功!',
    					'back_list', 'index.php?app=dissertation');
    			}
    			else
    			{
    				$this->show_message('主题添加失败!',
    					'back_list', 'index.php?app=dissertation');
    			}
    	}
    	
    }
    
    function edit()
    {
    
    	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    	
    	$data     = $this->_dis_mod->find($id);
    	if (empty($data))
    	{
    		$this->show_warning('主题不存在！');
    	
    		return;
    	}
    	$_olink=$this->_link_mod->find("d_id = {$id}");
    	foreach ($_olink as $row){
    	    $link[]=$row['c_id'];
    	}
    	
    	if (!IS_POST)
    	{
    		$this->import_resource(array('script' => 'inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
    				'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
	    	$data    =   current($data);
	    	
	    	$template_name = $this->_get_template_name();
	    	$style_name    = $this->_get_style_name();
	    	$this->assign('build_editor', $this->_build_editor(array(
	    			'name' => 'content',
	    			'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
	    	)));
	    	
	    	if(isset($link) && !empty($link)){
	    	    $linkid=implode(',', $link);
	    	}else{
	    		$linkid='';
	    	}
	    	
	    	$this->assign('linkid',$linkid);
	    	
	    	$this->assign("cats", $this->disCats());
	    	
	    	$this->assign('data',$data);
    		
    		$this->display('dissertation.info.html');
    	}
    	else
    	{
    		$stime = isset($_POST['start_time']) ? strtotime($_POST['start_time']) : 0;
    		$etime = isset($_POST['end_time']) ? strtotime($_POST['end_time']) : 0;
    		if($stime && $etime){
    			if($stime >= $etime){
    				$this->show_message("主题结束时间必须大于开始时间！");
    				return false;
    			}
    		}

    		
    		$data = array(
    				'title' => $_POST['title'],
    				'price' => $_POST['price'],
    				'start_time' => $stime,
    				'end_time' => $etime,
    				'cat'      => $_POST['cat'],
    				'brief'    => $_POST['brief'],
    				'design'   => $_POST['design'],
    				'is_hot'   => $_POST['is_hot'],
					'is_rec'   => $_POST['is_rec'],
    				'content'  => $_POST['content'],
    				'sort_order' => $_POST['sort_order'],
    				'small_img'  => $_POST["small_img"],
    				'middle_img' => $_POST["middle_img"],
    		);
    		 
    		
    		$res = $this->_dis_mod->edit($id, $data);
    			
    		
			if(!empty($_POST['linkid'])){
			    
				$links = explode(",",$_POST['linkid']);
				
				$_ls = array();
				foreach($links as $val){
					$_ls[$val] = $val;
				}
				
				$eds = array();
				
				if(!empty($_olink)){
				    foreach($_olink as  $val){
				        if(!isset($_ls[$val["c_id"]])){
				            $this->_link_mod->drop($val["id"]);
				        }else{
				        	$eds[] = $_ls[$val["c_id"]];
				            unset($_ls[$val["c_id"]]);
				        }
				    }
				    
				}
				
				if(!empty($_ls)){
					foreach($_ls as $val){
						$this->_link_mod->add(array("d_id" => $id, "c_id" => $val, 'lorder' => $_POST['lorder'][$val]));
					}
				}
				
				if(!empty($eds)){
					foreach($eds as $key => $val){
						$this->_link_mod->edit("d_id='{$id}' AND c_id='{$val}'", array('lorder' => $_POST['lorder'][$val]));
					}
				}
				
			}else{
				$this->_link_mod->drop("d_id = '{$id}'");
			}
			
			/* add by v5 生成基本款的 二维码 */
			Qrcode('dissertation',$id,M_SITE_URL.'/index.php/dissertation-info-'.$id.'.html');
			
			$this->show_message('主题编辑成功!',
					'back_list', 'index.php?app=dissertation');

    	}
    }

    function drop()
    {
    	
    	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    	
    	if (!$this->_dis_mod->drop($id))    //删除
    	{
    		$this->show_warning($this->_dis_mod->get_error());
    	
    		return;
    	}
    	else
    	{
    		$this->_link_mod->drop("d_id = '{$id}'");
    		$this->show_message('主题删除成功!',
    					'back_list', 'index.php?app=dissertation');
    	}

    }
    
    function ajax_customs_info(){
        $ids=$_GET['ids'];
        $did = intval($_GET['did']);
        $cData=$this->_customs_mod->findAll("cst_id IN ({$ids}) AND to_site='app'");
        $links = $this->_link_mod->find(array(
        	"conditions" => "d_id='{$did}'",
        ));
        
        $order = array();
       	foreach($links as $key => $val){
       		$order[$val['c_id']] = $val['lorder'];
       	}
        
        foreach($cData as $key => $val){
        	$cData[$key]['order'] = $order[$key];
        }
        $this->assign('goods_list',$cData);
        $this->display('dissertation.item.html');
    }
    
}

?>
