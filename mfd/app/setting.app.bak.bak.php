<?php
define('UPLOAD_DIR', 'data/files/mall/settings');

/**
 * 基本设置控制器
 *
 * @author Hyber
 *         @usage none
 */
class SettingApp extends BackendApp
{

    function __construct()
    {
        $this->SettingApp();
    }

    function SettingApp()
    {
        parent::BackendApp();
        $_POST = stripslashes_deep($_POST);
    }

    /**
     * 系统设置
     *
     * @author Hyber
     * @return void
     */
    function base_setting()
    {
        $model_setting = &af('settings');
        $setting = $model_setting->getAll(); // 载入系统设置数据
        $ms = & ms();
        $feed_enabled = $ms->feed->feed_enabled();
        
        if ($feed_enabled) {
            $_feed_list = array(
                'store_created' => Lang::get('feed_store_created.name'),
                'order_created' => Lang::get('feed_order_created.name'),
                'goods_collected' => Lang::get('feed_goods_collected.name'),
                'store_collected' => Lang::get('feed_store_collected.name'),
                'customs_collected' => Lang::get('feed_customs_collected.name'), // ns add 基本款
                'goods_evaluated' => Lang::get('feed_goods_evaluated.name'),
                'groupbuy_joined' => Lang::get('feed_groupbuy_joined.name'),
                'goods_created' => Lang::get('feed_goods_created.name'),
                'groupbuy_created' => Lang::get('feed_groupbuy_created.name')
            );
        }
        
        if (! IS_POST) {
            $time_zone = $model_setting->_get_time_zone();
            $this->assign('time_zone', $time_zone);
            /* Config */
            $config_file = ROOT_PATH . '/data/config.inc.php';
            $config = include ($config_file);
            $setting['session_type'] = $config['SESSION_TYPE'];
            $setting['session_memcached'] = $config['SESSION_MEMCACHED'];
            $setting['cache_server'] = $config['CACHE_SERVER'];
            $setting['cache_memcached'] = $config['CACHE_MEMCACHED'];
            $this->assign('setting', $setting);
            if ($feed_enabled) {
                $this->assign('default_feed_config', Conf::get('default_feed_config'));
                $this->assign('feed_items', $_feed_list);
            }
            $this->assign('feed_enabled', $feed_enabled);
            
            $this->display('setting/setting.base_setting.html');
        } else {
            $images = array(
                'default_goods_image',
                'default_store_logo',
                'default_user_portrait'
            );
            $image_urls = $this->_upload_images($images);
            foreach ($images as $image) {
                isset($image_urls[$image]) && $data[$image] = $image_urls[$image];
            }
            
            $data['time_zone'] = $_POST['time_zone'];
            $data['time_format_simple'] = $_POST['time_format_simple'];
            $data['time_format_complete'] = $_POST['time_format_complete'];
            $data['price_format'] = $_POST['price_format'];
            $data['rewrite_enabled'] = ($_POST['rewrite_enabled'] == '1');
            $data['shoucang'] = $_POST['shoucang'];
            $data['liuxing_like'] = $_POST['liuxing_like'];
            $data['follow'] = $_POST['follow'];
            $data['proecss_profile'] = $_POST['proecss_profile'];
            $data['series_comment'] = $_POST['series_comment'];
            $data['dingzhi_comment'] = $_POST['dingzhi_comment'];
            $data['dingzhi_like'] = $_POST['dingzhi_like'];
            $data['zhuti_like'] = $_POST['zhuti_like'];
            $data['jiepai_like'] = $_POST['jiepai_like'];
            $data['sheji_like'] = $_POST['sheji_like'];
            $data['jiepai_comment'] = $_POST['jiepai_comment'];
            $data['sheji_comment'] = $_POST['sheji_comment'];
            $data['check_comment'] = $_POST['check_comment'];
            $data['jiepai_reward'] = $_POST['jiepai_reward'];
            $data['sheji_reward'] = $_POST['sheji_reward'];
            $data['order_reward'] = $_POST['order_reward'];
            $data['login_reward'] = $_POST['login_reward'];
            $data['sheji_order'] = $_POST['sheji_order'];
            $data['jiepai_order'] = $_POST['jiepai_order'];
            
            $data['order_dk_point'] = $_POST['order_dk_point'];
            $data['order_dk_coin'] = $_POST['order_dk_coin'];
            
            //$data['measure_fee'] = $_POST['measure_fee'];
            
            $data['order_if_review'] = $_POST['order_if_review'];
            
            
            if ($feed_enabled) {
                $_default_feed_list = array();
                foreach ($_feed_list as $key => $_v) {
                    $_default_feed_list[$key] = 0;
                }
                $data['default_feed_config'] = array_merge($_default_feed_list, (array) $_POST['default_feed_config']);
            }
            $model_setting->setAll($data);
            
            /* config info */
            
            /* 初始化 */
            $session_type = $_POST['session_type'];
            $session_memcached = trim($_POST['session_memcached']);
            $cache_server = $_POST['cache_server'];
            $cache_memcached = trim($_POST['cache_memcached']);
            
            /* Config */
            $config_file = ROOT_PATH . '/data/config.inc.php';
            $config = include ($config_file);
            $config['SESSION_TYPE'] = $session_type;
            $config['SESSION_MEMCACHED'] = $session_memcached;
            $config['CACHE_SERVER'] = $cache_server;
            $config['CACHE_MEMCACHED'] = $cache_memcached;
            $new_config = var_export($config, true);
            
            /* 写入 */
            file_put_contents($config_file, "<?php\r\n\r\nreturn {$new_config};\r\n\r\n?>");
            //var_dump(__FUNCTION__);exit();
            $conditions=array_merge($data,$config);
            $this->operation_log(__FUNCTION__,$conditions);
            $this->show_message('edit_base_setting_successed');
        }
    }

