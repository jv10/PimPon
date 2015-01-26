<?php


class PimPon_RouteController extends Pimcore_Controller_Action_Admin {


    public function exportAction()
    {
        try {

            $exportFile = PimPon_Routes_Export::doExport();

            ob_end_clean();
            header("Content-type: application/json");
            header("Content-Disposition: attachment; filename=\"pimponexport.routes.json\"");
            echo file_get_contents($exportFile);
            exit;

        } catch (Exception $ex) {
            Logger::err($ex->getMessage());
            $this->_helper->json(array("success" => false, "data" => 'error'), false);
        }
    }

    public function importAction()
    {
        try {
            
            $importFile = $_FILES["Filedata"]["tmp_name"];

            $routeImport = new PimPon_Routes_Import ();
            $routeImport->setImportFile($importFile);
            $routeImport->setAllowReplace($this->allowReplace());

            $routeImport->doImport();

            $this->_helper->json(array("success" => true, "data" => "ok"), false);

        } catch (Exception $ex) {
            Logger::err($ex->getMessage());
            $this->_helper->json(array("success" => false, "data" => 'error'), false);
        }
        $this->getResponse()->setHeader("Content-Type", "text/html");
    }

    private function allowReplace(){
        $config = PimPon_Plugin::getConfig();
        return ($config->replaceroutes===PimPon_Plugin::ALLOW_REPLACE?true:false);
    }
}
