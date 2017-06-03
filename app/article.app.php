<?php
use Cyteam\Config\Config;
class ArticleApp extends MallbaseApp
{

    var $_article_mod;
    var $_acategory_mod;
    var $_ACC; //系统文章cate_id数据
    var $_cate_ids; //当前分类及子孙分类cate_id
    function __construct()
    {
        $this->ArticleApp();
        $this->_config_seo('title', 	"麦富迪讲堂");
    }
    function ArticleApp()
    {
        parent::__construct();
        $this->_article_mod = &m('article');
        $this->_acategory_mod = &m('acategory');
        /* 获得系统分类cate_id数据 */
        $this->_ACC = $this->_acategory_mod->get_ACC();
    }
    function index()
    {
        $_acategory_mod = &m('acategory');
        $_article_mod   = &m('article');
        $setting_obj=new Config();      
        $settings=$setting_obj->get_settings();
        $code=$settings['help_center_code'];
        $cate=$_acategory_mod->get("code='{$code}'");
        $cate_ids = $_acategory_mod->get_descendant($cate['cate_id']);//小讲堂
        
        $conditions='';
        !empty($cate_ids)&&$conditions='AND cate_id'.db_create_in($cate_ids);
        $articles=array();
        if($conditions){
            $articles = $_article_mod->find(array(
                'conditions' => 'if_show=1 AND store_id=0 '.$conditions,
                'fields'     => 'article_id,title,img,brief,add_time',
                'order'      => 'sort_order ASC',
                'count'      => true,
                'index_key'  => '',
            ));//找出所有符合条件的文章
            $count = $_article_mod->getCount();
        }
        
        
//  echo '<pre>';
//  var_dump($articles);
//  die();
        $this->assign('articles',$articles);
        return $this->display('article/index.html');
        /* 取得导航 */
        $this->assign('navs', $this->_get_navs());

        /* 处理cate_id */
        $cate_id = !empty($_GET['cate_id'])? intval($_GET['cate_id']) : $this->_ACC[ACC_NOTICE]; //如果cate_id为空则默认显示商城快讯
        isset($_GET['code']) && isset($this->_ACC[trim($_GET['code'])]) && $cate_id = $this->_ACC[trim($_GET['code'])]; //如果有code
        /* 取得当前分类及子孙分类cate_id */
        $cate_ids = array();
        if ($cate_id > 0 && $cate_id != $this->_ACC[ACC_SYSTEM]) //排除系统内置分类
        {
            $cate_ids = $this->_acategory_mod->get_descendant($cate_id);
            if (!$cate_ids)
            {
                $this->show_warning('no_such_acategory');
                return;
            }
        }
        else
        {
            $this->show_warning('no_such_acategory');
            return;
        }
        $this->_cate_ids = $cate_ids;
        /* 当前位置 */
        $curlocal = $this->_get_article_curlocal($cate_id);
        unset($curlocal[count($curlocal)-1]['url']);
        $this->_curlocal($curlocal);
        /* 文章分类 */
        $acategories = $this->_get_acategory(0);
        /* 分类下的所有文章 */
        $all = $this->_get_article('all');
        $articles = $all['articles'];
        $page = $all['page'];
        /* 新文章 */
        $new = $this->_get_article('new');
        $new_articles = $new['articles'];

        // 页面标题
        $category = $this->_acategory_mod->get_info($cate_id);
        $this->_config_seo('title', $category['cate_name'] . ' - ' . Conf::get('site_title'));

        $this->assign('articles', $articles);
        $this->assign('new_articles', $new_articles);
        $this->_format_page($page);
        $this->assign('page_info', $page);
        $this->assign('acategories', $acategories);
        $this->display('article.index.html');
    }

