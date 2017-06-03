<?php
/**
 * 营销大类
 */
class PromotionApp extends BackendApp{

    function __construct(){
        $this->PromotionApp();
    }
    function PromotionApp(){
        parent::BackendApp();
    }
    
    function onegift(){
    	
        $mOneGift = &m('onegift');
        
        $list = $mOneGift->find();
        $time = gmtime();
        foreach ($list as &$row){
            if($row['is_active'] == '1' && $row['end_time'] <= $time ){
                $upIds[$row['one_id']] = $row['one_id'];
                $row['is_active'] = '0';
            }
        }
        
        if($upIds){
            $mOneGift->edit(db_create_in($upIds,'one_id'),array('is_active'=>'0','last_time'=>$time));
        }
        
        $has = $mOneGift->get(" is_active = '1' ");
        $this->assign('has',$has);
        $this->assign('list',$list);
        $this->assign('status',array('0'=>'否','1'=>'是'));
        $this->display('promotion/onegift/index.html');
    }
    
    function addOneGift(){
        $mOneGift = &m('onegift');
        
        if(!$_GET['id'] && !$_POST['one_id']){
            $has = $mOneGift->get(" is_active = '1' ");
            if($has){
                $this->show_warning('已有正在进行的活动,请稍后再试!');
                return;
            }
        }
        
        
        if(!IS_POST){
            
            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                    'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
            
            $data = $mOneGift->get(" one_id = '{$_GET['id']}' ");
            
            $data['condi'] = json_decode($data['condi'],1); 
            $data['appoint_fabric'] = json_decode($data['appoint_fabric'],1); 
            
            $this->assign('data',$data);
            
            $this->assign('cloths',$this->cloths());
            $this->assign('algorithms',$mOneGift->algorithms());
            $this->assign('status',array('0'=>'否','1'=>'是'));
            $this->display('promotion/onegift/add.html');
        }else{
            
            $id = isset($_POST['one_id']) ? $_POST['one_id'] : '';
            $time = gmtime();
            
            if($_FILES['appoint_fabric'] && $_FILES['appoint_fabric']['size'] > 0 && $_FILES['appoint_fabric']['error'] == UPLOAD_ERR_OK && $_POST['is_fabric']){
                $files = fopen($_FILES['appoint_fabric']['tmp_name'], 'r');
                while ($row = fgetcsv($files)) {
                    if( count($row) != 2 || !in_array($row[1], array('8001','8030','8050')) || !$row[0] || !$row[1] ){
                        /* $fabric = array();
                        break; */
                        continue;
                    }
                    $fabric[$row[1]][$row[0]] = $row[0];
                }
            }
            
            
            
            /* if ($_FILES['appoint_fabric']['error'] == UPLOAD_ERR_OK){
                import('uploader.lib');
                $uploader = new Uploader();
                $uploader->allowed_type('csv');
                $uploader->addFile($_FILES['appoint_fabric']);
                if ($uploader->file_info() != false)
                {
                    $uploader->root_dir(ROOT_PATH);
                    
                    $str = $uploader->save('data/files/mall/promotion' , 'appoint_fabric_'.$time);
                    if($str){
                        $files = fopen(ROOT_PATH.'/'.$str, 'r');
                        while ($row = fgetcsv($files)) {
                            $fabric[] = $row;
                        }
                    }
                }
            } */
            
            $data = array(
                    'is_active'   => $_POST['is_active'],
                    'is_employee' => $_POST['is_employee'],
                    'start_time'  => strtotime($_POST['start_time']),
                    'end_time'    => strtotime($_POST['end_time']),
                    'condi'       => $_POST['condi'] ? json_encode($_POST['condi']) : '',
                    'is_fabric'   => $_POST['is_fabric'],
                    'algo'        => $_POST['algo'],
                    'add_time'    => $time,
                    'last_time'   => $time,
            );
            
            if ($id){
                
                
                if(!$_POST['is_fabric']){
                    $data['appoint_fabric'] = '';
                }elseif ($fabric){
                    $data['appoint_fabric'] =  json_encode($fabric);
                }
                
                $res = $mOneGift->edit(" one_id = '{$id}' ",$data);
                if($res >= 0){
                    $this->show_message('保存成功!','返回列表','index.php?app=promotion&act=onegift');
                    return;
                }else{
                    $this->show_warning('保存失败!');
                    return;
                }
                
            }else{
                $data['appoint_fabric'] = empty($fabric) ? '' : json_encode($fabric);
                $res = $mOneGift->add($data);
                if($res){
                    $this->show_message('保存成功!','返回列表','index.php?app=promotion&act=onegift');
                    return;
                }else{
                    $this->show_warning('保存失败!');
                    return;
                }
            }
            
            
        }
        
    }
    function input_csv($handle) {
        $out = array ();
        $n = 0;
        while ($data = fgetcsv($handle, 10000)) {
            $num = count($data);
            for ($i = 0; $i < $num; $i++) {
                $out[$n][$i] = $data[$i];
            }
            $n++;
        }
        return $out;
    }
    
