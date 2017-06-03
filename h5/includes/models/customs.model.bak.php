<?php
/* 数据模型 */
class CustomsModel extends BaseModel{
    var $table  = 'customs';
    var $prikey = 'cst_id';
    var $alias  = 'cst';
    var $_name  = 'customs';
    var $_obj_search='cst_name|基本款名称';
    var $_obj_fields='cst_id|ID,cst_name|名称,cst_cost|成本价,cst_price|价格,cst_image|大图,cst_dis_image|小图';
    var $_obj_images = 'cst_image,cst_dis_image';
    var $_order  = 'cst_rank desc';
    var $temp; // 临时变量
    var $_relation = array(
		'has_customsparts' => array(
            'model'         => 'customsparts',
            'type'          => HAS_MANY,
            'foreign_key'   => 'cst_id',
            'dependent'     => true
        ),
        //一个商品对应多个评论
        'has_comments' => array(
                'model'         => 'comments',
                'type'          => HAS_MANY,
                'foreign_key'   => 'goods_id',
        ),
       // 基本款/分类
       'has_gcategory' => array(
           'model'         => 'gcategory',
           'type'          => HAS_ONE,
           'foreign_key'   => 'cate_id',
           'refer_key'     => 'cst_cate',
          //'reverse'       => 'belongs_to_customs',
       ),
       // 基本款和会员是多对多的关系（会员收藏商品） ns add
        'be_collect' => array(
            'model'         => 'member',
            'type'          => HAS_AND_BELONGS_TO_MANY,
            'middle_table'  => 'collect',
            'foreign_key'   => 'item_id',
            'ext_limit'     => array('type' => 'customs'),
            'reverse'       => 'collect_customs',
        ),


//        // 一个商品对应一条商品统计记录
//        'has_goodsstatistics' => array(
//            'model'         => 'goodsstatistics',
//            'type'          => HAS_ONE,
//            'foreign_key'   => 'goods_id',
//            'dependent'     => true
//        ),
        // 一个商品对应多个规格
//        'has_customsparts' => array(
//            'model'         => 'customs_parts',
//            'type'          => HAS_MANY,
//            'foreign_key'   => 'cst_id',
//            'dependent'     => true
//        ),
//        // 一个商品只能属于一个店铺
//        'belongs_to_store' => array(
//            'model'         => 'store',
//            'type'          => BELONGS_TO,
//            'foreign_key'   => 'store_id',
//            'reverse'       => 'has_goods',//对面的本数组的键
//        ),
//        // 商品和分类是多对多的关系
//        'belongs_to_gcategory' => array(
//            'model'         => 'gcategory',
//            'type'          => HAS_AND_BELONGS_TO_MANY,
//            'middle_table'  => 'category_goods',
//            'foreign_key'   => 'goods_id',
//            'reverse'       => 'has_goods',
//        ),
//    );

//    var $_autov = array(
//        'goods_name' => array(
//            'required'  => true,
//            'filter'    => 'trim',
//        ),
    );
    /**
     * get the cate
     */
    function get_cate($cstId=0,$cateId=0){
        $_gcategory_mod      =& m('gcategory');
        if($cstId!=0){
            if($cateId!=0){
                $cst=$this->get($cstId);
                return $_gcategory_mod->get($cst['sec_cate']);
            }else{
                $cst=$this->get($cstId);
                return $_gcategory_mod->get($cst['cst_cate']);
            }
        }else{
            //ns add 添加是否显示条件
            return  $_gcategory_mod->findAll(array('conditions'=>'parent_id='.$cateId.' AND store_id=0 AND if_show=1','order'=>'sort_order ASC'));
        }
    }
    /*
     * drop the related data
    */
    function drop_data($ids){
        $customsparts_mod =& m('customsparts');
        $customsparts_mod->drop(db_create_in($ids,'cst_id'));
    }
    /*
     * load part's type info
    */
    function load_parts_type($cateId=0,$cstId=0){
        $_customsparts_mod =& m('customsparts');
        if($cateId!=0){
            $parts_type=$this->get_parts_type($cateId);
        }
        $cst_pt=$_customsparts_mod->find(array("conditions" => 'cst_id='.$cstId,));
        foreach ($cst_pt as $v){
            if($v['top_type']=='' || $v['top_type']==0){
                $parts_type[$v['pt_type']]['check']='1';
                if($v['is_dft']==1)$parts_type[$v['pt_type']]['dft']=$v['pt_id'];
                $parts_type[$v['pt_type']]['items'].=$v['pt_id'].',';
            }else{
                if($v['is_active']==1)$parts_type[$v['top_type']]['son_type'][$v['pt_type']]['check']='1';
                $parts_type[$v['top_type']]['check']='1';
                if($v['is_dft']==1)$parts_type[$v['top_type']]['son_type'][$v['pt_type']]['dft']=$v['pt_id'];
                $parts_type[$v['top_type']]['son_type'][$v['pt_type']]['items'].=$v['pt_id'].',';
            }
        }
        return $parts_type;
    }
    /*
     * get part's type by cateId
    */
    function get_parts_type($cateId=0){
        $_parttype_mod     =& m('parttype');
        $temArrType=$_parttype_mod->find(array("conditions" => 'cate_id='.$cateId,));
        $parts_type=$this->format_parts_type($temArrType);
        return $parts_type;
    }
    /*
     * format the array of parts
    */
    function format_parts_type($arrType=array()){
        $return=array();
        if(!empty($arrType)){
            foreach ($arrType as $row){
                $return[$row['type_id']]['type_id']    =$row['type_id'];
                $return[$row['type_id']]['type_name']  =$row['type_name'];
                $return[$row['type_id']]['cate_id']    =$row['cate_id'];
                $return[$row['type_id']]['parent_id']  =$row['parent_id'];
                $return[$row['type_id']]['sort_order'] =$row['sort_order'];
                if($row['parent_id']!=0){
                    unset($return[$row['type_id']]);
                    $return[$row['parent_id']]['son_type'][$row['type_id']]=$row;
                }
            }
        }
        return $return;
    }
    /*
     * format the post data
    */
    function fmt_Adata($post){
        foreach ($post as $row){
            if($row['type_id']!=''){
                if(isset($row['son_type']) || !empty($row['son_type'])){
                    foreach ($row['son_type'] as $rowSon){
                        if($rowSon['part_items']!='' && $rowSon['type_id']!=''){
                            $items = array_filter(explode(',', $rowSon['part_items']));
                            foreach ($items as $value){
                                $Adata[$value] = array(
                                        'pt_id'			 => $value,
                                        'pt_type'        => $rowSon['type_id'],
                                );
                                if($rowSon['part_dft']==$value){
                                    $Adata[$value]['is_dft']=1;
                                }else{
                                    $Adata[$value]['is_dft']=0;
                                }
                                $Adata[$value]['top_type']=$row['type_id'];
                            }
                        }
                    }
                }else{
                    if($row['part_items']!='' && $row['type_id']!=''){
                        $items = array_filter(explode(',', $row['part_items'])) ;
                        foreach ($items as $value){
                            $Adata[$value] = array(
                                    'pt_id'			 => $value,
                                    'pt_type'        => $row['type_id'],
                            );
                            if($row['part_dft']==$value){
                                $Adata[$value]['is_dft']=1;
                            }else{
                                $Adata[$value]['is_dft']=0;
                            }
                            $Adata[$value]['top_type']=0;
                        }
                    }
                }
            }
        }
        return $Adata;
    }
    
    /*
     * likes api
     */
    function change_likes(){
        
    }
    /*
     * sales api
     */
    function change_sales(){
    	
    }
}

/* 商品业务模型 business model */
class CustomsBModel extends CustomsModel{
    var $_store_id = 0;
    /*
     * 判断名称是否唯一
     */
    function unique($goods_name, $goods_id = 0){
        return true;
    }
}