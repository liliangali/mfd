<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\RequestOptions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Swagger\Annotations\Post;

class CrmCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';
    protected $sign = "";
    protected $timeout = 30.0;
    protected $client;
    protected $promises = [];
    protected $up = "up";
    protected $add = "add";
    protected $rest = "reset";
    protected $tabledir = "public/table/";//已经跑过脚本的文件
    protected $description = '';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {

        parent::__construct();
        $this->csign();
        $this->client =  new Client([
            'base_uri' => config('app.baseurl').'cf/'.$this->description."/putlist".$this->sign,
            'timeout'  => $this->timeout,
        ]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->tableFile();
        $this->sfile($this->up);
        $this->sfile($this->add);
        $results= $this->crmPost($this->promises);
        $this->delfile($results);
    }

    /**
     * 生成签名和签名验证参数
     *
     * @return mixed
     */
    public function csign()
    {
        $timestamp = Carbon::now(config('app.timezone'))->timestamp."000";
        $sign = strtolower(md5(strtolower(urlencode(strtolower($timestamp."&".config("app.appsecret"))))));
        $this->sign = "?appkey=".config("app.appkey")."&timestamp=".$timestamp."&sign=".$sign;
    }

    /**
     * 读取文件下所有文件名字
     * @return array
     */
    public function scnfile($file)
    {
        $dir = env('CRM_TABLE_DIR');
        if(!is_dir($dir.$file))
        {
            return;
        }
        $res = Storage::files($dir.$file);
        //获取某目录下所有文件、目录名（不包括子目录下文件、目录名）
        $handler = opendir($dir.$file);
        while (($filename = readdir($handler)) !== false)
        {
            if ($filename != "." && $filename != "..")
            {
                $files[] = $filename ;
            }
        }
        closedir($handler);
        return $files;
    }

    /**
     * 读取文件下所有文件名字
     * @return array
     */
    public function delfile($results)
    {
        $dir = env('CRM_TABLE_DIR');
        if(!$results)
        {
            return ;
        }
        foreach ($results as $index => $item)
        {
            $res = \GuzzleHttp\json_decode($item->getBody()->getContents(),1);
            if(isset($res) && $res['code'] == 'SUCCESS')
            {
                $file = "/".config('database.connections.mysql.prefix').$this->model->getTable()."/".$index;
                $this->delDir($dir.$file);
            }
        }
    }

    /**
     * 删除目录
     * @return array
     */
    function delDir($directory){//自定义函数递归的函数整个目录
        if(file_exists($directory)){//判断目录是否存在，如果不存在rmdir()函数会出错
            if($dir_handle=@opendir($directory)){//打开目录返回目录资源，并判断是否成功
                while($filename=readdir($dir_handle)){//遍历目录，读出目录中的文件或文件夹
                    if($filename!='.' && $filename!='..'){//一定要排除两个特殊的目录
                        $subFile=$directory."/".$filename;//将目录下的文件与当前目录相连
                        if(is_dir($subFile)){//如果是目录条件则成了
                            $this->delDir($subFile);//递归调用自己删除子目录
                        }
                        if(is_file($subFile)){//如果是文件条件则成立
                            unlink($subFile);//直接删除这个文件
                        }
                    }
                }
                closedir($dir_handle);//关闭目录资源
                rmdir($directory);//删除空目录
            }
        }
    }

    /**
     * 初始化需要post的数据
     * @return array
     */
    public function postData($option=[])
    {
        $post_option = ['jsondata' =>json_encode($option)];
        $arr =  [
            RequestOptions::FORM_PARAMS => $post_option,
            RequestOptions::DEBUG => true,
//            RequestOptions::PROXY => ['http'  => '127.0.0.1:8888']//代理
        ];
        return $arr;
    }

    /**
     * 为每个数组都拼接上
     * @return array
     */
    public function synTime(&$post=[])
    {
        if(!$post)
        {
            return [];
        }
        foreach ((array)$post as $index => $item)
        {
            $post[$index]['syn_time'] = Carbon::now(config('app.timezone'))->toDateTimeString();
        }
    }


    /**
     * 推送数据,并返回结果值
     *
     * @return mixed
     */
    public function crmPost($arr=[])
    {
//        echo '<pre>';print_r($arr);exit;
        
        if(!is_array($arr) || !$arr)
        {
            return false;
        }
        $results= \GuzzleHttp\Promise\unwrap($arr);
        return $results;
    }

    /**
     * 读取当前表数据下面的up和add下面的文件 然后拼接异步参数
     *
     * @return mixed
     */
    public function sfile($type)
    {
        $list = [];
        foreach ((array)$this->scnfile("/".config('database.connections.mysql.prefix').$this->model->getTable()."/$type") as $index => $item)
        {
            $info = $this->model->whereRaw($item)->get()->makeHidden($this->hidden)->toArray();
            $list = array_merge($list,$info);
        }
        if(!$list)
        {
            return [];
        }
        $this->synTime($list);
        $this->promises[$type] = $this->client->postAsync("",$this->postData($list));
    }



    /**
     * 把表处说实话的数据
     *
     * @return mixed
     */
    public function tableFile()
    {
        $files = Storage::files($this->tabledir);
        foreach ((array)$files as $index => $item)
        {
            $files[$index] = str_replace($this->tabledir,"",$item);
        }

        if(in_array($this->model->getTable(),$files))//数据已初始化
        {
            return;
        }
        $this->model->chunk(100, function($list) {//每次操作100个订单
            $list = $list->makeHidden($this->hidden);
            $reset['reset'] = $this->client->postAsync("",$this->postData($list->toArray()));
            $results = $this->crmPost($reset);
        });
        Storage::disk('local')->put('public/table/'.$this->model->getTable(), '');
    }

}