    /**
     * 基本信息
     *
     * @author Hyber
     * @return void
     */
    function base_information()
    {
        $model_setting = &af('settings');
        $setting = $model_setting->getAll(); // 载入系统设置数据
        if (! IS_POST) {
            $this->assign('setting', $setting);
            $this->display('setting/setting.base_information.html');
        } else {
            $images = array(
                'site_logo'
            );
            $image_urls = $this->_upload_images($images);
            foreach ($images as $image) {
                isset($image_urls[$image]) && $data[$image] = $image_urls[$image];
            }
            
            $data['site_name'] = $_POST['site_name'];
            $data['site_title'] = $_POST['site_title'];
            $data['site_description'] = $_POST['site_description'];
            $data['site_keywords'] = $_POST['site_keywords'];
            // $data['copyright'] = $_POST['copyright'];
            $data['icp_number'] = $_POST['icp_number'];
            // $data['site_region'] = $_POST['site_region'];
            // $data['site_address'] = $_POST['site_address'];
            // $data['site_postcode'] = $_POST['site_postcode'];
            // $data['site_phone_tel'] = $_POST['site_phone_tel'];
            // $data['site_email'] = $_POST['site_email'];
            // $data['page_size'] = $_POST['page_size'];
            $data['site_status'] = $_POST['site_status'];
            $data['closed_reason'] = $_POST['closed_reason'];
            $data['hot_search'] = $_POST['hot_search'];
            $data['if_photo'] = $_POST['if_photo'];
            
            $comma = Lang::get('comma');
            $data['hot_search'] = str_replace($comma, ',', $data['hot_search']);
            $data['hot_search'] = preg_replace('/\s*,\s*/', ',', $data['hot_search']);
            $data['bonus'] = $_POST['bonus'];
            $data['commission'] = $_POST['commission'];
			$data['initial'] = $_POST['initial'];
			$data['service_phone'] = $_POST['service_phone'];
            $data['bodycost'] = $_POST['bodycost'];
            
            $model_setting->setAll($data);
            $this->operation_log(__FUNCTION__,$data);
            $this->show_message('edit_base_information_successed');
        }
    }

    /**
     * EMAIL 设置
     *
     * @author Hyber
     * @return void
     */
    function email_setting()
    {
        $model_setting = &af('settings');
        $setting = $model_setting->getAll(); // 载入系统设置数据
        if (! IS_POST) {
            $this->assign('setting', $setting);
            $this->assign('mail_type', array(
                MAIL_PROTOCOL_SMTP => Lang::get('smtp'),
                MAIL_PROTOCOL_LOCAL => Lang::get('email')
            ));
            $this->display('setting/setting.email_setting.html');
        } else {
            $data['email_type'] = $_POST['email_type'];
            $data['email_host'] = $_POST['email_host'];
            $data['email_port'] = $_POST['email_port'];
            $data['email_addr'] = $_POST['email_addr'];
            $data['email_id'] = $_POST['email_id'];
            $data['email_pass'] = $_POST['email_pass'];
            $data['email_test'] = $_POST['email_test'];
            $model_setting->setAll($data);
            $this->operation_log(__FUNCTION__,$data);
            $this->show_message('edit_email_setting_successed');
        }
    }

