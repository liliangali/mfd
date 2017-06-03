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
    
    function get_cate_g($cstId=0,$cateId=0){
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
            return  $_gcategory_mod->findAll(array('conditions'=>'parent_id='.$cateId.' AND store_id=0 AND if_show=1 AND categoryid = 1 AND cate_id !=5000','order'=>'sort_order ASC'));
        }
    }
    
    /**
     * 删除关联数据
     * @author Ruesin
     */
    function drop_data($ids){
        $customsparts_mod =& m('customsparts');
        $customsparts_mod->drop(db_create_in($ids,'cst_id'));
    }
    /**
     * 获取组件分类层级数组
     * @param $id 分类ID
     * @return array 数组
     * @author Ruesin
     */
    function get_parts_cate($id){
        //还没有进行是否显示的筛选
        $cst =& f('custom');
        $gcategories = $cst ->_get_gcategory();
        
        //     	$tree = $cs->_tree($gcategories);
        //     	$a = $tree->getOptions(0);				//生成嵌套格式的树形
        //     	$a = $tree->getChilds(8001);			//所有的子id
        //     	$a = $tree->getParents(23);				//查找父id 逐级返回. 不包括本身
        $tree = $cst ->deep_tree($gcategories,$id);	//生成嵌套格式的树形数组
        return $tree;
    }

    /**
     * 递归获取层级分类(失败!)
     * @author Ruesin
     */
    function get_parts_cate1($ids){
        $row=array();
        while (strlen($ids)>0){
            $ss=$this->_gcategory->findAll(array(
                'conditions' => " 1 = 1 AND parent_id IN (".$ids.")",
            ));
            if(empty($ss))break;
            foreach ($ss as $s){
                $id .=$s['cate_id'].",";
            }
            $ids="";
            $ids=substr($id, 0 ,strlen($id)-1);
            $row = array_merge($row,$ss);
            unset($ss);
        }
        print_exit($row);
    }
    /**
     * 加载当前基本款下的组件及分类
     * @author Ruesin
     */
    function load_parts_cate($cateId=0,$cstId=0){
        if($cateId==0)return;
    
        $_customsparts_mod =& m('customsparts');
    
        $cates = $this->get_parts_cate($cateId);
    
        $parts = $_customsparts_mod->find(array("conditions" => "cst_id = {$cstId} AND top_cate = {$cateId} ",));

        foreach ($parts as $row){

            switch ($row['depth']){
                case 2:
                    $Arr[$row['grand_cate']][$row['pt_cate']] = $row['pt_cate'];
                    break;
                case 3:
                    $Arr[$row['grand_cate']][$row['parent_cate']][$row['pt_cate']] = $row['pt_cate'];
                    break;
                default:
                    $Arr[$row['grand_cate']] = $row['pt_cate'];
                    break;
            }

        }
        //编辑基本款时剔除当前基本款下没有选择的分类//并把数组下标格式化
         foreach ($cates as $key =>  $cate){
            if(empty($Arr[$cate['cate_id']])){  //一级
                
                unset($cates[$key]);
                
            }else{
                if($cate['children']){
                    
                    foreach ($cate['children'] as $kk => $son){
                        
                        if($son['children']){  //三级
                            
                            foreach ($son['children'] as $k => $child){
                                
                                if(empty($Arr[$cate['cate_id']][$son['cate_id']][$child['cate_id']])){
                                
                                    unset($cates[$key]['children'][$kk]['children'][$k]);
                                
                                }
                            }
                            
                        }else{  //二级
                            
                            if(empty($Arr[$cate['cate_id']][$son['cate_id']])){
                            
                                unset($cates[$key]['children'][$kk]);
                            
                            }
                        }
                          
                    }
                }
            }
        }
        
        foreach ($parts as $v){
            
            if($v['depth'] == '1'){  //只有一层
                
                $cates[$v['pt_cate']]['check']='1';
                
                if($v['is_dft']==1)$cates[$v['pt_cate']]['dft']=$v['pt_id'];
                
                $cates[$v['pt_cate']]['items'].=$v['pt_id'].',';
                
            }else{
                
                if($v['depth'] == '2'){//二层
                    
                    $cates[$v['parent_cate']]['children'][$v['pt_cate']]['check']='1';
                    
                    $cates[$v['parent_cate']]['check']='1';
                    
                    if($v['is_dft']==1)$cates[$v['parent_cate']]['children'][$v['pt_cate']]['dft']=$v['pt_id'];
                    
                    $cates[$v['parent_cate']]['children'][$v['pt_cate']]['items'].=$v['pt_id'].',';
                    
                }else{//三层

                    $cates[$v['grand_cate']]['children'][$v['parent_cate']]['children'][$v['pt_cate']]['check']='1';
                    
                    $cates[$v['grand_cate']]['children'][$v['parent_cate']]['check']='1';
                    
                    $cates[$v['grand_cate']]['check']='1';
                    
                    if($v['is_dft']==1)$cates[$v['grand_cate']]['children'][$v['parent_cate']]['children'][$v['pt_cate']]['dft']=$v['pt_id'];
                    
                    $cates[$v['grand_cate']]['children'][$v['parent_cate']]['children'][$v['pt_cate']]['items'].=$v['pt_id'].',';
                    
                }
                
                
            }
        }

        return $cates;
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


    /**
     * 返回面料分类
     * @param null
     * @return array
     * @author Ruesin
     */
    function getFabricCate(){

        //         $fabric = array('8001','8030','8050');  //面料
        //         if(in_array($id, $fabric)){
        $fabric = array(
            //西装,西裤,马夹
            '8001' => array(
                '8014' => 'flower',
                '8009' => 'color',
                '8019' => 'composition',
            ),
            //衬衣
            '8030' => array(
                '8040' => 'flower',
                '8035' => 'color',
                '8054' => 'composition',
            ),
            //大衣
            '8050' => array(
                '8077' => 'flower',
                '8082' => 'color',
                '8074' => 'composition',
            ),
        );
        return $fabric;
    }
    /**
     * 获取基本款/组件 一级分类
     * @return array
     * @author Ruesin
     */
    function getTopCate(){
        
        $Cates = $this->getCate();
        
        if(empty($Cates)){
            $this->setCate();
        }

        foreach ($this->getCate() as $row){
            $cate[$row['cate_id']] = $row['cate_name'];
        }
        
        return $cate;
    }
    /**
     * 获取基本款/组件分类
     * @return array
     * @author Ruesin
     */
    function getCate(){
        $Cates = getConf('customs','cate');
        return $Cates;
    }
    /**
     * 设置分类配置文件
     * 工艺: 西服 3 , 西裤 2000 , 衬衣 3000 , 马夹 4000 , 配件 5000 , 大衣 6000 ,
     * 面料: 西装 8001 , 衬衣 8030 , 大衣 8050 , 里料 8051
     * @author Ruesin
     */
 
    function setCate(){
        $customs=array(
            '3' => array(
                'cate_id'   => '3',
                'cate_name' => '西服',
                'fabric_m'  => '2.1',
                'lining_m'  => '2',
                'parts_type'=> array(
                    'fabric'    => array(
                        'id'    => '8001',
                        'name'  => '面料选择',
                    ),        
//                     'lining'    => array(
//                         'id'    => '8051',
//                         'name'  => '里料选择',
//                     ),
                    'style'    => array(
                        'id'    => '24',
                        'name'  => '款式风格',
                    ),
                    'depth'    => array(
                        'id'    => '298',
                        'name'  => '深度设计',
                    ),
                ),
            ),
            '2000' => array(
                'cate_id'   => '2000',
                'cate_name' => '西裤',
                'fabric_m'  => '1.5',
                'lining_m'  => '0',
                'parts_type'=> array(
                    'fabric'    => array(
                        'id'    => '8001',
                        'name'  => '面料选择',
                    ),
                    'style'    => array(
                        'id'    => '2021',
                        'name'  => '款式风格',
                    ),
                    'depth'    => array(
                        'id'    => '2157',
                        'name'  => '深度设计',
                    ),
                ),
            ),
            '4000' => array(
                'cate_id'   => '4000',
                'cate_name' => '马夹',
                'fabric_m'  => '0.8',
                'lining_m'  => '0',
                'parts_type'=> array(
                    'fabric'    => array(
                        'id'    => '8001',
                        'name'  => '面料选择',
                    ),
                    'style'    => array(
                        'id'    => '4016',
                        'name'  => '款式风格',
                    ),
                    'depth'    => array(
                        'id'    => '3184',
                        'name'  => '深度设计',
                    ),
                ),
            ),
            '3000' => array(
                'cate_id'   => '3000',
                'cate_name' => '衬衣',
                'fabric_m'  => '2.8',
                'lining_m'  => '0',
                'parts_type'=> array(
                    'fabric'    => array(
                        'id'    => '8030',
                        'name'  => '面料选择',
                    ),
                    'style'    => array(
                        'id'    => '3016',
                        'name'  => '款式风格',
                    ),
                    'depth'    => array(
                        'id'    => '3184',
                        'name'  => '深度设计',
                    ),
                ),
            ),
            '8050' => array(
                'cate_id'   => '8050',
                'cate_name' => '大衣',
                'fabric_m'  => '1.68',
                'lining_m'  => '2',
                'parts_type'=> array(
                    'fabric'    => array(
                        'id'    => '8050',
                        'name'  => '面料选择',
                    ),
//                     'lining'    => array(
//                         'id'    => '8051',
//                         'name'  => '里料选择',
//                     ),
                    'style'    => array(
                        'id'    => '6007',
                        'name'  => '款式风格',
                    ),
                    'depth'    => array(
                        'id'    => '6276',
                        'name'  => '深度设计',
                    ),
                ),
            ),
        );
        return setConf('customs','cate',$customs);
    }
}