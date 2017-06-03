<?php

class BrandApp extends MallbaseApp
{
    function index()
    {
        //导航
        $this->assign('navs', $this->_get_navs());
        $_curlocal = array(
                array(
                'text' => Lang::get('index'),
                'url'  => 'index.php',),
                array(
                'text' => Lang::get('all_brands'),
                'url'  => '',),
            );
        $this->assign('_curlocal', $_curlocal);
        
        
        
        $recommended_stores = $this->_recommended_stores(5);
        $this->assign('recommended_stores', $recommended_stores);
        $recommended_brands = $this->_recommended_brands(10);
        $this->assign('recommended_brands', $recommended_brands);
        
        
		$brand_mod =&m('brand', array('_store_id' => 0));
		$brand = $brand_mod ->getIntroBrands();
		
		$this->assign('brand',$brand);
		
        $this->_config_seo('title', Lang::get('all_brands'));
        
        $gcategory_mod =& bm('gcategory', array('_store_id' => 0));
        $cate1 = $gcategory_mod ->get_list(1);
        
        $gcategory_mod =& bm('gcategory', array('_store_id' => 0));
        $cate2 = $gcategory_mod ->get_list(21);
        
        $this->assign('cate1', $cate1);
        $this->assign('cate2', $cate2);
        
       // array_merge($cate1,$cate2);
        
        $_arr = array();
        foreach($cate1 as $key => $val){
        	$_arr[]=$val["cate_id"];
        }
        
        $_str = $_arr ? implode(",", $_arr) : 0;
        
        $goods_mod =& bm('goods');
        
        $goods1 = $goods_mod->get_brand_goods($_str);
        
        $this->assign('goods1', $goods1);
        
        
        $_arr = array();
        foreach($cate2 as $key => $val){
        	$_arr[]=$val["cate_id"];
        }
        
        $_str = $_arr ? implode(",", $_arr) : 0;
        
        
        $goods2 = $goods_mod->get_brand_goods($_str);
        
        $this->assign('goods2', $goods2);
        
        
        $this->assign('adlist1', $this->_c_ad(1));
        $this->assign('adlist3', $this->_c_ad(3));
        
        $store_mod =&m('store', array('_store_id' => 0));
        $store_list = $store_mod ->get_new_store();
        $this->assign('store_list', $store_list);
        
        $this->_config_seo('title', '品牌馆 - '. Conf::get('site_title'));
        
        $this->display('brand.index.html');
    }
    
    /* 取得店铺分类 */
    function _c_ad($type=1)
    {
    	$scategory_mod =& m('scategory');
    	$sql = "SELECT * FROM cf_fimg WHERE tpl = '1' AND type_id = '{$type}' ORDER BY l_order";
    	$list  = $scategory_mod->getAll($sql);
    	return $list;
    }
    
    function _recommended_brands($num)
    {
        $brand_mod =& m('brand');
        $brands = $brand_mod->find(array(
            'conditions' => 'recommended = 1 AND if_show = 1',
            'order' => 'sort_order',
            'limit' => '0,' . $num));
        return $brands;
    }

    function _recommended_stores($num)
    {
        $store_mod =& m('store');
        $goods_mod =& m('goods');
        $stores = $store_mod->find(array(
            'conditions'    => 'recommended=1 AND state = 1',
            'order'         => 'sort_order',
            'join'          => 'belongs_to_user',
            'limit'         => '0,' . $num,
        ));
        foreach ($stores as $key => $store){
            empty($store['store_logo']) && $stores[$key]['store_logo'] = Conf::get('default_store_logo');
            $stores[$key]['goods_count'] = $goods_mod->get_count_of_store($store['store_id']);
        }
        return $stores;
    }
}

?>
