<?php

namespace App;

use App\Models\Cash;
use App\Models\ChDiscount;
use App\Models\Order;
use Carbon\Carbon;
use Dingo\Api\Routing\Helpers;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\ChannelInfo;
use App\Models\Region;
use App\Models\Voucher;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;

class User extends Authenticatable
{
    public $table = 'member';
    public $primaryKey = "user_id";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_name', 'nickname', 'password',
    ];
    public  $timestamps = false;//去掉update_time等三个字段
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * 部分隐藏字段
     */
    public static function hidenList()
    {
        return [
          'default'=>['password','pay_password','real_name','openid','last_ip','channel_pid'],
        ];
    }



    //和cf_channel_infos一对一关系
    public function channelInfo()
    {
        return $this->hasOne('App\Models\ChannelInfo', 'user_id', 'user_id');
    }

    public function cashs()
    {
        return $this->hasMany('App\Models\Cash','user_id','user_id');
    }

    /*
    * 注册函数
    * @pram email
    */
    public static function register($data)
    {
        $pass = $data['pass'];
        $newUser = [
            'user_name' => $data['user_name'],
            'email' => $data['email'],
            'phone_mob' => $data['user_name'],
            'password' => bcrypt($pass),
            'member_type' => 2,
        ];
        $user = User::create($newUser);
        if(!$user)
        {
            return false;
        }
//        User::makeQrcode($user);//生成二维码
        $user->channel_code = $user->user_id;
        $user->save();
        $channel = new ChannelInfo();
        $channel->user_id = $user->user_id;
        $channel->jigou = $data['zuzhi'];
        $channel->name =  $data['shangpu'];
        $channel->region = implode(',',$data['option']);
        $channel->address = $data['address'];
        $channel->image_url = $data['imageUrl'];
        //格式化地区
        $region_list = Region::whereIn('region_id',$data['option'])->get()->toArray();
        $channel->region_name = $region_list[0]['region_name'].','.$region_list[1]['region_name'];
        $channel->save();
        return true;
    }
    
    public static function makeQrcode($user)
    {
        $client = new Client();
        $user_id_b =  bcrypt($user->user_id);
        if(!$user->channel)
        {
            $image_name = $user_id_b.'.png';
            try
            {
                Storage::disk('local')->put('public/qrcode'.$image_name, $client->request('get',Helper::qrcode($user->user_id))->getBody()->getContents());
            }
            catch (\GuzzleHttp\RequestException $e)
            {

            }
            $user->channel = asset('storage/public/qrcode/'.$image_name);
        }
        if(!$user->erweimaUrl)
        {
            $user->erweimaUrl = Helper::qrcode('bangding_'.$user->user_id);
        }

        $user->save();
    }
    
    public static function bangCha($user,$ch_id)
    {
        if(!$user->openid || !$ch_id)
        {
            return ;
        }
        $ch_info = User::getById($ch_id);
        if(!$ch_id)
        {
            return;
        }
        $data['openid'] = $user->openid;
        $data['headimgurl'] = $user->headimgurl;
        $data['nickname'] = $user->nickname;
        $ch_info->memo = \GuzzleHttp\json_encode($data);
        $ch_info->save();
    }


    /*
    * 修改用户
    * @pram email
    */
    public static function saveUser($req)
    {
        $user = User::getById($req->user_id);
        if(!$user)
        {
            return false;
        }
        $user->user_name = isset($req->user_name) ? $req->user_name : $user->user_name;
        $user->email = isset($req->email) ? $req->email : $user->email;
        $user->password = (isset($req->password) && !empty($req->password)) ? md5($req->password) : $user->password;
        $user->is_service = isset($req->is_service) ? $req->is_service : $user->is_service;
        $user->avatar = isset($req->avatar) ? $req->avatar : $user->avatar;
        if($user->member_type == 2) //公司渠道人员
        {
           $channel_infos = ChannelInfo::where('user_id','=',$user->user_id)->first();
           $channel_infos->save();
            if($channel_infos)
            {
                $channel_infos->name = isset($req->name) ? $req->name : $channel_infos->name;
                $channel_infos->jigou = isset($req->jigou) ? $req->jigou : $channel_infos->jigou;
                $channel_infos->region = isset($req->region) ? implode(',',$req->region) : $channel_infos->region;
                $region_list = Region::whereIn('region_id',$req->region)->get()->toArray();
                $channel_infos->region_name = isset($req->region) ? $region_list[0]['region_name'].','.$region_list[1]['region_name']:$channel_infos->region_name;
                $channel_infos->address = isset($req->address) ? $req->address : $channel_infos->address;
                $channel_infos->image_url = isset($req->image_url) ? $req->image_url : $channel_infos->image_url;

                $channel_infos->save();
            }

        }
        else
        {
            $user->member_type = isset($req->member_type) ? $req->member_type : $user->member_type;
        }
        $user->save();
        return true;

    }
        
    /*
   * 查询用户信息
   * @pram email
   */
    protected static function  getAll(Request $request) {
        $condition = [];
        if(isset($request->member_type)) //渠道会员
        {
            $condition[] = ['member_type','>',0];
            if(isset($request->is_service))
            {
                if($request->is_service == 1) //未审核
                {
                    $condition[] = ['is_service','=',0];
                }
                elseif ($request->is_service == 2)//已审核
                {
                    $condition[] = ['is_service','=',1];
                }
            }
            $chmodel = new ChannelInfo();
            $chid = "user_id";
        }
        else//普通会员(包括渠道会员)
        {
            $chmodel = new User();
            $chid = "channel_pid";
        }
        if($request->user_name)
        {
            $condition[] = ['user_name','like',$request->user_name.'%'];
        }
        if($request->email)
        {
            $condition[] = ['email','like',$request->email.'%'];
        }
        if($request->chname)
        {
            $chinfo = User::where("user_name",'=',$request->chname)->first();
            if($chinfo)
            {
                $condition[] = ['channel_pid','=',$chinfo->user_id];
            }
        }

        $user = User::where($condition)->orderBy('member.user_id', 'desc')->paginate($request->page_size);
        $user = $user->toArray();

        $chid_arr = array_unique(Helper::i_array_column($user['data'],$chid));
        $chl_list = [];
        if($chid_arr)
        {
            $chl_list = $chmodel->whereIn('user_id', $chid_arr)->get()->toArray();
            $chl_list = Helper::v_array_column($chl_list,'user_id');
        }
        foreach ((array)$user['data'] as $index => $item)
        {
            $user['data'][$index]['channel_info'] = [];
            if($item[$chid] && isset($chl_list[$item[$chid]]))
            {
                $user['data'][$index]['channel_info'] = $chl_list[$item[$chid]];
            }
        }

        return $user;
    }


     /*
     * 根据event获取渠道二维码的相关信息
     * @pram email
     */
    protected static function  findUserKey($code) {
       $user = User::where('channel_code', $code)->first();
       if(!$user)
       {
           return false;
       }
    }

    /*
    * 根据event添加渠道信息
    * @pram
    */
    public static function qrcode($eventKey,$user)
    {
        $user_mod = new User();
        $openid = $user->openid;
        $m_info =$user_mod->where("openid",$openid)->first();
        if(!$m_info) //只认初次关注的会员
        {
            $channel_info = $user_mod->where("channel_code",$eventKey)->first();
            if($channel_info && $channel_info->user_id)//二级渠道人员
            {
                $user_mod->channel_pid = $channel_info->user_id;
            }
            else//临时渠道人员
            {
                $user_mod->channel_pid = $eventKey;
            }
            $user_mod->openid = $openid;
            $user_mod->reg_time = time();
            $nicname = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $user->nickname);
            if (!get_magic_quotes_gpc())
            {
                $nicname = addslashes($nicname);
            }
            if(!$nicname)
            {
                $nicname = "**";
            }
            $user_mod->nickname = $nicname;
            $user_mod->user_name = $nicname;
            $user_mod->avatar = $user->headimgurl;
            $user_mod->save();
            Voucher::regDebit($user_mod->user_id,$user_mod->nickname);
        }
    }
    
    public static  function dogVoucher($user)
    {
        $user_mod = new User();
        $openid = $user->openid;
        $m_info = $user_mod->where("openid",$openid)->first();
        if(!$m_info)
        {
            return false;
        }
        return Voucher::dogDebit($m_info->user_id,$m_info->nickname);
    }

    /*
    * 根据event取消渠道信息
    * @pram email
    */
    public static function uqrcode($user)
    {
        $user_mod = new User();
        $openid = $user->openid;
        $m_info =$user_mod->where("openid",$openid)->first();
        if($m_info)
        {
            $m_info->channel_pid = 0;
            $m_info->save();
        }
    }

    /*
     * 通过公司和姓名判断用户是否存在
     * @param compnay, name
     */

    protected static function findUserName($company, $name) {
        return User::where('company', $company)->where('name', $name)->first();
    }

    /*
     * 获取公司成员信息
     * @param compnay
     */

    protected static function companyMember($company, $search) {
        if ($search === 'null') {
            return User::leftJoin('department_member', 'department_member.user', '=', 'id')->leftjoin('departments', 'department', '=', 'department_id')->where('users.company', $company)->get();

        } else {
            $emailMode = "/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/";
            if (is_numeric($search)) {
                return User::leftJoin('department_member', 'department_member.user', '=', 'id')->leftjoin('departments', 'department', '=', 'department_id')->where('users.company', $company)->where('phone', 'like', '%'.$search.'%')->get();

            } else if (preg_match($emailMode, $search)) {
                return User::leftJoin('department_member', 'department_member.user', '=', 'id')->leftjoin('departments', 'department', '=', 'department_id')->where('users.company', $company)->where('email', 'like', '%'.$search.'%')->get();

            } else {
                return User::leftJoin('department_member', 'department_member.user', '=', 'id')->leftjoin('departments', 'department', '=', 'department_id')->where('users.company', $company)->where('name', 'like', '%'.$search.'%')->orWhere('department_name', 'like', '%'.$search.'%')->orWhere('role', 'like', '%'.$search.'%')->get();

            }
        }
    }

    /*
     * 获取用户及用户组
     * @param compnay
     */

    protected static function getMemberGroup($company) {
        return User::join('user_group', 'user_group', '=', 'group_id')->where('users.company', $company)->get();
    }
    /*
     * 获取单个用户及用户组信息
     * @param uid
     */

    protected static function getUserGroup($uid) {
        return User::join('user_group', 'user_group', '=', 'group_id')->where('id', $uid)->first();
    }

    /*
     * 更新用户组
     * @param uid, group_id
     */
    protected static function changeUserGroup($uid, $group_id) {
        return User::where('id', $uid)->update(array("user_group"=>$group_id));
    }



    /*
    *根据id获取用户信息
    * @author cck
    * create_time 2016-08-08
    */
    public static function getById($id){
        return User::where('user_id',$id)->first();
    }

    /*
     *根据user_name获取用户信息
     * @author cck
     * create_time 2016-08-08
     */
    public static function getByName($name){
        return User::where('user_name',$name)->first();
    }

    /*
    *根据id获取基本信息
    * @author cck
    * create_time 2016-08-08
    */
    public static function getBasic($user,$time = '')
    {
        $ltime = 0;
        if($time == 'today')
        {
            $ltime =  Carbon::today(config('app.timezone'))->timestamp;
        }
        elseif ($time == 'week')
        {
            $date = date('Y-m-d');  //当前日期
            $first=1; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
            $w=date('w',strtotime($date));  //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
            $now_start=date('Y-m-d',strtotime("$date -".($w ? $w - $first : 6).' days')); //获取本周开始日期，如果$w是0，则表示周日，减去 6 天
            $ltime = strtotime($now_start);
        }
        elseif ($time == 'month')
        {
            $ltime = strtotime( date('Y-m-01', strtotime(date("Y-m-d"))));
        }
        elseif ($time == 'year')
        {
            $ltime = strtotime( date('Y-01-01', strtotime(date("Y-m-d"))));
        }
        $discount = ChDiscount::getDis();
        $return = [];
        $return['user_num'] = User::where("reg_time",'>',$ltime)->where('channel_pid',$user->user_id)->count();//注册人数
        $return['order_count_sum'] =  Order::where("add_time",'>',$ltime)->where('channel_pid',$user->user_id)->whereIn("status",[20,30,40])->count();//下单次数
        $return['order_money_sum'] =  Order::where("add_time",'>',$ltime)->where('channel_pid',$user->user_id)->whereIn("status",[20,30,40])->sum("final_amount");//下单金额
        $return['discount_money_sum'] = $return['order_money_sum']*($discount['discount']/100);
        $return['total_money'] = $user->money;
        $return['tixian_money']   = Cash::where('user_id',$user->user_id)->where("status",3)->sum("cash_money");//下单金额;
        $return['order_money_by'] = User::where('channel_pid',$user->user_id)->orderBy('final_amount_num','DESC')->take(5)->get()->toArray();
        $return['order_count_by'] = User::where('channel_pid',$user->user_id)->orderBy('order_num','DESC')->take(5)->get()->toArray();
        return $return;
    }

    public static function getAdminBasic()
    {
        $today = Carbon::today()->toDateTimeString();
        $ltime =  Carbon::today(config('app.timezone'))->timestamp;
        $return['user_num'] = User::where("reg_time",'>',$ltime)->count();//注册人数
        $return['check_user_num'] = User::where("reg_time",'>',$ltime)->where('member_type','>',0)->where('is_service',0)->count();//注册人数
        $return['order_count'] = Order::where("add_time",'>',$ltime)->whereIn("status",[20,30,40])->count();
        $return['order_num'] = !empty(Order::where("add_time",'>',$ltime)->whereIn("status",[20,30,40])->sum("final_amount")) ? Order::where("add_time",'>',$ltime)->whereIn("status",[20,30,40])->sum("final_amount") : 0;
        $return['cash_num'] = Cash::where('created_at','>',$today)->whereIn("status",[3])->sum("cash_money");
        $return['total_user_num'] = User::count();//注册人数
        $return['order_user_num'] = User::where('order_num','>',0)->count();
        $return['total_order_num'] = Order::whereIn("status",[20,30,40])->sum("final_amount");
        $return['total_cash_num'] = Cash::whereIn("status",[3])->sum("cash_money");
        return $return;
    }

}
