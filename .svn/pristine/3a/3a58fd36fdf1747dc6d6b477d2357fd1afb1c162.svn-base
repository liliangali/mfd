<?php
class FashionApp extends MallbaseApp{
    var $_fashion_mod;
    var $_fashion_custom;
    function __construct(){
        $this->FashionApp();
    }
    function FashionApp(){
        $this->_fashion_mod=&m("fashion");
        $this->_fashion_custom=m('fashionLink');
        parent::__construct();
    }
    function index(){
//         $pageper=6;
//         $page=$this->_get_page($pageper);
//         $fashion_list=$this->_fashion_mod->find(array(
//             'condition'=>'1=1',
//             'count'=>true,
//             'order'=>"f_sort asc,pubdate desc",
//             'limit'=>$page['limit']
//         ));
//         foreach ($fashion_list as $key=>$value){
//             $fashion_list[$key]['pubdate']=date('Y-m-d',$value['pubdate']);
//             $fashion_list[$key]['summary']=strip_tags($value['summary']);
//         }
//         $this->assign('fashion_list',$fashion_list);
//         $page['item_count'] = $this->_fashion_mod->getCount();
        $this->_config_seo('title', '潮流 - ' . Conf::get('site_title'));
//         $this->_format_page($page);
//         if($pageper<$page['item_count']){
//             $this->assign('page_info',$page);
//         }       
        $this->display("fashion/fashion_list.html");
    }
    function info(){
        $args=$this->get_params();
        $id=empty($args)?0:intval($args[0]);
        if(!$id){
            $this->show_warning(lang::get('no_fashion'));
            return;
        }
        $fashion_list=$this->_fashion_mod->find(array(
            'condition'=>'1=1',
            'count'=>true,
            'order'=>"f_sort asc,pubdate desc"));
        $fashion_info='';
        $time=date('F Y',time());
        $end=end($fashion_list);
        $find=false;
        foreach ( $fashion_list as $key=>$value){      
            if($id==$value['id']){
                $fashion_info=$value;
            }
            if($find){
                $next=$value;
                break;
            }
            if($fashion_list[$key]['id']==$id && $end['id']!=$id){
                $find=true;
            }
        }
        $fashion_info['pubdate']=date('Y-m-d',$fashion_info['pubdate']);
        foreach ($fashion_list as $key => $value) {        
            $clicknum[$key] = $value['clicknum'];
            $pubdate[$key] = $value['pubdate'];        
        }
        array_multisort($clicknum, SORT_ASC, $pubdate, SORT_DESC, $fashion_list);
        $clicknum=array_slice($fashion_list,0,9,true);
        $links=$this->_fashion_custom->find(array(
            'conditions'=>"link.fashion_id='{$id}'",
            'join'=>'belongs_to_custom',
            'fields'=>'c.id as cid,c.name as cname,c.small_img as cimg,c.price as cprice'
        ));
        if(count($links)>4){
            $links=array_rand($links,4);
        }
        $this->assign('links',$links);
        $this->assign('fashion_info',$fashion_info);
        $this->assign('next',$next);
        $this->assign('clicknum',$clicknum);
        $this->assign('time',$time);
        $this->_config_seo('title', '潮流详情 - ' . Conf::get('site_title'));
        $this->display("fashion/fashion_info.html");
    }
    function ajax_click(){
       	$args=$this->get_params();
        $id=empty($args)?0:$args[0];
        if(!$id){
            $this->json_error(lang::get('no_fashion'));
            return;
        }
        $r=$this->_fashion_mod->setInc($id,'clicknum',1);
        if($r){
            $this->json_result('true',lang::get("fashion_clicknum_success"));
            return;
        }else{
            $this->json_error(lang::get('fashion_clicknum_error'));
            return;
        } 
    }
    function loadCustomData(){
        $pageper=6;
        $page=$this->_get_page($pageper);
        $fashion_list=$this->_fashion_mod->find(array(
            'condition'=>'1=1',
            'count'=>true,
            'order'=>"f_sort asc,pubdate desc",
            'limit'=>$page['limit']
        ));
        foreach ($fashion_list as $key=>$value){
            $fashion_list[$key]['pubdate']=date('Y-m-d',$value['pubdate']);
            $fashion_list[$key]['summary']=strip_tags($value['summary']);
        }
        $this->assign('fashion_list',$fashion_list);
        $page['item_count'] = $this->_fashion_mod->getCount();
        $this->_format_page($page);
         if($pageper<$page['item_count']){
             $this->assign('page_info',$page);
         }
        
//         $conditions = "c.cat_id = '{$cat}'";
//         if(!empty($attr)){
//             $attrCondition=' 0 ';
//             $aNum = 0;
//             foreach($attr as $key => $val){
//                 $aNum +=1;
//                 $attrCondition .= " OR (attr_id='{$key}' AND attr_value = '{$val}')";
//             }
             
//             $attrCondition .= " GROUP BY custom_id HAVING num = {$aNum}";
             
//             $customids = $this->_customAttr_mod->find(array(
//                 'conditions' => $attrCondition,
//                 'fields' => "custom_id, count(1) AS num",
//             ));
             
//             foreach($customids as $_k => $_v)
//             {
//                 $ids[] = $_v['custom_id'];
//             }
             
//             if(!empty($ids))
//             {
//                 $conditions .= " AND c.id ".db_create_in($ids);
//             }else{
//                 $conditions .= " AND c.id IN (0)";
//             }
//         }
         
//         $conditions .= " AND c.is_sale =1";
         
//         $page = $this->_get_page(8);
    
//         $_order = "c.{$order} {$sort}";
//         $customs = $this->_custom_mod->find(array(
//             "conditions" => $conditions,
//             'order'      => $_order,
//             'fields'     => "c.id,c.name,c.price,c.small_img,c.design_id, designer.username, designer.photo_url",
//             'limit'      => $page['limit'],
//             'join'       => "belongs_to_design",
//             'count'      => true,
//         ));
         
//         $page['item_count'] = $this->_custom_mod->getCount();
//         $this->_format_page($page);
//         $this->assign("customs",   $customs);
        $content = $this->_view->fetch("fashion/fashion.list.html");
        $retArr = array(
            'content' => $content,
            'link'    => $page["next_link"],
        );
        die($this->json_result($retArr));
    }
}
