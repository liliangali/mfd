<?php

/* 地区 region */
class RegionModel extends BaseModel
{
    var $table  = 'region';
    var $prikey = 'region_id';
    var $_name  = 'region';

    var $_relation  = array(
        // 一个地区有多个子地区
        'has_region' => array(
            'model'         => 'region',
            'type'          => HAS_MANY,
            'foreign_key'   => 'parent_id',
            'dependent'     => true
        ),
    );

    var $_autov = array(
        'region_name' => array(
            'required'  => true,
            'filter'    => 'trim',
        ),
        'sort_order'    => array(
            'filter'    => 'intval',
        ),
    );

    /**
     * 取得地区列表
     *
     * @param int $parent_id 大于等于0表示取某个地区的下级地区，小于0表示取所有地区
     * @return array
     */
    function get_list($parent_id = -1)
    {
        if ($parent_id >= 0)
        {
            return $this->find(array(
                'conditions' => "parent_id = '$parent_id'",
                'order' => 'sort_order, region_id',
            ));
        }
        else
        {
            return $this->find(array(
                'order' => 'sort_order, region_id',
            ));
        }
    }

    /*
     * 判断名称是否唯一
     */
    function unique($region_name, $parent_id, $region_id = 0)
    {
        $conditions = "parent_id = '" . $parent_id . "' AND region_name = '" . $region_name . "'";
        $region_id && $conditions .= " AND region_id <> '" . $region_id . "'";
        return count($this->find(array('conditions' => $conditions))) == 0;
    }

    /**
     * 取得options，用于下拉列表
     */
    function get_options($parent_id = 0)
    {
        $res = array();
        $regions = $this->get_list($parent_id);
        foreach ($regions as $region)
        {
            $res[$region['region_id']] = $region['region_name'];
        }
        return $res;
    }
    
    /**
    * 获得option拼接起来的字符串
    * @version 1.0.0
    * @author liang.li <1184820705@qq.com>
    * @2015-1-21
    */
    function get_options_html($parent_id,$cur_region_id) 
    {
    	$res = array();
    	$html = "";
        $regions = $this->get_list($parent_id);
        if ($regions) 
        {		
        	foreach ($regions as $key => $value)
        	{
        		if ($key == $cur_region_id)
        		{
        			$html .= "<option value=".$key." selected>".$value['region_name']."</option>";
        		}
        		else 
        		{
        			$html .= "<option value=".$key.">".$value['region_name']."</option>";
        		}
        		
        	}
        }
        return $html;
    }
    
    /**
    * 根据region_id获得region_name
    * @version 1.0.0
    * @author liang.li <1184820705@qq.com>
    * @2015-1-22
    */
    function getRegionName($region_id) 
    {	
    	if ($region_id) 
    	{
    		$region_info = $this->get($region_id);
    		return $region_info['region_name'];
    	}
    	return '';
    }

    /**
     * 取得某地区的所有子孙地区id
     */
    function get_descendant($id)
    {
        $ids = array($id);
        $ids_total = array();
        $this->_get_descendant($ids, $ids_total);
        return array_unique($ids_total);
    }

    /**
     * 取得某地区的所有父级地区
     *
     * @author Garbin
     * @param  int $region_id
     * @return void
     **/
    function get_parents($region_id)
    {
        $parents = array();
        $region = $this->get($region_id);
        if (!empty($region))
        {
            if ($region['parent_id'])
            {
                $tmp = $this->get_parents($region['parent_id']);
                $parents = array_merge($parents, $tmp);
                $parents[] = $region['parent_id'];
            }
            $parents[] = $region_id;
        }

        return array_unique($parents);
    }

    function _get_descendant($ids, &$ids_total)
    {
        $childs = $this->find(array(
            'fields' => 'region_id',
            'conditions' => "parent_id " . db_create_in($ids)
        ));
        $ids_total = array_merge($ids_total, $ids);
        $ids = array();
        foreach ($childs as $child)
        {
            $ids[] = $child['region_id'];
        }
        if (empty($ids))
        {
            return ;
        }
        $this->_get_descendant($ids, $ids_total);
    }
    /**     * 获得包含服务点下的二级分类列表     * @version 1.0.0     * @author liang.li <1184820705@qq.com>     * @2015-3-31     */    function getServeRegion()    {    	$region_list = $this->find(array(    			'conditions' => "parent_id=2 AND is_serve=1",    	));    	$region = array();    	if ($region_list)    	{    		foreach ($region_list as $key => $value)    		{    			$region[$key] = $value['region_name'];    		}    	}    	     	return $region;    }
}

?>
