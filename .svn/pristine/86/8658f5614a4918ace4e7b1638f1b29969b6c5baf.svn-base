<?php
/**
 *    我的作品
 * ns up  修改
 */
class My_worksApp extends MemberbaseApp{
    var $works_mod;
    var $work_imgs_mod;
    var $imgs_mod;
    var $_share_mod;
    var $_shareimgs_mod;
     function __construct()
    {
        $this->My_worksApp();
    }
    function My_worksApp()
    {
        parent::__construct();
        $this->works_mod =& m('works');
        $this->imgs_mod  =& m('workimgs');
        $this->_shares_mod = &m('shares');
        $this->_shareimgs_mod = &m('shareimgs');
        $this->member_mod=& m('member');
        $this->work_imgs_mod =& m('store_service');
        require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
        $this->objAvatar = new Avatar();
        $this->cloth = array(
            '0001'=>'套装',
            '0003'=>'西服',
            '0004'=>'西裤',
            '0005'=>'马甲',
            '0006'=>'衬衣',
            '0007'=>'大衣',
        );
    }

    public function index()
    {

        imports('diys.lib');
        $diys = new Diys();

        $user = $this->visitor->get();
        $page   =   $this->_get_page(8);    //获取分页信息
        $conditions = ' owner_id ='.$user['user_id'] .' AND iscover=1 ';
        $sql = "SELECT w.id,w.cloth,w.work_name,w.style,w.designer_id,w.diy_data,w.description,w.add_time,i.iscover,i.img_url FROM cf_works w  JOIN cf_work_imgs i ON w.id=i.work_id WHERE {$conditions} AND source_from='pc' ORDER BY w.add_time DESC LIMIT {$page['limit']}";

        //$sql = "SELECT w.id,w.cloth,w.work_name,w.style,w.designer_id,w.diy_data,w.description,w.add_time,i.iscover,i.img_url FROM cf_works w  JOIN cf_work_imgs i ON w.id=i.work_id WHERE {$conditions} ORDER BY w.add_time DESC LIMIT {$page['limit']}";
        $db = db();
        $works = $db->getALL($sql);
        //=======获取价格、设计人姓名
        foreach($works as $key=>$val){
            $params = json_decode($val['diy_data'],true);
            if($params){
            //获取价格于面料格式整合
            foreach($params['sysprocess'] as $k=>$v){
                $params_array[$val['id']][$k] = $v['fabric'];
             }
            }
            if($val['designer_id'] != $user['user_id']){
                $members = $this->member_mod->get(array(
                    'conditions' => 'user_id ='.$val['designer_id'],
                    'fields'     => 'nickname'
                ));

                $works[$key]['designer_name'] = $members['nickname'];
            }
        }
        //获取价格跟面料是否下架-lgx提供接口
        if($params_array){
            $works_diys = $diys->_calcPrice($params_array);
            foreach ($works as &$value) {
                //去掉小数点
                $works_diys[$value['id']]['price'] = (int)($works_diys[$value['id']]['price']);
                $value['pa'] = $works_diys[$value['id']];
            }
        }

        $sql = "SELECT count(w.id) FROM cf_works w  JOIN cf_work_imgs i ON w.id=i.work_id WHERE {$conditions} AND source_from='pc'";
        //$sql = "SELECT count(w.id) FROM cf_works w  JOIN cf_work_imgs i ON w.id=i.work_id WHERE {$conditions}";
        $count_sql = $db->getOne($sql);
        $page['item_count'] = $count_sql;   //获取统计的数据
        $this->_format_page($page);
        $this->assign('works',$works);
        $this->assign('app', APP);
        $this->assign('user',$user);
        $this->assign('page_info', $page);

        //面料与价格信息
        $this->assign('works_diys',$works_diys);
        $this->assign('status','index');
        //头部title
        $this->_config_seo('title', '我的麦富迪 - 我的作品');
        $this->display('my_works.index.html');
    }

