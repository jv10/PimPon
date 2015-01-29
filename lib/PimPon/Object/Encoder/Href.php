<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Href
 *
 * @author javierdiez
 */
class PimPon_Object_Encoder_Href implements PimPon_EncoderInterface
{

    const TYPE = 'href';

    public static function encode($value)
    {
        if ($value instanceOf Object_Abstract || $value instanceOf Document || $value instanceOf Asset) {
            return [[
                'class' => get_class($value),
                'type' => self::TYPE,
                'data' => $value->getFullPath()
            ]];
        }
        return null;
    }

    public static function decode($value)
    {
        if ($value['type'] === self::TYPE) {
            return $value['data'];
        }
        return null;
    }

}
