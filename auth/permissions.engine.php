<?php
  
include('../../model/roles.permission.php');
include('../../model/mappings.permission.php');
include('../../model/objects.permission.php');
 
class PermissionsEngine
{
  
  /**
    @param int $projectContext
    The ID of the current project we are working on 
    
    @param int $requestingUserID
    The ID of ths user that is requesting to change permissions
    
    @param int $intendedObjectID
    The ID of the object that will be changed after the operation
    
    @param string $intendedTable
    The string name of the table where $intendedObjectID lies
    
    @param PERMISSION_TYPE $intendedOperation
    The P_* permission that will overwrite the intended object
    
    @return TRUE 
    If the requesting user does not have permission to change the object
    
    @return FALSE 
    If the requesting user has permission AND the object update succeeds
  */ 
  public function requestPermissionForOperation($projectContext, $requestingUserID, $intendedObjectID, $intendedTable, $intendedOperation)
  { 
    $rolesObject = new PermissionRoleMapper($projectContext, $requestingUserID);
    if($rolesObject->getRoleInfo() === null)
      return false;
      
    $permsObject = new PermissionsObjectMapper($intendedObjectID, $intendedTable, $rolesObject->getRoleInfo()->getUserID());
    
    return $this->canCompleteOperation($rolesObject, $permsObject, $intendedOperation);   
  }
  
  
   /** 
    @param RoleObject $rolesObject
    The RoleObject that would like to perform the operation
    
    @param int $intendedPermissionsObject
    The PermissionObject that will be have the operation applied to it.
        
    @param PERMISSIONS_TYPE $intendedOperation
    The operation that will be performed; will accept a binary OR (|)
    */  
  public function requestPermissionForOperationWithObjects($rolesObject, $intendedPermissionsObject, $intendedOperation)
  { 
    return $this->canCompleteOperation($rolesObject, $intendedPermissionsObject, $intendedOperation);    
  }
    
  /** 
    @param RoleObject $rolesObject
    The RoleObject that would like to perform the operation
    
    @param int $intendedObjectID
    The ID of the object that will have an operation performed on it.
    
    @param string $intendedTable
    The table where the $intendedObjectID lies
    
    @param PERMISSIONS_TYPE $intendedOperation
    The operation that will be performed; will accept a binary OR (|)
    
    */    
  public function requestPermissionForOperationWithUserObject($rolesObject, $intendedObjectID, $intendedTable, $intendedOperation)
  {
    if($rolesMapper instanceof PermissionRoleMapper)
        $rolesMapper = $rolesMapper->getRoleInfo();
        
    $permsObject = new PermissionsObjectMapper($intendedObjectID, $intendedTable, $rolesObject->getUserID());
    return $this->canCompleteOperation($rolesObject, $permsObject, $intendedOperation);
  }
  
  /**
    This is a private operation that cannot be directly accessed.
  */  
  private function canCompleteOperation($rolesMapper, $intendedPermissionsObject, $intendedOperation)
  {
    if($rolesMapper !== null)
    {
      if($rolesMapper instanceof PermissionRoleMapper)
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
        $everyoneMapper = new PermissionRoleMapper($projectContext, 0);
        return true === ((($intendedOperation & $everyoneMapper->getValue()) == $intendedOperation) || (($everyoneMapper->getValue() & P_FULL_CONTROL))); 
        
      }
    }
  }
    
  /**
    @param RoleObject $requestingRoleObject
    The RoleObject of the user who is requesting permission to change Permission
    
    @param PermissionObject $intendedPermissionsObject
    The PermissionObject that will have its permissions changed
    
    @param PERMISSION_TYPE $intendedPermissions
    The P_* value of the permissions that will be written to the object.
    
    @return FALSE
    If the user does not have permission to complete the operation
    
    @return TRUE
    If the user has P_CHANGE_ACCESS permissions AND the operation is successful.
  */
  public function changePermissionsForObjectWithObjects($requestingRoleObject, $intendedPermissionsObject, $intendedPermissions)
  {
    return doChangePermissions($requestingRoleObject, $intendedPermissionsObject, $intendedPermissions);
  }
  
  
  /**
    @param int $projectContext
    The ID of the current project we are working on 
    
    @param int $requestingUserID
    The ID of ths user that is requesting to change permissions
    
    @param int $intendedObjectID
    The ID of the object that will be changed after the operation
    
    @param string $intendedTable
    The string name of the table where $intendedObjectID lies
    
    @param PERMISSION_TYPE $intendedPermissions
    The P_* permission that will overwrite the intended object
    
    @return TRUE 
    If the requesting user does not have permission to change the object
    
    @return FALSE 
    If the requesting user has permission AND the object update succeeds
  */          
  public function changePermissionsForObject($projectContext, $requestingUserID, $intendedObjectID, $intendedTable, $intendedPermissions)
  {
    $roleObject = new PermissionRoleMapper($projectContext, $requestingUserID);
    $permsObject = new PermissionsObjectMapper($intendedObjectID, $intendedTable, $roleObject->getUserID());
    
    return $this->doChangePermissions('Object', $rolesObject, $intendedObjectID, $intendedPermissions);
  } 
  
  
  public function changePermissionsForRole($projectContext, $requestingUserID, $intendedRoleInformationModel, $intendedPermissions)
  {
    $roleObject = new PermissionRoleMapper($projectContext, $requestingUserID);
    
    return $this->doChangePermissions('Role', $rolesObject, $$intendedRoleInformationModel, $intendedPermissions);
  }
    
  /**
     This is a private function that cannot be directly accessed
  */
  private function doChangePermissions($type, $roleObject, $permsObject, $intendedPerms)
  {
    if($rolesMapper instanceof PermissionRoleMapper)
        $rolesMapper = $rolesMapper->getRoleInfo();
        
    /* Check we have permission */
    if(!canCompleteOperation($rolesMapper, $permsObject, P_CHANGE_ACCESS))
      return false;
      
    if($type = 'Object')
    {
      $newPerms = new PermissionsObjectMapper($permsObject->getPermissionObjects()[0]->getUserID(), $permsObject->getPermissionObjects()[0]->getTable(), $permsObject->getPermissionObjects()[0]->getOID());
      $newPerms->updateObjectPermissions($intendedPermissions);
    } 
    else
    if($type = 'Role')
    {
      $rolesMapper = new PermissionRoleMapper($projectContext, $requestingUserID);
      $rolesMapper->updateRolePermissions($aNew);
      
    }
    
  }
}