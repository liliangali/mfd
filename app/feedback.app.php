<?php
/**
 *    意见反馈控制器
 */
class FeedbackApp extends MallbaseApp{
    var $feedback_mod;
    function __construct(){
        parent::__construct();
        $this->feedback_mod = &m("feedback");
        header("Content-Type:text/html;charset=" . CHARSET);
    }

    function index()
    {
            if($_POST){
        	//var_dump($_POST);exit();
        	if(!empty($_POST['url'])){
        		$url=explode(',', $_POST['url']);
        		foreach ($url as $k=>$v){
        			$img_url=base64_decode($v);
        			$t = time()."$k".'.jpg';
        			$url[$k]='/feedback/'.$t;
        			$save = file_put_contents('../upload/feedback/'.$t,$img_url);
        		}
        	}
            $user_id = $this->visitor->get('user_id');
             $moblie=$_SESSION['user_info']['phone_mob'];
            $data = array(
                'add_time'=>time(),
                'description'=>$_POST['content'],
                'mobile'=>$moblie,
                'url' => 'http://'.$_SERVER['HTTP_HOST']."/feedback-index.html",
                'user_id' => empty($user_id) ? 0 : $user_id,
                'img1_url' =>!empty($url[0])?$url[0]:'',
                'img2_url' =>!empty($url[1])?$url[1]:'',
                'img3_url' =>!empty($url[2])?$url[2]:'',
                'img4_url' =>!empty($url[3])?$url[3]:'',
                'img5_url' =>!empty($url[4])?$url[4]:'',
            );

            $id = $this->feedback_mod->add($data);

            if(!$id){
                echo json_encode(false);
                return;
            }
            echo json_encode(true);
            return;
        }
          
    }

/*     function add(){
        if($_POST){
            $user_id = $this->visitor->get('user_id');
            $img_total = 5;
            $data = array(
                'add_time'=>time(),
                'description'=>$_POST['description'],
                'mobile'=>$_POST['mobile'],
                'url' => $_POST['url'],
                'user_id' => empty($user_id) ? 0 : $user_id,
                'img1_url' => $_POST['img1_url'],
                'img2_url' => $_POST['img2_url'],
                'img3_url' => $_POST['img3_url'],
                'img4_url' => $_POST['img4_url'],
                'img5_url' => $_POST['img5_url'],
            );
            $id = $this->feedback_mod->add($data);
            if(!$id){
                echo json_encode(false);
                return;
            }
            echo json_encode(true);
            return;

        }

    } */
/*     //ajax-上传图片-非ACTION
    function upload(){
        $num = $_REQUEST['num'];
        $type = $_REQUEST['type'];
        $type == '' && $type ='feedback';

        $dir = $_SERVER['DOCUMENT_ROOT'].'/upload_user_photo/'.$type.'/';
        $fileName = time() . ".jpg";

        $fileDirName2 = $dir ."235x315/" . $fileName;
        pro_img('up_file',235,315,$fileDirName2);
        $fileDirName3 = $dir ."520x685/" . $fileName;
        pro_img('up_file',520,685,$fileDirName3);

        $fileDirName1 = $dir ."original/" . $fileName;
        $rs = move_uploaded_file($_FILES['up_file']["tmp_name"],$fileDirName1);


        $src= get_domain() . "/upload_user_photo/".$type."/235x315/".$fileName;
        $arr = array('src'=>$src,'file'=>$fileName);
        echo json_encode($arr);
        exit;
    } */




}

?>
