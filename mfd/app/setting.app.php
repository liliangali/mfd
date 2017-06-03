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
     * diy基本设置
     */

    function base_diy()
    {
        $model_setting = &af('settings');
        $setting = $model_setting->getAll(); // 载入系统设置数据
        if (! IS_POST) {
            $this->assign('setting', $setting);
            $this->display('setting/setting.base_diy.html');
        } else {
            
            $data['diy_aprice'] = $_POST['diy_aprice'];
            $data['diy_ratio'] = $_POST['diy_ratio'];
            $data['diy_maxnum'] = $_POST['diy_maxnum'];
            $data['kg_minnum'] = $_POST['kg_minnum'];
            $data['zhekou_max'] = $_POST['zhekou_max'];
            
            if($_POST['word_val']){
                
                if($_POST['word_val']){
                    foreach ($_POST['word_val'] as $key => $value) {
                        $data['word_libraries'][]=trim($value);
                    }
                }
                
            }
            $model_setting->setAll($data);
            $this->operation_log(__FUNCTION__,$data);
            $this->show_message('edit_base_information_successed');
        }
    }

    //首单减免
    public function forder()
    {
        $model_setting = &af('settings');
        $setting = $model_setting->getAll(); // 载入系统设置数据
        if (!IS_POST)
        {
            if($setting['forder_start_time'])
            {
                $setting['forder_start_time'] = date("Y-m-d",$setting['forder_start_time']);
            }
            if($setting['forder_end_time'])
            {
                $setting['forder_end_time'] = date("Y-m-d",$setting['forder_end_time']);
            }
            $this->assign('setting', $setting);
            $this->display('setting/setting.forder.html');
        }
        else
        {
            $data['forder'] = $_POST['forder'];
            $data['forder_start_time'] = strtotime($_POST['forder_start_time']);
            $data['forder_end_time'] = strtotime($_POST['forder_end_time']);
            $data['forder_money'] = $_POST['forder_money'];
            $data['forder_name'] = $_POST['forder_name'];
            $data['forder_order_money'] = $_POST['forder_order_money'];
            $data['forder_days'] = $_POST['forder_days'];
            $data['forder_category'] = $_POST['forder_category'];

            $model_setting->setAll($data);
            $this->operation_log(__FUNCTION__,$data);
            $this->show_message('edit_base_information_successed');
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
            $data['forder'] = $_POST['forder'];
            $data['forderbeg'] = $_POST['forderbeg'];
            $data['forderend'] = $_POST['forderend'];
            
            $comma = Lang::get('comma');
            $data['hot_search'] = str_replace($comma, ',', $data['hot_search']);
            $data['hot_search'] = preg_replace('/\s*,\s*/', ',', $data['hot_search']);
            $data['bonus'] = $_POST['bonus'];
            $data['commission'] = $_POST['commission'];
			$data['initial'] = $_POST['initial']?$_POST['initial']:0;
			$data['service_phone'] = $_POST['service_phone'];
            $data['bodycost'] = $_POST['bodycost']?$_POST['bodycost']:0;
			
			$data['delay_days'] = $_POST['delay_days'];
			$data['ship_days'] = $_POST['ship_days'];
  
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
     * 麦 券log
     *
     * @author tangsj
     * @return void
     */
    function debit_log()
    {

        $is_used_arr = array(
            0=>'未使用',
            1=>'已使用',
        );


        $is_invalid_arr =array(
            0=>'未失效',
            1=>'已失效'
        );


        $pageye = isset($_GET['page']) ? trim($_GET['page']) : '';
        $conditions = '1=1';
        $conditions .= $this->_get_query_conditions(array(
            array(
                'field' => 'user_name',
                'name' =>  'user_name',
                'equal' => '=',

            ),
            array(
                'field' => 'is_invalid',
                'name' =>  'is_invalid',
                'equal' => '=',
                'type'  => 'numeric',
            ),
            array(
                'field' => 'is_used',
                'name' => 'is_used',
                'equal' => '=',
                'type'  => 'numeric',
            ),
            array(
                'field' => 'debit.add_time',
                'name'  => 'add_time_from',
                'equal' => '>=',
                'handler'=> 'gmstr2time',
            ),array(
                'field' => 'debit.add_time',
                'name'  => 'add_time_to',
                'equal' => '<=',
                'handler'   => 'gmstr2time_end',
            )

        ));


        if (isset($_GET['sort']) && isset($_GET['order'])) {
            $sort = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order, array('asc', 'desc'))) {
                $sort = 'id';
                $order = 'desc';
            }
        } else {
            $sort = 'id';
            $order = 'desc';
        }

        $_debit_mod =&m('debit');
        $page = $this->_get_page(30);

        $list = $_debit_mod->find(array(
            'join'=>'has_member',
            'conditions' => $conditions,
            'limit' => $page['limit'],
            'count' => true,
            'order' => "$sort $order",
        ));


        $_member_mod =&m('member');
        foreach($list as $k=>$v){
            $list[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
            $list[$k]['expire_time'] = date('Y-m-d H:i:s',$v['expire_time']);
            $user_info =$_member_mod->get(array('conditions'=>"user_id='{$v['from_uid']}'",'fields'=>"user_name,nickname"));

            $list[$k]['from_user_name'] = empty($user_info['nickname'])?$user_info['user_name']:$user_info['nickname'];

        }

        $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                                     'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));





        $page['item_count'] = $_debit_mod->getCount();
        $this->_format_page($page);


        $this->assign('is_used_arr', $is_used_arr);
        $this->assign('cate_arr', $this->_get_options());
        $this->assign('is_invalid_arr', $is_invalid_arr);

        $this->assign('page_info', $page);
        $this->assign('list', $list);
        $this->display('debit/log.html');

    }
    /**
     * 优惠券生成
     */
    function voucher_create(){
//        $model_setting = &af('settings');
//        $setting = $model_setting->getAll(); // 载入系统设置数据
        if (!IS_POST)
        {
            $arrdata['date'] = date("Y-m-d",time());
            $this->assign('arrdata', $arrdata);
            $this->display('voucher/voucher.create.html');
        } 
        else 
        {
            $data = $_POST['data'];
            (int)$data['num'];
            (int)$data['money'];
            
            $db = &db();
            // 生成批次记录
            $sql = "insert into `cf_voucher_batch` ( `category`, `end_time`, `status`, `money`, `start_time`, `create_time`, `num`, `name`) "
                    . " values ( '".$data['category']."', '".strtotime($data['end_time'])."', '1', '".$data['money']."', '".strtotime($data['start_time'])."', '".time()."', '".$data['num']."', '".$data['name']."')";
            $db->query($sql);
            $batch_id = $db->insert_id(); // 批次ID
            // 生成详细记录
            for($f=0;$f<$data['num'];$f++){
                do {
                        $string = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
                        $str = '';
                        for ($i=0; $i < 10; $i++) {
                            $str.= $string[rand(0,strlen($string)-1)];
                        }
                        $str = strtoupper($str);
                        $code=$db->getcol("select code from cf_voucher ");
                } while ($code=='');
                $sql = "insert into `cf_voucher` ( "
                                . "`batch_id`, " // 关联批次ID
                                . "`name`, " // 券名称
                                . "`money`, " // 价格
                                . "`create_time`, " // 生成时间
                                . "`start_time`, " // 生效时间
                                . "`end_time`, " // 过期时间
                                . "`code`, " // 激活码 10位 数字加字母
                                . "`use_status`, " // 使用状态 1未使用默认 0已使用
                                . "`category`, " // 品类 1,2 通用 1定制商品 2普通商品
                                . "`source` " // 来源 现在只有一种 1 激活
                            . " ) values ( "
                                . "'".$batch_id."', " // 关联批次ID
                                . "'".$data['name']."', " // 券名称
                                . "'".$data['money']."', " // 价格
                                . "'".time()."', " // 生成时间
                                . "'".strtotime($data['start_time'])."', " // 生效时间
                                . "'".strtotime($data['end_time'])."', " // 过期时间
                                . "'".$str."', " // 激活码 10位 数字加字母 年月日 20161226 时分秒
                                . "'0', " // 使用状态 1未使用默认 0已使用
                                . "'".$data['category']."', " // 品类 1,2 通用 1定制商品 2普通商品
                                . "'1' " // 来源 现在只有一种 1 激活
                        . " )";
                $db->query($sql);
            }
            exit('ok');
        }
    }
    /**
     * 优惠券批次管理
     */
    function voucher_batch(){
        
        if($_GET['op'] == 'del'){
            $voucher_batch =&m('voucher_batch');
            $voucher_batch->edit(array('id'=>$_GET['id']),array('status'=>0));
            $voucher =&m('voucher');
            $voucher->edit(' batch_id = \''.$_GET['id'].'\' ',array('status'=>0));
            $this->show_message('删除成功','返回列表页','index.php?app=setting&act=voucher_batch');
            exit;
        }
        if($_GET['op'] == 'export'){
            $voucher_batch =&m('voucher_batch');
            $batch = $voucher_batch->get(array(
                'id'=> $_GET['id']
            ));
            $voucher =&m('voucher');
            $list = $voucher->find(array(
                'fields' => 'id,category,name,money,create_time,start_time,end_time,code,binding_time,binding_username,use_status,order_id',
                'batch_id'=> $_GET['id']
            ));
            foreach($list as $k => $v){
                $list[$k]['category'] = $v['category'] == '1,2'?'通用':$v['category'] == '1'?'定制商品':'普通商品';
                $list[$k]['create_time'] = $v['create_time'] == ''?'':date('Y-m-d H:i:s',$v['create_time']);
                $list[$k]['start_time'] = $v['start_time'] == ''?'':date('Y-m-d',$v['start_time']);
                $list[$k]['end_time'] = $v['end_time'] == ''?'':date('Y-m-d',$v['end_time']);
                $list[$k]['binding_time'] = $v['binding_time'] == ''?'':date('Y-m-d H:i:s',$v['binding_time']);
            }
            
            $fields_name = array(
                'ID',
                '品类',
                '券名称',
                '价格',
                '生成时间',
                '生效时间',
                '过期时间',
                '激活码',
                '激活时间',
                '激活用户',
                '是否使用',
                '关联订单号',
                );
            array_unshift($list,$fields_name);
//            $this->export_to_csv($list, $batch['name'].date('YmdHis',time()), 'gbk');
            $this->export_to_csv($list, '优惠券'.date('YmdHis',time()), 'gbk');
            exit;
        }

        $is_used_arr = array(
            0=>'未使用',
            1=>'已使用',
        );

        $is_invalid_arr =array(
            0=>'未失效',
            1=>'已失效'
        );

        $pageye = isset($_GET['page']) ? trim($_GET['page']) : '';
        $conditions = '1=1 and status = 1 ';
        $conditions .= $this->_get_query_conditions(array(
            array(
                'field' => 'user_name',
                'name' =>  'user_name',
                'equal' => '=',
            ),
            array(
                'field' => 'is_invalid',
                'name' =>  'is_invalid',
                'equal' => '=',
                'type'  => 'numeric',
            ),
            array(
                'field' => 'is_used',
                'name' => 'is_used',
                'equal' => '=',
                'type'  => 'numeric',
            ),
            array(
                'field' => 'debit.add_time',
                'name'  => 'add_time_from',
                'equal' => '>=',
                'handler'=> 'gmstr2time',
            ),array(
                'field' => 'debit.add_time',
                'name'  => 'add_time_to',
                'equal' => '<=',
                'handler'   => 'gmstr2time_end',
            )
        ));

        if (isset($_GET['sort']) && isset($_GET['order'])) {
            $sort = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order, array('asc', 'desc'))) {
                $sort = 'id';
                $order = 'desc';
            }
        } else {
            $sort = 'id';
            $order = 'desc';
        }

        $_debit_mod =&m('voucher_batch');
        $page = $this->_get_page(50);

        $list = $_debit_mod->find(array(
            'join'=>'has_member',
            'conditions' => $conditions,
            'limit' => $page['limit'],
            'count' => true,
            'order' => "$sort $order",
        ));

        $this->import_resource(array(
                    'script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                    'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'
            ));

        $page['item_count'] = $_debit_mod->getCount();
        $this->_format_page($page);

        $this->assign('is_used_arr', $is_used_arr);
        $this->assign('cate_arr', $this->_get_options());
        $this->assign('is_invalid_arr', $is_invalid_arr);

        $this->assign('page_info', $page);
        $this->assign('list', $list);
        
        $this->display('voucher/voucher.batch.html');
    }
    /**
     * 优惠券管理
     */
    function voucher(){
        
        $voucher_batch =&m('voucher_batch');
        $batchList = $voucher_batch->find(array(
            'fields' => 'id,name',
        ));

        $is_used_arr = array(
            0=>'未使用',
            1=>'已使用',
        );

        $is_invalid_arr =array(
            0=>'未失效',
            1=>'已失效'
        );

        $pageye = isset($_GET['page']) ? trim($_GET['page']) : '';
        $conditions = '1=1 and status = 1 ';
        $stj = $_GET['s'];
            if($stj['binding_username'] != ''){
                $conditions .= " and `binding_username` like '%{$stj['binding_username']}%' ";
            }
            if($stj['code'] != ''){
                $conditions .= " and `code` = '".strtoupper($stj['code'])."' ";
            }
            if($stj['batch_id'] != ''){
                $conditions .= " and `batch_id` = '{$stj['batch_id']}' ";
            }
            // 激活状态
            if($stj['binding_status'] != ''){
                switch ($stj['binding_status'])
                {
                    case '':
                        break;
                    case '0': // 未使用
                            $conditions .= " and (`binding_username` = '' or `binding_username` is null) ";
                        break;
                    case '1': // 已使用
                            $conditions .= " and `binding_username` != '' ";
                        break;
                }
            }
            // 使用状态
            if($stj['use_status'] != ''){
                switch ($stj['use_status'])
                {
                    case '':
                        break;
                    case '0': // 未使用
                            $conditions .= " and `use_status` = '{$stj['use_status']}' ";
                        break;
                    case '1': // 已使用
                            $conditions .= " and `use_status` = '{$stj['use_status']}' ";
                        break;
                }
            }
            // 过期状态
            if($stj['end_time'] != ''){
                switch ($stj['end_time'])
                {
                    case '':
                        break;
                    case '0': // 未过期
                            $conditions .= " and `end_time` > '".time()."' ";
                        break;
                    case '1': // 已过期
                            $conditions .= " and `end_time` <= '".time()."' ";
                        break;
                }
            }

        if (isset($_GET['sort']) && isset($_GET['order'])) {
            $sort = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order, array('asc', 'desc'))) {
                $sort = 'id';
                $order = 'desc';
            }
        } else {
            $sort = 'id';
            $order = 'desc';
        }
        $_debit_mod =&m('voucher');
        $page = $this->_get_page(50);

        $list = $_debit_mod->find(array(
            'join'=>'has_member',
            'conditions' => $conditions,
            'limit' => $page['limit'],
            'count' => true,
            'order' => "$sort $order",
        ));
        
