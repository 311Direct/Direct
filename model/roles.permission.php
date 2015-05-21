<?php
  
class RoleInformationModel
{
  /* From the Roles table */
  protected $_id;
  protected $_projectID;
  protected $_roleName;
  protected $_value;
  
  public function __construct($projectID, $roleName, $permissionsValue, $id = NULL)
  {
    $this->_id = $id;
    $this->_projectID = $projectID;
    $this->_roleName = $roleName;
    $this->_value = $permissionsValue;
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
    return $this->_roleName;
  }
  
  public function getPermissionsValue()
  {
    return $this->_value;
  }
}


class DBRoleInformationModel extends DatabaseAdaptor
{
  private $_roleInfo;
  
  public function __construct($tablePrimaryKey, $projectID)
  {    
    parent::__construct();
  
    if($projectContext > 0)
    {
      $stmt = $this->dbh->prepare("SELECT * FROM `Roles` WHERE `id` = :guid AND `projectid` = :uid");    
  
      $stmt->bindParam(':guid', $tablePrimaryKey);
      $stmt->bindParam(':uid', $userID);
      
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
            $this->_permAssignmentModel = new RoleInformationModel($projectContext, intval($possibleMatches[0][`rolename`]), intval($possibleMatches[0][`priv_bit_mask`]), intval($possibleMatches[0][`id`]));
            return;         
          }        
          JSONResponse::printErrorResponseWithHeader("DBRoleInformationModel experienced an internal error."); 
        }
      }
      catch(PDOException $e)
      {
        JSONResponse::printErrorResponseWithHeader("Unable to load roles - fatal database error: ".$e);
      }      
    }        
  } }