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
  protected $_ID;
  protected $_roleMappingID;
  protected $_table;
  protected $_tableOID;

  public function __construct($roleMappingID, $tableName, $tablePrimaryKey)
  {
    $this->_ID = NULL; /* Means we have not spoken to the database */
    $this->_roleMappingID = intval($roleMappingID);
    $this->_table = $tableName;
    $this->_tableOID = intval($tablePrimaryKey);
  }

  public function getID()
  {
    return $this->_ID;
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
  
    $stmt = $this->dbh->prepare("SELECT * FROM `PermRolesToObjectMappings` WHERE `rolemappingid` = :rid AND `table` = :tbl AND `oid` = :objectID");    

    $stmt->bindParam(':rid', $roleMappingID);
    $stmt->bindParam(':tbl', $tableName);
    $stmt->bindParam(':objectID', $tableOID);
    
    try
    {
      if($stmt->execute()){
        $possibleMatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
        if(count($possibleMatches) > 1)
          JSONResponse::printErrorResponseWithHeader("Multiple role mapping were found for this object. Please run Permissions Repair Doctor!");
          
        if(count($possibleMatches) == 1 || count($possibleMatches) == 0)
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
    
  public function createRoleToObjectMapping()
  {
    if($this->_returnedMapping == NULL)
      return null;
    else
      $this->deleteRoleToObjectMapping(); 
    
    $stmt = $this->dbh->prepare("INSERT INTO `PermRolesToObjectMappings` (id, rolemappingid, table, oid) VALUE (NULL, :rid, :tbl, :objectID)");
    $stmt->bindParam(':rid', $this->_returnedMapping->_userID);
    $stmt->bindParam(':tbl', $this->_returnedMapping->_projectID);
    $stmt->bindParam(':objectID', $roleID); 

    try{
      if($stmt->execute())
        return true;
      else
        return false;
    }
    catch(PDOException $e)
    {
      JSONResponse::printErrorResponseWithHeader("Unable to add or update role to object mapping - fatal database error: ".$e);
    }
  }
  
  public function deleteRoleToObjectMapping()
  {
    if($this->_returnedMapping == NULL)
      return false;
    
    $stmt = $this->dbh->prepare("DELETE FROM `PermRolesToObjectMappings` WHERE `id` = :guid");     
    $stmt->bindParam(':guid', $this->_returnedMapping->getID());

    try{
      if($stmt->execute())
        return true;
      else
        return false; 
    }
    catch(PDOException $e)
    {
      JSONResponse::printErrorResponseWithHeader("Unable to add or update role to object mapping - fatal database error: ".$e);
    }
  }  
}

/* This is the model stored in the DB */
class PermProjectDefaultsModel
{
   /* From the PermProjectDefaults Table */
  protected $_ID;
  protected $_projectID;
  protected $_everyonePermissions;
  protected $_defaultRoleID;

  public function __construct($projectID, $everyonePermissions, $defaultRoleID, $tablePrimaryKey = NULL)
  {
    $this->_ID = intval($tablePrimaryKey); /* Means we have not spoken to the database */
    $this->_projectID = intval($projectID);
    $this->_everyonePermissions = $everyonePermissions;
    $this->_defaultRoleID = intval($defaultRoleID);
  }

  public function getID()
  {
    return $this->_ID;
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
            $this->_projectDefaults = new PermProjectDefaultsModel($projectContext, intval($possibleMatches[0]['everyonepermissions']), intval($possibleMatches[0]['defaultroleid']));
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
  
  private function save($sqlQuery, $newInfo)
  {
    $stmt = $this->dbh->prepare($sqlQuery);
    $stmt->bindParam(':pid', $this->_projectDefaults->getProjectID());
    $stmt->bindParam(':did', $newInfo);

    try{
      if($stmt->execute())
        return true;
      else
        return false;
    }
    catch(PDOException $e)
    {
      JSONResponse::printErrorResponseWithHeader("Unable to add or update role to object mapping - fatal database error: ".$e);
    }

  }
  
  public function saveNewProjectDefaults($everyonePerms, $defaultRole)
  {     
    $stmt = $this->dbh->prepare("INSERT INTO `PermProjectDefaults` (id, projectid, everyonepermissions, defaultroleid) VALUE (NULL, :pid, :eid, :rid)");
    $stmt->bindParam(':pid', $this->_projectDefaults->getProjectID());
    $stmt->bindParam(':eid', $everyonePerms);
    $stmt->bindParam(':rid', $defaultRole);

    try{
      if($stmt->execute())
        return true;
      else
        return false;
    }
    catch(PDOException $e)
    {
      JSONResponse::printErrorResponseWithHeader("Unable to add or update role to object mapping - fatal database error: ".$e);
    }
  }

  public function updateProjectDefaultsEveryonePermission($newEveryonePerms)
  {
      return $this->save("UPDATE `PermProjectDefaults` SET `everyonepermissions` = :did WHERE `projectid` = :pid", $newRoleID); 
  } 
  
  public function updateProjectDefaultsRoleID($newDefaultRole)
  {
      return $this->save("UPDATE `PermProjectDefaults` SET `defaultroleid` = :did WHERE `projectid` = :pid", $newDefaultRole); 
  }  
}

/* This is the model stored in the DB too */
class PermAssignmentMappingsModel
{
   /* From the PermRolesToObjectMapping Table */
  protected $_ID;
  protected $_userID;
  protected $_projectID;
  protected $_roleMappingID;

  public function __construct($projectID, $userID, $roleMappingID, $tablePrimaryKey = NULL)
  {
    $this->_ID = intval($tablePrimaryKey); /* Means we have not spoken to the database */
    $this->_userID = intval($userID);
    $this->_projectID = intval($projectID);
    $this->_roleMappingID = intval($roleMappingID);
  }

  public function getID()
  {
    return $this->_ID;
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
  private $_projectID;
  private $_userID;
  
  public function __construct($projectContext, $userID)
  {
    $this->_projectID = intval($projectContext);
    $this->_userID = intval($userID);
    
    parent::__construct();
  
    if($projectContext > -1)
    {
      $stmt = $this->dbh->prepare("SELECT * FROM `PermAssignmentMappings` WHERE `projectid` = :pid AND `userID` = :uid");    
  
      $stmt->bindParam(':pid', $projectContext);
      $stmt->bindParam(':uid', $userID);
      
      try
      {
        if($stmt->execute()){
          $possibleMatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
          
          if(count($possibleMatches) < 1)
            $this->_permAssignmentModel = NULL;
          
          if(count($possibleMatches) > 1)
            JSONResponse::printErrorResponseWithHeader("Fatal: this project has more than one default!");
            
          if(count($possibleMatches) == 1)
          {            
            $this->_permAssignmentModel = new PermAssignmentMappingsModel($projectContext, $possibleMatches[0]['projectid'], $possibleMatches[0]['id'], $possibleMatches[0]['role']); 
            return $this;
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
    
  public function getModel()
  {
    return $this->_permAssignmentModel;
  }        

  private function save($sqlQuery, $roleID)
  {
    $stmt = $this->dbh->prepare($sqlQuery);
    $stmt->bindParam(':uid', $this->_userID);
    $stmt->bindParam(':pid', $this->_projectID);
    $stmt->bindParam(':rid', $roleID); 

    try{
      if($stmt->execute())
        return true;
      else
        return false; 
    }
    catch(PDOException $e)
    {
      JSONResponse::printErrorResponseWithHeader("Unable to add or update role to object mapping - fatal database error: ".$e);
    }

  }
  
  public function addAssignmentToRole($newRoleID)
  {
      return $this->save("INSERT INTO `PermAssignmentMappings` (id, userid, projectid, role) VALUE (NULL, :uid, :pid, :rid)", $newRoleID);
  }
  
  public function alterAssignmentForRole($roleID)
  {
      return $this->save("UPDATE `PermAssignmentMappings` SET `role` = :rid WHERE `projectid` = :pid AND `userID` = :uid", $newRoleID); 
  } 
  
  public function deleteMapping()
  {
    if($this->_permAssignedModel == NULL)
      return false;
    
    $stmt = $this->dbh->prepare("DELETE FROM `PermAssignmentMappings` WHERE `projectid` = :pid AND `userID` = :uid");     
    $stmt->bindParam(':uid', $this->_userID);
    $stmt->bindParam(':pid', $this->_projectID);

    try{
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
      JSONResponse::printErrorResponseWithHeader("Unable to add or update role to object mapping - fatal database error: ".$e);
    }
  }
}