    /**
     * 验证码设置
     *
     * @author Hyber
     * @return void
     */
    function captcha_setting()
    {
        $model_setting = &af('settings');
        $setting = $model_setting->getAll(); // 载入系统设置数据
        if (! IS_POST) {
            $this->assign('setting', $setting);
            $this->display('setting/setting.captcha_setting.html');
        } else {
            $data['captcha_status'] = empty($_POST['captcha_status']) ? array() : $_POST['captcha_status'];
            // $data['captcha_error_login'] = $_POST['captcha_error_login'];
            $model_setting->setAll($data);
            $this->operation_log(__FUNCTION__,$data);
            $this->show_message('edit_captcha_setting_successed');
        }
    }

    /**
     * 开店设置
     *
     * @author Hyber
     * @return void
     */
    function store_setting()
    {
        $model_setting = &af('settings');
        $setting = $model_setting->getAll(); // 载入系统设置数据
        if (! IS_POST) {
            $this->assign('setting', $setting);
            $this->display('setting/setting.store_setting.html');
        } else {
            $data['store_allow'] = $_POST['store_allow'];
            // $data['store_need_papers'] = $_POST['store_need_papers'];
            // $data['store_free_days'] = $_POST['store_free_days'];
            // $data['store_allowed_goods'] = $_POST['store_allowed_goods'];
            // $data['store_allowed_files'] = $_POST['store_allowed_files'];
            $model_setting->setAll($data);
            $this->operation_log(__FUNCTION__,$data);
            $this->show_message('edit_store_setting_successed');
        }
    }

    /**
     * 信用评价设置
     *
     * @author Hyber
     * @return void
     */
    function credit_setting()
    {
        $model_setting = &af('settings');
        $setting = $model_setting->getAll(); // 载入系统设置数据
        if (! IS_POST) {
            $this->assign('setting', $setting);
            $this->display('setting/setting.credit_setting.html');
        } else {
            // $data['min_goods_amount'] = $_POST['min_goods_amount'];
            // $data['valid_transations'] = $_POST['valid_transations'];
            // $data['buy_interval_days'] = $_POST['buy_interval_days'];
            // $data['plus_base'] = $_POST['plus_base'];
            $data['upgrade_required'] = $_POST['upgrade_required'];
            // $data['auto_evaluate'] = $_POST['auto_evaluate'];
            $model_setting->setAll($data);
            $this->operation_log(__FUNCTION__,$data);
            $this->show_message('edit_credit_setting_successed');
        }
    }
    
    /**
     * 抵用券
     *
     * @author liang.li
     * @return void
     */
    function profit_setting()
    {
        $type = array(
            '0003' => '西服',
            '0004' => '西裤',
            '0005' => '马甲',
            '0006' => '衬衣',
            '0007' => '大衣',
        );
        
        $cate = array(
            '1' => '指定有限天数',
            '2' => '指定有限日期',
        );
        
        
        $this->assign('type',$type);
        $this->assign('cate',$cate);
        
        
        $model_setting = &af('settings');
        $setting = $model_setting->getAll(); // 载入系统设置数据
//         $setting['debit_time'] = '';
        if (! IS_POST) 
        {
//      dump($setting);
            if (($setting['debit_cate'] == 2) && $setting['debit_time']) 
            {
                $setting['debit_time'] = date("Y-m-d",$setting['debit_time']);
            }
            
            if (($setting['debit_cate2'] == 2) && $setting['debit_time2'])
            {
                $setting['debit_time2'] = date("Y-m-d",$setting['debit_time2']);
            }
            
            if (($setting['debit_cate_o'] == 2) && $setting['debit_time_o'])
            {
                $setting['debit_time_o'] = date("Y-m-d",$setting['debit_time_o']);
            }
            
// dump($setting);            
            $this->assign('setting', $setting);
            $this->display('setting/setting.profit_setting.html');
        } 
        else 
        {
//      dump($_POST);
            $data['debit_num'] = $_POST['debit_num'];
            $data['debit_name'] = $_POST['debit_name'];
            $data['debit_type'] = $_POST['debit_type'];
            $data['debit_cate'] = $_POST['debit_cate'];
            
            $data['debit_num2'] = $_POST['debit_num2'];
            $data['debit_name2'] = $_POST['debit_name2'];
            $data['debit_type2'] = $_POST['debit_type2'];
            $data['debit_cate2'] = $_POST['debit_cate2'];
            
            $data['debit_num_o'] = $_POST['debit_num_o'];
            $data['debit_name_o'] = $_POST['debit_name_o'];
            $data['debit_type_o'] = $_POST['debit_type_o'];
            $data['debit_cate_o'] = $_POST['debit_cate_o'];
            $data['debit_order_o'] = $_POST['debit_order_o'];
            
            $data['debit_gift'] = $_POST['debit_gift'];
            
            
            if ($data['debit_cate'] == 2) 
            {
                $time = strtotime($_POST['debit_time2']);
            }
            else 
            {
                $time = $_POST['debit_time1'];
            }
            $data['debit_time'] = $time;
            
            if ($data['debit_cate2'] == 2)
            {
                $time2 = strtotime($_POST['debit_time22']);
            }
            else 
            {
                $time2 = $_POST['debit_time21'];
            }
            $data['debit_time2'] = $time2;
            
            if ($data['debit_cate_o'] == 2)
            {
                $time_o = strtotime($_POST['debit_time_o2']);
            }
            else 
            {
                $time_o = $_POST['debit_time_o1'];
            }
            $data['debit_time_o'] = $time_o;
           
//  dump($data);
            $model_setting->setAll($data);
            $this->operation_log(__FUNCTION__,$data);
            $this->show_message('edit_credit_setting_successed');
        }
    }


