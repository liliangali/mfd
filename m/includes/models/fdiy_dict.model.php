<?php
/**
 *  面料diy工艺表
 *
 * 主要实现工艺存储
 * --------------------------------------------------------
 * @author       小五
 * $Id: fdiy_dict.model.php 14067 2016-01-31 00:45:20Z lil $
 * $Date: 2016-01-31 08:45:20 +0800 (Sun, 31 Jan 2016) $
 * --------------------------------------------------------
 */
class Fdiy_dictModel extends BaseModel
{
    var $table  = 'fdiy_dict';
    var $prikey = 'id';
    var $_name  = 'fdict';
	var $_category = array (
			'0003' => '男西服', 
			'0004'=>'男西裤',
			'0005'=>'男马夹',
			'0006'=>'男衬衣',
			'0007'=>'男大衣',
			'0011'=>'女西装',
			'0012'=>'女西裤',
			'0016'=>'女衬衣',
			'0017'=>'男短裤',
			'0018'=>'立领西服',
			'0021'=>'女大衣'
	);
	
	/* 品类号(1西服 2西裤 3衬衣 4大衣 5马甲 10套装2pcs 11套装3pcs 7立领西服) */
	var $_categoryZ = array (
			'0001' => 10,
			'0002' => 11,
			'0003' => 1,
			'0018' => 7,
			'0004'=>2,
			'0005'=>5,
			'0006'=>3,
			'0007'=>4
	);
    var $init = array(
             /*
             "0001" => array(
                    'name'  => "套装(2pcs)",
                    'items' => array(
                        "0003"     => array("fabric" =>'8001'),
                        '0004'     => array("fabric" =>'8001'),
                     ),
              ),
             "0002" => array(
                 'name'  => "套装(3pcs)",
                 'items' => array(
                     "0003"     => array("fabric" =>'8001'),
                     '0004'     => array("fabric" =>'8001'),
                     '0005'     => array("fabric" =>'8001'),
                 ),
             ),
             */
             "0003" => array(
                  "name"  => "男西服",  
                  'mldh' => "1.75" ,
                  'lldh' => "0", 
                   "ll" => "313",
                   'dict' => array(
                        array(
                             "name" => "缝制工艺 ",
                             "list" => array(
                                  array(
                                      "name"  => "机器缝制",
                                      'price' => "360",
                                  ),array(
                                      "name"  => "半手工缝制",
                                      'price' => "1260",
                                  ),array(
                                      "name"  => "全手工缝制",
                                      "price" => "1700"
                                  ),
                              )      
                        ),
                        array(
                             "name" => "衬工艺&nbsp; ",
                              "list"  => array(
                                   array(
                                       "name" => "粘合衬工艺",
                                       'price' => "160",
                                   ),
                                   array(
                                       "name" => "半麻衬工艺",
                                       "price" => "500"
                                   ),
                                   array(
                                       "name"  => "全麻衬工艺",
                                       "price" => "700",
                                   ),
                              )
                        ),
                    ),
                   "items" => array(
                     "0003"     => array("design" =>'24', "deep" => "298"),
                  )
             ),
             
             "0004" => array(
                 "name"  => "男西裤",
                 'mldh' => "1.15" ,
                 'lldh' => "0", 
                 "dict" => array(
                     array(
                         "name" => "缝制工艺",
                         "list"  => array(
                             array(
                                 "name" => "机器缝制",
                                 'price' => "260",
                             ),
                             array(
                                 "name" => "手工工艺",
                                 "price" => "860"
                             ),
                         )
                     ),
                  ),
                 "items" => array(
                     "0004"     => array("design" =>'2021', "deep" => "2157"),
                 )
             ),
             "0017" => array(
                 "name"  => "男短裤",
                 'mldh' => "0.85" ,
                 'lldh' => "0",
                 "dict" => array(
                     array(
                         "name" => "缝制工艺",
                         "list"  => array(
                             array(
                                 "name" => "机器缝制",
                                 'price' => "260",
                             ),
                             array(
                                 "name" => "手工工艺",
                                 "price" => "860"
                             ),
                         )
                     ),
                 ),
                 "items" => array(
                     "0004"     => array("design" =>'2021', "deep" => "2157"),
                 )
             ),
             "0005" => array(
                 "name"  => "男马甲",
                 'mldh' => "0.7" ,
                 'lldh' => "0", 
                 "dict" => array(
                     array(
                         "name" => "缝制工艺",
                         "list"  => array(
                             array(
                                 "name" => "机器缝制",
                                 'price' => "260",
                             ),
                             array(
                                 "name" => "手工工艺",
                                 "price" => "600"
                             ),
                         )
                     ),
                 ),
                 "items" => array(
                     "0005"     => array("design" =>'4016', "deep" => "4075"),
                 )
             ),
             
             "0006" => array(
                 "name"  => "男衬衣",
                 'mldh' => "1.49" ,
                 'lldh' => "0", 
                 "dict" => array(
                     array(
                         "name" => "缝制工艺",
                         "list"  => array(
                             array(
                                 "name" => "机器缝制",
                                 'price' => "180",
                             ),
                             array(
                                 "name" => "手工工艺",
                                 "price" => "480"
                             ),
                         )
                     ),
                 ),
                 "items" => array(
                     "0006"     => array("design" =>'3016', "deep" => "3184"),
                 )
             ),
             
             "0007" => array(
                 "name"  => "男大衣",
                 'mldh' => "2.3" ,
                 'lldh' => "0", 
                 'll'   => "6291",
                 "dict" => array(
                     array(
                         "name" => "缝制工艺",
                         "list"  => array(
                             array(
                                 "name" => "机器缝制",
                                 'price' => "1260",
                             ),
                             array(
                                 "name" => "手工工艺",
                                 "price" => "1760"
                             ),
                         )
                     ),
                 ),
                 "items" => array(
                     "0007"     => array("design" =>'6007', 'deep' => "298"),
                 )
             ),
             
             //-------------------------------------------女

             "0011" => array(
                 "name"  => "女西装",
                 'mldh' => "1.75" ,
                 'lldh' => "0",
                 'll'   => "95272",
                   'dict' => array(
                        array(
                             "name" => "缝制工艺 ",
                             "list" => array(
                                  array(
                                      "name"  => "机器缝制",
                                      'price' => "360",
                                  ),array(
                                      "name"  => "半手工缝制",
                                      'price' => "1260",
                                  ),array(
                                      "name"  => "全手工缝制",
                                      "price" => "1700"
                                  ),
                              )      
                        ),
                        array(
                             "name" => "衬工艺&nbsp; ",
                              "list"  => array(
                                   array(
                                       "name" => "粘合衬工艺",
                                       'price' => "160",
                                   ),
                                   array(
                                       "name" => "半麻衬工艺",
                                       "price" => "500"
                                   ),
                                   array(
                                       "name"  => "全麻衬工艺",
                                       "price" => "700",
                                   ),
                              )
                        ),
                 ),
                 "items" => array(
                    
                 )
             ),
             
             
             
             "0012" => array(
                 "name"  => "女西裤",
                 'mldh' => "1.15" ,
                 'lldh' => "0",
                 'll'   => "98201",
                 "dict" => array(
                     array(
                         "name" => "缝制工艺",
                         "list"  => array(
                             array(
                                 "name" => "机器缝制",
                                 'price' => "260",
                             ),
                             array(
                                 "name" => "手工工艺",
                                 "price" => "860"
                             ),
                         )
                     ),
                 ),
                 "items" => array(
                     
                 )
             ),
             
             
             "0016" => array(
                 "name"  => "女衬衣",
                 'mldh' => "1.49" ,
                 'lldh' => "0",
                 'll'   => "0",
                 "dict" => array(
                     array(
                         "name" => "缝制工艺",
                         "list"  => array(
                             array(
                                 "name" => "机器缝制",
                                 'price' => "180",
                             ),
                             array(
                                 "name" => "手工工艺",
                                 "price" => "480"
                             ),
                         )
                     ),
                 ),
                 "items" => array(
                    
                 )
             ),
			"0018" => array(
					"name"  => "立领西服",
					'mldh' => "1.49" ,
					'lldh' => "0",
					'll'   => "0",
					"dict" => array(
							array(
									"name" => "缝制工艺",
									"list"  => array(
											array(
													"name" => "机器缝制",
													'price' => "180",
											),
											array(
													"name" => "手工工艺",
													"price" => "480"
											),
									)
							),
					),
					"items" => array(

					)
			),
             
             "0021" => array(
                 "name"  => "女大衣",
                 'mldh' => "2.3" ,
                 'lldh' => "0",
                 'll'   => "0",
                 "dict" => array(
                     array(
                         "name" => "缝制工艺",
                         "list"  => array(
                             array(
                                 "name" => "机器缝制",
                                 'price' => "1260",
                             ),
                             array(
                                 "name" => "手工工艺",
                                 "price" => "1760"
                             ),
                         )
                     ),
                 ),
                 "items" => array(
             
                 )
             ),
             
         );

