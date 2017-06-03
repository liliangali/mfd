<?php
class KukeApp extends MemberbaseApp{
    function test(){
        if($_REQUEST['opt']){
            $rs = uploadNiukou('up_image');
            var_dump($rs);
            echo 2;exit;
        }
        $this->display(KUKE.'test.html');
    }

	function KukeApp(){
		parent::__construct();
	}

	function __construct(){
//        Lang::load(lang_file('common'));
//        Lang::load(lang_file(APP));
        $setting =& af('settings');
        Conf::load($setting->getAll());

		parent::__construct();

		$this->photo = m('userphoto');
		$this->album =  m('album');

		define("KUKE", "kuke/");
		$this->assign('kuke',KUKE);

		if(!$this->visitor->has_login && ACT != 'findpsSMSCode' &&  ACT != 'findpsEmail' && ACT != 'delAlbum'
				&& ACT !='findpsRestSMSCode' && ACT !='findpsResetEmail' &&  ACT != 'findpsSMSCode'){

			$back_url = urlencode("/index.php/kuke-".ACT.".html");
			header("location:/index.php/member-login.html?ret_url=".$back_url);
			exit;
		}

		$this->img_url = get_domain() . "/themes/mall/default/styles/default/"	;
		$this->assign('img_url',$this->img_url );

		$this->assign('act',ACT);


        $this->import_resource(array(
            'script' => array(
                array(
                    'path' => 'dialog/dialog.js',
                    'attr' => 'id="dialog_js"',
                ),
                array(
                    'path' => 'jquery.ui/jquery.ui.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.plugins/jquery.validate.ad.js',
                    'attr' => '',
                ),
            ),
            'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
        ));

		if( $this->visitor->info['phone_mob'] ){
			$hide_phone = $this->getHidePhone($this->visitor->info['phone_mob']);
			$this->assign('hide_phone',$hide_phone);
		}

		$this->_curitem('kuke');

	}
	//个人中心-首页
	function index(){
        $this->assign('title', 'RCTAILOR-酷客中心-账户概览');

		$this->assign('title_info', '账户概览');
		//账号强弱等待
		$this->accountLevel();
		//公告
		$db = &db();
		$sql = " select * from cf_article where cate_id = 3 order by add_time desc limit 5";
		$article = $db->getAll($sql);

		$this->assign('article' , $article);

		//用户浏览记录
		$cs =& cs();
		$history = $cs->get_history(6, $this->visitor->info['user_id'] );
		$this->assign('history' , $history);

		//个人街拍
		$des = $this->photo->getByCateUid(2 , $this->visitor->info['user_id']  ,' add_time desc ' ,8);
		foreach($des as $k=>$v){
			$des[$k]['url'] = getCameraUrl($v['url'],200);
			//$des[$k]['link'] = getPhotoDetailLink($v['id'], 1);
		}
		$this->assign('des' , $des);

		//取会员的订单 by lirp
        /* 获取订单列表 */
        $this->get_new_orders();

		//取基本款推荐 by lirp
        /* 猜你喜欢  */
        $cus = &m("customs");
        $cus_list  = $cus->find(array(
        				'conditions' => "cst_store > 0 AND is_active=1 AND is_rec=1",
        				'limit' => "0,10",
        				'field' => "cst_name, cst_price, cst_image, cst_id",
        			));
        $this->assign('cus_list', $cus_list);

		$this->display(KUKE.'index.html');
	}
	//我的麦富迪币
	function coin(){
// 		setCoin(200,58,'add','order_reward',$author = 'system','aaaa',$way = 'pc');
		$db = &db();

		$order = $_REQUEST['order'];
		$date = $_REQUEST['month'];
		//目前仅支持6个月以内的
		if(!$date || $date >6 || $date < 1)
			$date = 1;

		$month = getMonth($date);


		$cate = $_REQUEST['cate'];
		$where = " uid = ".$_SESSION['user_info']['user_id'];
		if($cate){
			if('order' == $cate){
				$where .= " and type = '$cate'";
			}elseif('sheji' == $cate){
				$where .= " and type = 'like' ";
			}elseif('jiepai' == $cate){
				$where .= " and type = 'comment' ";
			}
		}

        $this->assign('cate',$cate);

		$sql = "select count(id) as total from cf_coin_log where add_time > $month and $where";
		$coin_cnt = $db->getRow($sql);

		if($coin_cnt['total']){
			$page = $_REQUEST['page'];
			if(!$page )
				$page = 1;


			include 'includes/libraries/page.lib.php';
			$page = new Page($coin_cnt['total'],10,$page);
			$page->execPage();
			$sql = "select * from cf_coin_log where add_time > $month and $where limit ".$page->mLimit[0]." , " .$page->mLimit[1];
			$coin = $db->getAll($sql);
			foreach($coin as $k=>$v){
				$coin[$k]['date'] = date("Y-m-d H:i:s",$v['add_time']);
				$coin[$k]['desc'] = checkCate($v['cate']);

			}
		}
		$this->assign('title', 'RCTAILOR-酷客中心-我的麦富迪币');
		$this->assign('title_info', '我的麦富迪币');
		$this->assign('coin',  $coin );
		$this->assign('month',  $date );
		$this->assign('total',  $page->mTotalDataNum );
		$this->assign('page_show',  $page->mShowHTML );


		$this->display(KUKE.'coin.html');
	}
	//我的积分
	function point(){
// 		setPoint(200,68,'add','dingzhi_xiangxi_comment',$author = 'system','bbbb',$way = 'pc');

		$this->assign('title','RCTAILOR-酷客中心-我的积分');
		$this->assign('title_info','我的积分');
		$db = &db();

		$order = $_REQUEST['order'];
		$date = $_REQUEST['month'];

		if(!$date || $date >6 || $date < 1)
			$date = 1;

		$month = getMonth($date);


		$cate = $_REQUEST['cate'];
		$where = " uid = ".$_SESSION['user_info']['user_id'];
		if($cate){
			if('order' == $cate){
				$where .= " and type = '$cate'";
			}elseif('hudong' == $cate){
				$where .= " and type = 'like' ";
			}elseif('comment' == $cate){
				$where .= " and type = 'comment' ";
			}
		}

        $this->assign('cate',$cate);

		$sql = "select count(id) as total from cf_point_log where add_time > $month and  $where  ";
		$coin_cnt = $db->getRow($sql);
		if($coin_cnt['total']){
			$page = $_REQUEST['page'];
			if(!$page )
				$page = 1;

			include 'includes/libraries/page.lib.php';
			$page = new Page($coin_cnt['total'],10,$page);
			$page->moduleSymbol = "/index.php/kuke-point.html";
			$page->execPage();
			$sql = "select * from cf_point_log where add_time > $month and $where order by add_time desc limit ".$page->mLimit[0]." , " .$page->mLimit[1];
            //echo $sql;
			$coin = $db->getAll($sql);
			foreach($coin as $k=>$v){
				$coin[$k]['date'] = date("Y-m-d H:i:s",$v['add_time']);
				$coin[$k]['desc'] = checkCate($v['cate']);
			}
		}
		//$this->assign('title', '我的麦富迪币');
		$this->assign('coin',  $coin );
		$this->assign('month',  $date );
		$this->assign('total',  $page->mTotalDataNum );
		$this->assign('page_show',  $page->mShowHTML );

		$this->display(KUKE.'point.html');
	}
	//资料管理-个人资料
	function userinfo(){
		$this->assign('title','RCTAILOR-酷客中心-个人资料');
		$this->assign('title_info','个人资料');
		$db = &db();
		$opt = $_REQUEST['opt'];
		if($opt){
			$year = $_REQUEST['year'];
			$month = $_REQUEST['month'];
			$day = $_REQUEST['day'];

			$nickname = $_REQUEST['nickname'];
			$signature = $_REQUEST['signature'];
			$memo = $_REQUEST['memo'];

			$province = $_REQUEST['province'];
			$city = $_REQUEST['city'];

			$gender =$_REQUEST['gender'];
			$real_name =$_REQUEST['real_name'];

			$zone_img = $_REQUEST['zone_img'];

			$data = array();

			$birthday = $year . "-" . $month  ."-" .$day;
			$rule = "/^(1|2)[0-9]{3}-[0-9]{2}-[0-9]{2}$/";
			if( preg_match($rule,$birthday)===1 ){
				$data['birthday'] = $birthday;
				$_SESSION['user_info']['birthday'] = $birthday;
			}

			if($province && $city){
				$data['province'] = $province;
				$data['city'] = $city;

				$_SESSION['user_info']['province'] = $province;
				$_SESSION['user_info']['city'] = $city;

			}

			if($zone_img){
				$data['zone_img'] = $zone_img;
				$_SESSION['user_info']['zone_img'] = $zone_img;
			}

			if($nickname){
				if(!$_SESSION['user_info']['nickname']){
					echo 2;
					$num = pointTurnNum('proecss_profile');
					setPoint($_SESSION['user_info']['user_id'], $num, 'add', 'proecss_profile','system','完善资料就送积分哟 ！');
				}
				$data['nickname'] = $nickname;
				$_SESSION['user_info']['nickname'] = $nickname;
			}
			if($signature){
				$data['signature'] = $signature;
				$_SESSION['user_info']['signature'] = $signature;
			}
			if($memo){
				$data['memo'] = $memo;
				$_SESSION['user_info']['memo'] = $memo;
			}
			if($gender){
				$data['gender'] = $gender;
				$_SESSION['user_info']['gender'] = $gender;
			}
			if($real_name){
				$data['real_name'] = $real_name;
				$_SESSION['user_info']['real_name'] = $real_name;
			}

			if($data){
				$m = m('member');
				$m->edit($this->visitor->info['user_id'] , $data);
			}



			$this->msg( '修改成功',0 ,"/index.php/kuke-userinfo.html");
		}

		if($this->visitor->info['birthday']){
			$birthday = explode("-", $this->visitor->info['birthday']);
			$year = $birthday[0];
			$month = $birthday[1];
			$day = $birthday[2];
		}
		$year_op = "";
		for($i=1960;$i<=2000;$i++){
			if($year == $i)
				$year_op .= "<option value=$i selected>$i</option>";
			else
				$year_op .= "<option value=$i>$i</option>";
		}

		$month_op = "";
		for($i=1;$i<=12;$i++){
			if($i < 10)
				$str = "0". $i;
			else
				$str = $i;

			if($month == $str)
				$month_op .= "<option value=$str selected>$str</option>";
			else
				$month_op .= "<option value=$str>$str</option>";
		}

		$day_op = "";
		for($i=1;$i<=31;$i++){
			if($i < 10)
				$str = "0". $i;
			else
				$str = $i;

			if($day == $str)
				$day_op .= "<option value=$str selected>$str</option>";
			else
				$day_op .= "<option value=$str>$str</option>";
		}
		$sql = "select * from cf_region where parent_id = 2";
		$province_db = $db->getAll($sql);
		$province_op = "";
		foreach($province_db as $k=>$v){
			if($this->visitor->info['province'] && $this->visitor->info['province'] == $v['region_id'])
				$selected = " selected ";
			else
				$selected = '';

			$province_op .="<option value='{$v['region_id']}' $selected>{$v['region_name']}</option>";
		}

		if($this->visitor->info['city']){
			$sql = "select * from cf_region where region_id = ".$this->visitor->info['city'];
			$city = $db->getRow($sql);
			$city = "<option value='{$city['region_id']}'>".$city['region_name']."</option>";
		}else{
			$city = "<option value=''>请选择</option>";
		}

		$this->assign('province_op',$province_op);
		$this->assign('year',$year_op);
		$this->assign('city',$city);
		$this->assign('month',$month_op);
		$this->assign('day',$day_op);


		$this->assign('msg', '修改成功');
		$this->display(KUKE.'userinfo.html');
	}
	//头像上传
	function avatar(){
		$this->assign('title','头像上传');

		$this->assign('title', '头像上传');
		$this->_curitem('kuke');
		$this->display(KUKE.'avatar.html');
	}
	//头像图片上传-非ACTION
	function avatarupload(){
		$this->assign('title', '头像图片上传');

		$src=base64_decode($_POST['pic']);
		$pic1=base64_decode($_POST['pic1']);
		$pic2=base64_decode($_POST['pic2']);
		$pic3=base64_decode($_POST['pic3']);


		$uid = $this->visitor->info['user_id'];

		$path = $_SERVER['DOCUMENT_ROOT']."/upload_user_photo/avatar/";
		$pic1_name = $path."original/".$uid.".jpg";
		$pic2_name = $path."162/".$uid.".jpg";
		$pic3_name = $path."48/".$uid.".jpg";
		$pic4_name = $path."20/".$uid.".jpg";

		$m = m('member');

		$url = $uid.".jpg";
		$m->edit( $uid , array('avatar'=>$url ) );



		file_put_contents($pic1_name,$src);
		file_put_contents($pic2_name,$pic1);
		file_put_contents($pic3_name,$pic2);
		file_put_contents($pic4_name,$pic3);

		$rs['status'] = 1;

 		$url = get_domain() . "/upload_user_photo/avatar/162/".$uid.".jpg";
		$_SESSION['user_info']['avatar'] = $url;

		print json_encode($rs);
	}
	//安全设置
	function safeset(){
		$this->assign('title','RCTAILOR-酷客中心-安全设置');
		$this->assign('title_info','安全设置');
		$this->display(KUKE.'safeset.html');
	}
	//我的优惠卷
	function coupon(){
// 		addCoupon(1, 200);

		$this->assign('title','RCTAILOR-酷客中心-我的优惠卷');
		$this->assign('title_info','我的优惠卷');
		$db = &db();

		$status = $_REQUEST['status'];
		if(!$status || $status >3 )
			$status = 0;

		$sql = "select count(*) as total from cf_coupon_sn where status = $status and claim = 1 and uid = ".$this->visitor->info['user_id'];
		$coupon_cnt = $db->getRow($sql);
		if($coupon_cnt['total']){
			include 'includes/libraries/page.lib.php';
			$page = new Page($coupon_cnt['total'],2);
			$page->execPage();

			//用接口调取数据//把下面的注释了//Ruesin
			$limit=$page->mLimit[0]." , " .$page->mLimit[1];
			$fCpn=& f('coupon');
			$coupon=$fCpn->getUserCouponList($this->visitor->info['user_id'],$status,1,$limit,'cpn');

			/*
			$sql = "select * from cf_coupon_sn where status = $status and claim = 1 and uid = ".$this->visitor->info['user_id'] . " limit ".$page->mLimit[0]." , " .$page->mLimit[1];
			$coupon = $db->getAll($sql);
			foreach($coupon as $k=>$v){//foreach 循环查询?
				$coupon[$k]['date'] = date("Y-m-d H:i:s",$v['add_time']);
 				$coupon[$k]['info'] = getCouponById($v['cpn_id']);
			}
			*/
		}

		$this->assign('coupon',  $coupon );
		$this->assign('status',  $status );
		$this->assign('total',  $page->mTotalDataNum );
		$this->assign('page_show',  $page->mShowHTML );

		$this->assign('title', '我的优惠卷');
		$this->display(KUKE.'coupon.html');
	}
	//ajax-街拍上传图片-非ACTION
	function upload(){
		$this->assign('title','街拍上传');

		$num = $_REQUEST['num'];

		$dir = $_SERVER['DOCUMENT_ROOT'].'/upload_user_photo/jiepai/';
		$fileName = $this->visitor->info['user_id'] ."_" . $num . "_" .md5( uniqid() . mt_rand(0,255) ) . ".jpg";


		$fileDirName2 = $dir ."235x315/" . $fileName;
		pro_img('up_file',235,315,$fileDirName2);
		$fileDirName3 = $dir ."520x685/" . $fileName;
		pro_img('up_file',520,685,$fileDirName3);

		$fileDirName1 = $dir ."original/" . $fileName;
		$rs = move_uploaded_file($_FILES['up_file']["tmp_name"],$fileDirName1);


		$src= get_domain() . "/upload_user_photo/jiepai/235x315/".$fileName;
		$arr = array('src'=>$src,'file'=>$fileName);
		echo json_encode($arr);
		exit;
	}
	//个人设计-基本信息
	function upload_baseinfo(){
		$dir = $_SERVER['DOCUMENT_ROOT'].'/upload_user_photo/sheji_base/original/';

		$inputName='filedata';
		$upfile=@$_FILES[$inputName];
// 		var_dump($upfile);
		$fileName = $this->visitor->info['user_id'] ."_" .md5( uniqid() . mt_rand(0,255) ) . ".jpg";
		$path = $dir . $fileName;
		//echo $page;
		$rs = file_put_contents($path,file_get_contents("php://input"));
// 		var_dump($rs);
		//move_uploaded_file($upfile['tmp_name'], $path);
		$src= get_domain() . "/upload_user_photo/sheji_base/original/".$fileName;

// 		$localName=$upfile['name'];
		$arr = array('err'=>"",'msg'=>$src,'localname'=>$src,'id'=>1);
		echo json_encode($arr);
		//{'err':'','msg':{'url':'upload\/day_140718\/201407181142275513.jpg','localname':'1.jpg','id':'1'}}
	}
	//ajax-用户个人空间-头部-上传图片-非ACTION
	function zone_upload(){
		$src = uploadZone('up_file');
		echo json_encode($src);
	}
	//酷吧上传-非ACTION
	function upload_kuba(){
		$this->assign('title','酷吧上传');

		$dir = $_SERVER['DOCUMENT_ROOT'].'/upload_user_photo/sheji/';
		$fileName = $this->visitor->info['user_id'] ."_" . md5( uniqid() . mt_rand(0,255) ) . ".jpg";


		$fileDirName2 = $dir ."235x315/" . $fileName;
		pro_img('up_file',235,315,$fileDirName2);
		$fileDirName3 = $dir ."520x685/" . $fileName;
		pro_img('up_file',520,685,$fileDirName3);

		$fileDirName1 = $dir ."original/" . $fileName;
		$rs = move_uploaded_file($_FILES['up_file']["tmp_name"],$fileDirName1);


		$src= get_domain() . "/upload_user_photo/sheji/235x315/".$fileName;
		$arr = array('src'=>$src,'file'=>$fileName);
		echo json_encode($arr);
		exit;
	}
	//删除个人设计图片
	function delcamera(){
		$id = $_REQUEST['id'];
		if(!$id)
			return 0;

		delUserPhoto($id);

		$back = $_SERVER['HTTP_REFERER'];
		header('location:'.$back);
		exit;
	}
	//个人设计列表
	function design(){
		$db = &db();
		$page_num = $_REQUEST['page'];
		$pic_db  = $this->photo->pageByCateUid($page_num,12,1,$this->visitor->info['user_id'],' add_time desc ');
		if($pic_db){
			$page = $pic_db['page'];
			$pic = $pic_db['data'];
			if($pic)
				foreach($pic as $k=>$v){
					$pic[$k]['url'] = getDesignUrl($v['url'],200);
					if($v['album_id']){
						$pic[$k]['album'] = $this->album->getById($v['album_id']);
					}

				}
		}
		$album = $this->album->getByUidCate($this->visitor->info['user_id'],1);
		$this->assign('total',  $page->mTotalDataNum );
		$this->assign('page_show',  $page->mShowHTML );

		$this->assign('pic',$pic);
		$this->assign('album',$album);
		$this->assign('title', 'RCTAILOR-酷客中心-我的设计');
		$this->assign('title_info', '我的设计');
		$this->display(KUKE.'design.html');
	}


