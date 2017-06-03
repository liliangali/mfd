<?php
/**
 *  基本款类接口
 *	@author v5
 */
class CustomMobile extends Object
{	
    var $_options = null;
	var $_customs_mod;
	var $_customsparts_mod;
	var $_customsuhistory_mod;
	var $_parttype_mod;
	var $_parts_mod;
	var $_partattr_mod;
	var $_gcategory_mod;
	var $_dis_mod;
	var $_res = array();
	var $_arrangement = array();
	var $_nodes_found = array();
	var $_nodes_found_val = array();
    function __construct($options = null)
    {
    	$this->_dis_mod            =& m("dissertation");
		$this->_customs_mod      	=& m('customs');
		$this->_customsparts_mod 	=& m('customsparts');
		$this->_customsuhistory_mod 	=& m('customsuserhistory');
		$this->_parttype_mod     	=& m('parttype');
		$this->_parts_mod        	=& m('part');
		$this->_partattr_mod       =& m('partattr');
		$this->_gcategory_mod      =& m('gcategory');
        $this->Custom($options);
    }
    function Custom($options = null)
    {
    	
        $this->_options = $options;
    }

    //-----------------------------------供给其它模块基本款的方法----------------------------------------------------------
    /**
     * 基本款详细页面记录访问信息
     * 
     * 记录用户访问基本款的信息，已实现推荐基本款、浏览历史.
     * 
     * @param array 数据  cid：基本款id	pid：组件父id	child：组件子id	uid：用户id
     * @param int 	$uid
     */
    function add_history($data,$uid=NULL){
    	$uid = empty($uid) ? $_SESSION['user_info']['user_id'] : $uid;
    	if (!$uid) return ;
    	$data['uid'] = $uid;
    	$data['ctime'] = gmtime();
    	$this->_customsuhistory_mod->add($data);
    }
    /**
     * 获取浏览历史
     * 
     * 
     * 根绝日志（每次用户点击基本款详细页面，都会把基本款的id以及父/子id记录）
     * 分组统计父、子id
     * 
     * @param int $num
     * @param int $uid
     * @example $cs->get_history();
     */
    function get_history($num = 6,$uid=NULL){
    	
    	return $this->_get_history(array('num'=>$num,'uid'=>$uid));
    	
    }
    
    /**
     * 获取推荐的基本款信息
     * 
     * @param int $num
     * @param int $uid
     * @param int|array $excludeid
     * @example $cs->get_recomm_costom(6,'',array(18));
     */
    function get_recomm_costom($num,$uid=NULL,$excludeid=NULL){
    	return $this->_get_history(array('num'=>$num,'uid'=>$uid,'excludeid'=>$excludeid));
    }
    
