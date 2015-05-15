<?php

class DummyUser
{
  protected $projectID, $userID, $roleID;
  
  public function __construct($pID, $uID, $rID)
  {
    $this->projectID = $pID;
    $this->userID = $uID;
    $this->roleID = $rID;
  }
  
  public function projectID()
  {
    return $this->projectID;
  }
  
  public function userID()
  {
    return $this->userID;
  }
  
  public function roleID()
  {
    return $this->roleID;
  }
  
  public function __toString()
  {
    return 'P:'.$this->projectID.' U:'.$this->userID.' R:'.$this->roleID.'<br />';
  }
}
  
class PermissionsEngine
{
  public function requestPermissionForOperation($projectContext, $requestingUserID, $intendedObjectID, $intendedTable, $intendedOperation,$roll)
  { 
    
    $rolesMapper = $roll;//new RolesObjectMapper($projectContext, $requestingUserID);
    if($rolesMapper !== null)
    {
      
      $v = ((($intendedOperation & $rolesMapper->getValue()) == $intendedOperation) || ((P_FULL_CONTROL & $rolesMapper->getValue()) == P_FULL_CONTROL));
      if(0)
      {
      echo 'Intended Operation: '.$intendedOperation.'<br />';
      echo 'Current Allowed   : '.$rolesMapper->getValue().'<br />';
      echo '               AND: '.($intendedOperation & $rolesMapper->getValue()).'<br />';
      echo '               XOR: '.($intendedOperation ^ $rolesMapper->getValue()).'<br />';    
      echo '             FINAL: '.(($intendedOperation & $rolesMapper->getValue()) == $intendedOperation ? "Equal" : "Not Equal").'<br /><br />';
      }
      return true === $v; 
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
  
  public function changePermissionsForObject($projectContext, $requestingUserID, $intendedObjectID, $intendedTable, $intendedPermissions)
  {
    /* Check we have permission */
    if(!requestPermissionForOperation($projectContext, $requestingUserID, $intendedObjectID, $intendedTable, P_CHANGE_ACCESS))
      return false;
      
    if($intendedTable = "Role")
    {
      $rolesMapper = new RolesObjectMapper($projectContext, $requestingUserID);
      
      $rolesMapper->updateRolePermissions($aNew);
    } 
     
  }  
}