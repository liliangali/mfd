<?php
class Sitecitys
{
	var $wdwl_url = '';
	var $error = '';
	var $token = '';

  /**
   * 构造函数
   * @param string $username
   *  可设置当前用户
   * @access protected
   * @return void
   */
  function __construct() {

  }

  /**
   * 设置参数
   */
  public function set($key, $value) {
    $this->$key = $value;
  }

  /**
   * 获取参数
   */
  public function get($key) {
    return isset($this->$key) ? $this->$key : NULL;
  }



	function getcity($lat,$lng)
	{
		$mcity='';


		//$mapjson= file_get_contents('http://api.map.baidu.com/geocoder/v2/?ak=D86faa317d5dd0367df3ea346f27ec86&location='.$lat.','.$lng.'&output=json&pois=0');
		//$mapjson=json_decode($mapjson);
		if($mapjson->status==0&&$mapjson->result->addressComponent->city)
		{
			$mcity=$mapjson->result->addressComponent->city;
			if($mcity)
			{
				$mcity=str_replace('市', '', $mcity);

			}
		}
		return $mcity;
	}

	function getcitybyname($mcity,$pageSize,$pageIndex,$lat=0,$lng=0)
	{

		$mod=m('testsite');
		if($mcity){

			if($lat&&$lng)
			{
			$res=$mod->find(array(

					'conditions'=>"city_name like '%$mcity%'",
					'limit' => ($pageSize * ($pageIndex-1)) . ','. $pageSize,
			        //'order' => "serve.idserve desc",
					'order'=>'ACOS(SIN(('.$lat.' * 3.1415) / 180 ) *SIN((ifnull(lat,\'0\') * 3.1415) / 180 ) +COS(('.$lat.' * 3.1415) / 180 ) * COS((ifnull(lat,\'0\') * 3.1415) / 180 ) *COS(('.$lng.'* 3.1415) / 180 - (ifnull(lng,\'0\') * 3.1415) / 180 ) ) * 6380 asc',
			        'count' => true,
					'index_key'=>false,
				));
				//var_dump($res);exit;
			}
			else
			{
				$res=$mod->find(array(

					'conditions'=>"city_name like '%$mcity%'",
					'limit' => ($pageSize * ($pageIndex-1)) . ','. $pageSize,
			        'order' => "city_id desc",
					//'order'=>'ACOS(SIN(('.$lat.' * 3.1415) / 180 ) *SIN((ifnull(lat,\'0\') * 3.1415) / 180 ) +COS(('.$lat.' * 3.1415) / 180 ) * COS((ifnull(lat,\'0\') * 3.1415) / 180 ) *COS(('.$lng.'* 3.1415) / 180 - (ifnull(lng,\'0\') * 3.1415) / 180 ) ) * 6380 asc',
			        'count' => true,
					'index_key'=>false,
				));

			}
		}
		else
		{
			$res=$mod->find(array(

				'limit' => ($pageSize * ($pageIndex-1)) . ','. $pageSize,
		        'order' => "city_id desc",
				//'order'=>'ACOS(SIN((39.983424 * 3.1415) / 180 ) *SIN((lat * 3.1415) / 180 ) +COS((39.983424 * 3.1415) / 180 ) * COS((lat * 3.1415) / 180 ) *COS((116.322987* 3.1415) / 180 - (lng * 3.1415) / 180 ) ) * 6380 asc',
		        'count' => true,
				'index_key'=>false,
			));
		}
	return $res;
	}

	function sitename($pageSize,$pageIndex,$mcity)
	{
		////http://local.mfd.com/soaapi/v1/soap/sitecitys.php?act=sitename&pageSize=10&pageIndex=1&city=%E5%8C%97%E4%BA%AC
		global $json;
		$res=$this->getcitybyname($mcity,$pageSize,$pageIndex);

		$arr_tmp['statusCode']=0;
		//
			if($mcity)
			{
				$arr_tmp['gps']='1';
			}else
			{
				$arr_tmp['gps']='0';
			}

			if($mcity)
			{
				$arr_tmp['gps_city']=$mcity;
			}else
			{
				$arr_tmp['gps_city']='';
			}

		$arr_tmp['datalist']	=$res;

		return $json->encode($arr_tmp);
	}

