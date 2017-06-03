<?php
/**
 * soap 接口 作品功能
 * M 层 具体实现
 * --------------------------------------------------------
 * @author       小五
 * $Id: Work.class.php 13440 2016-01-08 06:56:30Z nings $
 * $Date: 2016-01-08 14:56:30 +0800 (Fri, 08 Jan 2016) $
 * --------------------------------------------------------
 */
	class Work
	{

	  var $result;
	  var $mod;
	  function __construct()
	  {
	      $this->result = new Result();
	      $this->mod    = m('works');
        $this->member_mod = m('member');
        $this->member_invite_mod = m('memberinvite');
          require(PROJECT_PATH . '/includes/avatar.class.php');     //基础控制器类
          $this->objAvatar = new Avatar();

	  }
        /**
         *获取作品列表分类
         */
        //0003西服0004西裤0005马甲0006衬衣0007大衣
        function workClothList(){
            $result = array(
                array(
                    'cloth'      => 0,
                    'cloth_name' => '全部'
                ),
                array(
                    'cloth'      => '0001',
                    'cloth_name' => '套装'
                ),
                array(
                    'cloth'      => '0003',
                    'cloth_name' => '西服'
                ),
                array(
                    'cloth'      => '0004',
                    'cloth_name' => '西裤'
                ),
                array(
                    'cloth'      => '0005',
                    'cloth_name' => '马甲'
                ),
                array(
                    'cloth'      => '0006',
                    'cloth_name' => '衬衣'
                ),
                array(
                    'cloth'      => '0007',
                    'cloth_name' => '大衣'
                ),
            );
            $this->result->result = $result;
            return $this->result->sresult();
        }

	  /**
	  *获取作品列表
	  */
	  function workList($data)
	  {
        //ns add 获取实时价格于面料上下架问题
        include(PROJECT_PATH . 'includes/libraries/diys.lib.php');
        $diys = new Diys();

        $token = isset($data->token) ? $data->token : '';
	      $pageSize = isset($data->pageSize) ? $data->pageSize : 10;
	      $pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
        $cloth = isset($data->cloth) ? $data->cloth : '';
        $user_info = getUserInfo($token);
          if (!$user_info)
          {
              return $this->result->tresult();
          }
          $user_id = $user_info['user_id'];
          $conditions = ' owner_id ='.$user_id .' AND iscover=1 ';
          if($cloth){
              $conditions .= ' AND cloth = '.$cloth;
          }
	      $limit = $pageSize*($pageIndex-1).','.$pageSize;
        $sql = "SELECT w.id,w.cloth,w.work_name,w.style,w.designer_id,w.diy_data,w.description,w.add_time,i.iscover,i.img_url FROM
                 cf_works w  JOIN cf_work_imgs i ON w.id=i.work_id WHERE {$conditions} AND source_from='app'  ORDER BY w.add_time DESC LIMIT {$limit}";
        $list = $this->mod->getALL($sql);

//          //========作品表数据==========
//	      $list = $this->mod->find(array(
//	          'conditions' => $conditions,
//	          'limit' => $limit,
//// 	          'join' => 'be_image',
//	          'order' => 'add_time desc',
//	          'index_key' => "",
//	      ));
          //=======获取价格、设计人姓名
          foreach($list as $key=>$val){
              $params = json_decode($val['diy_data'],true);
              //ns add 整合数据
              if($params){
              //获取价格于面料格式整合
                foreach($params['sysprocess'] as $k=>$v){
                  $params_array[$val['id']][$k] = $v['fabric'];
                }
              }

              $list[$key]['url'] = SHAREURL.$val['cloth'].'-'.$val['style'].'-mfd-'.$val['id'].'.html';
              $list[$key]['img_url'] = substr_replace($list[$key]['img_url'],"_178",strrpos($list[$key]['img_url'],'.',-1),0);

              $member_mod = m('member');
              if($val['designer_id'] != $user_id){
                  $members = $member_mod->get(array(
                      'conditions' => 'user_id ='.$val['designer_id'],
                      'fields'     => 'nickname'
                  ));

                  $list[$key]['designer_name'] = $members['nickname'];
              }

          }
        //获取价格-lgx提供接口
        if($params_array){
            $works_diys = $diys->_calcPrice($params_array);
            foreach ($list as &$value) {
                $value['price'] = (int)($works_diys[$value['id']]['price']);
            }
        }

	      $this->result->result = $list;
	      return $this->result->sresult();
	  }

        function lookFigure($data)
        {
            $token = isset($data->token) ? $data->token : '';
            $figure_id = isset($data->figure_id) ? $data->figure_id : 0;
            $mod = m('member');
            $user_info = getUserInfo($token);
            $cus_mod = m('customer_figure');
            $user_id = $user_info['user_id'];
            //////////////////////////////////////////
            $liangti_item = $cus_mod->get($figure_id);

            $liangti_item['lheight'] = $liangti_item['height'];
            $liangti_item['lweight'] = $liangti_item['weight'];
            unset($liangti_item['height']);
            unset($liangti_item['weight']);

            if($user_info['figure_type']==1){
                if (($liangti_item['id_serve'] != getIdserveByUser($user_id)))
                {
                    $this->result->errorCode = 101;
                    $this->result->msg = '无权查看此数据';
                    return $this->result->eresult();
                }
            }elseif($user_info['figure_type']==2){
                if (($liangti_item['id_serve'] != getIdserveByUser($user_id)) && ($liangti_item['liangti_id'] != $user_id))
                {
                    $this->result->errorCode = 101;
                    $this->result->msg = '无权查看此数据';
                    return $this->result->eresult();
                }
            }

            /*	    if (($liangti_item['figure_state'] < 1))
                    {
                        $this->result->errorCode = 101;
                        $this->result->msg = '此数据尚未录入 无法查看';
                        return $this->result->eresult();
                    }*/

            if($liangti_item['service_mode'] != 3){

                $this->result->errorCode = 101;
                $this->result->msg = '无权查看此数据';
                return $this->result->eresult();

            }
            //////////////////////////////////////////////
            $json = file_get_contents(PROJECT_PATH.'includes/data/config/size_json/figure.json');
            $json = json_decode($json,true);
            $arr = array_merge($json['public'],$json['special']);
            if ($arr)
            {
                $mod = m('mtmbodytype');
                foreach ($arr as $key => $value)
                {
//                    print_r($value);echo'<br/>';
                    if ($liangti_item[$value['value_name']])
                    {
                        $val = $liangti_item[$value['value_name']];
                        $item = $mod->get("id = '$val' ");
                        $liangti_item[$value['value_name']] = $item['name'];
                    }
                }
            }

            $this->result->result = $liangti_item;
            return $this->result->sresult();

        }

        /**
         * 内部方法
         * 作品分享，实际是crop一份作品.
         * SQL INSERT INTO SELECT 语句 通过 SQL，您可以从一个表复制信息到另一个表。
         * 数据量产生过大，考虑 定期删除信息
         * @return string
         * @author 小五
         */
       protected function _cropWork($wid){
       	$sql = "INSERT  INTO cf_shares
( `work_id`, `store_id`, `work_name`, `cus_id`, `is_suit`, `cloth`, `style`, `source_from`, `designer_id`, `owner_id`, `description`, `comment_num`, `from_id`, `share_num`, `like_num`, `views`, `add_time`, `voting`, `voting_status`, `start_time`, `end_time`, `fail_reason`, `deny_reason`, `status`, `is_sub`, `cate_id`, `type`, `is_diy`, `diy_data`, `decode_img`, `nscore_num`)
SELECT  `work_id`, `store_id`, `work_name`, `cus_id`, `is_suit`, `cloth`, `style`, `source_from`, `designer_id`, `owner_id`, `description`, `comment_num`, `from_id`, `share_num`, `like_num`, `views`, `add_time`, `voting`, `voting_status`, `start_time`, `end_time`, `fail_reason`, `deny_reason`, `status`, `is_sub`, `cate_id`, `type`, `is_diy`, `diy_data`, `decode_img`, `nscore_num`
FROM cf_works where id=$wid";
       	$this->mod->exec($sql);
       	$sid = $this->mod->insert_id();

       	$sql1 = "INSERT INTO cf_share_imgs
(`img_name`, `img_url`, `work_id`, `store_id`, `description`, `iscover`, `deny_reason`, `status`, `add_time`)
SELECT  `img_name`, `img_url`, `work_id`, `store_id`, `description`, `iscover`, `deny_reason`, `status`, `add_time`
FROM cf_work_imgs where work_id=$wid";
      $this->mod->exec($sql1);
      return $sid;
       }

	  /**
	  *获取作品详情
	  */
	  function workInfo($data)
	  {
        include(PROJECT_PATH . 'includes/libraries/diys.lib.php');
        $diys = new Diys();

	      $token = isset($data->token) ? $data->token : '';
	      $id = isset($data->id) ? $data->id : 0;
	      $user_info = getUserInfo($token);
	      if (!$user_info)
	      {
	          return $this->result->tresult();
	      }

        $user_id = $user_info['user_id'];

	      $conditions = 'w.id = '.$id.' AND owner_id = '.$user_id ;

        $sql = "SELECT w.id,w.cloth,w.work_name,w.owner_id,w.style,w.designer_id,w.description,w.diy_data,w.add_time,w.counter_description,w.is_counter,w.counter_add_time,i.iscover,i.img_url FROM
                 cf_works w  JOIN cf_work_imgs i ON w.id=i.work_id WHERE {$conditions} AND source_from='app' ORDER BY i.iscover DESC";

        $works = $this->mod->getALL($sql);
          if (!$works)
          {
              $this->result->msg = '此数据不存在';
              return $this->result->eresult();
          }
          do{
              $api_token = ApiAuthcode($user_info['user_id'], 'ENCODE', 'kuteiddiy', 0);
          } while (!preg_match("/[a-zA-Z\d]{40,42}$/u", $api_token));


          $result = array();
          foreach($works as $key=>$val){
              //==========获取设计者名称========
              $member_mod = m('member');
              $member = $member_mod->get($val['owner_id']);
							$result['id']            = $val['id'];
              $result['cloth']         = $val['cloth'];
              $result['work_name']     = $val['work_name'];
              $result['designer_id']   = $val['designer_id'];
              $result['designer_name'] = $member['nickname'];
              $result['description']   = $val['description'];
              $result['diy_data']      = $val['diy_data'];
              $result['add_time']      = $val['add_time'];
							$result['is_counter'] = $val['is_counter']; //ns add 是否推送过
							$result['counter_add_time'] = date('Y-m-d H:i:s',$val['counter_add_time']);//ns add 反推时间
							$result['counter_description'] = $val['counter_description'];
              $result['imgs'][]        = array_pop($val);
              //=========获取分享url
              $result['url']  = SHAREURL.$val['cloth'].'-'.$val['style'].'-mfd-'.$this->_cropWork($id).'-share.html';
              $result['curl'] = DIYURL.$val['cloth'].'-'.$val['style'].'-'.$api_token.'-'.$val['id'].'.html';
          }

          $diy_data = json_decode($result['diy_data'],true);
          // if($diy_data){
          //     $result['price'] = array_pop($diy_data);
          // }else{
          //     $result['price'] = 0;
          // }

          //遗漏问题
          array_pop($diy_data);

          //ns add 获取面料上下架与diy同步价格
          if($diy_data){
               foreach($diy_data['sysprocess'] as $k=>$v){
                   $params_array[$val['id']][$k] = $v['fabric'];
               }
          }

          if($params_array){
              $works_diys = $diys->_calcPrice($params_array);
              $result['price'] = (int)($works_diys[$id]['price']);
              $result['is_sale'] = $works_diys[$id]['is_sale'];
          }


          $rData =array();
          foreach($diy_data as $kk=>$vv){
              foreach ((array)$vv as $key=>$row){
                  //将数据格式化成与PC一致
                  foreach ($row['cstr'] as $pk=>$pv){
                      $vt = explode(':', $pv);
                      $row['prm'][$vt[0]] = $vt[1];
                  }
                  $rData[$key] = json_encode($row['prm'],JSON_UNESCAPED_UNICODE);
              }
          }
//          print_exit($rData);
          //===========获取工艺信息=========
          $dict_mod = m('dict');

          $params = array();

          foreach($rData as $key=>$value){
              $params[$key] = json_decode($value,true);
          }
//          print_exit($params);
          if ($params)
          {
              $tmp = array();
              foreach ((array)$params as $key1 => $value1)
              {
                  $i=0;
                  $value1 = array_filter($value1);//去除工艺中的空值
                  foreach((array)$value1 as $key2 => $value2){
                      $value1_val = explode("|", $value2);

                          $dict2_info = $dict_mod->get("id=".$key2);
                          $a[$key1][$i]['p_name'] =$dict2_info['name'];
                          $dict1_info = $dict_mod->get("id=".$value1_val[0]);
                          if (count($value1_val) > 1)
                          {
                              $a[$key1][$i]['s_name'] = $dict1_info['name'] .$value1_val[2];
                          }
                          else
                          {
                              $a[$key1][$i]['s_name'] = $dict1_info['name'];
                          }
                          $i++;

                  }
                  $result['params_cloth'][] = $key1;
                  $result['params_value'][] = $a[$key1];
              }
          }
          if(!$result['params_value']){
              $result['params_value'] = array();
          }
	      $this->result->result = $result;
	      return $this->result->sresult();
	  }
        /**
         * 分享列表
         */
        function shareList($data){
            $token     = isset($data->token) ? $data->token : '';
            $work_id   = isset($data->work_id) ? $data->work_id : '';
            $pageSize  = isset($data->pageSize) ? $data->pageSize : 10;
            $pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
						//ns add 搜索
						$keyword = isset($data->keyword) ? $data->keyword : '';
            $limit     = $pageSize*($pageIndex-1).','.$pageSize;
            $user_info = getUserInfo($token);
            if(!$user_info){
               return $this->result->tresult();
            }
            $user_id = $user_info['user_id'];
            if(!$work_id){
                $this->result->msg = '选择要分享的作品';
                return $this->result->eresult();
            }
            if($user_info['member_lv_id'] == 1){//消费者不显示我的邀请人
                $this->result->result = '';
            }else{//消费者显示我的邀请人
                $works = $this->mod->find(array(
                    'conditions' => 'from_id = '.$work_id.' AND designer_id ='.$user_id.' AND owner_id != '.$user_id,
                ));
                $ids = array();
                foreach($works as $kk=>$vv){
                    $ids[] = $vv['owner_id'];
                }

                //==========查询我的推荐人======
                $cus_mod = m('memberinvite');
                $mem_mod = m('member');
								if($keyword){
										$conditions = ' AND (m.nickname LIKE "%'.$keyword.'%" or m.user_name LIKE "%'.$keyword.'%")';
								}
                $sql = 'SELECT m.user_name as phone_mob,i.invitee,m.nickname FROM cf_member_invite i  JOIN cf_member m ON m.user_id=i.invitee WHERE inviter = '.$user_id.$conditions;
                $db = db();
                $cus_list = $db->getALL($sql);
//                $cus_list = $cus_mod->find(array(
//                    'conditions' => 'inviter = '.$user_id,
//                    'fields'     => 'invitee,nickname,phone_mob',
//                    'limit'      => $limit,
//                ));
                $cf_mod = m('customer_figure');
                foreach($cus_list as $key=>$val){
                    //邀请人如果在customer_figure表有数据的话，采用customer表的用户昵称，和顾客列表统一起来
                    // $customer = $cf_mod->get('userid='.$val['invitee']);
                    // if($customer){
                    //     $cus_list[$key]['nickname'] = $customer['customer_name'];
                    // }
                    //获得用户头像
                    $cus_list[$key]['avatar'] = $this->getAvatar($val['invitee']);

                }
//                print_exit($cus_list);
                $result = array();
                //======是否已分享0已分享1未分享
                foreach($cus_list as $key=>$val){
                    if(in_array($val['invitee'],$ids)){
                        $val['shared'] = 0;
                    }else{
                        $val['shared'] = 1;
                    }
                    $result[]=$val;
                }
                $this->result->result = $result;
            }
            return $this->result->sresult();
        }
        /**
         * 搜索顾客
         */
        function searchCus($data){
            $token      = isset($data->token) ? $data->token : '';
            $conditions = isset($data->conditions) ? $data->conditions : '';
            $work_id    = isset($data->work_id) ? $data->work_id : '';
            $user_info = getUserInfo($token);
            if(!$user_info){
                $this->result->tresult();
            }
            if ($user_info['member_lv_id'] == 1)
            {
                $this->errorCode = 101;
                $this->result->msg = '必须当前创业者才能分享给顾客';
                return $this->result->eresult();
            }
            $user_id = $user_info['user_id'];
            //====== 搜索条件(用户名或手机号)==========
            $condition = 'inviter = '.$user_id.' AND (nickname LIKE "%'.$conditions.'%" or phone_mob LIKE "%'.$conditions.'%")';
//            print_exit($condition);
            $cus_mod = m('memberinvite');
            $mem_mod = m('member');
            $cus_list = $cus_mod->find(array(
                'conditions' => $condition,
                'fields'     => 'invitee,nickname'
            ));

            foreach($cus_list as $key=>$val){
                $member = $mem_mod->get($val['invitee']);
                $cus_list[$key]['phone_mob'] = $member['user_name'];
                //获得用户头像
                $cus_list[$key]['avatar'] = $this->getAvatar($val['invitee']);
            }

            //======== 获取id判断是否已分享 ========
            $works = $this->mod->find(array(
                'conditions' => 'id ='.$work_id.' AND designer_id ='.$user_id,
            ));
            $ids = array();
            foreach($works as $kk=>$vv){
                $ids[] = $vv['owner_id'];
            }
            $result = array();
            foreach($cus_list as $key=>$val){
                if(in_array($val['invitee'],$ids)){
                    $val['shared'] = 0;
                }else{
                    $val['shared'] = 1;
                }
                $result[]=$val;
            }

            $this->result->result = $result;
            return $this->result->sresult();

        }
        /**
         * 获取头像
         */
        function getAvatar($id){
            /* 头像 add by xiao5 START */
            $member_mod = m('member');
            $member = $member_mod->get($id);

            //获得用户头像
            $avatar = $this->objAvatar->avatar_show($id, 'big');
            $return = $avatar.'?'.$member['avatar_time'];//加入头像时间，用于app及时更新头像
            return $return;
        }

    function getParams($diy_data){

        foreach ((array)$diy_data['params'] as $key=>$row){
            //将数据格式化成与PC一致
            foreach ($row['cstr'] as $pk=>$pv){
                $vt = explode(':', $pv);
                $row['prm'][$vt[0]] = $vt[1];
            }

            $rData = array(
                'params' => json_encode($row['prm'],JSON_UNESCAPED_UNICODE),
            );

            $aSave[] = array_merge($diy_data,$rData);
        }
        return $aSave;
    }

	 /**
	 * 评论作品
	 */
	 function workComment($data)
	 {
	     $token = isset($data->token) ? $data->token : '';
	     $id = isset($data->id) ? $data->id : 0;
	     $content = isset($data->content) ? $data->content : '';
	     $score = isset($data->score) ? $data->score : 0;
	     $user_info = getUserInfo($token);
	     if (!$user_info)
	     {
	         $this->result->tresult();
	     }
	     $user_id = $user_info['user_id'];

	     //=====  验证数据合法性  =====
	     if (!$this->mod->get($id))
	     {
	         $this->result->msg = '无此数据';
	         return $this->result->eresult();
	     }

	     $_data['member_id'] = $user_id;
	     $_data['nickname'] = $user_info['nickname'];
	     $_data['content'] = $content;
	     $_data['comment_id'] = $id;
	     //$_data['score'] = $score;
	     $_data['cate'] = 'wk';
	     $_data['come_from'] = "app";
	     $_data['addtime'] = time();
	     $mod = m('comcomments');
	     if ($mod->add($_data))
	     {
	         //$this->mod->setInc(array("id"=>$id),"nscore_num");
	         return $this->result->sresult();
	     }

	     $this->result->msg = '评论失败';
	     return $this->result->eresult();
	 }
        /**
         * 分享作品
         */
        function shareWork($data){
            $token   = isset($data->token) ? $data->token : '';
            $work_id = isset($data->work_id) ? $data->work_id : 0;
            $cus_id  = isset($data->cus_id) ? $data->cus_id : 0;
            $user_info = getUserInfo($token);
            $user_id = $user_info['user_id'];
            if(!$token){
                return $this->result->tresult();
            }
            if ($user_info['member_lv_id'] == 1)
            {
                $this->errorCode = 101;
                $this->msg = '必须当前创业者才能分享给顾客';
                return $this->result->eresult();
            }
            if(!$work_id){
                $this->result->msg ='请选择要分享的作品';
                return $this->result->eresult();
            }
            if(!$cus_id){
                $this->result->msg ='请选择要分享的顾客';
                return $this->result->eresult();
            }
            //=====  获取作品信息  =====
            $work_mod = m('works');
            $conditions = 'id = '.$work_id.' AND owner_id ='.$user_id;
//            print_exit($conditions);
            $work_info = $work_mod->get(array(
                'conditions' => $conditions,
            ));

            array_shift($work_info);//去除id
//            print_exit($work_info);
            //=====  分享作品  =====
                $shared = $this->mod->find(array(
                    'conditions' => 'from_id = '.$work_id.' AND owner_id ='.$cus_id,
                ));
                //如果作品已经分享给该顾客则不添加数据
                if(!$shared){
                    $work_info['owner_id'] = $cus_id;
                    $work_info['from_id']  = $work_id;
                    $updata['share_num'] = $work_info['share_num']+1;
                    $this->mod->edit($work_id,$updata);
                    $work_img_mod = m('workimgs');
                    $new_work_id = $work_mod->add($work_info);
                    $img = $work_img_mod->get('work_id='.$work_id);
                    $new_work_img['img_url'] = $img['img_url'];
                    $new_work_img['work_id'] = $new_work_id;
                    $new_work_img['description'] = $img['description'];
                    $new_work_img['iscover'] = $img['iscover'];
                    $new_work_img['add_time'] = $img['add_time'];
                    $img = $work_img_mod->add($new_work_img);
                    if(!img){
                        $this->result->msg ='图片分享失败s';
                        return $this->result->eresult();
                    }
                }
            return $this->result->sresult();
        }


        /**
         * 删除作品
         */
        function delWork($data){

            $token = isset($data->token) ? $data->token : '';
            $work_id    = isset($data->work_id) ? $data->work_id : 0;
            $user_info = getUserInfo($token);
            if(!$token){
                return $this->result->tresult();
            }

            if(!$work_id){
                $this->result->msg ='请选择要删除的作品';
                return $this->result->eresult();
            }

            $user_id = $user_info['user_id'];
            $work_mod = m('works');
            $conditions = 'id in ('.$work_id.') AND owner_id = '.$user_id;

//            print_exit($conditions);
            $result = $work_mod->drop($conditions);

            if($result){
                return $this->result->sresult();
            }else{
                $this->result->msg = '作品删除失败';
                return $this->result->eresult();
            }

        }

        /**
         * 作品分享记录
         */
        function shareRecord($data){
            $token = isset($data->token) ? $data->token : '';
            $id = isset($data->id) ? $data->id : 0;
            $pageSize  = isset($data->pageSize) ? $data->pageSize : 10;
            $pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
            $limit     = $pageSize*($pageIndex-1).','.$pageSize;
            $user_info = getUserInfo($token);
            if(!$token){
                return $this->result->tresult();
            }

            $user_id = $user_info['user_id'];
            if(!$id){
                $this->result->msg ='请选择要查看的作品';
                return $this->result->eresult();
            }
            if ($user_info['member_lv_id'] == 1)
            {
                $this->resutl->errorCode = 101;
                $this->result->msg = '必须创业者才能分享给顾客';
                return $this->result->eresult();
            }
            //==========获取已经分享过的人的id
            $works = $this->mod->find(array(
                'conditions' => 'from_id = '.$id,
                'limit'      => $limit,
            ));
            if(!$works){
                $result = array();
                $this->result->result = $result;
                return $this->result->sresult();
            }
            $ids = array();
						$result =array();
            foreach($works as $kk=>$vv){
                $ids[] = $vv['owner_id'];
								$array_list[$vv['owner_id']]['add_time'] = $vv['add_time'];
            }
            $ids = implode(',',$ids);
            $conditions = "invitee IN ({$ids})";
//            print_exit($ids);
            $cus_mod = m('memberinvite');
            $mem_mod = m('member');
            $cus_list = $cus_mod->find(array(
                'conditions' => $conditions,
                'fields'     => 'invitee'
            ));

            foreach($cus_list as $key=>$val){
                $member = $mem_mod->get($val['invitee']);
                $val['phone_mob'] = $member['user_name'];
                $val['nickname']  = $member['nickname'];
								//ns add 添加作品生成时间
								$val['add_time'] = date('Y-m-d',$array_list[$val['invitee']]['add_time']);
                //获得用户头像
                $val['avatar'] = $this->getAvatar($val['invitee']);
                $result[] =$val;
            }
            $this->result->result = $result;
            return $this->result->sresult();
        }




	  /**
	  * 评论列表
	  */
	  function commentList($data)
	  {
	      $token = isset($data->token) ? $data->token : '';
	      $pageSize = isset($data->pageSize) ? $data->pageSize : 10;
	      $pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
	      $id = isset($data->id) ? $data->id : 0;
	      $limit = $pageSize*($pageIndex-1).','.$pageSize;

	      $user_info = getUserInfo($token);
	      if (!$user_info)
	      {
	          $this->result->tresult();
	      }
	      $user_id = $user_info['user_id'];

	      //=====  验证数据合法性  =====
	      if (!$this->mod->get($id))
	      {
	          $this->result->msg = '无此数据';
	          return $this->result->eresult();
	      }

	      $mod = m('comcomments');
	      $conditions = "comment_id = $id AND cate='wk' ";
	      $list = $mod->find(array(
              'conditions' => $conditions,
	          'join' => 'belongs_to_member',
	          'fields' => "com.*,member.avatar",
	          'limit' => $limit,
	          'index_key' => '',
	      ));

	      $this->result->result = $list;
	      return $this->result->sresult();
	  }

        /**
         * 继续设计
         */
        function continueDiy($data){
            $token = isset($data->token) ? $data->token : '';
            $id = isset($data->id) ? $data->id : 0;
            $user_info = getUserInfo($token);
            if (!$user_info)
            {
                $this->result->tresult();
            }
            $user_id = $user_info['user_id'];
            if(!$id){
                $this->result->msg ='请选择要设计的作品';
                return $this->result->eresult();
            }
            $work = $this->mod->get(array(
                'conditions' => "id = {$id} AND (owner_id = {$user_id} or counter_user_id={$user_id})",
            ));
            if(!$work){
                $this->result->msg ='未找到该作品';
                return $this->result->eresult();
            }
            do{
                $api_token = ApiAuthcode($user_info['user_id'], 'ENCODE', 'kuteiddiy', 0);
            } while (!preg_match("/[a-zA-Z\d]{40,42}$/u", $api_token));
            //custom-diy-0004-bformal-13af038wcit9xkmZSJBCGgfe3SQOZkUyPXhMDQFqkA.html
            $result['url'] = DIYURL.$work['cloth'].'-'.$work['style'].'-'.$api_token.'-'.$work['id'].'.html';
            $this->result->result = $result;
            return $this->result->sresult();
        }


	  function test(){
		  print_r(file_get_contents('../../data/settings.inc.php'));
         exit;
	  }


    /*
     * ns add 获取反推列表
     * 2015-12-14
     */
    function Counter($data){
        include(PROJECT_PATH . 'includes/libraries/diys.lib.php');
        $diys = new Diys();

        $token = isset($data->token) ? $data->token : '';
        $pageSize = isset($data->pageSize) ? $data->pageSize : 10;
        $pageIndex = isset($data->pageIndex) ? $data->pageIndex : 1;
        $cloth = isset($data->cloth) ? $data->cloth : '';
        $user_info = getUserInfo($token);

        if (!$user_info)
        {
            return $this->result->tresult();
        }
          $user_id = $user_info['user_id'];
          //查询条件
          $conditions = $conditions = 'is_counter=1 AND counter_user_id ='.$user_id .' AND iscover=1 ';
          if($cloth){
              $conditions .= ' AND cloth = '.$cloth;
          }
        $limit = $pageSize*($pageIndex-1).','.$pageSize;
        $sql = "SELECT w.id,w.cloth,w.work_name,w.designer_id,w.diy_data,w.description,w.add_time,w.owner_id,i.iscover,i.img_url FROM cf_works w  JOIN cf_work_imgs i ON w.id=i.work_id WHERE {$conditions} AND source_from='app'  ORDER BY w.add_time DESC LIMIT {$limit}";
        $works = $this->mod->getALL($sql);
        //=======获取价格、设计人姓名
        foreach($works as $key=>$val){
            $params = json_decode($val['diy_data'],true);
            if($params){
            //获取价格于面料格式整合
            foreach($params['sysprocess'] as $k=>$v){
                $params_array[$val['id']][$k] = $v['fabric'];
             }
            }
            // if($val['designer_id'] != $user['user_id']){
            //     $members = $this->member_mod->get(array(
            //         'conditions' => 'user_id ='.$val['designer_id'],
            //         'fields'     => 'nickname'
            //     ));
            //     $works[$key]['designer_name'] = $members['nickname'];
            // }
            //获取推送者-消费者-用户信息
            $works[$key]['x_user'] = $this->member_mod->get(array(
                    'conditions' => 'user_id ='.$val['owner_id'],
                    'fields'     => 'nickname,user_name'
                ));
        }
        //获取价格跟面料是否下架-lgx提供接口
        if($params_array){
            $works_diys = $diys->_calcPrice($params_array);
            foreach ($works as &$value) {
							  $works_diys[$value['id']]['price'] = (int)($works_diys[$value['id']]['price']);
                $value['pa'] = $works_diys[$value['id']];
            }
        }

        $this->result->result = $works;
        return $this->result->sresult();
    }


  /**
    *获取反推作品详情
    */
    function Counter_workInfo($data)
    {
        include(PROJECT_PATH . 'includes/libraries/diys.lib.php');
        $diys = new Diys();

        $token = isset($data->token) ? $data->token : '';
        $id = isset($data->id) ? $data->id : 0;
        $user_info = getUserInfo($token);
        if (!$user_info)
        {
            return $this->result->tresult();
        }

        $user_id = $user_info['user_id'];

        $conditions = 'w.id = '.$id.' AND is_counter=1 AND counter_user_id = '.$user_id;

        //$conditions = 'w.id = '.$id.' AND owner_id = '.$user_id ;

        $sql = "SELECT w.id,w.cloth,w.work_name,w.owner_id,w.style,w.designer_id,w.description,w.diy_data,w.add_time,w.is_counter,w.counter_add_time,w.owner_id,w.counter_description,i.iscover,i.img_url FROM
                 cf_works w  JOIN cf_work_imgs i ON w.id=i.work_id WHERE {$conditions} AND source_from='app' ORDER BY i.iscover DESC";

        $works = $this->mod->getALL($sql);
          if (!$works)
          {
              $this->result->msg = '此数据不存在';
              return $this->result->eresult();
          }
          do{
              $api_token = ApiAuthcode($user_info['user_id'], 'ENCODE', 'kuteiddiy', 0);
          } while (!preg_match("/[a-zA-Z\d]{40,42}$/u", $api_token));


          $result = array();
          foreach($works as $key=>$val){
              //==========获取设计者名称========
              $member_mod = m('member');
              $member = $member_mod->get($val['owner_id']);
              $result['id']            = $val['id'];
              $result['cloth']         = $val['cloth'];
              $result['work_name']     = $val['work_name'];
              $result['designer_id']   = $val['designer_id'];
              //$result['designer_name'] = $member['nickname'];
              $result['description']   = $val['description'];
              $result['diy_data']      = $val['diy_data'];
              $result['add_time']      = $val['add_time'];
              $result['imgs'][]        = array_pop($val);
              //=========获取分享url
              $result['url']  = SHAREURL.$val['cloth'].'-'.$val['style'].'-mfd-'.$this->_cropWork($id).'-share.html';
              $result['curl'] = DIYURL.$val['cloth'].'-'.$val['style'].'-'.$api_token.'-'.$val['id'].'.html';
              $result['is_counter'] = $val['is_counter'];
              $result['counter_add_time'] = date("Y-m-d h:i:s", $val['counter_add_time']);
              $result['counter_info'] = $this->member_mod->get(array(
                         'conditions' => 'user_id ='.$val['owner_id'],
                         'fields'     => 'nickname,user_name'
                 ));
              $result['counter_description'] = $val['counter_description'];
          }

          $diy_data = json_decode($result['diy_data'],true);

          //遗漏问题
          array_pop($diy_data);

          //ns add 获取面料上下架与diy同步价格
          if($diy_data){
               foreach($diy_data['sysprocess'] as $k=>$v){
                   $params_array[$val['id']][$k] = $v['fabric'];
               }
          }

          if($params_array){
              $works_diys = $diys->_calcPrice($params_array);
              $result['price'] = (int)($works_diys[$id]['price']);
              $result['is_sale'] = $works_diys[$id]['is_sale'];
          }


          $rData =array();
          foreach($diy_data as $kk=>$vv){
              foreach ((array)$vv as $key=>$row){
                  //将数据格式化成与PC一致
                  foreach ($row['cstr'] as $pk=>$pv){
                      $vt = explode(':', $pv);
                      $row['prm'][$vt[0]] = $vt[1];
                  }
                  $rData[$key] = json_encode($row['prm'],JSON_UNESCAPED_UNICODE);
              }
          }
          //===========获取工艺信息=========
          $dict_mod = m('dict');

          $params = array();

          foreach($rData as $key=>$value){
              $params[$key] = json_decode($value,true);
          }
//          print_exit($params);
          if ($params)
          {
              $tmp = array();
              foreach ((array)$params as $key1 => $value1)
              {
                  $i=0;
                  $value1 = array_filter($value1);//去除工艺中的空值
                  foreach((array)$value1 as $key2 => $value2){
                      $value1_val = explode("|", $value2);

                          $dict2_info = $dict_mod->get("id=".$key2);
                          $a[$key1][$i]['p_name'] =$dict2_info['name'];
                          $dict1_info = $dict_mod->get("id=".$value1_val[0]);
                          if (count($value1_val) > 1)
                          {
                              $a[$key1][$i]['s_name'] = $dict1_info['name'] .$value1_val[2];
                          }
                          else
                          {
                              $a[$key1][$i]['s_name'] = $dict1_info['name'];
                          }
                          $i++;

                  }
                  $result['params_cloth'][] = $key1;
                  $result['params_value'][] = $a[$key1];
              }
          }
          if(!$result['params_value']){
              $result['params_value'] = array();
          }
        $this->result->result = $result;
        return $this->result->sresult();
    }



    /**
     * 反推作品
     *@author ns add
     *@2015年11月26日
     */
    function counterWork($data){
        $token = isset($data->token) ? $data->token : '';
        $id = isset($data->id) ? $data->id : 0;
        if(!is_numeric($id)){
            $this->result->msg = '作品推送失败';
            $this->result->errorCode = -1;
            return $this->result->eresult();
        }
        $user = getUserInfo($token);
        if (!$user)
        {
            return $this->result->tresult();
        }
        //判断是否为普通消费者
        if($user['member_lv_id'] > 1){
            $this->result->msg = '只有消费者才能进行推送创业者';
            $this->result->errorCode = -1;
            return $this->result->eresult();
        }else{
            //查看是否有绑定用户
            //$member_invite_mod = m('memberinvite');
            $invite = $this->member_invite_mod->get(array(
            'conditions'=>'invitee = '. $user['user_id'],
            ));
            if($invite){
                //进行查询是否推送过
                $works = $this->mod->get(array(
                    'conditions'=>'id = '.$id.' AND owner_id = '. $user['user_id'],
                    ));
                if($works['is_counter'] > 0 || $works['counter_user_id'] > 0){
                    $this->result->msg = '你已经进行推送过了';
                    $this->result->errorCode = -1;
                    return $this->result->eresult();
                }
                return $this->result->sresult();
            }else{
                //没有信息就进行弹窗版定
                $this->result->msg = '没有绑定创业者';
                $this->result->errorCode = -2;
                return $this->result->eresult();
            }
        }
    }



    //进行添加推送
    function add_counterWork($data){
        $counter_description = isset($data->counter_description) ? $data->counter_description : '';
        $token = isset($data->token) ? $data->token : '';
        $id = isset($data->id) ? $data->id : 0;
        if(!is_numeric($id)){
            $this->result->msg = '作品推送失败';
            $this->result->errorCode = -1;
            return $this->result->eresult();
        }
        $user = getUserInfo($token);
        if (!$user)
        {
            return $this->result->tresult();
        }
        //查看是否有绑定用户
         // $member_invite_mod = m('memberinvite');
         $invite = $this->member_invite_mod->get(array(
         'conditions'=>'invitee = '. $user['user_id'],
         ));
         if($invite){
        //进行查询是否推送过
             $works = $this->mod->get(array(
                     'conditions'=>'id = '.$id.' AND owner_id = '. $user['user_id'],
             ));
            if($works['is_counter'] > 0 || $works['counter_user_id'] > 0){
                    $this->result->msg = '你已经进行推送过了';
                    $this->result->errorCode = -1;
                    return $this->result->eresult();
            }
            //进行推送的添加
            $data = array(
            'is_counter' => 1,
            'counter_user_id' => $invite['inviter'],
            'counter_add_time' => time(),
            'designer_id'=> $invite['inviter'],
            'counter_description' => $counter_description
            );
            $this->mod->edit($id,$data);
            return $this->result->sresult();
         }else{
             //没有信息就进行弹窗版定
             $this->result->msg = '没有绑定创业者';
             $this->result->errorCode = -2;
             return $this->result->eresult();
         }
    }


		//ns add 添加bd绑定
    function addinviter($data){
        //BD码
        $invite = isset($data->invite) ? $data->invite : '';
        $token = isset($data->token) ? $data->token : '';

        // $m = &m('member');
        // $meminvite_mod = &m('memberinvite');
        $_generalize_mod = m('generalize_member');
        $customer_figure= m('customer_figure');
        $user = getUserInfo($token);
        if (!$user)
        {
            return $this->result->tresult();
        }
        //$user_id = $this->visitor->get('user_id');


        //db+邀请  只能绑定一种码
        if($this->member_invite_mod->get("invitee = ".$user['user_id']))
        {
            $this->result->msg = '您已经邀请过';
            return $this->result->eresult();
        }


        if(strlen($invite)==12){
            //创业者

            //存在db
            $g_member = $_generalize_mod->get("status=1 and invite = '".$invite."'");

            if(empty($g_member))
            {
                $this->result->msg = 'BD码不存在';
                return $this->result->eresult();
            }
            $_type =1;
            $invite_nickname=$g_member['name'];
            $inviter = $g_member['id'];

        }else{

            //创业者过滤绑定邀请码
            if($user['member_lv_id']>1)
            {
                $this->result->msg = '创业者不能参与此活动';
                return $this->result->eresult();

            }

            $member = $this->member_mod->get( "serve_type=1 and invite = '".strtoupper($invite)."'");


            if($member['user_id'] == $user['user_id']){
                $this->result->msg = '不能邀请自己';
                return $this->result->eresult();
            }
            if(empty($member))
            {
                $this->result->msg = '邀请码错误';
                return $this->result->eresult();
            }

            $_type =0;
            //邀请码
            if(empty($member['nickname']))
            {
                if(empty($member['real_name']))
                {
                    $invite_nickname =$member['user_name'];
                }else
                {
                    $invite_nickname =$member['real_name'];
                }
            }else
            {
                $invite_nickname =$member['nickname'];
            }

            $inviter = $member['user_id'];
        }


        //邀请关系    邀请码 db码都放一个表
        //$member_invite = m("memberinvite");
        $invite_data = array(
            'inviter'  => $inviter, //邀请人
            'invitee'  => $user['user_id'],
            'nickname' => $invite_nickname,     //邀请人昵称
            'type'      => $_type,
            'add_time' => time(),
            'come_from'=>'app|my_works',
        );

        $this->member_invite_mod->add($invite_data);

        if(!empty($g_member)){
            change_lv($user['user_id']);
        }
        if(!empty($member)) {

            //奖励
            $custom_info = $customer_figure->get(array(
                'conditions' =>"storeid='{$inviter}' and customer_mobile='{$user['user_name']}' and type_cus <> 0",
                'fields' =>"figure_sn",
            ));
            if(!empty($custom_info)){

                $c_ret = $customer_figure->edit("figure_sn='{$custom_info['figure_sn']}'",array('userid'=>$user['user_id'],'type_cus'=>2,'firsttime'=>time(),'lasttime'=>time()));

                if(!$c_ret)
                {
                  $this->result->msg = '顾客修改不成功';
                  return $this->result->eresult();
                }
            }else{
                $custome_data=array(
                    'storeid'  => $inviter, //邀请的人的id
                    'customer_mobile'  => $user['user_name'],//被邀请人的手机号
                    'userid' => $user['user_id'],  //被邀请人的id
                    'customer_name'    => $user['nickname'],//被邀请人的昵称
                    'type_cus' => 3,//类型
                    'firsttime' => time(),
                    'lasttime' => time(),
                );
                $customer = $customer_figure->add($custome_data);
                if(!$customer){
                  $this->result->msg = '添加 顾客不成功!';
                  return $this->result->eresult();

                }
            }

            $store_allow = include  ROOT_PATH.'/data/settings.inc.php';
            $this->_debit_mod = &m("debit");

            if($user_info['serve_type'] == 1){
                //礼券- self
                if(!empty($store_allow['debit_cate']) && !empty($store_allow['debit_time'])&& !empty($store_allow['debit_name'])&&!empty($store_allow['debit_num']) && !empty($store_allow['debit_type'])){

                    if($store_allow['debit_cate']==1){
                        $expire_time =strtotime('+'.$store_allow['debit_time'].' days') - date('Z');
                    }else{
                        $expire_time =$store_allow['debit_time'];
                    }

                    $data =array(
                        'debit_name'=>$store_allow['debit_name'],
                        'debit_sn'=>time().createNonceStr(8),
                        'money'=>$store_allow['debit_num'],
                        'user_id'=>$user['user_id'],
                        'source'=>'invite',
                        'add_time'=>time(),
                        'cate'=>$store_allow['debit_type'],
                        'expire_time'=>$expire_time,
                    );
                    $this->_debit_mod->add($data);
                }

                //礼券 -self
                if(!empty($store_allow['debit_cate2']) && !empty($store_allow['debit_time2'])&& !empty($store_allow['debit_name2'])&&!empty($store_allow['debit_num2']) && !empty($store_allow['debit_type2'])){
                    if($store_allow['debit_cate2']==1){
                        $expire_time2 =strtotime('+'.$store_allow['debit_time2'].' days') - date('Z');
                    }else{
                        $expire_time2 =$store_allow['debit_time2'];
                    }

                    $data =array(
                        'debit_name'=>$store_allow['debit_name2'],
                        'debit_sn'=>time().createNonceStr(8),
                        'money'=>$store_allow['debit_num2'],
                        'user_id'=>$user['user_id'],
                        'source'=>'invite',
                        'add_time'=>time(),
                        'cate'=>$store_allow['debit_type2'],
                        'expire_time'=>$expire_time2,
                    );
                    $this->_debit_mod->add($data);
                }
            }
            return $this->result->sresult();
        }
    }



}
