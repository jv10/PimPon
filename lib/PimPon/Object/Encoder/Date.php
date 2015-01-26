<?php


class PimPon_Object_Encoder_Date implements PimPon_EncoderInterface
{

    const TYPE = 'date';

    public static function encode ($value){
        if ($value instanceOf Zend_Date) {
            return [[
                'class' => get_class($value),
                'type' => self::TYPE,
                'data' => $value->getTimestamp()
            ]];
        }
        return null;
    }

    public static function decode ($value){
        if ($value['type'] === self::TYPE) {
            return new Zend_Date($value['data'], Zend_Date::TIMESTAMP);
        }
        return null;
    }

}