	/**
	 * 基本款Flash数据格式
	 * 
	 * 根绝基本款id，返回基本款的整体数据结构
	 * 
	 * @param int $id	基本款id
	 */
    function get_basis_info($id,$rtype='json') {
    	if (!$id) {
    		ajaxReturn('-1','id empty');
    	}
    
    	/* 获取基本款数据 */
    	$customs=$this->_customs_mod->get($id);
    
    	if (!$customs) {
    		ajaxReturn('-1','There is no data');
    	}
    
    	/* 文件缓存 */
    	$cache_server =& cache_server();
    	$key = 'page_of_custom_mobile_' . $id;
    	/* 测试暂时不开启缓存 */
    	$data = false;
    	$cached = true;
    	if ($data === false)
    	{
    		$cached = false;
    		 
    		/* 组件关联信息 */
    		$arr_slc=$this->_customsparts_mod->find(array('conditions'=>'cst_id='.$id));
    		
    		$cids = array();				//所有组件的fid集合
    		$defdata = array();				//所有默认组件的集合 用于获取默认图片
    		$conditions = '';
    		if ($arr_slc){
    			foreach ($arr_slc as $v){
    				$cids[] = $v['pt_cate'];
    				$cids[] = $v['parent_cate'];
    				$cids[] = $v['top_cate'];
    				if ($v['is_dft']){
    					$defdata[] = $v;
    				}
    			}
    			
    		}
    		
    		$data['data'] = $this->_format_data($arr_slc);
    		
    		print_r($data);exit();
    		
    		$data['customs'] = $customs;
    		$data['fabric'] = Constants::$fabricsParent;						//面料顶级id
    		$data['design'] =  Constants::$designParent;						//深度设计5大类的顶级id
    		$data['style'] =  Constants::$graphParent;							//款式父id 用于区分线图
    		$data['material'] =  Constants::$materialParent;					//里料父id
    		$data['size'] =  Constants::$sizelParent[$customs['cst_cate']];		//尺寸
    		$data['process'] =  $data['data']['process'];						//工艺
    		$data['hiddendata'] =  $data['data']['hiddendata'];					//隐藏
    		$data['buttons'] =  Constants::$buttonsParent;						//扣子、联动因素获取别名
    		$data['graphruleout'] = Constants::$graphRuleoutParent;				//工艺下不纳入出图规则计算
    		$data['more_classes'] = Constants::$designShowParent;				//多级拆分-深度设计-撞色设计
    		//获取分类下的工艺分类
    		$cates = $this->_customs_mod->getCate();
    		//面料单耗、里料单耗
    		$data['consumption'] = array('fabric_m'=>$cates[$customs['cst_cate']]['fabric_m'],'lining_m'=>$cates[$customs['cst_cate']]['lining_m']);
    		unset($data['data']['process']);
    		unset($data['data']['hiddendata']);

    		if ($data['data']['conditions']){
    			
    			$conditions = db_create_in(array_merge($cids,$data['data']['conditions']),'cate_id');
    			
    			
    		}else{
    			$conditions = db_create_in($cids,'cate_id');
    		}

    		unset($data['data']['conditions']);
    		$data['category'] =  $this->_get_gcategory($conditions);
    		
    		
    		
    		/* 图片规则 模拟-start */
    		
    		$cross = $this->cross_base($data['data'], $data['category']);
    		if ($rtype == 'arr' && !$cross['cross']['fabric']) {
    			echo '<pre style="font:10px Tahoma;">';
    			print_r($cross);
    			print_r($data);
    		}
    		
    		//检验别名
    		if ($rtype == 'arr') {
    			if ($cross['cross']['data']){
    				
    				foreach ($cross['cross']['data'] as $v){
    					if (strstr($v,'X-')){
    						echo $v;
    					}
    				}
    			}
    		}
    		
    		
    		$diy = array('DIY');
    		
    		$data['key_sequence'] = $cross['crosskey'];
    		$data['key_name'] = $cross['crosskeyname'];
//     		$data['wez'] = $cross['wz'];
    		
    		
//     		var_dump($cross);exit();
    		
    		foreach ($cross['cross']['fabric'] as $fabric){
    			$fabricdata =  array();
    			$weizhi = array();
    			$this->com('', array_merge(array($fabric),$cross['cross']['data']),array_merge($diy,$cross['crosskey']));
    			$fabricdata = $this->_res;
    			unset($this->_res);
    			
    			if ($cross['wz']){//撞色位置多选
    				
    				$arr = $cross['wz'];
//     				var_dump($arr);
    				$this->arrangement('', $arr);
//     				var_dump(array_unique($this->_arrangement));
    				if ($this->_arrangement){
    					unset($this->_arrangement[0]);
    					$temp = array('data'=>array(),'crosskey'=>array());
    					foreach (array_unique($this->_arrangement) as $r){
    						$t = array();
    						$t = explode(' ',$r);
    						$temp['data'] = array_flip($cross['cross']['data']);
    						$temp['crosskey'] = array_flip($cross['crosskey']);
    						foreach ($t as $v){
//     							var_dump($v);
    							unset($temp['data']["$v"]);
    							unset($temp['crosskey']["$v"]);
    							    					    			
    						}
//     						echo "\n--\n";
//     						var_dump($temp);
    						$this->com('', array_merge(array($fabric),array_flip($temp['data'])),array_merge($diy,array_flip($temp['crosskey'])));
//     						    					    			var_dump($this->_res);exit();
    						if ($this->_res){
    							foreach ($this->_res as $r){
    								array_push($fabricdata,$r);
    							}
    						}
    							
    						unset($this->_res);
    					}
    					unset($this->_arrangement);
    				}
    				
    			}
    			$data[$fabric] =$fabricdata;
    		}
    		/* 图片规则 模拟-end */
    		
    		if ($rtype == 'json'){
    			ajaxReturn(1,'',$data);
    		}elseif ($rtype == 'arr') {
    			header('Content-Type:text/html; charset=utf-8');
    			echo '<pre style="font:10px Tahoma;">';
    			unset($data['customs']);
    			print_r($data);
    		}else {
    			return $data;
    		}
    		
    		
    		$cache_server->set($key, $data, 1800);
    	}
    
    	 
    }
    
