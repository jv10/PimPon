<?php

class PimPon_Object_Encoder
{

    private static $currentEncoderType = null;

    private static function getEncoderClass($fieldtype)
    {
        $encoderClass = '';
        switch ($fieldtype) {
            case PimPon_Object_Encoder_Image::TYPE:
                $encoderClass = 'PimPon_Object_Encoder_Image';
                break;
            case PimPon_Object_Encoder_Collection::TYPE:
                $encoderClass = 'PimPon_Object_Encoder_Collection';
                break;
            case PimPon_Object_Encoder_Date::TYPE:
                $encoderClass = 'PimPon_Object_Encoder_Date';
                break;
            case PimPon_Object_Encoder_Href::TYPE:
                $encoderClass = 'PimPon_Object_Encoder_Href';
                break;
            case PimPon_Object_Encoder_Table::TYPE:
                $encoderClass = 'PimPon_Object_Encoder_Table';
                break;
            case PimPon_Object_Encoder_Structuredtable::TYPE:
                $encoderClass = 'PimPon_Object_Encoder_Structuredtable';
                break;
            default:
                $encoderClass = 'PimPon_Object_Encoder_Default';
        }
        return $encoderClass;

    }

    public static function encode($value, $fieldtype)
    {
        $encoderClass = self::getEncoderClass($fieldtype);
        self::setCurrentEncoderType($encoderClass::TYPE);
        return $encoderClass::encode($value);

    }

    public static function decode($value)
    {
        $encoderClass = self::getEncoderClass($value['type']);
        self::setCurrentEncoderType($encoderClass::TYPE);
        return $encoderClass::decode($value);
    }

    public static function getCurrentEncoderType()
    {
        return self::$currentEncoderType;

    }

    private static function setCurrentEncoderType($type)
    {
        self::$currentEncoderType = $type;

    }

}
