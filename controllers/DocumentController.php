<?php

class PimPon_DocumentController extends Pimcore_Controller_Action_Admin
{

    public function exportAction()
    {
        try {
            $documentId = $this->getParam("documentId");

            $document = Document::getById($documentId);

            $exportFile = PimPon_Document_Export::doExport($document);

            ob_end_clean();
            header("Content-type: application/json");
            header("Content-Disposition: attachment; filename=\"pimponexport.documents.".$document->getKey().".json\"");
            echo file_get_contents($exportFile);
            exit;
        } catch (Exception $ex) {
            Logger::err($ex->getMessage());
            $this->_helper->json(array("success" => false, "data" => 'error'),
                false);
        }

    }

    public function importAction()
    {
        try {

            $importFile = $_FILES["Filedata"]["tmp_name"];

            $documentId = $this->getParam("documentId");

            $documentImport = new PimPon_Document_Import ();
            $documentImport->setImportFile($importFile);
            $documentImport->setRootId($documentId);
            $documentImport->setAllowReplace($this->allowReplace());

            $documentImport->doImport();

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
        return ($config->replacedocument === PimPon_Plugin::ALLOW_REPLACE ? true
                    : false);

    }

}