    public function info(){
        imports('diys.lib');
        $diys = new Diys();
        $args = $this->get_params();
        $user = $this->visitor->get();

        //$type = $_GET['type'] ? null : $_GET['type'];
        $id = $args[0];
        $type = $args[1];
        if(!$id){
            $this->show_message("此作品不存在！");
            return;
        }
        if($type == 'counter'){
            $conditions = 'w.id = '.$id.' AND is_counter=1 AND counter_user_id = '.$user['user_id'] ;
            $title = '我的麦富迪 - 顾客推送作品';
            $type_info = $type;
        }else{
            $conditions = 'w.id = '.$id.' AND owner_id = '.$user['user_id'] ;
            $title = '我的麦富迪 - 我的作品';
            $type_info = 'info';
        }
        $sql = "SELECT w.id,w.cloth,w.work_name,w.style,w.designer_id,w.description,w.diy_data,w.add_time,w.is_counter,w.counter_add_time,w.owner_id,w.counter_description,i.iscover,i.img_url FROM cf_works w  JOIN cf_work_imgs i ON w.id=i.work_id WHERE {$conditions} AND source_from='pc' ORDER BY i.iscover DESC";
        //$sql = "SELECT w.id,w.cloth,w.work_name,w.style,w.designer_id,w.description,w.diy_data,w.add_time,w.is_counter,w.counter_add_time,w.owner_id,w.counter_description,i.iscover,i.img_url FROM cf_works w  JOIN cf_work_imgs i ON w.id=i.work_id WHERE {$conditions} ORDER BY i.iscover DESC";
        $db = db();
        $works = $db->getALL($sql);
        if(empty($works)){
            $this->show_message("此作品不存在！");
            return;
        }

        foreach($works as $key=>$val){
            //==========获取设计者名称========
            $member = $this->member_mod->get($val['designer_id']);
            $result['id']            = $val['id'];
            $result['cloth']         = $val['cloth'];
            $result['work_name']     = $val['work_name'];
            $result['designer_id']   = $val['designer_id'];
            $result['designer_name'] = $member['nickname'];
            $result['description']   = $val['description'];
            $result['style']         = $val['style'];
            $result['owner_id']      = $val['owner_id'];
            $result['diy_data']      = $val['diy_data'];
            $result['add_time']      = $val['add_time'];
            $result['imgs'][]        = $val['img_url'];
            $result['url'] = SITE_URL.'/custom-diy-'.$val['cloth'].'-'.$val['style'].'-'.$val['id'].'.html';
            $result['curl'] = SITE_URL.'/custom-diy-'.$val['cloth'].'-'.$val['style'].'-'.$val['id'].'-share.html';
            $result['is_counter'] = $val['is_counter'];
            //推送信息
            if($type == 'counter'){
                $result['counter_add_time'] = date("Y-m-d h:i:s", $val['counter_add_time']);
                $result['counter_info'] = $this->member_mod->get(array(
                         'conditions' => 'user_id ='.$val['owner_id'],
                         'fields'     => 'nickname,user_name'
                 ));
                $result['counter_description'] = $val['counter_description'];
            }

        }
        $diy_data = json_decode($result['diy_data'],true);

        //遗漏问题
        array_pop($diy_data);

        if($diy_data){
             foreach($diy_data['sysprocess'] as $k=>$v){
                 $params_array[$val['id']][$k] = $v['fabric'];
             }
        }
        if($params_array){
            $works_diys = $diys->_calcPrice($params_array);
            $works_diys[$result['id']]['price'] = (int)($works_diys[$result['id']]['price']);
            $result['pa'] = $works_diys[$result['id']];
        }

        $rData =array();

        foreach($diy_data as $kk=>$vv){
            foreach ((array)$vv as $key=>$row){
                //将数据格式化成与PC一致
                foreach ($row['cstr'] as $pk=>$pv){
                    $vt = explode(':', $pv);
                    $row['prm'][$vt[0]] = $vt[1];
                }
                //$rData[$key] = json_encode($row['prm'],JSON_UNESCAPED_UNICODE);
                $rData[$key] = $row['prm'];
            }
        }

        //===========获取工艺信息=========
        $dict_mod = m('dict');
        $params = array();
        foreach($rData as $key=>$value){
            //$params[$key] = json_decode($value,true);
            $params[$key] = $value;
        }
        if ($params)
        {
            $tmp = array();
            $n=0;
            foreach ((array)$params as $key1 => $value1)
            {
                $i=0;
                $value1 = array_filter($value1);//去除工艺中的空值
                foreach((array)$value1 as $key2 => $value2){
                    $value1_val = explode("|", $value2);

                    $dict2_info = $dict_mod->get("id=".$key2);
                    $a[$key1][$i]['p_name'] =$dict2_info['name'];
                    $dict1_info = $dict_mod->get("id=".$value1_val[0]);
                    if (count($value1_val) > 1)
                    {
                        $a[$key1][$i]['s_name'] = $dict1_info['name'] .$value1_val[2];
                    }
                    else
                    {
                        $a[$key1][$i]['s_name'] = $dict1_info['name'];
                    }
                    $i++;

                }
                $result['params_value'][$n]['name'] = $this->cloth[$key1];
                $result['params_value'][$n]['params'] = $a[$key1];
                $n++;
            }
        }
        //echo $this->visitor->get('member_lv_id');
        $this->assign('member_lv_id',$this->visitor->get('member_lv_id'));
        $this->assign('result',$result);
        $this->assign('app', APP);
        $this->assign('user',$user);
        $this->assign('type_info',$type_info);
        //头部title
        $this->_config_seo('title', $title);
        $this->display('my_works.info.html');

    }