	//个人设计 上传
	function designup(){
		$db = &db();
		$opt = $_REQUEST['opt'];
		if($opt){
			$title = $_REQUEST['title'];
			$base_info = $_REQUEST['base_info'];
			$fileName = $_REQUEST['fileName'];

			if(!$title)
				$this->msg('标题不能为空哟');

			$m = m("userphoto");
			$data = array(
					'add_time'=>time(),
					'url'=>$fileName,
					'cate'=>1,
					'base_info'=>$base_info,
					'uid'=>$this->visitor->info['user_id'],
					'title'=>$title,
					'status'=>1,
			);

			$num = pointTurnNum("sheji_reward");
			setPoint($this->visitor->info['user_id'], $num, 'add', "sheji_reward");

			$rs  =$m->add($data);

			$m = m('member');
			$m->setInc(" user_id = ".$this->visitor->info['user_id'] , 'pic_num');
			$this->msg('上传成功',0,"/index.php/kuke-designup.html");
		}

		$this->assign('title', 'RCTAILOR-酷客中心-设计上传');
		$this->assign('title_info', '设计上传');
		$this->display(KUKE.'designup.html');
	}
	function msg($msg,$err = 0,$back = ''){
		if(!$back)
			$back = $_SERVER['HTTP_REFERER'];

		$this->assign('back',$back);
		$this->assign('err',$err);
		$this->assign('msg',$msg);
		$this->display(KUKE.'msg.html');
		exit;
	}
	//编辑-街拍-相册
	function albumedit(){
		$this->assign('title','街拍相册编辑');

		$id = $_REQUEST['id'];
		$albcum_id = $id;
		if(!$id)
			$this->msg('album_id is null',1);

		$album = $this->album->getById($id);
		if(!$album)
			$this->msg('album_id not is db...',1);

        $this->assign('album', $album);
		$db = &db();

		$opt =  $_REQUEST['opt'];
		if($opt){
			//先清空这个相册下的所有图片
			$this->photo->delByAlbumId($albcum_id);

			$is_top = 0;
			$img_total = $_REQUEST['total'];
			$title = $_REQUEST['title'];
			$cate = 2;
			$desc = $_REQUEST['description'];

			$data = array(
					'title'=>$title,
					'description'=>$desc,
			);
			//封面图地址
			$top_url = $_REQUEST['top_url'];
			if($top_url){
				$data['top_url'] = $top_url;
				$is_top = 1;
			}

			$up_album = $this->album->edit($id,$data);

			$j=0;
			for($i=0;$i<=$img_total;$i++){
				$img = $_REQUEST['input_'.$i];
				if($img){
					$data = array(
							'add_time'=>time(),
							'url'=>$img,
							'uid'=>$this->visitor->info['user_id'],
							'cate'=>$cate,
							'album_id'=>$albcum_id,
							'status'=>1,
					);
					$this->photo->add($data);

					if(!$is_top){
						$new_data= array(   'top_url' => $img );
						$this->album->edit($albcum_id,$new_data);
						$is_top = 1;
					}
					$j++;
				}
			}
			$m = m('member');
			$rs = $m->setInc(" user_id = {$this->visitor->info['user_id']} " , 'pic_num',$j);

			$sql = " update cf_album set pic_num = $j where id = ".$albcum_id;
			$rs = $db->query($sql);
// 			$id++;

			$this->msg('编辑成功',0,'/index.php/kuke-albumedit.html?id='.$id);
		}


		$photo = $this->photo->getByAlbumId($id);
		$top_url_key = 0;
		if($photo){
			foreach($photo as $k=>$v){
				if($album['top_url'] == $v['url'])
					$top_url_key = $k;

				$photo[$k]['img_url'] = getCameraUrl($v['url'],'200');
			}

			$this->assign('top_url_key', $top_url_key);

		}

		$this->assign('id', $id);
		$this->assign('photo', $photo);
		$this->assign('total', count($photo) - 1);
		$this->assign('total_inc', count($photo) );
		$this->display(KUKE.'albumedit.html');
	}
	//街拍相册
	function album(){
		$this->assign('title','RCTAILOR-酷客中心-街拍相册');
		$this->assign('title_info','街拍相册');
		$db = &db();

		$page_num = $_REQUEST['page'];
		if(!$page_num)
			$page_num = 1;
		$pic_db  = $this->album->pageByCateUid($page_num,12,2,$this->visitor->info['user_id'],' add_time desc ');
		if($pic_db){
			$page = $pic_db['page'];
			$album = $pic_db['data'];
		}

		$this->assign('total',  $page->mTotalDataNum );
		$this->assign('page_show',  $page->mShowHTML );
		$this->assign('album', $album);

		$this->display(KUKE.'album.html');
	}

