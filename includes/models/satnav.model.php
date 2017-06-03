<?php

/*  */
class SatnavModel extends BaseModel
{
    var $table  = 'satnav';
    var $prikey = 'satnav_id';
    var $_name  = 'satnav';
    
    
    
    /**
     * 取得分类列表
     *
     * @param int $parent_id 大于等于0表示取某分类的下级分类，小于0表示取所有分类
     * @return array
     */
    function get_list($parent_id = -1)
    {
        $conditions = "1 = 1";
        $parent_id >= 0 && $conditions .= " AND parent_id = '$parent_id'";
        return $this->find(array(
            'conditions' => $conditions,
            'order' => 'sort_order, satnav_id',
        ));
    }
    
    /**
     * 根据id取得信息
     *
     * @param mix $id
     * @return array
     */
    function get_info($id)
    {
        $rows = $this->find(array(
            'conditions' => intval($id),
        ));
        return $rows ? current($rows) : array();
    }
    
    /*
     * 判断是否可以有上下级分类
     */
    function parent_children_valid($parent_id)
    {
        $fdiy_management = $this->get_info($parent_id);
        if($fdiy_management['code'] == ACC_SYSTEM || $fdiy_management['code'] == ACC_NOTICE || $fdiy_management['code'] == ACC_HELP)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    
    /**
     * 取得结构化的分类数据（不包括不显示的分类，数据会缓存）
     *
     * @return array(
     *      0 => array(                             'cids' => array(1, 2, 3))
     *      1 => array('name' => 'abc', 'pid' => 0, 'cids' => array(2, 3, 4)),
     *      2 => array('name) => 'xyz', 'pid' => 1, 'cids' => array()
     * )
     *    分类id        分类名称             父分类id     子分类ids
     */
    function _get_structured_data()
    {
        $cache_server =& cache_server();
        $key  = 'goods_category_0';
        $data = $cache_server->get($key);
        if ($data === false)
        {
            $data = array(0 => array('cids' => array()));
    
            $cate_list = $this->get_list(-1, true);
            foreach ($cate_list as $id => $cate)
            {
                $data[$id] = array(
                    'name' => $cate['name'],
                    'pid'  => $cate['parent_id'],
                    'cids' => array()
                );
            }
    
            foreach ($cate_list as $id => $cate)
            {
                $pid = $cate['parent_id'];
                isset($data[$pid]) && $data[$pid]['cids'][] = $id;
            }
    
            $cache_server->set($key, $data, 1800);
        }
    
        return $data;
    }
    
    
    
    /**
     * 取得某分类的祖先分类（包括自身，按层级排序）
     *
     * @param   int     $id     分类id
     * @param   bool    $cached 是否缓存（缓存数据不包括不显示的分类，一般用于前台；非缓存数据包括不显示的分类，一般用于后台）
     * @return  array(
     *              array('cate_id' => 1, 'cate_name' => '数码产品'),
     *              array('cate_id' => 2, 'cate_name' => '手机'),
     *              ...
     *          )
     */
    function get_ancestor($id, $cached = false)
    {
        $res = array();
    
        if ($cached)
        {
            $data = $this->_get_structured_data();
            if ($id > 0 && isset($data[$id]))
            {
                while ($id > 0)
                {
                    $res[] = array('satnav_id' => $id, 'name' => $data[$id]['name']);
                    $id    = $data[$id]['pid'];
                }
            }
        }
        else
        {
            while ($id > 0)
            {
                $sql = "SELECT satnav_id, name, parent_id FROM {$this->table} WHERE satnav_id = '$id'";
                $row = $this->getRow($sql);
                if ($row)
                {
                    $res[] = array('satnav_id' => $row['satnav_id'], 'name' => $row['name']);
                    $id    = $row['parent_id'];
                }
            }
        }
    
        return array_reverse($res);
    }
    
    function get_parent_id($id){
        $sql = "SELECT parent_id FROM {$this->table} WHERE cate_id = '$id'";
        $row = $this->getRow($sql);
        if($row["parent_id"] == 0){
            return $id;
        }else{
            return $this->get_parent_id($row["parent_id"]);
        }
    }
    
    /**
     * 取得某分类的层级（从1开始算起）
     *
     * @param   int     $id     分类id
     * @param   bool    $cached 是否缓存（缓存数据不包括不显示的分类，一般用于前台；非缓存数据包括不显示的分类，一般用于后台）
     * @return  int     层级     当分类不存在或不显示时返回false
     */
    function get_layer($id, $cached = false)
    {
        $ancestor = $this->get_ancestor($id, $cached);
        if (empty($ancestor))
        {
            return false; //分类不存在或不显示
        }
        else
        {
            return count($ancestor);
        }
    }
    


}
?>