    function arrangement($line, $x){
    	if (count($x)==1){
    		if (trim("$line $x[0]")) $this->_arrangement[] = trim("$line $x[0]");
    		
    	}
    	else {
    		$y=array_pop($x);
    		$this->arrangement($line.' '.$y, $x);
    		$this->arrangement($line, $x);
    		$this->arrangement($line, array($y));
    	}
    	
    }
    
    //--------------------------------支持接口内部私有方法 受保护----------------------------------------------------------
    
    /**
     * 生成图片
     * 
     * 这个算法 很坑 注意...
     * 
     * 注意.key是倒序别名，因为生成的图片地址是倒序过来的.
     * 
     * 
     */
    	//递归方法
    	function com($line, $x,$key){
//     		var_dump($x);
//     		var_dump($key);
//     		exit();
			    	if (count($x)==1){
					if (substr_count("$line-$x[0]",'-') == count($key)){	//限制拼接字符
						$temp = '';
						$temp = "DIY$line-$x[0]";
						
						$i =0;
						foreach (explode("-",$temp) as $k=>$v){
							if (@strstr($v,$key[$k])){
								$i++;
							}
						}
						if ($i == count($key)){
							if (strstr("$line-$x[0]",'ML')){
// 								echo substr("$line-$x[0]",1)."\n";
								$this->_res[] = substr("$line-$x[0]",1);
							}
						}
					}
				}else {
					$y=array_pop($x);
					$this->com($line.'-'.$y, $x,$key);
					$this->com($line, $x,$key);
					$this->com($line, array($y),$key);
				}
    	}
    
    
    /**
     * 解析定制code
     * 
     * 格式说明：一级分类id:组件id 
     * 格式备注：款式设计一级分类有重复;个性签名位置会有多个用","连接
     * 
     * 事例：8001:609997|24:36|24:100013|24:100037|298:31655|298:376|298:450|298:528|298:427,485
     * 备注  面料:DBL652A		
     * 		款式设计:前门扣(单排一粒扣) 		款式设计:胸口袋(正常胸袋1(单1）) 	款式设计:下口袋(标准1)
     * 		里料:FLLDL009					纽扣搭配:奶白色果壳扣KG051 	
     * 		个性签名:字体颜色(102#--白色) 		个性签名:字体(宋体)				个性签名:位置(领底呢，左里袋上方的面料上（直过面除外）)
     *      
     *      
     * 返回：process ：工艺 
     * 		consumption：单耗 
     * 		customs-service_fee:服务费
     * 		data ：is_inventory ：1 检查库存	；is_price ：1 计算价格
     */
    function parsing_code($id,$code){
//     	var_dump($expression);
    	$return = array('error'=>0,'msg'=>'','data'=>array());
    	$codearr = array();
    	$codedata = array();
    	if (!$id || !$code) return array('error'=>1,'msg'=>'id or code empty','data'=>array());
    	
    	$custom = $this->get_basis_info($id,1);	//生成嵌套格式的树形数组
    	
    	if (!$custom) return array('error'=>1,'msg'=>'custom info empty','data'=>array());
    	
    	$codearr = array_filter(explode('|', $code));
    	if ($codearr){
    		//规整数组 一级分类=>组件
    		foreach($codearr as $r) {
    			$ex = array();
    			$ex = explode(':', $r);
    			$id = $ex[0];
    			$exs = explode(',', $ex[1]);
    			if(!isset($codedata[$id])) {
    				$codedata[$id] = array($exs[0]);
    				if (isset($exs[1])) $codedata[$id] = array($exs[1]);
    			}else{
    				$codedata[$id][] = $exs[0];
    				if (isset($exs[1])) $codedata[$id][] = $exs[1];
    			}
    		}
    		
    		//检查改定制是否存在这些组件
    		foreach ($codedata as $key=>$val){
    			foreach ($val as $pid){
    				$this->array_search_key(intval($pid), $custom['data']);
    				if (!$this->_nodes_found[$pid]){
    						 $return['error'] = 1;
    						 $return['msg'] = $pid.' is empty';
    						 break;
    				}
    				$this->_nodes_found[$pid]['t_id'] = $key;
    			}
    		}
    		
    		//错误 包含定制款之外的组件
    		if ($return['error']){
    			$return['data'] = array();
    			return $return;
    		}

    		$return['data'] = $this->_nodes_found;
    		
    		/* 释放 */
    		unset($this->_nodes_found);
    		
    		foreach ($return['data'] as $k=>$v){
    			$return['data'][$k]['is_inventory'] = 0;		//是否需要检查库存
    			$return['data'][$k]['is_price'] = 0;			//是否计算价格
    			/* 现在组件 只有面料、里料涉及到库存价格 */
    			if (in_array($v['t_id'], Constants::$fabricsParent) || in_array($v['t_id'], Constants::$materialParent)){//面料
    				$return['data'][$k]['is_price'] = 1;
    			}
    			if (in_array($v['t_id'], Constants::$fabricsParent)){//只有面料减库存
    				$return['data'][$k]['is_inventory'] = 1;
    			}
    			
    		}
    		$return['process'] = $custom['process'];
    		$return['consumption'] = $custom['consumption'];
    	}
    	
    	return $return;
    }
    
