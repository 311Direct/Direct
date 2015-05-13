<?php
  require_once '../config/db.config.php';
  require_once '../config/db.inc.php';
  require_once '../config/json.inc.php';

  require_once '../model/tasks.php';
  
  
  /* We can only use this page via POST. Die with a JSON error to inform the design team to check! */
  if($_SERVER['REQUEST_METHOD'] != 'POST')
    JSONResponse::printErrorResponseWithHeader("Task request specified was not valid. Error 405 - Method Not Allowed");
    
  /* Setup our allowable actions on this page (sans permissions) */    
  $ACTION = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);  
  $ALLOWEDACTIONS = array('TASK_LIST', 'TASK_EDIT', 'TASK_GET', 'TASK_WATCH', 'TASK_ATTACH_FILE', 'TASK_ADD_COMMENT', 'TASK_ASSIGN_SUBTASK', 'SEARCH_TASKS', 'TASK_CREATE');
  $ACTIONRESPONSE = $ACTION.'_RESPONSE';
  
    /* Check that what we have requested to do is allowed. Return with error if we are not. */  
  if($_SERVER['REQUEST_METHOD'] == 'POST' && !in_array($ACTION, $ALLOWEDACTIONS))
      JSONResponse::printErrorResponseWithHeader("Task request specified was not valid. Please check your variables and try again. ".$ACTION);
      
    
  $tasksDB = new TaskDB();
  
  /* Functions that do things to make code easier to manage */
  
  function getTasksAssignedToUser($user)
  {
    global $tasksDB, $ACTION, $ACTIONRESPONSE;
    $tasks = $tasksDB->retrieveTasksAssignedToUser($user);
    $tasksCount = count($tasks);
     for($i=0;$i<$tasksCount;$i++)
    {
      //$tasks[$i]['taskTitle'] = $tasksDB->retrievetaskWithID($tasks[$i]['task_id'])['name'];
    }
    $r = new JSONResponse($ACTIONRESPONSE, array("count"=>$tasksCount, "tasks"=>$tasks));
    $r->printResponseWithHeader();
  }
  
   function getTask($id)
  {
    global $tasksDB, $ACTION, $ACTIONRESPONSE;
    $tasks = $tasksDB->retrieveTaskWithID($id);
    $tasksCount = count($tasks);
     for($i=0;$i<$tasksCount;$i++)
    {
      //$tasks[$i]['taskTitle'] = $tasksDB->retrievetaskWithID($tasks[$i]['task_id'])['name'];
    }
    $r = new JSONResponse($ACTIONRESPONSE, array("count"=>$tasksCount, "task"=>$tasks));
    $r->printResponseWithHeader();
  }
    
  function createTask()
  {
    global $tasksDB, $ACTION, $ACTIONRESPONSE;
       
    $title        = filter_input(INPUT_POST,'title',          FILTER_SANITIZE_STRING);
    $userID       = filter_input(INPUT_POST,'userId',         FILTER_SANITIZE_NUMBER_INT);
    $projectID    = filter_input(INPUT_POST,'projectId',      FILTER_SANITIZE_NUMBER_INT); 
    $milestoneID  = filter_input(INPUT_POST,'milestoneId',    FILTER_SANITIZE_NUMBER_INT);
    $priority     = filter_input(INPUT_POST,'priority',       FILTER_SANITIZE_STRING);
    $allocB       = filter_input(INPUT_POST,'allocatedBudget',FILTER_SANITIZE_STRING);
    $allocT       = filter_input(INPUT_POST,'allocatedTime',  FILTER_SANITIZE_STRING);
    $desc         = filter_input(INPUT_POST,'description',    FILTER_SANITIZE_STRING);
    $flags        = filter_input(INPUT_POST,'flags',          FILTER_SANITIZE_STRING);
    $subTaskIDs   = filter_input(INPUT_POST,'subTaskIds',     FILTER_SANITIZE_STRING); // We don't process this just yet.
    $dependeeIDs  = filter_input(INPUT_POST,'dependeeIds',    FILTER_SANITIZE_STRING); // Need to talk about at Wednesday's meeting
    $dependantIDs = filter_input(INPUT_POST,'dependantIds',   FILTER_SANITIZE_STRING); // Need to talk about at Wednesday's meeting
    $dStart       = filter_input(INPUT_POST,'dateStart',      FILTER_SANITIZE_STRING); 
    $dEnd         = filter_input(INPUT_POST,'dateExpectedFinish',   FILTER_SANITIZE_STRING);
    
    /*
      $asignees     = $_POST['assigneeUserIds'];
    
    for($i = 0; $i < count($asignees); $i++)
      $asignees[$i] = filter_var($asignees[$i], FILTER_SANITIZE_NUMBER_INT);
    */
    
    if($dependeeIDs == 'NULL')
      $dependeeIDs = NULL;
    
    if((!isset($title) || !isset($userID) || !isset($dStart) || !isset($dEnd) || !isset($allocB) || !isset($allocT) || !isset($desc))
       || (empty($title) || $userID < 0 || empty($dStart) || empty($dEnd) || empty($allocB) || empty($allocT) || empty($desc))
      )
    {
      JSONResponse::printErrorResponseWithHeader("One or more fields were absent or empty for this operation. Please check your values and try again.");
    }
            
    /* At the moment, we simply go about our business, not checking any inputs other than sanatising inputs */
    $doCreatetask = $tasksDB->saveTask($title, $projectID, $milestoneID, $userID, $priority, $allocB, $allocT, $dEnd, $flags, $desc, $subTaskIDs, $dependeeIDs);
    
    if($doCreatetask)
    {
      $r = new JSONResponse($ACTIONRESPONSE, $doCreatetask);
      $r->printResponseWithHeader();
    } else 
    {
      JSONResponse::printErrorResponseWithHeader("Unable to create task."); 
    }
  }  
  
  $id = filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_STRING);
  $ALLOWEDACTIONS = array('TASK_LIST', 'TASK_EDIT', 'TASK_WATCH', 'TASK_ATTACH_FILE', 'TASK_ADD_COMMENT', 'TASK_ASSIGN_SUBTASK', 'SEARCH_TASKS', 'TASK_CREATE');
    
  switch($ACTION)
  {
    case 'TASK_LIST'  : getTasksAssignedToUser($id); break;
    case 'TASK_CREATE': createTask(); break;
    case 'TASK_GET'   : getTask($id); break;
    case 'TASK_EDIT'  :      break;
    case 'TASK_WATCH':      break;
    case 'TASK_ATTACH_FILE': break;
    case 'TASK_ADD_COMMENT': break;
    case 'TASK_ASSIGN_SUBTASK': break;
    case 'SEARCH_TASKS': break;
    default: JSONResponse::printErrorResponseWithHeader("Task request specified was not valid. Please check your variables and try again."); break;
  }
  
?>