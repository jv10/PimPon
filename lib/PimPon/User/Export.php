<?php

class PimPon_User_Export extends PimPon_ExportBase
{

    const SYSTEM = 'system';

    private static $userClases     = array('User_UserRole', 'User', 'User_Abstract');
    private static $excludeMethods = array('');

    public static function doExport(User_Abstract $user)
    {
        self::$exportFile = self::getExportFilePath();
        self::openExportFile();
        self::exportUser($user);
        self::closeExportFile();
        return self::$exportFile;

    }

    private static function exportUser(User_Abstract $user, $key = null)
    {
        $userClass = get_class($user);
        if (self::isSystem($user)===false) {
            $userData        = array();
            $reflectionClass = new ReflectionClass($userClass);
            $userMethods     = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

            self::l($userMethods);

            foreach ($userMethods as $method) {
                if (self::isAvailableMethod($method) === false) {
                    continue;
                }
                $property             = self::fetchProperty($method);
                $userData [$property] = $method->invoke($user);
            }

            self::writeDataOnFile($userData);
            
        }
        if (self::hasChilds($user) === true) {
            array_walk(self::getChilds($user), 'PimPon_User_Export::exportUser');
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
        if (in_array($method->getDeclaringClass()->name, self::$userClases) === true) {
            return true;
        }
        return false;

    }

    private static function hasChilds(User_Abstract $user)
    {
        if (self::isSystem($user) === true) {
            return true;
        }
        if (method_exists($user, 'hasChilds') === false) {
            return false;
        }
        return $user->hasChilds();

    }

    private static function getChilds(User_Abstract $user)
    {
        $list = new User_List();
        $list->setCondition("parentId = ?", $user->getId());
        $list->load();
        return $list->getUsers();

    }

    private static function isSystem(User_Abstract $user)
    {
        return ($user->getName() === self::SYSTEM);

    }

}
