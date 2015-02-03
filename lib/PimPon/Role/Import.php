<?php

class PimPon_Role_Import extends PimPon_ImportBase
{

    protected $excludeProperties = array('ParentId', 'Id', 'Type', 'Name');
    private $rolesMap            = array();
    private $workspaces          = array('WorkspacesObject' => 'User_Workspace_Object',
        'WorkspacesDocument' => 'User_Workspace_Document', 'WorkspacesAsset' => 'User_Workspace_Asset');
    private $arrayTypeProperties = array('Classes', 'DocTypes');
    private $currentClass        = '';
    private $availablesRoleTypes = array('role', 'rolefolder');

    public function doImport()
    {

        $jsonData = file_get_contents($this->importFile);

        if (is_json($jsonData) === false) {
            throw new Exception('El fichero de importación no parece que este en formato json');
        }

        $arrayData = Zend_Json::decode($jsonData, Zend_Json::TYPE_ARRAY);

        foreach ($arrayData as $roleData) {
            $this->createRole($roleData);
        }

    }

    private function createRole($roleData)
    {
        try {

            $this->throwExceptionIfNotAvailableType($roleData['Type']);

            $this->currentClass = User_Service::getClassNameForType($roleData['Type']);

            $class           = $this->currentClass;
            $oldRoleId       = $roleData['Id'];
            $oldRoleParentId = $roleData['ParentId'];
            $roleName        = $roleData['Name'];

            $parentId = ($this->rolesMap[$oldRoleParentId] > 0) ? $this->rolesMap[$oldRoleParentId]
                    : $this->rootId;

            if ($this->getAllowReplace() === true) {
                $this->deleteRoleIfExist($roleData);
            }

            $role = $class::create(array(
                    'name' => $roleName,
                    'parentId' => $parentId
            ));

            $this->rolesMap[$oldRoleId] = $role->getId();

            foreach ($roleData as $property => $value) {
                if ($this->isAvailableProperty($property, $role) === false) {
                    continue;
                }
                
                $parseValue = $this->parseValue($value, $property,
                    $role->getId());
                self::l('hola');
                $role->{'set'.$property}($parseValue);
            }

            $role->save();
        } catch (Exception $ex) {
            self::l($ex);
            throw $ex;
        }

    }

    private function parseValue($value, $property, $roleId)
    {
        if (in_array($property, $this->arrayTypeProperties)) {
            return implode(',', $value);
        }

        if (array_key_exists($property, $this->workspaces)) {
            $clonedWorkspaces = array();
            if (is_array($value)) {
                foreach ($value as $workspace) {
                    $workspaceClass = $this->workspaces[$property];
                    $newWorkspace   = new $workspaceClass();
                    foreach ($workspace as $varKey => $varValue) {
                        $newWorkspace->$varKey = $varValue;
                    }
                    $newWorkspace->setUserId($roleId);
                    $clonedWorkspaces[] = $newWorkspace;
                }
            }
            return $clonedWorkspaces;
        }
        return $value;

    }

    private function deleteRoleIfExist($roleData)
    {
        $class      = $this->currentClass;
        $roleHinder = $class::getByName($roleData['Name']);
        if ($roleHinder instanceof User_Abstract) {
            $roleHinder->delete();
        }

    }

    private function throwExceptionIfNotAvailableType($type)
    {
        if (in_array($type, $this->availablesRoleTypes) === false) {
            throw new Exception(sprintf('Tipo de objeto no permitido se esperaba: %s y se ha recibido: %s',
                implode($this->availablesRoleTypes), $type));
        }

    }

}
