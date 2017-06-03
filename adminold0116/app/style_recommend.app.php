<?php

/**
 * 风格产品推荐
 *
 *
 * @access protected
 * @author xuganglong <781110641@qq.com>
 * @return void
 */
class Style_recommendApp extends BackendApp
{
    var $_recommend_mod;

    function __construct()
    {
        $this->Style_recommendApp();
    }

    function Style_recommendApp()
    {
        parent::BackendApp();
		$this->_recommend_mod = & m('style_recommend');
		$this->_dis_mod       = & m('dissertation');
		$this->_suit_mod      = & m('suitlist');
    	$this->_customs_mod   = & m("custom");
		$this->_view_type      = array(//定义展示终端
			array(
				'type_id' => 1,
				'type_name' => '创业者app',
			),
			array(
				'type_id' => 2,
				'type_name' =>'消费者app',
			),
		);
		
    }
	
	/**
	 * 风格产品推荐--列表
	 *
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
	 */
    function index()
    {
		//print_r($this->_customs_mod);exit;
        $title = isset($_GET['title']) ? trim($_GET['title']) : '';
        $query['to_site']  = isset($_GET['to_site']) ? trim($_GET['to_site']) : '';
        $conditions = '';
        if($name){
            $conditions .= " AND name LIKE '%".$title."%'";
        }
        if($query["to_site"]){
            $conditions .= " AND to_site = '{$query["to_site"]}'";
        }

        $page = $this->_get_page(30);
        $items = $this->_recommend_mod->find(array(
            'conditions' => '1=1' . $conditions,
            'limit' => $page['limit'],
            'order' => "add_time DESC, sort_order ASC",
            'count' => true,
        ));
	
        $this->assign('view_type', $this->_view_type);
		$this->assign('items', $items);
        $page['item_count'] = $this->_recommend_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign("to_site", $this->to_site);
        $this->assign('title', $title);
        
       // $this->assign("cats", $this->disCats());
        
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));

        $this->display('style_recommend.index.html');
    }

	
	/**
	 * 添加产品推荐
	 *
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
	 */
    function add()
    {
    	if (!IS_POST) {
    		$this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
				
	    	$template_name = $this->_get_template_name();
	    	$style_name    = $this->_get_style_name();
            $this->assign("to_site", $this->to_site);
			$this->assign('view_type', $this->_view_type);
	    	$this->assign('build_editor', $this->_build_editor(array(
	    			'name' => 'content',
	    			'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
	    	)));
	    	$this->display('style_recommend.info.html');
    	} else {
            $to_site    = $_POST['to_site'];
    		$stime = isset($_POST['start_time']) ? strtotime($_POST['start_time']) : 0;
    		$etime = isset($_POST['end_time']) ? strtotime($_POST['end_time']) : 0;
    		if($stime && $etime){
    			if($stime >= $etime){
    				$this->show_message("主题结束时间必须大于开始时间！");
    				return false;
    			}
    		}
			//图片上传
			include_once ROOT_PATH."/includes/libraries/ImageTool.class.php";
			$imageTool = new ImageTool();
			$imageTool->_upload_dir = ROOT_PATH.'/upload/recommend/';
			if ($_FILES["slider_img"]) {
				$slider_img = $imageTool->uploadImage($_FILES["slider_img"]);
			}


			$data = array(
				'name'       => $_POST['name'],
				'start_time' => $stime,
				'end_time'   => $etime,
				'brief'      => $_POST['brief'],
				'status'     => $_POST['status'],
				'sort_order' => $_POST['sort_order'],
				'slider_img' => '/upload/recommend/'.$slider_img,
				'add_time'   => gmtime(),
				'type'       => $_POST['type'],
                'to_site'    => $to_site,
			);

            if($to_site=='pc'){
                $data['customs'] = $_POST['linkid_pc'];
                $data['dissertations'] = $_POST['disid_pc'];
            }elseif($to_site=='app'){
                $data['customs'] = $_POST['linkid_app'];
                $data['dissertations'] = $_POST['disid_app'];
            }else{
                $data['customs'] = '';
                $data['dissertations'] = '';
            }
    			
			$id = $this->_recommend_mod->add($data);
	
			if($id) {
				$this->show_message('主题添加成功!', 'back_list', 'index.php?app=style_recommend');
			} else {
				$this->show_message('主题添加失败!', 'back_list', 'index.php?app=style_recommend');
			}
    	}
    }

	/**
	 * 编辑产品推荐
	 *
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
	 */
    function edit()
    {
    	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    	
    	$data     = $this->_recommend_mod->find($id);
    	if (empty($data)){
    		$this->show_warning('主题不存在！');
    		return;
    	}

    	if (!IS_POST) {
    		$this->import_resource(array('script' => 'inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
    				'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
	    	$data    =   current($data);
            //print_exit($data);

	    	$template_name = $this->_get_template_name();
	    	$style_name    = $this->_get_style_name();
            $this->assign("to_site", $this->to_site);
			$this->assign('view_type', $this->_view_type);
	    	$this->assign('build_editor', $this->_build_editor(array(
	    			'name' => 'content',
	    			'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
	    	)));

	    	$this->assign('linkid', $data['customs']);
			$this->assign('disid', $data['dissertations']);
	    	$this->assign('data',$data);
    		$this->display('style_recommend.info.html');
    	} else {
    		$stime = isset($_POST['start_time']) ? strtotime($_POST['start_time']) : 0;
    		$etime = isset($_POST['end_time']) ? strtotime($_POST['end_time']) : 0;
            $to_site       = $_POST['to_site'];
    		if($stime && $etime){
    			if($stime >= $etime){
    				$this->show_message("主题结束时间必须大于开始时间！");
    				return false;
    			}
    		}
    		$data = array(
				'name'          => $_POST['name'],
				'start_time'    => $stime,
				'end_time'      => $etime,
				'brief'         => $_POST['brief'],
				'status'        => $_POST['status'],
				'sort_order'    => $_POST['sort_order'],
				'add_time'      => gmtime(),
				'type'          => $_POST['type'],
                'to_site'       => $to_site,
    		);
            if($to_site=='pc'){
                $data['customs'] = $_POST['linkid_pc'];
                $data['dissertations'] = $_POST['disid_pc'];
            }elseif($to_site=='app'){
                $data['customs'] = $_POST['linkid_app'];
                $data['dissertations'] = $_POST['disid_app'];
            }else{
                $data['customs'] = '';
                $data['dissertations'] ='';
            }

			if ($_FILES["slider_img"]['name']) {
				//图片上传
				include_once ROOT_PATH."/includes/libraries/ImageTool.class.php";
				$imageTool = new ImageTool();
				$imageTool->_upload_dir = ROOT_PATH.'/upload/recommend/';
				
				$slider_img = $imageTool->uploadImage($_FILES["slider_img"]);
				$data['slider_img'] = '/upload/recommend/'.$slider_img;
			}
			
    		$res = $this->_recommend_mod->edit($id, $data);
			$this->show_message('主题编辑成功!', 'back_list', 'index.php?app=style_recommend');
    	}
    }
	
	/**
	 * 删除产品推荐
	 *
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
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
        if (!$this->_recommend_mod->drop($ids))
        {
            $this->show_warning($this->_recommend_mod->get_error());
            return;
        }

        $this->show_message('drop_ok');
    } 

	/**
	 * 基本款ajax处理
	 *
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
	 */
	function ajax_customs_info(){
        $ids=$_GET['ids'];
        $did = intval($_GET['did']);
        $to_site = $_GET['to_site'];
        $cData=$this->_customs_mod->findAll("id IN ({$ids}) AND to_site='{$to_site}'");
        $order = array();
        foreach($cData as $key => $val){
        	$cData[$key]['order'] = $order[$key];
        }
        $this->assign('to_site',$to_site);
		$this->assign('goods_list',$cData);
        $this->display('style_recommend.item.html');
    }
	
	/**
	 * 套装ajax处理
	 *
	 *
	 * @access protected
	 * @author xuganglong <781110641@qq.com>
	 * @return void
	 */
	function ajax_dis_info(){
        $ids=$_GET['ids'];
        $did = intval($_GET['did']);
        $to_site = $_GET['to_site'];
        $cData=$this->_suit_mod->findAll("id IN ({$ids}) AND to_site='{$to_site}'");
        print_exit($cData);
        $order = array();
        foreach($cData as $key => $val){
        	$cData[$key]['order'] = $order[$key];
			$cData[$key]['name']  = $val['suit_name'];
        }
		$this->assign('goods_list', $cData);
        $this->display('style_recommend.item.html');
    }

}

?>