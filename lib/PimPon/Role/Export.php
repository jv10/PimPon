<?php

class PimPon_Role_Export extends PimPon_ExportBase
{

    const SYSTEM = 'system';

    private static $roleClasses     = array('User_UserRole','User_Role', 'User_Abstract');
    private static $excludeMethods = array('');

    public static function doExport(User_Abstract $role)
    {
        self::$exportFile = self::getExportFilePath();
        self::openExportFile();
        self::exportRole($role);
        self::closeExportFile();
        return self::$exportFile;

    }

    private static function exportRole(User_Abstract $role, $key = null)
    {
        $roleClass = get_class($role);
        $roleData        = array();
        $reflectionClass = new ReflectionClass($roleClass);
        $roleMethods     = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

        self::l($roleMethods);

        foreach ($roleMethods as $method) {
            if (self::isAvailableMethod($method) === false) {
                continue;
            }
            $property             = self::fetchProperty($method);
            $roleData [$property] = $method->invoke($role);
        }

        self::writeDataOnFile($roleData);

        if (self::hasChilds($role) === true) {
            array_walk(self::getChilds($role), 'PimPon_Role_Export::exportRole');
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

    private static function hasChilds(User_Abstract $role)
    {
        if (self::isSystem($role) === true) {
            return true;
        }
        if (method_exists($role, 'hasChilds') === false) {
            return false;
        }
        return $role->hasChilds();

    }

    private static function getChilds(User_Abstract $role)
    {
        $list = new User_Role_List();
        $list->setCondition("parentId = ?", $role->getId());
        $list->load();
        return $list->getRoles();

    }

    private static function isSystem(User_Abstract $role)
    {
        return ($role->getName() === self::SYSTEM);

    }

}
