<?php
use Cyteam\Goods\Goods;
use Cyteam\Goods\Gcategory;
use Cyteam\Goods\Products;
use Cyteam\Goods\Region;
use Cyteam\Module\Module;
use Cyteam\Config\Config;
/* 商品 相关管理
 * 
 * */
class ProductApp extends MallbaseApp
{
    function __construct()
    {
       parent::__construct();
	   
    }
    
    /**
     * liang.li
     * @param unknown $url
     * @param string $data
     * @return mixed
     */
    function https_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
     
        $output = json_decode($output,true);
        return $output;
    }
    
   
    
    function tll()
    {
        $this->_do_login(2);
    }

    /**
     * @access protected
     * @author shaozizhen
     */
    function search()
    {
        $keywords = explode(',', conf::get('hot_search'));

        $this->assign('keywords',$keywords);
        $this->display('sousuo.html');
    }
   
   
    /**
     * 商品列表
     *
     * 2016-6-21
     *
     * @access protected
     * @author shaozizhen
     */
    function index()
    {
        
    	$user_info=$this->visitor->get();
    	$goods_mod =& m('goods');
    	$goods_promotion_mod =& m('goods_prorule');
    	$member_lvs=m("memberlv");
    	$goods_prorel_mod =& m('goods_prorel');
    	$goodslink_mod = &m('goods_prolink');
    	$search_recordmod=m("search_record");
        $search = isset($_GET['search']) ? $_GET['search'] : 0;

        if($search)
        {
            $conditions=" AND name like '%$search%' ";
        }

    	if($_GET['son_id']){
    		$conditions .= " AND cat_id = '{$_GET['son_id']}'";
    	}else{
    		if($_GET['p_id']){
    			$gcates = $this->lists($_GET['p_id']);
                
                
    			if($gcates){
    			foreach($gcates as $k1=>$v1){
    				
    				$cat_ids[]=$v1['cate_id'];
    			}
    			$conditions .= " AND cat_id ".db_create_in($cat_ids);
    			}
    		}
    	}
    	if($_GET['cat_id']){
    		$conditions .= " AND cat_id = '{$c_id}'";
    	}
    	
    	if($_GET['sort']){
    		$sorts=$_GET['sort'];
    	}else{
    		$sorts="p_order";
    	}
    	if($_GET['order']){
    		$orders=$_GET['order'];
    	}else{
    		$orders="DESC";
    	}
    	$page = $this->_get_page(200);
    	setcookie('curr_page',$page["curr_page"]);
    
    	$gcategoryLib1 = $this->lists(0);

    	if($gcategoryLib1){
    		foreach($gcategoryLib1 as $k1=>$v1)
    		{
    				
    			$gcategoryLib2 = $this->lists($v1['cate_id']);
    			if($gcategoryLib2){
    				$gcategoryLib1[$k1]['son']=$gcategoryLib2;
    			}else{
    				$gcategoryLib1[$k1]['son']=[];
    			}
    				
    		}
    	}


    	$new_time=time();
    	if($user_info['user_id']){
    
    		$member_lv=$user_info['member_lv_id'];
    		if($member_lv){
    		    $conditiony=" AND FIND_IN_SET('{$member_lv}',member_lv_id) ";
    		}
    	
    
    	}else{
    		$user_infos=$member_lvs->get(array(
    				'conditions' =>"1=1",
    
    		));
    		$member_lv=$user_infos['member_lv_id'];
    		if($member_lv){
    		    $conditiony=" AND FIND_IN_SET('{$member_lv}',member_lv_id) ";
    		}
    		
    	}
    	
    	$goodlists=$goods_mod->find(array(
    			'conditions'=>"marketable=1 ".$conditions,
    			'limit' => $page['limit'],
    			'count' => true,
    			'order'=>$sorts." ".$orders,
    			'index_key'=>"",
    	));
    	$new_time=time();
    	$goods_promotion= $goods_promotion_mod->find(array(
    			'conditions'=>"is_open = 1 AND find_in_set(2,site_id)  AND '{$new_time}' >= starttime AND '{$new_time}' <= endtime ".$conditiony,
    			'order'  =>"level ASC",
    			'index_key'=>"",
    	));

    	if($goodlists){
    		foreach($goodlists as $key=>$val){
    			$goodlists[$key]['order_price']=($val['price']);
    			$goodlists[$key]['price']=($val['price']);
    			$goodlists[$key]['mktprice']=($val['mktprice']);
    		}
    	}

    	$iny=0;
    	if($member_lv){
    
    		if($goods_promotion){
    
    			$len=count($goods_promotion);
    			for($i=1;$i<$len;$i++)
    			{
    			for($k=0;$k<$len-$i;$k++)
    			{
    			if($goods_promotion[$k]['if_ex']<$goods_promotion[$k+1]['if_ex'])
    			{
    				$tmp=$goods_promotion[$k+1];
    				$goods_promotion[$k+1]=$goods_promotion[$k];
    				$goods_promotion[$k]=$tmp;
    				}
    				}
    				}
    
    			}

    			if($goodlists){
    				
    			foreach($goodlists as $key=>$val){
    				
    			if($goods_promotion){
    				foreach($goods_promotion as $k1=>$v1){
    					
    				if($v1['favorable']==1){
    
    					$goodlink=$goodslink_mod->find(array(
    						'conditions'=>"rules_id='{$v1['id']}' AND favorable_id=1",
    						'fields'=>"favorable_value",
    						'index_key'=>"",
    								));
						if($goodlink){
						foreach($goodlink as $k2=>$v2){
    						$goods_lists[]=$v2['favorable_value'];
    						}
    						}
    						
    						if(in_array($val['type_id'],$goods_lists)){
    						if($v1['yhcase']==1){
    								$goodlists[$key]['yhcase_id']=1;
    									$goodlists[$key]['yhcase_name']='促销';
							$goodlists[$key]['yhcase']=($val['price']*($v1['yhcase_value']/100));
    							$goodlists[$key]['order_price']=($val['price']*($v1['yhcase_value']/100));
							}elseif($v1['yhcase']==2){
    									$goodlists[$key]['yhcase_id']=2;
    									$goodlists[$key]['yhcase_name']='促销';
    											$goodlists[$key]['yhcase']=intval($v1['yhcase_value']);
    											$goodlists[$key]['order_price']=intval($v1['yhcase_value']);
    								}elseif($v1['yhcase']==3){
									$goodlists[$key]['yhcase_id']=3;
    											$goodlists[$key]['yhcase_name']='促销';
    											$goodlists[$key]['yhcase']=($val['price']-$val['price']*($v1['yhcase_value']/100));
    													$goodlists[$key]['order_price']=($val['price']-$val['price']*($v1['yhcase_value']/100));
    											}elseif($v1['yhcase']==4){
    											$goodlists[$key]['yhcase_id']=4;
    												$goodlists[$key]['yhcase_name']='促销';
    												$goodlists[$key]['yhcase']=($val['price']-$v1['yhcase_value']);
    												$goodlists[$key]['order_price']=($val['price']-$v1['yhcase_value']);
    											}elseif($v1['yhcase']==5){
    												$goodlists[$key]['yhcase_name']='免邮';
    												$goodlists[$key]['yhcase_id']=5;//免邮
    												$goodlists[$key]['yhcase']=0;
    												}
    													
    								}else{
    								$goodlists[$key]['yhcase_id']=0;
    										$goodlists[$key]['yhcase']=0;
    								}
    								break;
    						}elseif($v1['favorable']==2){
    						$goods_lists=$goods_prorel_mod->get(array(
    								'conditions'=>"c_id='{$val['goods_id']}' AND d_id='{$v1['id']}'",
    										));
    
    										if($goods_lists){
    										if($v1['yhcase']==1){
    											$goodlists[$key]['yhcase_id']=1;
    													$goodlists[$key]['yhcase_name']='促销';
    													$goodlists[$key]['yhcase']=($val['price']*($v1['yhcase_value']/100));
    													$goodlists[$key]['order_price']=($val['price']*($v1['yhcase_value']/100));
    										}elseif($v1['yhcase']==2){
    										$goodlists[$key]['yhcase_id']=2;
    										$goodlists[$key]['yhcase_name']='促销';
    										$goodlists[$key]['yhcase']=intval($v1['yhcase_value']);
    										$goodlists[$key]['order_price']=intval($v1['yhcase_value']);
    										}elseif($v1['yhcase']==3){
    										$goodlists[$key]['yhcase_id']=3;
    										$goodlists[$key]['yhcase_name']='促销';
    												$goodlists[$key]['yhcase']=($val['price']-$val['price']*($v1['yhcase_value']/100));
    														$goodlists[$key]['order_price']=($val['price']-$val['price']*($v1['yhcase_value']/100));
    										}elseif($v1['yhcase']==4){
    										$goodlists[$key]['yhcase_id']=4;
    										$goodlists[$key]['yhcase_name']='促销';
    										$goodlists[$key]['yhcase']=($val['price']-$v1['yhcase_value']);
    										$goodlists[$key]['order_price']=($val['price']-$v1['yhcase_value']);
    										}elseif($v1['yhcase']==5){
    												$goodlists[$key]['yhcase_name']='免邮';
    												$goodlists[$key]['yhcase_id']=5;//免邮
    												$goodlists[$key]['yhcase']=0;
    												}
    												}else{
    												$goodlists[$key]['yhcase_id']=0;
    												$goodlists[$key]['yhcase']=0;
    												}
    												break;
    												}elseif($v1['favorable']==3){
    													$goodlink=$goodslink_mod->find(array(
    													'conditions'=>"rules_id='{$v1['id']}' AND favorable_id=3",
    													));
    													if($goodlink){
    													foreach($goodlink as $k2=>$v2){
    															$goodlinks[]=$v2['favorable_value'];
    															}
    															}
    															if(in_array($val['cat_id'],$goodlinks)){
    
    																	if($v1['yhcase']==1){
    																	$goodlists[$key]['yhcase_id']=1;
    																	$goodlists[$key]['yhcase_name']='促销';
    																			$goodlists[$key]['yhcase']=($val['price']*($v1['yhcase_value']/100));
    																			$goodlists[$key]['order_price']=($val['price']*($v1['yhcase_value']/100));
    																	}elseif($v1['yhcase']==2){
    																			$goodlists[$key]['yhcase_id']=2;
    																			$goodlists[$key]['yhcase_name']='促销';
    																					$goodlists[$key]['yhcase']=intval($v1['yhcase_value']);
    																					$goodlists[$key]['order_price']=intval($v1['yhcase_value']);
    																					}elseif($v1['yhcase']==3){
    																					$goodlists[$key]['yhcase_id']=3;
    																							$goodlists[$key]['yhcase_name']='促销';
    																					$goodlists[$key]['yhcase']=($val['price']-$val['price']*($v1['yhcase_value']/100));
    																							$goodlists[$key]['order_price']=($val['price']-$val['price']*($v1['yhcase_value']/100));
    																					}elseif($v1['yhcase']==4){
    																					$goodlists[$key]['yhcase_id']=4;
    																						$goodlists[$key]['yhcase_name']='促销';
    																						$goodlists[$key]['yhcase']=($val['price']-$v1['yhcase_value']);
    																						$goodlists[$key]['order_price']=($val['price']-$v1['yhcase_value']);
    												}elseif($v1['yhcase']==5){
    														$goodlists[$key]['yhcase_name']='免邮';
    															$goodlists[$key]['yhcase_id']=5;//免邮
    															$goodlists[$key]['yhcase']=0;
    														}
    														}else{
    														$goodlists[$key]['yhcase_id']=0;
    														$goodlists[$key]['yhcase']=0;
    					}
    					break;
    					}elseif($v1['favorable']==4){
    														if($v1['yhcase']==1){
    														$goodlists[$key]['yhcase_id']=1;
    														$goodlists[$key]['yhcase_name']='促销';
    										$goodlists[$key]['yhcase']=($val['price']*($v1['yhcase_value']/100));
    										$goodlists[$key]['order_price']=($val['price']*($v1['yhcase_value']/100));
    										}elseif($v1['yhcase']==2){
    										$goodlists[$key]['yhcase_id']=2;
    												$goodlists[$key]['yhcase_name']='促销';
    														$goodlists[$key]['yhcase']=intval($v1['yhcase_value']);
    												$goodlists[$key]['order_price']=intval($v1['yhcase_value']);
    										}elseif($v1['yhcase']==3){
    												$goodlists[$key]['yhcase_id']=3;
    													$goodlists[$key]['yhcase_name']='促销';
    													$goodlists[$key]['yhcase']=($val['price']-$val['price']*($v1['yhcase_value']/100));
    															$goodlists[$key]['order_price']=($val['price']-$val['price']*($v1['yhcase_value']/100));
    												}elseif($v1['yhcase']==4){
    													$goodlists[$key]['yhcase_id']=4;
    															$goodlists[$key]['yhcase_name']='促销';
    															$goodlists[$key]['yhcase']=($val['price']-$v1['yhcase_value']);
    															$goodlists[$key]['order_price']=($val['price']-$v1['yhcase_value']);
    															}elseif($v1['yhcase']==5){
    															$goodlists[$key]['yhcase_name']='免邮';
    															$goodlists[$key]['yhcase_id']=5;//免邮
    															$goodlists[$key]['yhcase']=0;
    															}
    															break;
    															}
    																
    }
    }else{
    $goodlists[$key]['yhcase_id']=0;
    $goodlists[$key]['yhcase']=0;
    }
    
    }
    }
    }else{
    foreach($goodlists as $key=>$val){
    $goodlists[$key]['yhcase_id']=0;
    $goodlists[$key]['yhcase']=0;
    }
    }

    //根据优惠以及普通价格混合排序
    /* if($sort="order_price"){
    $sort = array(
    		'direction' => 'SORT_DESC',
    		'field'     => 'order_price',
    );
    $arrSort = array();
    foreach($goodlists AS $uniqid => $row){
    foreach($row AS $key=>$value){
    $arrSort[$key][$uniqid] = $value;
    }
    }
    if($sort['direction']){
    $mult=	array_multisort($arrSort[$sort['field']], constant($sort['direction']), $goodlists);
    }
    } */
    //var_dump($gcategoryLib1);exit;


   $this->assign('goodlists',$goodlists);
   $this->assign('list',$gcategoryLib1);
   $this->display("product/index.html");
    
    			
   
}
function lists($pId)
{
	$gcategory = &m('gcategory');
// echo '<pre>';print_r($gcategory);exit;

	/*  $list = $gcategory->find(array(
	 'conditions'  => "if_show=1 AND parent_id='{$pId}'",
			'index_key'   => " ",
			'order'       => "sort_order ASC",
	)); */
	$list = $gcategory->get_list($pId);
	return $list;
}

