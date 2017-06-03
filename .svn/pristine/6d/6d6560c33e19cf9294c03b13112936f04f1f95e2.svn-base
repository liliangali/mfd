<?php
/**
 *认证控制器
 * @author liang.li <1184820705@qq.com>
 * @version 1.0
 * @copyright Copyright 2014 caifeng.com
 * @package auth.app.php
*/
class AuthApp extends MallbaseApp
{
	var $real_dir;
	var $web_dir;
	function __construct()
	{
		$this->AuthApp();
	}

	/**
	* content
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-1-17
	*/
	function AuthApp()
	{
		$this->real_dir = ROOT_PATH."/upload/auth/";
		$this->web_dir = SITE_URL."/upload/auth/";
		define("AUTH", "auth/");
		parent::__construct();

		//===== 用户用户未登录 跳到登录页面 =====
		if (!$this->visitor->has_login)
		{
			$link = array("app" => "member","act" => "login" );
			$_view = &v();
			$url = $_view->build_url($link);
			header('Location: '.$url.'?ret_url=' . rawurlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']));

			//===== 如果不是品牌商 暂时不允许申请 =====
			if (!$this->visitor->get('has_store'))
			{
				$this->show_warning("只允许品牌商申请");
			}
		}
	}

	/**
	* 验证用户身份者
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-1-20
	*/
	function check_card()
	{
		echo 1;
	}

	/**
	* 个人认证主页
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-1-17
	*/
	function person()
	{
		if(!IS_POST)
		{
			//===== 导入TP的js文件用户ajax上传文件 =====
// 			$this->import_resource(array('script' => 'think/Base.js,think/mootools.js,think/prototype.js,think/ThinkAjax.js'));
			$this->import_resource(array('script' => 'jquery.min.js'));
			$this->display(AUTH."person.html");
		}
		else
		{
			//===== 正则验证身份者 =====
			$preg18 = "/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{4}$/";
			$preg15 = "/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/";
			$card = $_POST['card'];
			if (preg_match($preg18, $card) || preg_match($preg15, $card))
			{
				$this->show_warning('身份者不合法');
			}

			if (preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$_POST['card_name']))
			{
				$this->show_warning('姓名填写不合法');
			}

			$data['card'] = $card;

			echo 222;exit;


			var_dump($res);

		}

	}

	function uploadImg()
	{
// 		print_exit($_FILES);

		include_once ROOT_PATH."/includes/libraries/ImageTool.class.php";
		$imageTool = new ImageTool();
		$imageTool->_upload_dir = ROOT_PATH."/upload/auth/";
		$dir = $imageTool->uploadImage($_FILES["AidImg"]);
		if(!$dir)
		{
			echo "<script>parent.callback('".$imageTool->_error_info."',false)</script>";
		}
		$imgUrl = site_url()."/upload/auth/".$dir;
		echo "<script>parent.callback('$imgUrl',true)</script>";
		return;


		try{
				if($_FILES["AidImg"]["size"]!=0)
				{
					$uploadPath = MB_ROOT_DIR . "/pic";
					if ($_FILES["AidImg"]["size"] < 1024 * 1024 * 2) {
						if ($_FILES["AidImg"]["error"] > 0) {
							error($_FILES["AidImg"]["error"],"index.php/Campaign/campaignPublish");
						} else {
							$suffix = substr($_FILES["AidImg"]["name"], strrpos($_FILES["AidImg"]["name"], '.') + 1);
							$imgDate=date("YmdHis");
							$name = $imgDate . rand("1000", "9999") . "." . $suffix;
							if (!is_dir($uploadPath)) {
								mkdir($uploadPath);
							}
							if (move_uploaded_file($_FILES["AidImg"]["tmp_name"], $uploadPath . "/" . $name)) {
								$pf = new IBPostFile(UPLOAD_IMAGE_URL,UPLOAD_IMAGE_PORT);
								$pf->setFile("AidImg", $uploadPath . "/" . $name);
								$pf->sendRequest();
								$imgUrl = $pf->getResponse();
							}
						}
					} else {
						echo "<script>parent.callback('图片大小不能超过2M',false)</script>";
						return;
					}
				}
			}
			catch(Exception $e)
			{
				echo "<script>parent.callback('图片上传失败',false)</script>";
				return;
			}
			echo "<script>parent.callback('$imgUrl',true)</script>";

	}


