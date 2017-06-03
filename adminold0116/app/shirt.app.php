<?php
class ShirtApp extends BackendApp{
    var $_shirt_mod;
    var $_uploadedfile_mod;
    var $category_mod;
    function _construct(){
        $this->ShirtApp();
    }
    function ShirtApp(){
        parent::BackendApp();
        $this->_shirt_mod      =& m('shirt');
        $this->_uploadedfile_mod=&m('uploadedfile');
        $this->category_mod=&m('YhCategory');
        $v_color = array(
            '黑色' => '黑色',
            '灰色' => '灰色',
            '白色' => '白色',
            '粉色' => '粉色',
            '红色' => '红色'
        );
        $this->assign('v_color',$v_color);
        $v_figure = array(
            '棉' => '棉',
            '雪纺' => '雪纺',
            '真丝' => '真丝',
            '粘胶' => '粘胶',
            '其他面料' => '其他面料'
        );
        $this->assign('v_figure',$v_figure);
        $v_sex = array(
            '男款'=>'男款',
            '女款'=>'女款',
            '儿童款'=>'儿童款'
        );
        $this->assign('v_sex',$v_sex);
        $this->assign('v_yinhua',$this->category_mod->find());
        
    }
    function index(){
        $pageper=15;
         $this->import_resource(array('script'=>'inline_edit_admin.js'));
         if (isset($_GET['sort']) && isset($_GET['order'])){
             $sort  = strtolower(trim($_GET['sort']));           
             $order = strtolower(trim($_GET['order']));
             if (!in_array($order,array('asc','desc'))){
                 $sort  = 'v_id';
                 $order = 'desc';
             }
         }else{
             $sort  = 'v_id';
             $order = 'desc';
         }
          $conditions = $this->_get_query_conditions(array(
            array(
                'field' => 'v_name',
                'equal' => 'like',
            ),
            array(
                'field' => 'v_color',
                'equal' => '=',
            ),
            array(
                 'field' => 'v_figure',
                 'equal' => '=',
             ),
        ));
         $page = $this->_get_page($pageper);
         $goods_list = $this->_shirt_mod->find(array(
             'conditions'=>'1=1'.$conditions,
             'count' => true,
             'order' => "$sort $order",
             'limit' => $page['limit'],
        ));
         if(!$goods_list){
             $pageindex = empty($_REQUEST['page']) ? 1 : intval($_REQUEST['page']);
             $start = ($page -2) * $page_per;
             $pageper=$page['pageper'];
             $page=array();
             $page=array(
                 'limit'=>"{$start},{$pageper}",
                 'curr_page'=>$pageindex-1,
                 'pageper'=>$pageper
             );
             $goods_list=$this->_shirt_mod->find(array(
                 'condition'=>'1=1'.$conditions,
                 'count'=>true,
                 'order'=>"$sort $order",
                 'limit'=>$page['limit']
             ));
         }
         $arr=array();
         $arr=$this->category_mod->find();
         foreach ($goods_list as $key=>$value){
             foreach ($arr as $k=>$v){
                 if($v["id"]==intval($value["v_yinhua"])){
                     $goods_list[$key]["v_yhname"] = $v["name"];
                 }
             }
         }
//          var_dump($arr);
         $this->assign('goods_list', $goods_list);
         $this->assign('cates',$this->cate);
         
         $page['item_count'] = $this->_shirt_mod->getCount();     
         $this->_format_page($page);
         $this->assign('page_info', $page);
        $this->display('shirt/list.html');
    }
    function add(){
        if(!IS_POST){       
            //echo aa;exit;
            $this->import_resource(array('script'=>'jquery.plugins/jquery.validate.js,inline_edit_admin.js,change_upload.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/'.i18n_code().'.js',
                'style'=>'jquery.ui/themes/ui-lightness/jquery.ui.css'));
            $template_name=$this->_get_template_name();
            $style_name=$this->_get_style_name();
            $this->assign('build_editor',$this->_build_editor(array(
                           'name'=>'v_message',
                'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
            )));
            $this->display("shirt/info.html");
            
        }
        if(IS_POST){
            $data = array(
            'v_name' => $_POST['v_name'],
            'v_store' => $_POST['v_store'],
            'v_sex' => $_POST['v_sex'],
            'v_yinhua' => $_POST['v_yinhua'],
            'v_fabric_sn'  => $_POST["v_fabric_sn"],    
            'v_cprice' => $_POST['v_cprice'],
            'v_price' => $_POST['v_price'],
            'v_color'=>$_POST['v_color'],
            'v_figure'=>$_POST['v_figure'],
            'v_image'=>$_POST['v_image'],
            'v_dis_image'=>$_POST['v_dis_image'],
             'v_message'=>$_POST['v_message'],
            'v_custom_id'=>$_POST['jb_shirt'],
            'v_custom_name'=>$_POST['jbs'],
            		
             );     
         //print_exit($data);exit;
            $id = $this->_shirt_mod->add($data);
            
            if($id){      
                $this->show_message('样衣添加成功!',
                    'back_list', 'index.php?app=shirt');
            }
            else
            {
                $this->show_message('样衣添加失败!',
                    'back_list', 'index.php?app=shirt');
            }
        }  
    }
    function edit(){
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    	$data     = $this->_shirt_mod->find($id);
    	if (empty($data))
    	{
    	    $this->show_warning('样衣不存在！');   	     
    	    return;
    	}
    	if (!IS_POST)
    	{
    	    $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,change_upload.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
    	    				'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
    	    $data    =   current($data); 
    	    
    	    $template_name = $this->_get_template_name();
    	    $style_name    = $this->_get_style_name();
    	    $this->assign('build_editor',$this->_build_editor(array(
    	        'name'=>'v_message',
    	        'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
    	    )));
    	    $this->assign('data',$data);
    	    $this->display("shirt/info.html");
    	}else{
    	    $data=array(
    	     'v_name' => $_POST['v_name'],
            'v_store' => $_POST['v_store'],
            'v_sex' => $_POST['v_sex'],
            'v_yinhua' => $_POST['v_yinhua'],
            'v_fabric_sn'  => $_POST["v_fabric_sn"],
    	    'v_cprice' => $_POST['v_cprice'],
            'v_price' => $_POST['v_price'],
    	    'v_color'=>$_POST['v_color'],
    	    'v_figure'=>$_POST['v_figure'],
            'v_image'=>$_POST['v_image'],
            'v_dis_image'=>$_POST['v_dis_image'],
    	     'v_message'=>$_POST['v_message'],
    	    'v_custom_id'=>$_POST['jb_shirt'],
    	    'v_custom_name'=>$_POST['jbs'],
    	    );
    	}
    	if (IS_POST){
    	    $res = $this->_shirt_mod->edit($id, $data); 	
    	    if($res){
    	        $this->show_message('样衣编辑成功!',
    	            'back_list', 'index.php?app=shirt');
    	    }
    	}
    }
    function drop(){
       $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        $ret_page = isset($_GET['ret_page']) ? intval($_GET['ret_page']) : 1;
        if (!$id){
            $this->show_warning('Hacking Attempt');
            return;
        }
        $ids = explode(',', $id);
       if(!$this->_shirt_mod->drop($ids)){
           $this->show_warning($this->_shirt_mod->get_error());
           return;
       }else{
            $this->show_message('删除成功','back_list', 'index.php?app=shirt&page=' . $ret_page);
       }     
    }
}