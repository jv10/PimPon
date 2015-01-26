<?php

class PimPon_User_Import extends PimPon_ImportBase
{

    protected $excludeProperties = array('ParentId', 'Id', 'Type', 'Name');
    private $usersMap            = array();
    private $workspaces          = array('WorkspacesObject' => 'User_Workspace_Object',
        'WorkspacesDocument' => 'User_Workspace_Document', 'WorkspacesAsset' => 'User_Workspace_Asset');
    private $arrayTypeProperties = array ('Classes', 'DocTypes');
    private $currentClass        = '';
    private $availablesUserTypes = array('user', 'userfolder');

    public function doImport()
    {

        $jsonData = file_get_contents($this->importFile);

        if (is_json($jsonData) === false) {
            throw new Exception('El fichero de importación no parece que este en formato json');
        }


        $arrayData = Zend_Json::decode($jsonData, Zend_Json::TYPE_ARRAY);

        foreach ($arrayData as $userData) {
            $this->createUser($userData);
        }

    }

    private function createUser($userData)
    {

        try {
            
            $this->throwExceptionIfNotAvailableType($userData['Type']);

            $this->currentClass = User_Service::getClassNameForType($userData['Type']);

            $class           = $this->currentClass;
            $oldUserId       = $userData['Id'];
            $oldUserParentId = $userData['ParentId'];
            $userName        = $userData['Name'];

            $parentId = ($this->usersMap[$oldUserParentId] > 0) ? $this->usersMap[$oldUserParentId]
                    : $this->rootId;

            if ($this->getAllowReplace() === true) {
                $this->deleteUserIfExist($userData);
            }

            $user = $class::create(array(
                    'name' => $userName,
                    'parentId' => $parentId
            ));

            $this->usersMap[$oldUserId] = $user->getId();

            foreach ($userData as $property => $value) {

                if ($this->isAvailableProperty($property, $user) === false) {
                    continue;
                }
                $parseValue = $this->parseValue($value, $property,
                    $user->getId());

                $user->{'set'.$property}($parseValue);
            }

            $user->save();

        } catch (Exception $ex) {
            self::l($ex);
        }

    }

    private function parseValue($value, $property, $userId)
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
                    $newWorkspace->setUserId($userId);
                    $clonedWorkspaces[] = $newWorkspace;
                }
            }
            return $clonedWorkspaces;
        }
        return $value;

    }

    private function deleteUserIfExist($userData)
    {
        $class      = $this->currentClass;
        $userHinder = $class::getByName($userData['Name']);
        if ($userHinder instanceof User_Abstract) {
            $userHinder->delete();
        }

    }

    private function throwExceptionIfNotAvailableType($type)
    {
        if (in_array($type, $this->availablesUserTypes) === false) {
            throw new Exception(sprintf('Tipo de objeto no permitido se esperaba: %s y se ha recibido: %s',
                implode($this->availablesUserTypes), $type));
        }
    }

}