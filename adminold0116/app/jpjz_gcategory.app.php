<?php

define('MAX_LAYER', 4);

/* 商品分类控制器 */
class Jpjz_gcategoryApp extends BackendApp
{
    var $jpjz_gcategory_mod;

    /**
     * 构造函数
     */
    function __construct()
    {
        $this->GcategoryApp();
    }
    function GcategoryApp()
    {
        parent::__construct();

        $this->jpjz_gcategory_mod =& bm('jpjz_gcategory', array('_store_id' => 0));

    }

    /* 管理 */
    function index()
    {
        /* 取得商品分类 */
        $gcategories = $this->jpjz_gcategory_mod->get_list(0);
//        echo '<pre>';var_dump($gcategories);die;
        $tree =& $this->_tree($gcategories);

        /* 先根排序 */
        foreach ($gcategories as $key => $val)
        {
            $gcategories[$key]['switchs'] = 0;
            if ($this->jpjz_gcategory_mod->get_list($val['cate_id']))
            {
               $gcategories[$key]['switchs'] = 1;
            }
        }
        $this->assign('gcategories', $gcategories);

        /* 构造映射表（每个结点的父结点对应的行，从1开始） */

        $this->assign('max_layer', MAX_LAYER);

        /* 导入jQuery的表单验证插件 */
        $this->import_resource(array(
            'script' => 'jqtable.js,inline_edit.js',
            'style'  => 'res:style/jqtreetable.css'
        ));
//echo '<pre>';var_dump();die;
        $this->display('jpjz_gcategory.index.html');
    }

    /* 异步去商品分类子元素 */
    function ajax_cate()
    {
        if(!isset($_GET['id']) || empty($_GET['id']))
        {
            echo ecm_json_encode(false);
            return;
        }
        $this->jpjz_gcategory_mod =& bm('jpjz_gcategory');
        $cate = $this->jpjz_gcategory_mod->get_list($_GET['id']);
        foreach ($cate as $key => $val)
        {
            $child = $this->jpjz_gcategory_mod->get_list($val['cate_id']);
            $lay = $this->jpjz_gcategory_mod->get_layer($val['cate_id']);
            if ($lay >= MAX_LAYER)
            {
                $cate[$key]['add_child'] = 0;
            }
            else 
            {
                $cate[$key]['add_child'] = 1;
            }
            if (!$child || empty($child) )
            {
                
                $cate[$key]['switchs'] = 0;
            }
            else
            {
                $cate[$key]['switchs'] = 1;
            }
        }
        header("Content-Type:text/html;charset=" . CHARSET);
        echo ecm_json_encode(array_values($cate));
        return ;
    }

    /* 新增 */
    function add()
    {
        if (!IS_POST)
        {
            /* 参数 */
            $pid = empty($_GET['pid']) ? 0 : intval($_GET['pid']);
            $gcategory = array('parent_id' => $pid, 'sort_order' => 255, 'if_show' => 1);
            $this->assign('gcategory', $gcategory);

            $this->assign('parents', $this->_get_options());
            $this->assign('parents2', $this->_get_options());
//            $this->assign('themes', $this->_get_themes());
            
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            
            $this->assign("type_list", $this->_type_list());
            
            $this->display('jpjz_gcategory.form.html');
        }
        else
        {
//            $attrids = isset($_POST['attrid']) ? $_POST['attrid'] : array();

            $data = array(
                'cate_name'  => $_POST['cate_name'],
                'alias'  => $_POST['alias'],
                'parent_id'  => $_POST['parent_id'],
                'sort_order' => $_POST['sort_order'],
                'if_show'    => $_POST['if_show'],
                'intro'      => $_POST['intro'],
//                'theme'      => $_POST['theme'],
//                'attrid'     => implode(",", $attrids),
//                'keywords'   => $_POST['keywords'],
//                'description' => $_POST["description"],
//                'goodsids'   => isset($_POST['ids']) ? implode(",", $_POST['ids']) : '',
            );


            /* 检查名称是否已存在 */
            if (!$this->jpjz_gcategory_mod->unique(trim($data['cate_name']), $data['parent_id']))
            {
                $this->show_warning('name_exist');
                return;
            }

            /* 检查级数 */
            $ancestor = $this->jpjz_gcategory_mod->get_ancestor($data['parent_id']);
            if (count($ancestor) >= MAX_LAYER)
            {
                $this->show_warning('max_layer_error');
                return;
            }

            /* 保存 */
            $cate_id = $this->jpjz_gcategory_mod->add($data);
            if (!$cate_id)
            {
                $this->show_warning($this->jpjz_gcategory_mod->get_error());
                return;
            }

            $this->show_message('add_ok',
                'back_list',    'index.php?app=jpjz_gcategory',
                'continue_add', 'index.php?app=jpjz_gcategory&amp;act=add&amp;pid=' . $data['parent_id']
            );
        }
    }

