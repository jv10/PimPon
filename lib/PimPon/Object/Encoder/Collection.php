<?php

class PimPon_Object_Encoder_Collection implements PimPon_EncoderInterface
{

    const TYPE = 'objects';

    public static function encode($value)
    {
        $collection = null;
        if (is_array($value) === true) {
            foreach ($value as $object) {
                if (is_object($object) === true) {
                    $collection [] = [
                        'class' => get_class($object),
                        'type' => self::TYPE,
                        'data' => $object->getFullPath()
                    ];
                }
            }
        }
        return $collection;

    }

    public static function decode($value)
    {
        if ($value['type'] === self::TYPE) {
            return $value['data'];
        }
        return null;

    }

}
