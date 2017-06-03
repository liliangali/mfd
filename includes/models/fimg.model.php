<?php

/* 会员 member */
class FimgModel extends BaseModel
{
    var $table  = 'fimg';
    var $prikey = 'id';
    var $_name  = 'fimg';
	var $_relation=array(
			/* 一个广告属于一个广告位 */
			'has_tpl'=>array(
					'model'=>'tpl',//模型的名称
					'type'=>HAS_ONE,//关系类型
					'foreign_key'=>'tid',//外键名
					'refer_key'=>'tpl_id',//关联键名
					'dependent'=>true//依赖
			),
	);
}

?>