<?php
namespace Cyteam\Comment;
class Comment
{
    var $_detailimpress_mod;
    var $_detailcomment_mod;
    var $_ordergoods_mod;
    var $_fbcategory_mod;
    function __construct($param = []){

    	$this->_detailimpress_mod =m('detail_impression');
    	$this->_detailcomment_mod=m('detail_comment');
    	$this->_ordergoods_mod=m('ordergoods');
    	$this->_fbcategory_mod=m('fbcategory');
    }
	    
    /**
    *-----------------------------------------------------------
    *获取评论
    *-----------------------------------------------------------
    *@access public
    *@param $user_id 用户id  $conditions条件 $page分页
    *@author shaozz
    *@date 2017-4-8
    *@version 1.0
    *@return 
    */
    function get_comment($user_id,$conditions,$page,$from='pc') 
    {
    	/*if($user_id){
            $conditions.=" AND order_alias.user_id = {$user_id}";
        }*/
	
        $sql="SElECT order_goods.rec_id,order_goods.dog_name,order_goods.dog_date,order_goods.dog_desc,order_goods.style,order_goods.weight,order_goods.run_time,order_goods.time_id,order_goods.body_condition,order_goods.dog_nums,order_goods.quantity as quantity,order_goods.params as params,order_goods.price as price,order_goods.goods_id,order_goods.goods_name,order_goods.comment,order_goods.goods_image,order_goods.type,order_goods.cloth,m.avatar,m.user_name,comments.* FROM cf_order_goods order_goods LEFT JOIN cf_detail_comments comments ON order_goods.rec_id=comments.rec_id LEFT JOIN cf_member m ON comments.member_id=m.user_id WHERE 1=1 {$conditions} ORDER BY comments.addtime DESC LIMIT {$page['limit']} ";
  
	  $comment_list=$this->_ordergoods_mod->getAll($sql);

        if(!empty($comment_list))
        {
	        foreach ($comment_list as $key=>$val)
	        {
	      		$comment_list[$key]['add_time'] = date("Y-m-d H:i:s",$val['addtime']);
	      		
		      		     //评价晒图
		      		     $comsql="SELECT * FROM cf_comment_img WHERE comment_id='{$val['id']}' order by id asc";
		      		     $commentimgs=$this->_ordergoods_mod->getAll($comsql);
		      		     if($commentimgs){
		      		         $comment_list[$key]['commentimgs'] = $commentimgs;
							 $comment_list[$key]['imgscount'] = count($commentimgs);
		      		     }
		      		 	if($comment_list[$key]['impression']){
		      		 		$comment_list[$key]['impression'] = explode(";", $comment_list[$key]['impression']);
		      		 	}
		      		 	
		      		 
		      		//$comment_list[$key]['pl_info'] = $c_l;
                    $avatar = $val['avatar'];
                    if(!$avatar)
                    {
                        $avatar = '/avatar/noavatar_big.gif';
                    }
                    if(strpos($avatar,"http") === false)
                    {
                        $comment_list[$key]['avatar'] = PC_URL.$avatar;
                    }
                   




	      		
	        }
        }
       
        return $comment_list;
    }
    
