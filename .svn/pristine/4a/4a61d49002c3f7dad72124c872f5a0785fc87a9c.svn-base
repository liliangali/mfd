<?php
use Cyteam\Shop\Type\Types;
/**---------------------------------------------------------------------
 *    商品相关接口
* ---------------------------------------------------------------------
* @author shaozz<shaozizhen94@163.com>
* 2016-5-27
*/
  class Product extends Result
	{
		
		/**
		 * 商品列表
		 *
		 * @param string $token 用户token;
		 *
		 * @access protected
		 * @author shaozizhen
		 */
		//http://local.soaapi.mfd.com/soap/product.php?act=goods_list
		//http://api.mfd.p.day900.com/soap/product.php?act=goods_list&token=2af4ea4f24bd16dae69b5f71d6941aaa&pageIndex=1&pageSize=9&sort=price&order=desc&p_id=128516
		function goods_list($data)
		{
			global $json;
			$token     = isset($data->token) ? $data->token: '';
			$pageSize  = isset($data->pageSize) ? $data->pageSize : 10;
			$pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
			$name=isset($data->name) ? $data->name : '';
			$sort=isset($data->sort) ? $data->sort : '';
			$order=isset($data->order) ? $data->order : '';
			$p_id=isset($data->p_id) ? $data->p_id : '';
			$c_id=isset($data->c_id) ? $data->c_id : '';
			$user_info = getUserInfo($token);
			$goods_mod =& m('goods');
			$goods_promotion_mod =& m('goods_prorule');
			$member_lvs=m("memberlv");
			$goods_prorel_mod =& m('goods_prorel');
			$goodslink_mod = &m('goods_prolink');
			$search_recordmod=m("search_record");
			$gcategorymod=m("gcategory");
			$product_mod=m("products");
			$conditions='';
			
			if(isset($name) && $name !=''){
				
				$searrecord=$search_recordmod->get(array(
						'conditions'=>"value = '{$name}'",
				));
				
				if($searrecord){
					$data=array(
							'user_id'=>$user_info['user_id'],
							'num'=>$searrecord['num']+1,
							'new_time'=>time(),
					);
					$search_recordmod->edit($searrecord['sid'],$data);
				}else{
					$data=array(
							'value'=>$name,
							'user_id'=>$user_info['user_id'],
							'num'=>1,
							'new_time'=>time(),
					);
					$search_recordmod->add($data);
				}
				
			
			
				$gcategorys=$gcategorymod->find(array(
						'conditions'=>"cate_name LIKE '%{$name}%'",
						'index_key'=>"cate_id",
				));
				
				if($gcategorys){
					foreach($gcategorys as $key=>$val)
					{
						$cates[]=$key;
					}
					$catess=implode(',', $cates);
				}
				
				if($catess){
					$conditions .= " AND cat_id in ('{$catess}') OR name LIKE '%{$name}%'";
				}else{
					$conditions .= " AND name LIKE '%{$name}%'";
				}
				
			    }


			if($c_id)
			{
			    $son_cate_list = $this->lists($c_id);
                if($son_cate_list) 
                {
                    $c_id_str = db_create_in(i_array_column($son_cate_list,'cate_id'),"cat_id");
                }
                else
                {
                    $c_id_str = "cat_id = $c_id";
                }
				$conditions .= " AND  $c_id_str ";
			}
			if($sort){
					$sorts=$sort;
                if($sort == 'buy_count')
                {
                    $order = "DESC";
                }
			}else{
				$sorts="p_order";
			}
			if($order){
				$orders=$order;
			}else{
				$orders="DESC";
			}
            if(!$sort)
            {
                $sortorder = "p_order DESC";
            }
            else
            {
                $sortorder = $sorts." ".$orders;
            }

//			echo '<pre>';print_r($sorts.$orders);exit;
			
			//$this->result =$conditions;
			//return $this->sresult();
			$limit = ($pageSize * ($pageIndex-1)) . ','. $pageSize;
			
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
			if($user_info){
				
				$member_lv=$user_info['member_lv_id'];
				$conditiony=" AND FIND_IN_SET('{$member_lv}',member_lv_id) ";
				
			}else{
				$user_infos=$member_lvs->get(array(
						'conditions' =>"1=1",
				
				));
				$member_lv=$user_infos['member_lv_id'];
				$conditiony=" AND FIND_IN_SET('{$member_lv}',member_lv_id) ";
			}
//	echo '<pre>';print_r($sortorder);exit;
	
			$goodlists=$goods_mod->find(array(
					'conditions'=>"marketable = 1".$conditions,
					'limit'	  => $limit,
					'count' => true,
					'order'=>$sortorder,
					'index_key'=>"",
			));
			
			$new_time=time();
			$goods_promotion= $goods_promotion_mod->find(array(
					'conditions'=>"is_open = 1 AND find_in_set(2,site_id)  AND '{$new_time}' >= starttime AND '{$new_time}' <= endtime ".$conditiony,
			        'order'  =>"level ASC",
			        'index_key'=>"",
			));
			///1111
			
			
			if($goodlists){
				foreach($goodlists as $key=>$val){
					$products=$product_mod->get(array(
							'conditions'=>"goods_id='{$val['goods_id']}' AND is_default=1",
					));
//					if($products){
//						$goodlists[$key]['order_price']=intval($products['price']);
//						$goodlists[$key]['price']=intval($products['price']);
//						$goodlists[$key]['mktprice']=intval($products['mktprice']);
//					}else{
//						$goodlists[$key]['order_price']=intval($val['price']);
//						$goodlists[$key]['price']=intval($val['price']);
//						$goodlists[$key]['mktprice']=intval($val['mktprice']);
//					}
				 
			     $goodlists[$key]['intro']=base64_encode($val['intro']);
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
							$goodlists[$key]['yhcase']=round($val['price']*($v1['yhcase_value']/100),2);
							$goodlists[$key]['order_price']=round($val['price']*($v1['yhcase_value']/100),2);
							}elseif($v1['yhcase']==2){
							$goodlists[$key]['yhcase_id']=2;
							$goodlists[$key]['yhcase_name']='促销';
							$goodlists[$key]['yhcase']=round($v1['yhcase_value'],2);
							$goodlists[$key]['order_price']=round($v1['yhcase_value'],2);
							}elseif($v1['yhcase']==3){
									$goodlists[$key]['yhcase_id']=3;
									$goodlists[$key]['yhcase_name']='促销';
											$goodlists[$key]['yhcase']=round($val['price']-$val['price']*($v1['yhcase_value']/100),2);
											$goodlists[$key]['order_price']=round($val['price']-$val['price']*($v1['yhcase_value']/100),2);
							}elseif($v1['yhcase']==4){
							$goodlists[$key]['yhcase_id']=4;
							$goodlists[$key]['yhcase_name']='促销';
									$goodlists[$key]['yhcase']=round($val['price']-$v1['yhcase_value'],2);
							$goodlists[$key]['order_price']=round($val['price']-$v1['yhcase_value'],2);
									}elseif($v1['yhcase']==5){
										$goodlists[$key]['yhcase_name']='免邮';
										$goodlists[$key]['yhcase_id']=5;//免邮
										$goodlists[$key]['yhcase']=0;
									}
									
							}else{
								$goodlists[$key]['yhcase_id']=0;
								$goodlists[$key]['yhcase']=0;
							}
							//break;
							}elseif($v1['favorable']==2){
							$goods_lists=$goods_prorel_mod->get(array(
									'conditions'=>"c_id='{$val['goods_id']}' AND d_id='{$v1['id']}'",
							));
												
							if($goods_lists){
							if($v1['yhcase']==1){
							$goodlists[$key]['yhcase_id']=1;
							$goodlists[$key]['yhcase_name']='促销';
							$goodlists[$key]['yhcase']=round($val['price']*($v1['yhcase_value']/100),2);
							$goodlists[$key]['order_price']=round($val['price']*($v1['yhcase_value']/100),2);
							}elseif($v1['yhcase']==2){
							$goodlists[$key]['yhcase_id']=2;
							$goodlists[$key]['yhcase_name']='促销';
								$goodlists[$key]['yhcase']=round($v1['yhcase_value'],2);
								$goodlists[$key]['order_price']=round($v1['yhcase_value'],2);
							}elseif($v1['yhcase']==3){
							$goodlists[$key]['yhcase_id']=3;
							$goodlists[$key]['yhcase_name']='促销';
							$goodlists[$key]['yhcase']=round($val['price']-$val['price']*($v1['yhcase_value']/100),2);
							$goodlists[$key]['order_price']=round($val['price']-$val['price']*($v1['yhcase_value']/100),2);
								}elseif($v1['yhcase']==4){
										$goodlists[$key]['yhcase_id']=4;
										$goodlists[$key]['yhcase_name']='促销';
										$goodlists[$key]['yhcase']=round($val['price']-$v1['yhcase_value'],2);
								$goodlists[$key]['order_price']=round($val['price']-$v1['yhcase_value'],2);
								}elseif($v1['yhcase']==5){
									$goodlists[$key]['yhcase_name']='免邮';
								$goodlists[$key]['yhcase_id']=5;//免邮
								$goodlists[$key]['yhcase']=0;
								}
								}else{
								$goodlists[$key]['yhcase_id']=0;
								$goodlists[$key]['yhcase']=0;
							}
							//break;
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
										$goodlists[$key]['yhcase']=round($val['price']*($v1['yhcase_value']/100),2);
										$goodlists[$key]['order_price']=round($val['price']*($v1['yhcase_value']/100),2);
										}elseif($v1['yhcase']==2){
										$goodlists[$key]['yhcase_id']=2;
										$goodlists[$key]['yhcase_name']='促销';
												$goodlists[$key]['yhcase']=round($v1['yhcase_value'],2);
												$goodlists[$key]['order_price']=round($v1['yhcase_value'],2);
										}elseif($v1['yhcase']==3){
										$goodlists[$key]['yhcase_id']=3;
										$goodlists[$key]['yhcase_name']='促销';
										$goodlists[$key]['yhcase']=round($val['price']-$val['price']*($v1['yhcase_value']/100),2);
												$goodlists[$key]['order_price']=round($val['price']-$val['price']*($v1['yhcase_value']/100),2);
										}elseif($v1['yhcase']==4){
										$goodlists[$key]['yhcase_id']=4;
										$goodlists[$key]['yhcase_name']='促销';
										$goodlists[$key]['yhcase']=round($val['price']-$v1['yhcase_value'],2);
										$goodlists[$key]['order_price']=round($val['price']-$v1['yhcase_value'],2);
										}elseif($v1['yhcase']==5){
											$goodlists[$key]['yhcase_name']='免邮';
										$goodlists[$key]['yhcase_id']=5;//免邮
										$goodlists[$key]['yhcase']=0;
										}
										}else{
								$goodlists[$key]['yhcase_id']=0;
								$goodlists[$key]['yhcase']=0;
							}
							//break;
							}elseif($v1['favorable']==4){
											if($v1['yhcase']==1){
											$goodlists[$key]['yhcase_id']=1;
											$goodlists[$key]['yhcase_name']='促销';
													$goodlists[$key]['yhcase']=round($val['price']*($v1['yhcase_value']/100),2);
															$goodlists[$key]['order_price']=round($val['price']*($v1['yhcase_value']/100),2);
													}elseif($v1['yhcase']==2){
															$goodlists[$key]['yhcase_id']=2;
															$goodlists[$key]['yhcase_name']='促销';
													$goodlists[$key]['yhcase']=round($v1['yhcase_value'],2);
													$goodlists[$key]['order_price']=round($v1['yhcase_value'],2);
					}elseif($v1['yhcase']==3){
														$goodlists[$key]['yhcase_id']=3;
														$goodlists[$key]['yhcase_name']='促销';
														$goodlists[$key]['yhcase']=round($val['price']-$val['price']*($v1['yhcase_value']/100),2);
											$goodlists[$key]['order_price']=round($val['price']-$val['price']*($v1['yhcase_value']/100),2);
													}elseif($v1['yhcase']==4){
														$goodlists[$key]['yhcase_id']=4;
														$goodlists[$key]['yhcase_name']='促销';
														$goodlists[$key]['yhcase']=round($val['price']-$v1['yhcase_value'],2);
														$goodlists[$key]['order_price']=round($val['price']-$v1['yhcase_value'],2);
														}elseif($v1['yhcase']==5){
															$goodlists[$key]['yhcase_name']='免邮';
																$goodlists[$key]['yhcase_id']=5;//免邮
																$goodlists[$key]['yhcase']=0;
														}
														//break;
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
		
			
			$this->result =array(
					'goodlists'=>$goodlists,
					'list'=>$gcategoryLib1,
					//'list2'=>$gcategoryLib2
			         );
			return $this->sresult();
		}
		
		function lists($pId)
		{
			$gcategory = m('gcategory');
			/*  $list = $gcategory->find(array(
					'conditions'  => "if_show=1 AND parent_id='{$pId}'",
					'index_key'   => " ",
					'order'       => "sort_order ASC",
			)); */ 
			$list = $gcategory->get_li($pId,1);
			
			return $list;
		}
		//购物车数量
		//http://local.soaapi.mfd.com/soap/product.php?act=cartcount&goods_id=102&token=86c64670cf009e91d7fa42804060c212
		//http://api.mfd.p.day900.com/soap/product.php?act=cartcount&goods_id=116
		function cartcount($data)
		{
			global $json;
			$token     = isset($data->token) ? $data->token: '';
			$cart_mod = m('cart');
			$user_info = getUserInfo($token);
			$user_id=$user_info['user_id'];
			if($user_id){
				$cartcount = $cart_mod->find(array(//=====   购物车数量 =====
					'conditions' => "user_id = '{$user_id}' AND source_from='app' ",
					//'fields'     => "count(*) as rec_id",
			));
			$count=count($cartcount);
			}else{
			$count=0;	
			}
			
			$this->result =$count;
			return $this->sresult();
		}
		//商品详情
		//http://local.soaapi.mfd.com/soap/product.php?act=goodscontent&goods_id=102&token=86c64670cf009e91d7fa42804060c212
		//http://api.mfd.p.day900.com/soap/product.php?act=goodsspec&goods_id=116
		
		function goodscontent($data){
			global $json;
			$token     = isset($data->token) ? $data->token: '';
			$goods_id     = isset($data->goods_id) ? $data->goods_id: '';
			$user_info = getUserInfo($token);
			$de_comment=m("detail_comment");
			$goodsspec_mod =& m('specification');
			$goods_mod =& m('goods');//商品表
			$goodsgallery_mod=m("goodsgallery");//商品相册表
			$products=m("products");//货品表
			$goods_promotion_mod =& m('goods_prorule');
			$goods_prorel_mod =& m('goods_prorel');
			$goodslink_mod = &m('goods_prolink');
			$goodsattr_mod=m("goodsattr");
			$collect_mod=m("collect");
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
			$goods_con=$goods_mod->get(array(
					'conditions'=>"marketable=1 AND goods_id='{$goods_id}'",
						
			));
		
			if($goods_con['intro']){
			    $src='/src="\//';
			    $on='src="'.SITE_URL.'/';
			    $ins=preg_replace($src,$on,$goods_con['intro']);
			    $src1='/<img/';
			    $on1='<img style="max-width:100%;"';
			    $ins1=preg_replace($src1,$on1,$ins);
			    $goods_con['intro']=$ins1;
			}
			
			if($user_info){
				$member_lv=$user_info['member_lv_id'];
				$conditiony=" AND FIND_IN_SET('{$member_lv}',member_lv_id) ";
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
				$conditiony=" AND FIND_IN_SET('{$member_lv}',member_lv_id) ";
				$goods_con['is_collect']=0;
			}
			
			
			
			if($goods_con){
					$goods_con['price']=($goods_con['price']);
					$goods_con['mktprice']=($goods_con['mktprice']);
					$goods_con['intro']=base64_encode($goods_con['intro']);
					$goods_con['link_url']= H5_URL.'product-content.html?id='.$goods_id;
			}


			/*========================*
			 分享
			*=========================*/
			
			$share_ios = "http://h5.myfoodiepet.com/product-content.html?id='{$goods_id}'|||".$goods_con['s_img'];
			//$shareAndroid=app.share(true,MHOST."product-content.html?id='{$goods_id}'|||".$goods_con['thumbnail_pic']);
			//----评论---//	
			$de_comments=$de_comment->get(array(
					'conditions'=>"comment_id='{$goods_id}' ",
					'fields' => 'count(*) as count'
							));
			$de_goods=$de_comment->get(array(
					'conditions'=>"comment_id='{$goods_id}'  AND star in (4,5)",
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
			$goodsattrss=$goodsattr_mod->find(array(
					'conditions'=>"goods_id='{$goods_id}'",
					'join'    =>"belongs_attribute",
					'index_key'=>"",
			));
			if($goodsattrss){
				foreach($goodsattrss as $key=>$val)
				{
					$goodsats[$key]=array_filter($val);
				}
				$goods_con['goods_attr']=$goodsats;
			}else{
				$goods_con['goods_attr']=[];
			}

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
						$cat_goods[$key]['intro']=base64_encode($val['intro']);
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
						$cat_goods[$key]['yhcase']=round($val['price']*($v1['yhcase_value']/100),2);
							}elseif($v1['yhcase']==2){
							$cat_goods[$key]['yhcase_id']=2;
							$cat_goods[$key]['yhcase_name']='促销';
							$cat_goods[$key]['yhcase']=round($v1['yhcase_value'],2);
						}elseif($v1['yhcase']==3){
								$cat_goods[$key]['yhcase_id']=3;
								$cat_goods[$key]['yhcase_name']='促销';
								$cat_goods[$key]['yhcase']=round($val['price']-$val['price']*($v1['yhcase_value']/100),2);
							}elseif($v1['yhcase']==4){
								$cat_goods[$key]['yhcase_id']=4;
								$cat_goods[$key]['yhcase_name']='促销';
										$cat_goods[$key]['yhcase']=round($val['price']-$v1['yhcase_value'],2);
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
													$cat_goods[$key]['yhcase']=round($val['price']*($v1['yhcase_value']/100),2);
							}elseif($v1['yhcase']==2){
										$cat_goods[$key]['yhcase_id']=2;
										$cat_goods[$key]['yhcase_name']='促销';
										$cat_goods[$key]['yhcase']=round($v1['yhcase_value'],2);
														}elseif($v1['yhcase']==3){
														$cat_goods[$key]['yhcase_id']=3;
						            	$cat_goods[$key]['yhcase_name']='促销';
										$cat_goods[$key]['yhcase']=round($val['price']-$val['price']*($v1['yhcase_value']/100),2);
														}elseif($v1['yhcase']==4){
														$cat_goods[$key]['yhcase_id']=4;
																$cat_goods[$key]['yhcase_name']='促销';
																	$cat_goods[$key]['yhcase']=round($val['price']-$v1['yhcase_value'],2);
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
																				$cat_goods[$key]['yhcase']=round($val['price']*($v1['yhcase_value']/100),2);
																}elseif($v1['yhcase']==2){
																$cat_goods[$key]['yhcase_id']=2;
																$cat_goods[$key]['yhcase_name']='促销';
																$cat_goods[$key]['yhcase']=round($v1['yhcase_value'],2);
																}elseif($v1['yhcase']==3){
																$cat_goods[$key]['yhcase_id']=3;
																$cat_goods[$key]['yhcase_name']='促销';
																		$cat_goods[$key]['yhcase']=round($val['price']-$val['price']*($v1['yhcase_value']/100),2);
																}elseif($v1['yhcase']==4){
																$cat_goods[$key]['yhcase_id']=4;
																$cat_goods[$key]['yhcase_name']='促销';
																$cat_goods[$key]['yhcase']=round($val['price']-$v1['yhcase_value'],2);
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
																		$cat_goods[$key]['yhcase']=round($val['price']*($v1['yhcase_value']/100),2);
																		}elseif($v1['yhcase']==2){
																		$cat_goods[$key]['yhcase_id']=2;
																				$cat_goods[$key]['yhcase_name']='促销';
																				$cat_goods[$key]['yhcase']=round($v1['yhcase_value'],2);
																				}elseif($v1['yhcase']==3){
																						$cat_goods[$key]['yhcase_id']=3;
																								$cat_goods[$key]['yhcase_name']='促销';
																								$cat_goods[$key]['yhcase']=round($val['price']-$val['price']*($v1['yhcase_value']/100),2);
																								}elseif($v1['yhcase']==4){
																										$cat_goods[$key]['yhcase_id']=4;
																										$cat_goods[$key]['yhcase_name']='促销';
																										$cat_goods[$key]['yhcase']=round($val['price']-$v1['yhcase_value'],2);
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
				}
			
			$goods_con['order_price']=round($goods_con['price'],2);
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
									$goods_con['yhcase']=round($goods_con['price']*($v1['yhcase_value']/100),2);
									$goods_con['content']=$v1['name'];
									$goods_con['introduct']=$v1['introduct'];
								}elseif($v1['yhcase']==2){
									$goods_con['yhcase_id']=2;
									$goods_con['yhcase_name']='促销';
									$goods_con['yhcase']=round($v1['yhcase_value'],2);
									$goods_con['content']=$v1['name'];
									$goods_con['introduct']=$v1['introduct'];
								}elseif($v1['yhcase']==3){
									$goods_con['yhcase_id']=3;
									$goods_con['yhcase_name']='促销';
									$goods_con['yhcase']=round($goods_con['price']-$goods_con['price']*($v1['yhcase_value']/100),2);
									$goods_con['content']=$v1['name'];
									$goods_con['introduct']=$v1['introduct'];
								}elseif($v1['yhcase']==4){
									$goods_con['yhcase_id']=4;
									$goods_con['yhcase_name']='促销';
									$goods_con['yhcase']=round($goods_con['price']-$v1['yhcase_value'],2);
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
									$goods_con['yhcase']=round($goods_con['price']*($v1['yhcase_value']/100),2);
									$goods_con['content']=$v1['name'];
									$goods_con['introduct']=$v1['introduct'];
								}elseif($v1['yhcase']==2){
									$goods_con['yhcase_id']=2;
									$goods_con['yhcase_name']='促销';
									$goods_con['yhcase']=round($v1['yhcase_value'],2);
									$goods_con['content']=$v1['name'];
									$goods_con['introduct']=$v1['introduct'];
								}elseif($v1['yhcase']==3){
									$goods_con['yhcase_id']=3;
									$goods_con['yhcase_name']='促销';
									$goods_con['yhcase']=round($goods_con['price']-$goods_con['price']*($v1['yhcase_value']/100),2);
									$goods_con['content']=$v1['name'];
									$goods_con['introduct']=$v1['introduct'];
								}elseif($v1['yhcase']==4){
									$goods_con['yhcase_id']=4;
									$goods_con['yhcase_name']='促销';
									$goods_con['yhcase']=round($goods_con['price']-$v1['yhcase_value'],2);
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
								$goods_con['yhcase']=round($goods_con['price']*($v1['yhcase_value']/100),2);
								$goods_con['content']=$v1['name'];
								$goods_con['introduct']=$v1['introduct'];
							}elseif($v1['yhcase']==2){
								$goods_con['yhcase_id']=2;
								$goods_con['yhcase_name']='促销';
								$goods_con['yhcase']=round($v1['yhcase_value'],2);
								$goods_con['content']=$v1['name'];
								$goods_con['introduct']=$v1['introduct'];
							}elseif($v1['yhcase']==3){
								$goods_con['yhcase_id']=3;
								$goods_con['yhcase_name']='促销';
								$goods_con['yhcase']=round($goods_con['price']-$goods_con['price']*($v1['yhcase_value']/100),2);
								$goods_con['content']=$v1['name'];
								$goods_con['introduct']=$v1['introduct'];
							}elseif($v1['yhcase']==4){
								$goods_con['yhcase_id']=4;
								$goods_con['yhcase_name']='促销';
								$goods_con['yhcase']=round($goods_con['price']-$v1['yhcase_value'],2);
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
			
			$goods_con['share_ios']=$share_ios;
			
			$this->result =$goods_con;
			return $this->sresult();
			
		}
		//商品规格接口
		function goodsspec_bak($data){
			global $json;
			$goodsspec_mod =& m('specification');
			$goodstypespec =& m('goodstypespec');
			$specvalues=m("specvalues");
			$goods_mod=m("goods");
			$token     = isset($data->token) ? $data->token: '';
			$goods_id     = isset($data->goods_id) ? $data->goods_id: '';
			$goods_con=$goods_mod->get(array(
					'conditions'=>"marketable=1 AND goods_id='{$goods_id}'",
						
			));
			$types=$goodstypespec->find(array(
					'conditions'=>"type_id='{$goods_con['type_id']}'",
					'join'=>"has_specification",
					'index_key' =>"",
			));
			 if($types){
				foreach($types as $key=>$val)
				{
					$types[$key]['spec_values']=$specvalues->find(array(
							'conditions'=>"spec_id='{$val['spec_id']}'",
							'index_key' =>"",
							//'fields'=>"spec_name,spec_type,p_order",
					));
				}
			} 
			$this->result =$types;
			return $this->sresult();
			
		}
		//
		function goodsspec($data){
			global $json;
			$products=m("products");//货品表
			$specificationMdl = m("specification");
			$goodstypespecMdl = m("goodstypespec");
			$specvaluesMdl = m('specvalues');
			$goodsMdl=m("goods");
			$token     = isset($data->token) ? $data->token: '';
			$goodsId     = isset($data->goods_id) ? $data->goods_id: '';
			$goodsInfo = $goodsMdl->get_info($goodsId);
			
			$typeId = $goodsInfo['type_id'];
			if($typeId){
				$goodstypespecList = $goodstypespecMdl->find(array(
						'conditions' => "type_id = $typeId",
				));
				$productsList=$products->find(array(
						'conditions' =>"goods_id='{$goodsId}'",
				));
				
				if($productsList){
					
					$speList = $specificationMdl->find(array(
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
								//                    $bak[$value1] = $spec['spec_value'][$key1];
								$tmp[$key1]['value'][$value1]['value_name'] = $spec['spec_value'][$key1];
								if ($value['is_default'])
								{
									$tmp[$key1]['value'][$value1]['is_default'] = $value['is_default'];
									$default_product_id = $key;
								}
								if ($speList[$key1]['spec_type'] == 'image')
								{
									 $tmp[$key1]['value'][$value1]['iamge'] = $img[$key1]['spec_image']; 
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
					$speciList = $specificationMdl->find(array(
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
			
			$this->result =$speciList;
			return $this->sresult();
				
		}
		function access($data){
			global $json;
			$token     = isset($data->token) ? $data->token: '';
			$goods_id     = isset($data->goods_id) ? $data->goods_id: '';
			$pageSize  = isset($data->pageSize) ? $data->pageSize : 10;
			$pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
			$user_info = getUserInfo($token);
			$de_comment=m("detail_comment");//商品评价
			$detail_impression=m("detail_impression");
			$goods_mod =& m('goods');//商品评论表
			//
			$limit = ($pageSize * ($pageIndex-1)) . ','. $pageSize;
			$goods_promotion_mod =& m('goods_prorule');
			$goods_prorel_mod =& m('goods_prorel');
			$goodslink_mod = &m('goods_prolink');
			
			$gooddata=$detail_impression->find(array(
					'conditions'=>"comment_id='{$goods_id}'",
					'index_key'=>'',
			));
			
			$comment=$de_comment->find(array(
					'conditions'=>"comment_id='{$goods_id}'",
					'limit' =>$limit,
					'join' =>"belongs_to_user",
					'fields'=>"member.avatar,detail_comments.nickname,detail_comments.content,detail_comments.star,detail_comments.addtime,detail_comments.hide_name,member.avatar",
					'index_key'=>"",
			));
			
			$this->result =array(
					'impression'=>$gooddata,
					'comment'=>$comment,
			);
			return $this->sresult();
		}
		//组合计算货品价格
		//http://local.soaapi.mfd.com/soap/product.php?act=priceaso&goods_id=102&spec_value_id=63,66
		function priceaso($data){
			global $json;
			$token     = isset($data->token) ? $data->token: '';
			$goods_id     = isset($data->goods_id) ? $data->goods_id: '';
			$spec_value_id =isset($data->spec_value_id) ? $data->spec_value_id: '';
			$num     = isset($data->num) ? $data->num: '1';
			$user_info = getUserInfo($token);
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
			if($user_info){
			
				$member_lv=$user_info['member_lv_id'];
				$conditiony=" AND FIND_IN_SET('{$member_lv}',member_lv_id) ";
			
			}else{
				$member_lvs=m("memberlv");
				$user_infos=$member_lvs->get(array(
						'conditions' =>"1=1",
							
				));
				$member_lv=$user_infos['member_lv_id'];
				$conditiony=" AND FIND_IN_SET('{$member_lv}',member_lv_id) ";
				
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
				$productLists['price']=round($productLists['price'],2);
				$productLists['mktprice']=round($productLists['mktprice'],2);
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
										$productLists['price']=round($productLists['price']*($v1['yhcase_value']/100),2)*$num;
									}elseif($v1['yhcase']==2){
										$productLists['price']=round($v1['yhcase_value'],2)*$num;
									}elseif($v1['yhcase']==3){
										$productLists['price']=round($productLists['price']-$productLists['price']*($v1['yhcase_value']/100),2)*$num;
									}elseif($v1['yhcase']==4){
										$productLists['price']=round($productLists['price']-$v1['yhcase_value'],2)*$num;
									}elseif($v1['yhcase']==5){
										$productLists['price']=round($productLists['price'],2)*$num;
									}
								}
									
								break;
							}elseif($v1['favorable']==2){
								$goods_lists=$goods_prorel_mod->get(array(
										'conditions'=>"c_id='{$goods_con['goods_id']}' AND d_id='{$v1['id']}'",
								));
					
								if($goods_lists){
									if($v1['yhcase']==1){
										$productLists['price']=round($productLists['price']*($v1['yhcase_value']/100),2)*$num;
									}elseif($v1['yhcase']==2){
										$productLists['price']=round($v1['yhcase_value'],2)*$num;
									}elseif($v1['yhcase']==3){
										$productLists['price']=round($productLists['price']-$productLists['price']*($v1['yhcase_value']/100),2)*$num;
									}elseif($v1['yhcase']==4){
										$productLists['price']=round($productLists['price']-$v1['yhcase_value'],2)*$num;
									}elseif($v1['yhcase']==5){
										$productLists['price']=round($productLists['price'],2)*$num;
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
										$productLists['price']=round($productLists['price']*($v1['yhcase_value']/100),2)*$num;
									}elseif($v1['yhcase']==2){
										$productLists['price']=round($v1['yhcase_value'],2)*$num;
									}elseif($v1['yhcase']==3){
										$productLists['price']=round($productLists['price']-$productLists['price']*($v1['yhcase_value']/100),2)*$num;
									}elseif($v1['yhcase']==4){
										$productLists['price']=round($productLists['price']-$v1['yhcase_value'],2)*$num;
									}elseif($v1['yhcase']==5){
										$productLists['price']=round($productLists['price'],2)*$num;
									}
								}
								break;
							}elseif($v1['favorable']==4){
								if($v1['yhcase']==1){
									$productLists['price']=round($productLists['price']*($v1['yhcase_value']/100),2)*$num;
								}elseif($v1['yhcase']==2){
									$productLists['price']=round($v1['yhcase_value'],2)*$num;
								}elseif($v1['yhcase']==3){
									$productLists['price']=round($productLists['price']-$productLists['price']*($v1['yhcase_value']/100),2)*$num;
								}elseif($v1['yhcase']==4){
									$productLists['price']=round($productLists['price']-$v1['yhcase_value'],2)*$num;
								}elseif($v1['yhcase']==5){
									$productLists['price']=round($productLists['price'],2)*$num;
								}
								break;
							}
					
						}
							
							
					}
				}
				
				
			$this->result =$productLists;
			return $this->sresult();
		
		}
		
		/* 	$postdata = http_build_query($post_data);
		 	
		$options = array(
				'http' => array(
						'method' => 'POST',
						'header' => 'Content-type:application/x-www-form-urlencoded',
						'content' => $postdata,
						'timeout' => 15 * 60 // 超时时间（单位:s）
				)
		);
			
		$context = stream_context_create($options);
		$this->result =$context;
		return $this->sresult();
		$result = file_get_contents($url, false, $context); */
		
		
		//加入购物车
		//http://local.soaapi.mfd.com/soap/product.php?act=addcart&token=86c64670cf009e91d7fa42804060c212&goods_id=102&spec_value_id=63,66&num=1
		//http://api.mfd.p.day900.com/soap/product.php?act=addcart&token=86c64670cf009e91d7fa42804060c212&goods_id=101&spec_value_id=39,61&num=2
		
		function addcart($data){
            include_once ROOT_PATH.'/vendor/autoload.php';
			global $json;
			$token     = isset($data->token) ? $data->token: '';
			$goods_id     = isset($data->goods_id) ? $data->goods_id: '';
			$spec_value_id     = isset($data->spec_value_id) ? $data->spec_value_id: '';
			$num     = isset($data->num) ? $data->num: '1';
			$user_info = getUserInfo($token);
			$specvaluesMdl = m('specvalues');
			$products_mod= m('products');
			if($num<0){
				$num=1;
			}
		if(!$token){
			$return = array(
					'statusCode' => 0,
					'error' => array(
							'errorCode' => 101,
							'msg'       => '未传token',
					)
			);
			return $json->encode($return);
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
			if (!$spec_value_id) {
				$return = array(
						'statusCode' => 0,
						'error' => array(
								'errorCode' => 102,
								'msg'       => 'spec_value_id未传',
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
			$productList=$products_mod->find(array(
					'conditions' =>"goods_id='{$goods_id}'",
			));
			
			
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
			$url = H5_URL.'/cart-add.html';
			$post_data['gid']       = $goods_id;
			$post_data['pid']      = $defaultProductId;
			$post_data['num'] = $num;
//			$post_data['token'] = $token;
			
			//$result=https_request($url,http_build_query($post_data));

            //  加入购物车
            $type = $_POST['type'] ? $_POST['type'] :"custom";
//            if(!in_array($type, $this->_class_carts->types)){
//                $this->json_error('type_error');
//                return;
//            }
            $goods = Types::createObj($type);
            $post = $goods->_formatPost($post_data);




            if(!$post){
                $this->json_error($goods->get_error());
                return;
            };

            $post['user_id'] = $user_info['user_id'];
            $post['source_from'] = "app";
            $result = $goods->add($post);


            if(!$result)
            {
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 101,
                        'msg'       => $goods->get_error(),
                    )
                );
                return $json->encode($return);
            }

			///curl传值
			/*  $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
            curl_setopt($ch, CURLOPT_USERAGENT, 'mfdApp'); 
           return curl_exec($ch); */
			
			 if($result.done ==false){
				$return = array(
						'statusCode' => 0,
						'error' => array(
								'errorCode' => 103,
								'msg'       => '加入购物车失败',
						)
				);
				return $json->encode($return);
			}

			$this->result =$result;

			return $this->sresult();
				
		}
		
		
	}