    function shareList(){
        if(!$_POST){
            $args    = $this->get_params();
            $work_id = $args[0];
            $page    =   $this->_get_page(10,1);    //获取分页信息
            $user    = $this->visitor->get();
            $works = $this->works_mod->find(array(
                'conditions' => 'from_id = '.$work_id.' AND owner_id != '.$user['user_id'],
            	'index_key'      => 'owner_id',
            ));
//        print_exit($works);
            $ids = array();
            foreach($works as $kk=>$vv){
                $ids[] = $vv['owner_id'];
            }
            //==========查询我的推荐人======
            $cus_mod = m('memberinvite');
            $mem_mod = m('member');
            $cus_list = $cus_mod->find(array(
                'conditions' => 'inviter = '.$user['user_id'],
                'fields'     => 'id,invitee,nickname,phone_mob',
                'limit'      => $page['limit'],
            ));
//            print_exit($cus_list);
            if(!$cus_list){
                $this->show_warning('你还没有顾客，请添加顾客继续操作');
                return;
            }
            foreach($cus_list as $key=>$val){
                $member = $mem_mod->get($val['invitee']);
                $cus_list[$key]['phone_mob'] = $member['user_name'];
                $cus_list[$key]['nickname']  = $member['nickname'];
                //获得用户头像
                $cus_list[$key]['avatar'] = $this->getAvatar($val['invitee']);
                $cus_list[$key]['wid'] = $works[$val['invitee']]['id'];

            }
//                print_exit($cus_list);
            $result = array();
            //======是否已分享0已分享1未分享
            foreach($cus_list as $key=>$val){
                if(in_array($val['invitee'],$ids)){
                    $val['shared'] = 0;
                }else{
                    $val['shared'] = 1;
                }
                $result[]=$val;
            }
        }else{
            $work_id       = $_POST['wid'];
            $condition = $_POST['keyword'];
            $user      = $this->visitor->get();
            $user_id   = $user['user_id'];
            //====== 搜索条件(用户名或手机号)==========
            $sql = 'SELECT m.user_name as phone_mob,i.invitee,m.nickname FROM cf_member_invite i  JOIN cf_member m ON m.user_id=i.invitee WHERE inviter = '.$user_id.' AND (m.nickname LIKE "%'.$condition.'%" or m.user_name LIKE "%'.$condition.'%")';
            $db = db();
            $cus_list = $db->getALL($sql);
            if($cus_list){
                foreach($cus_list as $key=>$val){
                    //获得用户头像
                    $cus_list[$key]['avatar'] = $this->getAvatar($val['invitee']);
                }

                //======== 获取id判断是否已分享 ========
                $works = $this->works_mod->find(array(
                    'conditions' => 'id ='.$work_id.' AND designer_id ='.$user_id,
                	'index_key'      => 'owner_id',
                ));
                $ids = array();
                foreach($works as $kk=>$vv){
                    $ids[] = $vv['owner_id'];
                }
                $result = array();
                foreach($cus_list as $key=>$val){
                    if(in_array($val['invitee'],$ids)){
                        $val['shared'] = 0;
                    }else{
                        $val['shared'] = 1;
                    }
                    $val['wid'] = $works[$val['invitee']]['id'];
                    $result[]=$val;
                }
            }
        }
        $this->assign('work_id',$work_id);
        $this->assign('result',$result);
        $this->assign('kw',$condition);
        $this->assign('app', APP);
        //头部title
        $this->_config_seo('title', '我的麦富迪 - 分享给我的顾客');
        $this->display('my_works.sharelist.html');
    }
    /**
     * 删除作品
     *@author chao.liu <280181131@qq.com>
     *@2015年8月20日
     */
    function delWork(){
        $id   = $_POST['id'];
        $conditions = 'id = '.$id;
        $result = $this->works_mod->drop($conditions);
        $i_conditions = 'work_id = '.$id;
        if(!$result){
            $this->json_error('作品删除失败！');
        }else{
            $this->imgs_mod->drop($i_conditions);
            $this->json_result('作品删除成功！');
        }
    }

