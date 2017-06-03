<?php

/**
 * 属性规格模型
 *
 * @author liang.li <1184820705@qq.com>
 * @version $Id: attribute.model.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 mfd.com
 * @package attribute.model.php
 */
/**
 * 属性规格模型
 * @see ProductsModel
 * @version 1.0.0 (2014-11-18)
 * @author liang.li <1184820705@qq.com>
 * @package attribute.model.php
 */
class AttributeModel extends BaseModel
{
    var $table  = 'attribute';
    var $prikey = 'attr_id';
    var $alias  = 'attr';
    var $_name  = 'attribute';
    
    var $_relation = array(
    		'has_goodsattr' => array(
    				'model'         => 'goodsattr',
    				'type'          => BELONGS_TO,
    				'foreign_key'   => 'attr_id',
    		        'reverse'       => 'has_attr',
    		),
    );
    
	/**
     * 判断名称是否唯一
     * @param string $attrName
     * @param int $type_id 类型id
     * @param int $attr_id 属性id
     * @return boolean
     */
    function unique($attr_name, $type_id,$attr_id=0)
    {
    	$conditions = "attr_name = '" . $attr_name . "' AND type_id = ".$type_id." AND attr_id != $attr_id";
    	return count($this->find(array('conditions' => $conditions))) == 0;
    }
    
    function getImage()
    {
    	$arr = array (				'衬衫' => array (						'口袋形式' => array (								'放口袋单开线、暗袋' => array (										'img' => 'upload/attr/00001.jpg' 								),								'三角口袋直折边' => array (										'img' => 'upload/attr/00002.jpg' 								),								'位置：无' => array (										'img' => 'upload/attr/00003.jpg' 								) 						) 				),				'西服' => array (						'后开叉方式' => array (								'单开叉' => array (										'img' => 'upload/attr/00004.jpg' 								),								'双开叉' => array (										'img' => 'upload/attr/00005.jpg' 								),								'无开叉' => array (										'img' => 'upload/attr/00006.jpg' 								) 						),						'前门扣方式' => array (								'单排两粒扣' => array (										'img' => 'upload/attr/00007.jpg' 								),								'单排六粒扣' => array (										'img' => 'upload/attr/00008.jpg' 								),								'单排三粒扣' => array (										'img' => 'upload/attr/00009.jpg' 								),								'单排四粒扣' => array (										'img' => 'upload/attr/00010.jpg' 								),								'单排五粒扣' => array (										'img' => 'upload/attr/00011.jpg' 								),								'单排一粒扣' => array (										'img' => 'upload/attr/00012.jpg' 								),								'双排扣' => array (										'img' => 'upload/attr/00013.jpg' 								),								'双排六扣二' => array (										'img' => 'upload/attr/00014.jpg' 								),								'双排六扣三' => array (										'img' => 'upload/attr/00015.jpg' 								),								'双排六扣一' => array (										'img' => 'upload/attr/00016.jpg' 								),								'双排四扣二' => array (										'img' => 'upload/attr/00017.jpg' 								),								'双排四扣一' => array (										'img' => 'upload/attr/000018.jpg' 								) 						),						'下口袋形式' => array (								'2.5cm单开线下口袋' => array (										'img' => 'upload/attr/00019.jpg' 								),								'标准' => array (										'img' => 'upload/attr/00020.jpg' 								),								'双开线' => array (										'img' => 'upload/attr/00021.jpg' 								),								'双开线下口袋加双开线票袋' => array (										'img' => 'upload/attr/00022.jpg' 								),								'正常单开线下口袋' => array (										'img' => 'upload/attr/00023.jpg' 								),								'正常明贴下口袋' => array (										'img' => 'upload/attr/00024.jpg' 								),								'正常下口袋加票袋' => array (										'img' => 'upload/attr/00025.jpg' 								),								'正常斜下口袋' => array (										'img' => 'upload/attr/00026.jpg' 								) 						) 				) 		);
    	return $arr;
    }
    
    //刺绣字体
    public function color(){
    	return array(
    			'430'=>array('name'=>'白','img'=>'http://img.rcmtm.com/process/422/430_S.png','limg'=>"/view/sc-utf-8/mall/default/styles/default/images/color-01.png"),
    			'450'=>array('name'=>'灰','img'=>'http://img.rcmtm.com/process/422/450_S.png','limg'=>"/view/sc-utf-8/mall/default/styles/default/images/color-02.png"),
    			'839'=>array('name'=>'紫','img'=>'http://img.rcmtm.com/process/422/839_S.png','limg'=>"/view/sc-utf-8/mall/default/styles/default/images/color-01.png"),
    			'3172'=>array('name'=>'金','img'=>'http://img.rcmtm.com/process/422/3172_S.png','limg'=>"/view/sc-utf-8/mall/default/styles/default/images/color-02.png"),
  
    			);
    }
    //刺绣颜色
    public function fontParent(){    	return array(    			'528'=>array('name'=>'RC01-书法字体','img'=>''),    			'529'=>array('name'=>'RC02-罗马字体','img'=>''),    			'530'=>array('name'=>'RC03-粗体','img'=>''),    			'531'=>array('name'=>'RC04-手写体','img'=>'')    	);    }
    
    
    
}

?>