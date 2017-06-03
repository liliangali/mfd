<?php

namespace App\Api\V1\Transformers;

use App\Models\Region;
use League\Fractal\TransformerAbstract;

class RegionTransformer extends TransformerAbstract {

    public function transform($region) {
        return (array)$region;
    }
}