    //反推作品列表
    function Counter(){
        imports('diys.lib');
        $diys = new Diys();

        $user = $this->visitor->get();
        $page   =   $this->_get_page(8);    //获取分页信息
        $conditions = 'is_counter=1 AND counter_user_id ='.$user['user_id'] .' AND iscover=1 ';
        //$sql = "SELECT w.id,w.cloth,w.work_name,w.style,w.designer_id,w.diy_data,w.description,w.add_time,w.owner_id,i.iscover,i.img_url FROM cf_works w  JOIN cf_work_imgs i ON w.id=i.work_id WHERE {$conditions} AND source_from='pc' ORDER BY w.add_time DESC LIMIT {$page['limit']}";
        $sql = "SELECT w.id,w.cloth,w.work_name,w.style,w.designer_id,w.diy_data,w.description,w.add_time,w.owner_id,i.iscover,i.img_url FROM cf_works w  JOIN cf_work_imgs i ON w.id=i.work_id WHERE {$conditions} ORDER BY w.add_time DESC LIMIT {$page['limit']}";
        $db = db();
        $works = $db->getALL($sql);
        //=======获取价格、设计人姓名
        foreach($works as $key=>$val){
            $params = json_decode($val['diy_data'],true);
            if($params){
            //获取价格于面料格式整合
            foreach($params['sysprocess'] as $k=>$v){
                $params_array[$val['id']][$k] = $v['fabric'];
             }
            }
            if($val['designer_id'] != $user['user_id']){
                $members = $this->member_mod->get(array(
                    'conditions' => 'user_id ='.$val['designer_id'],
                    'fields'     => 'nickname'
                ));
                $works[$key]['designer_name'] = $members['nickname'];
            }
            //获取推送者-消费者-用户信息
            $works[$key]['x_user'] = $this->member_mod->get(array(
                    'conditions' => 'user_id ='.$val['owner_id'],
                    'fields'     => 'nickname,user_name'
                ));
        }
        //获取价格跟面料是否下架-lgx提供接口
        if($params_array){
            $works_diys = $diys->_calcPrice($params_array);
            foreach ($works as &$value) {
                $works_diys[$value['id']]['price'] = (int)($works_diys[$value['id']]['price']);
                $value['pa'] = $works_diys[$value['id']];
            }
        }

        //$sql = "SELECT count(w.id) FROM cf_works w  JOIN cf_work_imgs i ON w.id=i.work_id WHERE {$conditions} AND source_from='pc'";
        $sql = "SELECT count(w.id) FROM cf_works w  JOIN cf_work_imgs i ON w.id=i.work_id WHERE {$conditions}";
        $count_sql = $db->getOne($sql);
        $page['item_count'] = $count_sql;   //获取统计的数据
        $this->_format_page($page);

        $this->assign('Counter',$works);
        $this->assign('app', APP);
        $this->assign('user',$user);
        $this->assign('page_info', $page);

        //面料与价格信息
        $this->assign('Counter_diys',$works_diys);
        $this->assign('status','Counter');
        //头部title
        $this->_config_seo('title', '我的麦富迪 - 推送作品');
        $this->display('my_works.index.html');
    }


