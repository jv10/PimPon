<?php

class PimPon_Object_Encoder_Collection implements PimPon_EncoderInterface
{

    const TYPE = 'collection';

    public static function encode($value)
    {
        $collection = null;
        if (is_array($value) === true) {
            foreach ($value as $object) {
                $collection [] = [
                    'class' => get_class($object),
                    'type' => 'collection',
                    'data' => $object->getFullPath()
                ];
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