function ajax_get()
{
	$gcategory = m('gcategory');
	$pid = isset($_POST['pid']) ? intval($_POST['pid']) : 0;
	if (!$pid){
		$this->json_error('error');
		return;
	}
	  $list = $gcategory->find(array(
	 'conditions'  => "if_show=1 AND parent_id='{$pid}'",
			'index_key'   => "0",
			'order'       => "sort_order ASC",
	)); 
	/* $list = $gcategory->get_list($pid); */
	 $this->json_result(array(
		'list'      =>  $list,
	 	'count' =>count($list),
	),'success'); 
	//  $this->json_result=$list;
	die();
}
 function contentbak(){
 	$user_info=$this->visitor->get();
 	$goods_id     = $_GET['id'];
 	$de_comment=m("detail_comment");
 	$products=m("products");//货品表
 	$goodstypespecMdl = m("goodstypespec");
 	$specvaluesMdl = m('specvalues');
 	$goodsspec_mod =& m('specification');
 	$goods_mod =& m('goods');//商品表
 	$goodsgallery_mod=m("goodsgallery");//商品相册表
 	$goods_promotion_mod =& m('goods_prorule');
 	$goods_prorel_mod =& m('goods_prorel');
 	$goodslink_mod = &m('goods_prolink');
 	$goodsattr_mod=m("goodsattr");
 	$collect_mod=m("collect");
 	if (!$goods_id) {
 		$this->json_error('商品id不存在');
 		die();
 	}
 	
 	$goods_con=$goods_mod->get(array(
 			'conditions'=>"marketable=1 AND goods_id='{$goods_id}'",
 	
 	));
 
 	if($user_info['user_id']){
 		$member_lv=$user_info['member_lv_id'];
 		if($member_lv){
 		    $conditiony=" AND FIND_IN_SET('{$member_lv}',member_lv_id) ";
 		}
 		
 		//已收藏标识
 		$collect=$collect_mod->get(array(
 				'conditions'=>"user_id='{$user_info['user_id']}' AND type='goods' AND item_id='{$goods_id}'",
 		));
 		if($collect){
 			$goods_con['is_collect']=1;
 		}else{
 			$goods_con['is_collect']=0;
 		}
 	
 	}else{
 		
 		$member_lvs=m("memberlv");
 		$user_infos=$member_lvs->get(array(
 				'conditions' =>"1=1",
 					
 		));
 		
 		$member_lv=$user_infos['member_lv_id'];
 		if($member_lv){
 		    $conditiony=" AND FIND_IN_SET('{$member_lv}',member_lv_id) ";
 		}
 		
 		$goods_con['is_collect']=0;
 	}
 		
 		
 	
 	if($goods_con){
 		$goods_con['price']=($goods_con['price']);
 		$goods_con['mktprice']=($goods_con['mktprice']);
 		$goods_con['link_url']='';
 	}
 		
 		
 	//----评论---//
 	$de_comments=$de_comment->get(array(
 			'conditions'=>"comment_id='{$goods_id}' AND status=1",
 			'fields' => 'count(*) as count'
 					));
 	$de_goods=$de_comment->get(array(
 			'conditions'=>"comment_id='{$goods_id}' AND status=1 AND star in (4,5)",
 			'fields' => 'count(*) as count'
 					));
 	if($de_goods['count'] && $de_comments['count']){
 		$de_asses1=$de_goods['count']/$de_comments['count']*100;
 		$de_asses=number_format($de_asses1, 2, '.', '');
 	}else{
 		$de_asses=0;
 	}
 	
 	$goods_con['assess']=$de_asses;
 	$goods_con['de_comments']=$de_comments['count'];
 	//---属性（产品参数）--///
 	$goods_con['goods_attr']=$goodsattr_mod->find(array(
 			'conditions'=>"goods_id='{$goods_id}'",
 			'join'    =>"belongs_attribute",
 			'index_key'=>"",
 	));
 	$new_time=time();
 	$goods_promotion= $goods_promotion_mod->find(array(
 			'conditions'=>"is_open = 1 AND find_in_set(2,site_id) AND '{$new_time}' >= starttime AND '{$new_time}' <= endtime ".$conditiony,
 			'order'  =>"level ASC",
 			'index_key'=>"",
 	));
 	
 	if($goods_promotion){
 			
 		$len=count($goods_promotion);
 		for($i=1;$i<$len;$i++)
 		{
 		for($k=0;$k<$len-$i;$k++)
 		{
 		if($goods_promotion[$k]['if_ex']<$goods_promotion[$k+1]['if_ex'])
 		{
 				$tmp=$goods_promotion[$k+1];
 				$goods_promotion[$k+1]=$goods_promotion[$k];
 				$goods_promotion[$k]=$tmp;
 		}
 		}
 		}
 	
 		}
 		//--相关商品--//
 		$cat_goods=$goods_mod->find(array(
 		'conditions'=>"cat_id='{$goods_con['cat_id']}' AND goods_id != '{$goods_con['goods_id']}'",
			));
 		$cat_goods= array_slice($cat_goods, 0, 4);
 			
 			
 		if($member_lv){
 		if($cat_goods){
 		foreach($cat_goods as $key=>$val){
 		$cat_goods[$key]['price']=($val['price']);
 		$cat_goods[$key]['mktprice']=($val['mktprice']);
 				if($goods_promotion){
 		foreach($goods_promotion as $k1=>$v1){
 			
 		if($v1['favorable']==1){
 			
 		$goodlink=$goodslink_mod->find(array(
 				'conditions'=>"rules_id='{$v1['id']}' AND favorable_id=1",
 			'fields'=>"favorable_value",
 			'index_key'=>"",
 			));
 					if($goodlink){
 					foreach($goodlink as $k2=>$v2){
 					$goods_lists[]=$v2['favorable_value'];
 		}
						}
 					if(in_array($val['type_id'],$goods_lists)){
 						
 					if($v1['yhcase']==1){
 						$cat_goods[$key]['yhcase_id']=1;
 						$cat_goods[$key]['yhcase_name']='促销';
						$cat_goods[$key]['yhcase']=($val['price']*($v1['yhcase_value']/100));
 						}elseif($v1['yhcase']==2){
 						$cat_goods[$key]['yhcase_id']=2;
 						$cat_goods[$key]['yhcase_name']='促销';
 						$cat_goods[$key]['yhcase']=($v1['yhcase_value']);
 						}elseif($v1['yhcase']==3){
 								$cat_goods[$key]['yhcase_id']=3;
 										$cat_goods[$key]['yhcase_name']='促销';
 										$cat_goods[$key]['yhcase']=($val['price']-$val['price']*($v1['yhcase_value']/100));
 								}elseif($v1['yhcase']==4){
								$cat_goods[$key]['yhcase_id']=4;
 									$cat_goods[$key]['yhcase_name']='促销';
 									$cat_goods[$key]['yhcase']=($val['price']-$v1['yhcase_value']);
 								}elseif($v1['yhcase']==5){
 								$cat_goods[$key]['yhcase_name']='免邮';
 								$cat_goods[$key]['yhcase_id']=5;//免邮
 										$cat_goods[$key]['yhcase']=0;
 						}
 							
 						}else{
 							$cat_goods[$key]['yhcase_id']=0;
 									$cat_goods[$key]['yhcase']=0;
 						}
 						break;
 						}elseif($v1['favorable']==2){
 						$goods_lists=$goods_prorel_mod->get(array(
 						'conditions'=>"c_id='{$val['goods_id']}' AND d_id='{$v1['id']}'",
 						));
		
 								if($goods_lists){
 								if($v1['yhcase']==1){
 								$cat_goods[$key]['yhcase_id']=1;
 								$cat_goods[$key]['yhcase_name']='促销';
 								$cat_goods[$key]['yhcase']=($val['price']*($v1['yhcase_value']/100));
 								}elseif($v1['yhcase']==2){
 								$cat_goods[$key]['yhcase_id']=2;
 								$cat_goods[$key]['yhcase_name']='促销';
 										$cat_goods[$key]['yhcase']=($v1['yhcase_value']);
 								}elseif($v1['yhcase']==3){
 										$cat_goods[$key]['yhcase_id']=3;
 										$cat_goods[$key]['yhcase_name']='促销';
 									$cat_goods[$key]['yhcase']=($val['price']-$val['price']*($v1['yhcase_value']/100));
 										}elseif($v1['yhcase']==4){
 												$cat_goods[$key]['yhcase_id']=4;
 														$cat_goods[$key]['yhcase_name']='促销';
 														$cat_goods[$key]['yhcase']=($val['price']-$v1['yhcase_value']);
 														}elseif($v1['yhcase']==5){
 														$cat_goods[$key]['yhcase_name']='免邮';
 														$cat_goods[$key]['yhcase_id']=5;//免邮
 														$cat_goods[$key]['yhcase']=0;
 														}
 														}else{
 														$cat_goods[$key]['yhcase_id']=0;
 														$cat_goods[$key]['yhcase']=0;
 														}
 														break;
 								}elseif($v1['favorable']==3){
 								$goodlink=$goodslink_mod->find(array(
 										'conditions'=>"rules_id='{$v1['id']}' AND favorable_id=3",
 								));
 								if($goodlink){
 								foreach($goodlink as $k2=>$v2){
 								$goodlinks[]=$v2['favorable_value'];
 								}
 								}
 								if(in_array($val['cat_id'],$goodlinks)){
 									
 								if($v1['yhcase']==1){
 								$cat_goods[$key]['yhcase_id']=1;
 								$cat_goods[$key]['yhcase_name']='促销';
 								$cat_goods[$key]['yhcase']=($val['price']*($v1['yhcase_value']/100));
 								}elseif($v1['yhcase']==2){
 								$cat_goods[$key]['yhcase_id']=2;
 									$cat_goods[$key]['yhcase_name']='促销';
 									$cat_goods[$key]['yhcase']=intval($v1['yhcase_value']);
 								}elseif($v1['yhcase']==3){
 										$cat_goods[$key]['yhcase_id']=3;
 											$cat_goods[$key]['yhcase_name']='促销';
 											$cat_goods[$key]['yhcase']=($val['price']-$val['price']*($v1['yhcase_value']/100));
 										}elseif($v1['yhcase']==4){
 										$cat_goods[$key]['yhcase_id']=4;
 										$cat_goods[$key]['yhcase_name']='促销';
 												$cat_goods[$key]['yhcase']=($val['price']-$v1['yhcase_value']);
 										}elseif($v1['yhcase']==5){
 												$cat_goods[$key]['yhcase_name']='免邮';
 														$cat_goods[$key]['yhcase_id']=5;//免邮
 																$cat_goods[$key]['yhcase']=0;
 														}
 														}else{
 														$cat_goods[$key]['yhcase_id']=0;
 														$cat_goods[$key]['yhcase']=0;
 														}
 														break;
 									}elseif($v1['favorable']==4){
 									if($v1['yhcase']==1){
 									$cat_goods[$key]['yhcase_id']=1;
 									$cat_goods[$key]['yhcase_name']='促销';
 									$cat_goods[$key]['yhcase']=($val['price']*($v1['yhcase_value']/100));
 									}elseif($v1['yhcase']==2){
 											$cat_goods[$key]['yhcase_id']=2;
 											$cat_goods[$key]['yhcase_name']='促销';
 											$cat_goods[$key]['yhcase']=($v1['yhcase_value']);
 								}elseif($v1['yhcase']==3){
 										$cat_goods[$key]['yhcase_id']=3;
 												$cat_goods[$key]['yhcase_name']='促销';
 								$cat_goods[$key]['yhcase']=($val['price']-$val['price']*($v1['yhcase_value']/100));
 								}elseif($v1['yhcase']==4){
 								$cat_goods[$key]['yhcase_id']=4;
 								$cat_goods[$key]['yhcase_name']='促销';
 								$cat_goods[$key]['yhcase']=($val['price']-$v1['yhcase_value']);
 								}elseif($v1['yhcase']==5){
 								$cat_goods[$key]['yhcase_name']='免邮';
 								$cat_goods[$key]['yhcase_id']=5;//免邮
 								$cat_goods[$key]['yhcase']=0;
 								}
 								break;
 								}
 									
 								}
 								}else{
 								foreach($cat_goods as $key=>$val){
 								$cat_goods[$key]['yhcase_id']=0;
								$cat_goods[$key]['yhcase']=0;
 								}
 								}
		
 								}
 								}
 								}else{
 								foreach($cat_goods as $key=>$val){
 								$cat_goods[$key]['yhcase_id']=0;
 										$cat_goods[$key]['yhcase']=0;
 		}
 		}
 			
		
 			
 			
 			
 		$goods_con['cat_goods']=$cat_goods;
 			
		
		
 		//---相关商品结束--//
 		//---相册----////
 		$goods_con['gallery']=$goodsgallery_mod->find(array(
 		'conditions' =>"goods_id='{$goods_id}'",
 		'index_key'=>'',
		'order'=>'sort ASC ,img_id ASC',
					
 				));
 		//---货品-----/
 		$product=$products->get(array(
 		'conditions'=>"goods_id='{$goods_id}' AND is_default=1",
 				));
 					
 				if($product['product_id']){
 				$goods_con['product_id']=$product['product_id'];
 		}
 		if($product['price']){
 				$goods_con['price']=($product['price']);
 		}
 		if($product['mktprice']){
 		$goods_con['mktprice']=($product['mktprice']);
 		}
 		if($product['bn']){
 		$goods_con['bn']=$product['bn'];
 		}
 		if($product['weight']){
 		$goods_con['weight']=$product['weight'];
 		}
 		if($product['store']){
 		$goods_con['store']=$product['store'];
 		}else{
 			$goods_con['store']=0;
 		}
 			
 		$goods_con['order_price']=($goods_con['price']);
 		$spec_desc=unserialize($product['spec_desc']);

 		if($spec_desc){
 		foreach($spec_desc as $kk=>$vv){
					$specvalue[$kk][]=$vv;
 			
 	}
 	$goods_con['spec_value'] = array_values($spec_desc['spec_value']);
 	$goods_con['spec_value_id'] = array_values($spec_desc['spec_value_id']);
 	
 	}else{
 			$goods_con['spec_value']=[] ;
 	$goods_con['spec_value_id']=[] ;
 	}
 		
 	if($member_lv){
 	//111111
 			if($goods_promotion){
 	
 			foreach($goods_promotion as $k1=>$v1){
 				
 			if($v1['favorable']==1){
 				
 			$goodlink=$goodslink_mod->find(array(
 			'conditions'=>"rules_id='{$v1['id']}' AND favorable_id=1",
 			'fields'=>"favorable_value",
 			'index_key'=>"",
 					));
 					if($goodlink){
 							foreach($goodlink as $k2=>$v2){
 							$goods_lists[]=$v2['favorable_value'];
 							}
 							}
 								
 							if(in_array($goods_con['type_id'],$goods_lists)){
 	
 									if($v1['yhcase']==1){
 								
 							$goods_con['yhcase_id']=1;
 							$goods_con['yhcase_name']='促销';
 							$goods_con['yhcase']=($goods_con['price']*($v1['yhcase_value']/100));
 							$goods_con['content']=$v1['name'];
 							$goods_con['introduct']=$v1['introduct'];
 							}elseif($v1['yhcase']==2){
 							$goods_con['yhcase_id']=2;
 							$goods_con['yhcase_name']='促销';
 							$goods_con['yhcase']=intval($v1['yhcase_value']);
 							$goods_con['content']=$v1['name'];
 							$goods_con['introduct']=$v1['introduct'];
								}elseif($v1['yhcase']==3){
 							$goods_con['yhcase_id']=3;
 								$goods_con['yhcase_name']='促销';
 								$goods_con['yhcase']=($goods_con['price']-$goods_con['price']*($v1['yhcase_value']/100));
 									
 								$goods_con['content']=$v1['name'];
 								$goods_con['introduct']=$v1['introduct'];
 								}elseif($v1['yhcase']==4){
 								$goods_con['yhcase_id']=4;
 								$goods_con['yhcase_name']='促销';
 								$goods_con['yhcase']=($goods_con['price']-$v1['yhcase_value']);
 								$goods_con['content']=$v1['name'];
 								$goods_con['introduct']=$v1['introduct'];
 								}elseif($v1['yhcase']==5){
 									$goods_con['yhcase_id']=5;//免邮
 									$goods_con['yhcase_name']='免邮';
 									$goods_con['content']=$v1['name'];
 									$goods_con['introduct']=$v1['introduct'];
 								}
 									
 								}else{
 								$goods_con['yhcase_id']=0;
 								$goods_con['yhcase']=0;
 								$goods_con['content']='';
 									$goods_con['introduct']='';
 								}
 								break;
 								}elseif($v1['favorable']==2){
 										$goods_lists=$goods_prorel_mod->get(array(
 												'conditions'=>"c_id='{$goods_con['goods_id']}' AND d_id='{$v1['id']}'",
 								));
 									
 								if($goods_lists){
 								if($v1['yhcase']==1){
 								$goods_con['yhcase_id']=1;
 								$goods_con['yhcase_name']='促销';
 								$goods_con['yhcase']=($goods_con['price']*($v1['yhcase_value']/100));
 								$goods_con['content']=$v1['name'];
 								$goods_con['introduct']=$v1['introduct'];
 								}elseif($v1['yhcase']==2){
 									$goods_con['yhcase_id']=2;
 									$goods_con['yhcase_name']='促销';
 									$goods_con['yhcase']=intval($v1['yhcase_value']);
									$goods_con['content']=$v1['name'];
 										$goods_con['introduct']=$v1['introduct'];
 								}elseif($v1['yhcase']==3){
 								$goods_con['yhcase_id']=3;
 								$goods_con['yhcase_name']='促销';
 								$goods_con['yhcase']=($goods_con['price']-$goods_con['price']*($v1['yhcase_value']/100));
 								$goods_con['content']=$v1['name'];
 									$goods_con['introduct']=$v1['introduct'];
 									}elseif($v1['yhcase']==4){
 											$goods_con['yhcase_id']=4;
 													$goods_con['yhcase_name']='促销';
 															$goods_con['yhcase']=($goods_con['price']-$v1['yhcase_value']);
 																	$goods_con['content']=$v1['name'];
 																		$goods_con['introduct']=$v1['introduct'];
 																		}elseif($v1['yhcase']==5){
 																			$goods_con['yhcase_id']=5;//免邮
 																					$goods_con['yhcase_name']='免邮';
 																					$goods_con['content']=$v1['name'];
 																					$goods_con['introduct']=$v1['introduct'];
 																		}
 																		}else{
				$goods_con['yhcase_id']=0;
 					$goods_con['yhcase']=0;
 																		$goods_con['content']='';
 																		$goods_con['introduct']='';
 																		}
 																		break;
 																		}elseif($v1['favorable']==3){
 																				$goodlink=$goodslink_mod->find(array(
 																						'conditions'=>"rules_id='{$v1['id']}' AND favorable_id=3",
 																						));
 																						if($goodlink){
 																						foreach($goodlink as $k2=>$v2){
 																								$goodlinks[]=$v2['favorable_value'];
 																				}
 																		}
 																		if(in_array($goods_con['cat_id'],$goodlinks)){
			
 																				if($v1['yhcase']==1){
 																						$goods_con['yhcase_id']=1;
 																				$goods_con['yhcase_name']='促销';
 																				$goods_con['yhcase']=($goods_con['price']*($v1['yhcase_value']/100));
 																					$goods_con['content']=$v1['name'];
 																					$goods_con['introduct']=$v1['introduct'];
 																				}elseif($v1['yhcase']==2){
 																						$goods_con['yhcase_id']=2;
 																						$goods_con['yhcase_name']='促销';
 																						$goods_con['yhcase']=intval($v1['yhcase_value']);
 																						$goods_con['content']=$v1['name'];
 																						$goods_con['introduct']=$v1['introduct'];
 																						}elseif($v1['yhcase']==3){
 																						$goods_con['yhcase_id']=3;
 																						$goods_con['yhcase_name']='促销';
									$goods_con['yhcase']=($goods_con['price']-$goods_con['price']*($v1['yhcase_value']/100));
 										$goods_con['content']=$v1['name'];
 										$goods_con['introduct']=$v1['introduct'];
 										}elseif($v1['yhcase']==4){
 										$goods_con['yhcase_id']=4;
 												$goods_con['yhcase_name']='促销';
 														$goods_con['yhcase']=($goods_con['price']-$v1['yhcase_value']);
 														$goods_con['content']=$v1['name'];
 																$goods_con['introduct']=$v1['introduct'];
 										}elseif($v1['yhcase']==5){
 										$goods_con['yhcase_id']=5;//免邮
 										$goods_con['yhcase_name']='免邮';
 										$goods_con['content']=$v1['name'];
 										$goods_con['introduct']=$v1['introduct'];
								}
 										}else{
 										$goods_con['yhcase_id']=0;
 										$goods_con['yhcase']=0;
 										$goods_con['content']='';
 										$goods_con['introduct']='';
 										}
 										break;
 																						}elseif($v1['favorable']==4){
							if($v1['yhcase']==1){
 								$goods_con['yhcase_id']=1;
 								$goods_con['yhcase_name']='促销';
 								$goods_con['yhcase']=($goods_con['price']*($v1['yhcase_value']/100));
								$goods_con['content']=$v1['name'];
 											$goods_con['introduct']=$v1['introduct'];
 																						}elseif($v1['yhcase']==2){
 																						$goods_con['yhcase_id']=2;
 																						$goods_con['yhcase_name']='促销';
 																						$goods_con['yhcase']=intval($v1['yhcase_value']);
 																								$goods_con['content']=$v1['name'];
 																								$goods_con['introduct']=$v1['introduct'];
 																								}elseif($v1['yhcase']==3){
 																										$goods_con['yhcase_id']=3;
 																												$goods_con['yhcase_name']='促销';
 																												$goods_con['yhcase']=($goods_con['price']-$goods_con['price']*($v1['yhcase_value']/100));
 																												$goods_con['content']=$v1['name'];
 																														$goods_con['introduct']=$v1['introduct'];
 																												}elseif($v1['yhcase']==4){
 																												$goods_con['yhcase_id']=4;
 																														$goods_con['yhcase_name']='促销';
 																														$goods_con['yhcase']=($goods_con['price']-$v1['yhcase_value']);
 																												$goods_con['content']=$v1['name'];
 																												$goods_con['introduct']=$v1['introduct'];
 																												}elseif($v1['yhcase']==5){
 																												$goods_con['yhcase_id']=5;//免邮
 																												$goods_con['yhcase_name']='免邮';
 																												$goods_con['content']=$v1['name'];
 																														$goods_con['introduct']=$v1['introduct'];
 																												}
 																														break;
 																												}
 																															
 																												}
 																													
 																													
 																												}else{
				$goods_con['yhcase_id']=0;
 					$goods_con['yhcase']=0;
 																												$goods_con['content']='';
 																												$goods_con['introduct']='';
 							}
 								
 	
 							}else{
				$goods_con['yhcase_id']=0;
				$goods_con['yhcase']=0;
				$goods_con['content']='';
				$goods_con['introduct']='';
 	}
 		
 		$spec=$this->goodspec($goods_id);
//print_exit($goods_con);
 	$this->assign('goods_con',$goods_con);
 	$this->assign('spec',$spec);
 	
 	$this->display("product/content.html");
 }