	/**
	* ajax上传图片
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-1-17
	*/
	function ajax_up_file()
	{
		sleep(5);

		include_once ROOT_PATH."/includes/libraries/ImageTool.class.php";
		$imageTool = new ImageTool();
		$imageTool->_upload_dir = ROOT_PATH."/upload/auth/";
		$dir = $imageTool->uploadImage($_FILES["pic"]);
		if(!$dir)
		{
			echo 0;
			return;
		}
		$_SESSION['face_img'] = $dir;
		$imgUrl = $this->web_dir.$dir;
		echo $imgUrl;
		return;
	}

	/**
	 * ajax上传图片
	 * @version 1.0.0
	 * @author liang.li <1184820705@qq.com>
	 * @2015-1-17
	 */
	function ajax_up_back_file()
	{
		sleep(3);

		include_once ROOT_PATH."/includes/libraries/ImageTool.class.php";
		$imageTool = new ImageTool();
		$imageTool->_upload_dir = $this->real_dir;
		$dir = $imageTool->uploadImage($_FILES["pic"]);
		if(!$dir)
		{
			echo 0;
			return;
		}
		$_SESSION['back_img'] = $dir;
		$imgUrl = site_url()."/upload/auth/".$dir;
		echo $imgUrl;
		return;
	}

	/**
	* ajax删除图片
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-1-21
	*/
	function del_file()
	{
		$type = isset($_POST['type']) ? $_POST['type'] : 0;
		$mode = isset($_POST['model']) ? $_POST['model'] : '';
		$field = isset($_POST['field']) ? $_POST['field'] : '';
		$auth_id = isset($_POST['id']) ? $_POST['id'] : 0;
		$user_id = $this->visitor->get('user_id');
		$img = isset($_POST['img']) ? $_POST['img'] : '';
		$real_dir = $this->real_dir.$img;
		if (!$img)
		{
			$this->json_error('图片地址不能为空删除失败');
			return;
		}

		if ($auth_id)
		{
			if (!$mode || !$field)
			{
				$this->json_error('删除失败 找不到模型或者字段');
				return;
			}
			$mod = m($mode);

			$m_info = $mod->get($auth_id);
			if ($m_info['user_id'] != $user_id)
			{
				$this->json_error('只能删除自己上传的图片');
				return;
			}
			if(!$mod->edit($auth_id,array($field=>'')))
			{
				$this->json_error('数据库修改失败');
				return;
			}

		}


		@unlink($real_dir);
		$this->json_result();

	}

	/**
	* ajax上传图片
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-1-21
	*/
	function upload()
	{
		sleep(1);
		$type = isset($_GET['type']) ? $_GET['type'] : '';
		include_once ROOT_PATH."/includes/libraries/ImageTool.class.php";

		$imageTool = new ImageTool();
		$imageTool->_upload_dir = $this->real_dir;
		$dir = $imageTool->uploadImage($_FILES[$type]);
		if(!$dir)
		{
			$this->json_error('上传失败!');
			return;
		}
		$imgUrl = $this->web_dir.$dir;
		$this->json_result(array('name'=>$dir,'src'=>$imgUrl,'type'=>$type),'上传成功!');
		return;
	}


	/**
	* ajax获得三级联动
	* @version 1.0.0
	* @author liang.li <1184820705@qq.com>
	* @2015-1-21
	*/
	function getRegion()
	{
		$region_id = isset($_POST['region_id']) ? $_POST['region_id'] : 0;
		$cur_id    = isset($_POST['cur_id']) ? $_POST['cur_id'] : 0;
		if (!$region_id)
		{
			echo false;
			exit;
		}
		$region_mod = m('region');
		$option = $region_mod->get_options_html($region_id,$cur_id);
		echo $option;
	}







}