    /**
     * 获取工艺信息
     * @return array()
     * 'conditions'=>db_create_in($cateIds,'cate_id'),
     * $conditions .= " AND parent_id = '$parent_id'";
     */
    public  function _get_dict($conditions='',$cache=''){
    	$res = $p_c_data = [];
    	if ($cache)
    	{
    		$conditions = empty($conditions) ? " is_sale = 1 and `ecode` IS NOT NULL " : $conditions;
    		$res = $this->find(array('conditions'=>$conditions,'fields'=> "id,ecode,name,small_img"));
    		return $res;
    	}
    
    	$cache_server =& cache_server();
    	$key = 'page_of_fdiy_dict_'.md5($conditions);
    	$p_c_data = $cache_server->get($key);
    	if ($p_c_data === false){
    		$conditions = empty($conditions) ? "  is_sale = 1 and `ecode` IS NOT NULL " : $conditions;
    		$res = $this->find(array('conditions'=>$conditions,'fields'=> "id,ecode,name,small_img"));
    		$cache_server->set($key, $res, 1800);
    		return $res;
    	}
    	return $p_c_data;
    }
    
    /**
     * 格式化数据 模版页面 直接对应数据
     * @param  $data        数据源
     * @param  $cache      不读取缓存的
     * @return  array
     */
    public function _format_ecode($data,$cache=''){
    	$res =[];
    	if (!$data){
    		return $res;
    	}
    	if ($cache)
    	{
    		foreach ($data as $v){
    			$res[$v['ecode']] = $v['name'];
    		}
    		return $res;
    	}
    	$cache_server =& cache_server();
    	$key = 'page_of_fdiy_format_ecode_'.md5(json_encode($data));
    	$res = $cache_server->get($key);
    	if ($res === false || empty($res)){
    		
    		foreach ($data as $v){
    			$res[$v['ecode']] = $v['name'];
    		}
    		$cache_server->set($key, $res, 1800);
    	}
    	return $res;
    }
    