    function multi_array_sort($multi_array,$sort_key,$sort=SORT_ASC){
    	if(is_array($multi_array)){
    		foreach ($multi_array as $row_array){
    			if(is_array($row_array)){
    				$key_array[] = $row_array[$sort_key];
    			}else{
    				return false;
    			}
    		}
    	}else{
    		return false;
    	}
    	array_multisort($key_array,$sort,$multi_array);
    	return $multi_array;
    }
    
    /**
     * s
     * @param unknown_type $data
     * @param unknown_type $category
     */
    function cross_base($data,$category){
    	//交叉数据
    	$cross = array();
    	$cross['data'] = array();
    	$crosskey= array();
    	$crosskey_wz= array();			//特殊处理-撞色设计位置
    	$crosskeyname= array();
    	$nodes_found = array();
    	$this->array_search_key('part_id',$data,2);
    	if ($this->_nodes_found){
    		
    		$nodes_found = $this->multi_array_sort($this->_nodes_found,'cate_id');
    		unset($this->_nodes_found);
//     		print_r($nodes_found); 
			$y =0;
    		foreach ($nodes_found as $v){
    			if (in_array($v['top_cate'], Constants::$fabricsParent)){//面料顶级id
    				
    				$cross['fabric'][] = (empty($category[$v['top_cate']]['alias']) ?$v['top_cate'] : $category[$v['top_cate']]['alias']).$v['part_name'];
    			}
    			
    			if(in_array($v['top_cate'], Constants::$graphParent)){//工艺
    				if (!in_array($v['parent_cate'], Constants::$graphRuleoutParent) && !in_array($v['cate_id'], Constants::$graphRuleoutParentCid)){//工艺下不纳入出图规则计算
    					$cross['data'][] = (empty($category[$v['cate_id']]['alias']) ? 'X-'.$v['cate_id'].":" : $category[$v['cate_id']]['alias']).$v['part_id'];
	    				$crosskey[] =$category[$v['cate_id']]['alias'];
	    				$crosskeyname[] =$category[$v['cate_id']]['cate_name'];
    				}
    			}
    			
    			if(in_array($v['parent_cate'], Constants::$buttonsParent)){//纽扣
    				$cross['data'][] = (empty($category[$v['cate_id']]['alias']) ? 'X-'.$v['cate_id'].":" : $category[$v['cate_id']]['alias']).$v['part_id'];
    				$crosskey[] =$category[$v['cate_id']]['alias'];
    				$crosskeyname[] =$category[$v['cate_id']]['cate_name'];
    			}

    			if (in_array($v['parent_cate'], Constants::$designShowParent)){//撞色设计
    				
    			
    			
    				if (in_array($v['cate_id'], Constants::$colorLocationParent)){//位置多选
//     					$cross['data'][] = (empty($category[$v['cate_id']]['alias']) ? 'X-'.$v['cate_id'].":" : $category[$v['cate_id']]['alias']).$v['part_id'];
//     					$crosskey[] =$category[$v['cate_id']]['alias'].$v['part_id'];
//     					$crosskey_wz[] = $category[$v['cate_id']]['alias'].$v['part_id'];
    				}else{
//     					$cross['data'][] = (empty($category[$v['cate_id']]['alias']) ? 'X-'.$v['cate_id'].":" : $category[$v['cate_id']]['alias']).$v['part_id'];
//     					$crosskey[] =$category[$v['cate_id']]['alias'];
//     					$crosskeyname[] =$category[$v['cate_id']]['cate_name'];
    				}
    			}
    			$y++;
    		}
    		
    	}

    	 
//     	print_r(array('cross'=>$cross,'crosskey'=>array_reverse(array_unique($crosskey)),'crosskeyname'=>array_reverse(array_unique($crosskeyname)),'wz'=>$crosskey_wz));
    	
//     	exit();
    	
 		return array('cross'=>$cross,'crosskey'=>array_reverse(array_unique($crosskey)),'crosskeyname'=>array_reverse(array_unique($crosskeyname)),'wz'=>$crosskey_wz);
    	
    }
    
