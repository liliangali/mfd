<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderGood;
use App\Models\WxConfig;
use App\User;
use Carbon\Carbon;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\Material;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class MakeImg extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qsend:img';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '合成海报体';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $application = new Application(config("wechat.open_app_config"));
        $app = $application->open_platform->createAuthorizerApplication(config("wechat.app_id"), WxConfig::getRefreshToken());
        $qrcode = $app->qrcode;
        $temporary = $app->material_temporary;
        $manager = new ImageManager(array('driver' => 'imagick'));
        $imagick = new \Imagick();
        $today = Carbon::now(config('app.timezone'))->timestamp-Order::SEND_QR_TIME;
        $condition = [
            ['status','=','20'],
            ['pay_time','>',intval($today)],
        ];
        $order_mod = new Order();
        $list = $order_mod->where($condition)->whereNull('erweima_url')->get();

        foreach ($list as $index => $item)
        {
            if(!$item->order_sn)
            {
                return;
            }
            $user_id = $item->user_id;
            $order_goods_info = $order_mod->getOrderQrcode($item->order_id);
            $user_info = User::find($item->user_id);
            if(!$order_goods_info) //没有diy商品 不做处理
            {
                $item->erweima_url = 1;
                $item->save();
                continue;
            }

            $result = $qrcode->temporary($user_id, 30 * 24 * 3600);
            $item->erweima_url = $qrcode->url($result->ticket);
            $client = new Client();
            //二维码图片本地化  人头像本地化     二维码图片resize大小 人图片resize大小
            try
            {
                Storage::disk('local')->put('tqrcode/'.$user_id.'q.png', $client->request('get',$item->erweima_url)->getBody()->getContents());
                $manager->make(storage_path('app/tqrcode/').$user_id.'q.png')->resize(130,130)->save();
//                if(!(Storage::disk('local')->exists('tqrcode/'.$user_id.'a.png')))
//                {
                    Storage::disk('local')->put('tqrcode/'.$user_id.'a.png', $client->request('get',$user_info->avatar)->getBody()->getContents());
                    $manager->make(storage_path('app/tqrcode/').$user_id.'a.png')->resize(90,90)->save();
//                }
            }
            catch (\GuzzleHttp\RequestException $e)
            {
                echo  'error 100';
                continue;
            }


            //狗图像resize大小
            $manager->make($order_goods_info['style'])->resize(290,290)->save(storage_path('app/tqrcode/').$user_id.'s.png');

            //diy图片做成圆形
            $imagick->readImage(storage_path('app/tqrcode/').$user_id.'s.png');
            $circle = new \Imagick();
            $circle->newImage(290, 290, 'none');
            $circle->setimageformat('png');
            $circle->setimagematte(true);
            $draw = new \ImagickDraw();
            $draw->setfillcolor('#ffffff');
            $draw->circle(290/2, 290/2, 290/2, 290);
            $circle->drawimage($draw);
            $imagick->setImageFormat( "png" );
            $imagick->setimagematte(true);
            $imagick->cropimage(290, 290, 0, 0);
            $imagick->compositeimage($circle, \Imagick::COMPOSITE_DSTIN, 0, 0);
            $imagick->writeImage(storage_path('app/tqrcode/').$user_id.'s.png');
            $imagick->destroy();

            //初始化空白版
            $imgblank = $manager->make(resource_path("assets/img/wx.png"));
            //合成人图像
            $imgblank->insert(storage_path('app/tqrcode/').$user_id.'a.png','top-left', 45,  480);
            //合成狗头
            $imgblank->insert(storage_path('app/tqrcode/').$user_id.'s.png','top-left', 175,  465);
            //合成二维码
            $imgblank->insert(storage_path('app/tqrcode/').$user_id.'q.png','top-left', 493,  435);
            //合成文字
            $imgblank->text($item->user_name, 90, 600, function($font) {
                $font->file(resource_path("assets/img/msyh.ttf"));
                $font->size(18);
                $font->align('center');
                $font->angle('top');
            });
            $imgblank->text('狗狗 : '.htmlspecialchars_decode($order_goods_info['dog_name']), 30, 685, function($font) {
                $font->file(resource_path("assets/img/msyh.ttf"));
                $font->size(20);
                $font->align('left');
                $font->angle('top');
            });
            $imgblank->text('犬期 : '.$order_goods_info['quanqi_str'], 30, 720, function($font) {
                $font->file(resource_path("assets/img/msyh.ttf"));
                $font->size(20);
                $font->align('left');
                $font->angle('top');
            });
            $imgblank->text('生日 : '.$order_goods_info['dog_date'], 30, 750, function($font) {
                $font->file(resource_path("assets/img/msyh.ttf"));
                $font->size(20);
                $font->align('left');
                $font->angle('top');
            });
            $imgblank->text('功效 : '.$order_goods_info['gongxiao_str'], 30, 780, function($font) {
                $font->file(resource_path("assets/img/msyh.ttf"));
                $font->size(20);
                $font->align('left');
                $font->angle('top');
            });
            $jiyu1 = htmlspecialchars_decode(mb_substr($order_goods_info['dog_desc'],0,11));
            $jiyu2 = htmlspecialchars_decode(mb_substr($order_goods_info['dog_desc'],11));
            $imgblank->text('主人寄语', 518, 720, function($font) {
                $font->file(resource_path("assets/img/msyh.ttf"));
                $font->size(19);
                $font->align('left');
                $font->angle('top');
            });
            $imgblank->text($jiyu1, 445, 750, function($font) {
                $font->file(resource_path("assets/img/msyh.ttf"));
                $font->size(17);
                $font->align('left');
                $font->angle('top');
            });
            $imgblank->text($jiyu2, 400, 777, function($font) {
                $font->file(resource_path("assets/img/msyh.ttf"));
                $font->size(17);
                $font->align('left');
                $font->angle('top');
            });
            $imgblank->save(storage_path('app/public/tqrcode/').$user_id.'_'.$item->order_id.'f.png');
            //上传素材并发送消息
            $result = $temporary->uploadImage(storage_path('app/public/tqrcode/').$user_id.'_'.$item->order_id.'f.png');
            $mediaId = $result->media_id;
            $material = new Material('image', $mediaId);
            $app->staff->message($material)->to($user_info->openid)->send();
            $item->erweima_url =  asset('storage/public/tqrcode/'.$user_id.'_'.$item->order_id.'f.png');
            $item->save();


        }

    }
}
