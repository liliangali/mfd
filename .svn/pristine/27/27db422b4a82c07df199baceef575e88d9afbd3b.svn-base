<?php
class Fabric_DiyApp extends MallbaseApp {
	var $_mod_fbcategory;
	var $_mod_fabric_info;
	var $_mod_fabric_rel_attr;
	var $_mod_fabric_rel_gallery;
	var $_mod_fabric_price;
	var $_mod_shuffling;
	var $_mod_fabric_attr;
	var $_mod_fdict;
	var $_mod_oldfabric;
	var $unshow_id = 6; // 西裙的相关面料不显示
	var $acate_id = 4; // 分类属性id
	function __construct() {
		$this->_mod_fbcategory = &m ( 'fbcategory' );
		$this->_mod_fabric_info = &m ( 'fabric_info' );
		$this->_mod_fabric_rel_attr = &m ( 'fabric_rel_attr' );
		$this->_mod_fabric_rel_gallery = &m ( 'fabric_rel_gallery' );
		$this->_mod_fabric_price = &m ( 'fabric_price' );
		$this->_mod_shuffling = &m ( 'shuffling' );
		$this->_mod_fabric_attr = &m ( 'fabric_attribute' );
		$this->_mod_fdict = &m ( "fdiy_dict" );
		$this->_mod_oldfabric = &m ( 'fabric' );
		parent::__construct ();
	}
	function index() {
		$categories = $this->_mod_fabric_info->_category;
		$unshowid = $this->unshow_id;
		$acate_id = $this->acate_id;
		$unshow_cate = $categories [$unshowid] ['name'];
		// 要过滤的面料id
		$export_ids = array ();
		// 查找出中国的id
		$regions = $this->_mod_fbcategory->find ( array (
				'conditions' => "1=1 AND parent_id=0" 
		) );
		$china = array ();
		foreach ( $regions as $k => $v ) {
			if (strstr ( $v ['cate_name'], '中' ) || strstr ( $v ['cate_name'], '大陆' )) {
				$china = $v;
				unset ( $regions [$k] );
				continue;
			}
			if (! $v ['if_show']) {
				unset ( $regions [$k] );
			}
		}
		// 所有的国产面料
		if ($china ['if_show']) {
			$domestic_fabrics = $this->_mod_fbcategory->find ( array (
					'conditions' => "parent_id={$china['cate_id']} AND if_show=1",
					'order' => 'sort_order DESC' 
			) );
			
			// 面料库存判断暂时判断老表的库存////////
			$fabrics = $this->_mod_fabric_info->find ( array (
					'conditions' => 'fi.is_sale=1 AND f.STOCK>5',
					'join' => 'has_stock' 
			) );
			// 品类为西裙的面料剔除
			$fabric_ids = i_array_column ( $fabrics, 'fabric_id' );
			$fabric_attrs = $this->_mod_fabric_rel_attr->find ( array (
					'conditions' => "attr_id={$acate_id} AND " . db_create_in ( $fabric_ids, 'fabric_id' ) 
			) );
			
			foreach ( ( array ) $fabric_attrs as $key => $val ) {
				if ($val ['attr_value'] == $unshow_cate) {
					$export_ids [] = $val ['fabric_id'];
				}
			}
			foreach ( ( array ) $fabrics as $k => $v ) {
				if (in_array ( $v ['fabric_id'], $export_ids )) {
					unset ( $fabrics [$k] );
				}
			}
			
			// 过滤没有面料的品牌
			$brand_exit_ids = i_array_column ( $fabrics, 'brand_id' );
			$brand_exit_ids = array_unique ( $brand_exit_ids );
			foreach ( $domestic_fabrics as $k => $v ) {
				if (! in_array ( $v ['cate_id'], $brand_exit_ids )) {
					unset ( $domestic_fabrics [$k] );
				}
			}
			
			$domestic_fabrics = array_values ( $domestic_fabrics );
			
			$this->assign ( 'dfabrics', $domestic_fabrics );
		}
		// 所有进口面料
		$import_fabrics = array ();
		if ($regions) {
			$region_ids = i_array_column ( $regions, 'cate_id' );
			$import_fabrics = $this->_mod_fbcategory->find ( array (
					'conditions' => "1=1 AND if_show=1 AND " . db_create_in ( $region_ids, 'parent_id' ),
					'order' => 'sort_order DESC' 
			) );
			// 面料库存判断暂时判断老表的库存////////
			$fabrics = $this->_mod_fabric_info->find ( array (
					'conditions' => 'fi.is_sale=1 AND f.STOCK>5',
					'join' => 'has_stock' 
			) );
			// 品类为西裙的面料剔除
			$fabric_ids = i_array_column ( $fabrics, 'fabric_id' );
			$fabric_attrs = $this->_mod_fabric_rel_attr->find ( array (
					'conditions' => "attr_id={$acate_id} AND " . db_create_in ( $fabric_ids, 'fabric_id' ) 
			) );
			
			foreach ( ( array ) $fabric_attrs as $key => $val ) {
				if ($val ['attr_value'] == $unshow_cate) {
					$export_ids [] = $val ['fabric_id'];
				}
			}
			foreach ( ( array ) $fabrics as $k => $v ) {
				if (in_array ( $v ['fabric_id'], $export_ids )) {
					unset ( $fabrics [$k] );
				}
			}
			// 过滤没有面料的品牌
			$brand_exit_ids = i_array_column ( $fabrics, 'brand_id' );
			$brand_exit_ids = array_unique ( $brand_exit_ids );
			foreach ( $import_fabrics as $k => $v ) {
				if (! in_array ( $v ['cate_id'], $brand_exit_ids )) {
					unset ( $import_fabrics [$k] );
				}
			}
			$import_fabrics = array_values ( $import_fabrics );
			$this->assign ( 'ifabrics', $import_fabrics );
		}
		// 轮播图
		$banners = $this->_mod_shuffling->find ( array (
				'conditions' => 'groups=8',
				'order' => "sort_order DESC" 
		) );
		$this->_config_seo ( 'title', '面料定制-面料品牌选择-' . Conf::get ( "site_name" ) . "-" . Conf::get ( 'site_title' ) );
		$this->assign ( 'banner', $banners );
		$this->display ( 'fabricdiy/fabric_brand.list.html' );
	}
	