	//街拍列表
	function street(){
		$id = $_REQUEST['id'];
		if(!$id)
			$this->msg('id is null');

		$album = $this->album->getById($id);
		if(!$album)
			$this->msg('album_id not in db...');


		$db = &db();
		$page_num = $_REQUEST['page'];
		if(!$page_num)
			$page_num = 1;

		$pic_db  = $this->photo->pageByCateUid($page_num,12,2,$this->visitor->info['user_id'],' add_time desc ' , $id);
		if($pic_db){
			$page = $pic_db['page'];
			$pic = $pic_db['data'];
			if($pic)
			foreach($pic as $k=>$v){
				$pic[$k]['url'] = getCameraUrl($v['url'],200);
			}
		}

		$album = $this->album->getByUidCate($this->visitor->info['user_id'],2);
		$this->assign('album',$album);

		$this->assign('total',  $page->mTotalDataNum );
		$this->assign('page_show',  $page->mShowHTML );
		$this->assign('pic',$pic);

		$this->assign('title', 'RCTAILOR-酷客中心-我的街拍');
		$this->assign('title_info', '我的设计');
		$this->display(KUKE.'street.html');
	}
	//删除街拍相册
	function delAlbum(){
		$id = $_REQUEST['id'];
		if(!$id)
			return -1;
		$db = &db();

		$rs = $this->album->delById($id);


		echo $rs;
	}
	//将单张个人设计图片加入到相册中
	function joinAlbum(){
		$db = &db();

		$album_id = $_REQUEST['album_id'];
		$photo_id = $_REQUEST['photo_id'];
		if(!$album_id)
			return 0;

		if(!$photo_id)
			return 0;

		$album = $this->album->getById($album_id);
		$photo =  $this->photo->getById($photo_id);

		$m = m('userphoto');
		$data = array(
				'album_id'=>$album_id,
		);

		$m->edit($photo_id,$data);

		$m = m('album');
		$rs = $m->setInc(" id = $album_id " , 'pic_num');

		if(!$album['top_url']){
			$data = array();
			$data   ['top_url'] = $photo['url'];
			$m->edit($album_id,$data);
		}

		echo $rs;
	}
	//创建一个 酷吧相册
	function creaalbum(){
		$name = $_REQUEST['album_name'];
		$m = m('album');
		$data = array(
				'cate'=>1,
				'add_time'=>time(),
				'title'=>$name,
				'uid'=>$this->visitor->info['user_id']
		);
		$id = $m->add($data);
		echo $id;
	}

