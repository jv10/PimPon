<?php

class PimPon_Document_Export extends PimPon_ExportBase
{

    private static $excludeMethods = array('getDependencies', 'getParent', 'getChilds',
        'getTypes', 'getType', 'getId', 'getParentId', 'getProperties', 'getUserModification',
        'getUserOwner', 'getModificationDate', 'getCreationDate', 'getIndex');

    public static function doExport(Document $document)
    {
        self::$exportFile = self::getExportFilePath();
        self::openExportFile();
        self::exportDocument($document);
        self::closeExportFile();
        return self::$exportFile;

    }

    private static function exportDocument(Document $document, $key = null)
    {
        if ($document->getId() !== self::ROOT_ID) {
            $documentData           = array();
            $documentClass          = get_class($document);
            $reflectionClass        = new ReflectionClass($documentClass);
            $documentMethods        = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
            foreach ($documentMethods as $method) {
                if (self::isAvailableMethod($method, $documentClass) === false) {
                    continue;
                }
                $property                 = self::fetchProperty($method);
                $documentData [$property] = $method->invoke($document);
            }
            $documentData ['class'] = $documentClass;
            self::writeDataOnFile($documentData);
        }
        if ($document->hasChilds() === true) {
            array_walk($document->getChilds(),
                'PimPon_Document_Export::exportDocument');
        }

    }

    private static function isAvailableMethod($method, $class)
    {
        if (in_array($method->getName(), self::$excludeMethods)===true) {
            return false;
        }
        if (0 !== strpos($method->getName(), "get")) {
            return false;
        }
        if ($method->getNumberOfParameters() > 0) {
            return false;
        }
        if ($method->getDeclaringClass()->name === $class || $method->getDeclaringClass()->name
            === 'Document') {
            return true;
        }
        return false;

    }

}
