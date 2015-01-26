<?php


class PimPon_Object_Encoder_Table implements PimPon_EncoderInterface
{

    const TYPE = 'table';

    public static function encode ($value){
        if ($value instanceOf Object_Data_StructuredTable) {
            return [[
                'class' => get_class($value),
                'type' => self::TYPE,
                'data' => $value->getData()
            ]];
        }
        return null;
    }

    public static function decode ($value){
        if ($value['type'] === self::TYPE) {
            return new Object_Data_StructuredTable($value['data']);
        }
        return null;
    }

}