    function view()
    {
        $arg = $this->get_params();
        $article_id = empty($arg[0]) ? 0 : intval($arg[0]);
        $article = $this->_article_mod->get('article_id=' . $article_id);
        //点击数加1
         $this->_article_mod->setInc($article_id,'click_num');
         //同一分组下的其他文章
         $conditions="AND article.cate_id={$article['cate_id']} AND article.article_id!={$article_id}";
          $articles = $this->_article_mod->find(array(
                    'conditions' => 'article.if_show=1 AND store_id=0 '.$conditions,
                    'fields'     => 'article.article_id,article.title,article.img,article.brief,article.add_time,acategory.cate_name,acategory.cate_id as ac_id',
                    'order'      => 'article.sort_order ASC',
                    // 'limit'      => $limit,
                    'count'      => true,
                    'index_key'  => '',
                    'join'       => 'belongs_to_acategory',
                ));//找出所有符合条件的文章
         $this->assign('article',$article);
         $this->assign('rel_articles',$articles);

        $this->_config_seo('title', $article['title'] . ' - ' . Conf::get('site_title'));
        return $this->display('article/view.html');
        $cate_ids = array();
        if ($article_id>0)
        {
            $article = $this->_article_mod->get('article_id=' . $article_id . ' AND code = "" AND if_show=1 AND store_id=0');
            if (!$article)
            {
                $this->show_warning('no_such_article');
                return;
            }
            if ($article['link']){ //外链文章跳转
                header("HTTP/1.1 301 Moved Permanently");
                header('location:'.$article['link']);
                return;
            }
            /* 上一篇下一篇 */
            $pre_article = $this->_article_mod->get('article_id<' . $article_id . ' AND code = "" AND if_show=1  AND store_id=0 ORDER BY article_id DESC limit 1');
            $pre_article && $pre_article['target'] = $pre_article['link'] ? '_blank' : '_self';
            $next_article = $this->_article_mod->get('article_id>' . $article_id . ' AND code = "" AND if_show=1  AND store_id=0 ORDER BY article_id ASC limit 1');
            $next_article && $next_article['target'] = $next_article['link'] ? '_blank' : '_self';
            if ($article)
            {
                $cate_id = $article['cate_id'];
                /* 取得当前分类及子孙分类cate_id */
                $cate_ids = $this->_acategory_mod->get_descendant($cate_id);
            }
            else
            {
                $this->show_warning('no_such_article');
                return;
            }
        }
        else
        {
            $this->show_warning('no_such_article');
            return;
        }
        //ns add
        $title_info = $this->_get_title_info();
        $this->assign("title",$title_info[$article_id]['title']);
        $this->assign('keywords',$title_info[$article_id]['keywords']);
        $this->assign('description',$title_info[$article_id]['description']);


        $this->_cate_ids = $cate_ids;
        /* 当前位置 */
        $curlocal = $this->_get_article_curlocal($cate_id);
        $curlocal[] =array('text' => Lang::get('content'));
        $this->_curlocal($curlocal);

        /*文章分类*/
        $acategories = $this->_get_acategory(0);
        //ns add 获取子集分类内容
        foreach($acategories as $k=>$aca){
             $acategories[$k]['article'] = $this->_article_mod->find(array(
                    'conditions' => "cate_id = ". $aca['cate_id'] ." AND if_show = 1",
             ));
        }
        /* 新文章 */
        $new = $this->_get_article('new');
        $new_articles = $new['articles'];
        $this->assign('article', $article);
        $this->assign('pre_article', $pre_article);
        $this->assign('next_article', $next_article);
        $this->assign('new_articles', $new_articles);
        $this->assign('acategories', $acategories);
        $this->assign('act_article_id',$article_id);
        //$this->_config_seo('title', $article['title'] . ' - ' . Conf::get('site_title'));
        $this->display('article.view.html');
    }

