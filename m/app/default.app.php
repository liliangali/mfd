<?php
use Cyteam\Module\Module;
use Cyteam\Config\Config;
class DefaultApp extends MallbaseApp
{
    function __construct(){
        parent::__construct();
        header("Content-Type:text/html;charset=" . CHARSET);
    }
    /**
    * @deprecated todo
    * @param pType pValueName pInfo
    * @return rType rValueName rInfo
    * @access public
    * @see functionName
    * @version 1.0.0 (2014-11-15)
    * @author Xiao5
    */
    function index()
    {
       $this->assign('index', 1); // 标识当前页面是首页，用于设置导航状态
       $this->_config_seo(array(
           'title' => Conf::get('site_title'),
       ));
       $this->assign('title','麦富迪定制');
       $user_info=$this->visitor->get();
//        echo '<pre>';
//        print_r($_SESSION);
//        die();
       $this->assign('page_description', Conf::get('site_description'));
       $this->assign('page_keywords', Conf::get('site_keywords'));
		$model_obj=new Module();
		$config_obj=new Config();
       //=====  轮播图  =====
       $settings=$config_obj->get_settings();
       $banners=$model_obj->getBanners($settings['wap_banners'], 3);
		$ads=$model_obj->getAds($settings['wap_ad_local']);
		$this->assign('banners',$banners);
		$this->assign('ads',$ads);
		$this->assign('token',$user_info['user_token']?$user_info['user_token']:0);
		$this->assign('app',APP);
       $this->display('index.html');
    }
 function down(){
 	$this->display('down.html');
}
    function _get_hot_keywords()
    {
        $keywords = explode(',', conf::get('hot_search'));
        return $keywords;
    }



    /**
    * @deprecated todo
    * @param pType pValueName pInfo
    * @return rType rValueName rInfo
    * @access public
    * @see functionName
    * @version 1.0.0 (2014-11-15)
    * @author Xiao5
    */
    function list_index()
    {
        $this->assign('index', 1); // 标识当前页面是首页，用于设置导航状态
        $this->assign('hot_keywords', $this->_get_hot_keywords());  /* 热门搜素 */
        $this->_config_seo(array(
            'title' => Conf::get('site_title'),
        ));
        $this->assign('page_description', Conf::get('site_description'));
        $this->assign('page_keywords', Conf::get('site_keywords'));


        $user_info = $this->visitor->get();
        $aData = $this->processPrice($user_info['user_id']);
        //=====  轮播图  =====

        $shuffling_mod = m('shuffling');
        $s_list = $shuffling_mod->find(array(
            'conditions' => "shuffling_group.name LIKE '%wap%' ",
            'join'      => "belongs_to_shufflinggroup",
        ));

        $pc_url = pc_url();
        if ($s_list)
        {
            foreach ($s_list as $key => &$value)
            {
                $value['img'] = $pc_url.$value['img'];
            }
        }

        //=====  套装列表  =====
        $suit_mod = m('suitlist');
        $suit_list_j = $suit_mod->find(array(
            'conditions' => "1=1 AND competitive = 1",
            'limit'      => '6',
        ));


        if ($suit_list_j)
        {

            foreach ($suit_list_j as $key => $val)
            {
                if($val['is_promotion']){
                    $val["price"] = $val["promotion_price"];
                }
                if(!empty($user_info)){
                    if(!in_array($val['category'], $aData) && $val["is_first"] == 1){
                        switch ($val['category']){
                            case "0003": //西装
                                $val["price"] = "999";
                                break;
                            case "0006": //男衬衣
                                $val["price"] = "199";
                                break;
                            case "0016":  //女衬衣
                                $val["price"] = "199";
                                break;
                        }
                    }
                }
            }
        }

        $suit_list_b = $suit_mod->find(array(
            'conditions' => "1=1 AND  promotion_now = 1",
            'limit'      => '6',
        ));

        $this->assign('j_list',$suit_list_j);
        $this->assign('b_list',$suit_list_b);
        $this->assign('pc_url',pc_url());
        $this->assign('s_list',$s_list);
        $this->display('list_index.html');
    }



	function processPrice($userid){
        if(!$userid) return array();
        $mod_first = m("orderfirstlog");
        $list = $mod_first->find(array(
            "conditions" => "user_id='{$userid}' AND is_active=1",
        ));

        $aData = array();

        foreach((array) $list as $key => $val){
            $aData[] = $val["cloth"];
        }

        return $aData;
    }
}

?>