	function cameraedit(){
		$this->assign('title', '图片编辑');

		$id = $_REQUEST['id'];
		$db = &db();
		$row = $this->photo->getById($id);


		$opt = $_REQUEST['opt'];
		if($opt){
			$m = m('userphoto');

			$title = $_REQUEST['title_0'];
			$cate = $_REQUEST['cate_0'];
			$desc = $_REQUEST['desc_0'];
			$img = $_REQUEST['img0'];
			$data = array(
					'add_time'=>time(),
					'url'=>$img,
					'title'=>$title,
					'cate'=>$cate,
					'description'=>$desc,
			);

			$m->edit($id,$data);

			$this->msg('编辑成功',1,"/index.php/kuke-cameraedit.html?id=".$id);
		}


		$this->assign('row',$row);
		$this->display(KUKE.'camera_edit.html');
	}
	//街拍上传
	function camera(){
		$this->assign('title','RCTAILOR-酷客中心-街拍上传');
		$this->assign('title_info','街拍上传');

		$opt = $_REQUEST['opt'];
		if($opt){
			$m = m('userphoto');
			$img_total = $_REQUEST['total'];
			$title = $_REQUEST['title'];
			$cate = 2;
			$desc = $_REQUEST['description'];

			$m_album = m('album');
			$data = array(
					'add_time'=>time(),
					'title'=>$title,
					'description'=>$desc,
					'uid'=>$this->visitor->info['user_id'],
					'cate'=>$cate,
			);
			//封面图地址
            $is_top = 0;
			$top_url = $_REQUEST['top_url'];
			if($top_url){
                $is_top = 1;
				$data['top_url'] = $top_url;
			}
			$album_id = $m_album->add($data);

			$m = m('userphoto');

			$j=0;
			for($i=0;$i<=$img_total;$i++){
				$img = $_REQUEST['input_'.$i];
				if($img){
					$data = array(
							'add_time'=>time(),
							'url'=>$img,
							'uid'=>$this->visitor->info['user_id'],
							'cate'=>$cate,
							'album_id'=>$album_id,
							'status'=>1,
					);
					$m->add($data);

					$num = pointTurnNum("jiepai_reward");
					setPoint($this->visitor->info['user_id'], $num, 'add', "jiepai_reward");


					if(!$is_top){
						$new_data = array('top_url'=>$img);
						$m_album->edit($album_id,$new_data);
						$is_top = 1;
					}
					$j++;
				}
			}
// 			var_dump($j);
			$m = m('member');
			$rs = $m->setInc(" user_id = {$this->visitor->info['user_id']} " , 'pic_num',$j);
// 			var_dump($rs);
			$m = m('album');
			$rs = $m->setInc(" id = $album_id " , 'pic_num',$j);
// 			var_dump($rs);

			$this->msg( '添加成功',0,"/index.php/kuke-camera.html");
		}else{
			$this->display(KUKE.'camera.html');
		}
	}
//----------------------安全设置---------------------------------------
	//绑定邮箱
	function bindemail(){
		$db = &db();

            $this->assign('title', '绑定邮箱');

            if($this->visitor->info['email'])
                $this->msg('您已绑定过邮箱，无须重复绑定',1,"/index.php/kuke-bindemail.html");

            $opt = $_REQUEST['opt'];
            if($opt){
                $email = $_REQUEST['email'];

                $email_href = getEmailHref($email);
                $this->assign('a_email', $email_href );


                if(!$email)
                    $this->msg('邮箱不能为空',1,"/index.php/kuke-bindemail.html");

                $sql = "select * from cf_member where email ='$email' or user_name = '$email'";
                $member = $db->getRow($sql);
                if($member)
                    $this->msg('此邮箱已经其它人占用...',1,"/index.php/kuke-bindemail.html");


			    $this->assign('email', $email);

			if($opt == 2){

				$rs = $this->emailcode($email, 'bind', 'email','pc');
				if($rs['err'])
					$this->msg($rs['msg'],1,"/index.php/kuke-bindemail.html");

				$this->display(KUKE.'bindemail2.html');
			}else{


				$code = $_REQUEST['code'];
				if(!$code)
					$this->msg('验证码为空',1,"/index.php/kuke-bindemail.html");

				if(!$email)
					$this->msg('邮箱号码为空',1,"/index.php/kuke-bindemail.html");

				$rs = authemail($email,$code);
				if(!$rs)
					$this->msg('邮箱验证码错误',1,"/index.php/kuke-bindemail.html");

				$sql = "select * from cf_email_code where category='bind' and type='email' and email = '$email'";
				$sms = $db->getRow($sql);
				if(!$sms)
					$this->msg('并未发送邮件给您...',1,"/index.php/kuke-bindemail.html");

				//if($sms['fail_time'] < time())
				//	$this->msg("邮件已失效...",1,"/index.php/kuke-bindemail.html");

				$sql = "update cf_member set email='".$email."' where user_id = {$this->visitor->info['user_id']}";
				$db->query($sql);


				$where = " category = 'bind' and type = 'email' and email = '$email'";
				$db->query("delete from cf_email_code where $where");

				$_SESSION['user_info']['email'] = $email;

				$this->display(KUKE.'bindemail3.html');
			}
		}else{
			$this->display(KUKE.'bindemail.html');
		}
	}
	//修改邮箱
	function upemail(){
		$db = &db();

		$this->assign('title', '修改邮箱');

		if( ! $this->visitor->info['email'] )
			$this->msg("您还未绑定邮箱，请先绑定邮箱...",1,"/index.php/kuke-upemail.html");

		$opt = $_REQUEST['opt'];
		if($opt){
			if($opt == 2){
				$code = $_REQUEST['authcode'];
				$category = "email";
				$type = 'up';
				if(!$code)
					$this->msg('验证码为空',1,"/index.php/kuke-upemail.html");

				$rs = $this->authcode($code, $category, $type, 'pc');
				if($rs['err'])
					$this->msg($rs['msg'],1,"/index.php/kuke-upemail.html");

				$this->assign('authcode',$code);

				$this->display(KUKE.'upemail2.html');
			}elseif($opt == 3){
				$code = $_REQUEST['authcode'];
				$category = "email";
				$type = 'up';
				if(!$code)
					$this->msg('验证码为空',1,"/index.php/kuke-upemail.html");

				$rs = $this->authcode($code, $category, $type, 'pc');
				if($rs['err'])
					$this->msg($rs['msg'],1,"/index.php/kuke-upemail.html");

				$this->assign('authcode',$code);


				$email = $_REQUEST['email'];
                $email_href = getEmailHref($email);
                $this->assign('a_email', $email_href );

				$sql = "select * from cf_member where email ='$email' or user_name = '$email'";
				$member = $db->getRow($sql);
				if($member)
					$this->msg("此邮箱已经其它人占用...",1,"/index.php/kuke-upemail.html");

				$where = " category = '$category' and type = '$type' and email = '$email' ";
				$this->sendEmail($email,'email','up',$where);

				$this->display(KUKE.'upemail3.html');
			}elseif($opt == 4){
				$email = $_REQUEST['email'];
				$sql = "select * from cf_member where email ='$email' or user_name = '$email'";
				$member = $db->getRow($sql);
				if($member)
					$this->msg('此邮箱已经其它人占用...',1,"/index.php/kuke-upemail.html");



				$code = $_REQUEST['code'];
				if(!$code)
					$this->msg('验证码为空',1,"/index.php/kuke-upemail.html");

				if(!$email)
					$this->msg('邮箱号码为空',1,"/index.php/kuke-upemail.html");

				$rs = authemail($email,$code);
				if(!$rs)
					$this->msg('邮箱验证码错误',1,"/index.php/kuke-upemail.html");

				$sql = "select * from cf_email_code where category='email' and type='up' and email = '$email'";
				$sms = $db->getRow($sql);
				if(!$sms)
					$this->msg('并未发送邮件给您...',1,"/index.php/kuke-upemail.html");

				//if($sms['fail_time'] < time())
				//	$this->msg("邮件已失效...");

				$sql = "update cf_member set email='".$email."' where user_id = {$this->visitor->info['user_id']}";
				$db->query($sql);


				$where = " category = 'bind' and type = 'email' and email = '$email'";
				$db->query("delete from cf_email_code where $where");

				$_SESSION['user_info']['email'] = $email;


				$this->display(KUKE.'upemail4.html');
			}
		}else{
			$this->display(KUKE.'upemail.html');
		}
	}
	//绑定手机
	function bindphone(){
		$this->assign('title', '绑定手机');
		$opt = $_REQUEST['opt'];
		if($opt){
			$phone = $_REQUEST['phone'];
			if(!$phone)
				$this->msg('手机号码不能为空');

			$this->assign('phone',$phone);

			if($opt == 2){
				$rs = $this->SMSCode($phone, 'bind', 'phone','pc');
				if($rs['err'])
					$this->msg($rs['msg'],1,"/index.php/kuke-bindphone.html");

				$this->assign('sec',$rs['msg']);
				$this->display(KUKE.'bindphone2.html');
			}else{
				$code = $_REQUEST['authcode'];
				if(!$code){
					$this->msg('验证码为空',1,"/index.php/kuke-bindphone.html");
				}

				$category = 'bind';
				$type = 'phone';

				$rs = $this->authcode($code,$category,$type,'pc');
				if($rs['err'])
					$this->msg($rs['msg']);


				$m = m('member');
				$m->edit( $this->visitor->info['user_id'],array('phone_mob'=>$phone ) );

				$db = &db();
				$where = " category = '$category' and type = '$type' and uid = " . $this->visitor->info['user_id'];
				$db->query("delete from cf_sms_code where $where");

				$_SESSION['user_info']['phone_mob'] = $phone;

				$this->display(KUKE.'bindphone3.html');
			}
		}else{
			$this->display(KUKE.'bindphone.html');
		}
	}
	//修改密码
	function upps(){
		$this->assign('title', '修改密码');


		$opt = $_REQUEST['opt'];
		if($opt){
			$code = $_REQUEST['authcode'];
			$category = "up";
			$type = 'ps';
			if(!$code)
				$this->msg('验证码为空',1,"/index.php/kuke-upps.html");

			$rs = $this->authcode($code, $category, $type, 'pc');
			if($rs['err'])
				$this->msg($rs['msg']);

			$this->assign('authcode',$code);

			if($opt == 2){
				$this->display(KUKE.'upps2.html');
			}else{
				$ps = $_REQUEST['ps'];
				$ps_sure = $_REQUEST['ps_sure'];

				if(!$ps)
					$this->msg('密码不能为空',1,"/index.php/kuke-upps.html");

				if(!$ps_sure)
					$this->msg('确认密码不能为空',1,"/index.php/kuke-upps.html");

				if($ps != $ps_sure)
					$this->msg('两次密码不一致',1,"/index.php/kuke-upps.html");

				$m = m('member');
				$m->edit( $this->visitor->info['user_id'],array('password'=>md5($ps) ) );

				$db = &db();
				$where = " category = '$category' and type = '$type' and uid = " . $this->visitor->info['user_id'];
				$db->query("delete from cf_sms_code where $where");

				$info = $this->visitor->info;
				$info['password'] =  md5($ps);
				$this->visitor->assign($info);

				$this->displayKUKE.(KUKE.'upps3.html');
			}
		}else{
			$this->display(KUKE.'upps.html');
		}
	}
	//修改手机
	function upphone(){
		$this->assign('title', '修改手机');

		$opt = $_REQUEST['opt'];
		if($opt){
			$code = $_REQUEST['authcode'];
			if(!$code)
				$this->msg('验证码为空',1,"/index.php/kuke-upphone.html");

			$category = 'phone';
			$type = 'up';
			$this->authcode($code,$category,$type,'pc');

			if($opt == 2){
				$this->display(KUKE.'upphone2.html');
			}else{
				$phone = $_REQUEST['phone'];
				if(!$phone)
					$this->msg('手机号为空',1,"/index.php/kuke-upphone.html");

				$this->assign('phone', $phone);


				$code = $_REQUEST['authcode'];
				if(!$code)
					$this->outinfo('验证码为空',1);

				$category = 'phone';
				$type_new = 'up-new';
				$this->authcode($code,$category,$type_new,'pc');


				$m = m('member');
				$m->edit( $this->visitor->info['user_id'],array('phone_mob'=>$phone ) );

				$db = &db();
				$where = " category = '$category' and type = '$type' and uid = " . $this->visitor->info['user_id'];
				$db->query("delete from cf_sms_code where $where");

				$where = " category = '$category' and type = '$type_new' and uid = " . $this->visitor->info['user_id'];
				$db->query("delete from cf_sms_code where $where");

				$_SESSION['user_info']['phone_mob'] = $phone;

				$this->display(KUKE.'upphone3.html');
			}
		}else{
			$this->display(KUKE.'upphone.html');
		}
	}
	//设置支付密码
	function setpayps(){
		$this->assign('title', '设置支付密码');

		$opt = $_REQUEST['opt'];
		if($opt){
			$code = $_REQUEST['authcode'];
			if(!$code){
				$this->msg('验证码为空');
			}

			$category = 'payps';
			$type = 'set';
			$rs = $this->authcode($code,$category,$type,'pc');
			if($rs['err'])
				$this->show_warning("222222222");

			$this->assign('authcode',$code);
			if($opt == 2){
				$this->display(KUKE.'setpayps2.html');
			}else{
				$ps = $_REQUEST['ps'];
				$ps_sure = $_REQUEST['ps_sure'];

				if(!$ps)
					$this->msg('密码不能为空');

				if(!$ps_sure)
					$this->msg('确认密码不能为空');

				if($ps != $ps_sure)
					$this->msg('两次密码不一致');

				$m = m('member');
				$m->edit( $this->visitor->info['user_id'],array('pay_ps'=>md5($ps) ) );

				$db = &db();
				$where = " category = '$category' and type = '$type' and uid = " . $this->visitor->info['user_id'];
				$db->query("delete from cf_sms_code where $where");

				$info = $this->visitor->info;
				$info['pay_ps'] =  md5($ps);
				$this->visitor->assign($info);

				$this->display(KUKE.'setpayps3.html');
			}
		}else{
			$this->display(KUKE.'setpayps.html');
		}
	}
	//修改支付密码
	function uppayps(){
		$this->assign('title', '修改支付密码');
		if( ! $this->visitor->info['phone_mob'] )
			$this->msg('用户未绑定手机',1,"/index.php/kuke-uppayps.html");

		$opt = $_REQUEST['opt'];
		if($opt){
			$code = $_REQUEST['authcode'];
			$category = "payps";
			$type = 'up';
			if(!$code)
				$this->msg('验证码为空',1,"/index.php/kuke-uppayps.html");

			$rs = $this->authcode($code, $category, $type, 'pc');
			if($rs['err'])
				$this->msg($rs['msg']);

			$this->assign('authcode',$code);

			if($opt == 2){
				$this->display(KUKE.'uppayps2.html');
			}else{
				$ps = $_REQUEST['ps'];
				$ps_sure = $_REQUEST['ps_sure'];

				if(!$ps)
					$this->msg('密码不能为空',1,"/index.php/kuke-uppayps.html");

				if(!$ps_sure)
					$this->msg('确认密码不能为空',1,"/index.php/kuke-uppayps.html");

				if($ps != $ps_sure)
					$this->msg('两次密码不一致',1,"/index.php/kuke-uppayps.html");

				$m = m('member');
				$m->edit( $this->visitor->info['user_id'],array('pay_ps'=>md5($ps) ) );

				$db = &db();
				$where = " category = '$category' and type = '$type' and uid = " . $this->visitor->info['user_id'];
				$db->query("delete from cf_sms_code where $where");

				$info = $this->visitor->info;
				$info['pay_ps'] =  md5($ps);
				$this->visitor->assign($info);

				$this->display(KUKE.'uppayps3.html');
			}
		}else{
			$this->display(KUKE.'uppayps.html');
		}
	}
//----------------------安全设置---------------------------------------
	function authcode($code,$category,$type,$way){

		$db = &db();
		$where = " category = '$category' and type = '$type' and uid = " . $this->visitor->info['user_id'];
		$sql = "select * from cf_sms_code where $where ";
		$sms = $db->getRow($sql);
		if(!$sms)
			return $this->outinfo("并没有给你发送过短信..",1,$way);

		if($sms['code'] != $code)
			return $this->outinfo("验证码错误",1,$way);

		//if( $sms['fail_time']  < time() )
		//	return $this->outinfo("验证码已失效",1,$way);

		return $this->outinfo('',0,$way);
	}

