<?php
use Cyteam\Config\Config;
use Cyteam\Message\ZhichiMessage;
class ProfessorApp extends MallbaseApp
{
   var $_article_mod;
   
    function __construct(){
        parent::__construct();
        $this->_article_mod=&m('article');
        header("Content-Type:text/html;charset=" . CHARSET);
    }

    function online()
    {
        $args=$this->get_params();
        $cate_id=isset($args[0])?intval($args[0]):0;
        //常见问题-专家在线文章分类
    	$_acategory_mod = &m('acategory');
        $_article_mod   = &m('article');
        $setting_obj=new Config();      
        $settings=$setting_obj->get_settings();
        $code=$settings['professor_online_code'];
        $count=0;
        $article_cates=array();//文章分类组
        $cate=$_acategory_mod->get("code='{$code}'");
        if($cate){
            $cate_ids = $_acategory_mod->get_descendant($cate['cate_id']);//小讲堂
        
            $conditions='';
            !empty($cate_ids)&&$conditions='AND article.cate_id'.db_create_in($cate_ids);
            if($conditions){
                $articles = $_article_mod->find(array(
                    'conditions' => 'if_show=1 AND store_id=0 '.$conditions,
                    'fields'     => 'article.article_id,article.title,article.img,article.brief,article.add_time,acategory.cate_name,acategory.cate_id as ac_id',
                    'order'      => 'article.sort_order ASC',
                    // 'limit'      => $limit,
                    'count'      => true,
                    'index_key'  => '',
                    'join'       => 'belongs_to_acategory',
                ));//找出所有符合条件的文章
                $count = $_article_mod->getCount();
                
                //将文章进行分类并统计
                
                foreach ($articles as $key => $value) {
                    $article_cates[$value['ac_id']]['name']=$value['cate_name'];
                    //分类id
                    $article_cates[$value['ac_id']]['list'][$value['article_id']]=$value;
                }
                foreach ($article_cates as $key => $value) {
                    //每个分类下的所有文章数
                    $article_cates[$key]['count']=count($value['list']);
                }
            }
        }
        // print_pre($article_cates);
        $this->assign('articlecount',$count);//所有的专家在线文章数
        $this->assign('acategories',$article_cates);
        // print_pre($article_cates);
        //指定问题分类
        $this->assign('cate_id',$cate_id);
        //专家在线列表
        $zhichi=new ZhiChiMessage();
        $access_token=$zhichi->getAccessToken();
        $actual_data=$zhichi->getActualData($access_token);
        $data=json_decode($actual_data,true);
        $scripts='';
if($_GET['ts']=='gaofei'){
    echo '<xmp>';
    var_dump($access_token);
    print_r($data);
    exit;
}
        if($data['status']){
            $adminList=$data['data']['adminList'];
            if($adminList){
                //如果用户登录拼接用户信息
                if($this->visitor->has_login){
                    $userInfo = $this->visitor->get();
                    $str="&partnerId={$userInfo['user_id']}&uname={$userInfo['nickname']}&realname={$userInfo['user_name']}&tel={$userInfo['phone_mob']}&face={$userInfo['avatar']}";
                }else{
                    $str='';
                }
                foreach ($adminList as $key => $value) {
                    if(empty($value['groupId'])){
                        unset($adminList[$key]);
                        continue;
                    }
                    $groupId=$value['groupId'][0];
                    $adminList[$key]['url']="http://www.sobot.com/chat/pc/index.html?sysNum=2b17cdee375a475e963aeed478c37fbf{$str}&modulType=2&groupId={$groupId}&from=iframe&visitTitle={$value['realname']}";
                }
            }
            
        }
        $this->assign('kefucount',count($adminList));
        $this->assign('adminList',$adminList);
        $this->assign('user_id',$_SESSION['user_info']['user_id']);
// var_dump($adminList);
    	$this->import_resource(array(
    			'script' => 'jquery-1.8.3.min.js,flickerplate.min.js,html5.js,css3-mediaqueries.js,jquery.plugins/jquery.validate.js,layer/layer.min.js,jquery.cookie.js,jscrollpane.js,change.city.js',
    			'style'  => "layer/skin/layer.css",
    	));
    	$this->_config_seo('title', '专家在线 - mfd麦富迪');
    	$this->display("professor/online.html");
    }

    /*常见问题详情*/
    function view()
    {
        $arg = $this->get_params();
        $article_id = empty($arg[0]) ? 0 : intval($arg[0]);
         $article = $this->_article_mod->get(array(
                'conditions'=>"article.article_id={$article_id}",
                'fields'=>'article.*,acategory.cate_name',
                'join'=>'belongs_to_acategory'
            ));
         //点击数加1
         $this->_article_mod->setInc($article_id,'click_num');
         //同一分组下的其他文章
         $conditions="AND article.cate_id={$article['cate_id']} AND article.article_id!={$article_id}";
          $articles = $this->_article_mod->find(array(
                    'conditions' => 'article.if_show=1 AND store_id=0 '.$conditions,
                    'fields'     => 'article.article_id,article.title,article.img,article.brief,article.add_time,acategory.cate_name,acategory.cate_id as ac_id',
                    'order'      => 'article.sort_order ASC',
                     'limit'      => 10,
                    'count'      => true,
                    'index_key'  => '',
                    'join'       => 'belongs_to_acategory',
                ));//找出所有符合条件的文章
         $this->assign('article',$article);
         $this->assign('rel_articles',$articles);
         $this->_config_seo('title', $article['title'].' - mfd麦富迪');
         return $this->display('professor/view.html');
     }

}

?>