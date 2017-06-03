<?php

    /**
     * 裁缝主页相关绑定的二级域名
     * 
     * @author 高飞
     */
    
    class Router {
        
        // 需要判断二级域名的App
        private $routers = array(
            'member',
        );
        
        // 匹配裁缝自定义二级域名前缀
        private function match($tailorDomain)
        {
//            $domainName = 'admin';
//            $member =& m('member');
//            $domainName = $member->get(array(
//                'fields'    => 'user_id',
//                'conditions' => "user_name = '{$tailorDomain}' ",
//                'limit'     => "1",
//            ));
//            if($domainName['user_id']){
//                return $domainName['user_id'];
//            }
            return false;
        }

        // 参数 app名称(控制器名称)
        public function dispatch()
        {
            $httpHost = $_SERVER['HTTP_HOST'];
            if(empty($_SERVER['PATH_INFO']) || $_SERVER['PATH_INFO'] == '/'){
                $appName = 'default';
            }else{
                $pathInfo = explode("-", $_SERVER['PATH_INFO']);
                $appName = ltrim('/',$pathInfo[0]);
            }
            
            if($httpHost != DOMAIN_PLATFORM && in_array($appName, $this->routers))
            {
                $tailorDomain = str_replace('.'.DOMAIN_TAILOR, '', $httpHost);
                
                if($tailorDomain)
                {
                    // 数据库匹配
                    $userid = $this->match($tailorDomain);
                    if($userid)
                    {
                        if(empty($_SERVER['PATH_INFO']) || $_SERVER['PATH_INFO'] == '/')
                        {
                            // 默认页
                            $_SERVER['PATH_INFO'] = '/member-login.html';
                        }
                        // 修改域名
                        $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = DOMAIN_PLATFORM;
                    }
                }
                else
                {
                    header('Location: http://'.DOMAIN_PLATFORM.'/');
                }
            }
        }
        
    }
