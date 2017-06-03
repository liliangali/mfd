<?php
//会员账户管理
class Account_logApp extends BackendApp
{
	var $_user_mod;
	var $_lv_mod;
	var $_member_accountlog_mod;
	var $_mod_accountGallery;
    var $sw = 80;
    var $sh = 80;
    var $mw = 300;
    var $mh = 300;

    function __construct()
    {
        $this->Account_logApp();
    }

    function Account_logApp()
    {
        parent::__construct();
        $this->_user_mod =& m('member');
        $this->_lv_mod =& m('memberlv');
        $this->member_accountlog_mod =& m('member_accountlog');
        $this->_mod_accountGallery = &m("member_accountgallery");
        
    }

    function index(){
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        /* 是否存在 */
        $user = $this->_user_mod->get_info($id);
        if (!$user)
        {
            $this->show_warning('user_empty');
            return;
        }  
        //等级信息
        $_lv_mod_list = $this->_lv_mod->find(array(
            'conditions' => "lv_type = 'supplier'",
            'fields' => 'name',
        ));
        $user['lv_name_info'] = $_lv_mod_list[$user['member_lv_id']]['name'];

        //获取后台更改记录
        $page = $this->_get_page(5);
        //$member_accountlog_mod =& m('member_accountlog');
        $accountlog = $this->member_accountlog_mod->find(array(
            'conditions'=>'user_id='.$user['user_id'],
            'count' => true,
            'limit' => $page['limit'],
            'order' => 'add_time DESC',
        ));
        foreach($accountlog as $k=>$v){
         /*    if($v['img_url']){
            $accountlog[$k]['img_url'] =  SITE_URL.'/upload/account_img/'.$v['img_url'];
          
            $accountlog[$k]['img_url_y'] = SITE_URL.'/upload/account_img/'.str_replace(array('thumb_100_100_'),"",$v['img_url']);
            } */
            $images = $this->_mod_accountGallery->find("custom_id='{$v['id']}'");
            $accountlog[$k]['imgs'] = $images;
            
            $accountlog[$k]['lv_id'] = empty($v['lv_id']) ? '无变化' : $_lv_mod_list[$v['lv_id']]['name'];
        }
       
        $page['item_count'] = $this->member_accountlog_mod->getCount();
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('accountlog',$accountlog);

        //用户信息
        $this->assign('user', $user);

        $this->display('account_log.index.html');
    }

