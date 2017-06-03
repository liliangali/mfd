<?php


class FigureorderlogApp extends BackendApp
{
	
    function __construct()
    {
    	parent::__construct();
        $this->assign('modelname','my_order_serve_2');
        
        
        
        $this->data_mod=m('figureorderlog');
        
    }

    function index()
    {
        $conditions = $this->_get_query_conditions(array(
            array(
                'field' => $_GET['field_name'],
                'name'  => 'field_value',
                'equal' => 'like',
            ),
        ));
    	
        if($_GET['t']&&$_GET['t']=='3')
        {
        	$conditions.=' and serve.serve_type=3 ';
        }elseif($_GET['t']&&$_GET['t']=='2')
        {
        	$conditions.=' and serve.serve_type=2 ';
        }else 
        {
        	$conditions.=' and serve.serve_type=2 ';
        }
    	//var_dump($conditions);
    	//更新排序
        if (isset($_GET['sort']) && !empty($_GET['order']))
        {
            $sort  = strtolower(trim($_GET['sort']));
            $order = strtolower(trim($_GET['order']));
            if (!in_array($order,array('asc','desc')))
            {
             $sort  = 'idfigureorderlog';
             $order = 'desc';
            }
        }
        else
        {
            if (isset($_GET['sort']) && empty($_GET['order']))
            {
                $sort  = strtolower(trim($_GET['sort']));
                $order = "";
            }
            else
            {
                $sort  = 'idfigureorderlog';
                $order = 'desc';
            }
        }
        
        $page = $this->_get_page();
        $figures = $this->data_mod->find(array(
        	'fields'=>'this.*,serve.serve_type',
        	'conditions' => '1=1'. $conditions,  
        	'join'=>'has_serve',  
        	'limit' => $page['limit'],
            'order' => "$sort $order",
            'count' => true,
        ));
		//var_dump($figures);
        $this->assign('datas', $figures);
        $page['item_count'] = $this->data_mod->getCount();
        $this->_format_page($page);

        $this->assign('page_info', $page);
        
        $this->assign('query_fields', array(
            'idfigureorderlog' => 'id'
        ));
        $this->assign('sort_options', array(
            'add_time DESC'   => '添加时间',
            'idfigureorderlog DESC' => 'id'
        ));
        
        
        $this->display('figureorderlog.index.html');
    }
	
	function edit()
    {
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        $figure = $this->data_mod->get_info($id);
        
        
        if (!IS_POST)
        {
            /* 是否存在 */
            
            if (!$figure)
            {
                $this->show_warning('user_empty');
                return;
            }
			
            
            //var_dump($figure);
            
            
            
            $this->assign('figure', $figure);

            /* 导入jQuery的表单验证插件 */
            $this->import_resource(array(
                'script' => 'jquery.plugins/jquery.validate.js'
            ));
            
        	$this->assign('acc',LANG::get('edit'));
        	
        	
        	
            $this->display('figureorderlog.form.html');
        }
        else
        {
        	
        	
        	if(!$_POST['operator_state'])
        	{
        		$this->show_warning('user_empty');
                return;
        	}
        	
            
			
            if(!$figure['operator_state']||$figure['operator_state']==1)
            {
            	
	            $data = array(
	                "operator_state"=>$_POST['operator_state'],
	            	"operator_comment"=>trim($_POST['operator_comment']),
	            	"operator_user"=>$this->visitor->get('user_name'),
	            	'settle_time'=>gmtime(),
	            
	            );
	            /* 修改本地数据 */
	            $this->data_mod->edit($id, $data);
	            
	            $figureadminlog_mod=m('figureadminlog');
	            //var_dump($figureadminlog_mod);exit;
	            $figureadminlog_data=array();
	            $figureadminlog_data['cate']='figureorder';
	            if($_POST['operator_state']=='1')
	            {
	            	$figureadminlog_data['opt']='未结算';
	            	$figureadminlog_data['div_amount']=0;
	            }elseif($_POST['operator_state']=='2')
	            {
	            	$figureadminlog_data['opt']='已结算';
	            	$figureadminlog_data['div_amount']=$figure['div_amount'];
	            }
	            $figureadminlog_data['add_time']=gmtime();
	            $figureadminlog_data['author']=$this->visitor->get('user_name');
	            $figureadminlog_data['msg']='量体订单分成';
	            
	            $figureadminlog_data['idserve']=$figure['serviceid'];
	            $figureadminlog_data['order_id']=$figure['order_id'];
	            
	            $figureadminlog_mod->add($figureadminlog_data);
	            
	        }
            
	        

            $this->show_message('修改成功',
                'back_list',    'index.php?app=figureorderlog&t='.$_POST['t'],
                '再次编辑',   'index.php?app=figureorderlog&amp;act=edit&amp;id=' . $id.'&t='.$_POST['t']
            );
        }
    }
    
}

?>