	/* 面料列表 */
	function flist() {
		$page = 8;
		$args = $this->get_params ();
		$t = ! empty ( $args [0] ) ? intval ( $args [0] ) : 0;
		$cate = ! empty ( $args [1] ) ? intval ( $args [1] ) : 10;
		$color = ! empty ( $args [2] ) ? intval ( $args [2] ) : 0;
		$colors = $this->_mod_fabric_attr->get ( 3 );
		$colors = explode ( ',', $colors ['attr_values'] );
		foreach ( $colors as $k => $v ) {
			$newcolors [$k + 1] = $v;
		}
		
		// 查出该面料所属产地和品牌
		$parents = $this->_mod_fbcategory->find ();
		foreach ( $parents as $k => $v ) {
			if ($v ['cate_id'] == $t) {
				$this->assign ( 'brand', $v ['cate_name'] );
				foreach ( $parents as $key => $val ) {
					if ($val ['cate_id'] == $v ['parent_id']) {
						$this->assign ( 'region', $val ['cate_name'] );
					}
				}
			}
		}
		
		$newcate = $this->_mod_fabric_info->_fcategory;
		
		/*
		 * //////////
		 * 面料库存判断暂时判断老表的库存,且面料在老表里存在,根据面料编号判断
		 * //////////
		 */
		$info = $this->_mod_fabric_info->find ( array (
				'conditions' => "fi.brand_id={$t} AND fi.is_sale=1 AND f.STOCK>5",
				'order' => 'sort_order ASC,add_time DESC',
				'join' => 'has_stock' 
		) );
		
		if ($info) {
			$fabric_ids = i_array_column ( $info, 'fabric_id' );
			// 筛选条件只显示该品牌下的面料拥有的属性范围
			$attrs = $this->_mod_fabric_rel_attr->find ( array (
					'conditions' => "1=1 AND " . db_create_in ( $fabric_ids, 'fabric_id' ) 
			) );
			$ncate = array ();
			$ncolor = array ();
			foreach ( $attrs as $key => $val ) {
				foreach ( $newcolors as $k => $v ) {
					if ($v == $val ['attr_value']) {
						$ncolor [$k] = $v;
					}
				}
				foreach ( $newcate as $k => $v ) {
					if ($v == $val ['attr_value']) {
						$ncate [$k] = $v;
					}
				}
			}
			// 显示面料拥有的品类范围
			$ocate = array_diff ( $newcate, $ncate );
			$rcate = array_diff ( $newcate, $ocate );
			$this->assign ( 'colors', $ncolor );
			$this->assign ( 'category', $rcate );
			
			// 面料列表页默认显示品类
			if (! $rcate [$cate]) {
				reset ( $rcate );
				$cate = key ( $rcate );
			}
			$this->assign ( 'cate', $cate );
			$this->assign ( 'color', $color );
			$this->assign ( 't', $t );
			
			$fprice = $this->_mod_fabric_price->find ( array (
					'conditions' => "1=1 AND " . db_create_in ( $fabric_ids, 'fabric_id' ) . " AND category={$cate}" 
			) );
			$pfabric_ids = i_array_column ( $fprice, 'fabric_id' );
			$pfabric_ids = array_unique ( $pfabric_ids );
			// 如果筛选颜色
			if ($color) {
				// $rfabrics=$this->_mod_fabric_rel_attr->find(array(
				// 'conditions'=>"1=1 AND attr_value='{$newcolors[$color]}' AND ".db_create_in($pfabric_ids,'fabric_id'),
				// ));
				$rfabrics = array ();
				foreach ( $attrs as $key => $val ) {
					foreach ( $pfabric_ids as $k => $v ) {
						if ($val ['attr_value'] == $newcolors [$color] && $val ['fabric_id'] == $v) {
							$rfabrics [] = $val;
						}
					}
				}
				$pfabric_ids = i_array_column ( $rfabrics, 'fabric_id' );
			}
			// 根据条件筛选要展示的面料
			foreach ( $info as $k => $v ) {
				if (! in_array ( $v ['fabric_id'], $pfabric_ids )) {
					unset ( $info [$k] );
				}
			}
			$fnum = count ( ( array ) $info );
			$finfo = array ();
			if ($fnum > $page) {
				$info = array_values ( $info );
				foreach ( $info as $k => $v ) {
					if ($k < $page) {
						$v ['cate_name'] = $newcate [$cate];
						foreach ( $fprice as $key => $val ) {
							if ($v ['fabric_id'] == $val ['fabric_id']) {
								$v ['cate_price'] = $val ['price'];
							}
						}
						$finfo [] = $v;
					}
				}
				$url = "/fabric_diy-loadData-2-{$t}-{$cate}-{$color}.html";
				$this->assign ( 'url', $url );
			} else {
				foreach ( $info as $k => $v ) {
					$info [$k] ['cate_name'] = $newcate [$cate];
					foreach ( $fprice as $key => $val ) {
						if ($v ['fabric_id'] == $val ['fabric_id']) {
							$info [$k] ['cate_price'] = $val ['price'];
						}
					}
				}
				$finfo = $info;
				$this->assign ( 'url', '' );
			}
			$this->_config_seo ( 'title', '面料定制-面料选择-' . Conf::get ( "site_name" ) . "-" . Conf::get ( 'site_title' ) );
			$this->assign ( 'fabrics', $finfo );
			$this->display ( 'fabricdiy/fabric.list.html' );
		} else {
			$this->show_message ( '库存不足，请选购其他品牌下的面料', '返回面料品牌页', 'fabric_diy.html' );
		}
	}
	function loadData() {
		$args = $this->get_params ();
		$t = intval ( $args [1] );
		$cate = ! empty ( $args [2] ) ? intval ( $args [2] ) : 10;
		$color = ! empty ( $args [3] ) ? intval ( $args [3] ) : 0;
		$page = $this->_get_page ( 8 );
		$colors = $this->_mod_fabric_attr->get ( 3 );
		$colors = explode ( ',', $colors ['attr_values'] );
		foreach ( $colors as $k => $v ) {
			$newcolors [$k + 1] = $v;
		}
		
		$newcate = $this->_mod_fabric_info->_fcategory;
		
		// var_dump($t);
		$info = $this->_mod_fabric_info->find ( array (
				'conditions' => "fi.brand_id={$t} AND fi.is_sale=1 AND f.STOCK>5",
				'order' => 'sort_order ASC,add_time DESC',
				'join' => 'has_stock' 
		) );
		$fabric_ids = i_array_column ( $info, 'fabric_id' );
		$fprice = $this->_mod_fabric_price->find ( array (
				'conditions' => "1=1 AND " . db_create_in ( $fabric_ids, 'fabric_id' ) . " AND category={$cate}" 
		) );
		$pfabric_ids = i_array_column ( $fprice, 'fabric_id' );
		$pfabric_ids = array_unique ( $pfabric_ids );
		// 如果筛选颜色
		if ($color) {
			$rfabrics = $this->_mod_fabric_rel_attr->find ( array (
					'conditions' => "1=1 AND attr_value='{$newcolors[$color]}' AND " . db_create_in ( $pfabric_ids, 'fabric_id' ) 
			) );
			$pfabric_ids = i_array_column ( $rfabrics, 'fabric_id' );
		}
		// 根据条件筛选要展示的面料
		foreach ( ( array ) $info as $k => $v ) {
			if (! in_array ( $v ['fabric_id'], $pfabric_ids )) {
				unset ( $info [$k] );
			}
		}
		$info = array_values ( $info );
		$page_start = ($page ['curr_page'] - 1) * $page ['pageper'];
		$page_end = $page_start + $page ['pageper'];
		$finfo = array ();
		foreach ( ( array ) $info as $k => $v ) {
			if ($k >= $page_start && $k < $page_end) {
				$v ['cate_name'] = $newcate [$cate];
				foreach ( $fprice as $key => $val ) {
					if ($v ['fabric_id'] == $val ['fabric_id']) {
						$v ['cate_price'] = $val ['price'];
					}
				}
				$finfo [] = $v;
			}
		}
		$page ['item_count'] = count ( $info );
		// die();
		$this->_format_page ( $page );
		$this->assign ( "fabrics", $finfo );
		$content = $this->_view->fetch ( "fabricdiy/fabric.add.html" );
		$retArr = array (
				'content' => $content,
				'link' => $page ["next_link"] 
		);
		die ( $this->json_result ( $retArr ) );
	}
	function info() {
		$args = $this->get_params ();
		$id = intval ( $args [0] );
		// 获取面料信息
		$fabric_info = $this->_mod_fabric_info->get ( $id );
		// 获取面料产地和品牌
		$region = $this->_mod_fbcategory->get ( $fabric_info ['region_id'] );
		$brand = $this->_mod_fbcategory->get ( $fabric_info ['brand_id'] );
		// 获取面料定制价格
		$fabric_price = $this->_mod_fabric_price->find ( array (
				'conditions' => "1=1 AND fabric_id={$fabric_info['fabric_id']}" 
		) );
		$newcate = $this->_mod_fabric_info->_fcategory;
		
		$realcates = $this->_mod_fdict->_categoryZ;
		
		foreach ( $newcate as $k => $v ) {
			foreach ( $fabric_price as $key => $val ) {
				if ($k == $val ['category']) {
					
					if ($val ['category'] == 10) {
						$fabric_price [$key] ['cate_name'] = "套装(西服、西裤)";
					} else {
						$fabric_price [$key] ['cate_name'] = $v;
					}
				}
			}
		}
		foreach ( $fabric_price as $k => $v ) {
			foreach ( $realcates as $key => $val ) {
				if ($val == $v ['category']) {
					$fabric_price [$k] ['realcate'] = $key;
				}
			}
		}
		$fabric_price = $this->arr_sort ( $fabric_price, 'realcate' );
		// 获取面料颜色
		$color = $this->_mod_fabric_rel_attr->get ( array (
				'conditions' => "attr_id=3 AND fabric_id={$fabric_info['fabric_id']}",
				'field' => 'attr_value' 
		) );
		$fabric_info ['color'] = $color ['attr_value'];
		// 获取面料相册
		$gallery = $this->_mod_fabric_rel_gallery->find ( array (
				'conditions' => "fabric_id={$fabric_info['fabric_id']}" 
		) );
		$ret_url = $_SERVER ['HTTP_REFERER'];
		$this->assign ( 'ret_url', $ret_url );
		$this->assign ( 'fprice', $fabric_price );
		$this->assign ( 'gallery', $gallery );
		$fabric_info ['special'] = stripslashes ( $fabric_info ['special'] );
		$fabric_info ['content'] = stripslashes ( $fabric_info ['des'] );
		$this->assign ( 'fabric', $fabric_info );
		$this->assign ( 'region', $region ['cate_name'] );
		$this->assign ( 'brand', $brand ['cate_name'] );
		$this->assign ( 't', $brand ['cate_id'] );
		$this->_config_seo ( 'title', "{$fabric_info['fabric_name']}-大牌面料-{$region['cate_name']}({$brand['cate_name']})" . Conf::get ( "site_name" ) . "-" . Conf::get ( 'site_title' ) );
		$this->display ( 'fabricdiy/fabric.info.html' );
	}
	function arr_sort($array, $key, $order = "asc") { // asc是升序 desc是降序
		$arr_nums = $arr = array ();
		
		foreach ( $array as $k => $v ) {
			
			$arr_nums [$k] = $v [$key];
		}
		
		if ($order == 'asc') {
			
			asort ( $arr_nums );
		} else {
			
			arsort ( $arr_nums );
		}
		
		foreach ( $arr_nums as $k => $v ) {
			
			$arr [$k] = $array [$k];
		}
		
		return $arr;
	}
	
