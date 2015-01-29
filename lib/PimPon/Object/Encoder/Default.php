<?php

class PimPon_Object_Encoder_Default implements PimPon_EncoderInterface
{

    const TYPE = 'default';

    public static function encode($value)
    {
        return [[
                'type' => self::TYPE,
                'data' => $value
            ]];
    }

    public static function decode($value)
    {
        if ($value['type'] === self::TYPE) {
            return $value['data'];
        }
        return null;
    }

}
