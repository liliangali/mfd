<?php
/**
 * @author zhaoxinran
 * @version v1.1
 * @copyright Copyright 2015 new.api.dev.mfd.cn
 * @package app
 */
	class SYB 
	{
	    var $return;
		var $error = '';
		var $token = '';
		function __construct()
		{
		    $this->result = new Result();
			//=====返回结果=====
			$this->return['statusCode'] = 0;
			$this->return['msg']             = '';
		}
		public function test($data){
		    global $json;
		    $return=123;
		    return $json->encode($return);
		}
		public function push($data) {
		    global $json;
 		    $token   = isset($data->token) ? $data->token:'';
			$code= isset($data->code) ? $data->code: 'classroom';
			
			$_acategory_mod = &m('acategory');
		    $_article_mod   = &m('article');
			$cate=$_acategory_mod->get("code='{$code}'");
			//默认cate_id=0，则根据用户身份查询对应分类文章，
/* 			if(empty($cate_id)) {
				$cate_id = 73;//默认不登录或消费者显示小讲堂
				if($token) {
					$user_info = getUserInfo($token);
					//判断用户类型，创业者查询“创业者培训”；
// 					if($user_info['member_lv_id'] > 1) {
// 						$cate_id = 53;//创业培训
// 					}
				}
			} */
			
			$cate_ids = $_acategory_mod->get_descendant($cate['cate_id']);//小讲堂
			
		    $conditions='';
		    !empty($cate_ids)&&$conditions='AND cate_id'.db_create_in($cate_ids);
		    $articles = $_article_mod->find(array(
		        'conditions' => 'if_show=1 AND store_id=0 AND code=""'.$conditions,
		        'fields'     => 'article_id,title,cate_id,content,add_time',
		        'order'      => 'sort_order ASC',
		        'count'      => true,
		        'index_key'  => '',
		    ));//找出所有符合条件的文章
		    if($articles){
		        foreach($articles as $k=>$v){
		            if($v['content']){
		                $src='/src="\//';
		                $on='src="'.SITE_URL.'/';
		                $ins=preg_replace($src,$on,$v['content']);
		                $src1='/<img/';
		                $on1='<img style="max-width:100%;"';
		                $ins1=preg_replace($src1,$on1,$ins);
		                $articles[$k]['content']=$ins1;
		            }
		        }
		    }
		    $count = $_article_mod->getCount();
		    $return = array(
		        'statusCode' => 1,
		        'result' => array(
		            'articles' =>! empty($articles)?$articles:'',
		            'count'    =>! empty($count)?$count:0,
		            'success'  => '获取数据成功'
		        )
		    );
		    return $json->encode($return);
		} 	
		
		public function searchRecord($data){
			global $json;
			$num   = isset($data->num) ? $data->num:8;
			$records=array();
			$search_record_mod=&m('search_record');
			$records=$search_record_mod->find(array(
					'conditions'=>'1=1',
					'fields'=>'value',
					'order'=>'num DESC,sid DESC',
					'limit'=>"0,{$num}"
			));
			if($records){
				$records=array_values(i_array_column($records, 'value'));
			}
// 			var_dump($records);
			$return = array(
					'statusCode' => 1,
					'result' => array(
 							'records' =>$records?$records:array(),
 							'count'    =>count($records),
							'success'  => '获取数据成功'
					)
			);
			return $json->encode($return);
		}
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	