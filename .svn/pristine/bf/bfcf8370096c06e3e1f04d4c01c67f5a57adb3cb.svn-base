<?php

/**
 *    支付方式管理控制器
 *
 *    @author    yhao.bai
 *    @usage    none
 */
class PaymentApp extends BackendApp
{
    function index()
    {
        /* 读取已安装的支付方式 */
        $model_payment =& m('payment');
        $payments      = $model_payment->get_builtin();
        $white_list    = $model_payment->get_white_list();
        $installed     = $model_payment->get_installed();
        foreach ($payments as $key => $value)
        {
        	foreach ($installed as $installed_payment)
        	{
        		if ($installed_payment['payment_code'] == $key)
        		{
        			$payments[$key]['payment_desc']     =   $installed_payment['payment_desc'];
        			$payments[$key]['enabled']          =   $installed_payment['enabled'];
        			$payments[$key]['installed']        =   1;
        			$payments[$key]['ismobile']          =   $installed_payment['ismobile'];
        			$payments[$key]['payment_id']       =   $installed_payment['payment_id'];
        		}
        	}
        }
//    echo '<pre>';print_r($payments);exit;
        $this->assign('payments', $payments);
        $this->display('payment.index.html');
    }

    /**
     *    安装支付方式
     *
     *    @author    yhao.bai
     *    @return    void
     */
    function install()
    {
    	$code = isset($_GET['code']) ? trim($_GET['code']) : 0;
    	if (!$code)
    	{
    		echo Lang::get('no_such_payment');
    
    		return;
    	}
    	$model_payment =& m('payment');
    	$payment       = $model_payment->get_builtin_info($code);
    	if (!$payment)
    	{
    		echo Lang::get('no_such_payment');
    
    		return;
    	}
    
    	$payment_info = $model_payment->get("payment_code='{$code}'");
    	if (!empty($payment_info))
    	{
    		echo Lang::get('already_installed');
    
    		return;
    	}
    	if (!IS_POST)
    	{
    		/* 默认启用 */
    		$payment['enabled'] = 1;
    
    		$this->assign('yes_or_no', array(Lang::get('no'), Lang::get('yes')));
    		$this->assign('payment', $payment);
    		$this->display('payment.form.html');
    	}
    	else
    	{
    		$data = array(
    				'payment_name'  => $payment['name'],
    				'payment_code'  => $code,
    				'payment_desc'  => $_POST['payment_desc'],
    				'config'        => $_POST['config'],
    				'is_online'     => $payment['is_online'],
    				'enabled'       => $_POST['enabled'],
    				'ismobile'       => $_POST['ismobile'],
    				'sort_order'    => $_POST['sort_order'],
    		);
    		
    		if (!($payment_id = $model_payment->install($data)))
    		{
    			//$this->show_warning($model_payment->get_error());
    			$msg = $model_payment->get_error();
    			 $this->show_warning($msg);
    			return;
    		}
    		$this->show_message("操作成功", '返回支付列表','index.php?app=payment');
    	}
    }
    
    function config()
    {
    	$payment_id =   isset($_GET['payment_id']) ? intval($_GET['payment_id']) : 0;
    	if (!$payment_id)
    	{
    		echo Lang::get('no_such_payment');
    
    		return;
    	}
    	$model_payment =& m('payment');
    	$payment_info  = $model_payment->get("payment_id={$payment_id}");
    	if (!$payment_info)
    	{
    		echo Lang::get('no_such_payment');
    
    		return;
    	}
    	$payment = $model_payment->get_builtin_info($payment_info['payment_code']);
    	if (!$payment)
    	{
    		echo Lang::get('no_such_payment');
    
    		return;
    	}
    
    	if (!IS_POST)
    	{
    		$payment['payment_id']  =   $payment_info['payment_id'];
    		$payment['payment_desc']=   $payment_info['payment_desc'];
    		$payment['enabled']     =   $payment_info['enabled'];
    		$payment['ismobile']       = $payment_info['ismobile'];
    		$payment['sort_order']  =   $payment_info['sort_order'];
    		$this->assign('yes_or_no', array(Lang::get('no'), Lang::get('yes')));
    		$this->assign('config', unserialize($payment_info['config']));
    		$this->assign('payment', $payment);

    		$this->display('payment.form.html');
    	}
    	else
    	{
    		$data = array(
    				'payment_desc'  =>  $_POST['payment_desc'],
    				'config'        =>  $_POST['config'],
    				'enabled'       =>  $_POST['enabled'],
    				'ismobile'       => $_POST['ismobile'],
    				'sort_order'    =>  $_POST['sort_order'],
    		);
    		
    		$model_payment->edit("payment_id={$payment_id}", $data);
    		
    		if ($model_payment->has_error())
    		{
    			//$this->show_warning($model_payment->get_error());
    			$msg = $model_payment->get_error();
    			$this->show_warning($msg['msg']);
    			return;
    		}
    		
    		$this->show_message("编辑成功", '返回支付列表','index.php?app=payment');
    	}
    }
    
    function uninstall()
    {
    	$payment_id = isset($_GET['payment_id']) ? intval($_GET['payment_id']) : 0;
    	if (!$payment_id)
    	{
    		$this->show_warning('no_such_payment');
    
    		return;
    	}
    
    	$model_payment =& m('payment');
    	$model_payment->uninstall($payment_id);
    	if ($model_payment->has_error())
    	{
    		$this->show_warning($model_payment->get_error());
    
    		return;
    	}
    
    	$this->show_message("缷载成功", '返回支付列表','index.php?app=payment');
    }
    
    
    
    /**
     *    启用
     *
     *    @author    yhao.bai
     *    @return    void
     */
    function enable()
    {
        $code = isset($_GET['code'])    ? trim($_GET['code']) : 0;
        if (!$code)
        {
            $this->show_warning('no_such_payment');

            return;
        }
        $model_payment =& m('payment');
        if (!$model_payment->enable_builtin($code))
        {
            $this->show_warning($model_payment->get_error());

            return;
        }

        $this->show_message('enable_payment_successed');

    }

    /**
     *    禁用
     *
     *    @author    yhao.bai
     *    @return    void
     */
    function disable()
    {
        $code = isset($_GET['code'])    ? trim($_GET['code']) : 0;
        if (!$code)
        {
            $this->show_warning('no_such_payment');

            return;
        }
        $model_payment =& m('payment');
        if (!$model_payment->disable_builtin($code))
        {
            $this->show_warning($model_payment->get_error());

            return;
        }

        $this->show_message('disable_payment_successed');
    }
}

?>