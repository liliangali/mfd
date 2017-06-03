<?php
/**
 * EC51 licence
 *
 * @copyright  Copyright (c) 2007-2016 EC51.cn Inc. (http://www.ec51.cn)
 * @license  http://license.ec51.cn/ EC51 License
 */
use Cyteam\Goods\Goods;
use Cyteam\Goods\Gcategory;
/* 商品 */
class GalleryApp extends MallbaseApp
{
    function __construct()
    {
        $this->mdlGoods =& m('goods');
       parent::__construct();
    }

    function index()
    {
        unset($_GET['submit']);
        $pId = isset($_GET['p_id']) ? $_GET['p_id'] : 0;
        if (!$pId) 
        {
           $_GET['son_id'] = 0;
        }
        
        if (!isset($_GET['sort'])) 
        {
        }
        
        if (!isset($_GET['order']))
        {
            $_GET['order'] = 0;
        }
        
        $sonId =  isset($_GET['son_id']) ? $_GET['son_id'] : 0;
        $order =  isset($_GET['order']) ? $_GET['order'] : 0;
        $sort  =  isset($_GET['sort']) ? $_GET['sort'] : '';
        $goodsLib = new Goods();
        $gcategoryLib = new Gcategory();
        $page = $this->_get_page(16);   //获取分页信息

        $conditions = "1=1 AND ";
        
        if ($sonId) //===== 先判断二级分类  =====
        {
           $catId[] = $sonId;
        }
        elseif ($pId)
        {
            $sonList = $gcategoryLib->lists($pId);
            if ($sonList) 
            {
                foreach ($sonList as $key => $value) 
                {
                    $catId[] = $value['cate_id'];
                }
            }
            $catId[] = $pId;
        }
        else 
        {
            $catId = [];
        }

        //支持商品关键词的搜索
        $keywords = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';
        $goods_condition = "1=1 ";
        if($keywords)
        {
            $keywordsarr = explode(' ',trim($keywords));
            $ks='';
            foreach($keywordsarr as $v){
                $ks .= "or name like '%$v%' ";
            }
            
            $goods_condition=' ( '.ltrim($ks,'or') . ' ) ';
            
//            $goods_condition = "name like '$keywords%' ";
        }

        //===== 排序  =====

        $goodsList = $goodsLib->lists($page['limit'],$goods_condition,$catId,$order,$sort);

        $page['item_count'] = $this->mdlGoods->getCount(); //获取统计数据
        
        $this->_format_page($page,7,'','','?son_id='.$_GET['son_id'].'&p_id='.$_GET['p_id'].'&order='.$_GET['order'].'&sort='.$_GET['sort'].'');
//        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('list', $goodsList);
        
        $gcategoryParentList = $gcategoryLib->lists(0);
        $selctId = $pId > 0 ? $pId : 0;
        if ($selctId) 
        {
            $sonList = $gcategoryLib->lists($selctId);
        }
        else 
        {
            $sonList = [];
        }
        
        // 查询轮播图片组
        $db=&db();
        $shuffling_group=$db->getrow("select * from cf_shuffling_group where code='gallery' "); // id
        $shuffling=$db->getall("select * from cf_shuffling where groups='".$shuffling_group['id']."' and status = '1' and site_id = '1' order by sort_order desc ");
        if(count($shuffling) == 1){
            $this->assign('shufflingnumber','1');
            $this->assign('shuffling',current($shuffling));
        }else{
            $this->assign('shufflingnumber','');
            $this->assign('shuffling',$shuffling);
        }
        
        $this->assign('pList',$gcategoryParentList);
        $this->assign('sonList',$sonList);
        $this->_config_seo('title', 	"麦富迪尚品");
        $this->display('man/gallery.html');
        exit;
	}
    
    function loadData(){
        $order = isset($_GET['order']) ? trim($_GET['order']) : "reorder";
        $sort  = isset($_GET['sort'])  ? trim($_GET['sort'])  : "DESC";
        $t     = isset($_GET['t'])     ? intval($_GET['t']) : 0;
        if(!in_array($order, array("add_time", "price", "sale_num", "reorder"))){
            $order = "reorder";
        }
        
        if(!in_array($sort, array("ASC", "DESC"))){
            $sort = "DESC";
        }
        
        $ids = array();
        if(empty($t)){
            $dis = m('jpjz_dissertation');
            $mData = $dis->find(array(
                "conditions" => "(cat_id='{$this->man}' OR cat_id='{$this->woman}') AND is_show = 1",
                'order'      => "sort_order ASC",
                "fields"     => "id",
            ));
            
            foreach($mData as $key => $val){
                $ids[] = $val["id"];
            }
        }
        
        $conditions = !empty($t) ? "suit_list.theme = '{$t}'" : "suit_list.theme ".db_create_in($ids);        
        $conditions .= " AND suit_list.is_sale=1 AND (suit_list.to_site = '0' OR suit_list.to_site = 'pc')";

        $suit = &m("suitlist");
        $page = $this->_get_page(8);
        $_order = "suit_list.{$order} {$sort},suit_list.id DESC";
        $list = $suit->find(array(
            "conditions" => $conditions,
            "order"      => $_order,
            'limit'      => $page['limit'],
            'fields'     => "suit_list.id, suit_list.suit_name, suit_list.side_images,suit_list.image, suit_list.price,suit_list.design_id, designer.username, designer.photo_url",
            'join'       => "belongs_to_design",
            "count"      => true,
        ));

        if ($list) 
        {
            foreach ($list as $key => &$value) 
            {
                $value['woman_price'] = 0;
                if ($t == 16) 
                {
                    $value['woman_price'] = _format_price_int($value['price'] * 0);
                }
                $value['price'] = _format_price_int($value['price']);
            }
        }
        
        $page['item_count'] = $suit->getCount();
        $this->_format_page($page);
        $this->assign("list",   $list);
        $content = $this->_view->fetch("man/suit.list.html");
        $retArr = array(
            'content' => $content,
            'link'    => $page["next_link"],
        );
        die($this->json_result($retArr));
    }
}
?>
