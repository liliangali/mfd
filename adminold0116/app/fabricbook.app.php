<?php

class FabricbookApp extends BackendApp
{
    var $_fabricbook_mod;
    
    function __construct()
    {
        parent::BackendApp();
        $this->_fabricbook_mod = &m('fabricbook');
        $this->_mod_bookGallery = &m("bookgallery");
    }
    function index()
    {
        $query["category"] = !empty($_GET['category'])? intval($_GET['category']) : 0;
        $query["name"] = !empty($_GET['name'])? trim($_GET['name']) : '';
        $conditions = " 1 ";
        
        if (!empty($query["category"])){
            $conditions .= " AND category='{$query["category"]}'";
        }
        
        if(!empty($query["name"])){
            $conditions .= " AND name LIKE '%{$query["name"]}%'";
        }
        
        $page    =   $this->_get_page(30);
        $fabrics =   $this->_fabricbook_mod->find(array(
            'conditions'  => $conditions,
            'limit'       => $page['limit'],
            'order'       => 'id DESC', 
            'count'       => true
        ));
        $page['item_count']=$this->_fabricbook_mod->getCount();
        $this->_format_page($page);
        $this->import_resource(array('script' => 'inline_edit_admin.js'));
        $this->assign('query', $query); 
        $this->assign('page_info', $page);
        $this->assign('fabrics', $fabrics);
        $this->assign("category", $this->_fabricbook_mod->getCategory());
        $this->display('fabricbook.index.html');
    }
    function add()
    {
        if (!IS_POST)
        {
            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
            $param = array(
                'dir' => 'gallery',
                'button_text' => "上传相册图片"
            );
            $this->assign("category", $this->_fabricbook_mod->getCategory());
            $this->assign('build_upload', $this->_build_upload($param)); // 构建swfupload上传组件
            $this->display('fabricbook.form.html');
        }
        else
        {
            $data = array();
            $data['name']      =   $_POST['name'];
            $data['category']    =   $_POST['category'];
            $data['is_sale']    =   $_POST['is_sale'];
            $data['price'] =   $_POST['price'];
            $data['brief'] =   $_POST['brief'];
            $data['store'] =   $_POST['store'];
            $data['add_time']   =   gmtime();
            $data['small_img']   =   $_POST['small_img'];
            $data['source_img']   =  $_POST['source_img'];
            $data['aftersale']    = $_POST['aftersale'];
            $data['unit']=$_POST['unit'];
            if (!$id = $this->_fabricbook_mod->add($data))
            {
                $this->show_warning($this->_fabricbook_mod->get_error());

                return;
            }

            $this->addGallery($id);
            $this->show_message('添加面料册成功',
                'back_list',    'index.php?app=fabricbook',
                'continue_add', 'index.php?app=fabricbook&amp;act=add'
            );
        }
    }

    function edit()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id)
        {
            $this->show_warning('参数错误');
            return;
        }
         if (!IS_POST)
        {
            $find_data     = $this->_fabricbook_mod->get_info($id);
            if (empty($find_data))
            {
                $this->show_warning('参数错误');

                return;
            }
            $param = array(
                'dir' => 'gallery',
                'button_text' => "上传相册图片"
            );
            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
            $this->assign('data', $find_data);
            $this->assign("category", $this->_fabricbook_mod->getCategory());
            $gallery_list = $this->_mod_bookGallery->find(array(
                'conditions' => "book_id = '{$id}'",
            ));
            
            $this->assign("gallery_list", $gallery_list);
            $this->assign('build_upload', $this->_build_upload($param)); // 构建swfupload上传组件
            $this->display('fabricbook.form.html');
        }
        else
        {
            $data = array();
            $data['name']      =   $_POST['name'];
            $data['category']    =   $_POST['category'];
            $data['is_sale']    =   $_POST['is_sale'];
            $data['price'] =   $_POST['price'];
            $data['brief'] =   $_POST['brief'];
            $data['store'] =   $_POST['store'];
            $data['add_time']   =   gmtime();
            $data['small_img']   =   $_POST['small_img'];
            $data['source_img']   =  $_POST['source_img'];
            $data['aftersale']    = $_POST['aftersale'];
            $data['unit']=$_POST['unit'];
            
            $rows=$this->_fabricbook_mod->edit($id, $data);
            if ($this->_fabricbook_mod->has_error())
            {
                $this->show_warning($this->_fabricbook_mod->get_error());

                return;
            }
            $this->addGallery($id);
            $this->show_message('面料册编辑成功',
                'back_list',        'index.php?app=fabricbook',
                '继续编辑',    'index.php?app=fabricbook&amp;act=edit&amp;id=' . $id);
        }
    }

    function drop()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$this->_fabricbook_mod->drop($id))    //删除
        {
            $this->show_warning($this->_fabricbook_mod->get_error());

            return;
        }
        
        $this->_mod_bookGallery->drop("book_id='{$id}'");
        
        $this->show_message("删除成功");
    }
    
    
    function addGallery($id){

        $gallery = array();
        foreach((array)$_POST['gallery'] as $k => $v){
                $gallery[] = array(
                    'book_id' => $id,
                    'image'   => $v,
                );
        }
        if(!empty($gallery)){
            $this->_mod_bookGallery->add($gallery);
        }
    }
    
    function drop_gallery(){
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($this->_mod_bookGallery->drop($id))
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