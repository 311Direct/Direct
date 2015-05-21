<?php
  
class PermissionObject
{
  public $_id;
  public $_userid;
  public $_table;
  public $_oid;
  public $_value;
  
  public function __construct($id, $uid, $tid, $oid, $value)
  {
    $this->_id = $id;
    $this->_userid = $uid;
    $this->_table = $tid;
    $this->_oid = $oid;
    $this->_value = $value; 
  }

  public function getID()
  {
    return $this->_id;
  }
    
  public function getUserID()
  {
    return $this->_userID;
  }
  
  public function getTable()
  {
    return $this->_table;
  }
  
  public function getOID()
  {
    return $this->_oid;
  }
  
  public function getValue()
  {
    return $this->_value;
  }   
}


function checkTableValue($value)
{
  $value = strtolower($value);
  if($value == 'o' || $value == 'r' || $value == 'project' || $value  == 'attachement' || $value == 'comment' || $value == 'milestone' || $value == 'notification' || $value == 'projectmanager' || $value == 'projects' || $value == 'tasks' || $value == 'taskdeps')
  {
    return true;
  }
  else
  {
    return false;
  }
}

class PermissionsObjectMapper extends DatabaseAdaptor
{
  
  private $_permObjs;
  
  public function __construct($userID, $tableName, $objectID, $getAll = false)
  {
    parent::__construct();

    if($getAll)
      $stmt = $this->dbh->prepare("SELECT * FROM `PermObjectsMappings` WHERE `table` = :table AND `oid` = :oid");
    else
      $stmt = $this->dbh->prepare("SELECT * FROM `PermObjectsMappings` WHERE `userid` = :uid AND `table` = :table AND `oid` = :oid");
    
    if(!checkTableValue($tableName))
      JSONResponse::printErrorResponseWithHeader("Unable to load Object ID Table for this transaction. Aborting operation...");
    
    $stmt->bindParam(':uid', $userID);
    $stmt->bindParam(':table', $tableName);
    $stmt->bindParam(':oid', $objectID);
    
    try
    {
      if($stmt->execute()){
        foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $object)
        {
          $newObj = new PermissionObject($object['id'], $object['userid'], $object['table'], $object['oid'], intval($object['value']));
          $this->_permObjs[] = $newObj;
        }
      }
      else
      {
        JSONResponse::printErrorResponseWithHeader("Unable to load Roles for this transaction. Aborting operation...\n".$stmt->errorCode());
      }    
    }
    catch(PDOException $e)
    {
      JSONResponse::printErrorResponseWithHeader("Unable to load roles - fatal database error: ".$e);
    }
  }
  
  public function getPermissionObjects()
  {
    return $this->_permObjs;
  }
  
  public function updateObjectPermission($newPermissions)
  {
    if(noAction($newPermissions) || (($newPermissions & P_ACTION_ADD) || ($newPermissions & P_ACTION_DELETE)))
      return false;
      
    /* We only allow for MODIFYING when we update an object mapping's permission */
   
    /* Modifying a new role */
    if($newPermissions & P_ACTION_UPDATE){
      
      $stmt = $this->dbh->prepare("UPDATE `PermObjectsMappings` SET `value` = :vid WHERE `id` = :id AND `projectid` = :pid");
      
      $stmt->bindValue(':value', ($newPermissions->getValue() & ~P_ACTION_UPDATE));
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
  
  public function alterObjectMapping($newPermissions)
  {
    if(noAction($newPermissions) || ($newPermissions & P_ACTION_UPDATE))
      return false;
      
    /* ADD a new permission */
    if(($newPermissions & P_ACTION_ADD) == P_ACTION_ADD){
      $stmt = $this->dbh->prepare("INSERT INTO `PermObjectsMappings` VALUES (NULL, :uid, :pid, :rid)");
      $stmt->bindValue(':rid', intval($newPermissions->getID()));
    }
    
    /* DELETE an existing permission */
    if(($newPermissions & P_ACTION_DELETE) == P_ACTION_DELETE)
      $stmt = $this->dbh->prepare("DELETE FROM `PermObjectsMappings` WHERE `projectid` = :pid AND `userid` = :uid"); 
    
    /* We DO NOT allow for modification of an OID mapping */    
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
  
}