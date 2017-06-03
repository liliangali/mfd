<?php
	ini_set("soap.wsdl_cache_enabled", "0");
	include_once './../config.php';
	
	//设定允许进行操作的action数组
	$class = 'Fitting';
	$act_arr = array('index');
	$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
	
	//http://local.rc.com/soaapi/soap/user.php?act=modifyPassWord&oldPassWord=123456&newPassWord=admin123 示例
	
	//判断一下是否是允许进行的操作
	if (!in_array($action, $act_arr))
	{
	}
	
	//初始化数据
	if ($action == 'index')
	{
		$data = _g();
		$id = isset($data->id) ? $data->id : 18;
		$pid = isset($data->pid) ? $data->pid : 2;
		$arr = array(
				'id' => $id,
				'pid'=>$pid,
		);
		
	}
	
	//根据属性筛选商品
	elseif ($action == 'jsonData') 
	{
		
	
		$data = _g();
		$id = $data->id;//分类id
		$pid = $data->pid;//上级分类id
		$st = $data->st;
		$sval = $data->sval;
		$fdata = isset($data->fdata) ? $data->fdata : '';
 		//{"aclass":"xiku","cid":"19","gid":"143"}|{"cid":"20","gid":"33","aclass":"chenyi"}|{"cid":"18","gid":"6","aclass":"xifu"}   ----fdata数据
		$fdata = '{"alias":"xiku","cid":"19","gid":"143"}|{"cid":"20","gid":"33","alias":"chenyi"}|{"cid":"18","gid":"6","alias":"xifu"}';
		
		if (!$id || !$pid || !$st || !$sval)
		{
			$arr = array('statusCode'=>1,'msg'=>'参数不能为空');
			echo $json->encode($arr); 
			die;
		}
		
		if ($st == 'fabric')
		{
			if (!$fdata)
			{
				$arr = array('statusCode'=>1,'msg'=>'筛选面料 必须传fdata参数');
				echo $json->encode($arr);
				die;
			}
		}
		
		$arr = array(
			'id'	=> $id,
			'pid'	=> $pid,
			'st'	=> $st,
			'sval'	=> $sval,
			'fdata'	=> $fdata,
		);
	}
	
	elseif ($action == "n")
	{
		
		//{"aclass":"xiku","cid":"19","gid":"143"}|{"cid":"20","gid":"33","aclass":"chenyi"}|{"cid":"18","gid":"6","aclass":"xifu"}   ----fdata数据
		$fdata = '{"alias":"xiku","cid":"19","gid":"143"}|{"alias":"chenyi","cid":"20","gid":"33"}|{"alias":"xifu","cid":"18","gid":"6"}';
		$data = _g();
		//$fdata = $data->fdata;
		$cid		= isset($data->cid) ? $data->cid:0;
		
		$arr = array(
			'fdata'	=> $fdata,
			'cid'		=> $cid,	
		);
	}
	
	else
	{
		echo "Lack of method ?action";
	}
	$rs = getSoapClients($class, $action, $arr);
	die($rs);
	

?>