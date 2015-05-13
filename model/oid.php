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
  
  public function __construct($userID, $tableName, $objectID)
  {
    parent::__construct();

    $stmt = $this->dbh->prepare("SELECT * FROM `PermObjects` WHERE `userid` = :uid AND `table` = :table AND `oid` = :oid");
    
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
          return $this->_permObjs;
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
  
}