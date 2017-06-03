<?php
/* 重要资讯推送
 * 2015-12-14 by shao
 *  */
class InformationsApp  extends BackendApp
{
	var $_member_mod;
	
	function __construct(){
		$this->_member_mod = &m("member");
	
           parent::__construct();
    }


    //列表
    function index(){
    	$informations=m("informations");
    	$gallery=m("infor_gallery");
    	$information=$informations->find(array(
    			'conditions' =>"1=1",
    			'order' =>"id DESC",
    	));
    	if($information){
    		$ins=0;
    		$iny =0;
    		foreach($information as $key=>$val){
    			if($val['state'] ==1)
    			{
    				$iny +=1;
    				if($val['trade'] ==0){
    					$ins +=1;
    				}else{
    					
    				}
    					if($val['trade'] ==1){
    						$tr='1';
    					}else{
    						$tr='2';
    					}
    					$this->assign('tr',$tr);
    				
    			}
    			if($val['con_type'] ==2){
    				$gallerys=$gallery->find(array(
    						'conditions' =>"infor_id='{$val['id']}'",
    						
    				));
    			}
    			$information[$key]['gallery']=$gallerys;
    			$information[$key]['time']=date("Y-m-d H:i",$val['time']);
    		}
    	}
    	$this->assign('ins',$ins);
    	$this->assign('iny',$iny);
    	$this->assign('information',$information);
    	$this->assign('trade',array(
    			'0'=>'全部平台',
    			'1'=>'ios',
    			'2'=>'android',
    	));
        $this->display('informations/index.html');
    }
    //查询资讯关闭状态
    function close(){
    	$id=$_GET['id'];
    	$informations=m("informations");
    	
    	$information=$informations->edit($id,array('state' =>0,));
    	$this->show_message('关闭资讯成功','back_list', 'index.php?app=informations');
    }
    //详情
    function add(){
    	$user=$this->visitor->get();
    	$tr=$_GET['tr'];
    	if($tr ==1){
    		$trade=array(
    				'2'=>'android',
    		);
    	}elseif($tr ==2){
    		$trade=array(
    				'1'=>'ios',
    		);
    	}else{
    		$trade=array(
    				'0'=>'全部平台',
    				'1'=>'ios',
    				'2'=>'android',
    		);
    	}
    	$informations=m("informations");
    	
    	if($_POST['con_type'] == 1){
    		$content=$_POST['content1'];
    	}
    	if($user['user_name'] =='admin')
    	{
    		$user['nickname']=$user['user_name'];
    	}
    	if($_POST){
    		$data=array(
    				'title' => trim($_POST['title']),
    				'content' =>$content,
    				'con_type' =>$_POST['con_type'],
    				'trade' =>$_POST['trade'],
    				'time' =>time(),
    				'user' =>$user['nickname'],
    				'user_id' =>$user['user_id'],
    				'state' =>1,
    		);
    		$informa=$informations->add($data);
    		
    		if($_POST['con_type'] == 2 && $informa){
    			$this->addGallery($informa);
    		}
    		if($informa){
    			$this->show_message("资讯添加成功",
    					'back_list', 'index.php?app=informations');
    		}else{
    			$this->show_message("资讯添加失败",
    					'back_list', 'index.php?app=informations');
    		}
    	}else{
    		$param = array(
    				'dir' => 'gallery',
    				'button_text' => "商品图片"
    		);
    		 
    		$this->assign('build_upload', $this->_build_upload($param)); // 构建swfupload上传组件
    		$this->assign('trade',$trade);
    		$this->display('informations/info.html');
    	}
    	
    } 
    function check(){
    	$informations=m("informations");
    	$infor_gallery=m("infor_gallery");
        $id=$_GET['id'];
    	$information=$informations->get(array(
    			'conditions' =>"id='{$id}'",
    	));
    	
    	if($information){
    			if($information['con_type'] ==2){
    				$gallerys=$infor_gallery->find(array(
    						'conditions' =>"infor_id='{$information['id']}'",
    	
    				));
    			}
    			$information['gallery']=$gallerys;
    		
    	}
    	$this->assign('data',$information);
    	$this->display('informations/check.html');
    }
    function addGallery($id){
    	//相册图片
    $infor_gallery=m("infor_gallery");
    	import("image.func");
    	$gallery = array();
    	foreach((array)$_POST['gallery'] as $k => $v){
    		$f=dirname($v);
    		$fname = str_replace($f, '',$v);
    		$t = date("YmdHi");
    
    		$sPath = '/upload/thumb/s/'.$t.$fname;
    		$mPath = '/upload/thumb/m/'.$t.$fname;
    
    		$s_img = make_thumb($v, ROOT_PATH.$sPath,70, 100);
    
    		$m_img = make_thumb($v, ROOT_PATH.$mPath,750, 1334);
    		if($s_img && $m_img){
    			$gallery[] = array(
    					'infor_id' => $id,
    					's_img'  => $sPath,
    					'm_img'  => $mPath,
    					'l_img'    => $v,
    			);
    		}
    	}
    	if(!empty($gallery)){
    		$infor_gallery->add($gallery);
    	}
    }
   
}
?>