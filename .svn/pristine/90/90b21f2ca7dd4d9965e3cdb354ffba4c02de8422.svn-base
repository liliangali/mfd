<?php

namespace App\Api\V1\Transformers;

use App\Models\Order;
use League\Fractal\TransformerAbstract;
use App\User;

class UserTransformer extends TransformerAbstract {

    protected $availableIncludes = ['comm'];
    public function transform(User $user) {
        $user = $user->toArray();
        return [
            'user_id' => $user['user_id'],
            'user_name' => $user['user_name'],
            'openid' => $user['openid'],
            'member_type' => $user['member_type'],
            'email' => $user['email'],
            'is_service' => $user['is_service'],
            'sex' => $user['gender'],
            'last_login' => !empty($user['last_login']) ? date("Y-m-d",$user['last_login']) : '未知',
            'reg_time' => !empty($user['reg_time']) ? date("Y-m-d",$user['reg_time']) : '未知',//chinfo表
            'avatar' => $user['avatar'],
            'order_num' => $user['order_num'],
            'final_amount_num' => $user['final_amount_num'],
            'money' => $user['money'],
            'channel' => $user['channel'],
            'erweimaUrl' => $user['erweimaUrl'],
            'memo' => empty($user['memo']) ? '': \GuzzleHttp\json_decode($user['memo'],true),

            'name' => isset($user['channel_info']['name']) ? $user['channel_info']['name'] : '',
            'region_name' => isset($user['channel_info']['region_name']) ? $user['channel_info']['region_name'] : '',
            'address' => isset($user['channel_info']['address']) ? $user['channel_info']['address'] : '',
            'region' => (isset($user['channel_info']['region']) && !empty($user['channel_info']['region'])) ? explode(',',$user['channel_info']['region']) :'',
            'jigou' => isset($user['channel_info']['jigou']) ? $user['channel_info']['jigou'] : '',
            'image_url' => isset($user['channel_info']['image_url']) ? $user['channel_info']['image_url'] : '',
            'created_at' => isset($user['channel_info']['created_at']) ? $user['channel_info']['created_at'] : '',
        ];
    }
    
}


