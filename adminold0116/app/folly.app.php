<?php
define('MAX_LAYER', 4);

/* 地区控制器 */
class FollyApp  extends BackendApp
{
	var $_folly_mod;
	function __construct(){
        $this->FollyApp();
    }
	function FollyApp(){
        parent::__construct();
        $this->_folly_mod =& m('fimg');
    }
	//列表
	function index(){
		$page=$this->_get_page(10);
		$conditions="1=1";
		$folly_items=$this->_folly_mod->find(array(
				'fields'=>'*',
				'conditions'=>$conditions,
				'join'=>'has_tpl',
				'limit'=>$page['limit'],
				'order'=>"tpl_id asc",
				'count'=>true
		));
		$page['item_count']=$this->_folly_mod->getCount();
		$this->_format_page($page);
		$this->assign('page_info',$page);
		//var_dump($page);
		$this->assign('pagearr',$folly_items);
		$this->display('folly.list.html');
		/* $db = &db();
		$page=1;
		if(@$_GET['page']){
		  $page=@$_GET['page'];
		}
		else{
		  $page=1;
		}
		$pageSize=30; */
		
/* 		$pageOffset=($page-1)*$pageSize;
		$counts=$db->getall("select count(*) from cf_fimg");
		$count=$counts[0]['count(*)'];
		$endpage =  ceil($count/$pageSize);
		$result=mysql_query("select * from cf_fimg join cf_tpl on cf_fimg.tpl_id=cf_tpl.tid order by tpl_id asc limit $pageOffset,$pageSize");

		$arr=array();
		while($row=@mysql_fetch_assoc($result)){
			$arr[]=$row;
		}
		$next_page = $page + 1; //当前页 + 1 = 下一页
		$pre_page = $page - 1; //当前页 - 1 = 上一页

		$this->assign('pre_page', $pre_page);
	    $this->assign('next_page', $next_page);
		$this->assign('endpage', $endpage);
		$this->assign('count', $count);
		$this->assign('page', $page);
		$this->assign('pagearr', $arr);  
		$this->display('folly.list.html'); */
	}
	//添加广告
	function add(){
		$db = &db();
		$bigname=$_GET['bigname'];
		$selectd = $db->getall("select * from cf_tpl where parent_id=0");
		$t_l = $db->getall("SELECT * FROM cf_gcategory where parent_id=0 and store_id=0");
		$this->assign('selectd',$selectd);
		$this->assign('t_l',$t_l);
		$this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
	    $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();
        $this->assign('build_editor', $this->_build_editor(array(
                'name' => 'summary,content',
                'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
            )));
		if($bigname){
			$json = $db->getall("select * from cf_tpl where parent_id='$bigname'");
			$res = array();
            foreach($json as $key =>$val){
				$res[] = $val;
			}
			echo json_encode($res); 
			die();
		}
		$this->display('folly.add.html');
	}
	//添加广告页面
	function add_tpl(){
		//读取现有的页面
		$db = &db();
		$selectd = $db->getall("select * from cf_tpl where parent_id=0");
		$this->assign('selectd',$selectd);

		$this->display('folly.add_tpl.html');
	}
	function add_tpl_do(){
		$db = &db();
		$tpl_title=$_POST['tpl_title'];
		$query=$db->getall("select * from cf_tpl where title='$tpl_title'");
		if($query){
			$this->show_warning('该已存在页面','<<返回');
		}else{
			$querys=$db->query("insert INTO cf_tpl (title,parent_id) VALUES ('$tpl_title','0')");
			if($querys){
				$this->show_warning('添加成功','<<返回');
			}
		}
	}
	//添加广告位
	function add_location(){
		$db = &db();
		$selectd = $db->getall("select * from cf_tpl where parent_id=0");
		$this->assign('selectd',$selectd);
		$this->display('folly.add_location.html');
	}
	function add_location_do(){
		$db = &db();
		$bigname=$_POST['bigname'];
		$lst=$db->getall("select * from cf_tpl where tid='$bigname'");
		$child_title=$_POST['child_title'];
		$tpl_id=$lst[0]['tid'];
		$q=$db->query("insert INTO cf_tpl (title,parent_id) VALUES ('$child_title','$tpl_id')");
		if($q){
			$this->show_warning('添加成功','<<返回');
		}
	}
	function add_do(){
		$db = &db(); 	
		$cate_id=$_POST['cate'];
		$t_id=$_POST['type_id'];
		$tpl_id=$_POST['bigname'];
		$sm=$_POST['smallname'];
		$tpl_child_id=$sm;
		$ct = $db->getall("select title from cf_tpl where tid = '$sm'");
		$tpl_title = $ct[0]['title'];
		$img_title=$_POST['title'];
		$link=$_POST['link'];
		$d=$_POST['lorder'];
		$tpl=$_POST['tpl'];
		$content = $_POST['summary'];
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
		}
		$q=$db->query("insert INTO cf_fimg (type_id,cate_id,tpl_id,tpl_child_title,tpl_child_id,img_title,link,l_order,uploadfiles,content) VALUES ('$t_id','$cate_id','$tpl_id','$tpl_title','$tpl_child_id','$img_title','$link','$d','$file2','$content')");
		if($q==true){
			$this->show_warning('添加成功','<<返回');
		}else{
			$this->show_warning('添加失败','<<返回');
		}
	}
	//修改
	function edit(){
		if(!IS_POST){
			$db = &db();
			$id=$_GET['id'];
			$bigname=$_GET['bigname'];
			$lists = $db->getall("select * from cf_fimg join cf_tpl on cf_fimg.tpl_id=cf_tpl.tid where cf_fimg.id=$id");
		
			if($bigname){
				$json = $db->getall("select * from cf_tpl where parent_id='$bigname'");
				$res = array();
	            foreach($json as $key =>$val){
					$res[] = $val;
				}
				echo json_encode($res); 
				die();
			}
			$t_l = $db->getall("SELECT * FROM cf_gcategory");
				
			$ty_l = $db->getall("select * from cf_gcategory where parent_id=0 and store_id=0");
			$selectd = $db->getall("select * from cf_tpl where parent_id=0");
			//echo "<pre>"; print_r($selectd); echo "</pre>";
			$this->assign('selectd',$selectd);
			$this->assign('ty_l',$ty_l);
			
			$json2 = $db->getall("select * from cf_tpl where parent_id='{$lists[0]["tpl_id"]}'");
		    $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
               'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
	    	$template_name = $this->_get_template_name();
       		$style_name    = $this->_get_style_name();
        	$this->assign('build_editor', $this->_build_editor(array(
                'name' => 'summary,content',
                'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
            )));
            
			$this->assign('lists2',$json2);
			$this->assign('lists',$lists[0]);
			$this->display('folly.edit.html');
		}else{
			//$this->_folly_mod
			$data = array(
				"img_title"    => $_POST['title'],
				'link'     => $_POST['link'],
				'l_order'  => $_POST["lorder"],
				'cate_id'  => $_POST['cate'],
				'tpl_id'   => $_POST['bigname'],
				'tpl_child_id' => $_POST["smallname"],
				'content'  => $_POST['summary']
			);
			
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
			
			$res = $this->_folly_mod->edit($_POST["id"], $data);
		
			if(!$res){
				$this->show_warning($this->_folly_mod->get_error());
				return;
			}
			
			
		    	$this->show_message("操作成功!",
    				'返回广告列表',    'index.php?app=folly'
    			);
		}
	}
	function edit_do(){
		$db = &db(); 	
		$id=$_POST['id'];
		$cate_id=$_POST['cate'];
		$t=$_POST['title'];
		$l=$_POST['link'];
		$d=$_POST['lorder'];
		$path="../upload/images/";        //上传路径 
		if(!file_exists($path)){
			mkdir("$path", 0777); 
		}
		$tp = array("image/gif","image/pjpeg","image/png","image/jpeg","image/jpg"); 
		if(!in_array($_FILES["file"]["type"],$tp)){ 
			$this->show_warning('请正确选择广告图片！','<<返回');
			exit; 
		}	
		if($_FILES["file"]["name"]) 
		{ 
			$file1=$_FILES["file"]["name"]; 
			$file2 = $path.time().$file1; 
			$flag=1; 
		}
		if($flag){
			$result=@move_uploaded_file($_FILES["file"]["tmp_name"],$file2);
		}
		$db->query(" UPDATE cf_fimg SET title='$t',link='$l',l_order='$d',uploadfiles='$file2' WHERE id='$id'");
		$this->show_warning('广告编辑成功','<<返回');
	}
	//删除
	function del(){
		$db = &db(); 	
		$id=$_GET['id'];
		$db->query("delete  from cf_fimg where id = $id");
		$this->show_warning('广告删除成功','<<返回');
	}
	
}

?>
