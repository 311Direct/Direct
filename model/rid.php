<?php
  
class RoleInformationModel
{
  /* From the ROLES Table */
  protected $_id;
  protected $_projectID;
  protected $_rolename;
  protected $_value;
  protected $_action;

  public function __construct($id, $projectID, $roleName, $value, $action = null)
  {
    $this->_id = $id;
    $this->_projectID = $projectID;
    $this->_rolename = $roleName;
    $this->_value = intval($value);
    $this->_action = $action;
  }

  public function getID()
  {
    return $this->_id;
  }
  
  public function getProjectID()
  {
    return $this->_projectID;
  }
  
  public function getRoleName()
  {
    return $this->_rolename;
  }
  
  public function getValue()
  {
    return $this->_value;
  }
  
  public function getAction()
  {
    return $this->_action;
  }   
  
}

class RoleMappingModel
{
  /* From the PermRoles Table */
  protected $_id;
  protected $_userID;
  protected $_projectID;
  protected $_roleID;
  protected $_action;
  
  public function __construct($projectID, $userID, $relatedRoleID, $action = null, $id = null)
  {
    $this->_id = $id;
    $this->_userID = $userID;
    $this->_projectID = $projectID;
    $this->_roleID = $relatedRoleID;
    $this->_action = $action;
  }
  
  public function getUserID()
  {
    return $this->_userID;
  }
  
  public function getProjectID()
  {
    return $this->_projectID;
  }
  
  public function getRoleID()
  {
    return $this->_roleID;
  }
  
  public function getAction()
  {
    return $this->_action;
  }  
  
  public function setAction($anAction)
  {
    if( (($this->_value & (P_FULL_CONTROL | P_CHANGE_ACCESS)) || ($this->_id == NULL))
        && ($anAction >= P_ACCESS_MIN)
        && ($anAction <= P_ACCESS_MAX)
    )
    {  $this->_action = $anAction; }
  }
}

class RoleObject
{
    
  public function __construct($userID, $projectID, $relatedRoleID, $roleName, $roleValue)
  {
    $this->_mappedModel = new RoleMappingModel($projectID, $userID, $relatedRoleID, null);
    $this->_mappedInfo = new RoleInformationModel($relatedRoleID, $projectID, $roleName, $roleValue, null);
  }
  public function getID()
  {
    return $this->_mappedModel->_userID;
  }
      
  public function getUserID()
  {
    return $this->_mappedModel->getUserID();
  }
  
  public function getProjectID()
  {
    return $this->_mappedModel->getProjectID();
  }
  
  public function getRoleID()
  {
    return $this->_mappedModel->getRoleID();
  }
  
  public function getRoleName()
  {
    return $this->_mappedInfo->getRoleName();
  }
  
  public function getValue()
  {
    return $this->_mappedInfo->getValue();
  }   
    
  public function getAction()
  {
    return null;
  }
}

class RolesObjectMapper extends DatabaseAdaptor
{

  private $_returnedRole;
  
  public function __construct($projectID, $userID)
  {
    parent::__construct();
  
    $stmt = $this->dbh->prepare("SELECT * FROM `PermRoles` WHERE `projectid` = :pid AND `userid` = :uid");    
  
    $stmt->bindParam(':pid', $projectID);
    $stmt->bindParam(':uid', $userID);
    
    try
    {
      if($stmt->execute()){
        $possibleMatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if(count($possibleMatches) > 1)
          JSONResponse::printErrorResponseWithHeader("The user and project specified had multiple roles!");
          
        if(count($possibleMatches) < 1)
          return null;
          
        if(count($possibleMatches) == 1)
        {
          $roleID = $possibleMatches[0]['role'];
          $rstmt = $this->dbh->prepare("SELECT * FROM `Roles` WHERE `id` = :id");
          $rstmt->bindParam(':id', $roleID);
          
          try
          {
            if($rstmt->execute())
            {
              $rs = $rstmt->fetchAll(PDO::FETCH_ASSOC); 
              if(count($rs) == 1)
                $this->_returnedRole = new RoleObject($userID, $projectID, $rs[0]['id'], $rs[0]['rolename'], $rs[0]['priv_bit_mask']);
              else
                $this->_returnedRole = null;
            }
            
            return $this->_returnedRole;
          }
          catch(PDOException $e)
          {
            JSONResponse::printErrorResponseWithHeader("Unable to load roles - fatal database error: ".$e);
          }
          
        }
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
  
  
  public function updateRolePermission($newPermissions)
  {
    
    /* Adding a new role */
    if($newPermissions->getAction() & P_ACCESS_ADD){
      $stmt = $this->dbh->prepare("INSERT INTO `Roles` VALUES (NULL, :pid, :name, :value)");
      $stmt->bindValue(':name', $newPermissions->getRole());
      $stmt->bindValue(':value', $newPermissions->getValue());
    }
    
    /* Deleting a new role */
    if($newPermissions->getAction() & P_ACCESS_DELETE){
      $stmt = $this->dbh->prepare("DELETE FROM `Roles` WHERE `projectid` = :pid AND `id` = :id"); 
      
      $stmt->bindValue(':id', $newPermissions->getID());
    }
    
    /* Modifying a new role */
    if($newPermissions->getAction() & P_ACCESS_UPDATE){
      
      $stmt = $this->dbh->prepare("UPDATE `Roles` SET `name` = :name, `priv_bit_mask` = :value WHERE `projectid` = :pid AND `id` = :id");
      
      $stmt->bindValue(':value', $newPermissions->getValue());
      $stmt->bindValue(':id', $newPermissions->getID());
    }
    
    $stmt->bindValue(':pid', $newPermissions->getProjectID());
      
    try
    {
      if($stmt->execute())
      {
        return true;
      }
      else
      {
        print_r($stmt->errorInfo());
        return false;
      }
    
    }
    catch(PDOException $e)
    {
      die($e);
    }
  }
  
  public function updateRoleMapping($newPermissions)
  {
    if($newPermissions->getAction() == 0)
      return false;    
      
    /* ADD a new permission */
    if(($newPermissions->getAction() & P_ACCESS_ADD) == P_ACCESS_ADD){
      $stmt = $this->dbh->prepare("INSERT INTO `PermRoles` VALUES (NULL, :uid, :pid, :rid)");
      $stmt->bindValue(':rid', intval($newPermissions->getID()));
    }
    
    /* DELETE an existing permission */
    if(($newPermissions->getAction() & P_ACCESS_DELETE) == P_ACCESS_DELETE)
      $stmt = $this->dbh->prepare("DELETE FROM `PermRoles` WHERE `projectid` = :pid AND `userid` = :uid"); 
    
    /* MODIFY an existing permission */
    if(($newPermissions->getAction() & P_ACCESS_UPDATE) == P_ACCESS_UPDATE){
      $stmt = $this->dbh->prepare("UPDATE `PermRoles` SET `role` = :rid WHERE `projectid` = :pid AND `userid` = :uid");
      $stmt->bindValue(':rid', intval($newPermissions->getID()));
    }
  
    $stmt->bindValue(':pid', $newPermissions->getProjectID());
    $stmt->bindValue(':uid', $newPermissions->getUserID());
    
    try
    {
      if($stmt->execute())
      {
        return true;
      }
      else
      {
        return false;
      }
    
    }
    catch(PDOException $e)
    {
      die($e);
    } 
  }
  
  public function getRoleInfo()
  {
    return $this->_returnedRole;
  }
}