    /**
     * 通过基本款id 解析出code
     * 
     * return array(3) {
  	 *  				["error"]=>int(0)
     * 					["msg"]=>string(0) ""
     *					["data"]=>string(119) "8001:612224|24:38|24:100013|24:100015|24:100017|24:100037|24:100039|24:100041|298:31655|298:376|298:450|298:528|298:427"
	 * }
     */
    function parsing_code_base($id){
    	$return = array('error'=>0,'msg'=>'','data'=>array());
    	$codearr = array();
    	$codedata = array();
    	if (!$id) return array('error'=>1,'msg'=>'id or code empty','data'=>array());
    	
    	$custom = $this->get_basis_info($id,1);	//生成嵌套格式的树形数组
    	 
    	
    	//必须有组件 
    	if(!$custom['key_sequence']) return array('error'=>1,'msg'=>'key_sequence info empty','data'=>array());
    		
    	
    	if (!$custom) return array('error'=>1,'msg'=>'custom info empty','data'=>array());
    	
    	$cs =& cs();
    	$gcategories = $cs->_get_gcategory();
    	$tree = $cs->_tree($gcategories);
    	
    	$this->array_search_val('is_dft', $custom['data'],$tree,1);
    	
    	
    	
    	
    	if (!$this->_nodes_found_val)  if (!$custom) return array('error'=>1,'msg'=>'default empty','data'=>array());
    	
    	foreach (Constants::$fabricsParent as $fabrics){
    		$this->array_search_key(intval($fabrics), $this->_nodes_found_val,1);
    	}
    	
    	//每个基本款必须有默认面料
    	if (!$this->_nodes_found) return array('error'=>1,'msg'=>'fabrics default empty','data'=>array());
    	
    	$code = '';
       	foreach ($this->_nodes_found_val as $k=>$v){
       		$tpatid = array();
       		$patid = array();
       		$tpatid = array_keys($v);
       		$patid = array_values($v);
    		$code .= $tpatid[0].':'.$patid[0]."|";
    	}
    	
    	if (!$code) return array('error'=>1,'msg'=>'code err','data'=>array());
    	
//     	echo $code."\n";
    	/* 释放 */
    	unset($this->_nodes_found_val);
    	unset($this->_nodes_found);
    	
    	return array('error'=>0,'msg'=>'','data'=>array(substr($code,0,-1)));
    	
    }
   
    /**
     * 搜索多维数组的键名
     * 
     * @param int	 	$needle			查找的key值
     * @param array 	$haystack		数据源
     * @param int 		$t				0是默认返回规定的格式		1返回通用格式
     */
    function array_search_key($needle, $haystack,$t=0){
    	foreach ($haystack as $key1=>$value1) {
    		if ($key1=== $needle){
    			if ($t==1){
    				$this->_nodes_found[$key1] = $value1;
    			}elseif ($t==2) {
//     				var_dump($haystack);exit();
    					$this->_nodes_found[] = array('top_cate'=>$haystack['top_cate'],'parent_cate'=>$haystack['parent_cate'],'cate_id'=>$haystack['cate_id'],'part_id'=>$haystack['part_id'],'part_name'=>$haystack['part_name']);
    			}else{
    				$this->_nodes_found[$value1['info']['part_id']] = $value1['info'];
    			}
    		}
    		if (is_array($value1)){
    			$this->array_search_key($needle, $value1,$t);
    		}
    	}
    }
    
