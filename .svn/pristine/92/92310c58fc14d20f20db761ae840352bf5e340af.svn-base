<?php
define('MAX_LAYER', 4);

/* 地区控制器 */
class ActiveApp  extends BackendApp
{
	var $_folly_mod;
	var $_active_mod;
	function __construct(){
        $this->ActiveApp();
    }
	function ActiveApp(){
        parent::__construct();
        $this->_folly_mod =& m('fimg');
        $this->_active_mod =& m('activeimg');
    }
    
    //列表
    function index(){
    	$page=$this->_get_page(30);
    	$active_items=$this->_active_mod->find(array(
    			'conditions'=>"1=1",
    			'field'=>'*',
    			'order'=>'l_order asc,id desc',
    			'limit'=>$page['limit'],
    			'count'=>true
    	));
    	$page['item_count']=$this->_active_mod->getCount();
    	$this->_format_page($page);
        $this->import_resource(array(
        		'script' => 'jqtreetable.js,inline_edit_admin.js',
        		'style'  => 'res:style/jqtreetable.css')
        );

        $this->assign('page_info', $page);
        $this->assign('pagearr', $active_items);
        $this->display('active.list.html');
    }
    
    //添加广告
    function add(){
        $db = &db();
        if(!IS_POST){
         $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
            
            
            $template_name = $this->_get_template_name();
            $style_name    = $this->_get_style_name();
  /*活动位置屏蔽  */      
/*             $location = $db->getall("select * from cf_activetpl ");
            $this->assign('location',$location); */
            $this->display('active.add.html');
        }else{
            $db = &db();
         
            //$sm=$_POST['smallname'];
            //$ct = $db->getall("select title from cf_activetpl where id = '$sm'");
            //$tpl_title = $ct[0]['title'];
            /*  活动位置屏蔽 活动位置id及title写死  */
            $sm=3;
            $tpl_title='www';
            $title = $_POST['title'];
            $link  = $_POST['link'];
            /*  活动打开方式屏蔽 */
            //$linktype = $_POST['linktype'];
            $linktype="_blank";
            $stime = isset($_POST['start_time']) ? strtotime($_POST['start_time']) : 0;
            $etime = isset($_POST['end_time']) ? strtotime($_POST['end_time']) : 0;
            if($stime && $etime){
                if($stime >= $etime){
                    $this->show_warning("活动结束时间必须大于开始时间！","back");
                    return false;
                }
                if($etime<=time()){
                	$this->show_warning("活动结束时间必须大于当前时间！","back");
                	return false;
                }
           }
            
            
            $is_show    = $_POST['is_show'];
            $lorder   = $_POST['lorder'];
            $introduce=$_POST['introduce'];
            $uptime   = time();
            $flag=0;
            $path="../upload/images/";
            if(!file_exists($path)){
                mkdir("$path", 0777);
            }
            $tp = array("image/gif","image/png","image/jpeg",'image/jpg');
            if(!in_array($_FILES["file"]["type"],$tp)){
                $this->show_warning('格式不对','<<返回');
                exit;
            }
            if($_FILES["file"]["name"]){
                $file1=$_FILES["file"]["name"];
                $file2 = $path.time().$file1;
                $flag=1;
            }
            if($flag){
                $result=@move_uploaded_file($_FILES["file"]["tmp_name"],$file2);
            }else{
            	$this->show_warning('文件传输失败','back');
            	return ;
            }
            if($result){
            	$q=$db->query("insert INTO cf_activeimg (title,link,linktype,uploadfiles,uptime,starttime,endtime,l_order,title_id,tpl_title,is_show,clicknum,introduce) VALUES ('$title','$link','$linktype','$file2','$uptime','$stime','$etime','$lorder','$sm','$tpl_title','$is_show','0','$introduce')");
            }else{
            	$this->show_warning('文件传输失败','back');
            	return ;
            }
            
            if($q==true){
                $this->show_message('添加成功','back_list', 'index.php?app=active&act=index&amp;cate_id=0');
            }else{
                $this->show_message('添加失败','<<返回', 'index.php?app=active&act=add&amp;cate_id=0');
            }
        }
      
    }
    ///$this->show_message('edit_zujian_successed','back_list', 'index.php?app=active&act=index&amp;cate_id='.$cate_id);
    
 
    //添加广告位
    function add_location(){
        $db = &db();
        if(!IS_POST){
            $this->display('active.add_location.html');
        }else{
            
            $db = &db();
            $tpl_title=$_POST['child_title'];
            $query=$db->getall("select * from cf_activetpl where title='$tpl_title'");
            if($query){
                $this->show_warning('该活动位置已经存在','<<返回');
            }else{
                $querys=$db->query("insert INTO cf_activetpl (title) VALUES ('$tpl_title')");
                if($querys){
                    $this->show_message('添加成功','<<返回','index.php?app=active&act=add_location&amp;cate_id=0');
                }
            }
            
        }
       
       
    }
    
