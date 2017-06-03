<?php
class UserphotoModel extends BaseModel
{
	var $table  = 'userphoto';
	var $prikey = 'id';
	//$cate :1设计，2街拍
    //isAlbum:图片是否加入相册
	function getByCateUid($cate = 0 ,$uid = 0 ,$desc = '' , $limit  = ''  , $isAlbum = 0){
		$db = &db();
		$where = " 1 = 1 and status = 1 ";
		if($cate) 
			$where .= " and cate = $cate ";
		if($uid)
			$where .= " and uid = $uid ";
		
		if($isAlbum)
			$where .= " and album_id != 0 ";
		
		if($limit)
			$limit = " limit $limit ";
		
		if($desc)
			$desc = " order by " . $desc;

		$sql = "select * from ".DB_PREFIX."userphoto where $where $desc $limit ";
		$rs = $db->getAll($sql);
		if($rs)
			foreach($rs as $k=>$v){
				if($v['cate']){
					$rs[$k]['link'] = getPhotoDetailLink($v['id'],$v['cate']);
				}
			}
		return $rs;
	}
	
	function getRecommend($cate = 1 ,$desc = '',$limit = ''){
		$db = &db();
		
		$where = " recommend = 1 and status = 1 ";
		
		if($cate)
			$where .= " and cate = $cate ";
		
		if($limit)
			$limit = " limit $limit ";
		
		if($desc)
			$desc = " order by " . $desc;
		
		$sql = "select * from ".DB_PREFIX."userphoto where $where $desc $limit ";
		$rs = $db->getAll($sql);
		if($rs)
			foreach($rs as $k=>$v){
					$rs[$k]['link'] = getPhotoDetailLink($v['id'],$v['cate']) ;
			}
			
		return $rs;
	}
	
	function pageByCateUid($curr_page = 1 , $page = 10,$cate = 0 ,$uid = 0 ,$desc = '',$album_id = 0){
		if(!$curr_page)
			$curr_page = 1;
		
		$db = &db();
		$where = " 1 = 1 and status = 1 ";
		if($cate)
			$where .= " and cate = $cate ";
		if($uid)
			$where .= " and uid = $uid ";
	
		if($album_id)
			$where .= " and album_id = $album_id ";
		
		if(!$where)
			return 0;
	
		$total = $this->cntByCateUid($cate,$uid,$album_id);
		if(!$total)
			return 0;
		
		if($desc)
			$desc = " order by " . $desc;
		
		include 'includes/libraries/page.lib.php';
		$page = new Page($total,$page,$curr_page);
		if($cate == 1)
			$page->moduleSymbol = "/index.php/kuke-design.html";
		else
			$page->moduleSymbol = "/index.php/kuke-street.html";
		$page->execPage();
		$sql = "select * from ".DB_PREFIX."userphoto where $where $desc limit ".$page->mLimit[0]." , " .$page->mLimit[1];
		$rs = $db->getAll($sql);
		foreach($rs as $k=>$v){
			$rs[$k]['date'] = date("Y-m-d H:i:s",$v['add_time']);
			$rs[$k]['link'] = getPhotoDetailLink($v['id'], $v['cate']);
		}
		return array('err'=>0,'page'=>$page,'data'=>$rs);
	}
	
	function cntByCateUid($cate = 0 ,$uid = 0, $album_id = 0){
		$db = &db();
		
		$where = " 1 = 1 and status = 1 ";
		if($cate)
			$where .= " and cate = $cate ";
		
		if($uid)
			$where .= " and uid = $uid ";

        if($album_id)
            $where .= " and album_id = $album_id ";

		if(!$where)
			return 0;
		
		$sql = "select count(*) as total from ".DB_PREFIX."userphoto where $where  ";
		$rs = $db->getRow($sql);
		return $rs['total'];
	}
	
	function getById($id , $size = 500){
		$db = &db();
		
		$sql =  "select * from ".DB_PREFIX."userphoto where id = ".$id;
		$photo = $db->getRow($sql);
		if($photo)
			$photo['link'] = getPhotoDetailLink($id,$photo['cate']);
		
		return $photo;
	}
	
	function getByAlbumId($id , $limit = ''){
		$db = &db();
		if($limit)
			$limit = " limit $limit ";
		
		$sql =  "select * from ".DB_PREFIX."userphoto where status = 1 and album_id = ".$id . $limit;
		$photo = $db->getAll($sql);
		if($photo)
			foreach($photo as $k=>$v){
				$photo[$k]['link'] = getPhotoDetailLink($v['id'], $v['cate']);
			}
		return $photo;
	}
	
	function delById($id){
		$sql = "delete from ".DB_PREFIX."userphoto where id = $id limit 1 ";
		$db = &db();
		return $db->query($sql);
	}
	
	function delByAlbumId($id){
		$photos = $this->getByAlbumId($id);
		if($photos){
			foreach($photos as $k=>$v){
				$rs = delUserPhoto($v['id']);
			}
		}
	}
	
}
?>