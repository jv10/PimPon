<?php

class PimPon_SettingsController extends Pimcore_Controller_Action_Admin
{
    protected $dataDir;

    public function init()
    {
        parent::init();

        // check if data folder exists
        $this->dataDir = dirname(PimPon_Plugin::getConfigFile());
        if (!is_dir($this->dataDir)) {
            mkdir($this->dataDir, 0755, true);
        }

    }

    public function indexAction()
    {
        if ($this->getParam("save") === 'yes') {

            $settings = array(
                "replaceobjects" => (($this->getParam("replaceobjects") === PimPon_Plugin::ALLOW_REPLACE)
                        ? PimPon_Plugin::ALLOW_REPLACE : PimPon_Plugin::DENY_REPLACE ),
                "replacedocuments" => (($this->getParam("replacedocuments") === PimPon_Plugin::ALLOW_REPLACE)
                        ? PimPon_Plugin::ALLOW_REPLACE : PimPon_Plugin::DENY_REPLACE ),
                "replaceroutes" => (($this->getParam("replaceroutes") === PimPon_Plugin::ALLOW_REPLACE)
                        ? PimPon_Plugin::ALLOW_REPLACE : PimPon_Plugin::DENY_REPLACE ),
                "replaceusers" => (($this->getParam("replaceusers") === PimPon_Plugin::ALLOW_REPLACE)
                        ? PimPon_Plugin::ALLOW_REPLACE : PimPon_Plugin::DENY_REPLACE ),
                "replaceroles" => (($this->getParam("replaceroles") === PimPon_Plugin::ALLOW_REPLACE)
                        ? PimPon_Plugin::ALLOW_REPLACE : PimPon_Plugin::DENY_REPLACE )
            );
            $config = new Zend_Config($settings, true);
            $writer = new Zend_Config_Writer_Xml(array(
                "config" => $config,
                "filename" => PimPon_Plugin::getConfigFile()
            ));
            $writer->write();
        }
        $config = PimPon_Plugin::getConfig();
        $this->view->config = $config;

    }

}
