<?php

/**
 *    商品品牌管理控制器
 *
 *    @author    Hyber
 *    @usage    none
 */
class FeedApp extends BackendApp
{
    var $_brand_mod;

    function __construct()
    {
        $this->BrandApp();
    }

    function BrandApp()
    {
        parent::BackendApp();

        $this->_feed_mod =& m('feedamount');
        $this->_fbcategory_mod =& m('fbcategory');
        $this->_brand_mod = & m('brand');
        $this->type_list = $this->_feed_mod->getType();
        $this->time_list = $this->_feed_mod->getTime();
        $this->body_list = $this->_feed_mod->getBody();
        $this->run_list = $this->_feed_mod->getRun();
        $this->yes_or_no = array(0=>'否',1=>'是');

        $this->assign('yes_or_no',$this->yes_or_no);
        $this->assign('type_list',$this->type_list);
        $this->assign('time_list',$this->time_list);
        $this->assign('body_list',$this->body_list);
        $this->assign('run_list',$this->run_list);
        $qianqi_list = $this->_fbcategory_mod->find(array(
            'conditions' => 'parent_id = 34',
        ));
        $qian = array();
        foreach ((array)$qianqi_list as $index => $item)
        {
            $qian[$index] = $item['cate_name'];
        }

        $this->age_list = $qian;
        $this->assign('age_list',$this->age_list);
//   echo '<pre>';print_r($qianqi_list);exit;


    }

    /**
     *    商品品牌索引
     *
     *    @author    Hyber
     *    @return    void
     */
    function index()
    {

        $page   =   $this->_get_page(20);   //获取分页信息
        //更新排序
        if (isset($_GET['sort']) && isset($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
             $sort  = 'id';
             $order = 'desc';
            }
        }
        else
        {
            $sort  = 'id';
            $order = 'desc';
        }
//        if($_SESSION['fbtype'])
//        {
//            $fbtype = $_SESSION['fbtype'];
//        }
//        if($_SESSION['age_id'])
//        {
//            $age_id = $_SESSION['age_id'];
//        }
        
        $fbtype = isset($_REQUEST['fbtype']) ? $_REQUEST['fbtype'] : 0;
        $age_id= isset($_REQUEST['age_id']) ? $_REQUEST['age_id'] : 0;
//        if($gfbtype)
//        {
//            $_SESSION['fbtype'] = $gfbtype;
//        }
//        else
//        {
//            $_SESSION['fbtype'] = 0;
//        }
//        if($gage_id)
//        {
//            $_SESSION['age_id'] = $gage_id;
//        }
//        else
//        {
//            $_SESSION['age_id'] = 0;
//        }


        $this->assign('fbtype',$fbtype);
        $this->assign('age_id',$age_id);
        $conditions = '1=1';
        if($fbtype)
        {
            $conditions .= " AND fbtype = $fbtype";
        }
        if($age_id)
        {
            $conditions .= " AND age_id = $age_id";
        }

        $brands=$this->_feed_mod->find(array(
        'conditions'    =>   $conditions,
        'limit'         => $page['limit'],
        'order'         => "$sort $order",
        'count'         => true
        ));
        
        foreach ((array)$brands as $key => &$brand)
        {
            $brands[$key]['fbtype_name'] = $this->type_list[$brand['fbtype']];
            $brands[$key]['age_id_name'] = $this->age_list[$brand['age_id']];
            $brands[$key]['time_id_name'] = $this->time_list[$brand['time_id']];
            $brands[$key]['body_condition_name'] = $this->body_list[$brand['body_condition']];
            $brands[$key]['run_time_name'] = $this->run_list[$brand['run_time']];
            $brands[$key]['feed_name'] = $this->yes_or_no[$brand['feed']];
        }

        $page['item_count']=$this->_feed_mod->getCount();   //获取统计数据
//        echo '<pre>';print_r($page);exit;
        
        /* 导入jQuery的表单验证插件 */
        $this->import_resource(array(
            'script' => 'jqtreetable.js,inline_edit_admin.js',
            'style'  => 'res:style/jqtreetable.css'
        ));
        $this->_format_page($page);
        $this->assign('wait_verify', $_GET['wait_verify']);
        $this->assign('page_info', $page);   //将分页信息传递给视图，用于形成分页条
        $this->assign('brands', $brands);
        $this->display('feed/brand.index.html');
    }
     /**
     *    新增商品品牌
     *
     *    @author    Hyber
     *    @return    void
     */
    function add()
    {
        if (!IS_POST)
        {
            /* 显示新增表单 */
            $brand = array(

            );

            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->assign('brand', $brand);
            $this->display('feed/brand.form.html');
        }
        else
        {
            unset($_POST['Submit']);
            if (!$brand_id = $this->_feed_mod->add($_POST))  //获取brand_id
            {
                $this->show_warning($this->_feed_mod->get_error());
                return;
            }

            $this->show_message('添加成功',
                'back_list',    'index.php?app=feed',
                'continue_add', 'index.php?app=feed&amp;act=add'
            );
        }
    }

