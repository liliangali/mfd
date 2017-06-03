<?php
namespace Cyteam\Shop\Type;

class Types
{
    /**
     * 工程类，主要用来创建对象
     * 功能：根据商品类型，工厂就能实例化出合适的对象
     *
     */
    public static function createObj($type){
        switch ($type){
            case 'custom':
                return new CustomBase();
                break;
            case 'fdiy':
                return new FdiyBase();
                break;
        }
    }
}