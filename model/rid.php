<?php
  
  class RoleObject
  {
    protected $_id;
    protected $_userID;
    protected $_projectID;
    protected $_role;
    protected $_value;
    
    public function __construct($id, $userID, $projectID, $roleName, $value)
    {
      $this->_id = $id;
      $this->_userID = $userID;
      $this->_projectID = $projectID;
      $this->_role = $roleName;
      $this->_value = intval($value);
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
    
    public function getRole()
    {
      return $this->_role;
    }
    
    public function value()
    {
      return $this->_value;
    }    
}


class RolesObjectMapper extends DatabaseAdaptor
{
  
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
          return false;
          
        if(count($possibleMatches) == 1)
        {
          $roleID = $possibleMatches[0]['role'];
          $rstmt = $this->dbh->prepare("SELECT * FROM `Roles` WHERE `id` = :id");
          $rstmt->bindParam(':id', $roleID);
          
          try
          {
            if($rstmt->execute())
            {
              $rs = $rstmt->fetch(PDO::FETCH_ASSOC); 
              $q = new RoleObject($rs['id'], $userID, $projectID, $rs['rolename'], $rs['priv_bit_mask']);
              var_dump($q);
            }
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
    
}