	function ajaxAuthcode(){
		$code = $_REQUEST['authcode'];
		if(!$code)
			$this->outinfo('验证码为空',1);

		$category = 'payps';
		$type = 'set';
		$this->authcode($code,$category,$type);
	}

	//AJAX，表单验证
	function remoteSMSCode(){
		if(!$this->visitor->info['user_id'])
			$this->outinfo(0,1,'remote');

		$category = $_REQUEST['category'];
		$type = $_REQUEST['type'];
		$opt = $_REQUEST['opt'];
		$phone = $_REQUEST['phone'];
		$this->SMSCode($phone,$category, $type , 'remote' ,$opt);
	}

	function ajaxAuthcode2(){
		$code = $_REQUEST['authcode'];
		if(!$code)
			$this->outinfo('验证码为空',1,'boolean');

		$category =$_REQUEST['category'];
		if(!$category)
			$this->outinfo('category为空',1,'boolean');

		$type = $_REQUEST['type'];
		if(!$type)
			$this->outinfo('type为空',1,'boolean');

		$rs = $this->authcode($code,$category,$type,'boolean');
// 		var_dump($rs);
	}
	function getHidePhone($phone){
		return substr( $phone, 0,3). "****". substr( $phone, 7);

	}

	function autchPhone($value){
		$rule = "/^1[3,5,8]\d{9}$/";

		return 	preg_match($rule,$value)===1;
	}

