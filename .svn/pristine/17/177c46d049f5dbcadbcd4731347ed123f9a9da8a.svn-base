<?php
/**
 * 酷客基地
 * @author yhao.bai
 *
 */
class ClubApp extends MallbaseApp
{
	var $_distance = 500;		//移动距离请求
	var $_spage_size = 16;		//每次加载个数
	var $_spage_max =  3;		//加载次数
	var $_comment_size = 3;	    //加载评论数
    function __construct()
    {
//        Lang::load(lang_file('common'));
//        Lang::load(lang_file(APP));
        $setting =& af('settings');
        Conf::load($setting->getAll());

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
    							'path' => 'jquery.plugins/jquery.validate.js',
                        ))
    	));

    	$this->photo = m('userphoto');
    	$this->album =  m('album');
    	$this->member = m('member');
        $this->ClubApp();

        $this->title_pre = "我是酷客";
    }

    function msg($msg){
    	show_warning($msg);
    	exit;
    }

    function ClubApp()
    {


    	//实例member的M
    	$this->m =& m('member');

        parent::__construct();
        define("CLUB", "club/");
        $this->assign('club',CLUB);
        $this->img_url = site_url() . "/themes/mall/default/styles/default/"	;

        $this->assign('act',ACT);
        $this->assign('img_url',$this->img_url );

    }
    //酷客基地首页
    function index(){
        $this->assign('title','RCTAILOR-定制是一种生活态度(rctailor.com)-时尚街拍、大牌走秀、服装设计');
        $this->assign('keywords','西服定制、时尚街拍、大牌走秀、服装设计');
        $this->assign('description','RCTAILOR会员基地，这里有最新最时尚的服装动态，最前沿且独一无二的服装设计，这是一个属于网友的平台！心意·新意-定制是一种生活态度(rctailor.com)');


    	$db = &db();
    	//热门排行-个人设计
    	$hot = $this->getCntPhotoIndex(1,8);
    	//热门排行-街拍
    	$hot_camera = $this->getCntPhotoIndex(2,8);
//        var_dump($hot_camera[0]);exit;
	    //---------------流行趋势-----------
    	$recommend = $this->album->getRecommed(0,0,' add_time desc ');
    	if($recommend)
	    	foreach($recommend as $k=>$v){
	    		$recommend[$k]['user'] = getUinfoByUid($v['uid']);
	    		$photos = $this->photo->getByAlbumId($v['id'],4);
	    		$views = 0;
                $k2 = 0;
                $tmp = array();
//                echo count($photos);
                if($photos)
                    foreach($photos as $k2=>$v2){
                        if($v2['cate'] == 1)
                            $tmp[$k2]['url'] = getDesignUrl($v2['url'],500);
                        else
                            $tmp[$k2]['url'] = getCameraUrl($v2['url'],500);

                        $tmp[$k2]['link'] = getPhotoDetailLink($v2['id'],$v2['cate'] );
                        $views += $v2['views'];

                    }
//	    		echo $k2 . "<br/>";
	    		if($k2 != 3)
		    		for($i=$k2 + 1; $i<4;$i++){
                        $tmp[$i] = $photos[] = array('url'=>getDefAlbumPic('big'));
		    		}

	    		$recommend[$k]['img'] = $tmp;
	    		$recommend[$k]['views'] = $views;

	    		if($this->visitor->info['user_id'])
		    		if($v['uid'] == $this->visitor->info['user_id']){
		    			$recommend[$k]['self'] = 1;
		    		}else{
		    			$recommend[$k]['self'] = 0;
		    			if(isFans($this->visitor->info['user_id'],$v['uid'])){
		    				$recommend[$k]['isFans'] = 1;
		    			}
		    		}

	    	}

    	$member = $this->member->getDaren(9);
    	foreach($member as $k=>$v){
    		$member[$k]['avatar'] = getAvatarByFile($v['avatar']);
    		$member[$k]['uid'] = $v['user_id'];

            $level = getLevel($v['member_lv_id']);
            $member[$k]['level_logo'] = $level['lv_logo'];

    		if($this->visitor->info['user_id'])
	    		if($v['user_id'] == $this->visitor->info['user_id']){
	    			$member[$k]['self'] = 1;
	    		}else{
	    			$member[$k]['self'] = 0;
	    			if(isFans($this->visitor->info['user_id'],$v['user_id']))
	    				$member[$k]['isFans'] = 1;
	    		}
    	}

    	$this->assign('hot_camera',$hot_camera);
    	$this->assign('hot',$hot);
    	$this->assign('recommend',$recommend);
    	$this->assign('member',$member);


        $this->display(CLUB.'index.html'); //先默认调用 晒酷首页，因为还没有酷客基地的首页
    }
    //达人列表
    function coolerlist(){
    	$db = &db();

    	$this->assign('title',$this->title_pre.'-达人列表');

    	$page_num = $_REQUEST['page'];

    	$daren_db  = $this->member->pageDaren($page_num,45);
    	$page = $daren_db['page'];
    	if($daren_db['data']){
    		foreach($daren_db['data'] as $k=>$v){

                $uinfo = getUinfoByUid($v['user_id']);
                $daren_db['data'][$k]['level_logo'] =  $uinfo['level_logo'];

    			if($this->visitor->info['user_id'])
    			if($v['user_id'] == $this->visitor->info['user_id']){
    				$daren_db['data'][$k]['self'] = 1;
    			}else{
    				$daren_db['data'][$k]['self'] = 0;
    				if(isFans($this->visitor->info['user_id'],$v['user_id']))
    					$daren_db['data'][$k]['isFans'] = 1;
    			}
    		}
    	}
    	$this->assign('member',$daren_db['data']);
    	$this->assign('total',  $page->mTotalDataNum );
    	$this->assign('page_show',  $page->mShowHTML );

    	$this->display(CLUB.'coolerlist.html');
    }
    //个人设计-详细页
    function personaldesign(){
    	$db = &db();

    	$pic_id = $_REQUEST['id'];
    	if(!$pic_id){
    		$args = $this->get_params();
    		$pic_id = $args[0];
    	}
    	if(!$pic_id)
    		$this->msg('id 为空，黑客：放下武器...');

    	$this->assign('id',$pic_id);

    	$photo = $this->photo->getById($pic_id);
		if(!$photo)
			$this->msg('cf_userphoto id not in db');

		if(!$photo['status'])
			$this->msg("该照片还未通过审核...");

    	#排除ipad 访问跳转到wap站
    	if (is_mobile_request()){
    		header("Location:http://m.rctailor.com/index.php/club-personaldesign-$pic_id.html");
    	}

        //$this->assign('title',$this->title_pre.'-个人设计-' . $photo['title']);
        //ns add
         $this->assign("title",'个人设计TOP-'.$photo['title'].'- RCTAILOR-定制是一种生活态度(rctailor.com)');
        $this->assign('keywords','时尚街拍、大牌走秀、个人设计、会员中心');
        $this->assign('description','个人设计TOP-潮流西服展示、时尚元素、西服定位、定制西服、产品实拍-RCTAILOR-定制是一种生活态度(rctailor.com)');

		$photo['url'] = getDesignUrl($photo['url'],500);
		if($photo['base_info'])
			$photo['base_info'] = html_entity_decode($photo['base_info']);

		if($this->visitor->info['user_id']){
			if($photo['uid'] == $this->visitor->info['user_id']){
				$photo['self'] = 1;
			}else{
				$photo['self'] = 0;
				$isFans = isFans($this->visitor->info['user_id'],$photo['uid']);
				if($isFans)
					$photo['isFans'] = 1;
			}

			$photo['like'] = getLikeByUid($this->visitor->info['user_id'],$pic_id, 'sheji_like');
		}

		$sheji_num = $this->photo->cntByCateUid(1,$photo['uid']);
		$this->assign('sheji_num',$sheji_num);



		if(!$photo['album_id'])
			$this->msg("该照片还未加入相册");

		if(!$photo['base_info'])
			$photo['base_info'] = "很懒，木有信息...";

		if(!$photo['title'])
			$photo['title'] = "很懒，木有信息...";

		$m = m('userphoto');
		$m->setInc(" id = ".$pic_id , 'views');

		//获取照片所属相册
    	$album = $this->album->getById($photo['album_id']);
    	$album_photo = $this->photo->getByAlbumId($photo['album_id']);
    	if($album_photo)
    		foreach($album_photo as $k=>$v){
    			$album_photo[$k]['url'] = getDesignUrl($v['url']) ;

    		}

    	$this->assign("album_photo",$album_photo);
    	//获取右侧-推荐相册
    	$user_album = $this->album->getByCateUid(1,$photo['uid'] , 'add_time desc', 2);
    	if($user_album)
	    	foreach($user_album as $k=>$v){
	    		$user_album[$k]['num'] = $v['pic_num'];

	    		$tmp = $this->photo->getByAlbumId($v['id']);
	    		$views = 0;
	    		foreach($tmp as $k2=>$v2){
	    			$tmp[$k2]['url'] = getDesignUrl($v2['url'],500);
	    			$tmp[$k2]['link'] = getPhotoDetailLink($v2['id'], 1);
	    			$views += $v2['views'];
	    		}
	    		$user_album[$k]['img'] = $tmp;
	    		$user_album[$k]['views'] = $views;

	    	}
    	$user = getUinfoByUid($photo['uid']);
    	$this->assign('user_album',$user_album);
    	$this->assign('user',$user);
    	$this->assign('photo',$photo);
    	$this->assign('album',$album);
        $this->assign('album_date',date("Y-m-d H:m",$album['add_time']) );

    	//评论
    	$opt = $_REQUEST['opt'];
    	if($opt){
    		$content = $_REQUEST['saytext'];
    		if(!$content)
    			$this->msg('comment content is null...');


    		$rs = setComment($this->visitor->info['user_id'], $photo['uid'], $pic_id, 'sheji_comment', $content);
    		if(!$rs['err']){
	    		$num = pointTurnNum('sheji_comment');
	    		$msg = "评价了个人设计~ID：".$pic_id;
	    		setPoint($this->visitor->info['user_id'], $num, 'add', 'sheji_comment','system',$msg);
    		}
    	}

    	//获取评论
    	$this->getCommends($pic_id,'sheji_comment');

    	$this->display(CLUB.'personaldesign.html');
    }
    //街拍-详细页面
    function streetinfo(){
    	$db = &db();

    	$pic_id = $_REQUEST['id'];
    	if(!$pic_id){
    		$args = $this->get_params();
    		$pic_id = $args[0];
    	}
    	if(!$pic_id)
    		$this->msg('$pic_id is null');

    	$this->assign('id',$pic_id);

    	$photo = $this->photo->getById($pic_id , 500);
    	if(!$photo)
    		$this->msg('cf_userphoto id not in db');

    	if(!$photo['album_id'])
    		$this->msg("该照片还未加入相册");

    	if(!$photo['status'])
    		$this->msg("该照片还未通过审核...");

    	#排除ipad 访问跳转到wap站
    	if (is_mobile_request()){
    		header("Location:http://m.rctailor.com/index.php/club-streetinfo-$pic_id.html");
    	}

    	if($this->visitor->info['user_id']){
    		if($photo['uid'] == $this->visitor->info['user_id']){
    			$photo['self'] = 1;
    		}else{
    			$photo['self'] = 0;
    			$isFans = isFans($this->visitor->info['user_id'],$photo['uid']);
    			if($isFans)
    				$photo['isFans'] = 1;
    		}

    		$photo['like'] = getLikeByUid($this->visitor->info['user_id'],$pic_id, 'jiepai_like');
    	}

    	//$photo['like'] = getLikeByUid($this->visitor->info['user_id'],$pic_id, 'camera');
    	$photo['url'] = getCameraUrl($photo['url'],2);

    	$m = m('userphoto');
    	$m->setInc(" id = ".$pic_id , 'views');

    	$album = $this->album->getById($photo['album_id']);
    	if(!$photo['album_id'])
    		$this->msg("该照片还未加入相册");

    	if(!$album['desc'])
    		$album['desc'] = '木有信息';

        //$this->assign('title',$this->title_pre.'-街拍-' . $album['title']);
                //ns add
        $this->assign("title",'时尚街拍TOP-'.$album['description'].'- RCTAILOR-定制是一种生活态度(rctailor.com)');
        $this->assign('keywords','时尚街拍、大牌走秀、个人设计、会员中心');
        $this->assign('description','时尚街拍TOP-潮流西服展示、时尚元素、西服定位、定制西服、产品实拍-RCTAILOR-定制是一种生活态度(rctailor.com)');
    	if($album){
    		$album_photo = $this->photo->getByAlbumId($photo['album_id']);
    		if($album_photo)
	    		foreach($album_photo as $k=>$v){
	    			$album_photo[$k]['url'] = getCameraUrl($v['url']) ;

	    		}
    		$this->assign("album_photo",$album_photo);
    	}

    	$opt = $_REQUEST['opt'];
    	if($opt){
    		$content = $_REQUEST['saytext'];
    		$rs = setComment($this->visitor->info['user_id'], $photo['uid'], $pic_id, 'jiepai_comment', $content);
    		if(!$rs['err']){
	    		$num = pointTurnNum('jiepai_comment');
	    		$msg = "评价了街拍~ID：".$pic_id;
	    		setPoint($this->visitor->info['user_id'], $num, 'add', 'jiepai_comment'  , 'system',$msg);
    		}
    	}

    	$this->getCommends($pic_id, 'jiepai_comment');


    	$jiepai_num = $this->photo->cntByCateUid(2,$photo['uid']);

    	$this->assign('jiepai_num',$jiepai_num);


    	$user = getUinfoByUid($photo['uid']);

    	$this->assign('user',$user);
    	$this->assign('photo',$photo);
    	$this->assign('album',$album);
        $this->assign('album_date',date("Y-m-d H:m",$album['add_time']) );

    	$this->display(CLUB.'streetinfo.html');
    }
    //酷吧/个人设计-相册
    function album(){
    	$this->assign('title',$this->title_pre.'-酷吧相册');

    	$db = &db();

    	$uid = $this->initUinfo();
    	$this->setUserNavNum($uid);

    	$this->assign('uid',$uid);
    	$this->assign('accessuid',$uid);

    	if($this->visitor->info['user_id']  == $uid){
    		$this->assign('self',1);
    	}

    	$sql = " select count(*) as total  from cf_album where uid = $uid and cate = 1 ";
    	$tmp = $db->getRow($sql);
    	$total = $tmp['total'];
    	if($total){

    		$page = $_REQUEST['page'];
    		if(!$page)
    			$page = 1;
    		
    		include 'includes/libraries/page_search.lib.php';
    		$page = new PageSearch($page,$total , 4 );

//     		include 'includes/libraries/page.lib.php';
//     		$page = new Page($total , 4);
    		$page->moduleSymbol = "/index.php/club-album-$uid.html";
    		$page->execPage();

    		$sql = " select *  from cf_album where  uid = $uid  and cate = 1   limit ".$page->mLimit[0]." , " .$page->mLimit[1];
    		$album = $db->getAll($sql);
    		foreach($album as $k=>$v){
    			$tmp = $this->photo->getByAlbumId($v['id'],4);
    			$def = 4;
    			$views = 0;
    			if($tmp)
	    			foreach($tmp as $k2=>$v2){
	    				$tmp[$k2]['url'] = getDesignUrl($v2['url'],500);
	    				$tmp[$k2]['link'] = "/index.php/club-albuminfo-{$v2['album_id']}-{$uid}.html";
	    				$views += $v2['views'];
	    			}

	    			$album[$k]['views'] = $views;
	    		$for = $def - count($tmp);
	    		if($for)
	    			for($i=0;$i<$for;$i++){
	    				$tmp[$i+1] = $tmp[] = array('url'=>getDefAlbumPic('big'));
	    			}

    			$album[$k]['img'] = $tmp;
    		}
    	}

    	$this->assign('album',$album);
    	$this->assign('total',  $page->mTotalDataNum );
    	$this->assign('page_show',  $page->mShowHTML );


    	$this->display(CLUB.'album.html');
    }
    //创建酷吧相册
    function createAlbum(){
    	$this->assign('title',$this->title_pre.'-创建酷吧相册');

    	$uid = $this->initUinfo();
    	$this->setUserNavNum($uid);
    	$this->assign('uid',$uid);
    	$this->assign('accessuid',$uid);

    	$opt = $_REQUEST['opt'];
    	if($opt){
    		$title = $_REQUEST['title'];
    		$desc = $_REQUEST['desc'];

    		if(!$title)
    			$this->msg(show_warning("标题不能为空..."));

    		$data = array(
    				'title'=>$title,
    				'description'=>$desc,
    				'add_time'=>time(),
    				'cate'=>1,
    				'uid'=>$this->visitor->info['user_id'],
    		);


    		$m = m('album');
    		$m->add($data);

    		header("location:/index.php/club-album.html");
    		exit;
    	}
    	$this->display(CLUB.'create_album.html');
    }
    //编辑酷吧相册
    function albumEdit(){
    	$this->assign('title',$this->title_pre.'-酷吧相册编辑');

    	$uid = $this->initUinfo();
    	$this->setUserNavNum($uid);
    	$this->assign('uid',$uid);
    	$this->assign('accessuid',$uid);

    	$id = $_REQUEST['id'];
    	if(!$id)
    		$this->msg('ID null');

    	$album = $this->album->getById($id);
    	if(!$album)
    		$this->msg('id not in db...');

    	$opt = $_REQUEST['opt'];
    	if($opt){
    		$title = $_REQUEST['title'];
    		$desc = $_REQUEST['desc'];

    		if(!$title)
    			$this->msg("标题不能为空...");

    		$data = array(
    				'title'=>$title,
    				'description'=>$desc,
    		);


    		$m = m('album');
    		$m->edit($id,$data);

    		header("location:/index.php/club-album.html");
    		exit;
    	}

    	$this->assign('album',$album);

    	$this->display(CLUB.'album_edit.html');
    }
    //达人/个人-主页
    function cooler(){
    	$this->assign('title',$this->title_pre."-个人主页");
        /* 带有uid访问  */
        $args = $this->get_params();
        $accessuid = $args[0];

    	$uid = $this->initUinfo();
    	$this->setUserNavNum($uid);

    	$this->assign('uid',$uid);

    	$db = &db();

    	$sql = " select count(*) as total from cf_user_follow  where follow_uid = ".$uid;
    	$tmp = $db->getRow($sql);
    	$total = $tmp['total'];
    	if($total){
    		$page = $_REQUEST['page'];
    		if(!$page)
    			$page = 1;

            include 'includes/libraries/page_search.lib.php';
            $page = new PageSearch($page,$total , 9 );

    		$page->moduleSymbol = "/index.php/club-cooler-{$uid}.html";
    		$page->execPage();
    		$sql = " select * from cf_user_follow where follow_uid = ".$uid ."  limit ".$page->mLimit[0]." , " .$page->mLimit[1];
    		$member = $db->getAll($sql);
    		foreach($member as $k=>$v){
    			$member[$k]['user'] = getUinfoByUid($v['uid']);

                $uinfo = getUinfoByUid($v['uid']);
                $member[$k]['level_logo'] =  $uinfo['level_logo'];

    			if($this->visitor->info['user_id'])
	    			if($v['uid'] == $this->visitor->info['user_id']){
	    				$member[$k]['self'] = 1;
	    			}else{
	    				$member[$k]['self'] = 0;
	    				$isFans = isFans($this->visitor->info['user_id'],$v['uid']);
	    				if($isFans)
	    					$member[$k]['isFans'] = 1;
	    			}
    		}
    	}

    	$this->assign('accessuid',$accessuid);
    	$this->assign('member',$member);
    	$this->assign('total',  $page->mTotalDataNum );
    	$this->assign('page_show',  $page->mShowHTML );

    	$this->display(CLUB.'cooler.html');
    }
    /******************************************************************************************************************/
    //-------------------------------------------	@author v5  Start--------------------------------------------------
    /******************************************************************************************************************/
	/**
	 * 喜欢
	 */
    function like(){
    	$this->assign('title',$this->title_pre. '-喜欢查看');

    	$args = $this->get_params();
    	$uid = $args[0];			//用户id
    	$isajax = $args[1];			//用户id

    	$uid = $this->initUinfo();
    	$this->setUserNavNum($uid);


    	$this->assign('accessuid', $uid);

    	//是否自己
    	$user_follow_mod =& m('userfollow');

    	$user = $this->visitor->get();

    	//ship   0:互相未关注 1:我关注对方 2:互相关注
    	$user['ship'] = 0;
    	if ($this->visitor->has_login) {
    		$ship = $user_follow_mod->get(array('uid' => $user['user_id'], 'follow_uid' => $uid));
    		if ($ship) {
    			$user['ship'] = $ship['mutually'] ? 2 : 1;
    		}
    	}

    	$this->assign('visitor', $user);
    	$authority = 0;
    	if($user['user_id'] == $uid){
    		$authority = 1;
    	}
    	$this->assign('authority', $authority);

    	$start = $this->_spage_size;

    	if ($isajax){
    		$sp = $_GET['sp'];//子页
    		//计算开始
    		$start = $this->_spage_size * $sp;
    		$start = $start.','.$this->_spage_size;
    	}


    	//获取用户信息
    	$uinfo = getUinfoByUid($uid);
    	$this->assign('uinfo', $uinfo);
    	//获取设计、街拍的喜欢
    	$conditions = ' and cate in ("sheji_like","jiepai_like")';
    	$this->_like_mod =& m('like');
    	$like_list = $this->_like_mod->find(array(
    			'conditions' => "uid = $uid ".$conditions,
    			'limit' => $start,
    			'count' => true
    	));

    	$ids = '';
    	foreach ($like_list as $k=>$v){
    		$ids[] = $v['like_id'];
    	}

    	$this->_userphoto_mod =& m('userphoto');

    	$photo_list =$this->_userphoto_mod->find(array(
    			'conditions'=>db_create_in($ids,'id'),
    	));

    	$itemlist = $this->_formatting_data($photo_list);

    	$this->assign('alist', $album_list);

    	$item_count = $this->_userphoto_mod->getCount();
    	if (!$isajax){
	    	$this->assign('distance', $this->_distance);
	    	$this->assign('spage_max', ceil($item_count/$this->_spage_size));
    	}
    	$this->assign('uid', $uid);
    	$this->assign('item_list', $itemlist);

    	if ($isajax){

    		$resp = array();
    		if ($itemlist){

    			$resp = $this->_view->fetch(CLUB."fans_waterfall.html");
    		}
	    		$data = array(
	    				'isfull' => 1,
	    				'html' => $resp
	    		);
	    		ajaxReturn(1, '', $data);

    	}else {
    		$this->display(CLUB.'fans.html');
    	}

    }



    /**
     * 酷客基地-相册详细页面
     *
     * http://local.rc.com/index.php/club-albuminfo-1-1-182.html
     */
    public function albuminfo(){
    	//$this->assign('title',$this->title_pre.'酷吧相册-列表');
        $this->assign("title",'酷吧推荐-'.$this->title_pre.'- RCTAILOR-定制是一种生活态度(rctailor.com)');
        $this->assign('keywords','时尚街拍、大牌走秀、个人设计、会员中心');
        $this->assign('description','酷吧推荐-潮流西服展示、时尚元素、西服定位、定制西服、产品实拍-RCTAILOR-定制是一种生活态度(rctailor.com)');

//     	Qrcode('goods','1004');
//     	echo getQrcodeImage('goods',1004);
    	$args = $this->get_params();
    	$id = $args[0];				//相册id
    	$uid = $args[1];			//用户id


    	//是否自己
    	$user_follow_mod =& m('userfollow');

    	$user = $this->visitor->get();

    	//ship   0:互相未关注 1:我关注对方 2:互相关注
    	$user['ship'] = 0;
    	if ($this->visitor->has_login) {
    		$ship = $user_follow_mod->get(array('uid' => $user['user_id'], 'follow_uid' => $uid));
    		if ($ship) {
    			$user['ship'] = $ship['mutually'] ? 2 : 1;
    		}
    	}
    	$this->assign('user', $user);
    	$authority = 0;
    	if($user['user_id'] == $uid){
    		$authority = 1;
    	}
    	$this->assign('authority', $authority);

    	//获取相册信息
    	$this->_album_mod =& m('album');
    	$albumdata =$this->_album_mod->get($id);
    	$this->assign('info', $albumdata);

    	//获取用户信息
    	$uinfo = getUinfoByUid($uid);
    	//=================
    	//由于喜欢总数这里设计到很多点,暂时先在这用的时候查询,后续优化
    	$sin_db = &db();
    	$conditions = ' and cate in ("jiepai_like","sheji_like")';
    	$sql = "select count(*) as total from cf_like where  uid = ".$uid.$conditions;
    	//$sql = "select count(*) as total from cf_like where  uid = ".$uid;
    	$row = $sin_db->getRow($sql);
    	$uinfo['like_num'] = $row['total'];

    	$sql = "select count(*) as total from cf_userphoto where  cate = '2' AND uid = ".$uid;
    	$row = $sin_db->getRow($sql);
    	$uinfo['pic_num'] = $row['total'];

    	//=================
    	$this->assign('uinfo', $uinfo);

    	//获取相册下的所有图片
    	$conditions = '';
    	$this->_userphoto_mod =& m('userphoto');
    	$photo_list = $this->_userphoto_mod->find(array(
    			'conditions' => "album_id = $id and status = 1 ".$conditions,
    			'order' => "add_time desc",
    			'limit' => $this->_spage_size,
    			'count' => true
    	));

    	//获取该用户的所有相册
    	$album_list = $this->_album_mod->find(array(
    			'conditions' => "id != $id and uid=".$albumdata['uid'],
    			'order' => "pic_num desc",
    			'limit'=>5,
    			'count' => true
    	));
    	$this->assign('alist', $album_list);

    	$itemlist = $this->_formatting_data($photo_list);

    	$item_count = $this->_userphoto_mod->getCount();
    	$this->assign('distance', $this->_distance);
    	$this->assign('spage_max', ceil($item_count/$this->_spage_size));
    	$this->assign('id', $id);
    	$this->assign('uid', $uid);
    	$this->assign('item_list', $itemlist);



    	$this->assign('infourl', 'personaldesign');

    	$this->display(CLUB.'albuminfo.html');
    }
    /**
     * 相册详细页瀑布流
     */
    function albuminfo_waterfal(){
    	$args = $this->get_params();
    	$id = $args[0];
    	$uid = $args[1];

    	//是否自己
    	$user = $this->visitor->get();
    	$authority = 0;
    	if($user['user_id'] == $uid){
    		$authority = 1;
    	}
    	$this->assign('authority', $authority);


    	$sp = $_GET['sp'];//子页
    	//计算开始
    	$start = $this->_spage_size * $sp;

    	$conditions = '';
    	$this->_userphoto_mod =& m('userphoto');
    	$photo_list = $this->_userphoto_mod->find(array(
    			'conditions' => "album_id = $id and status = 1 ".$conditions,
    			'order' => "add_time desc",
    			'limit' => $start.','.$this->_spage_size,
    			'count' => true
    	));

    	$resp = array();
    	if ($photo_list){
    		$itemlist = $this->_formatting_data($photo_list);
    		$this->assign('item_list', $itemlist);

    		$resp = $this->_view->fetch(CLUB."album_waterfall.html");
    	}
    	$data = array(
    			'isfull' => 1,
    			'html' => $resp
    	);
    	ajaxReturn(1, '', $data);
    }
   /**
    * 个人主页-个人设计|街拍
    *
    * 两种视角[自己看自己；自己看别人]
    *
    * uid | page 必须传递
    *
    * @author v5
    *
    */
    function hwaterfall($cate = 1){
    	//$this->assign('title',$this->title_pre.'-个人设计|街拍');
    	$this->assign("title",'酷客中心-个人主页');
        $this->assign('keywords','酷客中心、个人主页');
        $this->assign('description','RCTAILOR-定制是一种生活态度(rctailor.com)-红领裁缝:西装定制、西服定制、衬衫定制、裤子定制、马夹定制、大衣定制');
    	$args = $this->get_params();
    	$uid = $args[2];
    	$page = $args[0];
    	$cate = $args[1];
    	//@致富接口，日后负责优化
    	$uid = $this->initUinfo();
    	$this->setUserNavNum($uid);

		//是否自己
    	$user = $this->visitor->get();
    	$authority = 0;
    	if($user['user_id'] == $uid){
    		$authority = 1;
    	}
    	$this->assign('authority', $authority);

    	//计算开始
    	if ($page == 1 || empty($page)){
    		$start = $this->_spage_size;
    	}

    	if ($page > 1){
    		$start = $this->_spage_size*($page-1)*$this->_spage_max;
    		$start .=" , ".$this->_spage_size;
    	}
    	$conditions = ' and uid='.$uid;
    	$this->_userphoto_mod =& m('userphoto');
    	$photo_list = $this->_userphoto_mod->find(array(
    			'conditions' => "cate = $cate and status = 1 ".$conditions,
    			'count' => true,
    			'order' => "add_time desc",		//根据权重 正序
    			'limit' => $start,
    			'count' => true
    	));


    	$itemlist = $this->_formatting_data($photo_list);
    	$this->assign('item_list', $itemlist);
    	$this->assign('distance', $this->_distance);
    	$this->assign('spage_max', $this->_spage_max);


    	$page = $this->_get_pages($this->_spage_size,$page);

    	$page['item_count'] = $this->_userphoto_mod->getCount();
    	$this->_format_page($page);
    	$this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
    	$this->assign('page_info', $page);

    	$acurl = ($cate == 1) ? 'design_ajax' : 'camera_ajax';
    	$acorurl = ($cate == 1) ? 'design' : 'camera';
    	$infourl = ($cate == 1) ? 'personaldesign' : 'streetinfo';

    	$this->assign('type', $cate);
    	$this->assign('infourl', $infourl);
    	$this->assign('acorurl', $acorurl);
    	$this->assign('acurl', $acurl);
    	$this->assign('accessuid', $uid);

    	$this->display(CLUB.'design.html');
    }
    /**
     * 个人首页【设计|街拍】
     *
     * 瀑布流数据读取
     *
     * @author v5
     */
    function hwaterfall_ajax(){
    	$args = $this->get_params();
    	$uid = $args[2];
    	$page = $args[0];
    	$cate = $args[1];


    	//是否自己
    	$user = $this->visitor->get();
    	$authority = 0;
    	if($user['user_id'] == $uid){
    		$authority = 1;
    	}
    	$this->assign('authority', $authority);

    	$this->_userphoto_mod =& m('userphoto');

    	$page = $this->_get_pages($this->_spage_size,$page);
    	$conditions = ' and uid='.$uid;
    	$photo_list = $this->_userphoto_mod->find(array(
    			'conditions' => "cate = $cate and status = 1".$conditions,
    			'order' => "add_time desc",
    			'limit' => $page['limit'],
    			'count' => true
    	));

    	$acorurl = ($cate == 1) ? 'streetlist' : 'photolist';
    	$infourl = ($cate == 1) ? 'personaldesign' : 'streetinfo';
    	$this->assign('infourl', $infourl);
    	$this->assign('acorurl', $acorurl);
    	$this->assign('type', $cate);

    	$resp = array();
    	if ($photo_list){
    		$itemlist = $this->_formatting_data($photo_list);
    		$this->assign('item_list', $itemlist);

    		$resp = $this->_view->fetch(CLUB."home_waterfall.html");
    	}
    	$data = array(
    			'isfull' => 1,
    			'html' => $resp
    	);
    	ajaxReturn(1, '', $data);
    }

    function delAlbum(){
    	$id = $_REQUEST['id'];
    	if(!$id)
    		return -1;

    	$rs = $this->album->delById($id);
    	echo $rs;
    }

    /**
     * 删除个人主页-个人设计|街拍
     *
     */
    function del_hwaterfall_ajax(){
    	$args = $this->get_params();
    	$id = $args[0];
    	$cate = $args[1];

    	if (!$id || !$cate)	ajaxReturn(0, Lang::get("del_".$cate."_pam."));

    	$this->_userphoto_mod =& m('userphoto');


    	$photo = $this->_userphoto_mod->get($id);

    	if (!$photo) ajaxReturn(0, Lang::get("del_".$cate."_pam."));

    	$user = $this->visitor->get();

    	if ($user['user_id'] != $photo['uid'])	ajaxReturn(0, Lang::get("authority_".$cate));

    	if ($this->_userphoto_mod->drop(array('id'=>$id))) {

    		//用户总图片数 --
    		$this->_member_mod         =& m('member');
    		$this->_member_mod->setDec($user['user_id'],'pic_num');

    		//相册总图片数 --
    		$this->_album_mod =& m('album');
    		$this->_album_mod->setDec($photo['album_id'],'pic_num');


    		ajaxReturn(1, Lang::get("del_ok_".$cate));
    	}else{
    		ajaxReturn(0, Lang::get("del_err_".$cate));
    	}

    }

    /**
     * 街拍列表面
     *
     * 瀑布流分页与设计列表方法公用
     *
     * @author	v5
     */
    function photolist(){
    	$this->streetlist(2);
    }

    /**
     * 设计-列表页面
     *
     * 瀑布流分页
     *
     * @param	int  $cate		1设计；2街拍
     * @author	v5
     */
    function streetlist($cate = 1){
    	$this->assign('title',$this->title_pre.'-个人设计|街拍');
    	$this->_userphoto_mod =& m('userphoto');

    	$args = $this->get_params();

    	if($cate == 1){
    		$this->assign('title','设计-列表');
    	}else{
    		$this->assign('title','街拍-列表');
    	}

    	$conditions='';
    	//更新排序
    	if (isset($args[1]) && $args[1] == 'at')
    	{
    		$order = 'id';
    	}else {
    		$order = 'like_num';
    	}

    	//计算开始
    	if ($args[0] == 1 || empty($args[0])){
    		$start = $this->_spage_size;
    	}

    	if ($args[0] > 1){
    		$start = $this->_spage_size*($args[0]-1)*$this->_spage_max;
    		$start .=" , ".$this->_spage_size;
    	}

    	$photo_list = $this->_userphoto_mod->find(array(
    			'conditions' => "cate = $cate and status = 1 ".$conditions,
    			'count' => true,
    			'order' => "$order desc",		//根据权重 正序
    			'limit' => $start,
    			'count' => true
    	));

//     	var_dump($photo_list);

    	$itemlist = $this->_formatting_data($photo_list,1);

//     	var_dump($itemlist);

    	$this->assign('item_list', $itemlist);
    	$this->assign('distance', $this->_distance);
    	$this->assign('spage_max', $this->_spage_max);


    	$page = $this->_get_pages($this->_spage_size);

    	$page['item_count'] = $this->_userphoto_mod->getCount();
    	$this->_format_page($page);
    	$this->assign('filtered', $conditions? 1 : 0); //是否有查询条件
    	$this->assign('page_info', $page);
    	$this->assign('or', $order);

    	$acurl = ($cate == 1) ? 'streetlists_ajax' : 'photolist_ajax';
    	$acorurl = ($cate == 1) ? 'streetlist' : 'photolist';
    	$actext = ($cate == 1) ? '个人设计' : '街拍';
    	$infourl = ($cate == 1) ? 'personaldesign' : 'streetinfo';
    	$this->assign('type', $cate);
    	$this->assign('infourl', $infourl);
    	$this->assign('cate', $cate);
    	$this->assign('acorurl', $acorurl);
    	$this->assign('acurl', $acurl);
    	$this->assign('actext', $actext);

    	$this->display(CLUB.'streetlist.html');
    }

    /**
     * 重新处理数据
     *
     * 街拍和列表要关联相册，坚决不能用sql.只能数组关联
     *
     * @param array $data 		原数据
     * @return array new data 	新数据
     * @author v5
     */
    function _formatting_data($data,$is_comments = FALSE){

    	if (!is_array($data)) return array();

    	$commentstype = '';
    	//相册id
    	$albumid = array();
    	$uphotoid = array();
    	foreach ($data as $k=>$v){
    		$albumid[] = $v['album_id'];
    		$uphotoid[] = $v['id'];
    		//     	街拍类型：photolist     	设计类型：streetlist
    		$commentstype = $v['cate'] == 1 ? 'sheji_like' : 'jiepai_like' ;
    	}

    	$this->_album_mod =& m('album');

    	$albumdata =$this->_album_mod->find(array(
    			'conditions'=>db_create_in($albumid,'id'),
    	));

    	if ($is_comments){
	    	/* 评论的id分组评论时间倒序-联表 */
	    	$this->_comments_mod =& m('comments');
			$where = db_create_in($uphotoid,'a.comment_id').' and a.cate="'.$commentstype.'" and status = 1 ';
			$sql ='SELECT a.*'
					.' FROM '. $this->_comments_mod->table.' as a'
					.' where  2 > (select count(*) from '. $this->_comments_mod->table.'  where comment_id = a.comment_id and add_time>a.add_time) '
					.' and '.$where .' ORDER BY a.add_time desc ';
			$commentsdata = $this->_comments_mod->getAll($sql);
    	}

    	$new_data = array();
    	foreach ($data as $key=>$value){
    		$new_data[$key]= $value;

    		$new_data[$key]['infourl'] = ($value['cate'] == 1) ? 'personaldesign' : 'streetinfo';
//     		if($value['cate'] == 1)
//     			$new_data[$key]['infourl'] = getDesignUrl($value['url'],500);
//     		else
//     			$new_data[$key]['infourl'] = getCameraUrl($value['url'],500);

    		$new_data[$key]['a_description']= $albumdata[$value['album_id']]['description'];
    		$cdata = array();


    		/* 是否已喜欢 */
    		if($_SESSION['user_info']['user_id']!=''){
    			$new_data[$key]['is_like']= getLikeByUid($_SESSION['user_info']['user_id'], $value['id'], $commentstype);
    		}else{
    			$new_data[$key]['is_like']=0;
    		}


    		/*评论数据*/
    		if ($is_comments){
	    		if ($commentsdata){
	    			foreach ($commentsdata as $v){
	    				if ($value['id'] == $v['comment_id']){
	    					$cdata[]=$v;
	    				}
	    			}
	    		}
    		}
    		$new_data[$key]['comment_list'] = $cdata;

    	}
    	return $new_data;

    }
    /**
     *	获取分页信息
     *
     *  瀑布流请求次数计算
     *
     *  @author    v5
     *  @return    array
     */
    function _get_pages($page_per = 10,$page=0)
    {

    	if (!$page){
	    	$args = $this->get_params();

	    	$page = empty($args[0]) ? 1 : intval($args[0]);
    	}

    	if ($page > 1){
    		$start = ($page -1) * $page_per * $this->_spage_max;
    		$page_per = $page_per*$this->_spage_max;
    	}else {
    		$page_per = $page_per*$this->_spage_max;
    		$start = ($page -1) * $page_per;
    	}

    	$sp = $_REQUEST['sp']; //子页
    	if ($sp){
    		$start = $this->_spage_size * ($this->_spage_max * ($page - 1) + $sp);
    		$page_per = $this->_spage_size;
    	}
    	return array('limit' => "{$start},{$page_per}", 'curr_page' => $page, 'pageper' => $page_per);
    }

    /**
     * 街拍瀑布流获取数据
     *
     *
     * @author v5
     * @return json
     */
    function photolist_ajax(){
    	$this->streetlists_ajax(2);
    }

    /**
     * 个人设计瀑布流获取数据
     *
     *
     * @author v5
     * @return json
     */
    function streetlists_ajax($cate = 1){
    	$args = $this->get_params();
    	//条件
    	$conditions='';
    	//更新排序
    	if (isset($args[1]) && $args[1] == 'id')
    	{
    		$order = 'id';
    	}else {
    		$order = 'like_num';
    	}

    	$acorurl = ($cate == 1) ? 'streetlist' : 'photolist';
    	$this->assign('acorurl', $acorurl);

    	$infourl = ($cate == 1) ? 'personaldesign' : 'streetinfo';
    	$this->assign('type', $cate);
    	$this->assign('infourl', $infourl);


    	$this->_userphoto_mod =& m('userphoto');

    	$page = $this->_get_pages($this->_spage_size);
//     	var_dump($cate);
    	$photo_list = array();
    	$photo_list = $this->_userphoto_mod->find(array(
    			'conditions' => "cate = $cate and status = 1 ".$conditions,
    			'order' => "id desc",
    			'limit' => $page['limit'],
    	));
    	$resp = '';
    	if ($photo_list){
    		$itemlist = array();
    		$itemlist = $this->_formatting_data($photo_list);
    		$this->assign('item_list', $itemlist);

	    	$resp = $this->_view->fetch(CLUB."waterfall.html");
    	}
    	$data = array(
    	'isfull' => 1,
    	'html' => $resp,
//     	'limit'=>$page['limit'],
//     	'list'=>$itemlist,
//     	'res'=>$photo_list,
//     	'conditions'=>"cate = $cate and status = 1 ".$conditions,
//     	'order'=>"$order desc",
//     	'pm'=>$args[1]

    	);
    	ajaxReturn(1, '', $data);
    }


    /**
     * ajax登录弹层
     *
     * 关注、喜欢 操作需要登录才能完成
     *
     * @author v5
     * @return JSON
     */
   function ajax_login(){
   	$this->assign('item_list', array());
   	$resp = $this->_view->fetch(CLUB.'/ajax_login.html');
   	ajaxReturn(1, '', $resp);

   }

   /**
    * ajax登录操作
    *
    * 关注、喜欢 操作需要登录才能完成
    *
   	* @author v5
    * @return JSON
    */
   function  ajax_do_login(){
   		$ret_url = rawurlencode($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);
   	    $user_name = trim($_POST['username']);
   	    $password  = $_POST['pwd'];
   	    $remember = $_POST['remember'];
   	    $ms =& ms();
   	    $user_id = $ms->user->auth($user_name, $password,true);
   	    if (!$user_id)
   	    {/* 验证失败 */
   	        	ajaxReturn('-1', Lang::get('login_error'), array());
   	    }


   	    $mod_user =& m('member');
   	    $user_info = $mod_user->get(array(
   	            'conditions' => $user_id,
   	            'join'       => 'manage_mall',
   	            'fields'     => '*'
   	    ));

   	    // if (!$user_info['privs'])
   	    // {
   	    //     echo ecm_json_encode(array('flag' => '-1'));
   	    //     return;
   	    // }

   	    /* 分派身份 */
   	    $this->visitor->assign($user_info);

   	    //记住密码-待完善
   	    if ($remember) {
   	    	$time = 3600 * 24 * 14; //两周
//    	    	ecm_setcookie('user_info', array('id'=>$user_info['user_id'], 'password'=>$user_info['password']), $time);
   	    	ecm_setcookie('user_remember',$user_info['user_name'], $time);
   	    	//cookie('user_info', array('id'=>$user_info['user_id'], 'password'=>$user_info['password']), $time);
   	    }

   	    /* 更新登录信息 */
   	    $time = gmtime();
   	    $ip   = real_ip();
   	    $mod_user->edit($user_id, "last_login = '{$time}', last_ip='{$ip}', logins = logins + 1");

   	    /* 更新购物车中的数据 */
   	    $mod_cart =& m('cart');
   	    $mod_cart->edit("user_id = '{$user_id}' OR session_id = '" . SESS_ID . "'", array(
   	    		'user_id'    => $user_id,
   	    		'session_id' => SESS_ID,
   	    ));

   	    ajaxReturn(1, Lang::get('login_ok'), array($ret_url));
   }

   /**
    * 获取用户卡片信息
    *
    * ajax 获取用户信息；验证关注关系
    *
    * @author v5
    * @return JSON
    */
   function ajax_cardinfo(){
   	$args = $this->get_params();
   	$uid = empty($args[0]) ? 0 : intval($args[0]);
   	$info = getUinfoByUid($uid);
   	if (!$info) {
   		ajaxReturn(0, Lang::get('follow_invalid_user'));
   	}

   	$user_follow_mod =& m('userfollow');

   	$user = $this->visitor->get();

   	$this->assign('visitor', $user);

   	//ship   0:互相未关注 1:我关注对方 2:互相关注
   	$info['ship'] = 0;
   	if ($this->visitor->has_login) {
   		$ship = $user_follow_mod->get(array('uid' => $user['user_id'], 'follow_uid' => $uid));
   		if ($ship) {
   			$info['ship'] = $ship['mutually'] ? 2 : 1;
   		}
   	}


   	$this->assign('user', $info);

   	$resp = $this->_view->fetch(CLUB."/card_info.html");
   	ajaxReturn(1, '', $resp);
   }

   /**
    *
    * 喜欢（点赞）
    *
    *
    * @author v5
    * @param  JSON
    */
   	function ajax_like(){
   		$user = $this->visitor->get();

   		if (!$user['user_id'] ) ajaxReturn(0, Lang::get('like_login'));

   		$args = $this->get_params();
   		$cate = $args[1];
   		$like_id = $args[0];

   		if (!$cate || !$like_id) ajaxReturn(0, Lang::get('like_param'));

   		//返回json格式重新处理
		$res = setLike($user['user_id'], $like_id, $cate,'pc');

		if ($res['err'] == 1){
			ajaxReturn(0, $res['msg']);
		}else{

			/* 喜欢成功加积分 */
			$p_num = pointTurnNum($cate);

			setPoint($user['user_id'],$p_num,'add',$cate);

			ajaxReturn(1, Lang::get('like_ok'));
		}
   	}



   /**
    * 关注
    *
    * 用户表 关注数，粉丝数
    * user_follow 关注索引表-mutually 1是双方关注 0 是单方
    *
    * A-B 关注的逻辑
    *
    * 1.自己不能关注自己
    * 2.已关注不能重复
    * 3.B是否关注A，如果是將mutually更改为1
    * 4.A为uid insert索引表
    * 5.A的关注+1
    * 6.B的粉丝+1
    *
    * @author v5
    * @return JSON
    */