    function _get_title_info(){
        $title_list = array(
            //定制流程说明
            '32' => array(
               'title'=> '注册及登录-RCTAILOR-定制是一种生活态度(rctailor.com)',
               'keywords' => 'RCTAILOR、 个性化定制、注册及登录',
               'description' => '注册及登录注意事项、注册及登录指南- RCTAILOR-定制是一种生活态度(rctailor.com)',
            ),
            //定制流程说明
            '48' => array(
               'title'=> '定制流程-RCTAILOR-定制是一种生活态度(rctailor.com)',
               'keywords' => '西服定制流程、服装定制流程、RCTAILOR、 个性化定制、定制流程说明',
               'description' => 'RCTAILOR个性化西服定制、西服套装定制、衬衫定制、西裤定制、马甲定制；定制流程说明-RCTAILOR-定制是一种生活态度(rctailor.com)',
            ),
            //企业文化
            '59' => array(
               'title'=> '企业文化、个性化定制平台、服装定制企业- RCTAILOR-定制是一种生活态度(rctailor.com)',
               'keywords' => 'RCTAILOR、西服定制企业、个性化定制',
               'description' => 'RCTAILOR企业简介、企业优势-RCTAILOR-定制是一种生活态度(rctailor.com)',
            ),
            //产品简介与优势
            '25' => array(
               'title'=> '产品简介与优势-RCTAILOR-定制是一种生活态度(rctailor.com)',
               'keywords' => 'RCTAILOR产品简介、企业优势、产品优势',
               'description' => 'RCTAILOR个性化西服定制、西服套装定制、衬衫定制、西裤定制、马甲定制- RCTAILOR-定制是一种生活态度(rctailor.com)',
            ),
            //联系我们
            '45' => array(
               'title'=> 'RCTAILOR联系方式及地址-RCTAILOR-定制是一种生活态度(rctailor.com)',
               'keywords' => 'RCTAILOR、RCTAILOR联系方式、RCTAILOR地址',
               'description' => '您可以通过RCTAILOR地址、RCTAILOR电话等进一步了解RCTAILOR个性化定制平台-定制是一种生活态度(rctailor.com)',
            ),
            //法律声明
            '53' => array(
               'title'=> '法律声明、知识产权-RCTAILOR-定制是一种生活态度(rctailor.com)',
               'keywords' => 'RCTAILOR、西服定制、知识产权',
               'description' => '关于RATAILOR知识产权及客户隐私保护的法律声明-RCTAILOR-定制是一种生活态度(rctailor.com)',
            ),            
            //伙伴计划
            '60' => array(
               'title'=> 'RCTAILOR招商加盟、企业优势- RCTAILOR-定制是一种生活态度(rctailor.com)',
               'keywords' => 'RCTALLOR、招商加盟、个性化定制、加盟流程',
               'description' => 'RCTAILOR招商加盟篇、企业优势及加盟优势、加盟流程 RCTAILOR-定制是一种生活态度(rctailor.com)',
            ),
            //媒体报道
            '16' => array(
               'title'=> '媒体报道、RATAILOR相关新闻-RCTAILOR-定制是一种生活态度(rctailor.com)',
               'keywords' => 'RCTAILOR、西服定制、新闻热点',
               'description' => 'RATAILOR关于微信的、电台的、杂志的、采访的等多个方向的媒体报道-RCTAILOR-定制是一种生活态度(rctailor.com)',
            ),
            //诚聘英才
            '52' => array(
               'title'=> '诚聘英才-RCTAILOR-定制是一种生活态度(rctailor.com)',
               'keywords' => 'RCTAILOR、诚聘英才、企业招聘',
               'description' => 'RATAILOR招聘公告、员工福利、工作环境-RCTAILOR-定制是一种生活态度(rctailor.com)',
            ),
            //网站地图
            '54' => array(
               'title'=> '网站地图-RCTAILOR-定制是一种生活态度(rctailor.com)',
               'keywords' => 'RCTAILOR、网站地图',
               'description' => '网站地图、网址导航-RCTAILOR-定制是一种生活态度(rctailor.com)',
            ),
            //原料&选材
            '66' => array(
               'title'=> '西服面料、西服定制原料、西服面料品牌-RCTAILOR-定制是一种生活态度(rctailor.com)',
               'keywords' => '西服面料、面料品牌、高端面料、国际大牌面料、RCTAILOR',
               'description' => 'RATAILOR西服定制选材、西服面料、国际大牌面料-RCTAILOR-定制是一种生活态度(rctailor.com)',
            ),
            //工艺说明
            '67' => array(
               'title'=> '西服定制、西服定制工艺、西服定制设计-RCTAILOR-定制是一种生活态度(rctailor.com)',
               'keywords' => '西服定制、西服定制工艺、手工西服定制',
               'description' => 'RATAILOR独一无二的定制工艺、工艺优势、西服定制工艺-RCTAILOR-定制是一种生活态度(rctailor.com)',
            ),
            //保养说明
            '68' => array(
               'title'=> '西服保养、定制西服保养、西服保养及洗涤、西服穿着知识-RCTAILOR-定制是一种生活态度(rctailor.com)',
               'keywords' => '西服保养、西服洗涤、西服干洗、西服穿着知识',
               'description' => 'RATAILOR优质西服保养之道、西服保养及洗涤知识-RCTAILOR-定制是一种生活态度(rctailor.com)',
            ),
            //成衣量体规范
            '61' => array(
               'title'=> '成衣量体规范、西服尺码、西服定制量体-RCTAILOR-定制是一种生活态度(rctailor.com)',
               'keywords' => '西服定制量体、西服尺码、大衣尺码、衬衣尺码、尺码对照表',
               'description' => 'RATAILOR成衣量体规范、西服尺码、西服定制规范-RCTAILOR-定制是一种生活态度(rctailor.com)',
            ),
            //标准尺码对照表
            '62' => array(
               'title'=> '西服尺码对照表、西服尺码、尺码对照表-RCTAILOR-定制是一种生活态度(rctailor.com)',
               'keywords' => '西服尺码、大衣尺码、衬衣尺码、、西裤尺码、尺码对照表',
               'description' => '西服定制尺码对照表、量体尺码对照表-RCTAILOR-定制是一种生活态度(rctailor.com)',
            ),
            //量体服务点查询
            '63' => array(
               'title'=> '量体服务点查询、免费量体-RCTAILOR-定制是一种生活态度(rctailor.com)',
               'keywords' => '免费量体、上门量体',
               'description' => 'RATAILOR量体服务点查询-RCTAILOR-定制是一种生活态度(rctailor.com)',
            ),
            //净体量体规范
            '64' => array(
               'title'=> '西服定制量体、量体规范、量体方法-RCTAILOR-定制是一种生活态度(rctailor.com)',
               'keywords' => '西服定制、量体定制、免费量体',
               'description' => '西服定制量体规范、西服定制量体方法、免费量体-RCTAILOR-定制是一种生活态度(rctailor.com)',
            ),
            //物流说明
            '70' => array(
               'title'=> '物流说明-RCTAILOR-定制是一种生活态度(rctailor.com)',
               'keywords' => '物流说明',
               'description' => 'RATAILOR物流说明-RCTAILOR-定制是一种生活态度(rctailor.com)',
            ),
            //常见问题
            '71' => array(
               'title'=> '常见问题-RCTAILOR-定制是一种生活态度(rctailor.com)',
               'keywords' => '常见问题',
               'description' => 'RATAILOR西服定制常见问题-RCTAILOR-定制是一种生活态度(rctailor.com)',
            ),
            //返修及其退换货说明
            '65' => array(
               'title'=> '返修及其退换货说明-RCTAILOR-定制是一种生活态度(rctailor.com)',
               'keywords' => '西服定制返修、西服定制退换',
               'description' => 'RATAILOR返修及其退换货说明-RCTAILOR-定制是一种生活态度(rctailor.com)',
            )
        );
        return $title_list;
    }


