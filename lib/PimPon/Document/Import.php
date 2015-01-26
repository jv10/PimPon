<?php

class PimPon_Document_Import extends PimPon_ImportBase
{

    const FOLDER_CLASS   = "Document_Folder";
    const ABSTRACT_CLASS = "Document";

    protected $excludeProperties = array('class');
    private $referencesMap            = array();

    public function doImport()
    {

        $jsonData = file_get_contents($this->importFile);

        if (is_json($jsonData) === false) {
            throw new Exception('El fichero de importación no parece que este en formato json');
        }

        $arrayData = Zend_Json::decode($jsonData, Zend_Json::TYPE_ARRAY);

        foreach ($arrayData as $documentData) {
            $this->createDocument($documentData);
        }

    }

    private function createDocument($documentData)
    {

        $class    = $documentData['class'];
        $fullPath = $documentData['RealFullPath'].'/';
        $path     = $documentData['RealPath'];

        $document = new $class();

        foreach ($documentData as $property => $value) {
            if ($this->isAvailableProperty($property, $document) === false) {
                continue;
            }
            $document->{'set'.$property}($value);
        }

        $parentId = ($this->referencesMap[$path] > 0) ? $this->referencesMap[$path]
                : $this->rootId;
        $document->setParentId($parentId);
        $this->documentSave($document);

        $this->referencesMap[$fullPath] = $document->getId();

    }
    

    private function documentSave(&$document)
    {
        try {
            $document->save();
        } catch (Exception $ex) {
            if ($this->getAllowReplace() === true && self::isDuplicateException($ex)
                === true) {
                $documentHinder = Document::getByPath($document->getFullPath());
                $documentHinder->delete();
                $document->save();
            } else {
                self::l($ex->getMessage());
                throw $ex;
            }
        }

    }

}
