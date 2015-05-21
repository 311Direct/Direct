<?php
  
  /**
    This file deals exclusively with mapping Roles to Permissions.
    
    This file will ONLY dealy with PermAssignmentMappings and PermProjectDefaults tables
    
  */
  

/**
  @param int $id
  The primary key ID stored of the object in the database
  
  @param int $projectID
  The Project ID to search for
  
  @param string $roleName
  The role name that will be displayed to the user
  
  @param int $value
  The permissions value is assigned in the database
  
  @param int $action
  The action to take.
*/
class PermRolesToObjectMappingsModel
{
  /* From the PermRolesToObjectMapping Table */
  protected $_id;
  protected $_roleMappingID;
  protected $_table;
  protected $_tableOID;

  public function __construct($roleMappingID, $tableName, $tablePrimaryKey)
  {
    $this->_id = NULL; /* Means we have not spoken to the database */
    $this->_roleMappingID = $roleMappingID;
    $this->_table = $tableName;
    $this->_tableOID = $tablePrimaryKey;
  }

  public function getID()
  {
    return $this->_id;
  }
  
  public function roleMappingID()
  {
    return $this->_roleMappingID;
  }
  
  public function tableName()
  {
    return $this->_tableName;
  }
  
  public function tableOID()
  {
    return $this->_tableOID;
  }   
}

class DBPermRolesToObjectMappings extends DatabaseAdaptor
{
  private $_returnedMapping;
  
  public function __construct($roleMappingID, $tableName, $tableOID)
  {
    if($roleMappingID == NULL || $tableName == NULL || $tableOID == NULL){
      $this->_returnedMapping = NULL;
      return;
    }
    parent::__construct();
  
    if($userID != 0)
    {
      $stmt = $this->dbh->prepare("SELECT * FROM `PermRolesToObjectMappings` WHERE `rolemappingid` = :rid AND `table` = :tbl AND `oid` = :objectID");    
  
      $stmt->bindParam(':rid', $roleMappingID);
      $stmt->bindParam(':tbl', $tableName);
      $stmt->bindParam(':objectID', $tableOID);
      
      try
      {
        if($stmt->execute()){
          $possibleMatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
          
          if(count($possibleMatches) == 0)
            JSONResponse::printErrorResponseWithHeader("There are no mappings associated with this mapping ID.");
          
          if(count($possibleMatches) > 1)
            JSONResponse::printErrorResponseWithHeader("Multiple role mapping were found for this object.");
            
          if(count($possibleMatches) == 1)
          {
            $this->_returnedMapping = new PermRolesToObjectMappingsModel($roleMappingID, $tableName, $tableOID);
            return;         
          }        
          JSONResponse::printErrorResponseWithHeader("DBPermRolesToObjectMappings experienced an internal error."); 
        }
      }
      catch(PDOException $e)
      {
        JSONResponse::printErrorResponseWithHeader("Unable to load roles - fatal database error: ".$e);
      }      
    }
  }
}

/* This is the model stored in the DB */
class PermProjectDefaultsModel
{
   /* From the PermProjectDefaults Table */
  protected $_id;
  protected $_projectID;
  protected $_everyonePermissions;
  protected $_defaultRoleID;

  public function __construct($projectID, $everyonePermissions, $defaultRoleID, $tablePrimaryKey = NULL)
  {
    $this->_id = $tablePrimaryKey; /* Means we have not spoken to the database */
    $this->_projectID = $projectID;
    $this->_everyonePermissions = $everyonePermissions;
    $this->_defaultRoleID = $defaultRoleID;
  }

  public function getID()
  {
    return $this->_id;
  }
  
  public function getProjectID()
  {
    return $this->_projectID;
  }
  
  public function getEveryonePermission()
  {
    return $this->_everyonePermissions;
  }
  
  public function getDefaultRoleID()
  {
    return $this->_defaultRoleID;
  }    
}

class DBPermProjectDefaultsModel extends DatabaseAdaptor
{
   /* From the PermProjectDefaults Table */
  private $_projectDefaults;
  
