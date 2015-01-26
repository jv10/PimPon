<?php

class PimPon_RoleController extends Pimcore_Controller_Action_Admin
{

    public function exportAction()
    {
        try {
            $roleId = $this->getParam("roleId");

            $role = User_Abstract::getById($roleId);

            $exportFile = PimPon_Role_Export::doExport($role);

            ob_end_clean();
            header("Content-type: application/json");
            header("Content-Disposition: attachment; filename=\"pimponexport.roles.".$role->getName().".json\"");
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

            $roleId = $this->getParam("roleId");

            $roleImport = new PimPon_Role_Import ();
            $roleImport->setImportFile($importFile);
            $roleImport->setRootId($roleId);
            $roleImport->setAllowReplace($this->allowReplace());

            $roleImport->doImport();

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
        return ($config->replaceroles === PimPon_Plugin::ALLOW_REPLACE ? true
                    : false);

    }

}
