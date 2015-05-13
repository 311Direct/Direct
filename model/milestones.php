<?php  
class MilestoneDB extends DatabaseAdaptor
{    
  public function retrieveMilestoneWithID($id)
  {
    $genericSearchSingle = $this->dbh->prepare("SELECT milestones.name AS title, milestones.user_id AS creatorUserId, 
        u1.displayname AS creatorDisplayName, milestones.create_date AS createDate, projects.id, milestones.manager_id AS managerUserId,
        u2.displayname AS managerDisplayName, milestones.status, milestones.estimate_budget AS allocatedBudget, milestones.real_budget AS usedBudget, 
        milestones.estimate_time AS allocatedTime, milestones.real_time AS usedTime, milestones.description FROM milestones join projects on milestones.project_id = projects.id
        join users u1 on milestones.user_id = u1.id join users u2 on milestones.manager_id = u2.id WHERE milestones.id =:id");
    $genericSearchSingle->bindParam(':id', $id);
    $genericSearchSingle->execute();
    
    $rs = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
    
    $genericSearchSingle = $this->dbh->prepare("SELECT Tasks.id, priority, taskTitle AS title, projects.id AS projectId, Tasks.status FROM Tasks 
        JOIN milestones ON Tasks.milestone_id = milestones.id 
        JOIN projects on milestones.project_id = projects.id WHERE milestones.id = :id");
    $genericSearchSingle->bindParam(':id', $id);
    $genericSearchSingle->execute();
    
    $rs_tasks = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
    if(count($rs_tasks) >= 1) {
        $rs[0]['tasks'] = $rs_tasks;
    } else {
        $rs[0]['tasks'] = null;
    }
    
    $genericSearchSingle = $this->dbh->prepare("SELECT username, displayname AS displayName FROM 
        milestones JOIN Projects ON milestones.project_id = Projects.id 
                     JOIN tasks on milestones.id = tasks.milestone_id 
						JOIN users on tasks.assignee = users.id WHERE milestones.id = :id group by username");
    $genericSearchSingle->bindParam(':id', $id);
    $genericSearchSingle->execute();
    
    $rs_users = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
    if(count($rs_users) >= 1) {
        $rs[0]['users'] = $rs_users;
    } else {
        $rs[0]['users'] = null;
    }
    
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
  
  public function saveMilestone($userId, $title, $projectId, $allocB, $allocT, $dCreate, $desc)
  {
    $createQuery = $this->dbh->prepare("INSERT INTO `Milestones` 
        (user_id, manager_id, project_id, name, status, estimate_budget, estimate_time, description, created_date) 
        VALUES	(:userId, :userId, :projectId, :title, 'Open', :allocB, :allocT, :desc, :dcreate)");
    
    $createQuery->bindParam(":userId", $userId);
    $createQuery->bindParam(":projectId", $projectId);
    $createQuery->bindParam(":title", $title);
    $createQuery->bindParam(":allocB", $allocB);
    $createQuery->bindParam(":allocT", $allocT);
    $createQuery->bindParam(":dcreate", $dCreate);
    $createQuery->bindParam(":desc", $desc);

    try
    {
      if($createQuery->execute()){
        $pid = $this->dbh->lastInsertId('id');
        return array("success"=>true,"newMID"=>$pid);
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
  
  public function updateMilestone($id, $status, $title, $projectId, $allocB, $allocT, $desc)
  {
      $updateQuery = $this->dbh->prepare("Update Milestones 
          set project_id = :projectId, 
              name = :title, 
              status = :status, 
              estimate_budget = :allocB, 
              estimate_time = :allocT, 
              description = :desc
          WHERE id = :id");
      
      $updateQuery->bindParam(":id", $id);
      $updateQuery->bindParam(":status", $status);
      $updateQuery->bindParam(":projectId", $projectId);
      $updateQuery->bindParam(":title", $title);
      $updateQuery->bindParam(":allocB", $allocB);
      $updateQuery->bindParam(":allocT", $allocT);
      $updateQuery->bindParam(":desc", $desc);

      try
      {
        if($updateQuery->execute()){
          return array("success"=>true,"newMID"=>$id);
        }
        else
        {
          return array("success"=>false,"error"=>$updateQuery->errorInfo());
        }    
      }
      catch(PDOException $e)
      {
        JSONResponse::printErrorResponseWithHeader("Unable to update Milestone - fatal database error: ".$e);
      }
      return false;
  }
}