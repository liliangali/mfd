<?php

namespace App\Http\Middleware;
use App\Models\ApiSign as ApiS;
use Carbon\Carbon;
use Closure;

class ApiSign
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $all = $request->all();
        $stype = isset($all['stype']) ? $all['stype'] : 0;
        if($stype) //是否签名验证  生产环境之后  屏蔽此代码
        {
            return $next($request);
        }
        unset($all['stype']);
        if(!isset($all['timestamp']) || !isset($all['appid']) || !isset($all['sign']))//缺少比填参数
        {
            $response = array(
                'error' => '缺少必填签名参数',
                'status' => 401
            );
            return response()->json($response);
        }
        $time = Carbon::now(config('app.timezone'))->timestamp;
        if ($time - $all['timestamp'] > 300)  //同一签名调用时间限制
        {
            $response = array(
                'error' => '超出时间限制',
                'status' => 403
            );
            return response()->json($response);
        }
        $row = ApiS::where('appid',$all['appid'])->first();
        if(!$row)
        {
            $response = array(
                'error' => 'appid error',
                'status' => 401
            );
            return response()->json($response);
        }

        
        $appsecret = $row->appsecret;
        $sign = strtolower(md5(strtolower(urlencode(strtolower($all['timestamp']."&".$appsecret)))));
        if($sign != $all['sign'])
        {
            $response = array(
                'error' => '签名错误',
                'status' => 402
            );
            return response()->json($response);
        }
        return $next($request);
    }
}
