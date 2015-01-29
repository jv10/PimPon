<?php

class PimPon_Object_Encoder_Table implements PimPon_EncoderInterface
{

    const TYPE = 'table';

    public static function encode($value)
    {
        if (is_array($value) === true) {
            if (is_array($value[0]) === true) {
                return [[
                    'type' => self::TYPE,
                    'data' => $value
                ]];
            }
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
