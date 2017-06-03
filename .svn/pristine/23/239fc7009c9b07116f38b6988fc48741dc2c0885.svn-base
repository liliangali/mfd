<?php
use Psr\Log\NullLogger;
/* 文章 article */
class Fabric_InfoModel extends BaseModel {
	var $table = 'fabric_info';
	var $prikey = 'fabric_id';
	var $_name = 'fabric_info';
	var $alias = 'fi';
	var $fee_point=102;//判断面料费用的临界点
	var $fabric_x = array ( // ===== 面料系数取决于面料价格 =====
			'0' => '2', // ===== 当 面料费 < 102元时取此值 =====
			'1' => '3',// ===== 当 面料费 >= 102元时取此值 =====
	);
	var $_category = array (
			'10' => array (
					'name' => '套装',
					'mldh' => '3.5',
					'service' => array ( // ===== 服务费/加工费 =====
							'0' => '880', // ===== 当 面料费 < 102元时取此值 =====
							'1' => '1180',// ===== 当 面料费 ≥ 102元时取此值 =====
					), 
			),
			'1' =>array (
					'name' => '西服',
					'mldh' => '2.0',
					'service' => array ( // ===== 服务费/加工费 =====
							'0' => '580', // ===== 当 面料费 < 102元时取此值 =====
							'1' => '780',// ===== 当 面料费 ≥ 102元时取此值 =====
					), 
			), 
			'2' => array (
					'name' => '西裤',
					'mldh' => '1.5',
					'service' => array ( // ===== 服务费/加工费 =====
							'0' => '300', // ===== 当 面料费 < 102元时取此值 =====
							'1' => '400',// ===== 当 面料费 ≥ 102元时取此值 =====
					), 
			),
			'3' => array (
					'name' => '衬衣',
					'mldh' => '1.5',
					'service' => array ( // ===== 服务费/加工费 =====
							'0' => '130', // ===== 当 面料费 < 102元时取此值 =====
							'1' => '160',// ===== 当 面料费 ≥ 102元时取此值 =====
					), 
			),
			'4' => array (
					'name' => '大衣',
					'mldh' => '2.5',
					'service' => array ( // ===== 服务费/加工费 =====
							'0' => '800', // ===== 当 面料费 < 102元时取此值 =====
							'1' => '1000',// ===== 当 面料费 ≥ 102元时取此值 =====
					), 
			),
			'5' =>array (
					'name' => '马甲',
					'mldh' => '1',
					'service' => array ( // ===== 服务费/加工费 =====
							'0' => '200', // ===== 当 面料费 < 102元时取此值 =====
							'1' => '250',// ===== 当 面料费 ≥ 102元时取此值 =====
					), 
			),
			'6' => array (
					'name' => '西裙',
					'mldh' => '1.5',
					'service' => array ( // ===== 服务费/加工费 =====
							'0' => '100', // ===== 当 面料费 < 102元时取此值 =====
							'1' => '150',// ===== 当 面料费 ≥ 102元时取此值 =====
					), 
			),
 			'7' => array (
					'name' => '立领',
					'mldh' => '2.0',
					'service' => array ( // ===== 服务费/加工费 =====
							'0' => '580', // ===== 当 面料费 < 102元时取此值 =====
							'1' => '780',// ===== 当 面料费 ≥ 102元时取此值 =====
					),
			), 
	);
	// 面料下单专用分类
	var $_fcategory = array (
			'10' => '套装',
			'1' => '西服',
			'2' => '西裤',
			'3' => '衬衣',
			'4' => '大衣',
			'5' => '马甲',
			'7' => '立领',
	);
	var $_relation = array (
			'has_region' => array (
					'model' => 'fbcategory',
					'type' => HAS_ONE,
					'foreign_key' => 'cate_id',
					'refer_key' => 'region_id' 
			),
			'has_brand' => array (
					'model' => 'fbcategory',
					'type' => HAS_ONE,
					'foreign_key' => 'cate_id',
					'refer_key' => 'brand_id' 
			),
			'has_stock'=>array(
					'model'=>'fabric',
					'type'=>HAS_ONE,
					'foreign_key'=>'CODE',
					'refer_key'=>'fabric_sn'
			) 
	);
	function check_unique($fabric_sn, $fabric_id = 0) {
		$conditions = '';
		if ($fabric_id) {
			$conditions = " AND fabric_id<>'{$fabric_id}'";
		}
		$rows = $this->find ( array (
				'conditions' => "fabric_sn='{$fabric_sn}' " . $conditions 
		) );
		if ($rows) {
			return false;
		}
		return true;
	}
}

?>