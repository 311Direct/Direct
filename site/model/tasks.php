<?php  
class TaskDB extends DatabaseAdaptor
{    
  public function retrieveTaskWithID($id)
  {
    $genericSearchSingle = $this->dbh->prepare("SELECT * FROM `Tasks` WHERE `id` = :id");
    $genericSearchSingle->bindParam(':id', $id);
    $genericSearchSingle->execute();
    
    $rs = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
    
    $genericSearchSingle = $this->dbh->prepare("SELECT ID, prioity, name, assignee, status FROM `Tasks` WHERE `parent_task_id` = :id");
    $genericSearchSingle->bindParam(':id', $id);
    $genericSearchSingle->execute();
    
    $rs_sub_tasks = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
    
    $rs[0]["subtasks"] = $rs_sub_tasks;
      if(count($rs) == 1)
      {
        return $rs[0];
      } else {
        return false;
      }
        
  }
  
  public function retrieveTasksWithMilestone($id)
  {
    $genericSearchSingle = $this->dbh->prepare("SELECT ID, prioity, name, project_id, status FROM `Tasks` WHERE `milestone_id` = :id");
    $genericSearchSingle->bindParam(':id', $id);
    $genericSearchSingle->execute();
    
    $rs = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
      if(count($rs) > 0)
      {
        return $rs;
      } else {
        return false;
      }
  }
  
  public function retrieveUsersWithMilestone($id)
  {
    $genericSearchSingle = $this->dbh->prepare("SELECT username, displayname, id from `Users` WHERE `ID` in (SELECT assignee FROM `Tasks` WHERE `milestone_id` = :id)");
    $genericSearchSingle->bindParam(':id', $id);
    $genericSearchSingle->execute();
    
    $rs = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
      if(count($rs) > 0)
      {
        return $rs;
      } else {
        return false;
      }
  }
  
  public function retrieveTasksAssignedToUser($userID)
  {
    $genericSearchSingle = $this->dbh->prepare("SELECT ID, prioity, name, project_id, status FROM `Tasks` WHERE `assignee` = :me");
    $genericSearchSingle->bindParam(':me', $userID);
    $genericSearchSingle->execute();
    
    $rs = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
    
    $count = array("count"=>count($rs));
    if($count["count"] > 0)
      array_push($count, $rs);
    
    return $count[0];
  }  
  
  public function saveTask($title, $projectID, $milestoneID, $userID, $priority, $allocated_budget, $allocated_time, $dueDate, $flags, $description, $subTaskIds, $parentID,$assignees = NULL)
  {
    $createQuery = $this->dbh->prepare("INSERT INTO `Tasks` (ID, project_id, milestone_id, parent_task_id, name, assignee, prioity, status, estimate_budge, estimate_time, due_date, flags, description, create_date) VALUES	(NULL, :pid, :mid, :dep, :name, :uid, :priority, 'Open', :ebud, :etime, :ddate, :flags, :desc, NOW())");
    $lol = "lol";
    $createQuery->bindParam(":pid", $projectID);
    $createQuery->bindParam(":uid", $userID);
    $createQuery->bindParam(":mid", $milestoneID);
    $createQuery->bindParam(":dep", $parentID);
    $createQuery->bindParam(":name", $title);
    $createQuery->bindParam(":priority", $priority);
    $createQuery->bindParam(":ebud", $allocated_budget);
    $createQuery->bindParam(":etime", $allocated_time);
    $createQuery->bindParam(":ddate", $dueDate);
    $createQuery->bindParam(":desc", $description);
    $createQuery->bindParam(":flags",$lol);
    
    /* At this point, we will not check if our projects exist. Simply process the error and return.
       Same goes for our milestones; we need to create a generic checking class to reduce
       inter-class dependencies.
     */
     
    try
    {
      if($createQuery->execute()){
        $pid = $this->dbh->lastInsertId('ID');
        return array("success"=>true,"newPID"=>$pid);
      }
      else
      {
        return array("success"=>false,"error"=>$createQuery->errorInfo());
      }    
    }
    catch(PDOException $e)
    {
      JSONResponse::printErrorResponseWithHeader("Unable to create task - fatal database error: ".$e);
    }
    return false;
  }
  
  public function updateTask($title = NULL, $projectID = NULL, $priority = NULL, $allocated_budget = NULL, $allocated_time = NULL, $dueDate = NULL, $flags = NULL, $description = NULL, $subTaskIds = NULL, $parent_id= NULL)
  {
    
  }
}