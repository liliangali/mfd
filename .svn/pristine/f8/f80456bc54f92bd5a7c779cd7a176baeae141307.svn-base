<?php

namespace App\Console\Commands;
use App\Models\BaseMaterial;
use App\Models\FbCategory;
use App\Models\Good;
use App\Models\Material;
use App\Models\Order;
use App\Models\OrderGood;
use App\Models\Pet;
use App\Models\PetType;
use App\Models\Voucher;
use App\Models\VoucherBatch;
use App\User;
class CrmUser extends CrmCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:user';
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $table_list = [
          'customer'  => ['model'=>new User(),'hidden'=>[
              'password', 'pay_ps','invite','pay_password','im_qq','im_msn','im_skype','im_yahoo','im_aliww','last_ip','portrait','df_bankcard','user_token','avatar','coupon',
              'parent_id','erweimaUrl','erweima_url','g_id','jiaose','status','channel','member_type','channel_code','channel_pid',
          ]],
          'order' => ['model'=>new Order(),'hidden'=>[
            'c_number','r_order_id','r_status','is_gift','is_back_money','kh_order_amount','kh_ship_pay',
            'source','source','source_id','store_id','one_id','one_status','kf_id','kf_name','jp_id','erweimaUrl','erweima_url',
            'kh_payment_name','kh_payment_name','kh_sex','shipping','invoice_need','invoice_type','invoice_title','invoice_com',
            'kh_payment_code'
            ]],
            'basematerial' => ['model'=>new BaseMaterial(),'hidden'=>[]],
            'fbcategory' => ['model'=>new FbCategory(),'hidden'=>[]],
            'product' => ['model'=>new Good(),'hidden'=>['brand_id','thumbnail_pic','big_pic','s_img']],
            'material' => ['model'=>new Material(),'hidden'=>[]],
            'orderitem' => ['model'=>new Pet(),'hidden'=>[]],
            'pet' => ['model'=>new OrderGood(),'hidden'=>[]],
            'pettype' => ['model'=>new PetType(),'hidden'=>[]],
            'voucher' => ['model'=>new Voucher(),'hidden'=>[]],
            'voucherbatch' => ['model'=>new VoucherBatch(),'hidden'=>[]],
        ];
        foreach ($table_list as $index => $item)
        {
            $this->description = $index;
            $this->model = $item['model'];
            $this->hidden = $item['hidden'];
            parent::__construct();
            parent::handle();
        }

    }



}
