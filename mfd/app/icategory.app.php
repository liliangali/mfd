<?php

define('MAX_LAYER', 2);

/* 消息模版分类控制器 */
class IcategoryApp extends BackendApp
{
    var $_icategory_mod;
    var $_messagetemplate_mod;

    function __construct()
    {
        $this->IcategoryApp();
    }

    function IcategoryApp()
    {
        parent::BackendApp();
		$this->_messagetemplate_mod=&m('messagetemplate');
        $this->_icategory_mod =& m('icategory');
    }

    /* 管理 */
    function index()
    {
    	
        /* 取得消息模版分类分类 */
        $acategories = $this->_icategory_mod->get_list();
        $tree =& $this->_tree($acategories);
		
        /* 先根排序 */
        $sorted_acategories = array();
        $cate_ids = $tree->getChilds();
        foreach ($cate_ids as $id)
        {
            $parent_children_valid = $this->_icategory_mod->parent_children_valid($id);
            $sorted_acategories[] = array_merge($acategories[$id], array('layer' => $tree->getLayer($id), 'parent_children_valid'=>$parent_children_valid));
        }
        $this->assign('acategories', $sorted_acategories);

        /* 构造映射表（每个结点的父结点对应的行，从1开始） */
        $row = array(0 => 0);   // cate_id对应的row
        $map = array();         // parent_id对应的row
        foreach ($sorted_acategories as $key => $icategory)
        {
            $row[$icategory['cate_id']] = $key + 1;
            $map[] = $row[$icategory['parent_id']];
        }
        $this->assign('map', ecm_json_encode($map));

        $this->assign('max_layer', MAX_LAYER);

        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css')
        );
        $this->display('message_template/icategory.index.html');
    }

    /* 新增 */
    function add()
    {
        if (!IS_POST)
        {
            /* 参数 */
            $pid = empty($_GET['pid']) ? 0 : intval($_GET['pid']);
            $icategory = array('parent_id' => $pid);
            $this->assign('icategory', $icategory);

            /* 如果当前分类是不能有上下级的分类，则不可添加下级分类 */
            if(!$this->_icategory_mod->parent_children_valid($pid))
            {
                $this->show_warning('cannot_add_children');
                return;
            }
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->assign('parents', $this->_get_options());
            $this->display('message_template/icategory.form.html');
        }
        else
        {
            $data = array(
                'cate_name'  => $_POST['cate_name'],
                'parent_id'  => $_POST['parent_id'],
// 				'type' =>$_POST['type']
            );
			if (empty($data['cate_name'])){
				$this->show_warning('分类名称不能为空');
				return;
			}
            /* 检查名称是否已存在 */
            if (!$this->_icategory_mod->unique(trim($data['cate_name']), $data['parent_id']))
            {
                $this->show_warning('name_exist');
                return;
            }

//             /* 检查分类类型是否已存在 */
//             if (!$this->_icategory_mod->unique_type(trim($data['type']), $data['parent_id']))
//             {
//             	$this->show_warning('name_exist');
//             	return;
//             }
            
            /* 选择的上级分类不允许有下级分类 */
            if(!$this->_icategory_mod->parent_children_valid($data['parent_id']))
            {
                $this->show_warning('cannot_be_parent');
                return;
            }

            /* 保存 */
            $cate_id = $this->_icategory_mod->add($data);
            if (!$cate_id)
            {
                $this->show_warning($this->_icategory_mod->get_error());
                return;
            }

            $this->show_message('add_ok',
                'back_list',    'index.php?app=icategory',
                'continue_add', 'index.php?app=icategory&amp;act=add&amp;pid=' . $data['parent_id']
            );
        }
    }

    /* 检查消息模版分类分类的唯一性 */
    function check_icategory()
    {
        $cate_name = empty($_GET['cate_name']) ? '' : trim($_GET['cate_name']);
        $parent_id = empty($_GET['parent_id']) ? 0 : intval($_GET['parent_id']);
        $cate_id   = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!$cate_name)
        {
            echo ecm_json_encode(true);
            return ;
        }
        if ($this->_icategory_mod->unique($cate_name, $parent_id, $cate_id))
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
            $icategory = $this->_icategory_mod->get_info($id);
            if (!$icategory)
            {
                $this->show_warning('icategory_empty');
                return;
            }
            /* 如果当前分类是系统分类，则不可编辑 */
            if($icategory['code'])
            {
                $this->show_warning('cannot_edit_system_icategory');
                return;
            }
            $this->assign('icategory', $icategory);
            if ($this->_icategory_mod->parent_children_valid($id))
            {
                $this->assign('parents', $this->_get_options($id));
            }
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->display('message_template/icategory.form.html');
        }
        else
        {
            $data = array(
                'cate_name'  => $_POST['cate_name'],
                'parent_id'  => $_POST['parent_id'],
//                 'type' => $_POST['type'],
            );
            if (empty($data['cate_name'])){
            	$this->show_warning('分类名称不能为空');
            	return;
            }
            /* 选择的上级分类不允许有下级分类 */
            if(!$this->_icategory_mod->parent_children_valid($data['parent_id']))
            {
                $this->show_warning('cannot_be_parent');
                return;
            }
            /* 检查名称是否已存在 */
            if (!$this->_icategory_mod->unique(trim($data['cate_name']), $data['parent_id'], $id))
            {
                $this->show_warning('name_exist');
                return;
            }

            /* 保存 */
            $rows = $this->_icategory_mod->edit($id, $data);
            if ($this->_icategory_mod->has_error())
            {
                $this->show_warning($this->_icategory_mod->get_error());
                return;
            }

            $this->show_message('edit_ok',
                'back_list',    'index.php?app=icategory',
                'edit_again',   'index.php?app=icategory&amp;act=edit&amp;id=' . $id
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

       if (in_array($column ,array('cate_name', 'type')))
       {
           $data[$column] = $value;
           if($column == 'cate_name')
           {
               $icategory = $this->_icategory_mod->get_info($id);

               if(!$this->_icategory_mod->unique($icategory['parent_id'], $id))
               {
                   echo ecm_json_encode(false);
                   return ;
               }
           }
           if($column =='type'){
           		$icategory=$this->_icategory_mod->get_info($id);
           		if(!$this->_icategory_mod->unique_type($icategory['parent_id'],$id))
           		{
           			echo ecm_json_encode(false);
           			return ;
           		}
           }
           $this->_icategory_mod->edit($id, $data);
           if(!$this->_icategory_mod->has_error())
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
            $this->show_warning('no_icategory_to_drop');
            return;
        }

        $ids = explode(',', $id);
        $message = 'ica_drop_ok';
        foreach ($ids as $key=>$id){
        	$id=intval($id);

            $mt_ids=$this->_icategory_mod->find(array(
            		'conditions'=>"cate_id={$id}",
             		'fields'=>'mt.mt_id,ica.*',
             		'join'    => 'has_messages',
            ));

            $sons=$this->_icategory_mod->find(array(
            		'conditions'=>"parent_id={$id}",
            		'fields'=>'cate_id',
            ));

            if($mt_ids){
            	foreach ($mt_ids as $k=>$v){
            		unset($v['mt_id']);
            		$icategory=$v;
            		break;
            	}
            	$mt_all_id=array();
            	foreach ($mt_ids as $k=>$v){
            		$mt_all_id[]=$v['mt_id'];
            	}
            }
            //$icategory=$this->_icategory_mod->find($id);
            //$icategory=current($icategory);
            if($icategory['code']!=null)
            {
                unset($ids[$key]);  //有部分是系统分类 过滤掉
                $message = 'drop_ok_system_icategory';
                continue;
            }
            $this->_messagetemplate_mod->drop($mt_all_id);
            $this->_icategory_mod->drop($sons);
        }
        if (!$ids)
        {
            $message = 'system_icategory'; //全是系统分类
            $this->show_warning($message);

            return;
        }

        if (!$this->_icategory_mod->drop($ids))
        {
            $this->show_warning($this->_icategory_mod->get_error());
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
            $this->_icategory_mod->edit($id, array('sort_order' => $sort_orders[$key]));
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

    /* 取得可以作为上级的消息模版分类数据 */
    function _get_options($except = NULL)
    {
        $acategories = $this->_icategory_mod->get_list();

        /* 过滤掉不能作为上级的分类 */
        foreach ($acategories as $key => $acategorie)
        {
            if (!$this->_icategory_mod->parent_children_valid($acategorie['cate_id']))
            {
                unset($acategories[$key]);
            }
        }

        $tree =& $this->_tree($acategories);
        return $tree->getOptions(MAX_LAYER - 1, 0, $except);
    }
    
}

?>