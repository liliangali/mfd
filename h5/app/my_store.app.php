<?php

class My_storeApp extends StoreadminbaseApp
{
    var $_store_id;
    var $_store_mod;
    var $_uploadedfile_mod;
    var $_store_attr_mod;

    function __construct()
    {
        $this->My_storeApp();
    }
    function My_storeApp()
    {
        parent::__construct();
        $this->_store_id  = intval($this->visitor->get('manage_store'));
        $this->_store_mod =& m('store');
        $this->_store_attr_mod =& m('store_attr');
        $this->_store_service_mod =& m('store_service');
        $this->_uploadedfile_mod = & m('uploadedfile');
    }

    function index()
    {
        $this->assign('title','RCTAILOR-酷客中心-店铺设置');
        $this->assign('title_info','店铺设置');

        $functions = $tmp_info['functions'] ? explode(',', $tmp_info['functions']) : array();
        $subdomain_enable = false;
        if (ENABLED_SUBDOMAIN && in_array('subdomain', $functions))
        {
            $subdomain_enable = true;
        }
        if (!IS_POST)
        {
            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array('script' => 'mlselection.js,jquery.plugins/jquery.validate.ad.js'));
            //获取地区
            $region_mod =& m('region');
            $this->assign('site_url', site_url());
            $this->assign('regions', $region_mod->get_options(0));

            //传给iframe参数belong, item_id
            $this->assign('belong', BELONG_STORE);
            $this->assign('id', $this->_store_id);

            $store = $this->_store_mod->get_info($this->_store_id);
            foreach ($functions as $k => $v)
            {
                $store['functions'][$v] = $v;
            }

            
            //查询属性
            $attr = $this->_store_attr_mod->find(array('conditions'=>'store_id='.$this->_store_id));
            $store_arrt_list = lang::get('store_arrt_list');
            foreach($attr as $avl){
                $type_id = $avl['type_id'];
                $content_id = $avl['content_id'];
                $ch = $store_arrt_list[$type_id][$content_id]['ch'];
                $store[$ch] = $ch ? 1 : 0;
            }

            $this->assign('store', $store);
            $this->display('my_store.index.html');
        }
        else
        {
            $data = $this->_upload_files();
            if ($data === false)
            {
                return;
            }
            else //删除冗余图标
            {
                if($store['store_logo'] != '' && $data['store_logo'] != '')
                {
                    $store_logo_old = pathinfo($store['store_logo']);
                    $store_logo_new = pathinfo($data['store_logo']);
                    if($store_logo_old['extension'] != $store_logo_new['extension'])
                    {
                        unlink($store['store_logo']);
                    }
                }

                if($store['fw_logo'] != '' && $data['fw_logo'] != '')
                {
                    $store_fw_logo_old = pathinfo($store['fw_logo']);
                    $store_fw_logo_new = pathinfo($data['fw_logo']);
                    if($store_fw_logo_old['extension'] != $store_fw_logo_new['extension'])
                    {
                        unlink($store['fw_logo']);
                    }
                }
            }
            
            $data = array_merge($data, array(
                'store_name' => $_POST['store_name'],
                'region_id'  => $_POST['region_id'],
                'region_name'=> $_POST['region_name'],
                'description'=> $_POST['description'],
                'address'    => $_POST['address'],
                'email'        => $_POST['email'],
                'im_qq'      => $_POST['im_qq'],
                'im_wx'      => $_POST['im_wx'],
                'experience' => $_POST['experience'],
                'fw_description' => $_POST['fw_description']
            ));
            $this->_store_mod->edit($this->_store_id, $data);
            $s_info = $this->_store_attr_mod->drop('store_id='.$this->_store_id);
            $store_arrt_list = lang::get('store_arrt_list');
            //更改属性
            foreach($store_arrt_list as $k=>$sal){
                foreach($sal as $sk=>$s){
                    if($_POST[$s['ch']]){
                        $this->_store_attr_mod->add(array('type_id'=>$k,'content_id'=>$sk,'store_id'=>$this->_store_id,'attr_name'=>$s['name']));
                    }
                }
            }
            $this->show_message('edit_ok');
        }
    }

    //服务信息管理（作品上传）
    function service(){
        $store_service = $this->_store_service_mod->find(array('conditions'=>'store_id='.$this->_store_id));
        $this->assign('store_service', $store_service);
        $this->display('my_store.service.html');
    }
    //添加服务信息
    function add_service(){
        if(!$_POST){
            $this->display('my_store.service.form.html');
        }else{
            $image = $this->_upload_files_service($this->_store_id);
            $data = array(
                'store_id' =>$this->_store_id,
                'img_name'=>$_POST['img_name'],
                'description'=>$_POST['description'],
                'add_time' => time()
                );
            $this->_store_service_mod->add(array_merge($data, $image));
            $this->_store_mod->setInc($this->_store_id,'published');
            $this->show_message('添加作品成功！');
        }
       
    }

    //修改服务信息
    function edit_service(){
        $args = $this->get_params();
        $id = isset($args[0]) ? intval($args[0]) : 0;
        if(!$id){
            $this->show_warning('此作品无效!');

        }
        $store_service = $this->_store_service_mod->get($id);
        if(!$_POST){
            $this->assign('store_service', $store_service);
            $this->display('my_store.service.form.html');
        }else{
            $image = $this->_upload_files_service($this->_store_id);
            if($store_service['img_url'] != '' && $image['img_url'] != '')
                {
                    $img_url_old = pathinfo($store_service['img_url']);
                    $img_url_new = pathinfo($image['img_url']);
                    if($img_url_old['extension'] != $img_url_new['extension'])
                    {
                        unlink($store_service['img_url']);
                    }
            }
            $data = array(
                'img_name'=>$_POST['img_name'],
                'description'=>$_POST['description'],
                'add_time' => time()
                );
            $this->_store_service_mod->edit($id, array_merge($data, $image));
            $this->show_message('edit_ok');

        }
    }
    //删除裁缝晒单
    function drop_service(){
        $args = $this->get_params();
        $id = isset($args[0]) ? intval($args[0]) : 0;
        if(!$id){
            $this->show_warning('此作品无效!');

        }
        $store_service = $this->_store_service_mod->get($id);
        if(!$store_service){
            $this->show_warning('删除失败!');
        }
        //删除相关图片
        unlink($store_service['img_url']);
        $drop_info = $this->_store_service_mod->drop($id);
        if($drop_info){
            $this->show_message('删除成功');
        }
    }



    function update_im_msn()
    {
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        $this->_store_mod->edit($this->_store_id, array('im_msn' => $id));
        header("Location: index.php?app=my_store");
        exit;
    }

    function drop_im_msn()
    {
        $this->_store_mod->edit($this->_store_id, array('im_msn' => ''));
        header("Location: index.php?app=my_store");
        exit;
    }

    function _get_member_submenu()
    {
        return array(
            array(
                'name' => 'my_store',
                'url'  => 'index.php?app=my_store',
            ),
        );
    }

    //上传作品
    function _upload_files_service($store_id){
        import('uploader.lib');
        $data = array();
        /* store_logo */
        $file = $_FILES['img_url'];
        if ($file['error'] == UPLOAD_ERR_OK && $file !='')
        {
            $uploader = new Uploader();
            $uploader->allowed_type(IMAGE_FILE_TYPE);
            $uploader->allowed_size(SIZE_STORE_LOGO); // 20KB
            $uploader->addFile($file);
            if ($uploader->file_info() === false)
            {
                $this->show_warning($uploader->get_error());
                return false;
            }
            $uploader->root_dir(ROOT_PATH);
            $data['img_url'] = $uploader->save('data/files/store_' . $this->_store_id . '/other/service', time());
        }
        return $data;
    }



    /**
     * 上传文件
     *
     */
    function _upload_files()
    {
        import('uploader.lib');
        $data      = array();
        /* store_logo */
        $file = $_FILES['store_logo'];
        if ($file['error'] == UPLOAD_ERR_OK && $file !='')
        {
            $uploader = new Uploader();
            $uploader->allowed_type(IMAGE_FILE_TYPE);
            //$uploader->allowed_size(SIZE_STORE_LOGO); // 20KB
            $uploader->allowed_size('1024000'); // 200KB
            $uploader->addFile($file);
            if ($uploader->file_info() === false)
            {
                $this->show_warning($uploader->get_error());
                return false;
            }
            $uploader->root_dir(ROOT_PATH);
            $data['store_logo'] = $uploader->save('upload_user_photo/files/store_' . $this->_store_id . '/other', 'store_logo');
        }

        /* fw_logo */
        $file = $_FILES['fw_logo'];
        if ($file['error'] == UPLOAD_ERR_OK && $file !='')
        {
            $uploader = new Uploader();
            $uploader->allowed_type(IMAGE_FILE_TYPE);
            $uploader->allowed_size('1024000'); // 200KB
            $uploader->addFile($file);
            if ($uploader->file_info() === false)
            {
                $this->show_warning($uploader->get_error());
                return false;
            }
            $uploader->root_dir(ROOT_PATH);
            $data['fw_logo'] = $uploader->save('upload_user_photo/files/store_' . $this->_store_id . '/other',time());
        }
		//print_r($_FILES); exit;
        return $data;
    }
        /* 异步删除附件 */
    function drop_uploadedfile()
    {
        $file_id = isset($_GET['file_id']) ? intval($_GET['file_id']) : 0;
        $file = $this->_uploadedfile_mod->get($file_id);
        if ($file_id && $file['store_id'] == $this->visitor->get('manage_store') && $this->_uploadedfile_mod->drop($file_id))
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
