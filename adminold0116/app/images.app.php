<?php

/* 图片 */
class ImagesApp extends BackendApp
{
    var $dir; // 上传的文件所属的模型
    var $mod_uploadedfile; //上传文件模型
    var $item_id = 0; //所属模型的ID
    var $save_path; // 储存路径
    function __construct()
    {

        $this->_init_session();
        if($_POST['PHPSESS']){
            $sess = json_decode(base64_decode($_POST['PHPSESS']),1);
            $_SESSION['admin_info'] = $sess;
        }
    	parent::__construct();
    	
        $this->mod_uploadedfile = &m('uploadedfile');
    }

    function index()
    {
    	
    	$dir    = isset($_GET["d"]) ? trim($_GET["d"]) : "other";
    	$water  = !empty($_GET["w"]) ?  1 : 0;
    	$param = array(
    			'dir' => $dir,
    			'button_text' => "选择文件", 
    			'water' => $water,
    	);
    	
        $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,change_upload.js'));
        $this->assign('build_upload', $this->_build_upload($param)); // 构建swfupload上传组件
        
        $this->assign("dir", $dir);
        $this->assign("water", $water);
        $this->assign('callback', $_GET['callback']);
        $this->display('image.index.html');
    }
    
	function upload()
    {
    	
        /* 初始化 */
        if (isset($_POST['dir']))
        {
            $this->dir = $_POST['dir'];
        }
        else
        {
            $this->json_error('no_post_param_belong');
            exit();
        }
        
  		#add by v5 整理【标签】<input_img ..> 上传目录
        $this->save_path = "upload/images/".$this->dir;

        $ret_info = array(); // 返回到客户端的信息
        $file = $_FILES['Filedata'];
        if ($file['error'] == UPLOAD_ERR_NO_FILE) // 没有文件被上传
        {
            $this->json_error('no_upload_file');
            exit();
        }
        import('uploader.lib'); // 导入上传类
        import('image.func');
        $uploader = new Uploader();
        //文件保存目录
        $get_save_path=$uploader->get_save_path();
        $save_path=$this->save_path.'/'.$get_save_path;
        
        
        $uploader->allowed_type(IMAGE_FILE_TYPE); // 限制文件类型
        $uploader->allowed_size(2048000); // 限制单个文件大小2M
        $uploader->addFile($_FILES['Filedata']);
        if (!$uploader->file_info())
        {
            $this->json_error($uploader->get_error());
            exit();
        }

        /* 指定保存位置的根目录 */
        $uploader->root_dir(ROOT_PATH);
        $filename  = $uploader->random_filename();
        
        /* 上传 */
        $file_path = $uploader->save($save_path, $filename);   // 保存到指定目录
        if (!$file_path)
        {
            $this->json_error('file_save_error');
            exit();
        }
        
        if($_POST['water']){
        	water_mark(ROOT_PATH."/".$file_path, ROOT_PATH."/".WATER_IMG, WATER_POS);
        }
        $file_type = $this->_return_mimetype($file_path);
       
        /* 文件入库 */
        $data = array(
            'store_id'  => 0,
            'file_type' => $file_type,
            'file_size' => $file['size'],
            'file_name' => $file['name'],
            'file_path' => $file_path,
            'dir'    => $this->dir,
            'admin_id'   => $this->visitor->info["user_id"],
            'add_time'  => gmtime(),
        );
        
        
        $file_id = $this->mod_uploadedfile->add($data,false,0);
        
        if (!$file_id)
        {
            $this->json_error('file_add_error');
            return false;
        }

        /* 返回客户端 */
        $ret_info =array(
            'file_id'   => $file_id,
            'file_path' => $file_path
        );
        $this->json_result($ret_info);
    }