    function add(){
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
       
        /* 是否存在 */
        $user = $this->_user_mod->get_info($id);
        if (!$user)
        {
            $this->show_warning('user_empty');
            return;
        }
        $lv_list = $this->_lv_mod->find("lv_type='supplier'");
        // 查询 该用户是否 调整 过  余额  麦富迪币   等级  auther  tangsj
        
        $accountinfo = $this->member_accountlog_mod->get(array(
            'conditions'=>"user_id='{$id}'",
            'order' => 'add_time DESC',
        ));
      
        
        if(!$_POST){
            //等级信息
        	//ns add 先把内容显示为空
            $this->assign('lv_list', $lv_list);

            //查询最后邀请码
            $z_invite = $this->_user_mod->get(array(
                    'conditions' => "invite IS NOT NULL",
                    'fields' => 'invite',
                    'order' => 'invite DESC'
                ));
            $this->assign('z_invite',$z_invite);
            
            //用户信息
            $this->assign('user', $user);
            // 用户账户调整信息
            $this->assign('account', $accountinfo);
            
            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
            
            
            $template_name = $this->_get_template_name();
            $style_name    = $this->_get_style_name();
            
            $param = array(
                'dir' => 'jp_dissertation',
                'button_text' => "上传文件"
            );
            $this->assign('build_upload', 			       $this->_build_upload($param)); // 构建swfupload上传组件
            
            
            //$this->assign("cats", $this->jpjz_disCats());
            
            $this->assign('build_editor', $this->_build_editor(array(
                'name' => 'content',
                'content_css' => SITE_URL . "/themes/mall/{$template_name}/styles/{$style_name}/css/RCTailor.css"
            )));
            $this->display('account_log.form.html');
        }else{
            
      
            //$member_accountlog_mod =& m('member_accountlog');
        	$tmp = '用户 '.$this->visitor->get('user_name').' 在后台会员管理对 '.$user['user_name'].' 的账户进行了调整:';
            $data = array(
                'money' => $_POST['money'],   //余额
                'money_type' => $_POST['money_type'],   //余额删减状态
                'point' => $_POST['point'], //积分
                'point_type' => $_POST['point_type'],    //积分删减状态
                'coin' => $_POST['coin'], //麦富迪币
                'coin_type' => $_POST['coin_type'],    //麦富迪币删减状态
                'user_id' => $user['user_id'],
                'admin_id' => $this->visitor->get('user_id'),
                'brief' => $_POST['brief'], //备注
                'add_time' => time(),
            ); 
			$mark='';
			$old_lv=$user['member_lv_id'];
			$new_lv=$_POST['member_lv_id'];
            //进行等级修改
            if($user['member_lv_id'] != $_POST['member_lv_id']){
            	$mark.="会员等级从{$lv_list[$old_lv]['name']}修改成{$lv_list[$new_lv]['name']}";
                $data['lv_id'] = $_POST['member_lv_id']; //用户会员等级变化
                //$this->_user_mod->edit($user['user_id'],array('member_lv_id'=> $_POST['member_lv_id']),0);
            }else{
                //如果等级没有变化。就要进行积分的判断。都没有变化就提示它要进行修改
                //判断。如果3个都没有进行修改
                if($_POST['money'] < 1 && $_POST['point'] < 1 && $_POST['coin'] < 1)
                {
                        if(empty($_POST['invite'])){
                            $this->show_warning('必须选择一项修改！');
                            return;
                        }

                }
            }

            //进行BD码的验证，如果跟当前用户不一样进行修改
            if($user['invite'] != $_POST['invite']){
                $this->_user_mod->edit($user['user_id'],array('invite'=> $_POST['invite'],0));
            }


             //给用户表里添加数据  暂时 屏蔽此接口：此操作 在 财务管理 审核 通过之后 才可以 写入日志表  已经 给账户 添加数据  auther tangsj
            ///$account_common = $this->account_common($data);
            
            //后台财务报表记录后台用户账户修改明细账
            $flag=0;
            $amount=0;
            $abstract=array();
            /* ==================  后台操作日志记录会员账户调整数据  =====================  */

           
           if($_POST['point']>0){
           		if($_POST['point_type']==1){
           			$mark.=",积分(改变前为{$user['point']})增加{$_POST['point']}";
           		}else{
           			$mark.=",积分(改变前为{$user['point']})减少{$_POST['point']}";
           		}
           }
           
           if($_POST['brief']){
           	  $mark.=",备注({$_POST['brief']})";
           }
           $mark.="<a href='index.php?app=account_log&amp;act=index&amp;id={$id}'>&nbsp;&nbsp;&nbsp;&nbsp;<font color='red'><b>详情&gt;&gt;</b></font></a>";
           $mark=$tmp.ltrim($mark,',');
            import('operationlog.lib');
           $type='edit';
           $operate_log=new OperationLog();
           $operate_log->operation_log('',$type,$mark); 
           /* ====================   END   ======================== */
			$a_id = $this->member_accountlog_mod->add($data,false,0);

			if($a_id){
				$finance_sn=$a_id;
			}else{
				$finance_sn='错误日志编号';
			}
			if($flag){
				get_client_finance($user['user_id'], $finance_sn,7,$amount,$abstract);
			}
            if (!$a_id) {       	
                $this->show_warning($this->member_accountlog_mod->get_error());
                return;
            }
            //处理图片上传
            if($_FILES['img_url']['size']){
            // 图片上传类
            include_once ROOT_PATH."/includes/libraries/ImageTool.class.php";
            $imageTool = new ImageTool(ROOT_PATH.'/upload/account_img/');
            $imageTool->_upload_dir=ROOT_PATH.'/upload/account_img/';
                foreach ($_FILES as $k=>$v){
                    if($k == 'img_url')
                    {
                        $upfile[$k]=$imageTool->uploadImage($v);
                        $goods_thumb=$imageTool->makeThumb($imageTool->_upload_dir.$upfile[$k], 100, 100);
                        $upfile['img_url']=$goods_thumb;
                    }
                }
                $this->member_accountlog_mod->edit($a_id,array('img_url'=> $upfile['img_url']));
            }

            $this->addGallery($a_id);

            $this->show_message('添加成功',
                '返回详细列表',    'index.php?app=account_log&amp;act=index&amp;id='.$user['user_id'],
                '继续添加', 'index.php?app=account_log&amp;act=add&amp;id='.$user['user_id']
            );
        }  

    }

