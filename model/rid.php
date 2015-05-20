<?php
  
/* The model for PermRolesToObjectMappings table */
class PermRolesToObjectMappingsModel
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

/* The model for PermAssignmentMappings table */
class PermAssignementMappingModel
{
  /* From the PermMappings Table */
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

/* Should be the only externally used class 99% of the time */
class PermissionRole
{
    
  public function __construct($userID, $projectID, $relatedRoleID, $roleName, $roleValue)
  {
    $this->_mappedModel = new PermRolesToObjectMappingsModel()($projectID, $userID, $relatedRoleID, null);
    $this->_mappedInfo = new PermAssignementMappingModel($relatedRoleID, $projectID, $roleName, $roleValue, null);
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

/* Connects to our DB and gets the required information */
class PermissionRoleMapper extends DatabaseAdaptor
{
  private $_returnedRole;
  
  public function __construct($projectID, $userID)
  {
    parent::__construct();
  
    if($userID != 0)
    {
      $stmt = $this->dbh->prepare("SELECT * FROM `PermAssignmentMappings` WHERE `projectid` = :pid AND `userid` = :uid");    
  
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
                  $this->_returnedRole = new PermissionRole($userID, $projectID, $rs[0]['id'], $rs[0]['rolename'], $rs[0]['priv_bit_mask']);
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
    
    if($userID == 0)
    {
      $stmt = $this->dbh->prepare("SELECT `projectid`,`everyonepermissions` FROM `PermProjectDefaults` WHERE `projectid` = :pid LIMIT 1");    
  
      $stmt->bindParam(':pid', $projectID);
      
      try
      {
        if($stmt->execute()){
          $possibleMatches = $stmt->fetch(PDO::FETCH_ASSOC);
          
          if(count($possibleMatches) != 1)
            JSONResponse::printErrorResponseWithHeader("This project does not have an everyone permission set. Please run the Permissions Doctor or inform your system administrator. Project Context: $projectID");

          if(count($possibleMatches) == 1)
          {
            $this->_returnedRole = new PermissionRole($userID, $projectID, NULL, "Everyone", $rs['everyonepermissions']);
            return $this->_returnedRole;                      
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
      $stmt = $this->dbh->prepare("INSERT INTO `PermMappings` VALUES (NULL, :uid, :pid, :rid)");
      $stmt->bindValue(':rid', intval($newPermissions->getID()));
    }
    
    /* DELETE an existing permission */
    if(($newPermissions->getAction() & P_ACCESS_DELETE) == P_ACCESS_DELETE)
      $stmt = $this->dbh->prepare("DELETE FROM `PermMappings` WHERE `projectid` = :pid AND `userid` = :uid"); 
    
    /* MODIFY an existing permission */
    if(($newPermissions->getAction() & P_ACCESS_UPDATE) == P_ACCESS_UPDATE){
      $stmt = $this->dbh->prepare("UPDATE `PermMappings` SET `role` = :rid WHERE `projectid` = :pid AND `userid` = :uid");
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

/* Connects to PermRoles and gets the roles with an associated user */
class RolePermissionModel
{
  protected $_id;
  protected $_roleMappingID;
  protected $_table;
  protected $_oid;
  
  public function __construct($id, $roleMappingID, $tableName, $objectID)
  {
    $this->_id = $id;
    $this->_roleMappingID = $roleMappingID;
    $this->_table = $tableName;
    $this->_oid = $objectID;
  }
  
  public function getRolePermissionID()
  {
    return $this->_id;
  }
  
  public function getRolePermissionMappingID()
  {
    return $this->_roleMappingID;
  }
  
  public function getTableName()
  {
    return $this->_table;
  }
  
  public function getObjectID()
  {
    return $this->_oid;
  }
}

class RolePermissionModelMapper extends DatabaseAdaptor
{
  private $_roleModel;
  
  public function __construct($roleID, $table, $objectID)
  {
    parent::__construct();
    $stmt = $this->dbh->prepare("SELECT * FROM `PermRolesToObjectMappings` WHERE `rolemappingid` = :mid AND `table` = :tid AND `oid` = :oid LIMIT 1");    
  
    $stmt->bindParam(':mid', $roleID);
    $stmt->bindParam(':tid', $table);
    $stmt->bindParam(':oid', $objectID);
    
    try
    {
      if($stmt->execute()){
        $possibleMatches = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(count($possibleMatches) != 1)
          JSONResponse::printErrorResponseWithHeader("The requested role had zero or more than one entry on the requested object. Please run Permissions Doctor to resolve.");
          
        if(count($possibleMatches) == 1)
          $_this->roleModel = new RolePermissionModel($possibleMatches['id'], $roleID, $table, $objectID);
      }
      else
      {
        JSONResponse::printErrorResponseWithHeader("Unable to load Roles for this transaction. Aborting operation...");
      }    
  
    } catch(PDOException $e)
    {
      print_r($e); die();
    }
  }
  public function getRoleModel()
  {
    return $this->_roleModel;
  }
}
