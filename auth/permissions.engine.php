<?php
 
class PermissionsEngine
{
  public function requestPermissionForOperation($projectContext, $requestingUserID, $intendedObjectID, $intendedTable, $intendedOperation)
  { 
    $rolesObject = new RolesObjectMapper($projectContext, $requestingUserID);
    if($rolesObject->getRoleInfo() === null)
      return false;
      
    $permsObject = new PermissionsObjectMapper($intendedObjectID, $intendedTable, $rolesObject->getRoleInfo()->getUserID());
    
    return $this->canCompleteOperation($rolesObject, $permsObject, $intendedOperation);   
  }
  
  public function requestPermissionForOperationWithObjects($rolesObject, $intendedPermissionsObject, $intendedOperation)
  { 
    return $this->canCompleteOperation($rolesObject, $intendedPermissionsObject, $intendedOperation);    
  }
  
  public function requestPermissionForOperationWithUserObject($rolesObject, $intendedObjectID, $intendedTable, $intendedOperation)
  {
    if($rolesMapper instanceof RolesObjectMapper)
        $rolesMapper = $rolesMapper->getRoleInfo();
        
    $permsObject = new PermissionsObjectMapper($intendedObjectID, $intendedTable, $rolesObject->getUserID());
    return $this->canCompleteOperation($rolesObject, $permsObject, $intendedOperation);
  }
  
  private function canCompleteOperation($rolesMapper, $intendedPermissionsObject, $intendedOperation)
  {
    if($rolesMapper !== null)
    {
      if($rolesMapper instanceof RolesObjectMapper)
          $rolesMapper = $rolesMapper->getRoleInfo();
                
      return true === ((($intendedOperation & $rolesMapper->getValue()) == $intendedOperation) || ((P_FULL_CONTROL & $rolesMapper->getValue()) == P_FULL_CONTROL));
    }
    else
    {
      $objectMapper = new PermissionsObjectMapper($userID, $intendedTable, $intendedObjectID);
      if($objectMapper != null)
      {
        return true === ((($intendedOperation & $objectMapper->getValue()) == $intendedOperation) || (($objectMapper->getValue() & P_FULL_CONTROL)));
      }
      else
      {
        /* Return everybody permission from project */
        $everyoneMapper = new RolesObjectMapper($projectContext, 0);
        return true === ((($intendedOperation & $everyoneMapper->getValue()) == $intendedOperation) || (($everyoneMapper->getValue() & P_FULL_CONTROL))); 
        
      }
    }
  }
  
  public function changePermissionsForObjectWithObjects($requestingRoleObject, $intendedPermissionsObject, $intendedPermissions)
  {
    return doChangePermissions($requestingRoleObject, $intendedPermissionsObject, $intendedPermissions);
  }
          
  public function changePermissionsForObject($projectContext, $requestingUserID, $intendedObjectID, $intendedTable, $intendedPermissions)
  {
    $roleObject = new RolesObjectMapper($projectContext, $requestingUserID);
    $permsObject = new PermissionsObjectMapper($intendedObjectID, $intendedTable, $roleObject->getUserID());
    
    return $this->doChangePermissions($rolesObject, $permsObject, $intendedPermissions);
  } 
  
  private function doChangePermissions($roleObject, $permsObject, $intendedPerms)
  {
    if($rolesMapper instanceof RolesObjectMapper)
        $rolesMapper = $rolesMapper->getRoleInfo();
        
    /* Check we have permission */
    if(!canCompleteOperation($roleObject->getProjectID(), $roleObject->getUserID(), $intendedObjectID, $intendedTable, P_CHANGE_ACCESS))
      return false;
      
    if($intendedTable = "Role")
    {
      $rolesMapper = new RolesObjectMapper($projectContext, $requestingUserID);
      $rolesMapper->updateRolePermissions($aNew);
    } 
  }
}