    function system()
    {
        $arg = $this->get_params();
        
        $code=$arg[0];
        
        if (!$code)
        {
            $this->show_warning('no_such_article');
            return;
        }
        $article = $this->_article_mod->get("code='" . $code . "'");
        if (!$article)
        {
            $this->show_warning('no_such_article');
            return;
        }
        if ($article['link']){ //外链文章跳转
                header("HTTP/1.1 301 Moved Permanently");
                header('location:'.$article['link']);
                return;
            }

        /*当前位置*/
        $curlocal[] =array('text' => $article['title']);
        $this->_curlocal($curlocal);
        /*文章分类*/
        $acategories = $this->_get_acategory('');
        /* 新文章 */
        $new = $this->_get_article('new');
        $new_articles = $new['articles'];
        $this->assign('acategories', $acategories);
        $this->assign('new_articles', $new_articles);
        $this->assign('article', $article);

        $this->_config_seo('title', $article['title'] . ' - ' . Conf::get('site_title'));
        $this->display('article.view.html');

    }

    function _get_article_curlocal($cate_id)
    {
        $parents = array();
        if ($cate_id)
        {
            $acategory_mod = &m('acategory');
            $acategory_mod->get_parents($parents, $cate_id);
        }
        foreach ($parents as $category)
        {
            $curlocal[] = array('text' => $category['cate_name'], 'ACC' => $category['code'], 'url' => 'index.php?app=article&amp;cate_id=' . $category['cate_id']);
        }
        return $curlocal;
    }
    function _get_acategory($cate_id)
    {
        $acategories = $this->_acategory_mod->get_list($cate_id);
        if ($acategories){
            unset($acategories[$this->_ACC[ACC_SYSTEM]]);
            return $acategories;
        }
        else
        {
            $parent = $this->_acategory_mod->get($cate_id);
            if (isset($parent['parent_id']))
            {
                return $this->_get_acategory($parent['parent_id']);
            }
        }
    }
    function _get_article($type='')
    {
        $conditions = '';
        $per = '';
        switch ($type)
        {
            case 'new' : $sort_order = 'add_time DESC,sort_order ASC';
            $per=5;
            break;
            case 'all' : $sort_order = 'sort_order ASC,add_time DESC';
            $per=10;
            break;
        }
        $page = $this->_get_page($per);   //获取分页信息
        !empty($this->_cate_ids)&& $conditions = ' AND cate_id ' . db_create_in($this->_cate_ids);
        $articles = $this->_article_mod->find(array(
            'conditions'  => 'if_show=1 AND store_id=0 AND code = ""' . $conditions,
            'limit'   => $page['limit'],
            'order'   => $sort_order,
            'count'   => true   //允许统计
        )); //找出所有符合条件的文章
        $page['item_count'] = $this->_article_mod->getCount();
        foreach ($articles as $key => $article)
        {
            $articles[$key]['target'] = $article[link] ? '_blank' : '_self';
        }
        return array('page'=>$page, 'articles'=>$articles);
    }
}

?>
