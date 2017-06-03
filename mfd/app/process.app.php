<?php

/**
 *    文章管理控制器
 *
 *    @author    Hyber
 *    @usage    none
 */
class ProcessApp extends BackendApp
{
    var $_process_mod;
    var $_uploadedfile_mod;

    function __construct()
    {
        $this->ProcessApp();
    }

    function ProcessApp()
    {
        parent::BackendApp();

        $this->_process_mod =& m('process');
        $this->_uploadedfile_mod = &m('uploadedfile');
    }

    /**
     *    文章索引
     *
     *    @author    Hyber
     *    @return    void
     */
    function index()
    {
        /* 处理cate_id */
        $cate_id = !empty($_GET['cate_id'])? intval($_GET['cate_id']) : 0;
        if ($cate_id > 0) //取得该分类及子分类cate_id
        {
            $pcategory_mod = & m('pcategory');
            $cate_ids = $pcategory_mod->get_descendant($cate_id);
            if (!$cate_ids)
            {
                $this->show_warning('no_this_pcategory');
                return;
            }
        }
        $conditions='';
        !empty($cate_ids)&& $conditions = ' AND process.cate_id ' . db_create_in($cate_ids);
        $conditions .= $this->_get_query_conditions(array(
            array(
                'field' => 'title',         //可搜索字段title
                'equal' => 'LIKE',          //等价关系,可以是LIKE, =, <, >, <>
                'assoc' => 'AND',           //关系类型,可以是AND, OR
                'name'  => 'title',         //GET的值的访问键名
                'type'  => 'string',        //GET的值的类型
            ),
        ));
        $page   =   $this->_get_page(30);   //获取分页信息
        $processs=$this->_process_mod->find(array(
        'fields'   => 'process.*,pcategory.cate_name',
        'conditions'  => 'store_id=0' . $conditions,
        'limit'   => $page['limit'],
        'join'    => 'belongs_to_pcategory',
        'order'   => 'process.sort_order ASC,process.add_time DESC', //必须加别名
        'count'   => true   //允许统计
        ));    //找出所有的文章
        $page['item_count']=$this->_process_mod->getCount();   //获取统计数据
        $if_show = array(
            0 => Lang::get('no'),
            1 => Lang::get('yes'),
        );
        foreach ($processs as $key =>$process){
            $processs[$key]['if_show']  = $if_show[$process['if_show']]; //是否显示
        }
        $this->_format_page($page);
        $this->import_resource(array('script' => 'inline_edit_admin.js'));
        $this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
        $this->assign('parents', $this->_get_options()); //分类树
        $this->assign('page_info', $page);   //将分页信息传递给视图，用于形成分页条
        $this->assign('processs', $processs);
     
        $this->display('process/process.index.html');
    }
     /**
     *    新增文章
     *
     *    @author    Hyber
     *    @return    void
     */
    function add()
    {
        if (!IS_POST)
        {
            /* 显示新增表单 */
            $cate_id = isset ($_GET['cate_id']) ? intval($_GET['cate_id']) : 0;//方便在某个分类下新增文章
            $process = array('cate_id' => $cate_id, 'sort_order' => 255, 'link' => '', 'if_show' => 1);

            /* 文章模型未分配的附件 */
            $files_BELONG_PROCESS = $this->_uploadedfile_mod->find(array(
                'conditions' => 'store_id = 0 AND belong = ' . BELONG_PROCESS . ' AND item_id = 0',
                'fields' => 'this.file_id, this.file_name, this.file_path',
                'order' => 'add_time DESC'
            ));

            $this->assign("id", 0);
            $this->assign('belong', BELONG_PROCESS);

            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
            $this->assign('process', $process);
            $this->assign('files_BELONG_PROCESS', $files_BELONG_PROCESS);
            $this->assign('parents', $this->_get_options()); //分类树
            
            $template_name = $this->_get_template_name();
            $style_name    = $this->_get_style_name();
            $this->assign('build_editor', $this->_build_editor(array(
                'name' => 'content',
                'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
            )));
            
            $this->assign('build_upload', $this->_build_upload(array('belong' => BELONG_PROCESS, 'item_id' => 0))); // 构建swfupload上传组件
            $this->display('process/process.form.html');
        }
        else
        {
            $data = array();
            $data['title']      =   $_POST['title'];
            $data['cate_id']    =   $_POST['cate_id'];
            $data['link']       =   $_POST['link'] == 'http://' ? '' : $_POST['link'];
            $data['if_show']    =   $_POST['if_show'];
            $data['sort_order'] =   $_POST['sort_order'];
            $data['content'] =   $_POST['content'];
            $data['add_time']   =   gmtime();

            if (!$process_id = $this->_process_mod->add($data))  //获取process_id
            {
                $this->show_warning($this->_process_mod->get_error());

                return;
            }

            /* 附件入库 */
            if (isset($_POST['file_id']))
            {
                foreach ($_POST['file_id'] as $file_id)
                {
                    $this->_uploadedfile_mod->edit($file_id, array('item_id' => $process_id));
                }
            }
            $this->show_message('add_process_successed',
                'back_list',    'index.php?app=process',
                'continue_add', 'index.php?app=process&amp;act=add'
            );
        }
    }
     /**
     *    编辑文章
     *
     *    @author    Hyber
     *    @return    void
     */
    function edit()
    {
        $process_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$process_id)
        {
            $this->show_warning('no_such_process');
            return;
        }
         if (!IS_POST)
        {
            /* 当前文章的附件 */
            $files_BELONG_PROCESS = $this->_uploadedfile_mod->find(array(
                'conditions' => 'store_id = 0 AND belong = ' . BELONG_PROCESS . ' AND item_id=' . $process_id,
                'fields' => 'this.file_id, this.file_name, this.file_path',
                'order' => 'add_time DESC'
            ));

            $find_data     = $this->_process_mod->find($process_id);
            if (empty($find_data))
            {
                $this->show_warning('no_such_process');

                return;
            }
            $process    =   current($find_data);
            $process['link'] = $process['link'] ? $process['link'] : '';
            $this->assign("id", $process_id);
            $this->assign("belong", BELONG_PROCESS);
            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
            $this->assign('parents', $this->_get_options());
            $this->assign('files_BELONG_PROCESS', $files_BELONG_PROCESS);
            $this->assign('process', $process);
            
            $template_name = $this->_get_template_name();
            $style_name    = $this->_get_style_name();
            $this->assign('build_editor', $this->_build_editor(array(
                'name' => 'content',
                'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
            )));
            
            $this->assign('build_upload', $this->_build_upload(array('belong' => BELONG_PROCESS, 'item_id' => $process_id))); // 构建swfupload上传组件
            $this->display('process/process.form.html');
        }
        else
        {
            $data = array();
            $data['title']          =   $_POST['title'];
            if (!empty($_POST['cate_id']))
            {
                $data['cate_id']        =   $_POST['cate_id'];
            }
            $data['link']           =   $_POST['link'] == 'http://' ? '' : $_POST['link'];
            $data['if_show']        =   $_POST['if_show'];
            $data['sort_order']     =   $_POST['sort_order'];
            $data['content']        =   $_POST['content'];

            $rows=$this->_process_mod->edit($process_id, $data);
            if ($this->_process_mod->has_error())
            {
                $this->show_warning($this->_process_mod->get_error());

                return;
            }

            $this->show_message('edit_process_successed',
                'back_list',        'index.php?app=process',
                'edit_again',    'index.php?app=process&amp;act=edit&amp;id=' . $process_id);
        }
    }

    //异步修改数据
   function ajax_col()
   {
       $id     = empty($_GET['id']) ? 0 : intval($_GET['id']);
       $column = empty($_GET['column']) ? '' : trim($_GET['column']);
       $value  = isset($_GET['value']) ? trim($_GET['value']) : '';
       $data   = array();

       if (in_array($column ,array('if_show', 'sort_order')))
       {
           $data[$column] = $value;
           $this->_process_mod->edit($id, $data);
           if(!$this->_process_mod->has_error())
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
    function drop()
    {
        $process_ids = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$process_ids)
        {
            $this->show_warning('no_such_process');

            return;
        }
        $process_ids=explode(',', $process_ids);
        $message = 'drop_ok';
        foreach ($process_ids as $key=>$process_id){
            $process=$this->_process_mod->find(intval($process_id));
            $process=current($process);
            if($process['code']!=null)
            {
                unset($process_ids[$key]);  //有部分是系统文章 过滤掉
                $message = 'drop_ok_system_process';
            }
            else
            {

            }
        }
        if (!$process_ids)
        {
            $message = 'system_process'; //全是系统文章
            $this->show_warning($message);

            return;
        }
        if (!$this->_process_mod->drop($process_ids))    //删除
        {
            $this->show_warning($this->_process_mod->get_error());

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
            $this->_process_mod->edit($id, array('sort_order' => $sort_orders[$key]));
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
    function _get_options()
    {
        $mod_pcategory = &m('pcategory');
        $pcategorys = $mod_pcategory->get_list();
        $tree =& $this->_tree($pcategorys);
        return $tree->getOptions();
    }

    /* 异步删除附件 */
    function drop_uploadedfile()
    {
        $file_id = isset($_GET['file_id']) ? intval($_GET['file_id']) : 0;
        if ($file_id && $this->_uploadedfile_mod->drop($file_id))
        {
            $this->json_result('drop_ok');
            return;
        }
        else
        {
            $this->json_error('drop_error');
            return;
        }
    }
}

?>