	// 面料下单前先判断是否登陆再判断面料库存是否大于5米（暂时查询diy_fabric表的库存）
	function ajax_check_stock() {
		/* 只有登录的用户才可访问 */
		if (! $this->visitor->has_login) {
			if (! IS_AJAX) {
				$link = array (
						"app" => "member",
						"act" => "login" 
				);
				$_view = &v ();
				$url = $_view->build_url ( $link );
				$this->json_error($url . '?ret_url=' . rawurlencode ( $_SERVER ['HTTP_REFERER'] ),1);
				return;
			} else {
				$this->json_error ( 'login_please' );
				return;
			}
		}
		$code = ! empty ( $_REQUEST ['code'] ) ? $_REQUEST ['code'] : 0;
		if (! $code) {
			return $this->json_error ( '参数错误,请联系客服' );
		}
		// var_dump($code);
		$fabric = $this->_mod_fabric_info->find ( array (
				'conditions' => "fi.fabric_sn='{$code}' AND f.STOCK>5 AND fi.is_sale=1",
				'join' => 'has_stock' 
		) );
		// var_dump($fabric);
		if ($fabric) {
			return $this->json_result ( 2, '有符合条件的面料' );
		} else {
			return $this->json_error ( '该面料库存不足或已下架，请选购其他面料' );
		}
	}
}