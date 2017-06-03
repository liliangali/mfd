<?php

/* 组件数据模型 */
class YH_templateModel extends BaseModel
{
		var $table  = 'yh_template';
		var $prikey = 'id';
		var $_name  = 'yh_template';
	
		/* 添加编辑时自动验证 */
		var $_autov = array(
				'title' => array(
						'required'  => true,    //必填
						'min'       => 1,       //最短1个字符
						'max'       => 100,     //最长100个字符
						'filter'    => 'trim',
				),
				
				'sort_order'  => array(
						'filter'    => 'intval',
				)
				
			
	
		);
	
		function praiseUp($id){
			return $this->db->query("UPDATE {$this->table} SET praise_num = praise_num+1 WHERE {$this->prikey} = {$id}");
		}

}


?>