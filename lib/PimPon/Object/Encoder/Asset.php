<?php


class PimPon_Object_Encoder_Asset implements PimPon_EncoderInterface
{
    const TYPE = 'asset';

    public static function encode ($value){
        if ($value instanceOf Asset) {
            return [[
                'class' => get_class($value),
                'type' => self::TYPE,
                'data' => $value->getFullPath()
            ]];
        }
        return null;
    }

    public static function decode ($value){
        if ($value['type'] === self::TYPE) {
            return Asset::getByPath($value['data']);
        }
        return null;
    }

}