    /**
    *-----------------------------------------------------------
    *获取订单评论
    *-----------------------------------------------------------
    *@access public
    *@param $user_id 用户id  $conditions条件 $page分页
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月28日
    *@version 1.0
    *@return 
    */
    function get_order_comment($user_id,$conditions,$page,$from='pc') 
    {
    	if($user_id){
            $conditions.=" AND order_alias.user_id = {$user_id}";
        }
	
        $sql="SElECT order_goods.rec_id,order_goods.dog_name,order_goods.dog_date,order_goods.dog_desc,order_goods.style,order_goods.weight,order_goods.run_time,order_goods.time_id,order_goods.body_condition,order_goods.dog_nums,order_goods.quantity as quantity,order_goods.params as params,order_goods.price as price,order_goods.goods_id,order_goods.goods_name,order_goods.comment,order_goods.goods_image,order_goods.type,order_goods.cloth,order_alias.order_sn,order_alias.user_id,order_alias.order_id,order_alias.status,order_alias.add_time,m.user_name,m.avatar FROM cf_order_goods order_goods LEFT JOIN cf_order order_alias ON order_goods.order_id=order_alias.order_id LEFT JOIN cf_member m ON order_alias.user_id=m.user_id WHERE 1=1 {$conditions} ORDER BY order_alias.pay_time DESC LIMIT {$page['limit']} ";
        $comment_list=$this->_ordergoods_mod->getAll($sql);
	    /*$comment_list =  $this->_ordergoods_mod->find(array(
	          'conditions' =>"1=1 ".$conditions,
	          'join' => "belongs_to_order",
	          'limit' => $page['limit'],
	          'fields' => "order_goods.rec_id,order_goods.quantity as quantity,order_goods.params as params,order_goods.price as price
	    		,order_goods.goods_id,order_goods.goods_name,order_goods.comment,order_goods.goods_image,order_goods.type,
	    		order_alias.order_sn,order_alias.user_id,order_alias.order_id,order_alias.status,order_alias.add_time",
// 	          'count'       => true,
	          'order' => "order_alias.pay_time DESC",
	      ));*/
//	    echo '<pre>';print_r($comment_list);exit;
	    
        if(!empty($comment_list))
        {
	        foreach ($comment_list as $key=>$val)
	        {
	      		$comment_list[$key]['add_time'] = date("Y-m-d H:i:s",$val['add_time']);
	      		if($comment_list[$key]['comment']==1)
	      		{
		      		 $c_l  = $this->_detailcomment_mod->find(array(
		      		'conditions'=>"rec_id ='{$val['rec_id']}'  AND member_id ='{$val['user_id']}' AND order_id='{$val['order_id']}'",
		      		 'fields'   =>'star,content,impression,id as commentid',
		      		 'order'         =>'id DESC',
			        'index_key'=>'',		
		      		));
                     if(!$c_l){
                        unset($comment_list[$key]);
                        continue;
                     }
		      		 foreach ($c_l as $kk=>$vv)
		      		 {
		      		     //评价晒图
		      		     $comsql="SELECT * FROM cf_comment_img WHERE comment_id='{$vv['commentid']}' order by id asc";
		      		     $commentimgs=$this->_ordergoods_mod->getAll($comsql);
		      		     if($commentimgs){
		      		         $c_l[$kk]['commentimgs'] = $commentimgs;
		      		     }
		      		 	if($c_l[$kk]['impression']){
		      		 		$c_l[$kk]['impression'] = explode(";", $c_l[$kk]['impression']);
		      		 	}
		      		 	
		      		 }
		      		$comment_list[$key]['pl_info'] = $c_l;
                    $avatar = $val['avatar'];
                    if(!$avatar)
                    {
                        $avatar = '/avatar/noavatar_big.gif';
                    }
                    if(strpos($avatar,"http") === false)
                    {
                        $comment_list[$key]['avatar'] = PC_URL.$avatar;
                    }
                    if($comment_list[$key]['user_name'] != "麦富迪初体验")
                    {
                        if(preg_match("/1[34587]{1}\d{9}$/",$comment_list[$key]['user_name']))
                        {
                            $comment_list[$key]['user_name'] = substr_replace($comment_list[$key]['user_name'],'****',3,4);
                        }
                        else
                        {
                            if(count($comment_list[$key]['user_name']) > 2)
                            {
                                $comment_list[$key]['user_name'] = $this->cut_str($comment_list[$key]['user_name'],1,0)."*".$this->cut_str($comment_list[$key]['user_name'],1,-1);
                            }
                            else
                            {
                                $comment_list[$key]['user_name'] = $this->cut_str($comment_list[$key]['user_name'],1,0)."*";
                            }

                        }

                    }




	      		}
	        }
        }
        
        return $comment_list;
    }

