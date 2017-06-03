<?php
class TailorApp extends MallbaseApp
{
    var $comment_mod;
    function __construct(){
        parent::__construct();
        $this->comment_mod = &m("ordercomment");
        header("Content-Type:text/html;charset=" . CHARSET);
    }
    /**
    * @deprecated todo
    * @param pType pValueName pInfo
    * @return rType rValueName rInfo
    * @access public
    * @see functionName
    * @version 1.0.0 (2014-12-12)
    * @author ns
    */
    function index()
    {
       //$args = $this->get_params();
       $conditions = $this->_get_query_conditions(array(array(
                'field' => 'nickname',         //可搜索字段title
                'equal' => 'LIKE',          //等价关系,可以是LIKE, =, <, >, <>
            ),
        ));
       //获取当前地区
       $citycode = $_COOKIE['cityCode'];
       $region['region_id'] = 0;
       if($citycode){
          $region_mod =& m('region');
          $region_info = $region_mod->get(array('fields'=>'region_id','conditions'=>'citycode='.$citycode));
          $region['region_id'] = $region_info['region_id'];
       }

       $where_sql = '';
       $type_id = empty($_GET['type_id']) ? 0 : intval($_GET['type_id']);
       $content_id = empty($_GET['content_id']) ? 0 : intval($_GET['content_id']);
       $sorting = empty($_GET['sorting']) ? 'popularity' : $_GET['sorting'];
       if($type_id > 0 && $content_id > 0){
            $store_attr_mod =& m('store_attr');
            $store_list = $store_attr_mod->find(array('conditions'=>'type_id='.$type_id.' and content_id='.$content_id));
            foreach($store_list as $v){
                $arrt_store .= $v['store_id'].',';
            }
            $where_sql ="state=1 and store_id in(".$arrt_store."'')";
       }else{
            $where_sql ='state=1'. $conditions;
       }
        $store_mod =& m('store');
        $page   =   $this->_get_page(20);    //获取分页信息
        $tailor_list = $store_mod->find(array(
            //'fields'=> '*',
            //'conditions' => 'city_id ='.$region['region_id'] .' and '.$where_sql,
            'conditions' => $where_sql,
            'count' => true,
            'order' => $sorting.' desc',
            'limit' => $page['limit'],
        ));
        //获取头像
        require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
        $objAvatar = new Avatar();
        foreach($tailor_list as $k=>$val){
            $tailor_list[$k]['member_logo'] = $objAvatar->avatar_show($val['store_id'],'big',1);
        }
        $page['item_count'] = $store_mod->getCount();
        $this->_format_page($page);
        $this->assign('tailor_list',$tailor_list);
        if($type_id > 0 && $content_id > 0){
            $page['parameter'] = '?sorting='.$sorting.'&type_id='.$type_id.'&content_id='.$content_id;
            $zt_content = lang::get('store_arrt_list');
            $content['name'] = $zt_content[2][$content_id]['name'];
            $content['content_id'] = $content_id;
            $this->assign('content', $content);
         }else{
            $page['parameter'] = '?sorting='.$sorting;
         }
        $this->assign('page_info', $page);          //将分页信息传递给视图，用于形成分页条
        $this->display('tailor/tailor.index.html');
    }


