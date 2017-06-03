<?php
/**
 * Cotte licence
 *
 * @copyright  Copyright (c) 2007-2016 cotte.cn Inc. (http://www.cotte.cn)
 * @license  http://license.cotte.cn/ cotte License
 */
namespace Cyteam\Goods;

class Descartes
{
    public $sourceArray;
    public $resultArray;

    public function __construct($array, $result)
    {
        $this->sourceArray = $array;
        $this->resultArray = $result;
    }

    public function calcDescartes($arrIndex, $arrResult)
    {
        if ($arrIndex >= count($this->sourceArray)) {
            array_push($this->resultArray, $arrResult);
            return ;
        }

        $currentArray = $this->sourceArray[$arrIndex];
        $currentArrayCount = count($currentArray);
        $arrResultCount = count($arrResult);

        for ($i = 0; $i < $currentArrayCount; ++$i) {
            $currentArraySlice = array_slice($arrResult, 0, $arrResultCount);
            array_push($currentArraySlice, $currentArray[$i]);
            $this->calcDescartes($arrIndex + 1, $currentArraySlice);
        }
    }

}

