<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public  $table = "order";
    public  $primaryKey = "order_id";
    const SEND_QR_TIME = 65;

    /**
     *  应被转换为日期的属性。
     *
     * @var array
     */
    protected $dates = [];
    public  $timestamps = false;//去掉update_time等三个字段
    public $order_status = [11,20,30,40,0];
    const ORDER_STATUS = [0,11,20,30,40,0];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /*
   * 通过id获取任务
   * @param id
   */
    protected static function getAll($request)
    {
        $condition[] = ['order_id','>=','1'];
        if(isset($request['username']) && $request['username'])
        {
            $condition[] = ['user_name','like',$request['username'].'%'];
        }
        if(isset($request['chname']) && $request['chname'])//渠道上线
        {
            $user = User::where('user_name','=',$request['chname'])->first();
            if($user)
            {
                $condition[] = ['channel_pid','=',$user->user_id];
            }
        }
        if(isset($request['status']) && $request['status'])
        {
            $arr = array_intersect([11,20,30,40,0],$request['status']);
            if($arr)
            {
                return Order::where($condition)->whereIn("status",$arr)->orderBy("order_id","DESC")->paginate($request['page_size']);
            }
        }
       return Order::where($condition)->orderBy("order_id","DESC")->paginate($request['page_size']);
    }

    /**
     * 被动请求处理
     *
     * @var array
     */
    public function payOrder($order_id)
    {
        $order = Order::where("order_sn",$order_id)->first();
        if($order->status == 11)//待支付订单才能处理
        {
            $order->status = 20;
            $order->pay_time = Carbon::now(config('app.timezone'))->timestamp;
            $order->save();
        }
    }

    /**
     * 获得可以发海报的图片
     * @param $order_id
     * @return array|mixed|void
     */
    public function getOrderQrcode($order_id,$rid=0)
    {
        $order_goods_list = OrderGood::where("order_id",$order_id)->get();
        $goods_item = [];
        $fdiy_goods = [];
        foreach ($order_goods_list as $index => $item)
        {
            if($item->type == 'fdiy')
            {
                $goods_item[] = $item->toArray();
                if($item->rec_id == $rid)//如果有特别指明的order_good表数据
                {
                    $fdiy_goods = $item->toArray();
                    break;
                }
                if($item->style)
                {
                    $fdiy_goods = $item->toArray();
                    break;
                }
            }
        }
        if(!$goods_item)
        {
            return;
        }
        if(!$fdiy_goods)
        {
            $fdiy_goods = $goods_item[0];
        }
        if(!$fdiy_goods['style']) //没有默认图 提供默认图
        {
            $fdiy_goods['style'] = env('WEB_ROOT_DIR')."/static/img/mfd_5.png";
        }
        else
        {
            $fdiy_goods['style'] = env('WEB_ROOT_DIR').$fdiy_goods['style'];
        }
        $gongiao_str = "";
        $quanqi_str = "";
        $dog_name = "";
        $fb_list = FbCategory::whereIn("cate_id",explode(",",$fdiy_goods['items']))->get();
        foreach ($fb_list as $index => $item)
        {
            if($item->parent_id == FbCategory::SHENGZHANG)
            {
                $quanqi_str = $item->cate_name;
            }
            elseif ($item->parent_id == FbCategory::GONGXIAO)
            {
                $gongiao_str .= $item->cate_name.',';
            }
            elseif (in_array($item->parent_id,FbCategory::DOGTYPE))
            {
                $dog_name = $item->cate_name;
            }
        }
        if(!$fdiy_goods['dog_name']) //昵称为空 去狗品种
        {
            $fdiy_goods['dog_name'] = $dog_name;
        }
        if(!$fdiy_goods['dog_date']) //昵称为空 去狗品种
        {
            $fdiy_goods['dog_date'] = "未知";
        }
        $gongiao_str = trim($gongiao_str,',');
        $fdiy_goods['gongxiao_str'] = $gongiao_str;
        $fdiy_goods['quanqi_str'] = $quanqi_str;
        return $fdiy_goods;
    }


    /**
     * 获取订单状态
     */
    public static function getStatic()
    {

        $return = [];
        foreach (Order::ORDER_STATUS as $index => $val)
        {
            $order_info = Order::where('status','=',$val)->count();
            $return[] = $order_info;
        }
        return $return;
    }



}