    /**
     * 搜索多维数组的键名，val符合的值
     *
     * @param int	 	$needle			查找的key值
     * @param array 	$haystack		数据源
     */
    function array_search_val($needle, $haystack,$tree,$val=1){
    	$tpid = array();
    	$pid = 0;
    	foreach ($haystack as $key1=>$value1) {
    		if ($key1== $needle){
    			if ($value1 == $val){
    			$tpid = $tree->getParents($haystack['cate_id']);				//查找父id 逐级返回. 不包括本身
    			$pid = $haystack['cate_id'];
    			//1表示是否顶级 2顶级id｛大类例：3是西服｝ 3为父id
    			if (isset($tpid[3])) $pid = $tpid[3];
    			
    			$this->_nodes_found_val[$haystack['parent_cate']] = array($pid=>$haystack['part_id']);
    			}
    		}
    		if (is_array($value1)){
    			$this->array_search_val($needle, $value1,$tree,$val);
    		}
    	}
    }
 
    /**
     * 分析用户的点击的log,返回数据
     * 
     * @param array $pams
     * @return array
     */
    protected function _get_history($pams){
    	$data = array();
    	$where = '';
    	/* 根绝点击log检索 */
    	if ($pams['uid']) $where = ' AND a.uid='.$pams['uid'];
    	
    	/* 排除基本款id */
    	if ($pams['excludeid']){
    		$where .=' AND a.`cid` NOT'.db_create_in($pams['excludeid']);
    	}
    	$sql ='SELECT a.`id` , a.`uid` , a.`cid` , a.`pid` ,a.`child`, COUNT( DISTINCT b.id )  AS num'
    			.' FROM '. $this->_customsuhistory_mod->table.' as a'
    			.' LEFT JOIN '. $this->_customsuhistory_mod->table.' b ON a.`id` = b.`id` '
    			.' WHERE 1 '.$where
    			.' GROUP BY a.`pid` , a.`child` ORDER BY num desc LIMIT 0 , '.$pams['num'];
    	$history = $this->_customsuhistory_mod->getAll($sql);
    	if (!$history) return $data;
    	 
    	$ids = array();
    	foreach ($history as $r){
    		$ids[]=$r['cid'];
    	}
    	$data = $this->_customs_mod->find(array(
    			'conditions' => 'cst_id '.db_create_in($ids),
    	));
    	return $data;
    }
    
    
    
