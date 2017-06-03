<?php

/* 文章分类 pcategory */
class IcategoryModel extends BaseModel
{
    var $table  = 'icategory';
    var $prikey = 'cate_id';
    var $_name  = 'icategory';
    var $alias ='ica';
    var $_relation = array(
        // 一个消息分类有多个消息
        'has_messages' => array(
            'model'         => 'messagetemplate',
            'type'          => HAS_ONE,
            'foreign_key'   => 'parent_id',
        	//'reverse'           => 'belongs_to_icategory',
        ),
        // 一个分类有多个子分类
        'has_icategory' => array(
            'model'         => 'icategory',
            'type'          => HAS_MANY,
            'foreign_key' => 'parent_id',
            'dependent' => true
        ),
    );
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
            'order' => 'cate_id',
        ));
    }

        /*
     * 判断名称是否唯一
     */
    function unique($cate_name, $parent_id, $cate_id = 0)
    {
        $conditions = "parent_id = '$parent_id' AND cate_name = '$cate_name'";
        $cate_id && $conditions .= " AND cate_id <> '" . $cate_id . "'";
        return count($this->find(array('conditions' => $conditions))) == 0;
    }
    
    /*
     * 判断分类类型是否唯一
     */
    function unique_type($parent_id, $cate_id = 0)
    {
    	$conditions = "parent_id = '$parent_id'";
    	$cate_id && $conditions .= " AND cate_id <> '" . $cate_id . "'";
    	return count($this->find(array('conditions' => $conditions))) == 0;
    }

     /*
     * 判断是否可以有上下级分类
     */
    function parent_children_valid($parent_id)
    {
        $pcategory = $this->get_info($parent_id);
        if($pcategory['code'] == ACC_SYSTEM || $pcategory['code'] == ACC_NOTICE)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

        /**
     * 把某分类及其上级分类加到数组前
     */
    function get_parents(&$parents, $id)
    {
        $data = $this->get(intval($id));
        array_unshift($parents, array('cate_id' => $data['cate_id'], 'cate_name' => $data['cate_name'], 'code' => $data['code']));
        if ($data['parent_id'] > 0)
        {
            $this->get_parents($parents, $data['parent_id']);
        }
    }

    /**
     * 取得某分类的所有子孙分类id
     */
    function get_descendant($id)
    {
        if (!$this->find("cate_id = '$id'"))
        {
            return false;
        }
        $ids = array($id);
        $this->_get_descendant($ids, $id);
        return $ids;
    }
    function _get_descendant(&$ids, $id)
    {
        $childs = $this->find("parent_id = '$id'");
        foreach ($childs as $child)
        {
            $ids[] = $child['cate_id'];
            $this->_get_descendant($ids, $child['cate_id']);
        }
    }
    function get_ACC($ACC_code = '')
    {
        if ($ACC_code)
        {
            $ACC = $this->get("code = '$ACC_code'");
            return isset($ACC['cate_id'])? $ACC['cate_id'] :false;
        }
        else
        {
            $ACC_code = array(ACC_HELP, ACC_NOTICE, ACC_SYSTEM);
            $data = $this->find('code '.db_create_in($ACC_code));
            foreach ($data as $v){
                $ACC[$v['code']] = $v['cate_id'];
            }
            return isset($ACC) ? $ACC :false;
        }
    }
}

?>