<?php

namespace App\Api\V1\Transformers;

use App\Models\Order;
use App\User;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract {

    protected $availableIncludes = ['comm'];
    public function transform(Order $order) {
        $order->add_time = date("Y-m-d H:i:s",$order->add_time);
        if($order->channel_pid) //有上线
        {
            $user = User::getById($order->channel_pid);
            $order->channel_info = $user->toArray();
        }
        return $order->toArray();
    }

}


