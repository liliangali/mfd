<?php
/*数据模型 */
class CustomspartsModel extends BaseModel{
    var $table  = 'customs_parts';
    var $prikey = 'id';
    var $alias  = 'cst_pt';
    var $_name  = 'customsparts';
    var $temp; // 临时变量
   var $_relation = array(
       
       // 基本款/分类
       'has_parts' => array(
           'model'         => 'part',
           'type'          => HAS_ONE,
           'foreign_key'   => 'part_id',
           'refer_key'     => 'pt_id',
           //'reverse'       => 'belongs_to_customs',
       ),
       
       /* // 一个商品对应一条商品统计记录
       'has_goodsstatistics' => array(
           'model'         => 'goodsstatistics',
           'type'          => HAS_ONE,
           'foreign_key'   => 'goods_id',
           'dependent'     => true
       ),
       // 一个商品对应多个规格
       'has_goodsspec' => array(
           'model'         => 'goodsspec',
           'type'          => HAS_MANY,
           'foreign_key'   => 'goods_id',
           'dependent'     => true
       ),
       // 一个商品只能属于一个店铺
       'belongs_to_store' => array(
           'model'         => 'store',
           'type'          => BELONGS_TO,
           'foreign_key'   => 'store_id',
           'reverse'       => 'has_goods',//对面的本数组的键
       ),
       // 商品和分类是多对多的关系
       'belongs_to_gcategory' => array(
           'model'         => 'gcategory',
           'type'          => HAS_AND_BELONGS_TO_MANY,
           'middle_table'  => 'category_goods',
           'foreign_key'   => 'goods_id',
           'reverse'       => 'has_goods',
       ), */
   
	   'belongs_to_customs' => array(
	   		'model'         => 'customs',
	   		'type'          => BELONGS_TO,
	   		'foreign_key'   => 'cst_id',
	   		'reverse'       => 'has_customsparts',
	   ), 
   );

  /*  var $_autov = array(
       'goods_name' => array(
           'required'  => true,
           'filter'    => 'trim',
       ),
   ); */

    
   


}
