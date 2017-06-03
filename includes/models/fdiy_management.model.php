<?php
/**
 *  面料diy配置管理
 *
 * 主要实现diy可选项控制
 * --------------------------------------------------------
 * @author       小五
 * $Id: fdiy_management.model.php 14067 2016-01-31 00:45:20Z lil $
 * $Date: 2016-01-31 08:45:20 +0800 (Sun, 31 Jan 2016) $
 * --------------------------------------------------------
 */
class Fdiy_managementModel extends BaseModel
{
    var $table  = 'fdiy_management';
    var $prikey = 'cate_id';
    var $_name  = 'fdiy_management';
    var $_relation = array( 
        // 一个工艺分类有多篇工艺
//         'has_article' => array(
//             'model'         => 'article',
//             'type'          => HAS_MANY,
//             'foreign_key'   => 'cate_id'
//         ),
        // 一个分类有多个子分类
        'has_fdiy_management' => array(
            'model'         => 'fdiy_management',
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
            'order' => 'sort_order, cate_id',
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
    
    /**     * 获取分类数据     * @return array()     * 'conditions'=>db_create_in($cateIds,'cate_id'),
     * $conditions .= " AND parent_id = '$parent_id'";     */    public  function _get_pdata($conditions='',$cache=''){    	/* 获取组件类型表 - 缓存数组关联 */    	if ($cache)    	{    		$conditions = empty($conditions) ? "1=1" : $conditions;    		$p_c_data = $this->find(array('conditions'=>$conditions));    		return $p_c_data;    	}    	     	$cache_server =& cache_server();    	$key = 'page_of_fdiy_management_'.md5($conditions);    	$p_c_data = $cache_server->get($key);    	$p_c_data = false;    	$cached = true;    	if ($p_c_data === false){    		$conditions = empty($conditions) ? "1=1" : $conditions;    		$p_c_data = $this->find(array('conditions'=>$conditions));    		$cache_server->set($key, $p_c_data, 1800);    	}    	return $p_c_data;    }
    
    /**     * 生成嵌套格式的树形数组     * @param arrary 	$gcategories	数据源     * @param int 		$root			父节点     * @return array|false				array(..."children"=>array(..."children"=>array(...)))     * @author 小五     */    function deep_tree($gcategories,$root=0){
//     	var_dump($gcategories);exit();    	if(!$gcategories){    		return FALSE;    	}    	$pk="cate_id";    	$parentKey="parent_id";    	$childrenKey="children";    	$tree=array();//最终数组    	$refer=array();//存储主键与数组单元的引用关系    	//遍历    	foreach($gcategories as $k=>$v){    		if(!isset($v[$pk]) || !isset($v[$parentKey]) || isset($v[$childrenKey])){    			unset($gcategories[$k]);    			continue;    		}    		$refer[$v[$pk]]=&$gcategories[$k];//为每个数组成员建立引用关系    	}    	//遍历子节点    	foreach($gcategories as $k=>$v){    		if($v[$parentKey]==$root){//根分类直接添加引用到tree中    			$tree[$gcategories[$k]['cate_id']]=&$gcategories[$k];    		}else{    			if(isset($refer[$v[$parentKey]])){    				$parent=&$refer[$v[$parentKey]];//获取父分类的引用    				$parent[$childrenKey][$gcategories[$k]['cate_id']]=&$gcategories[$k];//在父分类的children中再添加一个引用成员    			}    		}    	}    	return $tree;    }
}

?>