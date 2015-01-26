<?php

class PimPon_Routes_Import extends PimPon_ImportBase
{

    public function doImport()
    {

        $jsonData = file_get_contents($this->importFile);

        if (is_json($jsonData) === false) {
            throw new Exception('El fichero de importación no parece que este en formato json');
        }

        $arrayData = Zend_Json::decode($jsonData, Zend_Json::TYPE_ARRAY);

        foreach ($arrayData as $routeData) {
            $this->createRoute($routeData);
        }

    }

    private function createRoute($routeData)
    {
        $route = new Staticroute();
        $route->setValues($routeData);
        $this->routeSave($route);
    }

    private function routeSave(&$route)
    {
        if ($this->getAllowReplace() === true) {
            $routeHinder = Staticroute::getByName($route->getName());
            $routeHinder->delete();
        }
        $route->save();
    }

}
