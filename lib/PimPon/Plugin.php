<?php

class PimPon_Plugin extends Pimcore_API_Plugin_Abstract implements Pimcore_API_Plugin_Interface
{

    const ALLOW_REPLACE = 'yes';
    const DENY_REPLACE  = 'no';
    const WORKING_DIR   = '/var/plugins/PimPon';
    const CONFIG_FILE   = '/config.xml';

    public function init()
    {

    }

    public static function install()
    {
        mkdir(self::getWorkingDir(), 0766, true);

    }

    public static function uninstall()
    {
        recursiveDelete(self::getWorkingDir(), true);

    }

    public static function isInstalled()
    {
        return is_dir(self::getWorkingDir());

    }

    public static function getWorkingDir()
    {
        return PIMCORE_WEBSITE_PATH.self::WORKING_DIR;

    }

    public static function getConfigFile()
    {
        return self::getWorkingDir().self::CONFIG_FILE;

    }

    public static function getConfig()
    {

        if (is_file(self::getConfigFile()) === true) {
            $config = new Zend_Config_Xml(self::getConfigFile(), null,
                array("allowModifications" => true));
        } else {
            $config = new Zend_Config(array(), true);
        }

        // set default
        $config->overwriteobjects   = ($config->overwriteobjects === self::ALLOW_REPLACE)
                ? self::ALLOW_REPLACE : self::DENY_REPLACE;
        $config->overwritedocuments = ($config->overwritedocuments === self::ALLOW_REPLACE)
                ? self::ALLOW_REPLACE : self::DENY_REPLACE;
        $config->overwriteroutes    = ($config->overwriteroutes === self::ALLOW_REPLACE)
                ? self::ALLOW_REPLACE : self::DENY_REPLACE;

        return $config;

    }

}