    /**
     * 反推作品
     *@author ns add
     *@2015年11月26日
     */
    function counterWork(){
        $id   = $_POST['id'];
        if(!is_numeric($id)){
            $this->json_error('作品推送失败',-1);
        }
        $user    = $this->visitor->get();
        //判断是否为普通消费者
        if($user['member_lv_id'] > 1){
            $this->json_error('只有消费者才能进行推送创业者',-1);
        }else{
            //查看是否有绑定用户
            $member_invite_mod =& m('memberinvite');
            $invite = $member_invite_mod->get(array(
            'conditions'=>'invitee = '. $user['user_id'],
            ));
            if($invite){
                //进行查询是否推送过
                $works = $this->works_mod->get(array(
                    'conditions'=>'id = '.$id.' AND owner_id = '. $user['user_id'],
                    ));
                if($works['is_counter'] > 0 || $works['counter_user_id'] > 0){
                    $this->json_error('你已经进行推送过了',-1);
                    return;
                }
                $this->json_result('可以进行推送',1);
                return;
            }else{
                //没有信息就进行弹窗版定
                $this->json_error('没有绑定创业者',-2);
                return;
            }
        }
    }

    //进行添加推送
    function add_counterWork(){
        $counter_description = $_POST['counter_description'];
        $id = $_POST['id'];
        if(!is_numeric($id)){
            $this->json_error('作品推送失败',-1);
        }
        $user = $this->visitor->get();
        //查看是否有绑定用户
         $member_invite_mod =& m('memberinvite');
         $invite = $member_invite_mod->get(array(
         'conditions'=>'invitee = '. $user['user_id'],
         ));
         if($invite){
        //进行查询是否推送过
             $works = $this->works_mod->get(array(
                     'conditions'=>'id = '.$id.' AND owner_id = '. $user['user_id'],
             ));
            if($works['is_counter'] > 0 || $works['counter_user_id'] > 0){
                    $this->json_error('你已经进行推送过了',-1);
                    return;
            }
            //进行推送的添加
            $data = array(
            'is_counter' => 1,
            'counter_user_id' => $invite['inviter'],
            'counter_add_time' => time(),
            'designer_id'=> $invite['inviter'],
            'counter_description' => $counter_description
            );
            $this->works_mod->edit($id,$data);
            $this->json_result('推送成功',1);
         }else{
             //没有信息就进行弹窗版定
             $this->json_error('没有绑定创业者',-2);
             return;
         }
    }


