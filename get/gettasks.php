<?php  
class TaskDB extends DatabaseAdaptor
{    
  public function retrieveTaskWithID($id)
  {
    $genericSearchSingle = $this->dbh->prepare("SELECT * FROM `Tasks` WHERE `id` = :id");
    $genericSearchSingle->bindParam(':id', filter_input(INPUT_POST, 'id',FILTER_SANITIZE_NUMBER_INT));
    $genericSearchSingle->execute();
    
    $rs = $genericSearchSingle->fetchAll();
      if(count($rs) == 1)
      {
        return $rs;
      } else {
        return false;
      }
        
  }
  
  public function retrieveTasksAssignedToUser($userID)
  {
    $genericSearchSingle = $this->dbh->prepare("SELECT * FROM `Tasks` WHERE `assignee` = :me");
    $id = filter_input(INPUT_POST, 'userId',FILTER_SANITIZE_NUMBER_INT);
    $genericSearchSingle->bindParam(':me', $id);
    $genericSearchSingle->execute();
    
    $rs = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
    
    $count = array("count"=>count($rs));
    if($count["count"] > 0)
      array_push($count, $rs);
    
    return $count[0];
  }  
  
  public function saveTask()
  {
    
  }
  
  public function updateTask()
  {
    
  }
}