    public function ajaxCondi(){
        
        $key = intval($_POST['key']) ? intval($_POST['key']) + 1 : 1 ;
        
        $this->assign('key',$key);
        $this->assign('cloths',$this->cloths());
        
        
        $this->json_result(
            	array(
        	'content' => $this->_view->fetch('promotion/onegift/ajaxcondi.html'),
            	        'key' => $key,
        )
        );
        die();
    }
    
    function cloths(){
        
        return array(
                '0003' => '男西服',
                '0004' => '男西裤',
                '0006' => '男衬衣',
                '0016' => '女衬衣',
        );
    }
    
    function importEm(){
        
        $mOneGift = &m('onegift');
                
        if(!IS_POST){
            
            if(!$_GET['id']){
                $this->show_warning('参数错误!');
                return;
            }
            
            $data = $mOneGift->get(" one_id = '{$_GET['id']}' AND is_active = '1' ");
            
            if(!$data){
                $this->show_warning('参数错误!');
                return;
            }
            
            $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                    'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
            
            $this->assign('data',$data);
            $this->display('promotion/onegift/import.html');
        }else{
        
            $files = fopen($_FILES['employee']['tmp_name'], 'r');
            while ($row = fgetcsv($files)) {
                /* if( count($row) != 2 || !in_array($row[1], array('8001','8030','8050')) ){
                    $fabric = array();
                    break;
                }
                $fabric[$row[1]][] = $row[0]; */
                if(!$row['0']) continue;
                $list[] = array(
                        'num'  => $row['0'],
                        'name' => $row['1'] ? iconv("GBK", "UTF-8", $row['1']) : '',
                        'com'  => $row['2'] ? iconv("GBK", "UTF-8", $row['2']) : '',
                        'start_time' => strtotime($_POST['start_time']),
                        'end_time'   => strtotime($_POST['end_time']),
                        'status'     => '0',
                        'one_id'     => $_POST['one_id'],
                );
            }
            //num name com start_time end_time status one_id
            $mEmployee = &m('rcemployee');
            $res = $mEmployee->add(addslashes_deep($list));
            if($res){
                $this->show_message('导入成功!','返回列表','index.php?app=promotion&act=onegift');
                return;
            }else{
                $this->show_warning('导入失败!');
                return;
            }
        }
    }
    
    
    public function employee() {
        
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if(!$id){
            $this->show_warning('ID错误!');
            return;
        }
        
        $conditions = $this->_get_query_conditions(array(
                array(
                        'field' => 'num',
                        'name'  => 'num',
                        'equal' => '=',
                )
        ));
        
        $mRcem = &m('rcemployee');
        
        $page   =   $this->_get_page(30);
        if (isset($_GET['sort']) && isset($_GET['order'])) {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc'))) {
                $sort  = 'id';
                $order = 'desc';
            }
        } else {
            $sort  = 'id';
            $order = 'desc';
        }
        $list = $mRcem->find(array(
                'conditions'    => " one_id = '{$id}' " . $conditions,
                'limit'         => $page['limit'],
                'order'         => "$sort $order",
                'count'         => true
        ));
        $page['item_count'] = $mRcem->getCount();   //获取统计的数据
        $this->_format_page($page);
        