//删除收藏

 function dropCollect(){
     $type = empty($_POST['type'])  ? 0 : intval($_POST['type']);
     $gid = empty($_POST['gid'])  ? 0 : intval($_POST['gid']);
  
     if (!$gid)
     {
         $this->json_error('删除收藏不存在！');
         return;
     }
 
     $collect_mod =& m('collect');
 
     $collect_mod->drop("item_id=".$gid." and user_id=".$this->visitor->get('user_id'));
     if($collect_mod->has_error()){
         $this->json_error('删除收藏失败！');
         return;
     }
 
     $this->json_result();
 }
    function content(){
        $user_info=$this->visitor->get();
        $goods_id = $_GET['id'];
        $goods_id = trim(stripslashes($goods_id),"'");
        $goods_mod = m('goods');
        $collect_mod=m("collect");
        if (!$goods_id) {
            $this->json_error('商品id不存在');
            die();
        }
        $goods_con = [];
        
        //=====  收藏  =====
        if($user_info['user_id'])
        {
            $member_lv=$user_info['member_lv_id'];
            if($member_lv){
                $conditiony=" AND FIND_IN_SET('{$member_lv}',member_lv_id) ";
            }
           
            //已收藏标识
            $collect=$collect_mod->get(array(
                'conditions'=>"user_id='{$user_info['user_id']}' AND type='goods' AND item_id={$goods_id} ",
            ));
            if($collect){
                $goods_con['is_collect']=1;
            }else{
                $goods_con['is_collect']=0;
            }

        }
        else{

            $member_lvs=m("memberlv");
            $user_infos=$member_lvs->get(array(
                'conditions' =>"1=1",

            ));

            $member_lv=$user_infos['member_lv_id'];
            if($member_lv){
                $conditiony=" AND FIND_IN_SET('{$member_lv}',member_lv_id) ";
            }
           
            $goods_con['is_collect']=0;
        }

        $model_obj=new Module();
        $config_obj=new Config();

        //=====  轮播图  =====
        $settings=$config_obj->get_settings();
        $banner_ads=$model_obj->getAds($settings['goods_banner']);
        $this->assign('banner',$banner_ads);
//echo '<pre>';print_r($banner_ads);exit;


        $goodsLib =  new Goods();
        $gcategoryLib = new Gcategory();
        $goodsInfo = $goodsLib->getGoodsInfo($goods_id);
        $goodsInfo['goods']['price'] = $goodsInfo['products']['pd'][$goodsInfo['products']['default_product_id']]['price'];
        $goodsInfo['goods']['mktprice'] = $goodsInfo['products']['pd'][$goodsInfo['products']['default_product_id']]['mktprice'];
        $goodsInfo['goods']['store'] = $goodsInfo['products']['pd'][$goodsInfo['products']['default_product_id']]['store'];

        $spec_desc = $goodsInfo['products']['pd'][$goodsInfo['products']['default_product_id']]['spec_desc'];
        $this->assign("is_pro",$goodsInfo['products']['pd'][$goodsInfo['products']['default_product_id']]['is_pro']);
        if($spec_desc)
        {
            foreach($spec_desc as $kk=>$vv)
            {
                $specvalue[$kk][]=$vv;

            }
            $goods_con['spec_value'] = array_values($spec_desc['spec_value']);
            $goods_con['spec_value_id'] = array_values($spec_desc['spec_value_id']);

        }
        else
        {
            $goods_con['spec_value']=[] ;
            $goods_con['spec_value_id']=[] ;
        }

        //--相关商品--//
        $cat_goods=$goods_mod->find(array(
            'conditions'=>"cat_id='{$goodsInfo['goods']['cat_id']}' AND goods_id != $goods_id ",
        ));
        $cat_goods= array_slice($cat_goods, 0, 4);
        
        //=====  规格  =====
        $spec=$this->goodspec($goods_id);
        $goodsInfo['goods']['intro'] = str_replace("/ueditor/php/upload/image/",PC_URL."/ueditor/php/upload/image/",$goodsInfo['goods']['intro']);


        $goods_con['cat_goods'] = $cat_goods;
        $this->assign('goods_con',$goods_con);
        $this->assign('goods',$goodsInfo);
        $this->display("product/content.html");
        return;


        if($goods_con){
            $goods_con['price']=($goods_con['price']);
            $goods_con['mktprice']=($goods_con['mktprice']);
            $goods_con['link_url']='';
        }


        //----评论---//
        $de_comments=$de_comment->get(array(
            'conditions'=>"comment_id='{$goods_id}' AND status=1",
            'fields' => 'count(*) as count'
        ));
        $de_goods=$de_comment->get(array(
            'conditions'=>"comment_id='{$goods_id}' AND status=1 AND star in (4,5)",
            'fields' => 'count(*) as count'
        ));
        if($de_goods['count'] && $de_comments['count']){
            $de_asses1=$de_goods['count']/$de_comments['count']*100;
            $de_asses=number_format($de_asses1, 2, '.', '');
        }else{
            $de_asses=0;
        }

        $goods_con['assess']=$de_asses;
        $goods_con['de_comments']=$de_comments['count'];
        //---属性（产品参数）--///
        $goods_con['goods_attr']=$goodsattr_mod->find(array(
            'conditions'=>"goods_id='{$goods_id}'",
            'join'    =>"belongs_attribute",
            'index_key'=>"",
        ));
        $new_time=time();
        $goods_promotion= $goods_promotion_mod->find(array(
            'conditions'=>"is_open = 1 AND find_in_set(2,site_id) AND '{$new_time}' >= starttime AND '{$new_time}' <= endtime ".$conditiony,
            'order'  =>"level ASC",
            'index_key'=>"",
        ));

        if($goods_promotion){

            $len=count($goods_promotion);
            for($i=1;$i<$len;$i++)
            {
                for($k=0;$k<$len-$i;$k++)
                {
                    if($goods_promotion[$k]['if_ex']<$goods_promotion[$k+1]['if_ex'])
                    {
                        $tmp=$goods_promotion[$k+1];
                        $goods_promotion[$k+1]=$goods_promotion[$k];
                        $goods_promotion[$k]=$tmp;
                    }
                }
            }

        }
        //--相关商品--//
        $cat_goods=$goods_mod->find(array(
            'conditions'=>"cat_id='{$goods_con['cat_id']}' AND goods_id != '{$goods_con['goods_id']}'",
        ));
        $cat_goods= array_slice($cat_goods, 0, 4);


        if($member_lv){
            if($cat_goods){
                foreach($cat_goods as $key=>$val){
                    $cat_goods[$key]['price']=($val['price']);
                    $cat_goods[$key]['mktprice']=($val['mktprice']);
                    if($goods_promotion){
                        foreach($goods_promotion as $k1=>$v1){

                            if($v1['favorable']==1){

                                $goodlink=$goodslink_mod->find(array(
                                    'conditions'=>"rules_id='{$v1['id']}' AND favorable_id=1",
                                    'fields'=>"favorable_value",
                                    'index_key'=>"",
                                ));
                                if($goodlink){
                                    foreach($goodlink as $k2=>$v2){
                                        $goods_lists[]=$v2['favorable_value'];
                                    }
                                }
                                if(in_array($val['type_id'],$goods_lists)){

                                    if($v1['yhcase']==1){
                                        $cat_goods[$key]['yhcase_id']=1;
                                        $cat_goods[$key]['yhcase_name']='促销';
                                        $cat_goods[$key]['yhcase']=($val['price']*($v1['yhcase_value']/100));
                                    }elseif($v1['yhcase']==2){
                                        $cat_goods[$key]['yhcase_id']=2;
                                        $cat_goods[$key]['yhcase_name']='促销';
                                        $cat_goods[$key]['yhcase']=intval($v1['yhcase_value']);
                                    }elseif($v1['yhcase']==3){
                                        $cat_goods[$key]['yhcase_id']=3;
                                        $cat_goods[$key]['yhcase_name']='促销';
                                        $cat_goods[$key]['yhcase']=($val['price']-$val['price']*($v1['yhcase_value']/100));
                                    }elseif($v1['yhcase']==4){
                                        $cat_goods[$key]['yhcase_id']=4;
                                        $cat_goods[$key]['yhcase_name']='促销';
                                        $cat_goods[$key]['yhcase']=($val['price']-$v1['yhcase_value']);
                                    }elseif($v1['yhcase']==5){
                                        $cat_goods[$key]['yhcase_name']='免邮';
                                        $cat_goods[$key]['yhcase_id']=5;//免邮
                                        $cat_goods[$key]['yhcase']=0;
                                    }

                                }else{
                                    $cat_goods[$key]['yhcase_id']=0;
                                    $cat_goods[$key]['yhcase']=0;
                                }
                                break;
                            }elseif($v1['favorable']==2){
                                $goods_lists=$goods_prorel_mod->get(array(
                                    'conditions'=>"c_id='{$val['goods_id']}' AND d_id='{$v1['id']}'",
                                ));

                                if($goods_lists){
                                    if($v1['yhcase']==1){
                                        $cat_goods[$key]['yhcase_id']=1;
                                        $cat_goods[$key]['yhcase_name']='促销';
                                        $cat_goods[$key]['yhcase']=($val['price']*($v1['yhcase_value']/100));
                                    }elseif($v1['yhcase']==2){
                                        $cat_goods[$key]['yhcase_id']=2;
                                        $cat_goods[$key]['yhcase_name']='促销';
                                        $cat_goods[$key]['yhcase']=($v1['yhcase_value']);
                                    }elseif($v1['yhcase']==3){
                                        $cat_goods[$key]['yhcase_id']=3;
                                        $cat_goods[$key]['yhcase_name']='促销';
                                        $cat_goods[$key]['yhcase']=($val['price']-$val['price']*($v1['yhcase_value']/100));
                                    }elseif($v1['yhcase']==4){
                                        $cat_goods[$key]['yhcase_id']=4;
                                        $cat_goods[$key]['yhcase_name']='促销';
                                        $cat_goods[$key]['yhcase']=($val['price']-$v1['yhcase_value']);
                                    }elseif($v1['yhcase']==5){
                                        $cat_goods[$key]['yhcase_name']='免邮';
                                        $cat_goods[$key]['yhcase_id']=5;//免邮
                                        $cat_goods[$key]['yhcase']=0;
                                    }
                                }else{
                                    $cat_goods[$key]['yhcase_id']=0;
                                    $cat_goods[$key]['yhcase']=0;
                                }
                                break;
                            }elseif($v1['favorable']==3){
                                $goodlink=$goodslink_mod->find(array(
                                    'conditions'=>"rules_id='{$v1['id']}' AND favorable_id=3",
                                ));
                                if($goodlink){
                                    foreach($goodlink as $k2=>$v2){
                                        $goodlinks[]=$v2['favorable_value'];
                                    }
                                }
                                if(in_array($val['cat_id'],$goodlinks)){

                                    if($v1['yhcase']==1){
                                        $cat_goods[$key]['yhcase_id']=1;
                                        $cat_goods[$key]['yhcase_name']='促销';
                                        $cat_goods[$key]['yhcase']=($val['price']*($v1['yhcase_value']/100));
                                    }elseif($v1['yhcase']==2){
                                        $cat_goods[$key]['yhcase_id']=2;
                                        $cat_goods[$key]['yhcase_name']='促销';
                                        $cat_goods[$key]['yhcase']=($v1['yhcase_value']);
                                    }elseif($v1['yhcase']==3){
                                        $cat_goods[$key]['yhcase_id']=3;
                                        $cat_goods[$key]['yhcase_name']='促销';
                                        $cat_goods[$key]['yhcase']=($val['price']-$val['price']*($v1['yhcase_value']/100));
                                    }elseif($v1['yhcase']==4){
                                        $cat_goods[$key]['yhcase_id']=4;
                                        $cat_goods[$key]['yhcase_name']='促销';
                                        $cat_goods[$key]['yhcase']=($val['price']-$v1['yhcase_value']);
                                    }elseif($v1['yhcase']==5){
                                        $cat_goods[$key]['yhcase_name']='免邮';
                                        $cat_goods[$key]['yhcase_id']=5;//免邮
                                        $cat_goods[$key]['yhcase']=0;
                                    }
                                }else{
                                    $cat_goods[$key]['yhcase_id']=0;
                                    $cat_goods[$key]['yhcase']=0;
                                }
                                break;
                            }elseif($v1['favorable']==4){
                                if($v1['yhcase']==1){
                                    $cat_goods[$key]['yhcase_id']=1;
                                    $cat_goods[$key]['yhcase_name']='促销';
                                    $cat_goods[$key]['yhcase']=($val['price']*($v1['yhcase_value']/100));
                                }elseif($v1['yhcase']==2){
                                    $cat_goods[$key]['yhcase_id']=2;
                                    $cat_goods[$key]['yhcase_name']='促销';
                                    $cat_goods[$key]['yhcase']=($v1['yhcase_value']);
                                }elseif($v1['yhcase']==3){
                                    $cat_goods[$key]['yhcase_id']=3;
                                    $cat_goods[$key]['yhcase_name']='促销';
                                    $cat_goods[$key]['yhcase']=($val['price']-$val['price']*($v1['yhcase_value']/100));
                                }elseif($v1['yhcase']==4){
                                    $cat_goods[$key]['yhcase_id']=4;
                                    $cat_goods[$key]['yhcase_name']='促销';
                                    $cat_goods[$key]['yhcase']=($val['price']-$v1['yhcase_value']);
                                }elseif($v1['yhcase']==5){
                                    $cat_goods[$key]['yhcase_name']='免邮';
                                    $cat_goods[$key]['yhcase_id']=5;//免邮
                                    $cat_goods[$key]['yhcase']=0;
                                }
                                break;
                            }

                        }
                    }else{
                        foreach($cat_goods as $key=>$val){
                            $cat_goods[$key]['yhcase_id']=0;
                            $cat_goods[$key]['yhcase']=0;
                        }
                    }

                }
            }
        }else{
            foreach($cat_goods as $key=>$val){
                $cat_goods[$key]['yhcase_id']=0;
                $cat_goods[$key]['yhcase']=0;
            }
        }





        $goods_con['cat_goods']=$cat_goods;



        //---相关商品结束--//
        //---相册----////
        $goods_con['gallery']=$goodsgallery_mod->find(array(
            'conditions' =>"goods_id='{$goods_id}'",
            'index_key'=>'',
			'order'=>'sort ASC ,img_id ASC',

        ));
        //---货品-----/
        $product=$products->get(array(
            'conditions'=>"goods_id='{$goods_id}' AND is_default=1",
        ));

        if($product['product_id']){
            $goods_con['product_id']=$product['product_id'];
        }
        if($product['price']){
            $goods_con['price']=($product['price']);
        }
        if($product['mktprice']){
            $goods_con['mktprice']=($product['mktprice']);
        }
        if($product['bn']){
            $goods_con['bn']=$product['bn'];
        }
        if($product['weight']){
            $goods_con['weight']=$product['weight'];
        }
        if($product['store']){
            $goods_con['store']=$product['store'];
        }else{
            $goods_con['store']=0;
        }

        $goods_con['order_price']=($goods_con['price']);
        $spec_desc=unserialize($product['spec_desc']);
        if($spec_desc){
            foreach($spec_desc as $kk=>$vv){
                $specvalue[$kk][]=$vv;

            }
            $goods_con['spec_value'] = array_values($spec_desc['spec_value']);
            $goods_con['spec_value_id'] = array_values($spec_desc['spec_value_id']);

        }else{
            $goods_con['spec_value']=[] ;
            $goods_con['spec_value_id']=[] ;
        }

        if($member_lv){
            //111111
            if($goods_promotion){

                foreach($goods_promotion as $k1=>$v1){

                    if($v1['favorable']==1){

                        $goodlink=$goodslink_mod->find(array(
                            'conditions'=>"rules_id='{$v1['id']}' AND favorable_id=1",
                            'fields'=>"favorable_value",
                            'index_key'=>"",
                        ));
                        if($goodlink){
                            foreach($goodlink as $k2=>$v2){
                                $goods_lists[]=$v2['favorable_value'];
                            }
                        }

                        if(in_array($goods_con['type_id'],$goods_lists)){

                            if($v1['yhcase']==1){

                                $goods_con['yhcase_id']=1;
                                $goods_con['yhcase_name']='促销';
                                $goods_con['yhcase']=($goods_con['price']*($v1['yhcase_value']/100));
                                $goods_con['content']=$v1['name'];
                                $goods_con['introduct']=$v1['introduct'];
                            }elseif($v1['yhcase']==2){
                                $goods_con['yhcase_id']=2;
                                $goods_con['yhcase_name']='促销';
                                $goods_con['yhcase']=($v1['yhcase_value']);
                                $goods_con['content']=$v1['name'];
                                $goods_con['introduct']=$v1['introduct'];
                            }elseif($v1['yhcase']==3){
                                $goods_con['yhcase_id']=3;
                                $goods_con['yhcase_name']='促销';
                                $goods_con['yhcase']=($goods_con['price']-$goods_con['price']*($v1['yhcase_value']/100));

                                $goods_con['content']=$v1['name'];
                                $goods_con['introduct']=$v1['introduct'];
                            }elseif($v1['yhcase']==4){
                                $goods_con['yhcase_id']=4;
                                $goods_con['yhcase_name']='促销';
                                $goods_con['yhcase']=($goods_con['price']-$v1['yhcase_value']);
                                $goods_con['content']=$v1['name'];
                                $goods_con['introduct']=$v1['introduct'];
                            }elseif($v1['yhcase']==5){
                                $goods_con['yhcase_id']=5;//免邮
                                $goods_con['yhcase_name']='免邮';
                                $goods_con['content']=$v1['name'];
                                $goods_con['introduct']=$v1['introduct'];
                            }

                        }else{
                            $goods_con['yhcase_id']=0;
                            $goods_con['yhcase']=0;
                            $goods_con['content']='';
                            $goods_con['introduct']='';
                        }
                        break;
                    }elseif($v1['favorable']==2){
                        $goods_lists=$goods_prorel_mod->get(array(
                            'conditions'=>"c_id='{$goods_con['goods_id']}' AND d_id='{$v1['id']}'",
                        ));

                        if($goods_lists){
                            if($v1['yhcase']==1){
                                $goods_con['yhcase_id']=1;
                                $goods_con['yhcase_name']='促销';
                                $goods_con['yhcase']=($goods_con['price']*($v1['yhcase_value']/100));
                                $goods_con['content']=$v1['name'];
                                $goods_con['introduct']=$v1['introduct'];
                            }elseif($v1['yhcase']==2){
                                $goods_con['yhcase_id']=2;
                                $goods_con['yhcase_name']='促销';
                                $goods_con['yhcase']=intval($v1['yhcase_value']);
                                $goods_con['content']=$v1['name'];
                                $goods_con['introduct']=$v1['introduct'];
                            }elseif($v1['yhcase']==3){
                                $goods_con['yhcase_id']=3;
                                $goods_con['yhcase_name']='促销';
                                $goods_con['yhcase']=($goods_con['price']-$goods_con['price']*($v1['yhcase_value']/100));
                                $goods_con['content']=$v1['name'];
                                $goods_con['introduct']=$v1['introduct'];
                            }elseif($v1['yhcase']==4){
                                $goods_con['yhcase_id']=4;
                                $goods_con['yhcase_name']='促销';
                                $goods_con['yhcase']=($goods_con['price']-$v1['yhcase_value']);
                                $goods_con['content']=$v1['name'];
                                $goods_con['introduct']=$v1['introduct'];
                            }elseif($v1['yhcase']==5){
                                $goods_con['yhcase_id']=5;//免邮
                                $goods_con['yhcase_name']='免邮';
                                $goods_con['content']=$v1['name'];
                                $goods_con['introduct']=$v1['introduct'];
                            }
                        }else{
                            $goods_con['yhcase_id']=0;
                            $goods_con['yhcase']=0;
                            $goods_con['content']='';
                            $goods_con['introduct']='';
                        }
                        break;
                    }elseif($v1['favorable']==3){
                        $goodlink=$goodslink_mod->find(array(
                            'conditions'=>"rules_id='{$v1['id']}' AND favorable_id=3",
                        ));
                        if($goodlink){
                            foreach($goodlink as $k2=>$v2){
                                $goodlinks[]=$v2['favorable_value'];
                            }
                        }
                        if(in_array($goods_con['cat_id'],$goodlinks)){

                            if($v1['yhcase']==1){
                                $goods_con['yhcase_id']=1;
                                $goods_con['yhcase_name']='促销';
                                $goods_con['yhcase']=($goods_con['price']*($v1['yhcase_value']/100));
                                $goods_con['content']=$v1['name'];
                                $goods_con['introduct']=$v1['introduct'];
                            }elseif($v1['yhcase']==2){
                                $goods_con['yhcase_id']=2;
                                $goods_con['yhcase_name']='促销';
                                $goods_con['yhcase']=intval($v1['yhcase_value']);
                                $goods_con['content']=$v1['name'];
                                $goods_con['introduct']=$v1['introduct'];
                            }elseif($v1['yhcase']==3){
                                $goods_con['yhcase_id']=3;
                                $goods_con['yhcase_name']='促销';
                                $goods_con['yhcase']=($goods_con['price']-$goods_con['price']*($v1['yhcase_value']/100));
                                $goods_con['content']=$v1['name'];
                                $goods_con['introduct']=$v1['introduct'];
                            }elseif($v1['yhcase']==4){
                                $goods_con['yhcase_id']=4;
                                $goods_con['yhcase_name']='促销';
                                $goods_con['yhcase']=($goods_con['price']-$v1['yhcase_value']);
                                $goods_con['content']=$v1['name'];
                                $goods_con['introduct']=$v1['introduct'];
                            }elseif($v1['yhcase']==5){
                                $goods_con['yhcase_id']=5;//免邮
                                $goods_con['yhcase_name']='免邮';
                                $goods_con['content']=$v1['name'];
                                $goods_con['introduct']=$v1['introduct'];
                            }
                        }else{
                            $goods_con['yhcase_id']=0;
                            $goods_con['yhcase']=0;
                            $goods_con['content']='';
                            $goods_con['introduct']='';
                        }
                        break;
                    }elseif($v1['favorable']==4){
                        if($v1['yhcase']==1){
                            $goods_con['yhcase_id']=1;
                            $goods_con['yhcase_name']='促销';
                            $goods_con['yhcase']=($goods_con['price']*($v1['yhcase_value']/100));
                            $goods_con['content']=$v1['name'];
                            $goods_con['introduct']=$v1['introduct'];
                        }elseif($v1['yhcase']==2){
                            $goods_con['yhcase_id']=2;
                            $goods_con['yhcase_name']='促销';
                            $goods_con['yhcase']=($v1['yhcase_value']);
                            $goods_con['content']=$v1['name'];
                            $goods_con['introduct']=$v1['introduct'];
                        }elseif($v1['yhcase']==3){
                            $goods_con['yhcase_id']=3;
                            $goods_con['yhcase_name']='促销';
                            $goods_con['yhcase']=($goods_con['price']-$goods_con['price']*($v1['yhcase_value']/100));
                            $goods_con['content']=$v1['name'];
                            $goods_con['introduct']=$v1['introduct'];
                        }elseif($v1['yhcase']==4){
                            $goods_con['yhcase_id']=4;
                            $goods_con['yhcase_name']='促销';
                            $goods_con['yhcase']=($goods_con['price']-$v1['yhcase_value']);
                            $goods_con['content']=$v1['name'];
                            $goods_con['introduct']=$v1['introduct'];
                        }elseif($v1['yhcase']==5){
                            $goods_con['yhcase_id']=5;//免邮
                            $goods_con['yhcase_name']='免邮';
                            $goods_con['content']=$v1['name'];
                            $goods_con['introduct']=$v1['introduct'];
                        }
                        break;
                    }

                }


            }else{
                $goods_con['yhcase_id']=0;
                $goods_con['yhcase']=0;
                $goods_con['content']='';
                $goods_con['introduct']='';
            }


        }else{
            $goods_con['yhcase_id']=0;
            $goods_con['yhcase']=0;
            $goods_con['content']='';
            $goods_con['introduct']='';
        }

        $spec=$this->goodspec($goods_id);
// print_exit($goods_con);
        $this->assign('goods_con',$goods_con);
        $this->assign('spec',$spec);

        $this->display("product/content.html");
    }

    /**
     *content
     *@author  liang.li
     */
    function checkp()
    {
        $pid = $_POST['hid'];
        $goodsId = $_POST['goodsId'];
        $num=$_POST['num'];
        $retp = explode(",", $pid);
        $productId = [];
        foreach ($retp as $key => $value)
        {
            if (!$value)
            {
                continue;
            }
            $val = explode("-", $value);
            $productId[$val[0]] = $val[1];
        }

        if (!$productId)
        {
            $this->json_error("参数为空");
            return;
        }
        ksort($productId);
        $productLib = new Products();
        $productList = $productLib->getProducts($goodsId);
        foreach ((array)$productList as $key => $value)
        {
            $spec = unserialize($value['spec_desc']);
            $specValueId = $spec['spec_value_id'];
            if (!array_diff($specValueId, $productId))
            {
                $defaultProductId = $key;
                break;
            }
        }
        $def = $productList[$defaultProductId];

        $spec_desc=unserialize($def['spec_desc']);
//        print_exit($spec_desc);
        if($spec_desc)
        {
//            foreach($spec_desc as $kk=>$vv)
//            {
//                $specvalue[$kk][]=$vv;
//
//            }
            $def['spec_value'] = array_values($spec_desc['spec_value']);
            $def['spec_value_count'] = count($def['spec_value']);
            $def['spec_value_id'] = array_values($spec_desc['spec_value_id']);
            $def['numbers'] = $num ;
        }
//print_exit($def);
        $this->json_result(array(
            'productLists'      =>  $def,
        ),'success');
        die();
    }


    //获取动态价格跟库存
 function priceaso(){
 	$num=$_POST['num'];
 	$spec_value_ids=$_POST['spec_value_ids'];
 	$spec_value_id=substr($spec_value_ids,0,-1);
 	$goods_id=$_POST['goods_id'];
 	$user_info=$this->visitor->get();
 	$products_mod=m("products");
 	$goods_mod=m("goods");
 	$goods_prorel_mod =& m('goods_prorel');
 	$goodslink_mod = &m('goods_prolink');
 	$member_mob=m("member");
 	$goods_promotion_mod =& m('goods_prorule');
 	$specvaluesMdl = m('specvalues');
 	if($num<0){
 		$num=1;
 	}
 	
 	if($user_info['user_id']){
 			
 		$member_lv=$user_info['member_lv_id'];
 		if($member_lv){
 		    $conditiony=" AND FIND_IN_SET('{$member_lv}',member_lv_id) ";
 		}
 		
 			
 	}else{
 		$member_lvs=m("memberlv");
 		$user_infos=$member_lvs->get(array(
 				'conditions' =>"1=1",
 					
 		));
 		$member_lv=$user_infos['member_lv_id'];
 		if($member_lv){
 		    $conditiony=" AND FIND_IN_SET('{$member_lv}',member_lv_id) ";
 		}
 		
 	
 	}
 
 	if (!$goods_id) {
 		$return = array(
 				'statusCode' => 0,
 				'error' => array(
 						'errorCode' => 101,
 						'msg'       => '商品id不存在',
 				)
 		);
 		return $json->encode($return);
 	}
 	$productList=$products_mod->find(array(
 			'conditions' =>"goods_id='{$goods_id}'",
 	));
 	
 	if (!$productList) {
 		$return = array(
 				'statusCode' => 0,
 				'error' => array(
 						'errorCode' => 101,
 						'msg'       => '商品不存在',
 				)
 		);
 		return $json->encode($return);
 	}
 	if(!$spec_value_id){
 		$return = array(
 				'statusCode' => 0,
 				'error' => array(
 						'errorCode' => 101,
 						'msg'       => '商品spec_value_id值未传',
 				)
 		);
 		return $json->encode($return);
 	}
 	$productIds=$specvaluesMdl->find(array(
 			'conditions' =>"spec_value_id in ({$spec_value_id})",
 	));
 	if($productIds){
 		foreach($productIds as $key=>$val)
 		{
 	
 			$productId[$val['spec_id']]=$key;
 		}
 	}else{
 	
 		$return = array(
 				'statusCode' => 0,
 				'error' => array(
 						'errorCode' => 101,
 						'msg'       => '未匹配到货品或spec_value_id传值错误',
 				)
 		);
 		return $json->encode($return);
 	
 	}
 	
 	
 		
 	foreach ((array)$productList as $key => $value)
 	{
 		$spec = unserialize($value['spec_desc']);
 		$specValueId = $spec['spec_value_id'];
 		if (!array_diff($specValueId, $productId))
 		{
 			$defaultProductId = $key;
 			break;
 		}
 	}
 	$productLists=$productList[$defaultProductId];
 	$goods_con=$goods_mod->get(array(
 			'conditions' =>"goods_id='{$productLists['goods_id']}'",
 	));
 	if($productLists){
 		$productLists['price']=($productLists['price']);
 		$productLists['mktprice']=($productLists['mktprice']);
 	}
 		
 	$new_time=time();
 	$goods_promotion= $goods_promotion_mod->find(array(
 			'conditions'=>"is_open = 1 AND find_in_set(2,site_id)  AND '{$new_time}' >= starttime AND '{$new_time}' <= endtime ".$conditiony,
 			'order'  =>"level ASC",
 			'index_key'=>"",
 	));
 	if($goods_promotion){
 			
 		$len=count($goods_promotion);
 		for($i=1;$i<$len;$i++)
 		{
 		for($k=0;$k<$len-$i;$k++)
 		{
 		if($goods_promotion[$k]['if_ex']<$goods_promotion[$k+1]['if_ex'])
 		{
 			$tmp=$goods_promotion[$k+1];
 			$goods_promotion[$k+1]=$goods_promotion[$k];
 			$goods_promotion[$k]=$tmp;
 		}
 		}
 		}
 			
 		}
 		if($member_lv){
 		if($goods_promotion){
 			
 		foreach($goods_promotion as $k1=>$v1){
 				
 			if($v1['favorable']==1){
 				
 			$goodlink=$goodslink_mod->find(array(
 					'conditions'=>"rules_id='{$v1['id']}' AND favorable_id=1",
 					'fields'=>"favorable_value",
 					'index_key'=>"",
 					));
 					if($goodlink){
 							foreach($goodlink as $k2=>$v2){
 									$goods_lists[]=$v2['favorable_value'];
 			}
 					}
 							
 						if(in_array($goods_con['type_id'],$goods_lists)){
 							
									if($v1['yhcase']==1){
 						$productLists['price']=($productLists['price']*($v1['yhcase_value']/100))*$num;
 						}elseif($v1['yhcase']==2){
 							$productLists['price']=($v1['yhcase_value'])*$num;
 						}elseif($v1['yhcase']==3){
 							$productLists['price']=($productLists['price']-$productLists['price']*($v1['yhcase_value']/100))*$num;
 						}elseif($v1['yhcase']==4){
 						$productLists['price']=($productLists['price']-$v1['yhcase_value'])*$num;
 						}elseif($v1['yhcase']==5){
 						$productLists['price']=($productLists['price'])*$num;
 						}
 						}
 							
 						break;
 			}elseif($v1['favorable']==2){
 					$goods_lists=$goods_prorel_mod->get(array(
 							'conditions'=>"c_id='{$goods_con['goods_id']}' AND d_id='{$v1['id']}'",
 			));
 				
 			if($goods_lists){
 			if($v1['yhcase']==1){
 			$productLists['price']=($productLists['price']*($v1['yhcase_value']/100))*$num;
 			}elseif($v1['yhcase']==2){
 			$productLists['price']=($v1['yhcase_value'])*$num;
 			}elseif($v1['yhcase']==3){
 			$productLists['price']=($productLists['price']-$productLists['price']*($v1['yhcase_value']/100))*$num;
 			}elseif($v1['yhcase']==4){
 					$productLists['price']=($productLists['price']-$v1['yhcase_value'])*$num;
 			}elseif($v1['yhcase']==5){
 			$productLists['price']=($productLists['price'])*$num;
 			}
 			}
 			break;
 			}elseif($v1['favorable']==3){
 			$goodlink=$goodslink_mod->find(array(
 					'conditions'=>"rules_id='{$v1['id']}' AND favorable_id=3",
 			));
 			if($goodlink){
 			foreach($goodlink as $k2=>$v2){
 			$goodlinks[]=$v2['favorable_value'];
 			}
 			}
 			if(in_array($goods_con['cat_id'],$goodlinks)){
 			if($v1['yhcase']==1){
 			$productLists['price']=($productLists['price']*($v1['yhcase_value']/100))*$num;
 			}elseif($v1['yhcase']==2){
 			$productLists['price']=($v1['yhcase_value'])*$num;
 			}elseif($v1['yhcase']==3){
										$productLists['price']=($productLists['price']-$productLists['price']*($v1['yhcase_value']/100))*$num;
 			}elseif($v1['yhcase']==4){
 			$productLists['price']=($productLists['price']-$v1['yhcase_value'])*$num;
 			}elseif($v1['yhcase']==5){
 				$productLists['price']=($productLists['price'])*$num;
 			}
 			}
 			break;
 			}elseif($v1['favorable']==4){
 			if($v1['yhcase']==1){
 			$productLists['price']=($productLists['price']*($v1['yhcase_value']/100))*$num;
 			}elseif($v1['yhcase']==2){
 				$productLists['price']=intval($v1['yhcase_value'])*$num;
 			}elseif($v1['yhcase']==3){
 				$productLists['price']=($productLists['price']-$productLists['price']*($v1['yhcase_value']/100))*$num;
 			}elseif($v1['yhcase']==4){
 			$productLists['price']=($productLists['price']-$v1['yhcase_value'])*$num;
 			}elseif($v1['yhcase']==5){
 			$productLists['price']=($productLists['price'])*$num;
 			}
 					break;
 			}
 					
 			}
 				
 				
 			}
 			}
 			$spec_desc=unserialize($productLists['spec_desc']);
 			if($spec_desc){
 				foreach($spec_desc as $kk=>$vv){
 					$specvalue[$kk][]=$vv;
 			
 				}
 				$productLists['spec_value'] = array_values($spec_desc['spec_value']);
 				$productLists['spec_value_count'] = count($productLists['spec_value']);
 				$productLists['spec_value_id'] = array_values($spec_desc['spec_value_id']);
 				$productLists['numbers'] = $num ;
 			}
//     print_exit($productLists);
 			$this->json_result(array(
 					'productLists'      =>  $productLists,
 			),'success');
 			die();
 }
 
 
 //获取规格
