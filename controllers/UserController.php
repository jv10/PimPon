<?php

class PimPon_UserController extends Pimcore_Controller_Action_Admin
{

    public function exportAction()
    {
        try {
            $userId = $this->getParam("userId");
            $user = User_Abstract::getById($userId);

            $exportFile = PimPon_User_Export::doExport($user);

            ob_end_clean();
            header("Content-type: application/json");
            header("Content-Disposition: attachment; filename=\"pimponexport.users.".$user->getName().".json\"");
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

            $userId = $this->getParam("userId");

            $userImport = new PimPon_User_Import ();
            $userImport->setImportFile($importFile);
            $userImport->setRootId($userId);
            $userImport->setAllowReplace($this->allowReplace());

            $userImport->doImport();

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
        return ($config->replaceusers === PimPon_Plugin::ALLOW_REPLACE ? true
                    : false);

    }

}
