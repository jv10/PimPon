<?php

class PimPon_UserController extends Pimcore_Controller_Action_Admin
{

    public function exportAction()
    {
        try {
            $fileContents = "";
            $fileTitle    = "";
            $userId       = $this->getParam("userId");
            if ($userId > 0) {
                $user              = User_Abstract::getById($userId);
                $userCollection [] = $user;
                $fileContents      = PimPon_User_Export::doExport($userCollection);
                $fileTitle         = $user->getName();
            } else if ($userId == 0) {
                $list           = new User_List();
                $list->setCondition("parentId = ?", intval($userId));
                $list->load();
                $userCollection = $list->getUsers();
                $fileContents   = PimPon_User_Export::doExport($userCollection);
                $fileTitle      = 'all';
            }
            ob_end_clean();
            header("Content-type: application/json");
            header("Content-Disposition: attachment; filename=\"pimponexport.users.".$fileTitle.".json\"");
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
        return ($config->replaceusers === PimPon_Plugin::ALLOW_REPLACE ? true : false);

    }

}
