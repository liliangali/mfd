<?php
	/**
	 * 试衣间
	 * @author liang.li <1184820705@qq.com>
	 * @version $Id: Fitting.class.php 4291 2015-05-30 02:41:45Z gaofei $
	 * @copyright Copyright 2014 mfd.com
	 * @package app
	 */
	class Fitting
	{
		var $wdwl_url = '';
		var $error = '';
		var $token = '';
	
	  /**
	   * 构造函数
	   * @param string $username
	   *  可设置当前用户
	   * @access protected
	   * @return void
	   */
	  function __construct() {
		  
	  }
	
	  /**
	   * 设置参数
	   */
	  public function set($key, $value) {
	    $this->$key = $value;
	  }
	  
	  /**
	   * 获取参数
	   */
	  public function get($key) {
	    return isset($this->$key) ? $this->$key : NULL;
	  }
	  
	  public function index($id,$pid)
	  {
	  	global $json;
	 
	  	$dataArray = array();
	  	$gcategories = $this->_get_gcategory();
	  	$d_tree = $this->deep_tree($gcategories,$pid);	//生成嵌套格式的树形数组
// 	 dump($d_tree);
	  	$dataArray['scategorys'] = $d_tree;
	  	/* 获取分类的关联属性 */
// 	  echo $id;exit;
	  	$catinfo = $gcategories[$id];
// dump($catinfo);	  	
	  	/* 套装特殊处理数据 */
	  	$s = '';
	  	$s =  strstr($catinfo['cate_name'],"套装");
	  	
	  	$attributeMod      =& m('attribute');
	  	$conditions = db_create_in($catinfo['attrid'],'attr_id');
	  	$attr_data = $attributeMod->find(array('conditions'=>$conditions));
	  	//=====格式化=====
	  	if ($attr_data)
	  	{
	  		foreach ($attr_data as $k=>$v)
	  		{
	  			if ($v['attr_values'])
	  			{
	  				$value_array = explode("\r\n", $v['attr_values']);
	  				$attr_data[$k]['attr_values'] = $value_array;
	  			}
	  		}
	  	}
	  	
	  	$dataArray['adata']	= $attr_data;
	  	
	  	
	  	//=====商品信息=====
	  	$goodsMod = m('goods');
// 	  echo $s;exit;
	  	if ($s)
	  	{
	  		$goods_conditions .= "g.cate_id=$id and is_suit=1 ";
	  		$goods_list =$goodsMod->find(array(
	  				'fields'		=> "goods_id,price,goods_name,suitstr,image_url",
	  				'conditions'    => $goods_conditions,
	  		));
// dump($goods_list);	  	
	  		if ($goods_list)
	  		{
	  			$data = array();
	  			$sdata = array();
	  			foreach ($goods_list as $k=>$v)
	  			{
	  				$goods_r = array();
	  				$goods_r = $goodsMod->find(array(
	  						'conditions' => "goods_id IN ({$v['suitstr']})",
	  						'fields' => "goods_id,price,goods_name,cate_id",
	  				));
	  	
	  				$sdata[$k]['goods_id'] = $v['goods_id'];
	  				$sdata[$k]['price'] =$v['price'];
	  				$sdata[$k]['goods_name'] =$v['goods_name'];
	  				$sdata[$k]['alias'] =$catinfo['alias'];
	  				//面料图片 后台维护
	  				$sdata[$k]['image'] = get_domain().'/upload/images/goods/'.$v['image_url'];
	  				//                     $sdata[$k]['is_suit'] = 1;
	  				if ($goods_r)
	  				{
	  					$r = array();
	  					$a = array();
	  					foreach ($goods_r as $key=>$val)
	  					{
	  						$r[$key]['goods_id'] = $val['goods_id'];
	  						$r[$key]['alias'] =$gcategories[$val['cate_id']]['alias'];
	  						$r[$key]['goods_name'] =  $val['goods_name'];
	  						$r[$key]['price'] = $val['price'];
	  						$r[$key]['cate_id'] = $val['cate_id'];
	  						//                          $a[] = array_values($r[$key]);
	  						$a[] = $r[$key];
	  					}
	  				}
	  				$sdata[$k]['data'] = $a;
	  				$data[] = array_values($sdata[$k]);
	  			}
// 	  		dump($data);
	  			$dataArray['gdata'] = $sdata;
	  		}
	  	}
	  	else 
	  	{
	  		$goods_conditions .= "g.cate_id=$id";
	  		$goods_list = $goodsMod->find(array(
	  				'fields'		=> "goods_id,price,goods_name",
	  				'conditions'    => $goods_conditions,
	  		));
	  	
	  		if ($goods_list)
	  		{
	  			$gdata = array();
	  			foreach ($goods_list as $k=>$v)
	  			{
	  				$goods_list[$k]['alias'] = $catinfo['alias'];
	  				//array_push($goods_list[$k],$catinfo['alias']);
	  				 //$gdata[] = array_values($goods_list[$k]);
	  			}
// 	  	dump($goods_list);		
	  		
	  			$dataArray['gdata'] = $goods_list;
	  		}
	  	}
	  	
	  	return $json->encode($dataArray);
	  }
	  
	  /**
	   * 面料选择
	   */
	  public  function n($fdata,$cid)
	  {
	  		global $json;
	  		$f_data = array();
		  	$fdataStr = implode(',',$fdata);
//dump($fdataStr);
		  	$tempFdata = explode("|", $fdata);
		  	$fdataArray = array();
		  	$sdata         = array();
//var_dump($fdataStr);
		  	$gcategories = $this->_get_gcategory();
		  	$attrbute = $this->_get_attrbute();
//dump($attrbute);	  	
//dump($tempFdata);
		  	foreach ($tempFdata as $key => $value)
		  	{
		  		$jdata = array();
		  		$jdata = json_decode($value,1);
		  		$jdata['cate_name'] = $gcategories[$jdata['cid']]['cate_name'];
		  		$fdataArray[$jdata['cid']] = $jdata;
		  		$sdata[$jdata['cid']]['cid'] = $jdata['cid'];
		  		$sdata[$jdata['cid']]['cate_name'] = $jdata['cate_name'];
		  	}
// dump($fdataArray);
		  	asort($fdataArray);
		  	asort($sdata);
		  	$f_data['sdata'] = array_values($sdata);
		  	$f = current($fdataArray);
		  	if ($cid)
		  	{
		  		$pGid = $cid;
		  	}
		  	else 
		  	{
		  		$pGid = $f['cid'];
		  	}
// dump($pGid);  
			//=====获取goods_id的款式属性=====
		  	$gAttrArray = $this->_getAttrByid($fdataArray[$pGid]['gid'],'goods',1);
// dump($gAttrArray);
		  	/* 根据款式属性获取goodsids集合 */
		  	$gids = $this->_getGidsByAttr($gAttrArray,1);
// 	dump($gids);	
	
			//=====  根据商品id获取面料 =====
			if ($gids)
			{
				$goods_mod = & m('goods');
				$gidstr = db_create_in($gids, 'goods_id');
				$goodsdata = $goods_mod->find(array(
						'fields' => "material,goods_id",
						'conditions' => $gidstr
				));
			}
			if ($goodsdata)
			{
				// ===== 面料ids =====
				$fids = array();
				foreach ($goodsdata as $v)
				{
					$fids[$v['material']] = $v['material'];
				}
			
				//===== 获取面料数据 =====
				$fabric_mod = & m('fabric');
				$fidstr = db_create_in($fids, 'fabric_id');
				$fabricdata =$fabric_mod->find(array(
						'conditions' => $fidstr
				));
			}
// dump($fids);			
			/* 通过面料id获取面料属性 */
			$fAttrArray = $this->_getAttrByid($fids,'fabric',1);
// dump($fAttrArray);
			/* 根据面料的属性归类 */
			$items = array();
			foreach ($fAttrArray as $value)
			{
				$attrId = $value['attr_id'];
				unset($value['attr_id']);
				if (! isset($items[$attrId]))
				{
					$items[$attrId] = array(
							'attr_id' => $attrId,
							'attr_name' => $attrbute[$attrId]['attr_name'],
							'items' => array()
					);
				}
				$items[$attrId]['items'][] = $value['attr_value'];
			}
			$f_data['adata'] = $items;
// 	dump($items)	;

			$d_tree = $this->deep_tree($gcategories,2);	//生成嵌套格式的树形数组
// dump($d_tree);
			$f_data['scategorys'] = $d_tree;
			if ($goodsdata)
			{
				$data = array();
				foreach ($goodsdata as $k=>$v)
				{
					//                 ["60", "0.00", "产品1", "xifu"，"/upload/sss.png"]
					$goods_list[$k]['goods_id'] = $v['goods_id'];
					$goods_list[$k]['price'] =$fabricdata[$v['material']]['price'];
					$goods_list[$k]['goods_name'] =$fabricdata[$v['material']]['name'];
					$goods_list[$k]['alias'] =$fdataArray[$pGid]['alias'];
			
					//面料图片 后台维护
					$goods_list[$k]['image'] = get_domain().'/upload/images/fabric/'.$fabricdata[$v['material']]['image_url'];
					//$data[] = array_values($goods_list[$k]);
					$data[] = $goods_list[$k];
				}
			}
			$f_data['gdata'] = $data;
// dump($data);
		return $json->encode($f_data);

	  }
	  
	  
	  public function jsonData($id,$pid,$st,$sval,$fdata)
	  {
	  	global $json;
	  	$data = array('state'=>false,'msg'=>'','data'=>array());
	  	
	  	//===== 通过商品属性还是面料属性筛选=====
	  	$sType = in_array($st,array('goods','fabric')) ? $st : 'goods';
	  	
	  	
	  	$search_data = explode(",",$sval);
	  	$data['postsval'] = $search_data;
	  	//=====拼接sql=====
	  	$ssql = '';
	  	$cnt=0;
	  	
	  	//======验证前端传过来的数据是否 在attribute和 goods_attr表中存在=====
	  	if ('fabric' == $sType)
	  	{
	  		foreach ($search_data as $val)
	  		{
	  			if ($val)
	  			{
	  				$s_data = explode("-", $val);
	  				$ssql .= '(attr_id = "'.$s_data[0].'" and attr_value="'.$s_data[1].'" ) or ';
	  				$cnt++;
	  			}
	  		}
	  	
	  	}
	  	else
	  	{
	  		$attributeMod = & m('attribute');
	  		$gcategories = $this->_get_gcategory();
	  		$catinfo = $gcategories[$id];
	  		$conditions = db_create_in($catinfo['attrid'],'attr_id');
	  		$attr_data = $attributeMod->find(array('conditions'=>$conditions));
	  		foreach ($search_data as $val)
	  		{
	  			if ($val)
	  			{
	  				$s_data = explode("-", $val);
	  	
	  				if ($attr_data[$s_data[0]])
	  				{
	  					if ($attr_data[$s_data[0]]['attr_values'])
	  					{   
		  					$value_array = array();
		  					$value_array = explode("\r\n", $attr_data[$s_data[0]]['attr_values']);
		  	
		  					if (in_array($s_data[1], $value_array))
		  					{
		  						$ssql .= '(attr_id = "'.$s_data[0].'" and attr_value="'.$s_data[1].'" ) or ';
		  						$cnt++; 
		  					}
	  					}
	  				}
	  			}
	  		}
	  	}
// echo $ssql;exit;
	  		//$data['debugSql'] = $ssql;
	  		
	  		
	  		if ($cnt)
	  		{
	  			$goodsMod = m('goods');
	  			$m = $sType.'attr';
	  			$sid = $sType.'_id';
	  			$_attr_mod      =& m($m);
	  			$gdata = $_attr_mod->find(array('conditions'=>substr($ssql, 0,-3).' group by '.$sid.' having count(*)='.$cnt));
	  			if ($gdata)
	  			{
	  				foreach ($gdata as $v)
	  				{
	  					$gids[$v[$sid]] = $v[$sid];
	  				}
	  			}
// 	  	dump($gdata);
	  			$gidstr = '';
	  			$gidstr = db_create_in($gids,'goods_id');
// 	  		dump($gids);
	  			//-----------面料筛选START
	  			//--------------------------------重复代码
	  			if ('fabric' == $sType)
	  			{
	  				//如果是面料，根据面料的属性检索面料id，还得跟商品对应的款式goodsids 结合中过滤
	  				//$fdata = '{"alias":"xiku","cid":"19","gid":"143"}|{"cid":"20","gid":"33","alias":"chenyi"}|{"cid":"18","gid":"6","alias":"xifu"}';
	  				$fdataStr = $fdata;
	  				$tempFdata = explode("|", $fdataStr);
	  				$fdataArray = array();
	  				foreach ($tempFdata as $key => $value)
	  				{
	  					$jdata = array();
	  					$jdata = json_decode($value,1);
	  					$jdata['cate_name'] = $gcategories[$jdata['cid']]['cate_name'];
	  					$fdataArray[$jdata['cid']] = $jdata;
	  				}
// 	  		dump($fdata);		
	  				$gAttrArray = $this->_getAttrByid($fdataArray[$id]['gid'],'goods',1);
	  				//===== 根据款式属性获取goods_id集合=====
	  				$gidsRange = $this->_getGidsByAttr($gAttrArray,1);
	  		
	  				//$id
	  				$gidstr = db_create_in($gidsRange,'goods_id');
	  		
	  				//面料id
	  				$f = current($gids);
	  				$gidstr .= 'and material = '.$f;
// 	 echo $gidstr;exit;
	  				$fabricMod      =& m('fabric');
	  				$fabricdata = $fabricMod->get(array(
	  						'conditions'    => 'fabric_id='.$f,
	  				));
	  				//-----------面料筛选END
	  			}
	  			$goodsdata = $goodsMod->find(array(
	  					'fields'		=> "goods_id,price,goods_name",
	  					'conditions'    => $gidstr,
	  			));
// 	  	dump($goodsdata);
	  			$gdata = array();
	  			if ($goodsdata)
	  			{
	  				
	  				$fata = array();
	  				foreach ($goodsdata as $k=>$v)
	  				{
	  		
	  					if ('fabric' == $sType)
	  					{
	  						$fata[$k]['goods_id'] = $v['goods_id'];
	  						$fata[$k]['price'] =$fabricdata['price'];
	  						$fata[$k]['name'] =$fabricdata['name'];
	  						$fata[$k]['alias'] =$fdataArray[$id]['alias'];
	  						//=====面料图片 后台维护=====
	  						$fata[$k]['image'] = get_domain().'/upload/images/fabric/'.$fabricdata['image_url'];
	  						$gdata[] = $fdata;
	  						//$gdata[] = array_values($fata[$k]);
	  					}
	  					else 
	  					{
	  						$goodsdata[$k]['alias'] = $catinfo['alias'];
	  						$gdata = $goodsdata;
	  						//array_push($goodsdata[$k],$catinfo['alias']);
	  						//$gdata[] = array_values($goodsdata[$k]);
	  					}
	  		
	  				}
	  			}
	  			if ($gdata)
	  			{
	  				$data['state'] = true;
	  				$data['data'] = $gdata;
	  			}
	  		}
	  	
	  	return $json->encode($data);
	  	
	  	
	  }
	  
	  
	  /**
	   * 获取属性
	   * @return array()
	   * 'conditions'=>db_create_in($cateIds,'cate_id'),
	   */
	  public function _get_attrbute($conditions='',$cache='')
	  {
	  	$_attribute_mod      =& m('attribute');
	  	
	  	$conditions = empty($conditions) ? "1=1" : $conditions;
	  	$p_c_data = $_attribute_mod->find(array('conditions'=>$conditions));
	  	return $p_c_data;
	  	
	  	/* 获取组件类型表 - 缓存数组关联 */
	  /* 	if ($cache)
	  	{
	  		$conditions = empty($conditions) ? "1=1" : $conditions;
	  		$p_c_data = $_attribute_mod->find(array('conditions'=>$conditions));
	  		return $p_c_data;
	  	}
	  	$cache_server =& cache_server();
	  	$key = 'page_of_attribute_'.md5($conditions);
	  	$p_c_data = $cache_server->get($key);
	  	if ($p_c_data === false)
	  	{
	  		$conditions = empty($conditions) ? "1=1" : $conditions;
	  		$p_c_data = $_attribute_mod->find(array('conditions'=>$conditions));
	  		$cache_server->set($key, $p_c_data, 1800);
	  	}
	  	return $p_c_data; */
	  }
	  
	  

	  /**
	   * 根据商品id获取 款式属性
	   * @param int $id 商品或面料id
	   * @param sting $ty 类型  fabricattr | goodsattr
	   * @param int $cach 强制缓存
	   * @return array
	   * @access public
	   * @see _getAttrByGid
	   * @version 1.0.0 (2014-11-29)
	   * @author Xiao5
	   */
	  
	  public function _getAttrByid($id,$ty='goods',$cache=false)
	  {
	  	 $m = $ty.'attr';
        $_attr_mod =& m($m);
        $cache_server =& cache_server();
        $key = $ty.'_attr_'.$gid;
        $data = $cache_server->get($key);
        
        if (is_array($id))
        {
            $conditions = db_create_in($id,$ty.'_id');
        }
        else
        {
            $conditions = array('conditions'=>$ty.'_id='.$id);
        }
// var_dump( $conditions);  
        if ($data === false || $cache)
        {
            $data = $_attr_mod->find($conditions);
            $cache_server->set($key, $data, 1800);
        }
      /*   if ($ty == 'fabric')
        {
        	var_dump($data);
        } */
   
        return $data;
	  }
	  
	  /**
	   * 根据商品id获取 款式属性
	   * @param array $attr 属于性值
	   * @param int $cach 强制缓存
	   * @return array
	   * @access public
	   * @see _getAttrByGid
	   * @version 1.0.0 (2014-11-29)
	   * @author Xiao5
	   */
	  
	  public function _getGidsByAttr($attr,$cache=false)
	  {
	  	if ($attr)
	  	{
	  		/*  拼接sql */
	  		$ssql = '';
	  		$cnt=0;
	  		foreach ($attr as $val)
	  		{
	  			$ssql .= '(attr_id = "'.$val['attr_id'].'" and attr_value="'.$val['attr_value'].'" ) or ';
	  			$cnt++;
	  
	  		}
	  	}
// 	  echo $ssql;exit;
	  	$cache_server =& cache_server();
	  	$key = 'goods_attr_ids_'.md5($ssql);
	  	$gdata = $cache_server->get($key);
	  	if ($gdata === false || $cache)
	  	{
	  		$goodsattr_mod      =& m('goodsattr');
	  		$gdata =$goodsattr_mod->find(array('conditions'=>substr($ssql, 0,-3).' group by `goods_id` having count(*)='.$cnt));
	  		$cache_server->set($key, $gdata, 1800);
	  	}
	  	$gids = array();
	  	if ($gdata)
	  	{
	  		foreach ($gdata as $v)
	  		{
	  			$gids[$v['goods_id']] = $v['goods_id'];
	  		}
	  	}
	  	return $gids;
	  }
	  
	  
	  /**
	   * 获取分类数据
	   * @return array()
	   * 'conditions'=>db_create_in($cateIds,'cate_id'),
	   */
	  public function _get_gcategory($conditions='')
	  {
	  	$gcategoryMod = m('gcategory');
	  	$conditions = empty($conditions) ? "1=1" : $conditions;
	  	$p_c_data = $gcategoryMod->find(array('conditions'=>$conditions));
	  	return $p_c_data;
	  }
	  /**
	   * 构造并返回树
	   * @param  arrary 	$gcategories	数据源
	   * @return Tree
	   * @author 
	   * <code>
	   * $gcategories = $this->_get_gcategory();
	   * $tree = $this->_tree($gcategories);
	   * $a = $tree->getOptions(0);				//生成嵌套格式的树形
	   * $a = $tree->getChilds(8001);				//所有的子id
	   * $a = $tree->getParents(23);				//查找父id 逐级返回. 不包括本身
	   * </code>
	   */
	  function &_tree($gcategories)
	  {
	  	import('tree.lib');
	  	$tree = new Tree();
	  	$tree->setTree($gcategories, 'cate_id', 'parent_id', 'cate_name');
	  	return $tree;
	  }
	  
	  /**
	   * 生成嵌套格式的树形数组
	   * @param arrary 	$gcategories	数据源
	   * @param int 		$root			父节点
	   * @return array|false				array(..."children"=>array(..."children"=>array(...)))
	   * @author 
	   * <code>
	   * $gcategories = $this->_get_gcategory();
	   * $this->deep_tree($gcategories,3);
	   * </code>
	   */
	  function deep_tree($gcategories,$root=0){
	  	if(!$gcategories){
	  		return FALSE;
	  	}
	  	$pk="cate_id";
	  	$parentKey="parent_id";
	  	$childrenKey="children";
	  	$tree=array();//最终数组
	  	$refer=array();//存储主键与数组单元的引用关系
	  	//遍历
	  	foreach($gcategories as $k=>$v){
	  		if(!isset($v[$pk]) || !isset($v[$parentKey]) || isset($v[$childrenKey])){
	  			unset($gcategories[$k]);
	  			continue;
	  		}
	  		$refer[$v[$pk]]=&$gcategories[$k];//为每个数组成员建立引用关系
	  	}
	  	//遍历子节点
	  	foreach($gcategories as $k=>$v){
	  		if($v[$parentKey]==$root){//根分类直接添加引用到tree中
	  			$tree[$gcategories[$k]['cate_id']]=&$gcategories[$k];
	  		}else{
	  			if(isset($refer[$v[$parentKey]])){
	  				$parent=&$refer[$v[$parentKey]];//获取父分类的引用
	  				$parent[$childrenKey][$gcategories[$k]['cate_id']]=&$gcategories[$k];//在父分类的children中再添加一个引用成员
	  			}
	  		}
	  	}
	  	return $tree;
	  }
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	
	  
	
	}

