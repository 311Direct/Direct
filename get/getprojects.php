<?php  
class ProjectDB extends DatabaseAdaptor
{    
  public function retrieveProjectWithID($id)
  {
    $genericSearchSingle = $this->dbh->prepare("SELECT * FROM `Projects` WHERE `id` = :id");
    $genericSearchSingle->bindParam(':id', $id);
    $genericSearchSingle->execute();
    
    $rs = $genericSearchSingle->fetchAll();
      if(count($rs) == 1)
      {
        return $rs[0];
      } else {
        return false;
      }
        
  }
  
  public function retrieveProjectsAssignedToUser($userID)
  {
    $genericSearchSingle = $this->dbh->prepare("SELECT * FROM `Tasks` WHERE `assignee` = :me");
    $id = filter_input(INPUT_POST, 'userId',FILTER_SANITIZE_NUMBER_INT);
    $genericSearchSingle->bindParam(':me', $id);
    $genericSearchSingle->execute();
    
    $rs = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
    
    $count = array("count"=>count($rs));
    if($count["count"] > 0)
      array_push($count, $rs);
    
    return $count[0][0];
  }  
  
  public function saveTask()
  {
    
  }
  
  public function updateTask()
  {
    
  }
}