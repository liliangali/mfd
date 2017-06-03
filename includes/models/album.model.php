<?php
class AlbumModel extends BaseModel
{
	var $table  = 'album';
	var $prikey = 'id';
	
	function delById($id){
		$db = &db();
		
		$photo = m('userphoto');
		
		$album = $this->getById($id);
		if(!$album)
			return -2;
		
		$photo->delByAlbumId($id);
		
		$sql = "delete from cf_album where id = $id";
		$db->query($sql);
			
		return 1;
	}
	
	function getById($id){
		$db = &db();
	
		$sql =  "select * from cf_album where id = ".$id;
		$photo = $db->getRow($sql);
		return $photo;
	}
	
	function getByUidCate($uid,$cate = ''){
		$db = &db();
		$where = " uid = ".$uid;
		
		if($cate)
			$where .= " and cate = $cate ";
			
		
		$sql =  "select * from cf_album where $where";
		return $db->getAll($sql);
	}
	
	function getRecommed($cate = '' ,$uid = '' , $desc = '' , $limit = 4){
		$db = &db();
		$where = " recommend = 1 ";
		if($cate)
			$where .= " and cate = $cate ";
		if($uid)
			$where .= " and uid = $uid ";
	
		if($limit)
			$limit = " limit $limit ";
	
		if($desc)
			$desc = " order by " . $desc;
	
		$sql = "select * from cf_album where $where $desc $limit ";
// 		echo $sql;
		$rs = $db->getAll($sql);
		return $rs;
	}
	
	function pageByCateUid($curr_oage = 1 , $page = 10,$cate = 0 ,$uid = 0 ,$desc = ''){
		$db = &db();
		$where = " 1 = 1 ";
		if($cate)
			$where .= " and cate = $cate ";
		if($uid)
			$where .= " and uid = $uid ";
	
		if(!$where)
			return 0;
	
		$total = $this->cntByCateUid(2,$uid);
		if(!$total)
			return 0;
	
		if($desc)
			$desc = " order by " . $desc;
	
		include 'includes/libraries/page.lib.php';
		$page = new Page($total,$page,$curr_oage);
		$page->moduleSymbol = "/index.php/kuke-album.html";
		$page->execPage();
	
		$sql = "select * from cf_album where $where $desc limit ".$page->mLimit[0]." , " .$page->mLimit[1];
		$rs = $db->getAll($sql);
		foreach($rs as $k=>$v){
			$rs[$k]['date'] = date("Y-m-d H:i:s",$v['add_time']);
			$rs[$k]['url'] = getCameraUrl($v['url'],200);
			$rs[$k]['top_url'] = getCameraUrl($v['top_url'],200);
			
			$rs[$k]['link'] = getPhotoDetailLink($v['id'], 1);
		}
		return array('err'=>0,'page'=>$page,'data'=>$rs);
	}
	
	function cntByCateUid($cate = 0 ,$uid = 0){
		$db = &db();
	
		$where = " 1 = 1 ";
		if($cate)
			$where .= " and cate = $cate ";
		if($uid)
			$where .= " and uid = $uid ";
	
		if(!$where)
			return 0;
	
		$sql = "select count(*) as total from cf_album where $where  ";
// 		echo $sql;
		$rs = $db->getRow($sql);
		return $rs['total'];
	}
	
	function getByCateUid($cate = 0 ,$uid = 0 ,$desc = '' ,$limit  = ''){
		$db = &db();
		$where = " 1 = 1 ";
		if($cate)
			$where .= " and cate = $cate ";
		if($uid)
			$where .= " and uid = $uid ";
	
		if(!$where)
			return 0;
	
		if($limit)
			$limit = " limit $limit ";
	
		if($desc)
			$desc = " order by " . $desc;
	
		$sql = "select * from cf_album where $where $desc $limit ";
		$rs = $db->getAll($sql);
		return $rs;
	}
	
	function delAlbum(){
		
	}
	
	
}