    /* 检查品牌唯一 */
    function check_brand ()
    {
        $brand_name = empty($_GET['brand_name']) ? '' : trim($_GET['brand_name']);
        $brand_id   = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!$brand_name) {
            echo ecm_json_encode(false);
        }
        if ($this->_brand_mod->unique($brand_name, $brand_id)) {
            echo ecm_json_encode(true);
        }
        else
        {
            echo ecm_json_encode(false);
        }
        return ;
    }

     /**
     *    编辑商品品牌
     *
     *    @author    Hyber
     *    @return    void
     */
    function edit()
    {
        $brand_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $fbtype = isset($_GET['fbtype']) ? $_GET['fbtype'] : 0;
        $age_id= isset($_GET['age_id']) ? $_GET['age_id'] : 0;
        if (!$brand_id)
        {
            $this->show_warning('no_such_brand');
            return;
        }
         if (!IS_POST)
        {
            $find_data     = $this->_feed_mod->find($brand_id);
            if (empty($find_data))
            {
                $this->show_warning('no_such_brand');

                return;
            }
            $brand    =   current($find_data);
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));

            $this->assign('brand', $brand);
            $this->display('feed/brand.form.html');
        }
        else
        {
            $data = array();
            unset($_POST['Submit']);
            $rows=$this->_feed_mod->edit($brand_id, $_POST);
            if ($this->_feed_mod->has_error())
            {
                $this->show_warning($this->_brand_mod->get_error());
                return;
            }

            $this->show_message('编辑成功',
                'back_list',        'index.php?app=feed&amp;page='.$page."&amp;fbtype=".$fbtype."&amp;age_id=".$age_id,
                '编辑',    'index.php?app=feed&amp;act=edit&amp;id=' . $brand_id);
        }
    }

         //异步修改数据
   function ajax_col()
   {
       $id     = empty($_GET['id']) ? 0 : intval($_GET['id']);
       $column = empty($_GET['column']) ? '' : trim($_GET['column']);
       $value  = isset($_GET['value']) ? trim($_GET['value']) : '';
       $data   = array();

       if (in_array($column ,array('brand_name', 'recommended', 'sort_order', 'tag')))
       {
           $data[$column] = $value;
           if($column == 'brand_name')
           {
               $brand = $this->_brand_mod->get_info($id);

               if(!$this->_brand_mod->unique($value, $id))
               {
                   echo ecm_json_encode(false);
                   return ;
               }
           }
           $this->_brand_mod->edit($id, $data);
           if(!$this->_brand_mod->has_error())
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
        $brand_ids = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$brand_ids)
        {
            $this->show_warning('ID');

            return;
        }
        $brand_ids=explode(',',$brand_ids);
        $this->_feed_mod->drop($brand_ids);
        if ($this->_brand_mod->has_error())    //删除
        {
            $this->show_warning($this->_brand_mod->get_error());

            return;
        }

        $this->show_message('删除成功');
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
            $this->_brand_mod->edit($id, array('sort_order' => $sort_orders[$key]));
        }

        $this->show_message('update_order_ok');
    }

        /**
     *    处理上传标志
     *
     *    @author    Hyber
     *    @param     int $brand_id
     *    @return    string
     */
    function _upload_logo($brand_id)
    {
        $file = $_FILES['logo'];
        if ($file['error'] == UPLOAD_ERR_NO_FILE) // 没有文件被上传
        {
            return '';
        }
        import('uploader.lib');             //导入上传类
        $uploader = new Uploader();
        $uploader->allowed_type(IMAGE_FILE_TYPE); //限制文件类型
        $uploader->addFile($_FILES['logo']);//上传logo
        if (!$uploader->file_info())
        {
            $this->show_warning($uploader->get_error() , 'go_back', 'index.php?app=brand&amp;act=edit&amp;id=' . $brand_id);
            return false;
        }
        /* 指定保存位置的根目录 */
        $uploader->root_dir(ROOT_PATH);

        /* 上传 */
        if ($file_path = $uploader->save('data/files/mall/brand', $brand_id))   //保存到指定目录，并以指定文件名$brand_id存储
        {
            return $file_path;
        }
        else
        {
            return false;
        }
    }

    /**
     * 更新字段
     *
     */
    function update()
    {
        $allow_cols=array(
        'recommended',  //允许更新的字段
        );
        $col    =   trim($_GET['col']);
        $value  =   trim($_GET['value']);
        if (!in_array($col, $allow_cols))
        {
            $this->show_warning('Hacking attempt');
            return;
        }
        $brand_ids = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!$brand_ids)
        {
            $this->show_warning('no_such_brand');

            return;
        }
        $brand_ids=explode(',',$brand_ids);
        $data = array();
        $data[$col] = $value;

        $rows=$this->_brand_mod->edit($brand_ids, $data);
        if ($this->_brand_mod->has_error())
        {
            $this->show_warning($this->_brand_mod->get_error());

            return;
        }
        $this->show_message('update_' . $col . '_successed');
    }

    function pass()
    {
        $id = $_GET['id'];
        if (empty($id))
        {
            $this->show_warning('request_error');
            exit;
        }
        $ids = explode(',', $id);
        $brands = $this->_brand_mod->find(db_create_in($ids, 'brand_id') . " AND if_show = 0");
        $this->_brand_mod->edit(db_create_in(array_keys($brands), 'brand_id'), array('if_show' => 1));
        if ($this->_brand_mod->has_error())
        {
            $this->show_warning($this->_brand_mod->get_error());
            exit;
        }
        $ms =& ms();
        $content = '';
        foreach ($brands as $brand)
        {
            $content = get_msg('toseller_brand_passed_notify', array('brand_name' => $brand['brand_name']));
            $ms->pm->send(MSG_SYSTEM, $brand['store_id'], '', $content);
        }
        $this->show_message('brand_passed',
            'back_list', 'index.php?app=brand&wait_verify=1');
    }

    function refuse()
    {
        $id = $_GET['id'];
        if (empty($id))
        {
            $this->show_warning('request_error');
            exit;
        }
        if (!IS_POST)
        {
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            $this->display('brand_refuse.html');
        }
        else
        {
            if (empty($_POST['content']))
            {
                $this->show_warning('content_required');
                exit;
            }
            $ids = explode(',', trim($_GET['id']));
            $brands = $this->_brand_mod->find(db_create_in($ids, 'brand_id') . ' AND if_show = 0');
            $ms =& ms();
            $content = '';
            foreach ($brands as $brand)
            {
                $content = get_msg('toseller_brand_refused_notify', array('brand_name' => $brand['brand_name'], 'reason' => trim($_POST['content'])));
                $ms->pm->send(MSG_SYSTEM, $brand['store_id'], '', $content);
                if (is_file(ROOT_PATH . '/' . $brand['brand_logo']) && file_exists(ROOT_PATH . '/' . $brand['brand_logo']))
                {
                    unlink(ROOT_PATH . '/' . $brand['brand_logo']);
                }
                $this->_brand_mod->drop($brand['brand_id']);
            }
            $this->show_message('brand_refused',
                    'back_list', 'index.php?app=brand&wait_verify=1');
        }
    }


}

?>