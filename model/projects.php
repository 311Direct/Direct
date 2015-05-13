<?php  
class ProjectDB extends DatabaseAdaptor
{    
  public function retrieveProjectWithID($id)
  {
    $genericSearchSingle = $this->dbh->prepare("SELECT projectTitle AS title, users.username AS userId, 
        create_date AS createDate, date_start AS dateStart, date_expected_finish AS dateExpectedFinish, 
        status, estimate_budget AS allocatedBudget, real_budget AS usedBudget, estimate_time AS allocatedTime, 
        real_time AS usedTime, projects.description FROM Projects JOIN users on projects.projectManager = users.id WHERE projects.id = :id");
    $genericSearchSingle->bindParam(':id', $id);
    $genericSearchSingle->execute();
    
    $rs = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
    
    $genericSearchSingle = $this->dbh->prepare("SELECT filepath AS url, filename AS title, type from Attachements join projects on projects.id = Attachements.project_id where projects.id = :id");
    $genericSearchSingle->bindParam(':id', $id);
    $genericSearchSingle->execute();
    
    $rs_attachements = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
    if(count($rs_attachements) >= 1) {
        $rs[0]['attachments'] = $rs_attachements;
    } else {
        $rs[0]['attachments'] = null;
    }
    
    $genericSearchSingle = $this->dbh->prepare("SELECT milestones.id, milestones.name AS title, milestones.status AS progress from milestones join projects on projects.id = milestones.project_id where projects.id = :id");
    $genericSearchSingle->bindParam(':id', $id);
    $genericSearchSingle->execute();
    
    $rs_milestones = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
    if(count($rs_milestones) >= 1) {
        $rs[0]['milestones'] = $rs_milestones;
    } else {
        $rs[0]['milestones'] = null;
    }
    
    $genericSearchSingle = $this->dbh->prepare("SELECT Tasks.id, priority, taskTitle AS title, Tasks.status, users.username FROM Tasks JOIN Projects ON Tasks.project_id = Projects.id JOIN users on tasks.assignee = users.id WHERE projects.id = :id");
    $genericSearchSingle->bindParam(':id', $id);
    $genericSearchSingle->execute();
    
    $rs_tasks = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
    if(count($rs_tasks) >= 1) {
        $rs[0]['tasks'] = $rs_tasks;
    } else {
        $rs[0]['tasks'] = null;
    }
    
    $genericSearchSingle = $this->dbh->prepare("SELECT username, displayname AS displayName FROM Tasks JOIN Projects ON Tasks.project_id = Projects.id JOIN users on tasks.assignee = users.id WHERE projects.id = :id group by username");
    $genericSearchSingle->bindParam(':id', $id);
    $genericSearchSingle->execute();
    
    $rs_users = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
    if(count($rs_users) >= 1) {
        $rs[0]['users'] = $rs_users;
    } else {
        $rs[0]['users'] = null;
    }
    
    $genericSearchSingle = $this->dbh->prepare("SELECT username, displayname AS displayName, datetime, comment from Comments join users on comments.user_id = users.id where engagement_id = :id and engagement_table = 'Projects'");
    $genericSearchSingle->bindParam(':id', $id);
    $genericSearchSingle->execute();
    
    $rs_comments = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
    if(count($rs_comments) >= 1) {
        $rs[0]['comments'] = $rs_comments;
    } else {
        $rs[0]['comments'] = null;
    }
    
    if(count($rs) == 1) {
        return $rs;
    } else {
        return false;
    }    
  }
  
  public function retrieveProjectsAssignedToUser($user_id)
  {
    $genericSearchSingle = $this->dbh->prepare("SELECT Projects.id, username AS manager, projectTitle AS title, CONCAT(Round((real_time/estimate_time)*100),'%')AS progress  FROM Projects JOIN Users ON Projects.projectManager = Users.id WHERE projectManager = :me");
    $genericSearchSingle->bindParam(':me', $user_id);
    $genericSearchSingle->execute();
    
    $rs = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
    if(count($rs) >= 1) {
        return $rs;
    } else {
        return false;
    }
  }  
  
  public function retrieveAllProjectInSystem($authorisedID, $code = NULL)
  {
    /* Our controller will check and pass us a code for checking in permissions later.
      For now, we can simply move onto returning the requested items.
    */
    $genericSearchSingle = $this->dbh->prepare("SELECT id, projectManager AS manager, projectTitle AS title, CONCAT(Round((real_time/estimate_time)*100),'%')AS progress FROM Projects");
    $genericSearchSingle->execute();
    
    $rs = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
    if(count($rs) >= 1) {
        return $rs;
    } else {
        return false;
    }
  }
  
  public function saveProject($title, $userID, $dateStart, $dateEnd, $estimated_budget, $estimated_time, $description)
  {
    $createQuery = $this->dbh->prepare("INSERT INTO `Projects` 
        (projectManager, create_date, status, estimate_budget, estimate_time, projectTitle, 
            description, date_start, date_expected_finish) 
	           VALUES (:uid, NOW(), 'Open', :ebud, :etime, :projectTitle, :description, :dcreate, :dend)");
    
    $createQuery->bindParam(':uid', $userID);
    $createQuery->bindParam(':ebud', $estimated_budget);
    $createQuery->bindParam(':etime', $estimated_time);
    $createQuery->bindParam(':projectTitle', $title);
    $createQuery->bindParam(':description', $description);  
    $createQuery->bindParam(':dcreate', $dateStart);
    $createQuery->bindParam(':dend', $dateEnd);
    
    try
    {
        $createQuery->execute();
        $pid = $this->dbh->lastInsertId();
        return array("success"=>true,"newPID"=>$pid);
        
        //   if($createQuery->execute()){
        //     $pid = $this->dbh->lastInsertId('ID');
        //     $manageQuery = $this->dbh->prepare("INSERT INTO `ProjectManagers` (ID, project_id, user_id) VALUES (NULL, :pid, :uid)");
        //     
        //     $manageQuery->bindParam(':pid', $pid);
        //     $manageQuery->bindParam(':uid', $userID);
        //     
        //     /* We MUST assign at least the calling user a manager to a project. */
        //     try
        //     {
        //       if($manageQuery->execute())
        //       {
        //         /* Created at least 1 manager; move onto creating the others */
        //         if($managedBy === NULL)
        //           return array("success"=>true,"newPID"=>$pid);
        //           
        //         if(!is_array($managedBy))
        //           JSONResponse::printErrorResponseWithHeader("Unable to create project - supplied managers for project was not an array. Please contact your support team.");  
        //           
        //         /* We now enumerate through the added managers.
        //           TODO: Check all managers exist first so we can have no error messages being returned
        //         */ 
        //         $managersPreQuery = "INSERT INTO `ProjectManagers` (ID, project_id, user_id) VALUES ";
        //         $managersValuesQuery = array_fill(0, count($managedBy), "(NULL, ?, ?)");
        //         $managersPreQuery .= implode(',', $managersValuesQuery);
        //         
        //         $managersQuery = $this->dbh->prepare($managersPreQuery);
        //         $i = 1;
        //         foreach($managedBy as $manager)
        //         {
        //           $managersQuery->bindValue($i++, $pid);
        //           $managersQuery->bindValue($i++, $manager);
        //         }
        //                     
        //         if($managersQuery->execute())
        //           return array("success"=>true,"newPID"=>$pid);
        //         else {
        //           #TODO: Create a list of common error codes and appropriate error messages.
        //           
        //           if($managersQuery->errorInfo()[1] == 1452)
        //             $details = "Project created, however one or more users could not be found.";
        //           
        //           return array("success"=>false,"newPID"=>$pid,"error"=>$details);
        //         }
        //       }
        //     } catch(PDOException $e)
        //     {
        //       JSONResponse::printErrorResponseWithHeader("Unable to create project managers - creation of primary owner failed.");
        //     }
        //   }
        //   else
        //   {
        //     return false;
        //   }
        
    } catch(PDOException $e)
    {
      JSONResponse::printErrorResponseWithHeader("Unable to create project - database error: ".$e);
    }
    return false;
  }
  
  public function updateProject($id, $status, $title, $userID, $dateStart, $dateEnd, $estimated_budget, $estimated_time, $description)
  {
      $updateQuery = $this->dbh->prepare("Update Projects 
          set projectManager = :uid, 
              status = :status, 
              estimate_budget = :ebud, 
              estimate_time = :etime, 
              projectTitle = :projectTitle, 
              description = :description, 
              date_start = :dcreate, 
              date_expected_finish = :dend
  	      where id = :id");
      
      $updateQuery->bindParam(':id', $id);
      $updateQuery->bindParam(':uid', $userID);
      $updateQuery->bindParam(':status', $status);
      $updateQuery->bindParam(':ebud', $estimated_budget);
      $updateQuery->bindParam(':etime', $estimated_time);
      $updateQuery->bindParam(':projectTitle', $title);
      $updateQuery->bindParam(':description', $description);  
      $updateQuery->bindParam(':dcreate', $dateStart);
      $updateQuery->bindParam(':dend', $dateEnd);
      
      try
      {
          if($updateQuery->execute())
          {
              return array("success"=>true,"PID"=>$id);
          }
      } catch(PDOException $e)
      {
        JSONResponse::printErrorResponseWithHeader("Unable to update project - database error: ".$e);
      }
      return false;
  }
  
  public function addAttachement($projectId, $taskId, $userId, $fileUrl)
  {
      $createQuery = $this->dbh->prepare("INSERT INTO Attachements (project_id, task_id, filepath, upload_user_id)
                VALUES (:projectId, :taskId, :fileUrl, :userId)");
      $createQuery->bindParam(':projectId', $projectId);
      $createQuery->bindParam(':taskId', $taskId);
      $createQuery->bindParam(':fileUrl', $fileUrl);
      $createQuery->bindParam(':userId', $userId);
      try
      {
          if($createQuery->execute())
          {
              $pid = $this->dbh->lastInsertId();
              return array("success"=>true,"newPID"=>$pid);
          }
      } catch(PDOException $e)
      {
        JSONResponse::printErrorResponseWithHeader("Unable to add attachement - database error: ".$e);
      }
      return false;
  }
  
  public function addComment($projectId, $userId, $comment)
  {
      $createQuery = $this->dbh->prepare("INSERT INTO Comments (engagement_id, engagement_table, user_id, comment, datetime)
                VALUES (:projectId, 'Projects', :userId, :comment, NOW())");
                
      $createQuery->bindParam(':projectId', $projectId);
      $createQuery->bindParam(':userId', $userId);
      $createQuery->bindParam(':comment', $comment);
      
      try
      {
          if($createQuery->execute())
          {
              $pid = $this->dbh->lastInsertId();
              return array("success"=>true,"newPID"=>$pid);
          }
      } catch(PDOException $e)
      {
        JSONResponse::printErrorResponseWithHeader("Unable to add comment - database error: ".$e);
      }
      return false;
  }
}