    /**
     * 格式话 数据 基本款-类型-二级类型-组件-组件属性1-组件属性2
     * @param array $new
     * @return array 
     */
    protected function _format_data($new){
    	/* 类型 */
    	$items = array();
    	$parts = array();
    	$process = array();//工艺
    	$processid = array();//工艺
    	
    	$hiddenid = array();		//有些情况不需要在flash前端，让用户点选 隐藏 id结合
    	$hiddendata = array();		//有些情况不需要在flash前端，让用户点选 隐藏 数组结合
    	
    	$conditions = array();
    	
    	foreach($new as $item) {
    		$top_type = empty($item['top_cate']) ? $item['parent_cate'] : $item['top_cate'];
    		$pt_type = $item['parent_cate'];
    		
    		unset($item['parent_cate']);
    		 
    		if(!isset($items[$pt_type])) {
    			$items[$top_type][$pt_type]['part'][$item['pt_id']] = array(
    			// 			        		'pt_type'=>$pt_type,
				//							'items'=>array()
    					);
    		}
    		//组件关联 循环sql,日后再优化
    		if ($item){
//     			foreach ($item as $r){
    				$patinfo = array();
    				$spec = array();
    				$patinfo = $this->_parts_mod->get(array('conditions' => $item['pt_id'], "fields" => "part_id,part_name,code,cost_price,price,part_number,part_small,sort_order,cate_id,zindex,vcompositionid,vcolorid,vflowerid"));
//     				$spec = $this->_format_spec_data($item['pt_id']);
    				$patinfo['is_dft'] = $item['is_dft'];
    				$patinfo['parent_cate'] = $pt_type;
    				$patinfo['top_cate'] = $top_type;
    				
    				$img = 'http://img.rcmtm.com/process/';
    				$patinfo['personality'] = '';
    				$patinfo['b_img'] = '';			//里料大图

    				
    				/* 处理原来图片的小图 */
					if (in_array($patinfo['cate_id'], Constants::$fabricsParent)){//面料顶级id
					
						$patinfo['s_img'] = $img.'fabric/'.$patinfo['code'].'_S.png';
						$patinfo['part_name'] = $patinfo['code'];
						$patinfo['part_small'] = $patinfo['part_small'];		
							
					}elseif (in_array($patinfo['parent_cate'], Constants::$botouParent)){//处理前端隐藏属性｛驳头形、驳头宽｝
						
						//todo $hiddenid[] =$patinfo['parent_cate'];
						if ($item['is_dft'] == 1){
							//todo $hiddendata[]=$patinfo;
						}
						//todo unset($patinfo);
							
// 						unset($patinfo);
					}elseif (in_array($patinfo['cate_id'], Constants::$hiddenParent)){//处理前端隐藏属性｛裤褶 、 褶深｝
						
						$hiddenid[] =$patinfo['2042'];
						if ($item['is_dft'] == 1){
							//todo $hiddendata[]=$patinfo;
						}
						//todo unset($patinfo);
							
// 						unset($patinfo);
					}elseif (in_array($top_type, Constants::$designParent)){//深度设计
						
						if (in_array($patinfo['parent_cate'], Constants::$designShowParent)){//撞色设计
							$patinfo['s_img'] = $img.$patinfo['cate_id'].'/'.$item['pt_id'].'_S.png';
							if (in_array($patinfo['cate_id'], Constants::$colorLocationParent)){
								$hiddenid[] =$patinfo['cate_id'];
									$hiddendata[]=$patinfo;
								unset($patinfo);
							}else{
								$hiddenid[] =$patinfo['parent_cate'];
								if ($item['is_dft'] == 1){
									$hiddendata[]=$patinfo;
								}
								unset($patinfo);
							}
						}elseif (in_array($patinfo['parent_cate'], Constants::$signatureParent)){//个性签名
							$patinfo['s_img'] = $img.$patinfo['cate_id'].'/'.$item['pt_id'].'_S.png';
							
							if (in_array($patinfo['cate_id'], Constants::$fontParent)){
								$patinfo['personality'] = 'font';
							}elseif (in_array($patinfo['cate_id'], Constants::$locationParent)){
								$patinfo['personality'] = 'location';
								$patinfo['s_img'] = '';
							}elseif (in_array($patinfo['cate_id'], Constants::$colorParent)){
								$patinfo['personality'] = 'color';
							}

						}elseif (in_array($patinfo['cate_id'], Constants::$materialParent)){//里料
							$patinfo['s_img'] = $img.$patinfo['cate_id'].'/'.$item['pt_id'].'_S.png';
							$patinfo['part_name'] = preg_replace("/[\s\x{4e00}-\x{9fff}#\[\]\{\}\’\”=+\\(\)*&%$@]+/iu", "", $patinfo['part_name']);
							$patinfo['b_img'] = '/material/'.$patinfo['part_name'].'.jpg';
						}elseif (in_array($patinfo['parent_cate'], Constants::$buttonsParent)){//扣子装饰
							$patinfo['s_img'] = $img.$patinfo['cate_id'].'/'.$item['pt_id'].'_S.png';
						}else{//深度设计里面不拆分的
							$hiddenid[] =$patinfo['parent_cate'];
							if ($item['is_dft'] == 1){
								$hiddendata[]=$patinfo;
							}
							unset($patinfo);
						}
						
					}elseif (in_array($patinfo['top_cate'], Constants::$graphParent)){
						$patinfo['s_img'] = $img.'graph/'.$patinfo['part_id'].'_S.png';
					}
					
    				$parts = array();
    				if ($patinfo['part_name']){
    					$parts = array('info'=>$patinfo);
    				}

    		}
    		
    		/**
    		 * 出现缺少组件 ，有可能是  top_cate 和  parent_cate 都相等，但是前端必须拆分成多个栏目
    		 */
    		/* 应阿亮需求 BT 限制死  稍后动态 定义数组 */
    		if ($patinfo['parent_cate'] == '2089' || $patinfo['parent_cate'] == '3034'){
    			$conditions[]= $patinfo['cate_id'];
    			$items[$top_type][$patinfo['cate_id']]['part'][$item['pt_id']] = $parts;
    		}else{
    			$items[$top_type][$pt_type]['part'][$item['pt_id']] = $parts;
    		}
    		
    	}
    	/* 过滤工艺 */
    	$processid = array_unique($processid);
    	if ($processid){
    		foreach ($items as $k=>$r){
    			foreach ($r as $key=>$value){
    				if (in_array($key, $processid)){
    					unset($items[$k][$key]);
    				}
    			}
    		}
    		
    	}
    	
    	/* 过滤工艺 */
    	$hiddenid = array_unique($hiddenid);
    	if ($hiddenid){
    		foreach ($items as $k=>$r){
    			foreach ($r as $key=>$value){
    				if (in_array($key, $hiddenid)){
    					unset($items[$k][$key]);
    				}
    			}
    		}
    	
    	}
    	/* 应阿亮需求 BT 限制死  稍后动态 定义数组 */
    	unset($items['2021']['2089']);
    	unset($items['3016']['3034']);
 
    	$items['process'] = $process;
    	//端隐藏数据
    	$items['hiddendata'] = $hiddendata;
    	$items['conditions'] = $conditions;
    	
    	return $items;
    }
    
    
    /**
     * 格式化 属性规格
     * @param int $pt_id	组件id
     */
    protected function _format_spec_data($pt_id){
    	 
    	$spec = $this->_partattr_mod->findAll(array('conditions' => 'part_id='.$pt_id));
    	$items = array();
    	foreach($spec as $item) {
    		$attr_id = $item['attr_id'];
    		unset($item['attr_id']);
    		 
    		if(!isset($items[$attr_id])) {
    			$items[$attr_id] = array('attr_id'=>$attr_id, 'specarr'=>array());
    		}
    		 
    		$items[$attr_id]['specarr'][] = $item;
    	}
    
    	return $items;
    }
    
