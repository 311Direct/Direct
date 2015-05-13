<?php  
class MilestoneDB extends DatabaseAdaptor
{    
  public function retrieveMilestoneWithID($id)
  {
    $genericSearchSingle = $this->dbh->prepare("SELECT * FROM `Milestones` WHERE `id` = :id");
    $genericSearchSingle->bindParam(':id', $id);
    $genericSearchSingle->execute();
    $rs = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
    if(count($rs) == 1)
    {
        return $rs[0];
    } else {
        return false;
    }
  }
  
  public function retrieveMilestonesCreatedByUser($userID)
  {
    //TODO: review the database structure milestone
    $genericSearchSingle = $this->dbh->prepare("SELECT * FROM `Milestones` WHERE `assignee` = :me");
    $genericSearchSingle->bindParam(':me', $userID);
    $genericSearchSingle->execute();
    
    $rs = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
    
    $count = array("count"=>count($rs));
    if($count["count"] > 0)
      array_push($count, $rs);
    
    return $count[0];
  }
  
  public function saveMilestone($title, $projectID, $milestoneID, $userID, $priority, $allocated_budget, $allocated_time, $dueDate, $flags, $description, $subMilestoneIds, $parentID,$assignees = NULL)
  {
    $createQuery = $this->dbh->prepare("INSERT INTO `Milestones` (project_id, name, status, estimate_budget, estimate_time, description) VALUES	(, '', '', , , '')");
    
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
      JSONResponse::printErrorResponseWithHeader("Unable to create Milestone - fatal database error: ".$e);
    }
    return false;
  }
  
  public function updateMilestone($title = NULL, $projectID = NULL, $priority = NULL, $allocated_budget = NULL, $allocated_time = NULL, $dueDate = NULL, $flags = NULL, $description = NULL, $subMilestoneIds = NULL, $parent_id= NULL)
  {
    
  }
}