    /* 检查分类名的唯一*/
    function check_gcategory ()
    {
        $cate_name = empty($_GET['cate_name']) ? '' : trim($_GET['cate_name']);
        $parent_id = empty($_GET['parent_id']) ? 0  : intval($_GET['parent_id']);
        $cate_id   = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$cate_name)
        {
            echo ecm_json_encode(true);
            return ;
        }
        if ($this->jpjz_gcategory_mod->unique($cate_name, $parent_id, $cate_id))
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
            $gcategory = $this->jpjz_gcategory_mod->get_info($id);
            if (!$gcategory)
            {
                $this->show_warning('gcategory_empty');
                return;
            }
            $this->assign('gcategory', $gcategory);
            
//            $this->assign('themes', $this->_get_themes());
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            
            $attrArray = array();
            if($gcategory['attrid'])
            {
                $attrModel = &m("attribute");
                $attrArray = $attrModel->find(array(
                    'conditions' => "attr_id IN ({$gcategory['attrid']})"
                ));
            }
            
            $typeid = '';
            foreach($attrArray as $key => $val)
            {
                $typeid .= $typeid ? ",".$val['type_id'] : $val['type_id'];
            }
            
            $typeAttrArray = array();
            if($typeid)
            {
                $list = $attrModel->find(array(
                    'conditions' => "type_id IN ({$typeid})"
                ));
                
                foreach($list as $key => $val)
                {
                    $typeAttrArray[$val["type_id"]][$val['attr_id']]['attr_id'] = $val['attr_id'];
                    $typeAttrArray[$val["type_id"]][$val['attr_id']]['attr_name'] = $val['attr_name'];
                }
                
                
                foreach($attrArray as $key => $val)
                {
                    if(isset($typeAttrArray[$val["type_id"]]))
                    {
                        $attrArray[$key]['child'] = $typeAttrArray[$val["type_id"]];
                    }
                }
            }
            
            $this->assign("attr_list", $attrArray);
            $this->assign("type_list", $this->_type_list());
            $this->assign('parents', $this->_get_options($id));
            $this->assign('parents2', $this->_get_options());
            
            $goodsModel = &m("goods");
            $goods_list = array();
            if(!empty($gcategory['goodsids'])){
                $goods_list = $goodsModel->find(array(
                    'conditions' => "goods_id IN ({$gcategory['goodsids']})",
                    'fields' => "goods_id, goods_name",
                ));
            }
            
            $this->assign("goods_list", $goods_list);
            
