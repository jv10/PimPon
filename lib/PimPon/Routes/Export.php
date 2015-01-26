<?php

class PimPon_Routes_Export extends PimPon_ExportBase
{

    const ROUTES_CLASS = 'Staticroute';

    private static $excludeMethods = array('getId','getCurrentRoute','getModificationDate','getCreationDate','getSiteId');

    public static function doExport()
    {
        self::$exportFile = self::getExportFilePath();

        $routeList   = new Staticroute_List();
        $routeList->load();
        $routesCollection = $routeList->getRoutes();

        self::openExportFile();
        array_walk($routesCollection, 'PimPon_Routes_Export::exportRoute');
        self::closeExportFile();

        return self::$exportFile;

    }

    private static function exportRoute(Staticroute $route, $key)
    {
        $routeData       = array();
        $reflectionClass = new ReflectionClass(self::ROUTES_CLASS);
        $routesMethods   = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($routesMethods as $method) {
            if (self::isAvailableMethod($method) === false) {
                continue;
            }
            $property              = self::fetchProperty($method);
            $routeData [$property] = $method->invoke($route);
        }
        self::writeDataOnFile($routeData);

    }

    private static function isAvailableMethod($method)
    {
        if (in_array($method->getName(), self::$excludeMethods)) {
            return false;
        }
        if (0 !== strpos($method->getName(), "get")) {
            return false;
        }
        if ($method->getNumberOfParameters() > 0) {
            return false;
        }
        if ($method->getDeclaringClass()->name !== self::ROUTES_CLASS) {
            return false;
        }
        return true;

    }

}