	function site($pageSize,$pageIndex,$lat,$lng){

		//http://local.mfd.com/soaapi/v1/soap/sitecitys.php?act=site&pageSize=10&pageIndex=1&lat=36.247964&lng=120.401204
		global $json;
		if($pageIndex<1)
		{
			$pageIndex = 1;
		}




			$mcity=$this->getcity($lat, $lng);




		//var_dump($conditions_str);exit;
		//exit;

		$res=$this->getcitybyname($mcity,$pageSize,$pageIndex,$lat,$lng);

		//var_dump($res);exit;

		$arr_tmp['statusCode']=0;
		//
			if($mcity)
			{
				$arr_tmp['gps']='1';
			}else
			{
				$arr_tmp['gps']='0';
			}

			if($mcity)
			{
				$arr_tmp['gps_city']=$mcity;
			}else
			{
				$arr_tmp['gps_city']='';
			}

		$arr_tmp['datalist']	=$res;

		return $json->encode($arr_tmp);
	}

	/**
     * 面料查询列表
	 *
     * @param int $pageSize  每页显示的个数；int $pageIndex 第几页；string $goods_name 料号（传空值为查询所有面料）;int $filter 排序方式;Int $type 分类； int $brand 品牌 int 地区 $city_id
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	function part($pageSize, $pageIndex, $goods_name)
	{
		global $json;
		$return = array();
		$conditionsstr = $goods_name?" part_name like '%".$goods_name."%' ":" 1=1 ";
		$mod = m('part');

		$res = $mod->find(array(
            'conditions' => $conditionsstr .' AND fabric_id !=0 AND state=1 AND is_on_sale=1',
			'limit'      => ($pageSize * ($pageIndex-1)) . ','. $pageSize,
			'order'      => "part_id desc",
			'count'      => true,
			'index_key'  => false,
		));

		//获得一个总数
		$count  = $mod->get(array(
			'fields'	 => "count('part_id') as count",
			'conditions' => $conditionsstr.' AND fabric_id !=0 AND state=1 AND is_on_sale=1',
		));

		$return = array(
			'statusCode' => 1,
			'result'     => array(
				'datalist'  => !empty($res) ? $res : '',
				'count'     => !empty($count['count']) ? $count['count'] : 0,
			),
		);
		return $json->encode($return);
	}

	/**
     *  获得面料搜索列表条件属性
	 *
     * @param NULL
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	function getPartAtrr()
	{
		global $json;
		$return = array();
		$partattr_mod = & m("partattribute");

        $attrs  = $partattr_mod->find(array(
            'conditions' => "type_id = 1",
            'order'      => "attr_id ASC",
        ));
		foreach($attrs as $key => $val) {
            $attrs[$key]['values'] = explode("\r\n", $val['attr_values']);
        }

		$attr_list    = array();
		foreach($attrs as $key => $val) {
		    $attr_list[$key]['name']   = $val['attr_name'];

		    foreach($val['values'] as $vk => $vv){
		        $attr_list[$key]['children'][$vk]['name'] = $vv;
		        $attr_list[$key]['children'][$vk]['attr'] = "{$key}:{$vv}{$ast}";

		    }
		}

		$return = array(
			'statusCode' => 1,
			'result' => array(
				'success'   => '获取成功',
				'attr_list' => !empty($attr_list) ? $attr_list : '',
			)
		);

		return $json->encode($return);
	}

	/**
     *  搜索获得面料列表
	 *
     * @param int $pageSize  每页显示的个数；int $pageIndex 第几页；int $order 排序方式;Int $type 分类； int $brand 品牌 int 地区 $city_id
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	function getByfilterPart($pageSize, $pageIndex, $order, $attr)
	{
		global $json;
		$conditions = ' fabric_id !=0 AND state=1 AND is_on_sale=1';

		$order = "";
		if ($filter == 1)
		{
			$order = "click_count desc";//人气
		}
		elseif ($filter == 2)
		{
			$order = "order_count desc";// 销量
		}
		elseif ($filter == 3)
		{
			$order = "part_id desc"; //最新
		}

		$attr_list = array();
		$tmpAttr   = explode(".", $attr);
		$tmpAttrArray = array();
		$searchAttr   = array();
		foreach($tmpAttr as $key => $val){
		    $tmpV = explode(":", $val);
		    if(isset($tmpV[1])){
		        $searchAttr[$tmpV[0]]   = $tmpV[1];
		        $tmpAttrArray[$tmpV[0]] = $tmpV[1];
		    }
		}

		$pidsArray = array();

		$partAttrModel = &m("partattr");
		$conditions    = ' fabric_id !=0 AND state=1 AND is_on_sale=1';
		if(!empty($searchAttr)) {
		    $attrCond=' 0 ';
		    $cNum = 0;
		    foreach($searchAttr as $key => $val) {
		        $cNum +=1;
		        $attrCond .= " OR (attr_id='$key' AND attr_value = '$val')";
		    }

		    $attrCond .= " GROUP BY part_id HAVING num = '$cNum'";

		    $goodsids = $partAttrModel->find(array(
		        'conditions' => $attrCond,
		        'fields'     => "part_id, count(1) AS num",
		    ));
		    foreach($goodsids as $key => $val)
		    {
		        $pidsArray[] = $val['part_id'];
		    }

		    if(!empty($pidsArray)) {
		        $conditions .= " AND part_id ".db_create_in($pidsArray);
		    } else {
		        $conditions .= " AND part_id IN (0)";
		    }

		}

		$mod = m('part');
		$res = $mod->find(array(
            'conditions' => $conditions,
			'limit'      => ($pageSize * ($pageIndex-1)) . ','. $pageSize,
			'order'      => $order,
			'fields'     => "part_id, part_name, goods_sn, click_count, part_small, price, part_number",
			'count'      => true,
			'index_key'  => false,
		));

		//获得总数
		$count = $mod->getCount();

		//组装返回数据
		$return = array(
			'statusCode' => 1,
			'result' => array(
				'datalist' => !empty($res) ? $res : '',
				'count'    => $count,
			),
		);

		return $json->encode($return);
	}

	/**
     * 获得面料详情
	 *
     * @param string $part_id 料号
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	function getPartInfo($part_id)
	{
		global $json;
		$mod = m('part');
		$datadetial = $mod->get(array(
            'conditions' => "part_id = '$part_id' AND fabric_id !=0 AND state=1",
			'fields'     => 'price, maket_price, link_cst, part_number, click_count, zujian_brief, part_img ',
			'count'      => true,
		));

		//获得对应基本款样衣
		$links = array();
		if($datadetial["link_cst"]){
		    $custom = m("customs");
		    $links = $custom->find(array(
		        'conditions' => "cst_id IN ({$datadetial["link_cst"]})",
				'fields'     => 'cst_image, cst_id ',
				'index_key'  => false,
		    ));
		}

		//获得属性
		$partAttrModel = m("partattr");
		$attrs = $partAttrModel->find(array(
		    'conditions' => "part_id = '$part_id'",
		    'join'       => 'belongs_to_partattr',
			'fields'     => 'attr_name, attr_value ',
			'index_key'  => false,
		));

		$return = array(
			'statusCode' => 1,
			'result'     => array(
				'datadetial' => $datadetial,
				'links'      => $links,
				'attrs'      => $attrs,
			),
		);

		return $json->encode($return);
	}

	function figurelist($pageSize,$pageIndex,$token)
	{//http://api.alicaifeng.com/soap/sitecitys.php?act=figurelist&pageIndex=1&pageSize=10&token=d29dee044c81c76158e8325b4cb17e1d
		//裁缝消费者的量体数据,量体列表，需要补充(这个裁缝对应的量体数据裁缝,id)
		global $json;


		if(!$token)
		{
			$arr_tmp['statusCode']=10000;
			$arr_tmp['msg'] = 'token error';
			return $json->encode($arr_tmp);
		}
		$userinfo=getUserInfo($token);
		if(!$userinfo)
		{
			$arr_tmp['statusCode']=10000;
			$arr_tmp['msg'] = 'token error';
			return $json->encode($arr_tmp);
		}
		$storeid=$userinfo['user_id'];


		//return $json->encode($userinfo);
		$arr_tmp['statusCode']=0;
		//$figure_mod=m("memberfigure");
		$figure_mod=m("customerfigure");



		$res=$figure_mod->find(array(
			'conditions'=>'storeid= '.$storeid,
			'limit' => ($pageSize * ($pageIndex-1)) . ','. $pageSize,
			'order' => "storeid desc",
			'count' => true,
			'index_key'=>false,
		));
		$arr_tmp['datalist']	=$res;

		return $json->encode($arr_tmp);
	}

	/**
     * 我的量体数据
	 *
     * @param int $pageSize  每页显示的个数；int $pageIndex 第几页；string $token 用户标识
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	function figurelistbyuser($pageSize, $pageIndex, $token)
	{
		//用户的量体数据
		global $json;
		$figurelist = '';
		$userinfo = getUserInfo($token);
		if(!$userinfo) {
			$return = array(
				'statusCode' => 0,
				'error' => array(
					'errorCode' => 100,
					'msg'       => 'token错误',
				),
			);
			return $json->encode($return);
		}

		$user_id = $userinfo['user_id'];
		/*
		$arr_tmp['statusCode'] = 0;
		$begin = $pageSize * ($pageIndex-1);
		$sql = "SELECT s.store_name, m.storeid, m.figure_sn ,m.storeid, m.lasttime, m.alias from ".DB_PREFIX."member_figure m LEFT JOIN ".DB_PREFIX."store s ON s.store_id=m.storeid WHERE m.userid=$user_id limit $begin, $pageSize";
		$figurelist = $db->getAll($sql);
		*/
		$mod_figure = m('memberfigure');