    /**
     * 获取所有的组件类型数据
     * @return array()
     */
    public  function _get_parttype(){
    	/* 获取组件类型表 - 缓存数组关联 */
    	$cache_server =& cache_server();
    	$key = 'page_of_part_type';
    	$p_c_data = $cache_server->get($key);
    	$data = false;
    	$cached = true;
    	if ($p_c_data === false){
    		$p_c_data = $this->_parttype_mod->find();
    		$cache_server->set($key, $p_c_data, 1800);
    	}
    	return $p_c_data;
    }

    /**
     * 获取分类数据
     * @return array()
     * 'conditions'=>db_create_in($cateIds,'cate_id'),
     */
    public  function _get_gcategory($conditions='',$cache=''){
    	/* 获取组件类型表 - 缓存数组关联 */
    	if ($cache)
    	{
    		$conditions = empty($conditions) ? "1=1" : $conditions;
    		$p_c_data = $this->_gcategory_mod->find(array('conditions'=>$conditions));
    		return $p_c_data;
    	}
    	
    	$cache_server =& cache_server();
    	$key = 'page_of_gcategory_'.md5($conditions);
    	$p_c_data = $cache_server->get($key);
    	$p_c_data = false;
    	$cached = true;
    	if ($p_c_data === false){
//     		$p_c_data = $this->_gcategory_mod->find(array('fields'=> 'cate_id, parent_id, cate_name, depth'));
    		$conditions = empty($conditions) ? "1=1" : $conditions;
    		$p_c_data = $this->_gcategory_mod->find(array('conditions'=>$conditions));
    		$cache_server->set($key, $p_c_data, 1800);
    	}
    	return $p_c_data;
    }
    
    
    
    /**
     * 构造并返回树
     * @param  arrary 	$gcategories	数据源
     * @return Tree
     * @author v5
     * <code>
     * $cs =& cs();
     * $gcategories = $cs->_get_gcategory();
     * $tree = $cs->_tree($gcategories);
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
     * @author v5
     * <code>
     * $cs =& cs();
     * $gcategories = $cs->_get_gcategory();
     * $cs->deep_tree($gcategories,3);
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

?>