	function autchemail($value){
		$rule = "/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/";

		return 	preg_match($rule,$value)===1;
	}
	//找回密码-发送验证短信
	function findpsSMSCode(){
		$category = 'findps';
		$type =  'findps';
		$phone = $_REQUEST['phone'];

		$this->SMSCode($phone,$category,$type);
	}
	//找回密码-重置发送验证短信
	function findpsRestSMSCode(){
		$category = 'findps';
		$type =  'findps';
		$phone = $_REQUEST['phone'];

		$this->SMSCode($phone,$category,$type,'ajax','reset');
	}
	//重置短信验证码
	function resetSMSCode(){
		if(!$this->visitor->info['user_id'])
			$this->echoJson("请登录");

		$category = $_REQUEST['category'];
		$type = $_REQUEST['type'];
		$opt = $_REQUEST['opt'];
		$phone = $_REQUEST['phone'];
		$this->SMSCode($phone,$category, $type , 'ajax' , $opt);
	}


	function SMSCodeAjax($way = 'ajax'){
		if(!$this->visitor->info['user_id'])
			$this->echoJson("请登录");

		$category = $_REQUEST['category'];
		$type = $_REQUEST['type'];
		$opt = $_REQUEST['opt'];
		$phone = $_REQUEST['phone'];
		$this->SMSCode($phone,$category, $type , 'ajax' , $opt);

	}