/*    function ajax_follow(){

   	$uid = $_GET['uid'];

   	!$uid && ajaxReturn(0, Lang::get('follow_invalid_user'));


   	$user = $this->visitor->get();

   	$uid == $user['user_id'] && ajaxReturn(0, Lang::get('follow_self_not_allow'));

   	if (!$this->m->get($uid)) {
   		ajaxReturn(0, Lang::get('follow_invalid_user'));
   	}
   	$user_follow_mod =& m('userfollow');

   	//已经关注？
   	$is_follow = $user_follow_mod->get(array('uid'=>$user['user_id'], 'follow_uid'=>$uid));

   	$is_follow && ajaxReturn(0, Lang::get('user_is_followed'));


   	//关注动作
   	$return = 1;
   	//他是否已经关注我
   	$map = array('uid'=>$uid, 'follow_uid'=>$user['user_id']);
   	$isfollow_me = $user_follow_mod->get($map);
   	$data = array('uid'=>$user['user_id'], 'follow_uid'=>$uid, 'add_time'=>time());
   	if ($isfollow_me) {
   		$data['mutually'] = 1; //互相关注
   		$user_follow_mod->edit($map,array('mutually'=>1)); //更新他关注我的记录为互相关注
   		$return = 2;
   	}
   	if (!$user_follow_mod->add($data)) ajaxReturn(0, Lang::get('follow_user_failed'));

   	//增加我的关注人数
   	$this->m->setInc(array('user_id'=>$user['user_id']),'follows');

   	//增加Ta的粉丝人数
   	$this->m->setInc(array('user_id'=>$uid),'fans');

   	//提醒被关注的人 - 预留接口
   	//add_tip($uid, 类型);

   	//把他的微薄推送给我
   	//TODO...是否有必要？
   	ajaxReturn(1, Lang::get('follow_user_success'), $return);
   } */


   /**
    * 取消关注
    *
    * @author v5
    * @return JSON
    */
 /*   public function ajax_unfollow() {
   	$uid = $_GET['uid'];

   	!$uid && ajaxReturn(0,Lang::get('unfollow_invalid_user'));

   	$user_follow_mod =& m('userfollow');

   	$user = $this->visitor->get();

   	if ($user_follow_mod->drop('uid='.$user['user_id'].' and follow_uid='.$uid)) {
   		//他是否已经关注我
   		$map = array('uid'=>$uid, 'follow_uid'=>$user['user_id']);
   		$isfollow_me = $user_follow_mod->get($map);
   		if ($isfollow_me) {
   			$user_follow_mod->edit($map,array('mutually'=>0)); //更新他关注我的记录为互相关注
   		}
   		//减少我的关注人数
   		$this->m->setDec(array('user_id'=>$user['user_id']),'follows');

   		//减少Ta的粉丝人数
   		$this->m->setDec(array('user_id'=>$uid),'fans');

   		ajaxReturn(1,Lang::get('unfollow_user_success'));

   	} else {
   		ajaxReturn(0,Lang::get('unfollow_user_failed'));
   	}
  } */

  /******************************************************************************************************************/
  //-------------------------------------------	@author v5  End------------------------------------------------------
  /******************************************************************************************************************/

	function getCntPhotoIndex($cate,$hot_limit){
		if($cate == 1)
			$cate_str = "sheji_like";
		else
			$cate_str = "jiepai_like";
	  	$db = &db();

	  	$hot = $this->photo->getByCateUid($cate, '' , ' like_num desc', $hot_limit , 1);

//        if( $cate_str == "jiepai_like")
//            var_dump($hot[0]);
	  	if($hot)
		  	foreach($hot as $k=>$v){
                //用户已登录
                if($this->visitor->info['user_id']){
                    //判断用户是否已喜欢了此条数据
                    $hot[$k]['like'] = getLikeByUid($this->visitor->info['user_id'], $v['id'], $cate_str);
                    //此条数据，是否为当前登录用户自己上传
                    if( $v['uid'] == $this->visitor->info['user_id'] ){
                        $hot[$k]['self'] = 1;
                    }else{
                        $hot[$k]['self'] = 0;
                        $hot[$k]['isFans'] = isFans($this->visitor->info['user_id'],$v['uid']);
                    }
                }

		  		$hot[$k]['user'] = getUinfoByUid($v['uid']);

		  		if (!$hot[$k]['user']['uid']){
		  			unset($hot[$k]);
		  			continue;
		  		}

		  		if($hot[$k]['user']){
                    //用户个人签名
		  			if(!$hot[$k]['user']['signature'])
		  				$hot[$k]['user']['signature'] = '';
// 		  				$hot[$k]['user']['signature'] = '这家伙很懒...';
		  		}
                //当前图片是否加入了相册
		  		if($v['album_id']){
		  			$album = $this->album->getById($v['album_id']);
		  			if( $album['description'])
		  				$hot[$k]['desc'] = $album['description'];
		  			else
		  				$hot[$k]['desc'] ="";
// 		  				$hot[$k]['desc'] ="这家伙很懒，啥都没留下...";
		  		}else{
		  			$hot[$k]['desc'] = "";
// 		  			$hot[$k]['desc'] = "这家伙很懒，啥都没留下...";
		  		}
                //图片地址
		  		if($cate == 1)
		  			$hot[$k]['url'] = getDesignUrl($v['url'],500);
		  		else
		  			$hot[$k]['url'] = getCameraUrl($v['url'],500);
		  	}

	  	return $hot;
	  }
	  //修改个人资料-省市-AJAX级联
	function getCity(){
	  	$id = $_REQUEST['id'];
	  	$db = &db();
	  	$sql = "select * from cf_region where parent_id= $id";
	  	$rs = $db->getAll($sql);
	  	echo json_encode($rs);
	}

	function getCommends($pic_id,$cate){
		$page = $_REQUEST['page'];
		if(!$page)
			$page = 1;

		$this->assign('next_page',$page + 1);

		$comment_db = getCommentPage($pic_id, $cate ,$page);
		$comment = $comment_db['data'];
		$page = $comment_db['page'];

		$this->assign('comment_more',0);
		if($comment){
			foreach($comment as $k=>$v){
				$comment[$k]['user'] = getUinfoByUid($v['uid']);
				if($v['add_time'])
					$comment[$k]['time'] = TimeFormat($v['add_time']);
				//替换表情符号
				$comment[$k]['content'] = $this->replace_em($comment[$k]['content']);

			}
			if($page->mCurrPage < $page->mTotalPage ){
				$this->assign('comment_more',1);
			}

			$this->assign('comment_total',count($comment) - 1);
		}else{
			$this->assign('comment_total',0 );
		}

		$this->assign('comment',$comment);
	}


	function replace_em($str){
		$str = preg_replace('/\[em_([0-9]*)\]/','<img src="/themes/mall/default/styles/default/images/arclist/$1.gif" border="0" />',$str);
		return $str;
	}

	//评论分页
	function ajaxComment(){
		$cate = $_REQUEST['cate'];
		$id = $_REQUEST['id'];
		$page_num = $_REQUEST['page'];

		if(!$page_num)
			$page_num = 1;

		$comment_db = getCommentPage($id,$cate,$page_num);
		$comment = $comment_db['data'];
		$page = $comment_db['page'];

		$this->assign('comment_more',0);
		$str = "";
		if($comment){
			$next_page = $page_num + 1;
			foreach($comment as $k=>$v){
				$comment[$k]['user'] = getUinfoByUid($v['uid']);
				if($v['add_time'])
					$comment[$k]['time'] = TimeFormat($v['add_time']);

			}
			$last_d = $page_num * 2  - 1 ;
			foreach($comment as $k=>$v){
				$content = $this->replace_em($comment[$k]['content']);

				$user = getUinfoByUid($v['uid']);
				$time = TimeFormat($v['add_time']);
				$str .= '<dl id="comment_div_'.$last_d.'">
                          <dt><a href="/index.php/club-cooler-'.$v['uid'].'.html"><img src="'.$v['avatar'].'" width="36" height="36" /></a></dt>
                          <dd>
                             <p class="p1"><a href="/index.php/club-cooler-'.$v['uid'].'.html">'.$user['user_name'].'</a></p>
                             <p class="p2">'.$content.'<a href="javascript:void(0);" class="hf">回复</a><font>'.$time.'</font></p>
                          </dd>
                      </dl>';

				$last_d++ ;
			}
			$more = "";
			if($page->mCurrPage < $page->mTotalPage ){
				$more = '<a href="javascript:void(0);" onclick=ajaxPage('.$id . "," . $next_page.')>查看更多</a>';
			}

			$rs = array('data'=>$str,'more'=>$more,'last_id'=>$last_d);
			echo json_encode($rs);
		}

	}


	function setUserNavNum($uid){
		$db = &db();
		$sql = "select count(*) as total from cf_album where cate = 1 and uid = ".$uid;
		$row = $db->getRow($sql);
		$album_num = $row['total'];


		$photo_num = $this->photo->cntByCateUid(1,$uid);
		$camera_num = $this->photo->cntByCateUid(2,$uid);

		/* add by v5 喜欢只统计街拍、设计 */
		$conditions = ' and cate in ("jiepai_like","sheji_like")';
		$sql = "select count(*) as total from cf_like where  uid = ".$uid.$conditions;
		$row = $db->getRow($sql);
		$like_num = $row['total'];

		$uinfo = getUinfoByUid($uid);
		$fans_num =  $uinfo['fans'];

		$this->assign('album_num',$album_num);
		$this->assign('photo_num',$photo_num);
		$this->assign('camera_num',$camera_num);
		$this->assign('like_num',$like_num);
		$this->assign('fans_num',$fans_num);


	}


	function initUinfo(){
		$uid = $_REQUEST['uid'];
		if(!$uid){
			$args = $this->get_params();
			if(ACT == 'hwaterfall'){
				$uid = $args[2];
			}else{
				$uid = $args[0];
			}
			if(!$uid){
				$uid = $this->visitor->info['user_id'];
				if(!$uid)
					$this->msg('uid 为空');
			}
		}
		$this->assign('uid',$uid);
		$uinfo = getUinfoByUid($uid);
		if(!$uinfo)
			$this->msg('uid错误，会员不存在');

		$this->assign('uinfo', $uinfo);

		if($this->visitor->info['user_id'] == $uid)
			$this->assign('is_self', 1);
		else{
			$isFans = isFans($this->visitor->info['user_id'],$uid);
			$this->assign('isFans', $isFans);
		}



		return $uid;
	}

}

?>
