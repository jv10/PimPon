<?php

class PimPon_Role_Export extends PimPon_ExportBase
{

    const SYSTEM = 'system';

    private static $roleClasses     = array('User_UserRole','User_Role', 'User_Abstract');
    private static $excludeMethods = array('');

    public static function doExport(array $roleCollection)
    {
        self::$exportFile = self::getExportFilePath();
        self::openExportFile();
        array_walk($roleCollection, 'PimPon_Role_Export::exportRole');
        self::closeExportFile();
        return self::$exportFile;

    }

    private static function exportRole($role, $key = null)
    {
        $roleClass = get_class($role);
        $roleData        = array();
        $reflectionClass = new ReflectionClass($roleClass);
        $roleMethods     = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($roleMethods as $method) {
            if (self::isAvailableMethod($method) === false) {
                continue;
            }
            $property             = self::fetchProperty($method);
            $roleData [$property] = $method->invoke($role);
        }

        self::writeDataOnFile($roleData);

        if (self::hasChilds($role) === true) {
            array_walk(self::getChilds($role->getId()), 'PimPon_Role_Export::exportRole');
        }

    }

    private static function isAvailableMethod($method)
    {
        if (in_array($method->getName(), self::$excludeMethods) === true) {
            return false;
        }
        if (0 !== strpos($method->getName(), "get")) {
            return false;
        }
        if ($method->getNumberOfParameters() > 0) {
            return false;
        }
        if (in_array($method->getDeclaringClass()->name, self::$roleClasses) === true) {
            return true;
        }
        return false;

    }

    private static function hasChilds($role)
    {
        if (method_exists($role, 'hasChilds') === false) {
            return false;
        }
        return $role->hasChilds();

    }

    private static function getChilds($parentId)
    {
        $list = new User_Role_List();
        $list->setCondition("parentId = ?", $parentId);
        $list->load();
        return $list->getRoles();
    }

}
