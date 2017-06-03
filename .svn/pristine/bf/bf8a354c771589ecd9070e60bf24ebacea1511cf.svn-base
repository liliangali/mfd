<?php

/* 商品 */
class GoodsApp extends StorebaseApp
{
    var $_goods_mod;
    function __construct()
    {


        $this->GoodsApp();
    }
    function GoodsApp()
    {
        parent::__construct();
        $this->_goods_mod =& m('goods');
        $this->_brand_mod =& m('brand');
        $this->_part_mod = & m("part");

    }

    function index()
    {
    	$args = $this->get_params();

    	$id = empty($args[0]) ? 0 : intval($args[0]);
        if (!$id)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }

        /* 可缓存数据 */
        $data = $this->_get_common_info($id);
        if ($data === false)
        {
            return;
        }
        else
        {
            $this->_assign_common_info($data);
        }

        //获取属性
         $part_mod = & m("part");
         $part_info = $part_mod->get("goods_id=".$id);
         $this->assign("goods_attr_list",$this->build_attr_html($part_info['type_id'],$part_info['part_id']));

        /* 更新浏览次数 */
        $this->_update_views($id);

        //是否开启验证码
        if (Conf::get('captcha_status.goodsqa'))
        {
            $this->assign('captcha', 1);
        }
        /*转换组件和基本款的cate_id*/
		//$mCates = array('8001'=>'西服', '8050'=>'大衣', '8030'=>'衬衣');//modify by Ruesin
        $cst_cate = 0;
		$v = $data['goods']['cate_id'];
	 	if ($v == '8001')
	 	{
	 		$cst_cate = '3';
	 	}
	 	elseif ($v == '8050')
	 	{
	 		$cst_cate = '6000';
	 	}
	 	elseif ($v == '8030')
	 	{
	 		$cst_cate = '3000';
	 	}

        $cus = &m("customs"); //获得关联基本款
        if($part_info['link_cst']){
            $customs_list  = $cus->find(array(
            'conditions' => "cst_id in (".$part_info['link_cst'].")",
            'limit' => "0,8",
            'field' => "cst_name, cst_price, cst_dis_image, cst_id",
            ));

        }else{
            //TODO 没有数据之前，临时先用推荐来代替
            $customs_list  = $cus->find(array(
            'conditions' => "cst_store > 0 AND is_active=1 AND is_rec=1 AND cst_cate = ".$cst_cate,
            'limit' => "0,8",
            'field' => "cst_name, cst_price, cst_dis_image, cst_id",
            ));
        }
        $this->assign('customs_list', $customs_list);

		//$part_list = $this->_get_part_list($id);
		//$this->assign('customs_list', $part_list['customs']);
		$this->assign('fy_info',1);

		$this->assign('part_info', $part_info);

		//获取店铺分类连接
        //$store_gcates = $this->_get_store_gcategory($id);

