 <?php
 /**
  * soap 接口 作品功能 映射接口
  * --------------------------------------------------------
  * @author       小五
  * $Id: work.php 13267 2016-01-03 06:53:21Z nings $
  * $Date: 2016-01-03 14:53:21 +0800 (Sun, 03 Jan 2016) $
  * --------------------------------------------------------
  */

	ini_set("soap.wsdl_cache_enabled", "0");
	include_once './../config.php';

	$class = 'Work';
	$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
	$data = _g();

	if ($action == 'test')
	{
	}

    //===== 获取作品分类列表  =====
    elseif ($action == 'workClothList')
    {
    }

	//===== 获取作品列表  =====
	elseif ($action == 'workList')
	{
	}

    //===== 搜索顾客  =====
    elseif ($action == 'searchCus')
    {
    }

	//===== 获取作品详情  =====
	elseif ($action == 'workInfo')
	{
	}

    //===== 分享列表  =====
    elseif ($action == 'shareList')
    {
    }

    //===== 分享作品  =====
    elseif ($action == 'shareWork')
    {
    }
    //===== 分享记录  =====
    elseif ($action == 'shareRecord')
    {
    }

    //===== 继续设计  =====
    elseif ($action == 'continueDiy')
    {
    }


    //===== 删除作品  =====
    elseif ($action == 'delWork')
    {
    }


	//===== 作品评论  =====
	elseif ($action == 'workComment')
	{
	}


	//===== 评论列表  =====
	elseif ($action == 'commentList')
	{
	}

	//===== test  =====
	elseif ($action == 'test')
	{
	}
	//===== ns add 反推作品  =====
	elseif ($action == 'Counter')
	{

	}
	//===== 反推作品详细页-创业者  =====
	elseif ($action == 'Counter_workInfo')
	{
	}
	//===== 验证是否可以反推作品 - 消费者 ====
	elseif ($action == 'counterWork')
	{

	}
	//===== 添加反推作品 - 消费者 ====
	elseif ($action == 'add_counterWork')
	{

	}
	//===== 通过BD码绑定创业者 - 消费者 ====
	elseif($action == 'addinviter')
	{

	}
	else
	{
		echo "Lack of method ?action";
	}

	$arr  =  array(
	    'data' => $data,
	);
	$rs   = getSoapClients($class,$action,$arr);
	die($rs);


?>