    /**
     * 获取头像
     *@author chao.liu <280181131@qq.com>
     *@2015年8月20日
     */
    function getAvatar($id){
        /* 头像 add by xiao5 START */
        $member = $this->member_mod->get($id);

        //获得用户头像
        $avatar = $this->objAvatar->avatar_show($id, 'big');
        $return = $avatar.'?'.$member['avatar_time'];//加入头像时间，用于app及时更新头像
        return $return;
    }
    /**
     * 分享作品
     *@author chao.liu <280181131@qq.com>
     *@2015年9月17日
     */
    function shareWork(){
        $cid = $_POST['cid'];
        $wid = $_POST['wid'];
        $user    = $this->visitor->get();
        $conditions = 'id = '.$wid.' AND owner_id ='.$user['user_id'];
//            print_exit($conditions);
        $work_info = $this->works_mod->get(array(
            'conditions' => $conditions,
        ));

        array_shift($work_info);//去除id
       // 备份作品 查询分享表是否有和该作品相同的备份数据
        $work = $this->works_mod->get('id='.$wid);
        $share = $this->_shares_mod->get(array(
            'conditions' => "cloth={$work_info['cloth']} AND work_name= '{$work_info['work_info_name']}' AND style='{$work_info['style']}' AND designer_id='{$work_info['designer_id']}' AND owner_id='{$work_info['owner_id']}' AND diy_data='{$work_info['diy_data']}'",
        ));

        //分享表没有数据则新添加一条数据
        if(!$share){
            $work['add_time'] = time();
            array_shift($work);
            $sid = $this->_shares_mod->add($work);
            if(!$sid){
            	$this->json_error('作品备份失败',-1);            	return;
            }
            $work_imgs = $this->imgs_mod->find(array(
                'conditions' => 'work_id='.$wid
            ));
            foreach($work_imgs as $key=>$val){
                $work_imgs[$key]['work_id'] = $sid;
                array_shift($work_imgs[$key]);
                $this->_shareimgs_mod->add($work_imgs[$key]);
            }
        }

        //=====  分享作品  =====
        $shared = $this->works_mod->find(array(
            'conditions' => 'from_id = '.$wid.' AND owner_id ='.$cid,
        ));
        //如果作品已经分享给该顾客则不添加数据
        if(!$shared){
            $work_info['owner_id'] = $cid;
            $work_info['from_id']  = $wid;
            $updata['share_num'] = $work_info['share_num']+1;
            $this->works_mod->edit($wid,$updata);
            $work_img_mod = m('workimgs');
            $new_work_id = $this->works_mod->add($work_info);
            $img = $work_img_mod->get('work_id='.$wid);
            $new_work_img['img_url'] = $img['img_url'];
            $new_work_img['work_id'] = $new_work_id;
            $new_work_img['description'] = $img['description'];
            $new_work_img['iscover'] = $img['iscover'];
            $new_work_img['add_time'] = $img['add_time'];
            $img = $this->imgs_mod->add($new_work_img);
            if(!$img){
            	$this->json_error('图片分享失败',-1);            	return;
            }
        }
        $this->json_result();

    }
    /**
     * 获取站外分享url
     *@author chao.liu <280181131@qq.com>
     *@2015年9月17日
     */
    function shareUrl(){
        $id = $_POST['id'];

        $work = $this->works_mod->get('id='.$id);
        //查询分享表是否有作品数据
//        print_exit($result);
        $share = $this->_shares_mod->get(array(
            'conditions' => "cloth={$work['cloth']} AND work_name= '{$work['work_name']}' AND style='{$work['style']}' AND designer_id={$work['designer_id']} AND owner_id='{$work['owner_id']}' AND diy_data='{$work['diy_data']}'",
        ));
        $result = array();
        //分享表没有数据则新添加一条数据
        if(!$share){
            $work['add_time'] = time();
            array_shift($work);
            $sid = $this->_shares_mod->add($work);
            if(!$sid){
                $this->result->msg = '作品备份失败';
                $this->json_error();
            }
            $work_imgs = $this->imgs_mod->find(array(
                'conditions' => 'work_id='.$id
            ));
            foreach($work_imgs as $key=>$val){
                $work_imgs[$key]['work_id'] = $sid;
                array_shift($work_imgs[$key]);
                $this->_shareimgs_mod->add($work_imgs[$key]);
                $result['imgs'][] = $val['img_url'];
            }
            $result['url'] = SITE_URL.'/custom-diy-'.$work['cloth'].'-'.$work['style'].'-'.$sid.'-share.html';
        }else{

            $share_img = $this->_shareimgs_mod->find('work_id='.$share['id']);
            $result = array();
            foreach($share_img as $key=>$val){
                $result['imgs'][]=$val['img_url'];
            }
            if(!$share_img){
                $this->result->msg='该备份数据没有备份图片';
                $this->json_error();
            }
            $result['url'] = SITE_URL.'/custom-diy-'.$work['cloth'].'-'.$work['style'].'-'.$share['id'].'-share.html';
        }
        $this->json_result($result);


    }

