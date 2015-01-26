<?php

class PimPon_Object_Import extends PimPon_ImportBase
{

    const FOLDER_CLASS   = "Object_Folder";
    const ABSTRACT_CLASS = "Object_Abstract";

    protected $excludeProperties = array('class');
    private $referencesMap            = array();
    private $bindObjectMap            = array();

    public function doImport()
    {

        $jsonData = file_get_contents($this->importFile);

        if (is_json($jsonData) === false) {
            throw new Exception('El fichero de importación no parece que este en formato json');
        }

        $arrayData = Zend_Json::decode($jsonData, Zend_Json::TYPE_ARRAY);

        foreach ($arrayData as $objectData) {
            $this->createObject($objectData);
        }

        $this->reassignReferences();

    }

    private function createObject($objectData)
    {
        $class = $objectData['class'];
        $fullPath = $objectData['FullPath'].'/';
        $path = $objectData['Path'];

        if ($class == self::FOLDER_CLASS) {
            $object = new $class();
        } else {
            $object = $class::create();
        }

        foreach ($objectData as $property => $value) {
            if ($this->isAvailableProperty($property, $object) === false) {
                continue;
            }
            //$parseValue = $this->parseValue($value, $property,$fullPath);
            $decodeValue = PimPon_Object_Encoder::decode($value);
            $object->{'set'.$property}($decodeValue);
        }

        $parentId = ($this->referencesMap[$path] > 0) ? $this->referencesMap[$path]
                : $this->rootId;
        
        $object->setParentId($parentId);
        $this->objectSave($object);
        $this->referencesMap[$fullPath] = $object->getId();
    }

    private function parseValue($value, $property, $fullPath)
    {
        if (is_array($value) === false) {
            return $value;
        }
        foreach ($value as $data) {
            if ($data['type'] === 'table') {
                return new Object_Data_StructuredTable($data['data']);
            }else if ($data['type'] === 'date') {
                return new Zend_Date($data['data'], Zend_Date::TIMESTAMP);
            } else if ($data['type'] === 'asset'){
                return Asset::getByPath($data['data']);
            } else if ($data['type'] === 'href' || $data['type'] === 'collection') {
                $this->bindObjectMap [$fullPath][$property][$data['type']][]
                    = $data['data'];
            } else {
                throw new Exception("Tipo de dato no contemplado, se esperaba 'date', 'collection', 'href', 'asset' o 'table' y se ha recibido '".$data['type']."'");
            }
        }
        return null;
    }

    private function reassignReferences()
    {
        foreach ($this->bindObjectMap as $oldPath => $bindDataProperties) {
            $objectId = $this->referencesMap[$oldPath];
            $object   = Object_Abstract::getById($objectId);
            foreach ($bindDataProperties as $property => $bindObjectData) {
                $setMethod = 'set'.ucfirst($property);
                foreach ($bindObjectData as $type => $data) {
                    $value = null;
                    foreach ($data as $oldReferencePath) {
                        $oldObject       = Object_Abstract::getByPath($oldReferencePath);
                        $referencePath   = (is_null($oldObject) === true) ? $this->referencesMap[$oldReferencePath]
                                : $oldObject->getFullPath();
                        $referenceObject = Object_Abstract::getByPath($referencePath);

                        if ($type === 'href') {
                            $value = $referenceObject;
                        }
                        if ($type === 'collection') {
                            $value [] = $referenceObject;
                        }
                    }
                }
                $object->$setMethod($value);
                $object->save();
            }
        }

    }

    private function objectSave(&$object)
    {
        try {
            $object->save();
        } catch (Exception $ex) {
            if ($this->getAllowReplace() === true && self::isDuplicateException($ex)
                === true) {
                $objectHinder = Object_Abstract::getByPath($object->getFullPath());
                $objectHinder->delete();
                $object->save();
            } else {
                self::l($ex->getMessage());
                throw $ex;
            }
        }

    }

}
