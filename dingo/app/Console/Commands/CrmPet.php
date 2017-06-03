<?php

namespace App\Console\Commands;
use App\Models\Pet;
use Illuminate\Support\Facades\Storage;

class CrmPet extends CrmCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:pet';
    /**
     * 用于发送CRM请求是拼接的参数,并非表明,请对应修改
     *
     * @var string
     */
    protected $description = 'pet';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new Pet();
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
     * 读取当前表数据下面的up和add下面的文件 然后拼接异步参数
     *
     * @return mixed
     */
    public function sfile($type)
    {
        $list = [];
        foreach ((array)$this->scnfile("/".config('database.connections.mysql.prefix').$this->model->getTable()."/$type") as $index => $item)
        {
            $info = $this->model->whereRaw($item)->get()->toArray();
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
            $reset['reset'] = $this->client->postAsync("",$this->postData($list->toArray()));
            $results = $this->crmPost($reset);
        });
        Storage::disk('local')->put('public/table/'.$this->model->getTable(), '');
    }
}
