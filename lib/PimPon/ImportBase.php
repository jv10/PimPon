<?php

class PimPon_ImportBase
{

    const DUPLICATE = 'Duplicate ';

    protected $excludeProperties = array();
    protected $rootId            = 1;
    protected $importFile        = "";
    protected $allowReplace      = false;

    function getAllowReplace()
    {
        return $this->allowReplace;

    }

    function setAllowReplace($allowReplace)
    {
        $this->allowReplace = $allowReplace;

    }

    public function setRootId($rootId)
    {
        $this->rootId = $rootId;

    }

    public function setImportFile($importFile)
    {
        $this->importFile = $importFile;

    }

    protected function isAvailableProperty($property, $document)
    {
        if (in_array($property, $this->excludeProperties) === true) {
            return false;
        }
        if (method_exists($document, 'set'.$property) === false) {
            return false;
        }
        return true;

    }

    protected static function isDuplicateException(Exception $ex)
    {
        return (strpos($ex->getMessage(), self::DUPLICATE) === false) ? false : true;

    }

    protected static function l($m)
    {
        Logger::emerg($m);

    }

}