   /**
    * 获取详细信息
    */
   function info(){
        $args = $this->get_params();
        $id = empty($args[0]) ? 0 : intval($args[0]);
        if (!$id)
        {
            $this->show_warning(lang::get('no_tailor'));
            return;
        }
        
        $public = $this->publicInfo($id);
        
        
        $this->assign("public", $public);
        
        $store_mod =& m('store');
        $tailor = $store_mod->get(array('conditions' => 'store_id='. $id));
        if(!$tailor){
            $this->show_warning(lang::get('no_tailor'));
            return;
        }
        //获取裁缝的用户头像
        require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
        $objAvatar = new Avatar();
        $tailor['member_logo'] = $objAvatar->avatar_show($tailor['store_id'],'big');
        //添加人气
        $store_mod->edit("store_id='{$id}'",array('popularity'=>$tailor['popularity']+1));
        //前端显示是+1的状态
        $tailor['popularity'] = $tailor['popularity']+1;

        //判断是否关注裁缝
        $user_follow_mod =& m('userfollow');
        $user_follow = $user_follow_mod->get(array('uid'=>$this->visitor->get('user_id'), 'follow_uid'=>$id));
        $is_follow = empty($user_follow) ? 1 : 0;

        $storephoto_mod =& m('storephoto');
        $storephoto = $storephoto_mod->find(array('conditions'=>'store_id='. $id,'limit' => '16'));
        $tree = array();
        $group = 1;
        $num = 0;
        $select=0;
        foreach($storephoto as $key => $val){
            $tree[$group]['children'][$val["id"]]['url'] = getUserPhotoUrl('works',$val['url'],200);
            if($val['id'] == $article_id){
                $select = $group;
            }
            $num++;
            if($num%4 == 0) $group+=1;
        }
        $store_attr =& m('store_attr');
        $attr_list = $store_attr->find(array('conditions'=>'store_id='.$id));
        //type_id 1、服务2、风格
        foreach($attr_list as $val){
            if($val['type_id'] == 1){
                $tailor['attr']['service'] .= $val['attr_name'].'、';
            }elseif($val['type_id'] == 2){
                $tailor['attr']['style'] .= $val['attr_name'].'、';
            }
        }
        $tailor['attr']['service'] = rtrim($tailor['attr']['service'],'、'); 
        $tailor['attr']['style'] = rtrim($tailor['attr']['style'],'、'); 

        /* add by xiao5 裁缝等级 */
        if ($tailor['level'])
        {
          $member_lv_mod  =& m('memberlv');
          $lvs = $member_lv_mod->get(array(
              'conditions'    => 'member_lv_id = '.$tailor['level'],
              'fields'        => 'name,lv_logo,lv_type',//等级名称 ，等级logo
          ));
          $tailor['lv_name'] = $lvs['name'];
          $tailor['lv_logo'] =  site_url().$lvs['lv_logo'];
          $tailor['lv_type'] = $lvs['lv_type'];
        }
        $this->assign('store_service', $store_service);
        //获取当前用户个人信息
        $member_mod =& m('member');
        $tailor['member'] = $member_mod->get(array('conditions'=>'user_id='.$id));

        $this->assign('tailor', $tailor);
        $this->assign('tree', $tree);
        $this->assign('act', 'info');
        $this->assign('is_follow', $is_follow);
         

         $_od = &m("order");
         $_oc = &m("ordercomment");
         
         $allowCM = 0;
         if($this->visitor->has_login){
             $_tmpids = $_od->find(array(
                  'conditions' => "user_id='{$this->visitor->get("user_id")}' AND store_id='{$id}' AND status='".ORDER_FINISHED."'",
                  'fields' => "order_id",
                   ));
             
             $ids = array();
             foreach((array) $_tmpids as $key => $val){
                 $ids[] = $val['order_id'];
             }
             
             $ocNum = $_oc->_count("member_id='{$this->visitor->get("user_id")}' AND order_id ".db_create_in($ids));
             
             if(count($ids) > $ocNum){
                 $allowCM = 1;
             }
         }
         
         $this->assign("allowCM", $allowCM);
         
        $this->display('tailor/tailor.info.html');
   }
   //设计上传作品
   function service(){
        $args = $this->get_params();
        $store_id = empty($_GET['store_id']) ? 0 : intval($_GET['store_id']);
        if (!$store_id)
        {
            $this->show_warning(lang::get('no_tailor'));
            return;
        }
        $public = $this->publicInfo($store_id);
        
        
        $this->assign("public", $public);
        $store_mod =& m('store');
        $page   =   $this->_get_page(3);    //获取分页信息
        $tailor = $store_mod->get(array('conditions' => 'store_id='. $store_id));
        if(!$tailor){
            $this->show_warning(lang::get('no_tailor'));
            return;
        }
        //获取裁缝的用户头像
        require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
        $objAvatar = new Avatar();
        $tailor['member_logo'] = $objAvatar->avatar_show($tailor['store_id'],'big');

        $store_service_mod =& m('store_service');
        $storephoto_mod =& m('storephoto');
        $store_service = $store_service_mod->find(array(
            'conditions' => 'store_id='. $store_id,
            'count' => true,
            'order' => 'add_time desc',
        ));
        foreach($store_service as $k=>$v){
            $store_service[$k]['storephoto'] = $storephoto_mod->find(array('conditions'=>'album_id='.$v['id'],'order' => 'id desc'));
            foreach($store_service[$k]['storephoto'] as $key=>$val){
                $store_service[$k]['storephoto'][$key]['url'] = getUserPhotoUrl('works',$val['url']);
            }
        }
        /* add by xiao5 裁缝等级 */
        if ($tailor['level'])
        {
        	$member_lv_mod  =& m('memberlv');
        	$lvs = $member_lv_mod->get(array(
        			'conditions'    => 'member_lv_id = '.$tailor['level'],
        			'fields'        => 'name,lv_logo,lv_type',//等级名称 ，等级logo
        	));
        	$tailor['lv_name'] = $lvs['name'];
        	$tailor['lv_logo'] =  site_url().$lvs['lv_logo'];
        	$tailor['lv_type'] = $lvs['lv_type'];
        }
        $this->assign('store_service', $store_service);
        $this->assign('tailor', $tailor);
        
        $this->assign('act', 'service');
        $this->display('tailor/service.index.html');
   }