        $this->assign('guest_comment_enable', Conf::get('guest_comment'));
        $this->display('goods.index.html');
    }

    /**
     * 根据属性数组创建属性的表单
     *
     * @access  public
     * @param   int     $cat_id     分类编号
     * @param   int     $part_id    商品编号
     * @return  string
     */
    function build_attr_html($cat_id, $part_id)
    {
        if ($part_id > 0)
        {
            $zujian_attr_mod = & m("partattr");
            $attr = $zujian_attr_mod->find(array(
                    'fields'    =>  'g.*,v.attr_value',
                    'conditions'=>" v.part_id=$part_id",
                    'join'  =>  'belongs_to_partattr',
                    'order'=> 'g.sort_order',
            ));
            return $attr;
        }
        else
        {
            return NULL;
        }
    }

    /* 商品评论 */
    function comments()
    {
        $args = $this->get_params();
        $id = empty($args[0]) ? 0 : intval($args[0]);
        //$id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!$id)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }

        $data = $this->_get_common_info($id);
        if ($data === false)
        {
            return;
        }
        else
        {
            $this->_assign_common_info($data);
        }

        /* 赋值商品评论 */
        $data = $this->_get_goods_comment($id, 10);
        $this->_assign_goods_comment($data);

        $this->display('goods.comments.html');
    }

    /* 销售记录 */
    function saleslog()
    {
        $args = $this->get_params();
        $id = empty($args[0]) ? 0 : intval($args[0]);
        //$id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        if (!$id)
        {
            $this->show_warning('Hacking Attempt');
            return;
        }

        $data = $this->_get_common_info($id);
        if ($data === false)
        {
            return;
        }
        else
        {
            $this->_assign_common_info($data);
        }

        /* 赋值销售记录 */
        $data = $this->_get_sales_log($id, 10);
        $this->_assign_sales_log($data);

        $this->display('goods.saleslog.html');
    }
    function qa()
    {
        $goods_qa =& m('goodsqa');
         // $id = intval($_GET['id']);
        $args = $this->get_params();
        $id = empty($args[0]) ? 0 : intval($args[0]);
         if (!$id)
         {
            $this->show_warning('Hacking Attempt');
            return;
         }
        if(!IS_POST)
        {
            $data = $this->_get_common_info($id);
            if ($data === false)
            {
                return;
            }
            else
            {
                $this->_assign_common_info($data);
            }
            $data = $this->_get_goods_qa($id, 10);
            $this->_assign_goods_qa($data);

            //是否开启验证码
            if (Conf::get('captcha_status.goodsqa'))
            {
                $this->assign('captcha', 1);
            }
            $this->assign('guest_comment_enable', Conf::get('guest_comment'));
            /*赋值产品咨询*/
            $this->display('goods.qa.html');
        }
        else
        {
            /* 不允许游客评论 */
            if (!Conf::get('guest_comment') && !$this->visitor->has_login)
            {
                $this->show_warning('guest_comment_disabled');

                return;
            }
            $content = (isset($_POST['content'])) ? trim($_POST['content']) : '';
            //$type = (isset($_POST['type'])) ? trim($_POST['type']) : '';
            $email = (isset($_POST['email'])) ? trim($_POST['email']) : '';
            $hide_name = (isset($_POST['hide_name'])) ? trim($_POST['hide_name']) : '';
            if (empty($content))
            {
                $this->show_warning('content_not_null');
                return;
            }
            //对验证码和邮件进行判断

            if (Conf::get('captcha_status.goodsqa'))
            {
                if (base64_decode($_SESSION['captcha']) != strtolower($_POST['captcha']))
                {
                    $this->show_warning('captcha_failed');
                    return;
                }
            }
            if (!empty($email) && !is_email($email))
            {
                $this->show_warning('email_not_correct');
                return;
            }
            $user_id = empty($hide_name) ? $_SESSION['user_info']['user_id'] : 0;
            $conditions = 'g.goods_id ='.$id;
            $goods_mod = & m('goods');
            $ids = $goods_mod->get(array(
                'fields' => 'store_id,goods_name',
                'conditions' => $conditions
            ));
            extract($ids);
            $data = array(
                'question_content' => $content,
                'type' => 'goods',
                'item_id' => $id,
                'item_name' => addslashes($goods_name),
                'store_id' => $store_id,
                'email' => $email,
                'user_id' => $user_id,
                'time_post' => gmtime(),
            );
            if ($goods_qa->add($data))
            {
                header("Location: index.php?app=goods&act=qa&id={$id}#module\n");
                exit;
            }
            else
            {
                $this->show_warning('post_fail');
                exit;
            }
        }
    }

    /**
     * 取得公共信息
     *
     * @param   int     $id
     * @return  false   失败
     *          array   成功
     */
    function _get_common_info($id)
    {
        $cache_server =& cache_server();
        $key = 'page_of_goods_' . $id;
        $data = $cache_server->get($key);
        $cached = true;
        if (1)
        {
            $cached = false;
            $data = array('id' => $id);

            /* 商品信息 */
            $goods = $this->_goods_mod->get_info($id);

            $param = array(
            	'store_id' => $goods["store_id"],
            	'recommended' => 1,
            	'limit' => "20",
            );

            $data["intro_list"] = $this->_goods_mod->get_list($param);


            $data["top10"] = $this->_get_ifList_goods($goods['store_id']);

            //if (!$goods || $goods['if_show'] == 0 || $goods['closed'] == 1 || $goods['state'] != 1)
            //if(!$goods || $goods['state'] != 1)
            if(!$goods) //modify By Ruesin
            {
                $this->show_warning('goods_not_exist');
                return false;
            }
            $goods['tags'] = $goods['tags'] ? explode(',', trim($goods['tags'], ',')) : array();

            $data['goods'] = $goods;

            /* 店铺信息 */
            if (!$goods['store_id'])
            {
                $this->show_warning('store of goods is empty');
                return false;
            }
            $this->set_store($goods['store_id']);
            $data['store_data'] = $this->get_store_data();
            //获取品牌信息。放在店铺信息里
            $data['store_data']['brand_info'] = $this->_get_brand_info($data['store_data']['brand_id']);

            //商品介绍需要转意下
            $data['goods']['description'] = htmlspecialchars_decode($data['goods']['description']);

            /* 当前位置 */
            $data['cur_local'] = $this->_get_curlocal($goods['cate_id']);
            $data['goods']['related_info'] = $this->_get_related_objects($data['goods']['tags']);
            /* 分享链接 */
            $data['share'] = $this->_get_share($goods);

            $cache_server->set($key, $data, 1800);
        }
        if ($cached)
        {
            $this->set_store($data['goods']['store_id']);
        }
        return $data;
    }
    //ns add 获取品牌信息
    function _get_brand_info($id){
        $brand = $this->_brand_mod->get_info($id);
        return $brand;
    }



    function _get_ifList_goods($id){
    	$goods_mod =& bm('goods', array('_store_id' => $id));
    	$goods_list = $goods_mod->find(array(
    			'conditions' => "closed = 0 AND if_list = 1",
    			'fields'     => 'goods_name, default_image, price, price_n',
    			'order'      => 'add_time desc',
    			'limit'      => 6,
    	));
    	foreach ($goods_list as $key => $goods)
    	{
    		empty($goods['default_image']) && $goods_list[$key]['default_image'] = Conf::get('default_goods_image');
    	}

    	return $goods_list;
    }


    function _get_related_objects($tags)
    {
        if (empty($tags))
        {
            return array();
        }
        $tag = $tags[array_rand($tags)];
        $ms =& ms();

        return $ms->tag_get($tag);
    }

    /* 赋值公共信息 */
    function _assign_common_info($data)
    {
        /* 商品信息 */
        $goods = $data['goods'];
        $this->assign('goods', $goods);
        $this->assign('sales_info', sprintf(LANG::get('sales'), $goods['sales'] ? $goods['sales'] : 0));
        $this->assign('comments', sprintf(LANG::get('comments'), $goods['comments'] ? $goods['comments'] : 0));

        $data['store_data']['store_gcates'] = $this->_get_store_gcategory($data['store_data']['store_id']);
        /* 店铺信息 */
        $this->assign('store', $data['store_data']);

        /* 推荐信息 */
        $this->assign('intro_list', $data['intro_list']);

        /* 销售排行 */
        $this->assign('top10_list', $data['top10']);

        /* 浏览历史 */
        $this->assign('goods_history', $this->_get_goods_history($data['id']));

        /* 默认图片 */
        $this->assign('default_image', Conf::get('default_goods_image'));

        /* 当前位置 */
        $this->_curlocal($data['cur_local']);

        /* 配置seo信息 */
        $this->_config_seo($this->_get_seo_info($data['goods']));

        /* 商品分享 */
        $this->assign('share', $data['share']);

        $this->import_resource(array(
            'script' => 'jquery.jqzoom.js',
            'style' => 'res:jqzoom.css'
        ));
    }

    /* 取得店铺分类 */
    function _get_store_gcategory($id)
    {
        $gcategory_mod =& bm('gcategory', array('_store_id' => $id));
        $gcategories = $gcategory_mod->get_list();
        import('tree.lib');
        $tree = new Tree();
        $tree->setTree($gcategories, 'cate_id', 'parent_id', 'cate_name');
        return $tree->getArrayList(0);
    }

    /* 取得浏览历史 */
    function _get_goods_history($id, $num = 9)
    {
        $goods_list = array();
        $goods_ids  = ecm_getcookie('goodsBrowseHistory');
        $goods_ids  = $goods_ids ? explode(',', $goods_ids) : array();
        if ($goods_ids)
        {
            $rows = $this->_goods_mod->find(array(
                'conditions' => $goods_ids,
                'fields'     => 'goods_name,default_image',
            ));
            foreach ($goods_ids as $goods_id)
            {
                if (isset($rows[$goods_id]))
                {
                    empty($rows[$goods_id]['default_image']) && $rows[$goods_id]['default_image'] = Conf::get('default_goods_image');
                    $goods_list[] = $rows[$goods_id];
                }
            }
        }

        $goods_ids[] = $id;
        if (count($goods_ids) > $num)
        {
            unset($goods_ids[0]);
        }
        ecm_setcookie('goodsBrowseHistory', join(',', array_unique($goods_ids)));

        return $goods_list;
    }

    /* 取得销售记录 */
    function _get_sales_log($goods_id, $num_per_page)
    {
        $data = array();

        $page = $this->_get_page($num_per_page);
        $order_goods_mod =& m('ordergoods');
        $sales_list = $order_goods_mod->find(array(
            'conditions' => "goods_id = '$goods_id' AND status = '" . ORDER_FINISHED . "'",
            'join'  => 'belongs_to_order',
            'fields'=> 'buyer_id, buyer_name, add_time, anonymous, goods_id, specification, price, quantity, evaluation',
            'count' => true,
            'order' => 'add_time desc',
            'limit' => $page['limit'],
        ));
        $data['sales_list'] = $sales_list;

        $page['item_count'] = $order_goods_mod->getCount();
        $this->_format_page($page);
        $data['page_info'] = $page;
        $data['more_sales'] = $page['item_count'] > $num_per_page;

        return $data;
    }

    /* 赋值销售记录 */
    function _assign_sales_log($data)
    {
        $this->assign('sales_list', $data['sales_list']);
        $this->assign('page_info',  $data['page_info']);
        $this->assign('more_sales', $data['more_sales']);
    }

    /* 取得商品评论 */
    function _get_goods_comment($goods_id, $num_per_page)
    {
        $data = array();

        $page = $this->_get_page($num_per_page);
        $order_goods_mod =& m('ordergoods');
        $comments = $order_goods_mod->find(array(
            'conditions' => "goods_id = '$goods_id' AND evaluation_status = '1'",
            'join'  => 'belongs_to_order',
            'fields'=> 'buyer_id, buyer_name, anonymous, evaluation_time, comment, evaluation',
            'count' => true,
            'order' => 'evaluation_time desc',
            'limit' => $page['limit'],
        ));
        $data['comments'] = $comments;

        $page['item_count'] = $order_goods_mod->getCount();
        $this->_format_page($page);
        $data['page_info'] = $page;
        $data['more_comments'] = $page['item_count'] > $num_per_page;

        return $data;
    }

    /* 赋值商品评论 */
    function _assign_goods_comment($data)
    {
        $this->assign('goods_comments', $data['comments']);
        $this->assign('page_info',      $data['page_info']);
        $this->assign('more_comments',  $data['more_comments']);
    }

    /* 取得商品咨询 */
    function _get_goods_qa($goods_id,$num_per_page)
    {
        $page = $this->_get_page($num_per_page);
        $goods_qa = & m('goodsqa');
        $qa_info = $goods_qa->find(array(
            'join' => 'belongs_to_user',
            'fields' => 'member.user_name,question_content,reply_content,time_post,time_reply',
            'conditions' => '1 = 1 AND item_id = '.$goods_id . " AND type = 'goods'",
            'limit' => $page['limit'],
            'order' =>'time_post desc',
            'count' => true
        ));
        $page['item_count'] = $goods_qa->getCount();
        $this->_format_page($page);

        //如果登录，则查出email
        if (!empty($_SESSION['user_info']))
        {
            $user_mod = & m('member');
            $user_info = $user_mod->get(array(
                'fields' => 'email',
                'conditions' => '1=1 AND user_id = '.$_SESSION['user_info']['user_id']
            ));
            extract($user_info);
        }

        return array(
            'email' => $email,
            'page_info' => $page,
            'qa_info' => $qa_info,
        );
    }

    /* 赋值商品咨询 */
    function _assign_goods_qa($data)
    {
        $this->assign('email',      $data['email']);
        $this->assign('page_info',  $data['page_info']);
        $this->assign('qa_info',    $data['qa_info']);
    }

    /* 更新浏览次数 */
    function _update_views($id)
    {
        $goodsstat_mod =& m('goodsstatistics');
        $goodsstat_mod->edit($id, "views = views + 1");
    }

    /**
     * 取得当前位置
     *
     * @param int $cate_id 分类id
     */
    function _get_curlocal($cate_id)
    {
        $parents = array();
        if ($cate_id)
        {
            $gcategory_mod =& bm('gcategory');
            $parents = $gcategory_mod->get_ancestor($cate_id, true);
        }

        $curlocal = array(
            array('text' => LANG::get('all_categories'), 'url' => url('app=category')),
        );
        foreach ($parents as $category)
        {
            $curlocal[] = array('text' => $category['cate_name'], 'url' => url('app=search&cate_id=' . $category['cate_id']));
        }
        $curlocal[] = array('text' => LANG::get('goods_detail'));

        return $curlocal;
    }

    function _get_share($goods)
    {
        $m_share = &af('share');
        $shares = $m_share->getAll();
        $shares = array_msort($shares, array('sort_order' => SORT_ASC));
        $goods_name = ecm_iconv(CHARSET, 'utf-8', $goods['goods_name']);
        $goods_url = urlencode(SITE_URL . '/' . str_replace('&amp;', '&', url('app=goods&id=' . $goods['goods_id'])));
        $site_title = ecm_iconv(CHARSET, 'utf-8', Conf::get('site_title'));
        $share_title = urlencode($goods_name . '-' . $site_title);
        foreach ($shares as $share_id => $share)
        {
            $shares[$share_id]['link'] = str_replace(
                array('{$link}', '{$title}'),
                array($goods_url, $share_title),
                $share['link']);
        }
        return $shares;
    }

    function _get_seo_info($data)
    {
        $seo_info = $keywords = array();
        $seo_info['title'] = $data['goods_name'] . ' - ' . Conf::get('site_title');
        $keywords = array(
            $data['brand'],
            $data['goods_name'],
            $data['cate_name']
        );
        $seo_info['keywords'] = implode(',', array_merge($keywords, $data['tags']));
        $seo_info['description'] = sub_str(strip_tags($data['description']), 10, true);
        return $seo_info;
    }
     /* 获取基本款关联*/
    function _get_part_list($goods_id = 0)
	{
        //获取商品信息
        $goods = $this->_get_common_info($goods_id);

		//查询part_id
        $part = $this->_part_mod->get(array(
                'fields' => 'part_id',
                'conditions' => "goods_id = ".$goods['goods']['goods_id'],
            ));

        if($part['part_id']){
            $conditions = "1=1 and part_id = ".$part['part_id'];
            $fabric=$this->_part_mod->get(array(
                    'conditions'    => $conditions,
                    'join'          => "belongs_to_brand",
                    'fields'        => "p.*,b.brand_name,b.brand_web",
            ));
            if (!empty($fabric['part_img']))
            {
                $fabric['part_img'] = $fabric['part_img'];
            }

            $customs_part_mod = & m("customsparts");

            $customs = $customs_part_mod->find(array(
                        'conditions' => "cst_pt.pt_id={$fabric['part_id']}",
                        'join'       => "belongs_to_customs",
                        'fields'     => "cst.cst_id,cst.cst_name,cst.cst_image,cst.cst_price",
            ));
                //格式化基本款缩略图
            foreach ($customs as $k=>$v) {
                if (!empty($v['cst_image'])) {
                    $customs[$k]['cst_image'] = $v['cst_image'];
                }
            }
            $data['customs'] = $customs;
            $data['fabric'] = $fabric;
            return $data;
        }else{
            return null;
        }

     }



}

?>