    //修改
    function edit(){
        if(!IS_POST){
            $db = &db();
            $id=$_GET['id'];
            $lists = $db->getall("select * from cf_activeimg  where cf_activeimg.id=$id");

            $json2 = $db->getall("select * from cf_activetpl ");
            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
            
            
            $template_name = $this->_get_template_name();
            $style_name    = $this->_get_style_name();
            $this->assign('lists2',$json2);
            	
            $this->assign('lists',$lists[0]);
            $this->display('active.edit.html');
        }else{
            //$this->_folly_mod
             $db = &db();
             $stime = isset($_POST['start_time']) ? strtotime($_POST['start_time']) : 0;
             $etime = isset($_POST['end_time']) ? strtotime($_POST['end_time']) : 0;
             if($stime && $etime){
                 if($stime >= $etime){
                     $this->show_warning("活动结束时间必须大于开始时间！","back");
                     return ;
                 }
                 if ($etime<=time()){
                 	$this->show_warning("活动结束时间必须大于当前时间！","back");
                 	return ;
                 }
             }
             $link=$_POST['link'];
            
            $data = array(
                "title"    => $_POST['title'],
                'link'     => $link,
                'linktype' =>"_blank",
                'l_order'  => $_POST["lorder"],
                'title_id' => 3,
                'uptime'   => time(),
                'starttime'=> $stime,
                'endtime'  => $etime,
                'is_show'  => $_POST['is_show'],
            	'introduce'=>$_POST['introduce']
            );
            $ct = $db->getall("select title from cf_activetpl where id = '$data[title_id]' ");
            $data['tpl_title'] = $ct[0]['title'];
            	
            $path="../upload/images/";        //上传路径
            $tp = array("image/gif","image/png","image/jpeg",'image/jpg');
            if(!in_array($_FILES["file"]["type"],$tp) && $_FILES["file"]["name"]){
                $this->show_warning('格式不对','<<返回');
                exit;
            }
            if($_FILES["file"]["name"]){
                $file1=$_FILES["file"]["name"];
                $file2 = $path.time().$file1;
                $flag=1;
            }
            if($flag){
                $result=@move_uploaded_file($_FILES["file"]["tmp_name"],$file2);
                $data["uploadfiles"] = $file2;
            }
            	
            $res = $this->_active_mod->edit($_POST["id"], $data);
    
            if(!$res){
                $this->show_warning($this->_active_mod->get_error());
                return;
            }
            	
            	
            $this->show_message("操作成功!",
                '返回列表',    'index.php?app=active'
            );
        }
    }
    
    //删除
    function del(){
        $id=$_REQUEST['id'];
        $act=$this->_active_mod->get($id);
        $file=substr($act['uploadfiles'],2);
        $this->_active_mod->drop($id);
        
        $this->show_message('活动删除成功','<<返回','index.php?app=active&act=index&amp;cate_id=0');
    }
    //删除指定目录下的文件
/*     function delFile($dirName) {
    	var_dump($dirName);
		if (file_exists ( $dirName ) && $handle = opendir ( $dirName )) {
			var_dump(123);
			while ( false !== ($item = readdir ( $handle )) ) {
				if ($item != "." && $item != "..") {
					if (file_exists ( $dirName . '/' . $item ) && is_dir ( $dirName . '/' . $item )) {
						delFile ( $dirName . '/' . $item );
					} else {
						var_dump(123);
						var_dump($dirName);
						if (unlink ( $dirName . '/' . $item )) {
							return true;
						}
					}
				}
			}
			closedir ( $handle );
		}
	} */
	
}

?>