    /**
     * 获取面料详细信息 属性以及内容 涉及到3张表
     * @param  $code    面料code
     * @param  $cid       品类id涉及到价格计算
     */
    public function _get_finfo($fcode,$cid){
    	$res = ['err'=>1,'msg'=>'请选择面料!'];
    	if (!$fcode){
    		return $res;
    	}
    	/* 面料详情 */
    	$_mod_fabric = &m("fabric_info");
    	$finfo = $_mod_fabric->get("fabric_sn ='".$fcode."'");
    	if (!$finfo){
    		return $res;
    	}
    	 
    	/* 面料属性 */
    	$_mod_fabric_ra = &m("fabric_rel_attr");
    	$_mod_fabric_biute = &m("fabric_attribute");
    	$flist = $_mod_fabric_ra->find(array(
    			'conditions' => "fabric_id = '".$finfo['fabric_id']."'"
    	));
    	 
    	$arr_ids = i_array_column($flist, 'attr_id');
    	$falist = $_mod_fabric_biute->find(array(
    			'conditions' => db_create_in($arr_ids,'attr_id').' and attr_name = "面料颜色"'
    	));
    	$a_n = [];
    	/* 考虑后期多个属性值循环 */
    	foreach ((array)$flist as $fk=>$fv){
    		if (isset($falist[$fv['attr_id']])){
    			//     			$a_n[] =$falist[$fv['attr_id']]['alias'].'：'.$fv['attr_value'];
    			$a_n[] ='颜色：'.$fv['attr_value'];
    		}
    	}
    	/* 前端已固定显示内容 */
    	foreach (['material'=>'成分','huaxing'=>'花型','shazhi'=>'纱支'] as $key=>$val){
    		if (isset($finfo[$key])&& !empty($finfo[$key])){
    			$a_n[] =$val.'：'.$finfo[$key];
    		}
    	}
    	/* 面料价格 */
    	$finfo['price'] = '0.00';
    	$_mod_fabricp = &m("fabric_price");
    	
    	/* 无奈又一套 品类id 对应  来源 后台：面料信息管理*/
    	$fprice = $_mod_fabricp->get("fabric_id ='".$finfo['fabric_id']."' and category='".$this->_categoryZ[$cid]."'");
    	$finfo['c_id'] = $this->_categoryZ[$cid];
    	$finfo['a_n'] = $a_n;
		$finfo['cid'] = $cid;
		$finfo['fcode'] = $fcode;
    	$finfo['price'] = $fprice['price'];
    	return $finfo;
    }
    
    /**
     * 根据品类id 倒推 所有面料 只能根据有效价格去检索
     * @param  $cid    品类id 对应工厂 "0003|0004"
     */
    public function _get_fabrics($cid){
    	$res = ['err'=>1,'msg'=>'请选择品类!'];
    	if (!$cid){
    		return $res;
    	}
    	$_mod_fabricp = &m("fabric_price");
    	 
    	/* 无奈又一套 品类id 对应  来源 后台：面料信息管理*/
    	$fprices = $_mod_fabricp->find("category='".$this->_categoryZ[$cid]."'");
    	$fids = i_array_column($fprices, 'fabric_id');
    	if (!is_array($fids)){
    		return $res;
    	}
    	$_mod_fabric = &m("fabric_info");
    	$_list = $_mod_fabric->findAll(array(
    			'conditions' => db_create_in($fids,'fabric_id').' and is_sale = 1',
    			'fields'=> "fabric_sn as ecode,start_stock as stock"
    	));
    	return $_list;
    	
    }

}

?>