<?php
/**
 * Created by PhpStorm.
 * User: tandi
 * Date: 17/4/19
 * Time: 下午3:06
 */
namespace App\MD5;
use Illuminate\Contracts\Hashing\Hasher;
class MD5Hasher implements Hasher
{

    /**
     * Hash the given value.
     *
     * @param  string  $value
     * @return array   $options
     * @return string
     */
    public function make($value, array $options = []) {
        return md5($value);//你可以在这里实现你想要的加密方式
    }

    /**
     * Check the given plain value against a hash.
     *
     * @param  string  $value
     * @param  string  $hashedValue
     * @param  array   $options
     * @return bool
     */
    public function check($value, $hashedValue, array $options = []) {
        return $this->make($value) === $hashedValue;
    }

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @param  string  $hashedValue
     * @param  array   $options
     * @return bool
     */
    public function needsRehash($hashedValue, array $options = []) {
        return false;
    }

}