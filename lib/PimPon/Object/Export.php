<?php

class PimPon_Object_Export extends PimPon_ExportBase
{

    private static $includeMethods = array('getKey', 'getFullPath', 'getPath', 'getPublished');

    public static function doExport(Object_Abstract $object)
    {
        self::$exportFile = self::getExportFilePath();
        self::openExportFile();
        self::exportObject($object);
        self::closeExportFile();
        return self::$exportFile;

    }

    private static function exportObject(Object_Abstract $object, $key = null)
    {
        self::l('******************************************');
        self::l($object);

        foreach ($object->getClass()->getFieldDefinitions() as $field) {
            $key = $field->getName();
            self::l($field);
        }

        if ($object->getId() !== self::ROOT_ID) {
            $objectData = array();
            $objectClass = get_class($object);
            $objectData ['class'] = $objectClass;
            foreach ($object->getClass()->getFieldDefinitions() as $field) {
                $property               = ucfirst($field->getName());
                $fieldtype              = $field->getFieldtype();
                $value                  = $object->{'get'.$property}();
                $objectData [$property] = PimPon_Object_Encoder::encode($value,
                        $fieldtype);
            }
            foreach (self::$includeMethods as $method) {
                $property               = ucfirst(substr($method, 3));
                $value                  = $object->{$method}();
                $objectData [$property] = PimPon_Object_Encoder_Default::encode($value);
            }

            self::writeDataOnFile($objectData);
        }
        if ($object->hasChilds() === true) {
            array_walk($object->getChilds(),
                'PimPon_Object_Export::exportObject');
        }

    }

    private static function isAvailableMethod($method, $class)
    {
        if (in_array($method->getName(), self::$includeMethods) === true) {
            return true;
        }
        if (0 !== strpos($method->getName(), "get")) {
            return false;
        }
        if ($method->getNumberOfParameters() > 0) {
            return false;
        }
        if ($method->getDeclaringClass()->name !== $class) {
            return false;
        }
        return true;

    }

}
