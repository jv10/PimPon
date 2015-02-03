<?php

class PimPon_RoleController extends Pimcore_Controller_Action_Admin
{

    public function exportAction()
    {
        try {
            $fileContents = "";
            $fileTitle    = "";
            $roleId       = $this->getParam("roleId");
            if ($roleId > 0) {
                $role              = User_Abstract::getById($roleId);
                $roleCollection [] = $role;
                $fileContents      = PimPon_Role_Export::doExport($roleCollection);
                $fileTitle         = $role->getName();
            } else if ($roleId == 0) {
                $list           = new User_Role_List();
                $list->setCondition("parentId = ?", intval($roleId));
                $list->load();
                $roleCollection = $list->getRoles();
                $fileContents   = PimPon_Role_Export::doExport($roleCollection);
                $fileTitle      = 'all';
            }
            ob_end_clean();
            header("Content-type: application/json");
            header("Content-Disposition: attachment; filename=\"pimponexport.roles.".$fileTitle.".json\"");
            echo file_get_contents($fileContents);
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