    /**
     * 订单交易额设定
     *
     * @author tsj
     * @return void
     */
    function creditscore_setting()
    {
        $model_setting = &af('settings');
        $setting = $model_setting->getAll(); // 载入系统设置数据
        
        if (! IS_POST) 
        {
             $this->assign('setting', $setting);
            $this->display('setting/setting.creditscore_setting.html');
        } 
        else 
        {
            $data['o_chenyi1'] = $_POST['o_chenyi1'];
            $data['o_1'] = $_POST['o_1'];
            $data['o_2'] = $_POST['o_2'];
            $data['o_3'] = $_POST['o_3'];
            $data['o_4'] = $_POST['o_4'];
            $model_setting->setAll($data);
            $this->operation_log(__FUNCTION__,$data);
            
            $this->show_message('修改成功');
        }
            
           
    }

    /**
     * 删除创业者等级
     *
     * @author tsj
     * @return void
     */
    
    function delcreditscore(){
        $id = isset($_GET['id'])? trim($_GET['id']):'';
        $credit_mod = & m('creditscore');
        $isdel=$credit_mod ->drop("id=".$id);
        
        if($isdel){
            $this->show_message('删除成功');
        }else 
        {
            $this->show_message('删除失败');
        }
         
    }

    /**
     * 二级域名设置
     *
     * @author Garbin
     * @return void
     */
    function subdomain_setting()
    {
        $model_setting = &af('settings');
        $setting = $model_setting->getAll(); // 载入系统设置数据
        if (! IS_POST) {
            $this->assign('setting', $setting);
            $this->assign('config', array(
                'enabled_subdomain' => ENABLED_SUBDOMAIN,
                'subdomain_suffix' => defined('SUBDOMAIN_SUFFIX') ? SUBDOMAIN_SUFFIX : ''
            ));
            $this->assign('yes_or_no', array(
                Lang::get('no'),
                Lang::get('yes')
            ));
            $this->display('setting/setting.subdomain_setting.html');
        } else {
            /* 初始化 */
            $subdomain_reserved = empty($_POST['subdomain_reserved']) ? '' : trim($_POST['subdomain_reserved']);
            $subdomain_length = empty($_POST['subdomain_length']) ? '' : trim($_POST['subdomain_length']);
            $enabled_subdomain = empty($_POST['enabled_subdomain']) ? 0 : intval($_POST['enabled_subdomain']);
            $subdomain_suffix = empty($_POST['subdomain_suffix']) ? '' : trim($_POST['subdomain_suffix']);
            
            /* Setting */
            $data['subdomain_reserved'] = $subdomain_reserved;
            $data['subdomain_length'] = $subdomain_length;
            
            /* Config */
            $config_file = ROOT_PATH . '/data/config.inc.php';
            $config = include ($config_file);
            $config['ENABLED_SUBDOMAIN'] = $enabled_subdomain;
            $config['SUBDOMAIN_SUFFIX'] = $subdomain_suffix;
            $new_config = var_export($config, true);
            
            /* 写入 */
            $model_setting->setAll($data);
            file_put_contents($config_file, "<?php\r\n\r\nreturn {$new_config};\r\n\r\n?>");
            $conditions=array_merge($data,$config);
            $this->operation_log(__FUNCTION__,$conditions);
            $this->show_message('edit_subdomain_setting_successed');
        }
    }