    function _return_mimetype($filename)
    {
        preg_match("|\.([a-z0-9]{2,4})$|i", $filename, $fileSuffix);
        switch(strtolower($fileSuffix[1]))
        {
            case "js" :
                return "application/x-javascript";

            case "json" :
                return "application/json";

            case "jpg" :
            case "jpeg" :
            case "jpe" :
                return "image/jpeg";

            case "png" :
            case "gif" :
            case "bmp" :
            case "tiff" :
                return "image/".strtolower($fileSuffix[1]);

            case "css" :
                return "text/css";

            case "xml" :
                return "application/xml";

            case "doc" :
            case "docx" :
                return "application/msword";

            case "xls" :
            case "xlt" :
            case "xlm" :
            case "xld" :
            case "xla" :
            case "xlc" :
            case "xlw" :
            case "xll" :
                return "application/vnd.ms-excel";

            case "ppt" :
            case "pps" :
                return "application/vnd.ms-powerpoint";

            case "rtf" :
                return "application/rtf";

            case "pdf" :
                return "application/pdf";

            case "html" :
            case "htm" :
            case "php" :
                return "text/html";

            case "txt" :
                return "text/plain";

            case "mpeg" :
            case "mpg" :
            case "mpe" :
                return "video/mpeg";

            case "mp3" :
                return "audio/mpeg3";

            case "wav" :
                return "audio/wav";

            case "aiff" :
            case "aif" :
                return "audio/aiff";

            case "avi" :
                return "video/msvideo";

            case "wmv" :
                return "video/x-ms-wmv";

            case "mov" :
                return "video/quicktime";

            case "rar" :
            return "application/x-rar-compressed";

            case "zip" :
                return "application/zip";

            case "tar" :
                return "application/x-tar";

            case "swf" :
                return "application/x-shockwave-flash";

            default :
            if(function_exists("mime_content_type"))
            {
                $fileSuffix = mime_content_type($filename);
            }
            return "unknown/" . trim($fileSuffix[0], ".");
        }
    }
    
    /**
     * author yaho.bai
     * 遍历图片库
     * 
     */
    
    function fromdb(){
    	
    	$uptime = isset($_GET['uptime']) ? strtotime(trim($_GET['uptime'])) : 0;
    	$filename = isset($_GET['filename']) ? trim($_GET['filename']) : '';
    	
    	$dir    = isset($_GET["d"]) ? trim($_GET["d"]) : "other";
    	$water  = !empty($_GET["w"]) ?  1 : 0;

    	$this->assign("dir", $dir);
    	$this->assign("water", $water);
    	
    	$conditions = '';
    	if($uptime > 0){
    		$conditions .= " AND add_time = '{$uptime}'";
    	}
    	 
    	if($filename){
    		$conditions .= " AND file_name LIKE '%".$filename."%'";
    	}
    	$page = $this->_get_page(12);
    	$img_list = $this->mod_uploadedfile->find(array(
    			'fields' => 'file_path, file_name',
    			'conditions' => '1=1' . $conditions,
    			'limit' => $page['limit'],
    			'order' => "add_time DESC",
    			'count' => true,
    	));
    	
    	$this->assign('img_list', $img_list);
    	$page['item_count'] = $this->mod_uploadedfile->getCount();
    	$this->_format_page($page);
    	
    	$this->assign('page_info', $page);
    	
    	$this->assign('filename', $filename);
    	$this->assign('uptime', $_GET['uptime']);
    	
    	$this->assign('site_url', SITE_URL);
    	
    	$this->assign('callback', $_GET['callback']);
    	$this->import_resource(array(
    			'script' => 'jqtreetable.js,inline_edit_admin.js',
    			'style'  => 'res:style/jqtreetable.css'
    	));
    	 
    	
    	$this->display("image.db.html");
    }
    
    
    /**
     * author yhao.bai
     * 网络图片地址
     */
    function fromweb(){
    	
    	$dir    = isset($_GET["d"]) ? trim($_GET["d"]) : "other";
    	$water  = !empty($_GET["w"]) ?  1 : 0;
    	$this->assign("dir", $dir);
    	$this->assign("water", $water);
    	$this->assign('callback', $_GET['callback']);
    	$this->display("image.web.html");
    	
    }

}
?>