	function SMSCode($phone,$category,$type , $way = 'ajax' , $opt = 'get'){
		$reset_time = 60;
		$db = &db();

		if(!$category)
			return $this->outinfo("category 参数错误",1,$way);

		if(!$type)
			return $this->outinfo("type 参数错误",1,$way);


		if(!$phone){
			if($this->visitor->info['phone_mob']){
				$phone = $this->visitor->info['phone_mob'];
			}else{
				return $this->outinfo("手机号码为空",1,$way);
			}
		}

		if(!$this->autchPhone($phone)){
			$this->outinfo("手机号码格式错误",1,$way);
		}


		$where = " category = '$category' and type = '$type' and phone = '$phone' ";
		if($this->visitor->info['user_id'])
			$where .=  " and  uid = " . $this->visitor->info['user_id'];

		$sql = "select * from cf_sms_code where $where ";
		$sms = $db->getRow($sql);

		$js = '<script>
					function aaa(){
						var num = $("#reset").html();
						num = parseInt(num);
						if(num > 0){
							num = num - 1;
							$("#reset").html(num);
						}else{
							clearInterval(loop_del);
							$("#getSMS").bind("click",resetSMSCode);
							$("#getSMS").html("重新发送");
						}
					}
					var loop_del = setInterval(aaa,1000);
			   </script>';
		$reset = '<span id="reset">'.$reset_time.'</span>秒后重新发送'.$js;
		if($sms){//之前已发过
			if(!$opt || $opt == 'get'){//get:第一次获取一个码,res:重新发送一个验证码
				if($sms['fail_time'] > time()){//未失效
					//已经发送过了...
					return $this->outinfo($reset_time."秒内，请不要重复发送...验证码",1,$way);
				}else{//已失效
					$this->sendSms($phone,$category,$type,$where);
					return $this->outinfo($reset,0,$way);
				}
			}else{//重置一个码
				if($sms['fail_time'] > time()){//未失效
					$str = $reset_time."秒之后才可重置...";
					return $this->outinfo($str,1,$way);
				}else{
					$this->sendSms($phone,$category,$type,$where);
					return $this->outinfo($reset,0,$way);
				}
			}
		}else{//之前未发过
			$this->sendSms($phone,$category,$type,$where);
			return $this->outinfo($reset,0,$way);
		}
	}

	function sendSMS($phone,$category,$type,$where){
		$db = &db();
		$m = m('sms_code');
		$sms_code = rand(100000, 999999);
		sms($phone, $sms_code);
		$db->query("delete from cf_sms_code where $where");
		$data = array(
				'code'=>$sms_code,
				'type'=>$type,
				'fail_time'=>time() + SMS_FAIL_TIME,
				'add_time'=>time(),
				'uid'=> $this->visitor->info['user_id'],
				'category'=>$category,
				'phone'=>$phone,
		);
		$m->add($data);
	}