        $this->assign('page_info', $page); 
        $this->assign('list', $list);
        $this->assign('id',$id);
        $this->import_resource(array('script' => 'inline_edit_admin.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
                                      'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
        $this->display('promotion/onegift/employee.html');
        
    }
    
    
    
    
    //===============================================================//
    //===================== ↓ 老数据 ↓ ===============================//
    //===============================================================//
    function index(){
    	$this->proReg();
    }
    //新会员优惠
    function proReg(){
        if(!IS_POST){
            $this->_data('register');
            $keys=array(
                    'point'=>'送积分',
                    'coupon'=>'送优惠券',
                    'exp'=>'送体验券',
            );
            $this->assign('keys',$keys);
            $this->display("promotion/register.html");
        }else{
            $this->_save($_POST,'register');
        }
    }
    /*
     * 下单送干洗
     * 当消费者下订单（后台可控时间段）
     * 当条件满足的消费者（第一次下订单，或者下订单满XX元）
     * 干洗次数随着购买产品的类型不同而不同，后台设定；
     */
    function proSend(){
        if(!IS_POST){
            $this->_data('send');
            $keys=array(
                    'first'=>'首次下单',
                    'amount'=>'订单满X元',
            );
            $this->assign('keys',$keys);
            $this->display("promotion/send.html");
        }else{
            $this->_save($_POST,'send');
        }
    }
    /*
     * 定制初体验:第一次下订单的消费者，某些时间段，享受免单或半价优惠；
     */
    function proFirst(){
    	if(!IS_POST){
    	    $this->_data('first');
    	    $this->display("promotion/first.html");
    	}else{
    	    if($_POST['value'] <= 0 || $_POST['value'] > 10){
    	    	$this->show_warning('请输入正确的折扣率!','返回','index.php?app=promotion&act=proFirst');
    	    	exit;
    	    }
    	    $this->_save($_POST,'first');
    	}
    }
    /**
     * 获取数据
     * @param
     * @return
     * @author Ruesin
     */
    function _data($type='register'){

        $this->import_resource(array('script' => 'jquery.plugins/jquery.validate.js,inline_edit.js,jquery.ui/jquery.ui.js,jquery.ui/i18n/' . i18n_code() . '.js',
            'style'=> 'jquery.ui/themes/ui-lightness/jquery.ui.css'));
        $active = array(
            'yes' => '是',
            'no'  => '否',
        );
        $this->assign('active',$active);
        $data=getConf('promotion',$type);

        $data['value']=$data[$data['key']]['value'];
        $this->assign('data',$data);
    }
    /**
     * 保存数据
     * @param array $post
     * @return void
     * @author Ruesin
     */
    function _save($post,$type='register'){

        $post['start_time']   = isset($post['start_time']) ? strtotime($post['start_time']) : '';
        $post['end_time']     = isset($post['end_time']) ? strtotime($post['end_time']) : '';
        if($post['start_time']>$post['end_time']){
            $this->show_warning("开始时间不能小于结束时间!");
            return;
        }

        $post[$post['key']]['value']=$post['value'];
        unset($post['value']);
        if(setConf('promotion',$type,$post)){
            $this->show_message("设置成功!");
            return;
        }

    }

    //ajax获取不同类型的优惠
    function ajax_type($key='point'){
    	$key=isset($_GET['key'])?trim($_GET['key']):'point';
    	$value=isset($_GET['value'])?trim($_GET['value']):'';

    	$this->assign('key',$key);
    	$this->assign('value',$value);
    	echo $this->_view->fetch('promotion/register_type.html');
//     	$this->display('promotion/register_type.html');
    }
    //ajax send
    function send_ajax($type='first'){
        $type=isset($_GET['key'])?trim($_GET['key']):'first';
        $value=isset($_GET['value'])?trim($_GET['value']):'';
        if($type=='amount'){
            echo "<th class='paddingT15'><label> 订单满足 :</label></th>
            <td class='paddingT15 wordSpacing5'><input id='value' type='text' name='value' value='{$value}' class='infoTableInput required digits'/></td>";
        }
    }
    /**
     * 获取优惠券名称
     * @param int $id 优惠券ID
     * @return string 优惠券名称
     * @author Ruesin
     */
    function ajax_coupon_name($id=0){

        $id=isset($_GET['id'])?trim($_GET['id']):0;
        if($id==0)exit('请选择优惠券');
        $m =& m('coupon');
        $arr=$m->get($id);
        echo $arr['cpn_name'];
    }
}


