<?php
/* 数据模型 */
class CustomsModel extends BaseModel{
    var $table  = 'customs';
    var $prikey = 'cst_id';
    var $alias  = 'cst';
    var $_name  = 'customs';
    var $_obj_search='cst_name|基本款名称,cst_sn|编号';
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
     * 基本款编号必须唯一
     * @author Ruesin
     */
    function unique_cst_sn($sn,$id){
        $conditions = " cst_sn = '{$sn}' AND cst_id !='{$id}'";
        return (count($this->find($conditions)) == 0);
    }
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
//         $gcategories = $cst ->_get_gcategory(" if_show = 1 AND alias != '' ");
        $gcategories = $cst ->_get_gcategory(" if_show = 1 ");
        
        //     	$tree = $cs->_tree($gcategories);
        //     	$a = $tree->getOptions(0);				//生成嵌套格式的树形
        //     	$a = $tree->getChilds(8001);			//所有的子id
        //     	$a = $tree->getParents(23);				//查找父id 逐级返回. 不包括本身
        $tree = $cst ->deep_tree($gcategories,$id);	//生成嵌套格式的树形数组
        return $tree;
    }
    /**
     * 加载当前基本款下的组件及分类
     * @author Ruesin
     */
    function load_parts_cate($cateId=0,$cstId=0){
        if($cateId==0)return;

        if($cateId == 8001 || $cateId == 8050 || $cateId == 8030){
            $cates = Array(
                    $cateId => Array(
                            'cate_id'   => $cateId,
                            'cate_name' => '面料选择',
                    ));
             
        }else{
            $cates = $this->get_parts_cate($cateId);
        }

        if($cstId == 0) return $cates;
        
        $_customsparts_mod =& m('customsparts');
        
        $parts = $_customsparts_mod->find(array("conditions" => "cst_id = {$cstId} AND top_cate = {$cateId} ",));
        //这里需要与小5商量下怎么存取数据//如果全部存8001,我后台的存/取都不用格式化一遍格式了//如果存的时候为了方便小五取,格式化了,编辑的时候也要反格式化一下
        if($cateId == 8001 || $cateId == 8030 || $cateId == 8050){
        	$parts = $this->cate_mtos($parts);
        }
        
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
    /**
     * 面料分类多层转单层,编辑基本款时用到
     * @param 面料分类
     * @return array
     * @author Ruesin
     */
    function cate_mtos($parts){
        
        foreach ($parts as $key=>$row){

            
            $parts[$key]['pt_cate']     = $row['top_cate'];
            $parts[$key]['parent_cate'] = $row['top_cate'];
            $parts[$key]['grand_cate']  = $row['top_cate'];
            $parts[$key]['depth']       = 1;
            
        }
        return $parts;
        
    }
    /**
     * 面料单层转多层,新增基本款存储数据到中间表是用到
     * @param 面料根层分类
     * @return array
     * @author Ruesin
     */
    function cate_stom($cates){
        if($cates['pt_cate']==8001 || $cates['pt_cate']==8030 || $cates['pt_cate']==8050){
            $mPart =& m('part');
            $mCate =& m('gcategory');
            
            $parts = $mPart->get($cates['pt_id']);
            
            if($parts['vcompositionid'] != 0){
                $cate = $parts['vcompositionid'];
            }elseif ($parts['vcolorid'] != 0){
                $cate = $parts['vcolorid'];
            }elseif ($parts['vflowerid'] != 0){
                $cate = $parts['vflowerid'];
            }else{
                $cate = $cates['pt_cate'];
            }
            $depth  = 1;
            $parent = $grand = $cate;
            
            $cats = $mCate -> get($cate);
            if($cats['parent_id'] != 0){
                $depth  = 2;
            	$parent = $cats['parent_id'];
            	$grand  = $cats['parent_id'];
            	
            	$grands = $mCate -> get($parent);
            	if($grands['parent_id'] != 0){
            	    if($grands['parent_id'] != 8001 && $grands['parent_id'] != 8030 && $grands['parent_id'] != 8050){
            	        $depth  = 3;
            	        $grand = $grands['parent_id'];
            	    }
            		
            	}
            	
            }

            $cates['pt_cate']     = $cate;
            $cates['parent_cate'] = $parent;
            $cates['grand_cate']  = $grand;
            $cates['depth']       = $depth;
            
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
                
                'craft'     => array(
            	    'id'   => '435',
            	    'name' => '工艺类型',
            	    'son'  =>array(
            	        '1230' => '手工艺',
            	        '431'  => '衬类型'
            	    ),
                ),
                
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
                
                'craft'     => array(
                    'id'   => '2224',
                    'name' => '工艺选择',
                ),
                
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
                        'id'    => '4075',
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
            '6000' => array(
                'cate_id'   => '6000',
                'cate_name' => '大衣',
                'fabric_m'  => '1.68',
                'lining_m'  => '2',
                
                'craft'     => array(
                    'id'   => '6409',
                    'name' => '工艺类别',
                ),
                
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
    /**
     * 获取分类下的工艺分类
     * @return array
     * @author Ruesin
     */
    function getCraft(){
    	$cates = $this->getCate();
    	foreach ($cates as $row){
    	    
    	    if(isset($row['craft']) && !empty($row['craft'])){
    	        $craft[$row['craft']['id']] = $row['craft'];
    	    }
    		
    	}
    	return $craft;
    }
}