    /**
     * 上传默认商品图片、默认店铺标志、默认会员头像
     *
     * @author Hyber
     * @param array $images            
     * @return array
     */
    function _upload_images($images)
    {
        import('uploader.lib');
        $image_urls = array();
        
        foreach ($images as $image) {
            $file = $_FILES[$image];
            if ($file['error'] != UPLOAD_ERR_OK) {
                continue;
            }
            $uploader = new Uploader();
            $uploader->allowed_type(IMAGE_FILE_TYPE);
            $uploader->addFile($file);
            if ($uploader->file_info() === false) {
                continue;
            }
            $uploader->root_dir(ROOT_PATH);
            $image_urls[$image] = $uploader->save(UPLOAD_DIR, $image);
        }
        
        return $image_urls;
    }

    function send_test_email()
    {
        if (IS_POST) {
            $email_from = Conf::get('site_name');
            $email_type = $_POST['email_type'];
            $email_host = $_POST['email_host'];
            $email_port = $_POST['email_port'];
            $email_addr = $_POST['email_addr'];
            $email_id = $_POST['email_id'];
            $email_pass = $_POST['email_pass'];
            $email_test = $_POST['email_test'];
            $email_subject = Lang::get('email_subjuect');
            $email_content = Lang::get('email_content');
            
            /* 使用mailer类 */
            import('mailer.lib');
            $mailer = new Mailer($email_from, $email_addr, $email_type, $email_host, $email_port, $email_id, $email_pass);
            $mail_result = $mailer->send($email_test, $email_subject, $email_content, CHARSET, 1);
            if ($mail_result) {
                $this->json_result('', 'mail_send_succeed');
            } else {
                $this->json_error('mail_send_failure', implode("\n", $mailer->errors));
            }
        } else {
            $this->show_warning('Hacking Attempt');
        }
    }
    //zxr add 后台文件修改录入后台日志
    function operation_log($act,$conditions){
    	$visitor=&env('visitor');
    	include(ROOT_PATH.'/mfd/includes/operation.inc.php');
    	Lang::load(lang_file('admin/admin'));
    	$app='setting';	
    	$operation_mod=&m('operatorloglogs');
    	$logdata=array();
    	$logdata['conditions'] = serialize($conditions);
    	if (!$visitor->info['user_id']){
    		$member_mod=&m('member');
    		$row=$member_mod->find();
    		$logdata['username'] = $row['user_name'];
    		$logdata['uid'] = $row['user_id'];
    		$logdata['ip'] = $row['last_ip'];
    	}else {
    		$logdata['username'] = $visitor->info['user_name'];
    		$logdata['uid'] = $visitor->info['user_id'];
    		$logdata['ip'] = $visitor->info['last_ip'];
    	}
    	$logdata['dateline'] = time();
    	$logdata['operate_type'] = "数据修改";
    	if ($menu_data){
    		foreach ($menu_data as $k=>$v){
    			foreach ($v as $key=>$r){
    	
    				//$r为数组是strstr参数报错,暂时写了个这玩意儿,但不是长久之计,如果再多一层数组怎么办~  还是抽空写个递归吧... //Ruesin
    				if(is_array($r)){
    					foreach ($r as $kk=>$rr){
    						if(!is_array($rr)){
    							if (stristr($rr,$app)&&stristr($rr,$act)){
    								$logdata['operate_key'] = $GLOBALS['__ECLANG__'][$k].':'.$GLOBALS['__ECLANG__'][$key].':'.$GLOBALS['__ECLANG__'][$kk];
    								break;
    							}
    						}
    					}
    				}else{
    					//var_dump($r);exit;
    					if (stristr($r,$app)&&stristr($r,$act)){
    						$logdata['operate_key'] = $GLOBALS['__ECLANG__'][$k].':'.$GLOBALS['__ECLANG__'][$key];
    						break;
    					}
    				}

    			}
    		}
    	}
    	$logdata['memo']='操作人员：'.$logdata['username'].'在['.date('Y-m-d H:i:s',$logdata['dateline']).']对-'.$logdata['operate_key'].'-执行了'.$logdata['operate_type'].'操作.';
    	$insert_info = $operation_mod->_getInsertInfo($logdata);
    	$this->db=&db();
        $this->db->query("INSERT INTO cf_operatorlog_logs  {$insert_info['fields']} VALUES{$insert_info['values']}");
    }
}

?>
