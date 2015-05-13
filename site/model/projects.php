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
    $genericSearchSingle = $this->dbh->prepare("SELECT ID, user_id, name, '100%' FROM `Projects` WHERE `user_id` = :me");
    $genericSearchSingle->bindParam(':me', $userID);
    $genericSearchSingle->execute();
    
    $rs = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
    
    $count = array("count"=>count($rs));
    if($count["count"] > 0)
      array_push($count, $rs);
    
    return $count[0];
  }  
  
  public function retrieveAllProjectInSystem($authorisedID, $code = NULL)
  {
    /* Our controller will check and pass us a code for checking in permissions later.
      For now, we can simply move onto returning the requested items.
    */
    $genericSearchSingle = $this->dbh->prepare("SELECT ID, user_id, name, '100%' FROM `Projects`");
    $genericSearchSingle->execute();
    
    $rs = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
    
    $count = array("count"=>count($rs));
    if($count["count"] > 0)
      array_push($count, $rs);
    
    return $count[0];
  }
  
  public function saveProject($title, $userID, $dateStart, $dateEnd, $estimated_budget, $estimated_time, $description, $managedBy = NULL)
  {
    $createQuery = $this->dbh->prepare("INSERT INTO `Projects` (ID, user_id, create_date, status, estimate_budget, estimate_time, name, description) VALUES (NULL, :uid, :dcreate, 'Open', :ebud, :etime, :name, :desc) ");
    
    $createQuery->bindParam(':uid', $userID);
    $createQuery->bindParam(':dcreate', $dateStart);
    $createQuery->bindParam(':ebud', $estimated_budget);
    $createQuery->bindParam(':etime', $estimated_time);
    $createQuery->bindParam(':name', $title);
    $createQuery->bindParam(':desc', $description);  
    
    try
    {
      if($createQuery->execute()){
        $pid = $this->dbh->lastInsertId('ID');
        $manageQuery = $this->dbh->prepare("INSERT INTO `ProjectManagers` (ID, project_id, user_id) VALUES (NULL, :pid, :uid)");
        
        $manageQuery->bindParam(':pid', $pid);
        $manageQuery->bindParam(':uid', $userID);
        
        /* We MUST assign at least the calling user a manager to a project. */
        try
        {
          if($manageQuery->execute())
          {
            /* Created at least 1 manager; move onto creating the others */
            if($managedBy === NULL)
              return array("success"=>true,"newPID"=>$pid);
              
            if(!is_array($managedBy))
              JSONResponse::printErrorResponseWithHeader("Unable to create project - supplied managers for project was not an array. Please contact your support team.");  
              
            /* We now enumerate through the added managers.
              TODO: Check all managers exist first so we can have no error messages being returned
            */ 
            $managersPreQuery = "INSERT INTO `ProjectManagers` (ID, project_id, user_id) VALUES ";
            $managersValuesQuery = array_fill(0, count($managedBy), "(NULL, ?, ?)");
            $managersPreQuery .= implode(',', $managersValuesQuery);
            
            $managersQuery = $this->dbh->prepare($managersPreQuery);
            $i = 1;
            foreach($managedBy as $manager)
            {
              $managersQuery->bindValue($i++, $pid);
              $managersQuery->bindValue($i++, $manager);
            }
                        
            if($managersQuery->execute())
              return array("success"=>true,"newPID"=>$pid);
            else {
              #TODO: Create a list of common error codes and appropriate error messages.
              
              if($managersQuery->errorInfo()[1] == 1452)
                $details = "Project created, however one or more users could not be found.";
              
              return array("success"=>false,"newPID"=>$pid,"error"=>$details);
            }
          }
        } catch(PDOException $e)
        {
          JSONResponse::printErrorResponseWithHeader("Unable to create project managers - creation of primary owner failed.");
        }
      }
      else
      {
        return false;
      }
    } catch(PDOException $e)
    {
      JSONResponse::printErrorResponseWithHeader("Unable to create project - database error: ".$e);
    }
    return false;
  }
  
  public function updateProject()
  {
    
  }
}