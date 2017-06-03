<?php

define('MAX_LAYER', 2);

/* 文章分类控制器 */
class PcategoryApp extends BackendApp
{
    var $_pcategory_mod;

    function __construct()
    {
        $this->PcategoryApp();
    }

    function PcategoryApp()
    {
        parent::BackendApp();

        $this->_pcategory_mod =& m('pcategory');
    }

    /* 管理 */
    function index()
    {
        /* 取得文章分类 */
        $acategories = $this->_pcategory_mod->get_list();
        $tree =& $this->_tree($acategories);

        /* 先根排序 */
        $sorted_acategories = array();
        $cate_ids = $tree->getChilds();
        foreach ($cate_ids as $id)
        {
            $parent_children_valid = $this->_pcategory_mod->parent_children_valid($id);
            $sorted_acategories[] = array_merge($acategories[$id], array('layer' => $tree->getLayer($id), 'parent_children_valid'=>$parent_children_valid));
        }
        $this->assign('acategories', $sorted_acategories);

        /* 构造映射表（每个结点的父结点对应的行，从1开始） */
        $row = array(0 => 0);   // cate_id对应的row
        $map = array();         // parent_id对应的row
        foreach ($sorted_acategories as $key => $pcategory)
        {
            $row[$pcategory['cate_id']] = $key + 1;
            $map[] = $row[$pcategory['parent_id']];
        }
        $this->assign('map', ecm_json_encode($map));

        $this->assign('max_layer', MAX_LAYER);

        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css')
        );
        $this->display('process/pcategory.index.html');
    }

    /* 新增 */
    function add()
    {
        if (!IS_POST)
        {
            /* 参数 */
            $pid = empty($_GET['pid']) ? 0 : intval($_GET['pid']);
            $pcategory = array('parent_id' => $pid, 'sort_order' => 255);
            $this->assign('pcategory', $pcategory);

            /* 如果当前分类是不能有上下级的分类，则不可添加下级分类 */
            if(!$this->_pcategory_mod->parent_children_valid($pid))
            {
                $this->show_warning('cannot_add_children');
                return;
            }
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->assign('parents', $this->_get_options());
            $this->display('process/pcategory.form.html');
        }
        else
        {
            $data = array(
                'cate_name'  => $_POST['cate_name'],
                'parent_id'  => $_POST['parent_id'],
                'sort_order' => $_POST['sort_order'],
            );

            /* 检查名称是否已存在 */
            if (!$this->_pcategory_mod->unique(trim($data['cate_name']), $data['parent_id']))
            {
                $this->show_warning('name_exist');
                return;
            }

            /* 选择的上级分类不允许有下级分类 */
            if(!$this->_pcategory_mod->parent_children_valid($data['parent_id']))
            {
                $this->show_warning('cannot_be_parent');
                return;
            }

            /* 保存 */
            $cate_id = $this->_pcategory_mod->add($data);
            if (!$cate_id)
            {
                $this->show_warning($this->_pcategory_mod->get_error());
                return;
            }

            $this->show_message('add_ok',
                'back_list',    'index.php?app=pcategory',
                'continue_add', 'index.php?app=pcategory&amp;act=add&amp;pid=' . $data['parent_id']
            );
        }
    }

    /* 检查文章分类的唯一性 */
    function check_pcategory()
    {
        $cate_name = empty($_GET['cate_name']) ? '' : trim($_GET['cate_name']);
        $parent_id = empty($_GET['parent_id']) ? 0 : intval($_GET['parent_id']);
        $cate_id   = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!$cate_name)
        {
            echo ecm_json_encode(true);
            return ;
        }
        if ($this->_pcategory_mod->unique($cate_name, $parent_id, $cate_id))
        {
            echo ecm_json_encode(true);
        }
        else
        {
            echo ecm_json_encode(false);
        }
        return ;
    }

    /* 编辑 */
    function edit()
    {
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!IS_POST)
        {
            /* 是否存在 */
            $pcategory = $this->_pcategory_mod->get_info($id);
            if (!$pcategory)
            {
                $this->show_warning('pcategory_empty');
                return;
            }
            /* 如果当前分类是系统分类，则不可编辑 */
            if($pcategory['code'])
            {
                $this->show_warning('cannot_edit_system_pcategory');
                return;
            }
            $this->assign('pcategory', $pcategory);
            if ($this->_pcategory_mod->parent_children_valid($id))
            {
                $this->assign('parents', $this->_get_options($id));
            }
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->display('process/pcategory.form.html');
        }
        else
        {
            $data = array(
                'cate_name'  => $_POST['cate_name'],
                'parent_id'  => $_POST['parent_id'],
                'sort_order' => $_POST['sort_order'],
            );

            /* 选择的上级分类不允许有下级分类 */
            if(!$this->_pcategory_mod->parent_children_valid($data['parent_id']))
            {
                $this->show_warning('cannot_be_parent');
                return;
            }
            /* 检查名称是否已存在 */
            if (!$this->_pcategory_mod->unique(trim($data['cate_name']), $data['parent_id'], $id))
            {
                $this->show_warning('name_exist');
                return;
            }

            /* 保存 */
            $rows = $this->_pcategory_mod->edit($id, $data);
            if ($this->_pcategory_mod->has_error())
            {
                $this->show_warning($this->_pcategory_mod->get_error());
                return;
            }

            $this->show_message('edit_ok',
                'back_list',    'index.php?app=pcategory',
                'edit_again',   'index.php?app=pcategory&amp;act=edit&amp;id=' . $id
            );
        }
    }

             //异步修改数据
   function ajax_col()
   {
       $id     = empty($_GET['id']) ? 0 : intval($_GET['id']);
       $column = empty($_GET['column']) ? '' : trim($_GET['column']);
       $value  = isset($_GET['value']) ? trim($_GET['value']) : '';
       $data   = array();

       if (in_array($column ,array('cate_name', 'sort_order')))
       {
           $data[$column] = $value;
           if($column == 'cate_name')
           {
               $pcategory = $this->_pcategory_mod->get_info($id);

               if(!$this->_pcategory_mod->unique($value, $pcategory['parent_id'], $id))
               {
                   echo ecm_json_encode(false);
                   return ;
               }
           }
           $this->_pcategory_mod->edit($id, $data);
           if(!$this->_pcategory_mod->has_error())
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

    /* 删除 */
    function drop()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('no_pcategory_to_drop');
            return;
        }

        $ids = explode(',', $id);
        $message = 'drop_ok';
        foreach ($ids as $key=>$id){
            $pcategory=$this->_pcategory_mod->find(intval($id));
            $pcategory=current($pcategory);
            if($pcategory['code']!=null)
            {
                unset($ids[$key]);  //有部分是系统分类 过滤掉
                $message = 'drop_ok_system_pcategory';
            }
        }
        if (!$ids)
        {
            $message = 'system_pcategory'; //全是系统分类
            $this->show_warning($message);

            return;
        }

        if (!$this->_pcategory_mod->drop($ids))
        {
            $this->show_warning($this->_pcategory_mod->get_error());
            return;
        }

        $this->show_message($message);
    }

    /* 更新排序 */
    function update_order()
    {
        if (empty($_GET['id']))
        {
            $this->show_warning('Hacking Attempt');
            return;
        }

        $ids = explode(',', $_GET['id']);
        $sort_orders = explode(',', $_GET['sort_order']);
        foreach ($ids as $key => $id)
        {
            $this->_pcategory_mod->edit($id, array('sort_order' => $sort_orders[$key]));
        }

        $this->show_message('update_order_ok');
    }

    /* 构造并返回树 */
    function &_tree($acategories)
    {
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($acategories, 'cate_id', 'parent_id', 'cate_name');
        return $tree;
    }

    /* 取得可以作为上级的文章分类数据 */
    function _get_options($except = NULL)
    {
        $acategories = $this->_pcategory_mod->get_list();

        /* 过滤掉不能作为上级的分类 */
        foreach ($acategories as $key => $acategorie)
        {
            if (!$this->_pcategory_mod->parent_children_valid($acategorie['cate_id']))
            {
                unset($acategories[$key]);
            }
        }

        $tree =& $this->_tree($acategories);
        return $tree->getOptions(MAX_LAYER - 1, 0, $except);
    }
}

?>