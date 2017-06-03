<?php

/**
*-----------------------------------------------------------
*宠物种类模型类
*-----------------------------------------------------------
*@access public
*@author cyrus <2621270755@qq.com>
*@date 2016年5月23日
*@version 1.0
*@return 
*/
class PetTypeModel extends BaseModel
{
    var $table  = 'pet_type';
    var $prikey = 'type_id';
    var $_name  = 'pettype';
    var $alias  ='pt';

    var $_relation  = array(
        // 一个种类有多个子种类
        'has_types' => array(
            'model'         => 'pettype',
            'type'          => HAS_MANY,
            'foreign_key'   => 'parent_id',
            'dependent'     => true
        ),
    );

    var $_autov = array(
        'type_name' => array(
            'required'  => true,
            'filter'    => 'trim',
        ),
        'sort_order'    => array(
            'filter'    => 'intval',
        ),
    );

    /**
    *-----------------------------------------------------------
    *取得种类列表
    *-----------------------------------------------------------
    *@access public
    *@author cyrus <2621270755@qq.com>
    *@param int $parent_id 大于等于0表示取某个地区的下级地区，小于0表示取所有地区
    *@date 2016年5月23日
    *@version 1.0
    *@return array
    */
    function get_list($parent_id = -1)
    {
        if ($parent_id >= 0)
        {
            return $this->find(array(
                'conditions' => "parent_id = '$parent_id'",
                'order' => 'sort_order, type_id',
            ));
        }
        else
        {
            return $this->find(array(
                'order' => 'sort_order, type_id',
            ));
        }
    }
//END function
    
    /**
    *-----------------------------------------------------------
    *判断名称是否唯一
    *-----------------------------------------------------------
    *@param string $type_name,int $parent_id,int $type_id
    *@access public
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月23日
    *@version 1.0
    *@return 
    */
    function unique($type_name, $parent_id, $type_id = 0)
    {
        $conditions = "parent_id = '" . $parent_id . "' AND type_name = '" . $type_name . "'";
        $type_id && $conditions .= " AND type_id <> '" . $type_id . "'";
        return count($this->find(array('conditions' => $conditions))) == 0;
    }
//END function

    /**
    *-----------------------------------------------------------
    *取得options，用于下拉列表
    *-----------------------------------------------------------
    *@access public
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月23日
    *@version 1.0
    *@return 
    */
    function get_options($parent_id = 0)
    {
        $res = array();
        $types = $this->get_list($parent_id);
        foreach ($types as $type)
        {
            $res[$type['type_id']] = $type['type_name'];
        }
        return $res;
    }
    //END function
    
    /**
    *-----------------------------------------------------------
    *获得option拼接起来的字符串
    *-----------------------------------------------------------
    *@access public
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月23日
    *@version 1.0
    *@return 
    */
    function get_options_html($parent_id,$cur_type_id) 
    {
    	$res = array();
    	$html = "";
        $types = $this->get_list($parent_id);
        if ($types) 
        {		
        	foreach ($types as $key => $value)
        	{
        		if ($key == $cur_type_id)
        		{
        			$html .= "<option value=".$key." selected>".$value['type_name']."</option>";
        		}
        		else 
        		{
        			$html .= "<option value=".$key.">".$value['type_name']."</option>";
        		}
        		
        	}
        }
        return $html;
    }
    //END function
    

    /**
    *-----------------------------------------------------------
    *根据type_id获得type_name
    *-----------------------------------------------------------
    *@access public
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月23日
    *@version 1.0
    *@return 
    */
    function getTypeName($type_id) 
    {	
    	if ($type_id) 
    	{
    		$type_info = $this->get($type_id);
    		return $type_info['type_name'];
    	}
    	return '';
    }
//END function
	
    /**
    *-----------------------------------------------------------
    *取得某地区的所有子孙地区id
    *-----------------------------------------------------------
    *@access public
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月23日
    *@version 1.0
    *@return 
    */
    function get_descendant($id)
    {
        $ids = array($id);
        $ids_total = array();
        $this->_get_descendant($ids, $ids_total);
        return array_unique($ids_total);
    }
//END function
    /**
     * 
     *
     * @author Garbin
     * @param  int $type_id
     * @return void
     **/
    /**
    *-----------------------------------------------------------
    *取得某地区的所有父级地区
    *-----------------------------------------------------------
    *@access public
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月23日
    *@version 1.0
    *@return array
    */
    function get_parents($type_id)
    {
        $parents = array();
        $type = $this->get($type_id);
        if (!empty($type))
        {
            if ($type['parent_id'])
            {
                $tmp = $this->get_parents($type['parent_id']);
                $parents = array_merge($parents, $tmp);
                $parents[] = $type['parent_id'];
            }
            $parents[] = $type_id;
        }

        return array_unique($parents);
    }
//END function

    /**
    *-----------------------------------------------------------
    *得到一些种类id及其子孙id的集合
    *-----------------------------------------------------------
    *@access public
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月23日
    *@version 1.0
    *@return 
    */
    function _get_descendant($ids, &$ids_total)
    {
        $childs = $this->find(array(
            'fields' => 'type_id',
            'conditions' => "parent_id " . db_create_in($ids)
        ));
        $ids_total = array_merge($ids_total, $ids);
        $ids = array();
        foreach ($childs as $child)
        {
            $ids[] = $child['type_id'];
        }
        if (empty($ids))
        {
            return ;
        }
        $this->_get_descendant($ids, $ids_total);
    }
    //END function
    
    /**
    *-----------------------------------------------------------
    *获得包含服务点下的二级分类列表
    *-----------------------------------------------------------
    *@access public
    *@author cyrus <2621270755@qq.com>
    *@date 2016年5月23日
    *@version 1.0
    *@return 
    */    function getServeType()    {    	$type_list = $this->find(array(    			'conditions' => "parent_id=2 AND is_serve=1",    	));    	$type = array();    	if ($type_list)    	{    		foreach ($type_list as $key => $value)    		{    			$type[$key] = $value['type_name'];    		}    	}    	     	return $type;    }
    //END function
}

?>
