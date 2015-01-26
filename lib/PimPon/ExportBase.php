<?php

class PimPon_ExportBase
{

    const ROOT_ID = 1;
    const TMP_DIR = PIMCORE_TEMPORARY_DIRECTORY;

    protected static $exportFile = '';
    protected static $isFirst    = true;

    protected static function getExportFilePath()
    {
        return self::TMP_DIR.'/'.uniqid().'.json';

    }

    protected static function fetchProperty($method)
    {
        $property = substr($method->getName(), 3);
        return ucfirst($property);

    }

    protected static function openExportFile()
    {
        file_put_contents(self::$exportFile, '['.PHP_EOL, LOCK_EX);

    }

    protected static function closeExportFile()
    {
        file_put_contents(self::$exportFile, ']', FILE_APPEND | LOCK_EX);

    }

    protected static function writeDataOnFile($data)
    {
        $comma         = (self::$isFirst === true) ? '' : ','.PHP_EOL;
        file_put_contents(self::$exportFile, $comma.json_encode($data).PHP_EOL,
            FILE_APPEND | LOCK_EX);
        self::$isFirst = false;

    }

    protected static function l($m)
    {
        Logger::emerg($m);

    }

}