    /*用户财务操作。公共方法*/
     private function account_common($data){
         $cash_mod =& m("ordercashlog");
         if($data['money'] > 0){
            $mData = array(
                "user_id"      => $data["user_id"],
                "type"         => "4", //余额
                "minus"        => "7", //后台管理员增加（或减少）余额
                "cash_money"   => $data["money"],
                "admin_id"     => $data['admin_id'],
                "add_time"     => time(),
            );
            //添加余额
            if($data['money_type'] == 1){
                $mData['mark'] = "+";
                $mData['name'] = "后台添加余额";
                //添加余额
                $res =  $this->_user_mod->setInc("user_id='{$data["user_id"]}'", "money", $data["money"]);
                if ($res === false)
                {
                    return false;
                }
            }
            //删减余额
            elseif($data['money_type'] == 2)
            {
                $mData['mark'] = "-";
                $mData['name'] = "后台减少余额";
                //减除余额
                $res = $this->_user_mod->setDec("user_id='{$data["user_id"]}'", "money", $data["money"]);
                if ($res === false)
                {
                    return false;
                }
            }
            //添加日志
            if (!$cash_mod->add($mData,false,0))
            {
                return false;
            }
         }
         if($data['point'] > 0)
         {
            $pData = array(
                "user_id"      => $data["user_id"],
                "type"         => "1", //积分
                "minus"        => "2", //后台管理员增加（或减少）积分
                "cash_money"   => $data["point"],
                "admin_id"     => $data['admin_id'],
                "add_time"     => time(),
            );
            //添加积分
            if($data['point_type'] == 1){
                $pData['mark'] = "+";
                $pData['name'] = "后台添加积分";
                //添加积分
                $res_point =  $this->_user_mod->setInc("user_id='{$data["user_id"]}'", "point", $data["point"]);
                //reloadMember($data["user_id"]);
                if ($res_point === false)
                {
                    return false;
                }
                //升级会员更新等级
                //$relo = reloadMember($data["user_id"]);
            }
            //删减积分
            elseif($data['point_type'] == 2)
            {
                $pData['mark'] = "-";
                $pData['name'] = "后台减少积分";
                //删减积分
                $res_point = $this->_user_mod->setDec("user_id='{$data["user_id"]}'", "point", $data["point"]);
                reloadMember($data["user_id"]);
                if ($res_point === false)
                {
                    return false;
                }
                //进行降级的会员等级更新
                //reloadDowngradeMember($data["user_id"]);
            }
            //添加日志
            if (!$cash_mod->add($pData,false,0))
            {
                return false;
            }

         }
         if($data['coin'] > 0){
            $cData = array(
                "user_id"      => $data["user_id"],
                "type"         => "2", //麦富迪币
                "minus"        => "7", //后台管理员增加（或减少）积分
                "cash_money"   => $data["coin"],
                "admin_id"     => $data['admin_id'],
                "add_time"     => time(),
            );
            //添加麦富迪币
            if($data['coin_type'] == 1){
                $cData['mark'] = "+";
                $cData['name'] = "后台添加麦富迪币";
                //添加麦富迪币
                $res_coin = $this->_user_mod->setInc("user_id='{$data["user_id"]}'", "coin", $data["coin"]);
                if ($res_coin === false)
                {
                    return false;
                }
            }
            //删减麦富迪币
            elseif($data['coin_type'] == 2)
            {
                $cData['mark'] = "-";
                $cData['name'] = "后台减少麦富迪币";
                //删减麦富迪币
                $res_coin = $this->_user_mod->setDec("user_id='{$data["user_id"]}'", "coin", $data["coin"]);
                if ($res_coin === false)
                {
                    return false;
                }
            }
            //添加日志
            if (!$cash_mod->add($cData,false,0))
            {
                return false;
            }
         }
         return true;

    }
    // 添加 账户调整 多图片支持 auther  tangsj
    function addGallery($id){
       
        $this->_mod_accountGallery = &m("member_accountgallery");
        //相册图片
        import("image.func");
        $gallery = array();

        foreach((array)$_POST['gallery'] as $k => $v){

            $f=dirname($v);
            $fname = str_replace($f, '',$v);
            $t = date("YmdHi");
            $sPath = '/upload/thumb/s/'.$t.$fname;
            $mPath = '/upload/thumb/m/'.$t.$fname;

            $s_img = make_thumb($v, ROOT_PATH.$sPath,$this->sw, $this->wh);

            $m_img = make_thumb($v, ROOT_PATH.$mPath,$this->mw, $this->mh);

            if($s_img && $m_img){
                $gallery[] = array(
                    'custom_id' => $id,
                    'small_img'  => $sPath,
                    'middle_img'  => $mPath,
                    'source_img'    => $v,
                );
            }
        }

        if(!empty($gallery)){
            $this->_mod_accountGallery->add($gallery);
        }

        

    }
    // 验证  调整 审核 是否有 最新一次 而且 未审核状态
    function check_account(){
        $user_id = isset($_POST['userid']) ? intval($_POST['userid']) : 0;
        $info = $this->member_accountlog_mod->get(array(
            'conditions'=>"user_id ='{$user_id}'",
            'order'  =>'add_time DESC',
        ));
       
        if($info && $info['status'] ==0){
            echo 0;
        }else{
            echo 1;
        }
        
    }

}  