function goodspec($goodsId){
	$goods_mod =& m('goods');//商品表
	$goodstypespecMdl = m("goodstypespec");
	$products=m("products");
	$goodsspec_mod =& m('specification');
	$specvaluesMdl = m('specvalues');
	$goodsspec_mod =& m('specification');
	$goodsInfo = $goods_mod->get_info($goodsId);
		
	$typeId = $goodsInfo['type_id'];
	if($typeId){
		$goodstypespecList = $goodstypespecMdl->find(array(
				'conditions' => "type_id = $typeId",
		));
		$productsList=$products->find(array(
				'conditions' =>"goods_id='{$goodsId}'",
		));
		if($productsList){
				
			$speList = $goodsspec_mod->find(array(
					'conditions'=>"1=1",
			));
			$img = $specvaluesMdl->find(array(
					'conditions'=>"1=1",
			));
	
			foreach ($productsList as $key => $value)
			{
	
				if ($value['spec_desc'])
				{
					$spec = unserialize($value['spec_desc']);
					$productsList[$key]['spec_desc'] = $spec;
					foreach ($spec['spec_value_id'] as $key1 => $value1)
					{
						$bak = [];
						$tmp[$key1]['name'] = $speList[$key1]['spec_name'];
						if ($speList[$key1]['spec_type'] == "image")
						{
							$speImg[$value1] = $value1;
						}
						$tmp[$key1]['spec_type'] = $speList[$key1]['spec_type'];
						$tmp[$key1]['value'][$value1]['value_name'] = $spec['spec_value'][$key1];
						if ($value['is_default'])
						{
							$tmp[$key1]['value'][$value1]['is_default'] = $value['is_default'];
							$default_product_id = $key;
						}
						if ($speList[$key1]['spec_type'] == 'image')
						{
							$tmp[$key1]['value'][$value1]['image'] = $img[$key1]['spec_image'];
						}
					}
						
						
				}
					
					
			}
	
			if ($speImg)
			{
				$imgs = $specValuesMdl->find(array(
						'conditions' => db_create_in($speImg,"spec_value_id"),
						"fields"     => "spec_image",
				));
				foreach ($imgs as $key => $value)
				{
					$img[$key] = $value['spec_image'];
				}
					
			}
				
			foreach ((array)$tmp as $key => $value)
			{
				foreach ($value as $key1 => $value1)
				{
					;
				}
			}
			$productsList['spe'] = $tmp;
		}
	
	
		$specarr = i_array_column($goodstypespecList, "spec_id");
		$conditions = db_create_in($specarr,"spec_id");
		if ($goodstypespecList)
		{
			$speciList = $goodsspec_mod->find(array(
					'conditions' =>  $conditions,
					'index_key' =>"",
			));
			$specValList = $specvaluesMdl->find(array(
					'conditions' =>  "spec_values.".$conditions,
			));
				
			foreach ($specValList as $key => $value)
			{
					
				if ($productsList['spe'][$value['spec_id']]['value'][$key])
				{
					$value['is_check'] = 1;
				}
	
	
				if($productsList['spe'][$value['spec_id']]['value'][$value['spec_value_id']]['is_default']){
					$value['is_default']=1;
				}else{
					$value['is_default']=0;
				}
				if($speciList){
						
					foreach($speciList  as $k1=>$v1){
						if($value['spec_id'] == $v1['spec_id'] && $value['is_check']==1){
							$speciList[$k1]['val'][] = $value;
						}
					}
				}
	
			}
		}
	}
	return $speciList;
}