   function publicInfo($id){
       
       $list = $this->comment_mod->find(array(
           'conditions' => "tailor_id = '{$id}'",
           'count' => true,
       ));
       
       $count = $this->comment_mod->getCount();
       
       $s1 = 0;
       $s2 = 0;
       $s3 = 0;
       
       foreach($list as $key => $val){
           if($val['approve'] == 1){
               $s1 +=1;
           }elseif($val['approve'] == 2 || $val['approve'] == 3){
               $s2 +=1;
           }else{
                $s3+=1;   
           }
       }
       
       $sarr = array(
           's1' => $count != 0 ? round(($s1/$count)*100,0) : 0,
           's2' => $count != 0 ? round(($s2/$count)*100,0) : 0,
           's3' => $count != 0 ? round(($s3/$count)*100,0) : 100,
       );
       
       return array('count' => $count, 'avg' => $sarr['s3'], 'sarr' => $sarr, 'val' => $sarr['s3']*90);
   }
   
   //获取用户评论
   function loadComment(){
   
       require(ROOT_PATH . '/includes/avatar.class.php');
   
       $face = $this->objAvatar = new Avatar();
   
       $arg = $this->get_params();
   
       $max = isset($_GET['max']) ? intval($_GET['max']) : 0;
   
       $conditions = "(status = 1 OR member_id = '{$this->visitor->get("user_id")}') AND tailor_id = '{$arg[0]}'";
   
       if($max){
           $conditions .= " AND id < '{$max}'";
       }
   
       $comment_list = $this->comment_mod->find(array(
           'conditions' => $conditions,
           'limit' => 7,
           'count' => 1,
           'order' => 'addtime DESC'
       ));
   
       $count = $this->comment_mod->getCount();
   
        
       foreach($comment_list as $key => $val){
           $comment_list[$key]['ftime'] = TimeFormat($val['addtime']);
           $comment_list[$key]['face'] = $face->avatar_show_src($val['member_id'],'big');
           $comment_list[$key]['imgs'] = explode(",", $val["imgs"]);
       }
       $end = end($comment_list);
       $this->assign("comment_list", $comment_list);
   
       $content =$this->_view->fetch("tailor/comment_list.html");
   
       $arr = array(
           'content' => $content,
           'count' => $count < 0 ? 0 : $count,
           'next' => $count > 7 ? 1 : 0,
           'max' => $end['id']
       );
   
       $this->json_result($arr);
   }
}

?>