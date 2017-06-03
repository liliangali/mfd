<?php
/**
 *  登陆 注册 修改会员资料等
 * @author  yusw
 * @version $Id: loginRegister.class.php 2015-05-30
 * @package app
 */
class Loginregister extends Result
{
    /**
     *  是否登陆
     * @param   array $data 消息数组信息
     * @return  string
     * @author  yusw
     * @URL     new.api.local.mfd.cn/soap/loginRegister.php?act=if_login&token=86c64670cf009e91d7fa42804060c212
     */
    function if_login($data){
        $token     = $data->token;
        if (!$token) {
            return $this->tresult();
        }

        $user_info = getToken($token);
        if (!$user_info) {
            $this->msg = '未登录';
            $this->errorCode='101';
            return $this->eresult();
        }else{
            $this->result = $user_info;
            return $this->sresult();
        }
    }


    function test($data){
    }

}

