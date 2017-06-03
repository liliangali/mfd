<?php

/* 商品 */
class GalleryApp extends MallbaseApp
{
    var $_dis_mod;
    var $_link_mod;
    var $_custom_mod;
    var $_gallery_mod;
    var $_mod_region;
    var $_mod_demanditem;
    var $top_csts;
    function __construct()
    {
       parent::__construct();
	   $this->_dis_mod = &m("dissertation");
	   $this->_link_mod = &m("links");
	   $this->_custom_mod = &m("customs");
	   $this->_gallery_mod = &m("gallery");
	   $this->_mod_region = &m('region');
	   $this->_mod_demanditem = &m('demanditem');
	   $this->top_csts = $this->_custom_mod->getTopCate();
	   $this->assign('cats', $this->top_csts);
	   
    }

    function index()
    {
        $arg = $this->get_params();
        $page = intval($_POST['page']);
        if($page){
            $arg[0] = $page;
            $this->set_params($arg);
        }
        $page = $this->_get_page(15);

        $dislist = $this->_dis_mod->find(array(
    			'limit' => $page['limit'],
    			'fields' => '*',
    			'order' => "sort_order DESC",
    			'count'	=>	"true",
    			
    	));
        
        $disids = array();
        
        foreach($dislist as $key => $val){
            $cstids[] = $val['id'];
        }
        
        $links = $this->_link_mod->find(array(
           'conditions' => "is_active=1 AND d_id ".db_create_in($cstids),
            'order' => "lorder ASC, c_id DESC",
            'join' => 'has_custom',
            'fields' => "d_id,cst_dis_image,cst_image",
        ));
        
        
        
        $append = array();
        foreach($links as $key => $val){
              $append[$val['d_id']][$val['id']] = $val;
        }
        
        
        $page['item_count'] = $this->_dis_mod->getCount();

        $regions = $this->_mod_region->get_list(0);
        if ($regions)
        {
            $tmp  = array();
            foreach ($regions as $key => $value)
            {
                $tmp[$key] = $value['region_name'];
            }
            $regions = $tmp;
        }
        
        $items = $this->_mod_demanditem->find(array(
           'conditions' => 'cate=4', 
        ));
        
        $opts = array();
        foreach((array)$items as $key => $val){
            $opts[$val['id']] = $val['name'];
        }
        
        $this->assign("regions", $regions);
        $this->assign("opts", $opts);
        $this->assign('dislist', $dislist);
        $this->assign('append', $append);
        $this->_format_page($page);
        $this->assign("page_info", $page);
        $this->assign('current', 'gallery');
        $this->_config_seo(array(
            'title' =>'选择您喜欢的款式进行定制',
            'description' => Conf::get('site_description'),
            'keywords' => Conf::get('site_keywords')
        ));
        
        
        
        /*****************
        $pt =& m("part");
        
        $flist = $pt->find(array(
            'conditions' => "1=1 AND fabric_id !=0",
        ));
        
        $at = &m("partattr");
        
        $l = array(
            '8001' => '西装',
            '8030' => '衬衫',
            '8050' => '大衣',
        );
        
        foreach($flist as $key => $val){
            if($val['cate_id']){
                $data['attr_value'] = $l[$val['cate_id']];
                $at->edit("part_id='{$val['part_id']}' AND attr_id=26", $data);  
            }
        }
        ******************************/
        
        $this->display('gallery.index.html');
    }
    
    function goods(){
        $arg = $this->get_params();
        $page = intval($_POST['page']);
        if($page){
            $arg[0] = $page;
            $this->set_params($arg);
        }

        $page = $this->_get_page(15);
        
        $list = $this->_custom_mod->find(array(
            'conditions' => "is_active=1 AND cst_cate ='{$arg[1]}'",
            'limit' => $page['limit'],
            'order' => "cst_id DESC",
            'count'	=>	"true",
        ));
        
        $this->assign("curCat", $arg[1]);
        $cstids = array();
        foreach($list as $key => $val){
            $cstids[] = $val['cst_id'];
        }
        
        $imgs = $this->_gallery_mod->find(array(
           'conditions' => "cst_id ".db_create_in($cstids), 
        ));
        
        $append = array();
        foreach($imgs as $key => $val){
            $append[$val['cst_id']][] =$val;
        }
        
        $fab_mod = &m("customsparts");
        
        $fabs = $fab_mod->find(array(
           'conditions' => "fabric_id != 0 AND cst_id ".db_create_in($cstids),
           'join' => 'has_parts',
        ));
        
        $fabsArray = array();
        $fabid = array();
        foreach($fabs as $key => $val){
            $fabsArray[$val['cst_id']] = $val;
            $fabid[] = $val['part_id'];
        }
        
        $attr_mod = &m("partattr");
        
        $eles = $attr_mod->find(array(
            'conditions' => 'attr_id = 25 AND part_id' .db_create_in($fabid),
        ));
        
        $eleArray = array();
        foreach($eles as $key => $val){
            $eleArray[$val['part_id']] = $val;
        }
      
        
        foreach($list as $key => $val){
            $list[$key]['fabric_sn'] = @$fabsArray[$key]['goods_sn'];
            $list[$key]['ele']       = @$eleArray[$fabsArray[$key]['part_id']]['attr_value'];
        }
        $page['item_count'] = $this->_custom_mod->getCount();
        $this->assign('current', 'gallery');
        $this->assign("append", $append);
        $this->assign("arg1", $arg[1]);
        $this->_format_page($page);
        $this->assign("page_info", $page);
        
        $this->assign('list', $list);
        $this->_config_seo(array(
            'title' => "所有{$this->top_csts[$arg[1]]}",
            'description' => Conf::get('site_description'),
            'keywords' => Conf::get('site_keywords')
        ));
        $this->display("gallery.goods.html");
    }
    
    function checkCode(){
        $phone = trim($_GET['phone']);
        $code  = intval($_GET['code']);
        $res = check::isMobileCode('suitdiy', 'suitdiy', $phone, $code);
        if(!empty($res)){
            $this->json_error($res);
        }else{
            $this->json_result();
        }
        die();
    }
    
    function checkTailor()
    {
    	$user =  $this->visitor->get('user_id');
    	$member_mod = m('member');
    	$user_info = $member_mod->get($user);
     	echo $user_info['serve_type'];exit;
    }
}
?>
