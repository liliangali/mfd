<?php
/**
 * 订单日志
 *
 * @author Ruesin
 */
class OrderLogs extends Object
{
    private $_mod_orderlogs;
    var $_behaviors = array('create','update','cancel','autoCancel','payment','production','delivery','quickPrice','pushPro','pushDel','remarks');
    
    function __construct()
    {
        
        $this->_mod_orderlogs = &mr('orderlogs');

    }
    
    function _record($ops = array()){ 
       
        $status = array(
                11 => '待付款',
                12 => '待量体',
                20 => '已付款',
                60 => '生产中',
                61 => '备货中',
                30 => '已发货',
                40 => '已完成',
                41 => '返修中',
                0  => '已取消',
                43 => '订单异常',
                99 => '订单备注',
        );
        
        if(!$ops) return;
        
        if(!$ops['behavior'] || !in_array($ops['behavior'], $this->_behaviors)) return ;
        $from = isset($ops['from']) ? (isset($status[$ops['from']]) ? $status[$ops['from']] : $ops['from'] ) : '';
        $to   = isset($ops['to']) ? (isset($status[$ops['to']]) ? $status[$ops['to']] : $ops['to'] ) : '';
        if(!$ops['text']){
            switch ($ops['behavior']){
            	case 'create':
            	    $ops['text'] = "订单创建成功!";
            	    break;
            	case 'update':
            	    $ops['text'] = "订单状态从[{$from}]更新到[{$to}]!";
            	    break;
            	case 'cancel':
            	    $ops['text'] = "订单从[{$from}]状态取消!";
            	    break;
            	case 'autoCancel':
            	    $ops['text'] = "订单自动取消!";
            	    break;
            	case 'payment':
            	    $ops['text'] = "订单支付成功!";    			
            	    break;
            	case 'production':
            	    $ops['text'] = "订单已进入生产中!";
            	    break;
            	case 'delivery':
            	    $ops['text'] = '订单已发货!';
            	    break;
            	case 'quickPrice':
            	    $ops['text'] = '订单快速修改价格成功!';
            	    break;
                case 'remarks':
            	        $ops['text'] = '订单增加备注!';
            	        break;
            	default:
            	    $ops['text'] = "hacking!";
            	    break;
            }
        }

        $save = array(
                'order_id' => $ops['order_id'],
                'op_id'    => $ops['op_id'],
                'op_name'  => $ops['op_name'],
                'alttime'  => gmtime(),
                'behavior' => $ops['behavior'],
                'log_text' => $ops['text'],
                'from_status'     => $ops['from'],
                'to_status'       => $ops['to'],
                'op_ip'    => real_ips(),
                'remark'   => $ops['remark'],
                
        );
        return $this->_mod_orderlogs->add($save,false,0);
    }
    
    
}

