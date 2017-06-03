<?php
/**
 *    主题设置控制器
 *
 *    @author    Garbin
 *    @usage    none
 */
class My_themeApp extends MemberbaseApp
{
    function __construct()
    {
        $this->My_themeApp();
    }
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

    function My_themeApp()
    {
        parent::__construct();
        $this->_store_id  = intval($this->visitor->get('manage_store'));
    }

    public function index()
    {
        $args = $this->get_params();
        $t = empty($args[0]) ? 'user' : $args[0];
        $this->assign('type', $t);
        $this->assign('ac', 'index');
        switch ($t)
        {
            //添加
            case 'set':
               $this->set();
                break;
            default:
                $this->df();
                break;
        }
    }
    function df()
    {
        //获取模板
        extract($this->_get_themes());
        if (empty($themes))
        {
            $this->show_warning('no_themes');

            return;
        }
        $this->assign('themes', $themes);
        $this->assign('curr_template_name', $curr_template_name);
        $this->assign('curr_style_name', $curr_style_name);
        $this->assign('manage_store', $this->visitor->get('manage_store'));
        $this->assign('id',$this->visitor->get('user_id'));
        $this->import_resource(array(
            'script' => array(
                array(
                    'path' => 'dialog/dialog.js',
                    'attr' => 'id="dialog_js"',
                ),
                array(
                    'path' => 'jquery.ui/jquery.ui.js',
                    'attr' => '',
                ),
                array(
                    'path' => 'jquery.ui/i18n/' . i18n_code() . '.js',
                    'attr' => '',
                ),
            ),
            'style' =>  'jquery.ui/themes/ui-lightness/jquery.ui.css',
        ));




        /* 头像 add ns */
        require(ROOT_PATH . '/includes/avatar.class.php');     //基础控制器类
        $objAvatar = new Avatar();
        //获取头像
        $user = $this->visitor->get();
        $avatar = $objAvatar->avatar_show($user['user_id'],'big');
        $this->assign('avatar', $avatar);
        $this->assign('type','tailor');
        $this->assign('user',$user);
        $this->assign('ac', ACT);
        /* 当前用户中心菜单 */
        $this->assign('_member_menu', $this->_get_member_menu('user'));
        $this->assign('_curitem', 'my_theme');
        $this->_config_seo('title', Lang::get('member_center') . ' - ' . Lang::get('my_theme'));
        $this->display('my_theme.index.html');

    }

    function _get_themes()
    {
        /* 获取当前所使用的风格 */
        $model_store =& m('store');
        $store_info  = $model_store->get($this->visitor->get('manage_store'));
        $theme = !empty($store_info['theme']) ? $store_info['theme'] : 'default|default';
        list($curr_template_name, $curr_style_name) = explode('|', $theme);

        /* 获取待选主题列表 */
        $model_grade =& m('sgrade');
        $grade_info  =  $model_grade->get($store_info['sgrade']);
        $skins = explode(',', $grade_info['skins']);
        
        $themes = array();
        foreach ($skins as $skin)
        {
            list($template_name, $style_name) = explode('|', $skin);
            $themes[$skin] = array('template_name' => $template_name, 'style_name' => $style_name);
        }

        return array(
            'curr_template_name' => $curr_template_name,
            'curr_style_name'    => $curr_style_name,
            'themes'             => $themes
        );
    }

    function set()
    {
        $template_name = isset($_GET['template_name']) ? trim($_GET['template_name']) : null;
        $style_name = isset($_GET['style_name']) ? trim($_GET['style_name']) : null;
        if (!$template_name)
        {
            $this->json_error('no_such_template');

            return;
        }
        if (!$style_name)
        {
            $this->json_error('no_such_style');

            return;
        }
        extract($this->_get_themes());
        $theme = $template_name . '|' . $style_name;

        /* 检查是否可以选择此主题 */
        if (!isset($themes[$theme]))
        {
            $this->json_error('no_such_theme');

            return;
        }
        $model_store =& m('store');
        $model_store->edit($this->visitor->get('manage_store'), array('theme' => $theme));

        $this->json_result('', 'set_theme_successed');
    }


}
?>