	function sendEmail($email,$category,$type,$where){
		$db = &db();
		$m = m('email_code');

		$url = $this->getEmailcode($email,$category);
		$code = $this->getEmailDesc($category).",请点击连接:<a href='$url'>$url</a>";

		$this->sendmail($email, $code);
		$db->query("delete from cf_email_code where $where");
		$data = array(
				'code'=>$url,
				'type'=>$type,
				'fail_time'=>time() + EMAIL_FAIL_TIME,
				'add_time'=>time(),
				'uid'=> $this->visitor->info['user_id'],
				'category'=>$category,
				'email'=>$email,
		);
		$m->add($data);
	}

	function getEmailDesc($category){
		$arr = array('findps'=>'找回密码','bind'=>'找回邮箱','email'=>'修改邮箱');
		return $arr[$category];
	}

	function getEmailbackUrl($category){
		$arr = array(
				'findps'=>site_url()."/index.php/member-find_password.html?opt=3",
				'bind'=>site_url()."/index.php/kuke-bindemail.html?opt=3",
				'email'=>site_url()."/index.php/kuke-upemail.html?opt=4",
		);
		return $arr[$category];
	}

	function findpsEmail(){
		$email = $_REQUEST['email'];
		$this->emailcode($email, 'findps', 'findps');
	}

	function findpsResetEmail(){
		$email = $_REQUEST['email'];
		$this->emailcode($email, 'findps', 'findps','ajax','reset');
	}


	function emailcode($email,$category,$type , $way = 'ajax' , $opt = 'get'){
		$db = &db();
		$reset_time = 60;

		if(!$category)
			return $this->outinfo("category 参数错误",1,$way);

		if(!$type)
			return $this->outinfo("type 参数错误",1,$way);


		if(!$email){
			if($this->visitor->info['email']){
				$email = $this->visitor->info['email'];
			}else{
				return $this->outinfo("邮箱地址为空",1,$way);
			}
		}

		if(!$this->autchemail($email)){
			$this->outinfo("邮箱格式错误",1,$way);
		}

		$where = " category = '$category' and type = '$type' and email = '$email' ";
		if($this->visitor->info['user_id'])
			$where .=  " and  uid = " . $this->visitor->info['user_id'];

		$sql = "select * from cf_email_code where $where ";
		$sms = $db->getRow($sql);

		$js = '<script>
					function aaa(){
						var num = $("#reset").html();
						num = parseInt(num);
						if(num > 0){
							num = num - 1;
							$("#reset").html(num);
						}else{
							clearInterval(loop_del);
							$("#getSMS").bind("click",findpsResetEmail);
							$("#getSMS").html("重新发送");
						}
					}
					var loop_del = setInterval(aaa,1000);
			   </script>';
		$reset = '<span id="reset">'.$reset_time.'</span>秒后重新发送'.$js;
		if($sms){
			if(!$opt || $opt == 'get'){//获取一个码
				if($sms['fail_time'] > time()){
					return $this->outinfo("请不要重复发送...验证码",1,$way);
				}else{//已失效
					$this->sendEmail($email, $category, $type, $where);
					return $this->outinfo($reset,0,$way);
				}
			}else{//重置一个码
				$this->sendEmail($email, $category, $type, $where);
				return $this->outinfo($reset,0,$way);
			}
		}else{
			$this->sendEmail($email, $category, $type, $where);
			return $this->outinfo($reset,0,$way);
		}
	}

	function getEmailcode($email,$category){
		$code = md5("rctailor".$email);
		$url = $this->getEmailbackUrl($category);
		$url .= "&account_type=email&email=$email&account=".$email."&code=".$code;
		return $url;
	}

	function outinfo($msg,$err = 1 ,$way = 'ajax'){
		$rs = array('err'=>$err,'msg'=>$msg);
		if($way == 'ajax'){
			echo json_encode($rs);
			exit;
		}elseif($way == 'pc'){
			return $rs;
		}else{
			if($err){
				echo 'false';
			}else
				echo 'true';
		}

	}

    /**
     *    获取订单列表
     *
     *    @author    Garbin
     *    @return    void
     */
    function get_new_orders()
    {
        $page = $this->_get_page(5);
        $model_order =& m('order');
        !$_GET['type'] && $_GET['type'] = 'all_orders';
        $conditions = '';
        /* 查找订单 */
        $orders = $model_order->findAll(array(
            'conditions'    => "buyer_id=" . $this->visitor->get('user_id') . "{$conditions}",
            'fields'        => 'this.*',
            'count'         => true,
            'limit'         => $page['limit'],
            'order'         => 'add_time DESC',
            'include'       =>  array(
                'has_ordergoods',       //取出商品
            ),
        ));
        foreach ($orders as $key1 => $order)
        {
            foreach ($order['order_goods'] as $key2 => $goods)
            {
                empty($goods['goods_image']) && $orders[$key1]['order_goods'][$key2]['goods_image'] = Conf::get('default_goods_image');
            }
        }

		$this->assign('orders', $orders);
        $this->_format_page($page);
    }

	//账号强弱等待
	function accountLevel(){
		if(
		$this->visitor->info['phone_mob'] &&
		$this->visitor->info['email'] &&
		$this->visitor->info['pay_ps'] )
		{
			$safe_level = "强";
			$safe_img = "qiang.gif";
		}elseif(
				( $this->visitor->info['phone_mob'] && $this->visitor->info['email']  )		||
				( $this->visitor->info['phone_mob'] && $this->visitor->info['pay_ps']  ) ||
				( $this->visitor->info['pay_ps'] && $this->visitor->info['email']  )
		)
		{
			$safe_level = "中";
			$safe_img = "zhong.gif";
		}elseif(
				$this->visitor->info['phone_mob'] ||
				$this->visitor->info['email'] ||
				$this->visitor->info['pay_ps']
		){
			$safe_level = "弱";
			$safe_img = "ruo.gif";
		}else{
			$safe_level = "差";
			$safe_img = "cha.gif";
		}
		$this->assign('safe_level' , $safe_level);
		$this->assign('safe_img' , $safe_img);
	}

	function sendmail($email,$msg){
		$model_setting = &af('settings');
		$setting = $model_setting->getAll(); //载入系统设置数据
		$email_from = Conf::get('site_name');
		$email_type = $setting['email_type'];
		$email_host = $setting['email_host'];
		$email_port = $setting['email_port'];
		$email_addr = $setting['email_addr'];
		$email_id   = $setting['email_id'];
		$email_pass = $setting['email_pass'];
		$email_test = $email;				//接收人email
		$email_subject = '用户帐号激活';	//邮件标题

		import('mailer.lib');
		$mailer = new Mailer($email_from, $email_addr, $email_type, $email_host, $email_port, $email_id, $email_pass);
		$res = $mailer->send($email_test, $email_subject, $msg, CHARSET, 1);
	}
}
?>