        /* 查找 */
        $figures = $mod_figure->find(array(
            'conditions' => "userid=" . $user_id,
            'fields'     => 'storeid, figure_sn, userid, alias, lasttime',
            'count'      => true,
            'limit'      => $pageSize * ($pageIndex-1) . ','. $pageSize,
            'order'      => 'lasttime DESC',
        ));

        //获取 裁缝的基础信息
        $sids = array();
        //$sids = array_column($figures, 'storeid');

		foreach($figures as $val ) {
			$sids[] = $val['storeid'];
		}
        $store_mod  = & m('store');
        $stores = $store_mod->findAll(array(
            'conditions' => db_create_in($sids, 'store_id'),
            'fields'     => 'popularity, published, fans, owner_name, store_name, store_logo, sgrade', // 人气,作品,粉丝,昵称,logo图,等级
			'index_key'	 => '',
        ));

		/*
		// 获取 裁缝等级的logo 数组关联，不链表
        $member_lv_mod = & m('memberlv');
        $sgradeids = i_array_column($stores, 'sgrade');
        $lvs = $member_lv_mod->findAll(array(
            'conditions' => db_create_in($sgradeids, 'member_lv_id'),
            'fields' => 'name,lv_logo', // 等级名称 ，等级logo
            'count' => true,
            'limit' => $page['limit'],
        ));*/
        if ($figures) {
            foreach ($figures as &$row) {
                $row['popularity'] = $stores[$row['storeid']]['popularity'];
                $row['published']  = $stores[$row['storeid']]['published'];
                $row['fans']       = $stores[$row['storeid']]['fans'];
                $row['owner_name'] = $stores[$row['storeid']]['owner_name'];
				$row['owner_name'] = $stores[$row['storeid']]['owner_name'];
                $row['store_id']   = $stores[$row['storeid']]['store_id'];
				$row['store_name'] = $stores[$row['storeid']]['store_name'];
                $row['store_logo'] = site_url() . $stores[$row['storeid']]['store_logo'];
                $row['sgrade']     = $stores[$row['storeid']]['sgrade'];
                $row['lv_logo']    = ''; // 默认等级logo
                $row['lv_name']    = '';
				/*
                if ($lvs[$stores[$row['storeid']]['sgrade']]) {
                    $row['lv_logo'] = site_url() . $lvs[$stores[$row['storeid']]['sgrade']]['lv_logo'];
                    $row['lv_name'] = $lvs[$stores[$row['storeid']]['sgrade']]['name'];
                }*/
            }
		}
		$count = $mod_figure->getCount();
		$return = array(
			'statusCode' => 1,
			'result'     => array(
				'figurelist' => !empty($figures) ? $figures : '',
				'success'    => '获取成功',
				'count'      => $count
			),
		);
		return $json->encode($return);

	}

	/**
     * 消费者的量体数据详情
	 *
     * @param string $token 用户标识；string $item_id  查看量体数据id
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
	public function getFigurebyuser($token, $item_id)
    {
    	global $json;
		$db = db();

		if(!$item_id) {
			$return = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 100,
					'msg'       => '量体数据不能为空',
				),
			);
			return $json->encode($return);
		}

    	$userInfo = getUserInfo($token);
    	if (!$userInfo) {
			$return = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 100,
					'msg'       => '找不该用户',
				),
			);
    		return $json->encode($return);
    	}
    	$user_id = $userInfo['user_id'];

		$sql = "SELECT s.store_name, m.* from ".DB_PREFIX."member_figure m LEFT JOIN ".DB_PREFIX."store s ON s.store_id=m.storeid WHERE m.userid=$user_id AND figure_sn=$item_id";
		$figure = $db->getRow($sql);

		$return = array(
			'statusCode' => 1,
			'result'     => array(
				'figure'  => !empty($figure) ? $figure : '',
				'success' => '获取成功',
			),
		);
		return $json->encode($return);

    }

	/**
     * 某裁缝获取某消费者的量体数据
	 *
     * @param String $token  裁缝对应用户标识；$figure_sn 量体数据id
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    public function getFigure($token, $figure_sn)
    {
    	global $json;
    	$userInfo = getUserInfo($token);

    	if (!$userInfo) {
			$return = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 100,
					'msg'       => '找不该用户',
				),
			);
    		return $json->encode($return);
    	}
    	$storeid = $userInfo['user_id'];

    	$figure = &m("customerfigure");
    	$figureInfo = $figure->find(array(
			'conditions' => "figure_sn='$figure_sn' and storeid='$storeid'",
			'index_key'  => false,
    	));

		$return = array(
			'statusCode' => 1,
			'result'     => array(
				'figure'  => !empty($figureInfo) ? $figureInfo : '',
				'success' => '获取成功',
			),
		);
		return $json->encode($return);

    }

	/**
     * 某裁缝修改某消费者的量体数据
	 *
     * @param Object $data 修改数据
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    function editFigure($data)
    {
    	global $json;
		$return = array();
   		if(!$data->token) {
			$return = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 100,
					'msg'       => '用户未登录',
				),
			);
			return $json->encode($return);
		}
    	$userInfo = getUserInfo($data->token);
    	if (!$userInfo)
    	{
			$return = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 100,
					'msg'       => '找不该用户',
				),
			);
    		return $json->encode($return);
    	}
    	if(!isset($data->figure_sn)||!$data->figure_sn)
    	{
			$return = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 100,
					'msg'       => '缺少参数userid',
				),
			);
    		return $json->encode($return);
    	}
    	$storeid = $userInfo['user_id'];


    	//$figure_mod=m('memberfigure');
    	$figure_mod = m('customerfigure');

    	$uparr = array();
    	if(isset($data->lw))$uparr['lw']=$data->lw;
    	if(isset($data->xw))$uparr['xw']=$data->xw;
    	if(isset($data->zyw))$uparr['zyw']=$data->zyw;
    	if(isset($data->tw))$uparr['tw']=$data->tw;
		if(isset($data->stw))$uparr['stw']=$data->stw;
		if(isset($data->zjk))$uparr['zjk']=$data->zjk;
		if(isset($data->zxc))$uparr['zxc']=$data->zxc;
		if(isset($data->yxc))$uparr['yxc']=$data->yxc;
		if(isset($data->qjk))$uparr['qjk']=$data->qjk;
		if(isset($data->hyjc))$uparr['hyjc']=$data->hyjc;
		if(isset($data->hyc))$uparr['hyc']=$data->hyc;
		if(isset($data->qyj))$uparr['qyj']=$data->qyj;
		if(isset($data->yw))$uparr['yw']=$data->yw;
		if(isset($data->tgw))$uparr['tgw']=$data->tgw;
		if(isset($data->td))$uparr['td']=$data->td;
		if(isset($data->hyg))$uparr['hyg']=$data->hyg;
		if(isset($data->qyg))$uparr['qyg']=$data->qyg;
		if(isset($data->zkc))$uparr['zkc']=$data->zkc;
		if(isset($data->ykc))$uparr['ykc']=$data->ykc;
		if(isset($data->xiw))$uparr['xiw']=$data->xiw;
		if(isset($data->jk))$uparr['jk']=$data->jk;
		if(isset($data->part_label_10726))$uparr['part_label_10726']=$data->part_label_10726;
		if(isset($data->part_label_10725))$uparr['part_label_10725']=$data->part_label_10725;
		if(isset($data->hd))$uparr['hd']=$data->hd;
		if(isset($data->kk))$uparr['kk']=$data->kk;
		if(isset($data->body_type_19))$uparr['body_type_19']=$data->body_type_19;
		if(isset($data->body_type_20))$uparr['body_type_20']=$data->body_type_20;
		if(isset($data->body_type_24))$uparr['body_type_24']=$data->body_type_24;
		if(isset($data->body_type_25))$uparr['body_type_25']=$data->body_type_25;
		if(isset($data->body_type_26))$uparr['body_type_26']=$data->body_type_26;
		if(isset($data->body_type_3))$uparr['body_type_3']=$data->body_type_3;
		if(isset($data->body_type_2000))$uparr['body_type_2000']=$data->body_type_2000;
		if(isset($data->body_type_3000))$uparr['body_type_3000']=$data->body_type_3000;
		if(isset($data->body_type_4000))$uparr['body_type_4000']=$data->body_type_4000;
		if(isset($data->body_type_6000))$uparr['body_type_6000']=$data->body_type_6000;
		if(isset($data->body_type_90000))$uparr['body_type_90000']=$data->body_type_90000;
		if(isset($data->styleLength))$uparr['styleLength']=$data->styleLength;
		if(isset($data->styleDY))$uparr['styleDY']=$data->styleDY;
		if(isset($data->height))$uparr['height']=$data->height;
		if(isset($data->weight))$uparr['weight']=$data->weight;
		if(isset($data->part_label_10130))$uparr['part_label_10130']=$data->part_label_10130;
		if(isset($data->part_label_10131))$uparr['part_label_10131']=$data->part_label_10131;
		$uparr['lasttime']=time();
		$figure_mod->edit(' figure_sn= '.$data->figure_sn.' and storeid='.$storeid,$uparr);

		$return = array(
			'statusCode' => 1,
			'result'     => array(
				'success' => '量体数据修改成功',
			),
		);
    	return $json->encode($return);

    }

	/**
     * 面料订购查询
	 *
     * @param int $pageSize  每页显示的个数；int $pageIndex 第几页；string $token 用户标识；string $fabrics_sn 料号（传空值为查询所有面料）
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    function fabrics($pageSize, $pageIndex, $token, $goods_sn)
    {
    	global $json;
		$return = array();

		$userinfo = getUserInfo($token);
		if(!$userinfo) {
			$return = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 100,
					'msg'       => 'token error',
				),
			);

			return $json->encode($return);
		}

		$user_id = $userinfo['user_id'];
		$fabrics_sn_str = empty($goods_sn)?'':' and goods_sn like \'%'.$goods_sn.'%\' ';

		$mod = m('store_fabric');
		$fabricslist = $mod->find(array(
            'conditions' => 'store_id='.$user_id.$fabrics_sn_str,
			'limit'     => ($pageSize * ($pageIndex-1)) . ','. $pageSize,
			'order'     => "fabric_id desc",
			'count'     => true,
			'index_key' => false,
		));
		$status = array(0 => '审核中', 1 => '已采购', 2 => '采购中', 3 => '审核失败', 4 => '已受理' );
		//处理申请状态
		//print_r($fabricslis);exit;
		if($fabricslist) {
			foreach($fabricslist as $key=>$val) {
				//处理状态名称
				$fabricslist[$key]['statusName'] = $status[$val['status']];
				//处理时间
				$fabricslist[$key]['date'] = strtotime($val['date']);
			}

		}

		//获得总数
		$count  = $mod->get(array(
			'fields'     =>'count(fabric_id) as count',
			'conditions' => 'store_id='.$user_id.$fabrics_sn_str,
		));

		$return = array(
			'statusCode' => 1,
			'result'     => array(
				'fabricslist' => !empty($fabricslist) ? $fabricslist : '',
				'count'       => $count['count'],
			),
		);
    	return $json->encode($return);
    }

	/**
     * 面料订购申请
	 *
     * @param string $goods_sn 料号；int $num 订购的米数；string $token 用户标识；string $date 交货日期；string $owner_name 联系人；$phone 电话；srting $description 备注
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    function fabricsadd($token, $goods_sn, $num, $phone, $owner_name, $description, $date)
    {
    	global $json;
		$return = array();

		$userinfo = getUserInfo($token);
		if(!$userinfo) {
			$return = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 100,
					'msg'       => '用户未登录',
				),
			);
			return $json->encode($return);
		}
		if(!($num>0)) {
			$return = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 100,
					'msg'       => '所需数量必须为大于0的数字',
				),
			);
			return $json->encode($return);
		}

		if(!$goods_sn){
			$return = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 100,
					'msg'       => '面料号不能为空',
				),
			);
			return $json->encode($return);
		}

		$part_mod = m('part');
		$part_oj = $part_mod->get("goods_sn='$goods_sn'");
		if(!$part_oj) {
			$return = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 101,
					'msg'       => '没有此面料编号',
				),
			);
			return $json->encode($return);
		}

    	$user_id = $userinfo['user_id'];

    	$mod = m('store_fabric');
    	$res = $mod->add(array(
			'goods_sn'    => $goods_sn,
			'num'         => $num,
			'store_id'    => $user_id,
			'phone'       => $phone,
			'owner_name'  => $owner_name,
			'description' => $description,
			'date'        => $date,
			'status'      => 0,
			'add_time'    => time(),
    	));
		if($res) {
			$return = array(
				'statusCode' => 1,
				'result'     => array(
					'success' => '面料订购成功',
				),
			);
		} else {
			$return = array(
				'statusCode' => 0,
				'error'      => array(
					'errorCode' => 103,
					'msg'       => '系统错误',
				),
			);
		}

    	return $json->encode($return);
    }

    /**
     * 量体派工申请
	 *
     * @param string $token 用户标识；int $user_id 用户id；int $type 量体方式(0上门1服务门店2历史数据);string $serve 服务门店; string $mob 联系方式；string $date 交货日期；string $address 交货地址;int region_id 地区id
	 *
     * @access protected
     * @author xuganglong <781110641@qq.com>
     * @return void
     */
    function applybody($token,$user_id,$type,$serve,$address,$region_id,$apply_date,$date)
    {
    	global $json;
		$return = array();
        $userinfo = getToken($token);
        $store_id  = $userinfo['user_id'];
        $store_mod = m('store');
        $store_info = $store_mod->get('store_id = '.$store_id);
        $store_name = $store_info['store_name'];
        if($type == 1){
            if(!$serve){
                $return = array(
                    'statusCode' => 0,
                    'error'      => array(
                        'errorCode' => 103,
                        'msg'       => '请选择服务门店',
                    ),
                );
                return $json->encode($return);
            }
        }
        $cus_mod = m('customer_figure');
        $userinfo = $cus_mod->get($user_id);
        $user_name = $userinfo['customer_name'];
        $user_mob  = $userinfo['customer_mobile'];
        $region_mod = m('region');
        $city = $region_mod->get('region_id = '.$region_id);
        $parent = $region_mod->get('region_id ='.$city['parent_id']);
        $country = $region_mod->get('region_id = '.$parent['parent_id']);
        $region_name = $country['region_name'].','.$parent['region_name'].','.$city['region_name'];
		$mod = m('store_dispatching');
        //删除已有的量体申请
        if($mod->get("owner_id='{$user_id}' AND store_id='{$store_id}'")){
            $mod->drop("owner_id='{$user_id}' AND store_id='{$store_id}'");
        }
		$arr = array(
			'store_id'   => $store_id,
            'store_name' => $store_name,
            'owner_name' => $user_name,
            'owner_id'   => $user_id,
            'tel'        => $user_mob,
            'address'    => $address,
            'apply_date' => $apply_date,
			'add_time'   => time(),
            'type'       => $type,
            'serve'      => $serve,
            'date'       => $date,
            'region_id'  => $region_id,
            'region_name'=> $region_name,
            'status'     => 0,
		);
        if($mod->add($arr)) {
            $cus_mod->edit('figure_sn ='.$user_id,array(
                'is_apply' => 1,
            ));
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success' => '申请提交成功',
                    'info'    => $arr,
                ),
            );
            return $json->encode($return);
        }else{
            $return = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 104,
                    'msg' =>'申请提交失败',
                ),
            );
            return $json->encode($return);
        }
    }
    /**
     * 得到服务点
     * @param string region_id 地区id
     * @access protected
     * @author liu_chao <280181131@qq.com>
     * @return void
     */
    public  function get_serve($region_id,$pageSize,$pageIndex){
        global $json;
        if(!$region_id){
            $return = array(
                'statusCode' => 0,
                'error'      => array(
                    'errorCode' => 100,
                    'msg'       => '请填写地区id',
                ),
            );
        }
        /*fields' => 'name,lv_logo', // 等级名称 ，等级logo
            'count' => true,
            'limit' => $page['limit'],*/
        $limit = $pageSize*($pageIndex-1).','.$pageSize;
        $serve_mod = m('serve');
        $serve_info = $serve_mod->findAll(array(
            'conditions' => 'serve.region_id = '.$region_id .' AND serve.state = 1 AND member.serve_type=2 AND member.figure_type=1',
            'join'       => 'has_member',
            'fields'     => '*',
            'count'      => true,
            'limit'      => $limit,
        ));
        if($serve_info){
            foreach($serve_info as $val){
                $serves[] = $val;
            }
        }
        $return['statusCode'] = 1;
        $return['result']['serves']     = $serves;
        return $json->encode($return);
    }
    /**
     * 历史量体数据
     * @param string token; int mob 消费者手机号
     * @access protected
     * @author liu_chao <280181131@qq.com>
     * @return void
     */
    public  function history_figure($token,$mob){
        global $json;
        $cus_mod =m('customer_figure');
        $figures = $cus_mod->findAll('customer_mobile = '.$mob);
        if(!$figures){
            $return = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 102,
                    'msg'       => '未找到量体数据',
                ),
            );
        }else{
            foreach($figures as $val){
                $figure_info[] = $val;
            }
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'info'    => $figure_info,
                ),
            );
        }
        return $json->encode($return);
    }
    /**
     * 添加顾客并预约量体
     *
     * @param string $token 用户标识；string $buyer_name 名称；int $type 量体方式(0上门1服务门店2历史数据);string $serve 服务门店; string $mob 联系方式；string $date 交货日期；string $address 交货地址;int region_id 地区id
     *
     * @access protected
     * @author liuchao <280181131@qq.com>
     * @return void
     */
    function add_cus($token, $name,$apply,$type,$serve, $mob, $address,$region_id,$date,$apply_date)
    {
        global $json;
        $userinfo = getToken($token);
        $store_id  = $userinfo['user_id'];
        $cus_mod  = m('customer_figure');
        $cus_arr = array(
            'storeid'        => $store_id,
            'customer_name'   => $name,
            'customer_mobile' => $mob,
            'lasttime'        => time(),
        );
        //添加预约
        if($apply == 1){
            if(!$address || !$region_id || !$date){
                $arr = array(
                    'statusCode' => 0,
                    'error'      => array(
                        'errorCode' => 104,
                        'msg'       => '请填写预约信息',
                    ),
                );
                return $json->encode($arr);
            }
            $store_mod = m('store');
            $store_info = $store_mod->get('store_id = '.$store_id);
            $store_name = $store_info['store_name'];
            if($type == 1){
                if(!$serve){
                    $return = array(
                        'statusCode' => 0,
                        'error'      => array(
                            'errorCode' => 103,
                            'msg'       => '请选择服务门店',
                        ),
                    );
                    return $json->encode($return);
                }
            }
            //添加顾客
            $cus_arr['is_apply'] = 1;
            $cus_info = $cus_mod->add($cus_arr);
            $cus_arr['figure_sn'] = $cus_info;
            //添加预约数据
            $region_mod = m('region');
            $city = $region_mod->get('region_id = '.$region_id);
            $parent = $region_mod->get('region_id ='.$city['parent_id']);
            $country = $region_mod->get('region_id = '.$parent['parent_id']);
            $region_name = $country['region_name'].','.$parent['region_name'].','.$city['region_name'];
            $mod = m('store_dispatching');
            $arr = array(
                'store_id'   => $store_id,
                'store_name' => $store_name,
                'owner_name' => $name,
                'owner_id'   => $cus_info,
                'tel'        => $mob,
                'address'    => $address,
                'add_time'   => time(),
                'type'       => $type,
                'serve'      => $serve,
                'apply_date' => $apply_date,
                'date'       => $date,
                'region_id'  => $region_id,
                'region_name'=> $region_name,
                'status'     => 0,
            );
            $add_apply = $mod->add($arr);

            if($add_apply && $cus_info) {
                $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'success' => '顾客添加成功，并已预约',
                        'cus_info'    => $cus_arr,
                        'apply_info'  => $arr,
                    ),
                );
                return $json->encode($return);
            }else{
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 105,
                        'msg' =>'申请提交失败',
                    ),
                );
                return $json->encode($return);
            }
        }else{
            //添加顾客
            $cus_info = $cus_mod->add($cus_arr);
            $cus_arr['figure_sn'] = $cus_info;
            if(!$cus_info){
                $return = array(
                    'statusCode' => 0,
                    'error' => array(
                        'errorCode' => 105,
                        'msg' =>'顾客添加失败',
                    ),
                );
            }else{
                $return = array(
                    'statusCode' => 1,
                    'result' => array(
                        'success' => '顾客添加成功',
                        'cus_info'    => $cus_arr,
                    ),
                );
            }
            return $json->encode($return);
        }
    }
    /**
     * 删除顾客
     *
     * @param string $token 用户标识；int $cus_id 顾客id
     *
     * @access protected
     * @author liuchao <280181131@qq.com>
     * @return void
     */
    function del_cus($token,$cus_id){
        global $json;
        $user_info = getToken($token);
        $store_id = $user_info['user_id'];
        $cus_mod = m('customer_figure');
        $cus_info = $cus_mod->get("figure_sn = '{$cus_id}' AND storeid = '{$store_id}'");
        if(!$cus_info){
            $return = array(
                'statusCode' => 0,
                'error' => array(
                    'errorCode' => 102,
                    'msg' =>'未找到改顾客',
                ),
            );
        }else{
            $del = $cus_mod->drop("figure_sn = '{$cus_id}' AND storeid = '{$store_id}'");
            if($del){
                //删除关联量体数据
                $figure_mod = m('figure');
                $figure_mod->drop("userid = '{$cus_id}'");
            }
            $return = array(
                'statusCode' => 1,
                'result' => array(
                    'success' => '顾客删除成功',
                ),
            );

        }
        return $json->encode($return);
    }



    function mgetUserInfo($token)
    {
    	if(!$token)
		{
			return false;
		}
		$userinfo=getUserInfo($token);
    	return $userinfo;
    }
}