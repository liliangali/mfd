<?php
/**
 * 工艺管理控制器
 *
 * @author Ruesin <ruesin@163.com>
 * @version $Id: craft.app.php 4291 2015-05-30 02:41:45Z gaofei $
 * @copyright Copyright 2014 redcollar
 */
class CraftApp extends BackendApp
{
    public $_mod_craft_parent;
    public $_cloth;
    function __construct()
    {
        $this->CraftApp();
        $this->_mod_craft_parent = &m('mtmcraftparent');
        $this->_cloth =array(
                3    => '上衣',
                2000 => '西裤',
                3000 => '衬衣',
                4000 => '马夹',
                6000 => '大衣',
        );
        
    }
    function CraftApp()
    {
        parent::BackendApp();
    }
    
    function parents(){
        $this->import_resource(array('script' => 'inline_edit_admin.js'));
        //=====
        $conditions = $this->_get_query_conditions(array(
                array(
                        'field' => 'clothingID',
                        'equal' => '=',
                ),
                array(
                        'field' => 'name',
                        'equal' => 'like',
                ),
        ));
        //update order
        if (isset($_GET['sort']) && isset($_GET['order'])){
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc'))){
                $sort  = 'id';
                $order = 'desc';
            }
        }else{
            $sort  = 'id';
            $order = 'desc';
        }
        
        $page = $this->_get_page(30);
        $list = $this->_mod_craft_parent->find(array(
                'conditions' => "1 = 1 ".$conditions,
                'count' => true,
                'order' => "$sort $order",
                'limit' => $page['limit'],
        ));
        
        $page['item_count'] = $this->_mod_craft_parent->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('list', $list);
        $this->assign('cloth',$this->_cloth);
        $this->display('craft/cloth.html');
        
    }
    
    
    
    
    //异步修改状态
    function ajax_col()
    {
        $id     = empty($_GET['id']) ? 0 : intval($_GET['id']);
        $column = empty($_GET['column']) ? '' : trim($_GET['column']);
        $value  = isset($_GET['value']) ? trim($_GET['value']) : '';
        $data   = array();
    
        if (in_array($column ,array('is_active')))
        {
            $data[$column] = $value;
            $this->_mod_craft_parent->edit($id, $data);
            if(!$this->_mod_craft_parent->has_error())
            {
                echo ecm_json_encode(true);
            }
        }
        else
        {
            return ;
        }
        return ;
    }
    
    
}