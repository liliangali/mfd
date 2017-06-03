<?php

/**
 *    达人上传设置控制器
 *
 *    @author    Garbin
 *    ns
 */
class My_singleApp extends MemberbaseApp
{
    /**
     * 重写模板文件
     * @return void
     * @access public
     * @see _config_view
     * @version 1.0.0 (2014-11-17)
     * @author yhao.bai
     */
    function _config_view()
    {
        parent::_config_view();
        $template_name = $this->_get_template_name();
        $style_name    = $this->_get_style_name();
        $this->_view->template_dir = ROOT_PATH . "/view/".LANG."/mall/{$template_name}/user_center";
        $this->_view->compile_dir  = ROOT_PATH . "/temp/compiled/mall/{$template_name}/user_center";
        $this->_view->res_base     = SITE_URL . "/view/".LANG."/mall/{$template_name}/styles/{$style_name}";
    }
    function index()
    {
        $member_single_mod =& m('member_single');
        $userphoto_mod = m('userphoto');
        $page   =   $this->_get_page(12);    //获取分页信息
        $single = $member_single_mod->find(array(
            'conditions'=>'member_id='.$this->visitor->get('user_id'),
            'count' => true,
            'limit' => $page['limit'],
        ));
        foreach($single as $k=>$v){
            $single[$k]['img_url'] = getUserPhotoUrl('shaidan',$v['img_url'],200);
            // $single[$k]['userphoto'] = $userphoto_mod->find(array('conditions'=>'uid='.$this->visitor->get('user_id').' and album_id='.$v['id'] ));
            // foreach($single[$k]['userphoto'] as $key=>$val){
            //     $single[$k]['userphoto'][$key]['url'] = getUserPhotoUrl('shaidan',$val['url'],200);
            // }
        }
        $page['item_count'] = $member_single_mod->getCount();
        $this->_format_page($page);
        
        $this->assign('type', 'user');
        
        /* 头像 add by xiao5 START */
        require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
        $objAvatar = new Avatar();
        
        //获取头像
        $avatar = $objAvatar->avatar_show($this->visitor->get('user_id'),'big');
        $this->assign('avatar', $avatar);
        
        
        $this->_curitem('my_sunsingle');
        $this->assign('single', $single);
        $this->assign('page_info', $page);          //将分页信息传递给视图，用于形成分页条
        $this->display('my_single.index.html');
    }
    //添加信息
    function add(){
        if(!$_POST){

            $user = $this->visitor->get();
            $this->assign('type', 'user');
            $this->assign('ac', 'index');
            /* 头像 add by xiao5 START */
            require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
            $objAvatar = new Avatar();
            
            //获取头像
            $avatar = $objAvatar->avatar_show($this->visitor->get('user_id'),'big');
            $this->assign('avatar', $avatar);

             /* 当前用户中心菜单 */
            $this->_curitem('my_sunsingle');
            $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('my_single'));

            //$this->assign('type','add');
            $this->display('my_single.form.html');
        }else{
            $m = m('userphoto');
            $img_total = 10;
            $title = $_REQUEST['img_name'];
            $cate = $_REQUEST['type_id'];
            $desc = $_REQUEST['description'];
            	
           $member_single_mod =& m('member_single');
            $data = array(
                'add_time'=>time(),
                'img_name'=>$title,
                'description'=>$desc,
                'member_id'=>$this->visitor->info['user_id'],
                'state' => 1,
                'type_id'=>$cate,
            );
            //封面图地址
            $is_top = 0;
            $top_url = $_REQUEST['top_url'];
            if($top_url){
                $is_top = 1;
                $data['img_url'] = $top_url;
            }
            $single_id = $member_single_mod->add($data);
            
            $j=0;
            for($i=0;$i<=$img_total;$i++){
                $img = $_REQUEST['input_'.$i];
                //if($img && $img != $top_url){
                if($img){
                    $data = array(
                        'add_time'=>time(),
                        'url'=>$img,
                        'uid'=>$this->visitor->info['user_id'],
                        'cate'=>$cate,
                        'album_id'=>$single_id,
                        'status'=> Conf::get('if_photo'),
                        'cate' =>3  //类型。晒图
                    );
                    $m->add($data);
                    		
                    if(!$is_top){
                        $new_data = array('img_url'=>$img);
                        $member_single_mod->edit($single_id,$new_data);
                        $is_top = 1;
                    }
                    $j++;
                }
            }
            //用户添加晒图数量加1
            $member_mod =& m('member');
            $member_mod->setInc(array('user_id='.$this->visitor->info['user_id']),'single');

            $this->show_message('添加作品成功！','go_back', '/my_single.html');
        }
       
    }

     //删除裁缝晒单
    function drop(){
        $args = $this->get_params();
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if(!$id){
            $this->show_warning('此作品无效!');

        }
        $member_single_mod =& m('member_single');
        $single = $member_single_mod->get($id);
        if(!$single){
            $this->show_warning('删除失败!');
        }
        $drop_info = $member_single_mod->drop($id);
        $userphoto_mod = m('userphoto');
        $userphoto_mod->drop('album_id='.$id);
        if($drop_info){
            $this->show_message('删除成功','go_back', '/my_single.html');
        }
    }

    
    //ajax-上传图片-非ACTION
    function upload(){
        $num = $_REQUEST['num'];
    
        $dir = $_SERVER['DOCUMENT_ROOT'].'/upload_user_photo/shaidan/';
        $fileName = $this->visitor->info['user_id'] ."_" . $num . "_" .md5( uniqid() . mt_rand(0,255) ) . ".jpg";
    
        $fileDirName2 = $dir ."235x315/" . $fileName;
        pro_img('up_file',235,315,$fileDirName2);
        $fileDirName3 = $dir ."520x685/" . $fileName;
        pro_img('up_file',520,685,$fileDirName3);
    
        $fileDirName1 = $dir ."original/" . $fileName;
        $rs = move_uploaded_file($_FILES['up_file']["tmp_name"],$fileDirName1);
    
    
        $src= get_domain() . "/upload_user_photo/shaidan/235x315/".$fileName;
        $arr = array('src'=>$src,'file'=>$fileName);
        echo json_encode($arr);
        exit;
    }
    
}
?>