            $this->display('jpjz_gcategory.form.html');
        }
        else
        {
            $attrids = isset($_POST['attrid']) ? $_POST['attrid'] : array();
            $data = array(
                'cate_name'  => $_POST['cate_name'],
                'alias'  => $_POST['alias'],
                'parent_id'  => $_POST['parent_id'],
                'sort_order' => $_POST['sort_order'],
                'if_show'    => $_POST['if_show'],
                'intro'      => $_POST['intro'],
                //'theme'      => $_POST['theme'],
                //'attrid'     => implode(",", $attrids),
                //'keywords'   => $_POST['keywords'],
                //'description' => $_POST["description"],
                //'goodsids'   => isset($_POST['ids']) ? implode(",", $_POST['ids']) : '',
            );

            /* 检查名称是否已存在 */
            if (!$this->jpjz_gcategory_mod->unique(trim($data['cate_name']), $data['parent_id'], $id))
            {
                $this->show_warning('name_exist');
                return;
            }

            /* 检查级数 */
            $depth    = $this->jpjz_gcategory_mod->get_depth($id);
            $ancestor = $this->jpjz_gcategory_mod->get_ancestor($data['parent_id']);
            if ($depth + count($ancestor) > MAX_LAYER)
            {
                $this->show_warning('max_layer_error');
                return;
            }

            /* 保存 */
            $old_data = $this->jpjz_gcategory_mod->get_info($id); // 保存前的数据
            $rows = $this->jpjz_gcategory_mod->edit($id, $data);
            if ($this->jpjz_gcategory_mod->has_error())
            {
                $this->show_warning($this->jpjz_gcategory_mod->get_error());
                return;
            }

            /* 如果改变了上级分类，更新商品表中相应记录的cate_id_1到cate_id_4 */
            if ($old_data['parent_id'] != $data['parent_id'])
            {
                // 执行时间可能比较长，所以不限制了
                _at(set_time_limit, 0);

                // 清除商城商品分类缓存
                $cache_server =& cache_server();
                $cache_server->delete('goods_category_0');

                // 取得当前分类的所有子孙分类（包括自身）
                $cids = $this->jpjz_gcategory_mod->get_descendant_ids($id, true);

                // 找出这些分类中有商品的分类
                $mod_goods =& m('goods');
                $mod_gcate =& $this->jpjz_gcategory_mod;
                $sql = "SELECT DISTINCT cate_id FROM {$mod_goods->table} WHERE cate_id " . db_create_in($cids);
                $cate_ids = $mod_goods->getCol($sql);

                // 循环更新每个分类的cate_id_1到cate_id_4
                foreach ($cate_ids as $cate_id)
                {
                    $cate_id_n = array(0,0,0,0);
                    $ancestor = $mod_gcate->get_ancestor($cate_id, true);
                    for ($i = 0; $i < 4; $i++)
                    {
                        isset($ancestor[$i]) && $cate_id_n[$i] = $ancestor[$i]['cate_id'];
                    }
                    $sql = "UPDATE {$mod_goods->table} " .
                            "SET cate_id_1 = '{$cate_id_n[0]}', cate_id_2 = '{$cate_id_n[1]}', cate_id_3 = '{$cate_id_n[2]}', cate_id_4 = '{$cate_id_n[3]}' " .
                            "WHERE cate_id = '$cate_id'";
                    $mod_goods->db->query($sql);
                }
            }
            
            $this->show_message('edit_ok',
                'back_list',    'index.php?app=jpjz_gcategory',
                'edit_again',   'index.php?app=jpjz_gcategory&amp;act=edit&amp;id=' . $id
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
       if (in_array($column ,array('cate_name', 'if_show', 'sort_order')))
       {
           $data[$column] = $value;
           if($column == 'cate_name')
           {
               $gcategory = $this->jpjz_gcategory_mod->get_info($id);
               if (!$this->jpjz_gcategory_mod->unique($value, $gcategory['parent_id'], $id))
               {
                   echo ecm_json_encode(false);
                   return ;
               }
           }
           $this->jpjz_gcategory_mod->edit($id, $data);
           if(!$this->jpjz_gcategory_mod->has_error())
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

    /* 批量编辑 */
    function batch_edit()
    {
        if (!IS_POST)
        {
            $this->display('jpjz_gcategory.batch.html');
        }
        else
        {
            $id = isset($_POST['id']) ? trim($_POST['id']) : '';
            if (!$id)
            {
                $this->show_warning('Hacking Attempt');
                return;
            }

            $ids = explode(',', $id);
            if ($_POST['if_show'] >= 0)
            {
                $data = array('if_show' => $_POST['if_show'] ? 1 : 0);
                $this->jpjz_gcategory_mod->edit($ids, $data);
            }

            $this->show_message('batch_edit_ok',
                'back_list', 'index.php?app=jpjz_gcategory');
        }
    }

    /* 删除 */
    function drop()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$id)
        {
            $this->show_warning('no_gcategory_to_drop');
            return;
        }

        $ids = explode(',', $id);
        if (!$this->jpjz_gcategory_mod->drop($ids))
        {
            $this->show_warning($this->jpjz_gcategory_mod->get_error());
            return;
        }

        $this->show_message('drop_ok');
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
            $this->jpjz_gcategory_mod->edit($id, array('sort_order' => $sort_orders[$key]));
        }

        $this->show_message('update_order_ok');
    }

    /* 导出数据 */
    function export()
    {
        // 目标编码
        $to_charset = (CHARSET == 'utf-8') ? substr(LANG, 0, 2) == 'sc' ? 'gbk' : 'big5' : '';

        if (!IS_POST)
        {
            if (CHARSET == 'utf-8')
            {
                $this->assign('note_for_export', sprintf(LANG::get('note_for_export'), $to_charset));
                $this->display('common.export.html');

                return;
            }
        }
        else
        {
            if (!$_POST['if_convert'])
            {
                $to_charset = '';
            }
        }

        $gcategories = $this->jpjz_gcategory_mod->get_list();
        $tree =& $this->_tree($gcategories);
        $csv_data = $tree->getCSVData(0, array('sort_order', 'if_show'));
        $this->export_to_csv($csv_data, 'jpjz_gcategory', $to_charset);
    }

    /* 导入数据 */
    function import()
    {
        if (!IS_POST)
        {
            $this->assign('note_for_import', sprintf(LANG::get('note_for_import'), CHARSET));
            $this->display('common.import.html');
        }
        else
        {
            $file = $_FILES['csv'];
            if ($file['error'] != UPLOAD_ERR_OK)
            {
                $this->show_warning('select_file');
                return;
            }
            if ($file['name'] == basename($file['name'],'.csv'))
            {
                $this->show_warning('not_csv_file');
                return;
            }

            $data = $this->import_from_csv($file['tmp_name'], false, $_POST['charset'], CHARSET);
            $parents = array(0 => 0); // 存放layer的parent的数组
            $fileds = array_intersect($data[0],array('cate_name', 'sort_order', 'if_show')); //第一行含有的字段
            $start_col = intval(array_search('cate_name', $fileds)); //主数据区开始列号
            foreach ($data as $row)
            {
                $layer = -1;
                if(array_intersect($row,array('cate_name', 'sort_order', 'if_show')))
                {
                    continue;
                }
                $if_show_col = array_search('if_show', $fileds); //从表头得到if_show的列号
                $if_show = is_numeric($if_show_col) && isset($row[$if_show_col]) ? $row[$if_show_col] : 1;
                $sort_order_col = array_search('sort_order', $fileds); //从表头得到sort_order的列号
                $sort_order = is_numeric($sort_order_col) && isset($row[$sort_order_col]) ? $row[$sort_order_col] : 255;
                for ($i = $start_col; $i < count($row); $i++)
                {
                    if (trim($row[$i]))
                    {
                        $layer = $i - $start_col;
                        $cate_name  = trim($row[$i]);
                        break;
                    }
                }
                // 没数据
                if ($layer < 0)
                {
                    continue;
                }
                // 只处理有上级的
                if (isset($parents[$layer]))
                {
                    $gcategory = $this->jpjz_gcategory_mod->get("cate_name = '$cate_name' AND parent_id = '$parents[$layer]'");
                    if (!$gcategory)
                    {
                        // 不存在
                        $id = $this->jpjz_gcategory_mod->add(array(
                            'cate_name'     => $cate_name,
                            'parent_id'     => $parents[$layer],
                            'sort_order'    => $sort_order,
                            'if_show'       => $if_show,
                        ));
                        $parents[$layer + 1] = $id;
                    }
                    else
                    {
                        // 已存在
                        $parents[$layer + 1] = $gcategory['cate_id'];
                    }
                }
            }

            $this->show_message('import_ok',
                'back_list', 'index.php?app=jpjz_gcategory');
        }
    }

    /* 构造并返回树 */
    function &_tree($gcategories)
    {
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($gcategories, 'cate_id', 'parent_id', 'cate_name');
        return $tree;
    }

    /* 取得可以作为上级的商品分类数据 */
    function _get_options($except = NULL)
    {
        $gcategories = $this->jpjz_gcategory_mod->get_list();
        $tree =& $this->_tree($gcategories);

        return $tree->getOptions(MAX_LAYER - 1, 0, $except);
    }
    
    /**
    * 模板列表
    * @return array
    * @access public
    * @see _get_themes
    * @version 1.0.0 (2014-11-19)
    * @author yhao.bai
    */
    function _get_themes()
    {
        return array(
            'man_index' => '男装频道首页',
            'man_zhengz' => "男装正装",
            'man_xiux'   => "男装休闲",
            'man_lif'    => "男装礼服",
            'man_tanxml' => "男装弹性面料"
        );
    }
    
    /**
    * 描述
    * @return array
    * @access public
    * @see _type_list
    * @version 1.0.0 (2014-11-19)
    * @author yhao.bai
    */
    function _type_list()
    {
        $typeModel = &m("goods_type");
        $list = $typeModel->find(array(
                   'conditions' => "enabled=1"
                ));
        $typeArray = array();

        foreach($list as $key => $val)
        {
            $typeArray[$val['type_id']] = $val["type_name"];
        }
        
        return $typeArray;
    }
    
    /**
    * 属性加载
    * @return json
    * @access public
    * @see loadAttr
    * @version 1.0.0 (2014-11-24)
    * @author yhao.bai
    */
    function loadAttr()
    {
        $type = isset($_GET['type']) ? intval($_GET['type']) : 0;
        
        $attrModel = &m("attribute");
        $list = $attrModel->find(array(
            'conditions' => "type_id='{$type}'"
        ));
        
        $newArray = array();
        foreach($list as $key => $val){
            $newArray[] = array(
                'id'   => $val['attr_id'],
                'name' => $val["attr_name"]
            );
        }
        $this->json_result($newArray); //返回商品属性
    }
    
    /**
    * 搜索商品
    * @return json
    * @access public
    * @see loadGoods
    * @version 1.0.0 (2014-11-24)
    * @author yhao.bai
    */
    function loadGoods()
    {
        $catid = isset($_GET['catid']) ? intval($_GET['catid']) : 0;
        $name = isset($_GET['name']) ? trim($_GET['name']) : 0;
        $goodsModel = &m("goods");
        $conditions = " 1 ";
        if($catid > 0)
        {
            $conditions .= " AND cate_id = '{$catid}'";
        }
        
        if($name)
        {
            $conditions .= " AND goods_name = '%{$name}%'";
        }
        
        $list = $goodsModel->find(array(
            'conditions' => $conditions,
            'limit' => 20,
        ));
        
        $newArray = array();
        foreach($list as $key => $val){
            $newArray[] = array(
                'id'   => $val['goods_id'],
                'name' => $val["goods_name"]
            );
        }
        $this->json_result($newArray); 
    }
}

?>