//        $_member_mod =&m('member');
//        foreach($list as $k=>$v){
//            $list[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
//            $list[$k]['expire_time'] = date('Y-m-d H:i:s',$v['expire_time']);
//            $user_info =$_member_mod->get(array('conditions'=>"user_id='{$v['from_uid']}'",'fields'=>"user_name,nickname"));
//            $list[$k]['from_user_name'] = empty($user_info['nickname'])?$user_info['user_name']:$user_info['nickname'];
//        }

        $this->import_resource(array(
                    'script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                    'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'
            ));

        $page['item_count'] = $_debit_mod->getCount();
        $this->_format_page($page);

        $this->assign('is_used_arr', $is_used_arr);
        $this->assign('cate_arr', $this->_get_options());
        $this->assign('is_invalid_arr', $is_invalid_arr);

        $this->assign('page_info', $page);
        $this->assign('list', $list);
        $this->assign('stj', $stj);
        $this->assign('batchList', $batchList);
        $this->display('voucher/voucher.list.html');
    }
    /**
     * 麦 券设定
     *
     * @author tsj
     * @return void
     */
    
    function wheat_setting()
    {
      
        $cate = array(
            '1' => '指定有限天数',
            '2' => '指定有限日期',
        );
        $debit_gift_type = array(
            '1' => '余额',
            '2' => '麦富迪币'
        );
        
        
        $this->assign('type',$type);
        $this->assign('cate',$cate);
        $this->assign('debit_gift_type',$debit_gift_type);
        
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
            
       
            
            if (($setting['debit_cate_o'] == 2) && $setting['debit_time_o'])
            {
                $setting['debit_time_o'] = date("Y-m-d",$setting['debit_time_o']);
            }
            
          
            $this->assign('setting', $setting);
            $this->assign("type", $this->_get_options());
            $this->display('setting/setting.wheat_setting.html');
        } 
        else 
        {
//      dump($_POST);
            $data['debit_num'] = $_POST['debit_num'];
            $data['debit_name'] = $_POST['debit_name'];
            $data['debit_type'] = $_POST['debit_type'];
            $data['debit_cate'] = $_POST['debit_cate'];
            $data['open']       = $_POST['open'];
     
            
            $data['debit_num_o'] = $_POST['debit_num_o'];
            $data['debit_name_o'] = $_POST['debit_name_o'];
            $data['debit_type_o'] = $_POST['debit_type_o'];
            $data['debit_cate_o'] = $_POST['debit_cate_o'];
            $data['debit_order_o'] = $_POST['debit_order_o'];
            $data['debit_open_o'] = $_POST['debit_open_o'];
           
            
            if ($data['debit_cate'] == 2) 
            {
                $time = strtotime($_POST['debit_time2']) + 86399;
            }
            else 
            {
                $time = $_POST['debit_time1'];
            }
            $data['debit_time'] = $time;
            
         
            
            if ($data['debit_cate_o'] == 2)
            {
                $time_o = strtotime($_POST['debit_time_o2']) + 86399;
            }
            else 
            {
                $time_o = $_POST['debit_time_o1'];
            }
            $data['debit_time_o'] = $time_o;
           
            $model_setting->setAll($data);
            $this->operation_log(__FUNCTION__,$data);
            $this->show_message('edit_wheat_setting_successed');
        }
    }
    
    /* 取得可以作为上级的商品分类数据 */
    function _get_options($except = NULL)
    {
        $_cate_mod = & bm('gcategory', array('_store_id' => 0));
    
        $gcategories = $_cate_mod->get_list();
        $tree =& $this->_tree($gcategories);
    
        return $tree->getOptions(MAX_LAYER - 1, 0, $except);
    }
    
    /* 构造并返回树 */
    function &_tree($gcategories)
    {
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($gcategories, 'cate_id', 'parent_id', 'cate_name');
        return $tree;
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
        if (! IS_POST) 
        {
            $setting = $model_setting->getAll(); // 载入系统设置数据
            
            $credit_mod = & m('creditscore');
          /*   
            $list1 = $credit_mod->find(array(
                                'conditions' => "class like 's_%'",
                                'field'=>'integral,class',
                                'count'=>true,
                                'order'=>'id DESC'
                                
            ));
            if ($list1) 
            {
                foreach ($list1 as $key => $value) 
                {
                    $arr = explode("_", $value['class']);
                    $list1[$key]['class'] = $arr[1];
                }
            }
            
            $list2 = $credit_mod->find(array(
                'conditions' => "class like 'd_%'",
                'field'=>'integral,class',
                'count'=>true,
                'order'=>'id DESC'
            
            ));
            if ($list2)
            {
                foreach ($list2 as $key => $value)
                {
                    $arr = explode("_", $value['class']);
                    $list2[$key]['class'] = $arr[1];
                }
            } */
            
            
            //=====      订单提成  =====
            $list_cut = $credit_mod->find(array(
                'conditions' => " `class` REGEXP '^cut_' ",
                'field'=>'integral,class',
                'count'=>true,
                'order'=>'id asc'
            
            ));
//     dump($list_cut);
            $list_cutp = $credit_mod->get(array(
                'conditions' => " `class` REGEXP '^cutp_' ",
                'field'=>'integral,class',
                'count'=>true,
                'order'=>'id asc'
            
            ));
    
            if ($list_cut) 
            {
                foreach ($list_cut as $key => $value) 
                {
                    $val = explode('_', $value['class']);
                    $list_cut[$key]['class'] = $val[1];
                }
            }
            if ($list_cutp) 
            {
                $val = explode('_', $list_cutp['class']);
                $list_cutp['class'] = $val[1];
            }
            
            
            
//       dump($list1);
           /*  $this->assign('list1',$list1);
            $this->assign('list2',$list2); */
            $this->assign('list_cut',$list_cut);
            $this->assign('list_cutp',$list_cutp);
            
        /*     print_r($list); die; */
            $this->assign('list',$list);
            $this->assign('setting', $setting);
            $this->assign('keys', $keys);
            $this->display('setting/setting.creditscore_setting.html');
        } 
        else 
        {
            
            $credit_mod = & m('creditscore');
            $jfs = empty($_POST['jfs']) ? '' : $_POST['jfs'];
            $djs = empty($_POST['djs']) ? '' : $_POST['djs'];
            
            $jfd = empty($_POST['jfd']) ? '' : $_POST['jfd'];
            $djd = empty($_POST['djd']) ? '' : $_POST['djd'];
            
            
            
            //=====  清空数据表  =====
           /*  $credit_mod->drop("class like 's_%' OR class like 'd_%' ");
            if ($jfs && $djs) 
            {
                foreach ($jfs as $key => $value) 
                {
                    if (!$value) 
                    {
                        continue;
                    }
                    $dj_value = $djs[$key];
                    $_data = array();
                    $_data['class'] = 's_'.$dj_value;
                    $_data['integral'] = $value;
                    $credit_mod->add($_data);
                }
            }
            
            if ($jfd && $djd)
            {
               
                foreach ($jfd as $key => $value)
                {
                    if (!$value)
                    {
                        continue;
                    }
                    $_data = array();
                    $dj_value = $djd[$key];
                    $_data['class'] = 'd_'.$dj_value;
                    $_data['integral'] = $value;
                    $credit_mod->add($_data);
                }
            } */
            
            $credit_mod->drop("class like 'cut_%' OR class like 'cutp_%' ");
            //=====  订单送提成  =====
            $cut =  empty($_POST['cut']) ? '' : $_POST['cut'];
            $cutn = empty($_POST['cutn']) ? '' : $_POST['cutn'];
            $cutp = empty($_POST['cutp']) ? '' : $_POST['cutp'];
            $cutpn = empty($_POST['cutpn']) ? '' : $_POST['cutpn'];
            if ($cut) 
            {
                foreach ($cut as $key => $value) 
                {
                    if (!$value) 
                    {
                        continue;
                    }
                    $_data = array();
                    $_data['class'] = "cut_".$value;
                    $_data['integral'] = $cutn[$key];
                    $credit_mod->add($_data);
                }
            }
            if ($cutp) 
            {
                $_data = array();
                $_data['class'] = "cutp_".$cutp;
                $_data['integral'] = $cutpn;
                $credit_mod->add($_data);
            }
            
            $data['selfp'] = $_POST['selfp'];
            $model_setting->setAll($data);
            $this->operation_log(__FUNCTION__,$data);
            
            
            $this->show_message('修改成功');
            return;
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
     * app强制更新设置
     *
     * @author Garbin
     * @return void
     */
    function appupdate_setting()
    {
        $app_setting = &af('appsetting');
        $setting = $app_setting->getAll(); // 载入系统设置数据
	
        if (! IS_POST) {
            $this->assign('setting', $setting['software_supdate']);
            $this->assign('config', array(
                'enabled_subdomain' => ENABLED_SUBDOMAIN,
                'subdomain_suffix' => defined('SUBDOMAIN_SUFFIX') ? SUBDOMAIN_SUFFIX : ''
            ));
            $this->assign('yes_or_no', array(
                Lang::get('no'),
                Lang::get('yes')
            ));
            $this->display('setting/setting.app_setting.html');
        } else {
            /* Setting */
			//mfd ios企业版
            $data['software_supdate']['mfd']['iosc']['version']         = $_POST['mfd_iosc_version'];
			$data['software_supdate']['mfd']['iosc']['if_update']       = $_POST['mfd_iosc_update'];
			$data['software_supdate']['mfd']['iosc']['description']     = $_POST['mfd_iosc_description'];
			$data['software_supdate']['mfd']['iosc']['link']            = $_POST['mfd_iosc_link'];
			//mfd ios  AppStore版
            $data['software_supdate']['mfd']['ioss']['version']         = $_POST['mfd_ioss_version'];
			$data['software_supdate']['mfd']['ioss']['if_update']       = $_POST['mfd_ioss_update'];
			$data['software_supdate']['mfd']['ioss']['description']     = $_POST['mfd_ioss_description'];
			$data['software_supdate']['mfd']['ioss']['link']            = $_POST['mfd_ioss_link'];
			//mfd 安卓
			$data['software_supdate']['mfd']['android']['version']     = $_POST['mfd_android_version'];
			$data['software_supdate']['mfd']['android']['if_update']   = $_POST['mfd_android_update'];
			$data['software_supdate']['mfd']['android']['description'] = $_POST['mfd_android_description'];
			$data['software_supdate']['mfd']['android']['link']        = $_POST['mfd_android_link'];
			
			
            /* 写入 */
            $app_setting->setAll($data);
            $this->show_message('APP强制更新配置成功！');
        }
    }
    /**
     * app更新链接
     *
     * @author Garbin
     * @return void
     */
    function appupdate_linking()
    {
    	$app_linking = &af('applinking');
    	$linking = $app_linking->getAll(); // 载入系统设置数据
    
    	if (! IS_POST) {
    		$this->assign('linking', $linking['app_link']);
    		$this->assign('config', array(
    				'enabled_subdomain' => ENABLED_SUBDOMAIN,
    				'subdomain_suffix' => defined('SUBDOMAIN_SUFFIX') ? SUBDOMAIN_SUFFIX : ''
    		));
    		$this->display('setting/setting.app_linking.html');
    	} else {
    		/* Setting */
    		//安卓 ios 分享地址
    		$data['app_link']['android_share_link']       = $_POST['android_share_link'];
    		$data['app_link']['ios_share_link']       = $_POST['ios_share_link'];
			$data['app_link']['android_mfd_link'] =$_POST['android_mfd_link'];
			$data['app_link']['ios_mfd_link'] =$_POST['ios_mfd_link'];
			$data['app_link']['android_liangti_link'] =$_POST['android_liangti_link'];
			$data['app_link']['ios_liangti_link'] =$_POST['ios_liangti_link'];
    		/* 写入 */
    		$app_linking->setAll($data);
    		$this->show_message(lang::get('link_update_ok'));
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
