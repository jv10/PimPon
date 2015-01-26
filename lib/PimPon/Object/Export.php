<?php

class PimPon_Object_Export extends PimPon_ExportBase
{

    private static $includeMethods = array('getKey', 'getFullPath', 'getPath','getPublished');

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
        if ($object->getId() !== self::ROOT_ID) {
            $objectData           = array();
            $objectClass          = get_class($object);
            $reflectionClass      = new ReflectionClass($objectClass);
            $objectMethods        = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
            foreach ($objectMethods as $method) {
                if (self::isAvailableMethod($method, $objectClass) === false) {
                    continue;
                }
                $property               = self::fetchProperty($method);
                $value                  = $method->invoke($object);
                //$objectData [$property] = self::parseValue($value);
                $objectData [$property] = PimPon_Object_Encoder::encode($value);
            }
            $objectData ['class'] = $objectClass;
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

    private static function parseValue($value)
    {
        $parsedValue = null;
        if ($value instanceOf Object_Abstract) {
            $parsedValue [] = [
                'class' => get_class($value),
                'type' => 'href',
                'data' => $value->getFullPath()
            ];
        } else if ($value instanceOf Zend_Date) {
            $parsedValue [] = [
                'class' => get_class($value),
                'type' => 'date',
                'data' => $value->getTimestamp()
            ];
        } else if ($value instanceOf Object_Data_StructuredTable) {
            $parsedValue [] = [
                'class' => get_class($value),
                'type' => 'table',
                'data' => $value->getData()
            ];
        } else if ($value instanceOf Asset) {
            $parsedValue [] = [
                'class' => get_class($value),
                'type' => 'asset',
                'data' => $value->getFullPath()
            ];
        } else if (is_array($value) === true) {
            foreach ($value as $object) {
                $parsedValue [] = [
                    'class' => get_class($object),
                    'type' => 'collection',
                    'data' => $object->getFullPath()
                ];
            }
        } else {
            $parsedValue = $value;
        }
        return $parsedValue;
    }

}
