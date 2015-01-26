<?php

class PimPon_Object_Encoder implements PimPon_EncoderInterface
{

    private static $encoders = array(
        "PimPon_Object_Encoder_Asset",
        //PimPon_Object_Encoder_Collection,
        "PimPon_Object_Encoder_Date",
        //PimPon_Object_Encoder_Href,
        "PimPon_Object_Encoder_Table"
    );

    public static function encode($value)
    {
        Logger::emerg($value);
        foreach (self::$encoders as $encoder) {
            $encodedValue = $encoder::encode($value);
            if (is_null($encodedValue) === false) {
                return $encodedValue;
            }
        }
        return self::defaultEncode($value);

    }

    public static function decode($value)
    {
        foreach (self::$encoders as $encoder) {
            $decodedValue = $encoder::decode($value);
            if (is_null($decodedValue) === false) {
                return $decodedValue;
            }
        }
        return self::defaultDecode($value);

    }

    private static function defaultEncode($value)
    {
        return $value;

    }

    private static function defaultDecode($value)
    {
        return $value;

    }

}
