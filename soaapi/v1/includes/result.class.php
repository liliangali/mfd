<?php
    /**
    *将返回结果集 放到一个类里面 方便处理
    *@author liang.li <1184820705@qq.com>
    *@2015年5月7日
    */
    class Result
    {
        var $statusCode;
        var $result;
        var $error;
        var $errorCode;
        var $msg;
        function __construct()
        {
            $this->statusCode = 1;
            $this->result = array();
            $this->errorCode = 1;
            $this->msg = '未知错误';
        }
        
        /**
        *成功的返回结果
        *@author liang.li <1184820705@qq.com>
        *@2015年5月7日
        */
        function sresult($success = '操作成功') 
        {
            $arr['statusCode'] = 1;
            $arr['result']['data'] = $this->result;
            $arr['result']['success'] = $success;
            return json_encode($arr);
        }
        
        
        /**
        *错误的返回结果
        *@author liang.li <1184820705@qq.com>
        *@2015年5月7日
        */
        function eresult($msg = '') 
        {
            if ($msg) 
            {
                $this->msg = $msg;
            }
            $arr['statusCode'] = 0;
            $arr['error']['errorCode'] = $this->errorCode;
            $arr['error']['msg'] = $this->msg;
            return json_encode($arr);
        }
        
        /**
        *token错误的返回结果
        *@author liang.li <1184820705@qq.com>
        *@2015年5月7日
        */
        function tresult() 
        {
            $arr['statusCode'] = 0;
            $arr['error']['errorCode'] = 100;
            $arr['error']['msg'] = '账号不存在';
            return json_encode($arr);
        }
        
        
        
    }