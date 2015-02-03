<?php

class PimPon_Object_Import extends PimPon_ImportBase
{

    const FOLDER_CLASS   = "Object_Folder";
    const ABSTRACT_CLASS = "Object_Abstract";

    protected $excludeProperties      = array('class');
    private $objectMap                = array();
    private $bindReferencesCollection = array();

    public function doImport()
    {

        $jsonData = file_get_contents($this->importFile);

        if (is_json($jsonData) === false) {
            throw new Exception('El fichero de importación no parece que este en formato json');
        }

        $dataArray = Zend_Json::decode($jsonData, Zend_Json::TYPE_ARRAY);
        foreach ($dataArray as $data) {
            $this->createObject($data);
        }

        $this->reassignReferences();

    }

    private function createObject($objectData)
    {
        $parentId = $this->rootId;
        $class    = $objectData['class'];
        $fullPath = $objectData['FullPath'][0]['data'].'/';
        $path     = $objectData['Path'][0]['data'];

        if ($class === self::FOLDER_CLASS) {
            $object = new $class();
        } else {
            $object = $class::create();
        }

        foreach ($objectData as $property => $values) {
            if(is_null($values)===true){
                continue;
            }
            if ($this->isAvailableProperty($property, $object) === false) {
                continue;
            }
            foreach ($values as $value) {
                $decodeValue = PimPon_Object_Encoder::decode($value);
                $encodertype = PimPon_Object_Encoder::getCurrentEncoderType();
                if ($this->isReference($encodertype) === true) {
                    $reference             = new stdClass();
                    $reference->type       = $encodertype;
                    $reference->class      = $value['class'];
                    $reference->path       = $decodeValue;
                    $this->bindReferencesCollection[$fullPath][$property][] = $reference;
                } else {
                    $object->{'set'.$property}($decodeValue);
                }
            }
        }
        if ($this->objectMap[$path] > 0) {
            $parentId = $this->objectMap[$path];
        }
        $object->setParentId($parentId);
        $this->objectSave($object);
        $this->objectMap[$fullPath] = $object->getId();

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

    private function reassignReferences()
    {
        foreach ($this->bindReferencesCollection as $objectPath => $propertiesCollection) {
            $objectId = $this->objectMap[$objectPath];
            $object   = Object_Abstract::getById($objectId);
            foreach ($propertiesCollection as $property => $referencesCollection) {
                $value = null;
                foreach ($referencesCollection as $reference) {
                    $referenceInstance = $this->getReferenceInstance($reference);
                    if ($reference->type === PimPon_Object_Encoder_Href::TYPE) {
                        $value = $referenceInstance;
                    } else if ($reference->type === PimPon_Object_Encoder_Collection::TYPE) {
                        $value[] = $referenceInstance;
                    }
                }
                $object->{'set'.ucfirst($property)}($value);
                $object->save();
            }
        }
    }

    private function isReference($fieldtype)
    {
        return ($fieldtype === PimPon_Object_Encoder_Collection::TYPE ||
            $fieldtype === PimPon_Object_Encoder_Href::TYPE);

    }

    private function isPimcoreObject($classname)
    {
        $object = new $classname();
        return ($object instanceof Object_Abstract);

    }

    private function getReferenceInstance($reference)
    {
        $referenceClass    = $reference->class;
        $referenceInstance = $referenceClass::getByPath($reference->path);
        if (is_null($referenceInstance) === true && $this->isPimcoreObject($referenceClass)
            === true) {
            $referenceId       = $this->objectMap[$reference->path];
            $referenceInstance = $referenceClass::getById($referenceId);
        }
        return $referenceInstance;

    }

}
