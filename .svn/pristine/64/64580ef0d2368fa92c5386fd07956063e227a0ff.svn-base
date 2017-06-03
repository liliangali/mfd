<?php
/**
 *    我的作品
 * ns up  修改
 */
class My_worksApp extends MemberbaseApp{
    var $works_mod;
    var $work_imgs_mod;
     function __construct()
    {
        $this->My_worksApp();
    }
    function My_worksApp()
    {
        parent::__construct();
        $this->works_mod =& m('works');
        $this->imgs_mod  =& m('workimgs');
        $this->member_mdo=& m('member');
        $this->work_imgs_mod =& m('store_service');
        require(ROOT_PATH . 'h5/includes/avatar.class.php');     //基础控制器类
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
        //ns add 获取实时价格于面料上下架问题
        require(ROOT_PATH.'includes/libraries/diys.lib.php');
        //importm('diys.lib');
        $diys = new Diys();

        $user = $this->visitor->get();
        $type = $_GET['type'];
        $conditions = ' owner_id ='.$user['user_id'] .' AND iscover=1';
        //$conditions = 'iscover=1';
        //判断是否有分类筛选
        if($type){
            $conditions .= ' AND cloth='.$type;
        }
        //$conditions = 'iscover=1';

        $sql = "SELECT w.id,w.cloth,w.work_name,w.style,w.designer_id,w.diy_data,w.description,w.add_time,i.iscover,i.img_url FROM
                 cf_works w  JOIN cf_work_imgs i ON w.id=i.work_id WHERE {$conditions} AND source_from='app'  ORDER BY w.add_time DESC LIMIT 20";
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
            $this->member_mod = m('member');
            if($val['designer_id'] != $user['user_id']){
                $members = $this->member_mod->get(array(
                    'conditions' => 'user_id ='.$val['designer_id'],
                    'fields'     => 'nickname'
                ));

                $works[$key]['designer_name'] = $members['nickname'];
            }
        }
        //var_dump($params_array);exit;

        //获取价格跟面料是否下架-lgx提供接口
        if($params_array){
            $works_diys = $diys->_calcPrice($params_array);
            foreach ($works as &$value) {
                $value['pa'] = $works_diys[$value['id']];
            }
        }
        $sql = "SELECT count(w.id) FROM
                  cf_works w  JOIN cf_work_imgs i ON w.id=i.work_id WHERE {$conditions} AND source_from='app'";
        $count_sql = $db->getOne($sql);

        //$page['item_count'] = $count_sql;   //获取统计的数据
        //$this->_format_page($page);
        $this->assign('works',$works);
        $this->assign('app', APP);
        $this->assign('count_sql', $count_sql);
        $this->display('my_works.index.html');
    }

    //获取瀑布流数据
    function ajax_list(){
        //ns add 获取实时价格于面料上下架问题
        require(ROOT_PATH.'includes/libraries/diys.lib.php');
        //importm('diys.lib');
        $diys = new Diys();

        $list = $_POST['list'];
        $type = $_GET['type'];
        $conditions = ' owner_id ='.$user['user_id'] .' AND iscover=1';
        //判断是否有分类筛选
        if($type){
            $conditions .= ' AND cloth='.$type;
        }
        $sql = "SELECT w.id,w.cloth,w.work_name,w.style,w.designer_id,w.diy_data,w.description,w.add_time,i.iscover,i.img_url FROM
                 cf_works w  JOIN cf_work_imgs i ON w.id=i.work_id WHERE {$conditions} AND source_from='app'  ORDER BY w.add_time DESC LIMIT ".$list;
        $db = db();
        $works = $db->getALL($sql);
        if($works){
            foreach($works as $key=>$val){
                $params = json_decode($val['diy_data'],true);
                if($params){
                //获取价格于面料格式整合
                foreach($params['sysprocess'] as $k=>$v){
                    $params_array[$val['id']][$k] = $v['fabric'];
                 }
                }
                $this->member_mod = m('member');
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
                    $value['pa'] = $works_diys[$value['id']];
                }
            }
            $this->json_result($works);
        }else{
            $this->json_error('没有数据了！');
            return;
        }

    }

    public function info(){
        //ns add 获取实时价格于面料上下架问题
        require(ROOT_PATH.'includes/libraries/diys.lib.php');
        //importm('diys.lib');
        $diys = new Diys();

        $args = $this->get_params();
        $user = $this->visitor->get();
        $id = $args[0];
        if(!$id){
            $this->show_message("此作品不存在！");
            return;
        }
        $conditions = 'w.id = '.$id.' AND owner_id = '.$user['user_id'] ;
        //$conditions = 'w.id = '.$id;
        $sql = "SELECT w.id,w.cloth,w.work_name,w.style,w.designer_id,w.description,w.diy_data,w.add_time,i.iscover,i.img_url FROM
                 cf_works w  JOIN cf_work_imgs i ON w.id=i.work_id WHERE {$conditions} AND source_from='app' ORDER BY i.iscover DESC";
        $db = db();
        $works = $db->getALL($sql);
        foreach($works as $key=>$val){
            //==========获取设计者名称========
            $member_mod = m('member');
            $member = $member_mod->get($val['designer_id']);
            $result['id']            = $val['id'];
            $result['cloth']         = $val['cloth'];
            $result['work_name']     = $val['work_name'];
            $result['designer_id']   = $val['designer_id'];
            $result['designer_name'] = $member['nickname'];
            $result['description']   = $val['description'];
            $result['diy_data']      = $val['diy_data'];
            $result['add_time']      = $val['add_time'];
            $result['imgs'][]        = array_pop($val);
            $result['url'] = 'http://wap.mfd.cn/custom-diy-'.$val['cloth'].'-'.$val['style'].'-mfd-'.$val['id'].'.html';

        }
        $diy_data = json_decode($result['diy_data'],true);
        //遗漏问题
        array_pop($diy_data);

        //ns add 获取面料上下架与diy同步价格
        if($diy_data){
             foreach($diy_data['sysprocess'] as $k=>$v){
                 $params_array[$val['id']][$k] = $v['fabric'];
             }
        }
        if($params_array){
            $works_diys = $diys->_calcPrice($params_array);
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
        $this->assign('result',$result);
        $this->display('my_works.info.html');
    }
    /**
     * 删除作品
     */
    function delWork(){
        $id   = $_POST['id'];
        $conditions = 'id = '.$id;
        $result = $this->works_mod->drop($conditions);
        $i_conditions = 'work_id = '.$id;
        if(!$result){
            $this->json_error('作品删除失败');
        }else{
            $this->imgs_mod->drop($i_conditions);
            $this->json_result();
        }
    }



}

?>
