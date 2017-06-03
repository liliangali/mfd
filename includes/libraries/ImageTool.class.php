<?php
	/**
	 *图片处理类
	 * @author liang.li <1184820705@qq.com>
	 * @version 1.0
	 * @copyright Copyright 2014 caifeng.com
	 * @package ImageTool.class.php
	*/
	class ImageTool {
		private $_allow_types;
		private $_max_size;
		public $_error_info;
		public $_error_infos;
		public $_upload_dir;
		private $creators = array(
					'image/jpeg' => 'imagecreatefromjpeg',
					'image/gif' => 'imagecreatefromgif',
					'image/png' => 'imagecreatefrompng',
					'image/x-png'=>'imagecreatefrompng',
					'image/pjpeg'=>'imagecreatefrompjpeg'
				);
		private $makers_img = array(
				'image/jpeg' => 'imagejpeg',
				'image/gif' => 'imagegif',
				'image/png' => 'imagepng',
				'image/x-png'=>'imagepng',
				'image/pjpeg'=>'imagepjpeg',
				);
		
		public function __construct() {
			$this->_max_size = isset($GLOBALS['configs']['MAX_SIZE']) ? $GLOBALS['configs']['ADMIN']['MAX_SIZE']:'50000000';
			$this->_allow_types = 'image/gif,image/jpeg,image/png,image/x-png,image/pjpeg';
			$default_upload_dir = ROOT_PATH.'/upload/images/';
			$this->_upload_dir = $default_upload_dir;

		}
		/**
		*对上传文件进行验证 并且保存
		Array
		(
			 [ori_name] => Array
			(
            [name] => aa.jpg
            [type] => image/pjpeg
            [tmp_name] => C:\Windows\temp\php3DF0.tmp
            [error] => 0
            [size] => 1097893
			)

		)
		*处理上传单个文件 并且把它加入到文件夹
		*/
		public function uploadImage($file) {
			//判断文件上传成功了没有
//echo $file['error'];
//exit();
			if($file['error'] == 0) {
				//判断文件大小是否超过限制
				if($file['size'] < $this->_max_size) {
					//判断文件是否符合格式
//var_dump(strpos($this->_allow_types,$file['type']));exit;
					//这里要注意要用不全等来判断 因为 strpos返回的是字符串首次出现的位置，如果 恰好出现在0位置，则会返回不执行了
					if(strpos($this->_allow_types,$file['type']) !==false) {
//print_r($file['type']);exit;
						//为图片创建一个新的名字
						$new_name = date('YmdHis').'_' . $this->getUniqueString() . strrchr($file['name'], '.');
						//创建一个目录
						$sub_dir = date('Ym');

						if(!is_dir($this->_upload_dir.$sub_dir)) {
							mkdir($this->_upload_dir.$sub_dir, 0777, true);
						}
						if(move_uploaded_file($file['tmp_name'],$this->_upload_dir.$sub_dir.'/'.$new_name)) {
							//上传文件成功 并且文件被放在了$this->_upload_dir.$new_name下边
							return $sub_dir.'/'.$new_name;
						}else {
							$this->_error_info = '文件上传失败 可能是文件存放目录没有写权限造成的';
							return false;
						}
					} else {
						$this->_error_info = "文件格式不正确必须是".$this->_allow_types;
						return false;
					}
				} else {
					$this->_error_info = "文件过大";
					return false;
				}


			}else {
				$this->_error_info = "反正是因为 文件error 那几个错误造成的";	
			}
			return false;
		}
		/**
		*Array
			(
    [ori_img] => Array
				 (
            [name] => Array
                (
                    [0] => IMG_20120526_134620.jpg
                    [2] => IMG_20120526_134708.jpg
                    [ca] => IMG_20120526_135131.jpg
                )

            [type] => Array
                (
                    [0] => image/jpeg
                    [2] => image/jpeg
                    [ca] => image/jpeg
                )

            [tmp_name] => Array
                (
                    [0] => C:\Windows\temp\phpEE68.tmp
                    [2] => C:\Windows\temp\phpEE88.tmp
                    [ca] => C:\Windows\temp\phpEEA9.tmp
                )

            [error] => Array
                (
                    [0] => 0
                    [2] => 0
                    [ca] => 0
                )

            [size] => Array
                (
                    [0] => 1205675
                    [2] => 1144952
                    [ca] => 905542
                )

			 )
			 *处理多文件上传中的表单中文件名字不一样并且下表也不一样
		*/
		public function multiUpload($files) 
		{
			$file['name'] = $files['name'][0];
			foreach($files['error'] as $k=>$v) 
			{
				if ($v)
				{
					continue;
				}
				$file['name'] = $files['name'][$k];
				$file['type'] = $files['type'][$k];
				$file['tmp_name'] = $files['tmp_name'][$k];
				$file['error'] = $files['error'][$k];
				$file['size'] = $files['size'][$k];
				if(!($new_names[$k] = $this->uploadImage($file))) 
				{
					$this->_error_infos[$k] = $this->_error_info;
					return false;
				}
			}
			return $new_names;

		}
		
		/**
		 * 生成唯一字符串
		 * @return string
		 */
		function getUniqueString()
		{
			return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X',mt_rand(0, 65535),mt_rand(0, 65535),
					mt_rand(0, 65535),mt_rand(0, 65535),mt_rand(0, 65535),mt_rand(0, 65535),mt_rand(0, 65535),mt_rand(0, 65535));
		}
		/**
		*制作缩罗图 
		*$parame $src_file string 是图片的路径
		* @pareme $dst_w int 是缩罗图允许的最大的宽
		* @pareme $dst_h int 是缩罗图允许的最大的高
		*return  string or flase 成功返回缩裸图存放的路径
		*/
		public function makeThumb($src_file,$dst_w,$dst_h,$fileName2='') {
			$src_info = getimagesize($src_file);
			$src_w = $src_info[0];
			$src_h = $src_info[1];
			$w = $src_w/$dst_w;
			$h = $src_h/$dst_h;
			if($w > $h) {
				$rel_w = $dst_w;
				$rel_h = $src_h/$w;
			} else {
				$rel_h = $dst_h;
				$rel_w = $src_w/$h;
			}
			//定义可变函数(实际上就是定义可变变量)
			
			/**
			*getiamgesize函数得到的数组格式
			*Array
				(
				[0] => 307
				[1] => 304
				[2] => 2
				[3] => width="307" height="304"
				[bits] => 8
				[channels] => 3
				[mime] => image/jpeg
				)
			*/
			$create_fuction = $this->creators[$src_info['mime']];
			$src_img = $create_fuction($src_file);
			//下边这个缩罗图画布的宽和高要跟缩罗图的大小一致 否则 会出现很框框
			$dst_img = imagecreatetruecolor($dst_w,$dst_h);
			$black_color = imagecolorallocate($dst_img,255,255,255);
			imagefill($dst_img,0,0,$black_color);
			$dst_x =  ($dst_w - $rel_w)/2;
			$dst_y = ($dst_h - $rel_h)/2;
//echo $dst_x.'-----'.$dst_y;
//exit();
			imagecopyresampled($dst_img,$src_img,$dst_x,$dst_y,0,0,$rel_w,$rel_h,$src_w,$src_h);
			//注意如果是要讲图片文件保存到文件下 而不是输出到浏览器则不能再写下边这个回应头了
			//header("Content-Type:image/jpeg");
			//$src_file = 'e:/phpenv/apache/htdocs/shop/uploads/2013032916/xxx.jpg';
			$base_name = basename($src_file);
			$dir_name = dirname($src_file);
			$sub_name = basename(dirname($src_file));
			$thumb_name = 'thumb_'.$dst_w.'_'.$dst_h.'_'.$base_name;
			//  再为图片输出定义一个可变函数
			$maker = $this->makers_img[$src_info['mime']];
			//dump($dir_name.'/'.$thumb_name);
			$maker($dst_img,$dir_name.'/'.$thumb_name);
			imagedestroy($src_img);
			imagedestroy($dst_img);
			return $sub_name.'/'.$thumb_name;

		}
		/**
		*为图片添加水印
		*&parame string $dst_file 是目标图片的位置
		*@parame string $stma_file 是印章图片的位置
		*parame int $pos 是印章图片所要放置的位置1,2,3,4, 5分别代表左上 右上，右下，坐下， 中间
		*/
		public function addStamp($dst_file,$stmp_file,$pos=1,$pct=61) {
			$dst_info = getimagesize($dst_file);
			$stmp_info = getimagesize($stmp_file);
			$dst_img = $this->creators[$dst_info['mime']]($dst_file);
			$stmp_img = $this->creators[$stmp_info['mime']]($stmp_file);
			$dst_w = $dst_info[0];
			$dst_h = $dst_info[1];
			$stmp_w = $stmp_info[0];
			$stmp_h = $stmp_info[1];
			switch($pos) {
				case 1: 
					$dst_x = 0;
					$dst_y = 0;
					break;
				case 2: 
					$dst_x = $dst_w - $stmp_w;
					$dst_y = 0;
					break;
				case 3: 
					$dst_x = $dst_w - $stmp_w;
					$dst_y = $dst_h - $stmp_h;
					break;
				case 4: 
					$dst_x = 0;
					$dst_y = $dst_h - $stmp_h;
					break;
				case 5: 
					$dst_x = ($dst_w - $stmp_w)/2;
					$dst_y = ($dst_h - $stmp_h)/2;
					break;
			}
			imagecopymerge($dst_img,$stmp_img,$dst_x,$dst_y,0,0,$stmp_w,$stmp_h,$pct);
			$maker = $this->makers_img[$dst_info['mime']];
			$maker($dst_img,$dst_file);
			imagedestroy($dst_img);
			imagedestroy($stmp_img);
		}
	}