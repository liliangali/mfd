<?php

namespace App\Console\Commands;
use App\Models\Order;
use App\Models\PaymentBill;
use App\User;
use Carbon\Carbon;
use EasyWeChat\Foundation\Application;

class PayOrder extends CrmCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pay:order';
    /**
     * 用于发送CRM请求是拼接的参数,并非表明,请对应修改
     *
     * @var string
     */
    protected $description = '定时扫描当天的支付订单,防止出现漏单的情况';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new Order();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $options = [
            // 前面的appid什么的也得保留哦
            'app_id' => config("wechat.app_id"),
            // payment
            'payment' => [
                'merchant_id'        => config("wechat.payment.merchant_id"),
                'key'                => config("wechat.payment.key"),
                // 'device_info'     => '013467007045764',
                // 'sub_app_id'      => '',
                // 'sub_merchant_id' => '',
                // ...
            ],
        ];
        $app = new Application($options);
        $payment = $app->payment;
        $today = Carbon::now(config('app.timezone'))->timestamp-90;//当日0点时间戳
        $condition = [
            ['status','!=','TRADE_SUCCESS'],
            ['start_time','>',intval($today)],
        ];
        $payment_bill_mod = new PaymentBill();
        $payment_list = $payment_bill_mod->where($condition)->get();
//        $info = User::where("openid",'oywp_wc-be-7CeEbIY1PsXIusktw1')->first();
//        if(!$info)
//        {
//            echo '<pre>';print_r(111);exit;
//        }
//        echo '<pre>';print_r(222);exit;
//        echo '<pre>';var_dump($info);exit;
        
//        $out = 14937722771046;
//        $res = $payment->query($out);
//        echo '<pre>';print_r($res);exit;
        foreach ($payment_list as $index => $item)
        {
            $res = $payment->query($item->payment_sn);
            if($res->return_code=='SUCCESS' && $res->trade_state == 'SUCCESS')
            {
                $this->model->payOrder($item->order_sn);
            }
        }
        $this->comment("End");

    }


}