    /**
     * 分享记录
     *@author chao.liu <280181131@qq.com>
     *@2015年9月17日
     */
    function shareRecord(){
        $args    = $this->get_params();
        $work_id = $args[0];
        $page    =   $this->_get_page(20,1);    //获取分页信息
        $user    = $this->visitor->get();
        $works = $this->works_mod->find(array(
            'conditions' => 'from_id = '.$work_id,
            'limit'      => $page['limit'],
            'index_key'      => 'owner_id',
        ));
//        print_exit($works);
        if(!$works){
            $this->show_message("您还没有分享给您的顾客，快去分享吧");
            return;
        }
        $ids = array();
        foreach($works as $kk=>$vv){
            $ids[] = $vv['owner_id'];
        }
        $ids = implode(',',$ids);
        $conditions = "invitee IN ({$ids})";
//            print_exit($ids);
        $cus_mod = m('memberinvite');
        $mem_mod = m('member');
        $cus_list = $cus_mod->find(array(
            'conditions' => $conditions,
            'fields'     => 'invitee'
        ));
        $result =array();
        foreach($cus_list as $key=>$val){
            $member = $mem_mod->get($val['invitee']);
            $val['phone_mob'] = $member['user_name'];
            $val['nickname']  = $member['nickname'];
            $val['add_time']  = $works[$val['invitee']]['add_time'];
            //获得用户头像
            $val['avatar'] = $this->getAvatar($val['invitee']);
            $result[] =$val;
        }
        $this->assign('result',$result);
        $this->assign('work_id',$work_id);
        $this->assign('app', APP);
        //头部title
        $this->_config_seo('title', '我的麦富迪 - 作品分享记录');
        $this->display('my_works.sharerecord.html');
    }
    /**
     * 再次分享
     *@author 小五
     * $Id: my_works.app.php 13198 2015-12-31 00:46:43Z nings $
     * $Date: 2015-12-31 08:46:43 +0800 (Thu, 31 Dec 2015) $
     */
    function shareRecordWork(){
    	$workid = $_POST['id'];
    	$wid = $_POST['wid'];

    	if (!$workid || !$wid){
    		$this->json_error('作品已不存在');
    		return;
    	}
    	/* 消费者分享的作品数据 */
    	$work_info = $this->works_mod->get($workid);
    	if (!$work_info){
    		$this->json_error('作品已不存在');
    		return;
    	}

    	/* 创业者的作品 diy时 worksid */
    	$b_work_info = $this->works_mod->get($wid);
    	if (!$b_work_info){
    		$this->json_error('创业者作品已不存在');
    		return;
    	}

    	/* 标题、品类、风格、简介、工艺 */
    	$pstr = md5($work_info['work_name'].$work_info['cloth'].$work_info['style'].$work_info['description'].$work_info['diy_data']);
    	$b_pstr = md5($b_work_info['work_name'].$b_work_info['cloth'].$b_work_info['style'].$b_work_info['description'].$b_work_info['diy_data']);


    	if ($pstr == $b_pstr){
    		$this->json_result(1,'再次分享成功');
    		return;
    	}

    	/* 事务 更新works、work_img */

    	$transaction =  $this->works_mod->beginTransaction();

    	$updata_w['work_name'] = $b_work_info['work_name'];
    	$updata_w['cloth'] = $b_work_info['cloth'];
    	$updata_w['style'] = $b_work_info['style'];
    	$updata_w['description'] = $b_work_info['description'];
    	$updata_w['diy_data'] = $b_work_info['diy_data'];
    	$updata_w['add_time'] = time();

    	$this->works_mod->edit($workid,$updata_w);

    	/* 删除之前作品的img信息 */
    	$this->imgs_mod->drop('work_id = '.$workid);


    	$img_data = $this->imgs_mod->find(array(
    			'conditions' => 'work_id='.$wid,
    	));
    	$res = 0;
    	foreach ((array)$img_data as $img){
    		unset($img['id']);
    		$img['work_id'] = $workid;
    		$img['add_time'] = time();
    		$res = $this->imgs_mod->add($img);
    	}


    	if (!$res) {
    		/* 事务回滚 */
    		$this->works_mod->rollback();
    		$this->json_error($this->imgs_mod->get_error());
    		return;
    	}

    	$this->works_mod->commit($transaction);

    	$this->json_result(1,'再次分享成功!');

    }

    /**
     *    用户中心个人资料添加邀请码的 接口
     *
     *    @author   ns add 模拟添加邀请码
     *    @usage    none
     */

