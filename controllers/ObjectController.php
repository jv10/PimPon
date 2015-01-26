<?php

class PimPon_ObjectController extends Pimcore_Controller_Action_Admin
{

    public function exportAction()
    {
        try {
            $objectId = $this->getParam("objectId");
            $object   = Object_Abstract::getById($objectId);

            $exportFile = PimPon_Object_Export::doExport($object);

            ob_end_clean();
            header("Content-type: application/json");
            header("Content-Disposition: attachment; filename=\"pimponexport.objects.".$object->getKey().".json\"");
            echo file_get_contents($exportFile);
            exit;
        } catch (Exception $ex) {
            Logger::err($ex->getMessage());
        }

    }

    public function importAction()
    {
        try {

            $importFile = $_FILES["Filedata"]["tmp_name"];

            $objectId = $this->getParam("objectId");

            $objectImport = new PimPon_Object_Import ();
            $objectImport->setImportFile($importFile);
            $objectImport->setRootId($objectId);
            $objectImport->setAllowReplace($this->allowReplace());

            $objectImport->doImport();

            $this->_helper->json(array("success" => true, "data" => "ok"), false);
        } catch (Exception $ex) {
            Logger::err($ex->getMessage());
            $this->_helper->json(array("success" => false, "data" => 'error'),
                false);
        }
        $this->getResponse()->setHeader("Content-Type", "text/html");

    }

    private function allowReplace()
    {
        $config = PimPon_Plugin::getConfig();
        return ($config->replaceobject === PimPon_Plugin::ALLOW_REPLACE ? true
                    : false);

    }

}
