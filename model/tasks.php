<?php  
class TaskDB extends DatabaseAdaptor
{    
    public function retrieveTaskWithID($id)
    {
        $genericSearchSingle = $this->dbh->prepare("SELECT taskTitle AS title, project_id, projects.projectTitle, 
            assignee, users.displayname AS displayName, priority, tasks.status, tasks.status, tasks.estimate_budget AS allocatedBudget, tasks.real_budget AS usedBudget, 
            tasks.estimate_time AS allocatedTime, tasks.real_time AS usedTime, due_date AS dueDate, flags, tasks.description
             FROM tasks JOIN projects on tasks.project_id = projects.id 
             JOIN users on tasks.assignee = users.id WHERE tasks.id =:id");
        $genericSearchSingle->bindParam(':id', $id);
        $genericSearchSingle->execute();

        $rs = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);

        $genericSearchSingle = $this->dbh->prepare("SELECT filepath AS url, filename AS title, type from Attachements join tasks on tasks.id = Attachements.task_id where tasks.id = :id");
        $genericSearchSingle->bindParam(':id', $id);
        $genericSearchSingle->execute();

        $rs_attachements = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
        if(count($rs_attachements) >= 1) {
            $rs[0]['attachments'] = $rs_attachements;
        } else {
            $rs[0]['attachments'] = null;
        }

        $genericSearchSingle = $this->dbh->prepare("SELECT username, displayname AS displayName, datetime, comment from Comments join users on comments.user_id = users.id where engagement_id = :id and engagement_table = 'Tasks'");
        $genericSearchSingle->bindParam(':id', $id);
        $genericSearchSingle->execute();

        $rs_comments = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
        if(count($rs_comments) >= 1) {
            $rs[0]['comments'] = $rs_comments;
        } else {
            $rs[0]['comments'] = null;
        }

        $genericSearchSingle = $this->dbh->prepare("SELECT tasks.id, tasks.priority, tasks.taskTitle, 
            assignee, users.displayname AS assigneeDisplayNames, tasks.status
             from tasks join users on tasks.assignee = users.id where tasks.parent_task_id = :id");
        $genericSearchSingle->bindParam(':id', $id);
        $genericSearchSingle->execute();

        $rs_sub_tasks = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
        if(count($rs_sub_tasks) >= 1) {
            $rs[0]['sub_tasks'] = $rs_sub_tasks;
        } else {
            $rs[0]['sub_tasks'] = null;
        }

        $rs[0]['id'] = $id;

        $genericSearchSingle = $this->dbh->prepare("select task_id from TaskDependencies where dependent_task_id = :id");
        $genericSearchSingle->bindParam(':id', $id);
        $genericSearchSingle->execute();

        $rs_dependant = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
        $rs[0]['dependeeIds'] = array();
        if(count($rs_dependant) >= 1) {
            for ($i = 0; $i < count($rs_dependant); $i++) { 
                array_push($rs[0]['dependeeIds'] , $rs_dependant[$i]['task_id']);
            }
        } else {
            $rs[0]['dependeeIds'] = null;
        }

        $genericSearchSingle = $this->dbh->prepare("select dependent_task_id from TaskDependencies where task_id = :id");
        $genericSearchSingle->bindParam(':id', $id);
        $genericSearchSingle->execute();

        $rs_dependant = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
        $rs[0]['dependantIds'] = array();
        if(count($rs_dependant) >= 1) {
            for ($i = 0; $i < count($rs_dependant); $i++) { 
                array_push($rs[0]['dependantIds'] , $rs_dependant[$i]['dependent_task_id']);
            }
        } else {
            $rs[0]['dependantIds'] = null;
        }

        if(count($rs) == 1) {
            return $rs;
        } else {
            return false;
        }
        }

    public function retrieveTasksAssignedToUser($user_id)
    {
        $genericSearchSingle = $this->dbh->prepare("SELECT Tasks.id, priority, taskTitle, projectTitle, Tasks.status FROM Tasks JOIN Projects ON Tasks.project_id = Projects.id WHERE assignee = :me");
        $genericSearchSingle->bindParam(':me', $user_id);
        $genericSearchSingle->execute();

        $rs = $genericSearchSingle->fetchAll(PDO::FETCH_ASSOC);
        if(count($rs) >= 1) {
            return $rs;
        } else {
            return false;
        }
    }  

    public function saveTask($title, $projectID, $milestoneID, $userID, $priority, $allocated_budget, $allocated_time, $dueDate, $flags, $description, $subTaskIds, $parentID,$assignees = NULL, $dStart)
        {
        $createQuery = $this->dbh->prepare("INSERT INTO `Tasks` 
            (project_id, milestone_id, parent_task_id, taskTitle, assignee, 
                priority, status, estimate_budget, estimate_time, due_date, flags, 
                description, create_date, date_start, date_finish) 
            VALUES	(:pid, :mid, :dep, :name, :uid, :priority, 'Open', :ebud, :etime, :ddate, :flags, :desc, NOW(), :dStart, NULL)");

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
        $createQuery->bindParam(":flags",$flags);
        $createQuery->bindParam(":dStart",$dStart);

        /* At this point, we will not check if our projects exist. Simply process the error and return.
           Same goes for our milestones; we need to create a generic checking class to reduce
           inter-class dependencies.
         */
         
        try
        {
          if($createQuery->execute()){
            $pid = $this->dbh->lastInsertId();
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

    public function updateTask($id, $status, $title, $projectID, $milestoneID, $userID, $priority, $allocated_budget, $allocated_time, $dueDate, $flags, $description)
    {

    }
}