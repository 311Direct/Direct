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
  
class PermissionsEngine extends DatabaseAdaptor
{
  
  private $projectID;
  private $rolesForCurrentProjectContext;
  
  public function __construct($currentProjectIDContext)
  {
    parent::__construct();
    $this->projectID = $currentProjectIDContext;
    $stmt = $this->dbh->prepare("SELECT * FROM `Roles` WHERE `project_id` = :pid");
    
    $stmt->bindParam(':pid', $currentProjectIDContext);
    
    try
    {
      if($stmt->execute()){
        $this->rolesForCurrentProjectContext = $stmt->fetchAll(PDO::FETCH_ASSOC);
      }
      else
      {
        JSONResponse::printErrorResponseWithHeader("Unable to load Roles for this transaction. Aborting operation...");
      }    
    }
    catch(PDOException $e)
    {
      JSONResponse::printErrorResponseWithHeader("Unable to load roles - fatal database error: ".$e);
    }
  }
  
  public function requestPermissionForOperation($requestingUser, $resourceACL, $intendedOperation)
  { 
    $assignedPermissions = $resourceACL->getPermissionForUser($requestingUser->projectID(), $requestingUser->userID(), $requestingUser->roleID());
    
    /* If SQL defines our role as having FULL CONTROL, we ignore whatever has been set in the ACL. */   
    if((($intendedOperation & $assignedPermissions) == $intendedOperation) || (($requestingUser->projectID() == $this->projectID) && ($this->rolesForCurrentProjectContext[$requestingUser->roleID()]["priv_bit_mask"] & P_FULL_CONTROL)))
    {
      /* We have permission to complete what we need to complete */
      return true;
    }   
        
    return false;    
  }
  
  public function changePermissionsForObject($requestingUser, $resourceACL, $intendedACE, $delete = false)
  {
    if(!$this->requestPermissionForOperation($requestingUser, $resourceACL, P_CHANGE_ACCESS))
      return null;
      
    /* Ask our ACL to change this for us */
    $resourceACL->modifyPermissions($intendedACE, $delete);    
    return $resourceACL;
  }  
}