    function addinviter(){

        $invite = $_POST['invite'];
        $m = &m('member');
        $meminvite_mod = &m('memberinvite');
        $_generalize_mod =& m('generalize_member');
        $customer_figure=& m('customer_figure');
        $user_info = $this->visitor->get();
        $user_id = $this->visitor->get('user_id');


        //db+邀请  只能绑定一种码
        if($meminvite_mod->get("invitee = '$user_id'"))
        {
           $this->json_error('应被邀请过!');
           return;
        }


        if(strlen($invite)==12){
            //创业者

            //存在db
            $g_member = $_generalize_mod->get("status=1 and invite = '".$invite."'");

            if(empty($g_member))
            {
                $this->json_error('BD码不存在!');
                return;
            }
            $_type =1;
            $invite_nickname=$g_member['name'];
            $inviter = $g_member['id'];

        }else{

            //创业者过滤绑定邀请码
            if($user_info['member_lv_id']>1)
            {
                $this->json_error('创业者不能参与此活动!');
                return;

            }

            $member = $m->get( "serve_type=1 and invite = '".strtoupper($invite)."'");


            if($member['user_id'] == $user_id){

                $this->json_error('不能邀请自己!');
                return;

            }

            if(empty($member))
            {
                $this->json_error('邀请码错误!');
                return;

            }

            $_type =0;
            //邀请码
            if(empty($member['nickname']))
            {
                if(empty($member['real_name']))
                {
                    $invite_nickname =$member['user_name'];
                }else
                {
                    $invite_nickname =$member['real_name'];
                }
            }else
            {
                $invite_nickname =$member['nickname'];
            }

            $inviter = $member['user_id'];
        }


        //邀请关系    邀请码 db码都放一个表
        $member_invite = m("memberinvite");
        $invite_data = array(
            'inviter'  => $inviter, //邀请人
            'invitee'  => $user_id,
            'nickname' => $invite_nickname,     //邀请人昵称
            'type'      => $_type,
            'add_time' => time(),
            'come_from'=>'pc|my_works',
        );

        $member_invite->add($invite_data);

        if(!empty($g_member)){
            change_lv($user_info['user_id']);
        }
        if(!empty($member)) {

            //奖励
            $custom_info = $customer_figure->get(array(
                'conditions' =>"storeid='{$inviter}' and customer_mobile='{$user_info['user_name']}' and type_cus <> 0",
                'fields' =>"figure_sn",
            ));
            if(!empty($custom_info)){

                $c_ret = $customer_figure->edit("figure_sn='{$custom_info['figure_sn']}'",array('userid'=>$user_id,'type_cus'=>2,'firsttime'=>time(),'lasttime'=>time()));

                if(!$c_ret)
                {

                    $this->json_error('顾客修改不成功!');
                    return;

                }
            }else{
                $custome_data=array(
                    'storeid'  => $inviter, //邀请的人的id
                    'customer_mobile'  => $user_info['user_name'],//被邀请人的手机号
                    'userid' => $user_id,  //被邀请人的id
                    'customer_name'    => $user_info['nickname'],//被邀请人的昵称
                    'type_cus' => 3,//类型
                    'firsttime' => time(),
                    'lasttime' => time(),
                );
                $customer = $customer_figure->add($custome_data);
                if(!$customer){

                    $this->json_error('添加 顾客不成功!');
                    return;

                }
            }

            $store_allow = include  ROOT_PATH.'/data/settings.inc.php';
            $this->_debit_mod = &m("debit");

            if($user_info['serve_type'] == 1){
                //礼券- self
                if(!empty($store_allow['debit_cate']) && !empty($store_allow['debit_time'])&& !empty($store_allow['debit_name'])&&!empty($store_allow['debit_num']) && !empty($store_allow['debit_type'])){

                    if($store_allow['debit_cate']==1){
                        $expire_time =strtotime('+'.$store_allow['debit_time'].' days') - date('Z');
                    }else{
                        $expire_time =$store_allow['debit_time'];
                    }

                    $data =array(
                        'debit_name'=>$store_allow['debit_name'],
                        'debit_sn'=>time().createNonceStr(8),
                        'money'=>$store_allow['debit_num'],
                        'user_id'=>$user_id,
                        'source'=>'invite',
                        'add_time'=>time(),
                        'cate'=>$store_allow['debit_type'],
                        'expire_time'=>$expire_time,
                    );
                    $this->_debit_mod->add($data);
                }

                //礼券 -self
                if(!empty($store_allow['debit_cate2']) && !empty($store_allow['debit_time2'])&& !empty($store_allow['debit_name2'])&&!empty($store_allow['debit_num2']) && !empty($store_allow['debit_type2'])){
                    if($store_allow['debit_cate2']==1){
                        $expire_time2 =strtotime('+'.$store_allow['debit_time2'].' days') - date('Z');
                    }else{
                        $expire_time2 =$store_allow['debit_time2'];
                    }

                    $data =array(
                        'debit_name'=>$store_allow['debit_name2'],
                        'debit_sn'=>time().createNonceStr(8),
                        'money'=>$store_allow['debit_num2'],
                        'user_id'=>$user_id,
                        'source'=>'invite',
                        'add_time'=>time(),
                        'cate'=>$store_allow['debit_type2'],
                        'expire_time'=>$expire_time2,
                    );
                    $this->_debit_mod->add($data);
                }
            }
            $this->json_result('添加邀请码成功！');
            return;
        }

    }

}

?>
