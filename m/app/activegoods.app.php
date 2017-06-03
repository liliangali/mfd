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
class ActivegoodsApp extends MallbaseApp
{
    function __construct()
    {
        parent::__construct();
       $this->mdlGoods =& m('goods');
       $this->_goods_promotion_mod =& m('goods_prorule');
       $this->_goodstype_mod =& m('goodstype'); //商品类型
       $this->_gcategory_mod =& bm('gcategory', array('_store_id' => 0));//商品分类
       $this->_link_mod =& m('goods_prorel');
       $this->_goodslink_mod = &m('goods_prolink');
       $this->_user_mod =& m('member');
       $this->_lv_mod =& m('memberlv');
       $this->_fdiy_management_mod =& m('fdiy_management');
    }

    function index()
    {
        
        $activeId = isset($_GET['active_id']) ? $_GET['active_id'] : 0;
        if(!$activeId){
               header("Location:index.php/activepublic.html");
               return;
        }
        
        $rule = $this->_goods_promotion_mod->get($activeId);
        
        if(!$rule){
            header("Location:index.php/activepublic.html");
            return;
        }


        $conditions ="1=1";
        if($rule['favorable']==1){
            
            $rule_value =  $this->_goodslink_mod->find(array(
                'conditions'=>"rules_id='{$activeId}' AND favorable_id=1 AND type=0",
            ));
            foreach ($rule_value as $k=>$v){
                
                $type[] = $v['favorable_value'];
                
            }
            $conditions .="  AND ".db_create_in($type,"type_id");
            
               
        }
        if($rule['favorable']==2){
            $goodsIds = $this->_link_mod->find(array(
                'conditions'=>"d_id='{$activeId}'",
            ));
            foreach ($goodsIds as $value){
                $gIds[] = $value['c_id'];
            }
            
            $conditions .= "  AND ".db_create_in($gIds,"goods_id");
            
        }
        if($rule['favorable'] ==3){
           
            $rule_value =  $this->_goodslink_mod->find(array(
                'conditions'=>"rules_id='{$activeId}' AND favorable_id=3 AND type=0",
            ));
           
            foreach ($rule_value as $v){
                $catId[] = $v['favorable_value'];
            }  
        }

        
        if (!isset($_GET['sort'])) 
        {
           
        }
        
        if (!isset($_GET['order']))
        {
            $_GET['order'] = 0;
        }
        
        $order =  isset($_GET['order']) ? $_GET['order'] : 0;
        $sort  =  isset($_GET['sort']) ? $_GET['sort'] : '';
        $goodsLib = new Goods();
        $gcategoryLib = new Gcategory();
        $page = $this->_get_page(9);   //获取分页信息
           
        $goodsList = $goodsLib->lists($page['limit'],$conditions,$catId,$order,$sort);
        $yhcase = $rule['yhcase'];
        $yhcasevalue = $rule['yhcase_value'];
        foreach ($goodsList as $k=>$v){
            if($yhcase==1){
                $goodsList[$k]['activeprice']= $v['price'] * $yhcasevalue * 0.01;
        
            }elseif($yhcase ==2){
                $goodsList[$k]['activeprice']= $yhcasevalue;
             
            }elseif($yhcase ==3){
                
                $goodsList[$k]['activeprice']= $v['price'] - $v['price'] * $yhcasevalue * 0.01;
                
            }elseif($yhcase==4){
                
                $goodsList[$k]['activeprice']= $v['price'] - $yhcasevalue;
            }elseif($yhcase ==5){
                $goodsList[$k]['activeprice']= $v['price'];
            }
            
        }
        $page['item_count'] = $this->mdlGoods->getCount();   //获取统计数据
        $this->_format_page($page);
        $this->assign("active_id",      $activeId);
        $this->assign("yhcase",      $yhcase);
        $this->assign('page_info', $page);
        $this->assign('list',$goodsList);
      
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
        
        $this->assign('pList',$gcategoryParentList);
        $this->assign('sonList',$sonList);
        $this->display('man/activegoods.html');

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
//    print_exit($list);      
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