  public function __construct($projectContext)
  {
    
    parent::__construct();
  
    if($projectContext > 0)
    {
      $stmt = $this->dbh->prepare("SELECT * FROM `PermProjectDefaults` WHERE `projectid` = :pid");    
  
      $stmt->bindParam(':pid', $projectContext);
      
      try
      {
        if($stmt->execute()){
          $possibleMatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
          
          if(count($possibleMatches) == 0)
            JSONResponse::printErrorResponseWithHeader("Fatal: this project does not have any defaults!");
          
          if(count($possibleMatches) > 1)
            JSONResponse::printErrorResponseWithHeader("Fatal: this project has more than one default!");
            
          if(count($possibleMatches) == 1)
          {
            $this->_projectDefaults = new PermProjectDefaultsModel($projectContext, intval($possibleMatches[0][`everyonepermissions`]), intval($possibleMatches[0][`defaultroleid`]));
            return;         
          }        
          JSONResponse::printErrorResponseWithHeader("DBPermProjectDefaultsModel experienced an internal error."); 
        }
      }
      catch(PDOException $e)
      {
        JSONResponse::printErrorResponseWithHeader("Unable to load roles - fatal database error: ".$e);
      }      
    }        
  } 
}

/* This is the model stored in the DB too */
class PermAssignmentMappingsModel
{
   /* From the PermRolesToObjectMapping Table */
  protected $_id;
  protected $_userID;
  protected $_projectID;
  protected $_roleMappingID;

  public function __construct($userID, $projectID, $roleMappingID, $tablePrimaryKey = NULL)
  {
    $this->_id = $tablePrimaryKey; /* Means we have not spoken to the database */
    $this->_userID = $userID;
    $this->_projectID = $projectID;
    $this->_roleMappingID = $roleMappingID;
  }

  public function getID()
  {
    return $this->_id;
  }
  
  public function getUserID()
  {
    return $this->_userID;
  }
  
  public function getProjectID()
  {
    return $this->_projectID;
  }
  
  public function getRoleMappingID()
  {
    return $this->_roleMappingID;
  }
}

class DBPermAssignmentMappingsModel extends DatabaseAdaptor
{
   /* From the PermProjectDefaults Table */
  private $_permAssignmentModel;
  
  public function __construct($projectContext, $userID)
  {
    $this->_projectID = $projectContext;
    
    parent::__construct();
  
    if($projectContext > 0)
    {
      $stmt = $this->dbh->prepare("SELECT * FROM `PermAssignmentMappings` WHERE `projectid` = :pid AND `userID` = :uid");    
  
      $stmt->bindParam(':pid', $projectContext);
      $stmt->bindParam(':pid', $userID);
      
      try
      {
        if($stmt->execute()){
          $possibleMatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
          
          if(count($possibleMatches) == 0)
            return null;
          
          if(count($possibleMatches) > 1)
            JSONResponse::printErrorResponseWithHeader("Fatal: this project has more than one default!");
            
          if(count($possibleMatches) == 1)
          {
            $this->_permAssignmentModel = new PermAssignmentMappingsModel($projectContext, intval($possibleMatches[0][`projectid`]), intval($possibleMatches[0][`id`]), intval($possibleMatches[0][`role`]));
            return;         
          }        
          JSONResponse::printErrorResponseWithHeader("DBPermProjectDefaultsModel experienced an internal error."); 
        }
      }
      catch(PDOException $e)
      {
        JSONResponse::printErrorResponseWithHeader("Unable to load roles - fatal database error: ".$e);
      }      
    }        
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
          JSONResponse::printErrorResponseWithHeader("Unable to load Roles for this transaction. Error info: ".$stmt->errorInfo());
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
          JSONResponse::printErrorResponseWithHeader("Unable to load Everybody Permission for this operation.");
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
  
  public function getPermissionRoleObject()
  {
    return $this->_returnedRole;
  }
}