    function cut_str($string, $sublen, $start = 0, $code = 'UTF-8')
    {
        if($code == 'UTF-8')
        {
            $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
            preg_match_all($pa, $string, $t_string);
            if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen));
            return join('', array_slice($t_string[0], $start, $sublen));
        }
        else
        {
            $start = $start*2;
            $sublen = $sublen*2;
            $strlen = strlen($string);
            $tmpstr = '';
            for($i=0; $i< $strlen; $i++)
            {
                if($i>=$start && $i< ($start+$sublen))
                {
                    if(ord(substr($string, $i, 1))>129)
                    {
                        $tmpstr.= substr($string, $i, 2);
                    }
                    else
                    {
                        $tmpstr.= substr($string, $i, 1);
                    }
                }
                if(ord(substr($string, $i, 1))>129) $i++;
            }
            //if(strlen($tmpstr)< $strlen ) $tmpstr.= "...";
            return $tmpstr;
        }
    }

    
    /**
    *-----------------------------------------------------------
    *获取评论印象
    *-----------------------------------------------------------
    *@access public
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月30日
    *@version 1.0
    *@return	array 
    */
    function get_impression_arr(){
    	$impression_arr = include_once ROOT_PATH.'/includes/impression.arrayfile.php';
    	foreach ($impression_arr as $k=>$v)
    	{
    		$impress[] = array(
    				'impress_id' => $k,
    				'impress_name'    => $v,
    		);
    	
    	}
    	return $impress;
    }
   
    /**
    *-----------------------------------------------------------
    * 添加评论
    *-----------------------------------------------------------
    *@access public
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月30日
    *@version 1.0
    *@return 
    */
    function add_comments($id,$user,$type,$from,$star,$content,$impress,$is_hide = '0',$gallery=''){
    	$custom_info = $this->_ordergoods_mod->find(array(
    			'conditions'=>"rec_id={$id}",
    			'fields'    =>"*",
    			'index_key'=>'',
    	));
    	$rec_info = end($custom_info);
    	$params=json_decode($rec_info['params'],true);
//     	echo '<pre>';
//     	print_r($params);
//     	die();
    	$comment_id=$params['oGoods']['goods_id'];
    	
    	if($type=='fdiy'){
    		$comment_id=0;
			//获得犬种
			if($params['21']){
				 $quans_id=explode(':',$params['21']);
    		    $quan_id=$quans_id[1];
			}
			
    		//获得生长阶段 
    		if($params['34']){
    		    $sz=explode(':',$params['34']);
    		    $jd_id=$sz[1];
    		    	
    		}else{
    		    $jd_id='0';
    		}
    		
    	}
    	    	
    	//有图无图判断
    	if($gallery){
    	    $is_img='1';
    	}else{
    	    $is_img='0';
    	}
    	$arr = array(
    			'member_id'	      => $user['user_id'],
    			'comment_id'      => $comment_id,//被评价商品id
    			'content'         => htmlspecialchars($content),
    			'addtime'         => gmtime(),
				'quan_id'         => $quan_id,
    	        'is_img'               =>$is_img,
    	        'jd_id'           =>$jd_id,
    			'status'          => 0,
    			'nickname'        => $user['nickname'],
    			'cate'            => $type,
    			'star'      => $star,
    			'come_from'       => $from,
    			'order_id'        => $rec_info['order_id'],
    			'hide_name'       => $is_hide,
    			'rec_id'          => $id,//评价订单商品id
    			'impression'      => $impress,
    	);
    	$is_comment = $this->_detailcomment_mod->add($arr);
    	// 印象分表的数据 组装
    	$impressAttr = explode(";", $impress);
    	if($impress && $comment_id)
    	{
    		$impression_temp=array();
    		foreach ($impressAttr as $key=>$value)
    		{
    			$impression_data = array(
    					'member_id'        =>$user['user_id'],
    					'comment_id'      =>$rec_info['goods_id'],
    					'addtime'         => time(),
    					'order_id'        =>$rec_info['order_id'],
    					'c_id'            =>$is_comment,
    					'impression'      =>$value,
    			);
    	
    			$impression_temp[] = $impression_data;
    		}
    	
    		$this->_detailimpress_mod->add($impression_temp);
    	}
    	 
    	if($is_comment)
    	{
    		$this->_ordergoods_mod->edit("rec_id ='{$id}'",array('comment'=>1));
    		return $is_comment;
    		 
    	}else
    	{
    		return false;
    	}
    }
    /**
    *-----------------------------------------------------------
    *重构商品属性
    *-----------------------------------------------------------
    *@access public
    *@author cyrus <2621270755@qq.com>
    *@date 2016年6月8日
    *@version 1.0
    *@return 
    */
    function getGoodParam($type,$params){
    	$spe_name=array();
    	if ($params)
    	{
    		$params= json_decode($params,true);
    	
    		if ($type== 'custom')
    		{
    			if ($params['oProducts']['spec_info'])
    			{
    				$spe = [];
    				$spec_info_arr = explode("、", $params['oProducts']['spec_info']);
    				foreach ((array)$spec_info_arr as $key1 => $value1)
    				{
    					$specInfo = explode("：", $value1);
    					$spec['p_name'] = $specInfo[0];
    					$spec['s_name'] = $specInfo[1];
    					$spe[] = $spec;
    				}
    			}
    			$spe_name= $spe;
    		}
    		else
    		{
    			//                   return $value;
    			$pstr =  $this->_format_params($params);
    			$spe = [];
    			$s_name = "";
    			$spec_info_arr = explode("、", $pstr);
    			
    			//                   return $spec_info_arr;
    			foreach ((array)$spec_info_arr as $key1 => $value1)
    			{
    				$specInfo = explode(":", $value1);
    				
    				//                     return $specInfo;
    				$spec['p_name'] = $specInfo[0];
    				$spec['s_name'] = $specInfo[1];
    				$spe[] = $spec;
    				$s_name .= $specInfo[0].':'.$specInfo[1]."/";
    			}
    			
    			//                     return $s_name;
    			$spe_name = trim($s_name,"/");
    		
    		}
    		
    		// $orderGoodsList[$key]['params'] = json_decode($value['params'],true);
    		// $orderGoodsList[$key]['params']['spec_desc'] = json_decode($value['params']['spec_desc'],true);quantity
    	}
    	return $spe_name;

    }
    
    
    public function _format_params($pData)
    {
     
    	if (!$pData || !is_array($pData))
    	{
    		return ;
    	}
    	$data = $this->_fd($pData);
    	
    	if ($data['ids'])
    	{
    	   foreach($data['ids'] as $key=>$val){
    	       
    	       if(!is_numeric($val)){
    	           unset($data['ids'][$key]);
    	           $numeric=explode(',', $val);
    	          
    	           if($numeric){
    	               foreach($numeric as $key1=>$val1){
    	                   $data['ids'][]=$val1;
    	               }
    	           }
    	        
    	       }
    	    }
    	 
    		$fbcategorys = $this->_fbcategory_mod->find(array('conditions'=>db_create_in($data['ids'],'cate_id') ,'index_key'=>'cate_id'));
    	
    	}
    
    	unset($data);
    	$data = $this->_fd($pData,$fbcategorys);
    
    	// 		return $data;
    	return $data['str'];
    }
    protected function _fd($pData,$data=array())
    {
    	$ids = [];
    	$str = '';
    	
    	
    	foreach ((array)$pData as $item)
    	{
    	   
    		$rows = $r = [];
    		$rows = explode(":", $item);
    		
    		if ($data && $data[$rows[0]])
    		{
    		    
    			$str .= $data[$rows[0]]['cate_name'].":";
    		}
    		// 		return $str;
    		$ids[] =$rows[0];

    		    $rows[1] = str_replace('"', '',$rows[1]);

    		$r = explode("|", $rows[1]);
    		if($data && !is_numeric($r['0'])){
    		   
    		    $rss=explode(',', $r[0]);
    		   
    		    foreach ((array)$rss as $val)
    		    {
    		       
    		        $ids[] = $val;
    		       
    		        if ($data && $data[$val])
    		        {
    		            $str .= $data[$val]['cate_name'].'-';
    		        }
    		    }
    	
    		}else{
    		    foreach ((array)$r as $val)
    		    {
    		        $ids[] = $val;
    		        if ($data && $data[$val])
    		        {
    		        				$str .= $data[$val]['cate_name'].'、';
    		        }
    		    }
    		}
    		//var_dump($r);exit;
    	
    	}
   
    	return ['ids'=>$ids,'str'=>rtrim($str,'、')];
    }
}