/* 商品评论 */
function comments()
{

	$goods_id = empty($_GET['id']) ? 0 : intval($_GET['id']);
	if (!$goods_id)
	{
		$this->show_warning('没有商品id');
		return;
	}
	$de_comment=m("detail_comment");//商品评价
	$detail_impression=m("detail_impression");
	$goods_mod =& m('goods');//商品评论表
	$goods_promotion_mod =& m('goods_prorule');
	$goods_prorel_mod =& m('goods_prorel');
	$goodslink_mod = &m('goods_prolink');
		
	$gooddata=$detail_impression->find(array(
			'conditions'=>"comment_id='{$goods_id}'",
			'index_key'=>'',
	));
	$page = $this->_get_page(30);
	setcookie('curr_page',$page["curr_page"]);
	$comment=$de_comment->find(array(
			'conditions'=>"comment_id='{$goods_id}'",
			'limit' =>$page['limit'],
			'join' =>"belongs_to_user",
			'fields'=>"member.avatar,detail_comments.nickname,detail_comments.content,detail_comments.star,detail_comments.addtime,detail_comments.hide_name,member.avatar",
			'index_key'=>"",
	));
		
	$this->assign('gooddata',$gooddata);//标签
	$this->assign('comment',$comment);//评价
	$this->display('product_comments.html');
}

}