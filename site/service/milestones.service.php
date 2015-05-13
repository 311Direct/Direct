<?php
  require_once '../config/db.config.php';
  require_once '../config/db.inc.php';
  require_once '../config/json.inc.php';

  require_once '../model/milestones.php';
 
  require_once '../model/tasks.php';
  
  /* We can only use this page via POST. Die with a JSON error to inform the design team to check! */
  if($_SERVER['REQUEST_METHOD'] != 'POST')
    JSONResponse::printErrorResponseWithHeader("Milestone request specified was not valid. Error 405 - Method Not Allowed");
    
  /* Setup our allowable actions on this page (sans permissions) */    
  $ACTION = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);  
  $ALLOWEDACTIONS = array('MILESTONE_EDIT', 'MILESTONE_ASSIGN_TASK', 'MILESTONE_GET', 'MILESTONE_CREATE', 'SEARCH_MILESTONES');
  $ACTIONRESPONSE = $ACTION.'_RESPONSE';
  
    /* Check that what we have requested to do is allowed. Return with error if we are not. */  
  if($_SERVER['REQUEST_METHOD'] == 'POST' && !in_array($ACTION, $ALLOWEDACTIONS))
      JSONResponse::printErrorResponseWithHeader("Milestone request specified was not valid. Please check your variables and try again. ".$ACTION);
      
    
  $milestonesDB = new MilestoneDB();
  $taskDB = new TaskDB();
  
  /* Functions that do things to make code easier to manage */
  
  function getMilestoneWithID($id)
  {
    global $milestonesDB, $taskDB, $ACTION, $ACTIONRESPONSE;
    if($milestones = $milestonesDB->retrieveMilestoneWithID($id)){
      $milestones["tasks"] = $taskDB->retrieveTasksWithMilestone($id);
      $milestones["users"] = $taskDB->retrieveUsersWithMilestone($id);
      $r = new JSONResponse($ACTIONRESPONSE, array("milestone"=>$milestones));
      $r->printResponseWithHeader();
    }
    else
    {
      JSONResponse::printErrorResponseWithHeader("Unable to retrieve milestone. id: ".$id); 
    }
    
  }
    
  function createMilestone()
  {/*
    
    if((!isset($title) || !isset($userID) || !isset($dStart) || !isset($dEnd) || !isset($allocB) || !isset($allocT) || !isset($desc))
       || (empty($title) || $userID < 0 || empty($dStart) || empty($dEnd) || empty($allocB) || empty($allocT) || empty($desc))
      )
    {
      JSONResponse::printErrorResponseWithHeader("One or more fields were absent or empty for this operation. Please check your values and try again.");
    }
    
    $milestonesDB = new taskDB();
        
    /* At the moment, we simply go about our business, not checking any inputs other than sanatising inputs //
    $doCreatetask = $milestonesDB->saveTask($title, $projectID, $milestoneID, $userID, $priority, $allocB, $allocT, $dEnd, $flags, $desc, $subTaskIDs, $dependeeIDs);
    
    if($doCreatetask)
    {
      $r = new JSONResponse($ACTIONRESPONSE, $doCreatetask);
      $r->printResponseWithHeader();
    } else 
    {
      JSONResponse::printErrorResponseWithHeader("Unable to create task."); 
    }
    */
  }  

  $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    
  switch($ACTION)
  {
    case 'MILESTONE_GET'  :       getMilestoneWithID($id); break;
    case 'MILESTONE_CREATE':      createMilestone(); break;
    case 'MILESTONE_EDIT'  :      break;
    case 'MILESTONE_ASSIGN_TASK': break;
    case 'SEARCH_MILESTONES':     break;
    default: JSONResponse::printErrorResponseWithHeader("Milestone request specified was not valid. Please check your variables and try again."); break;
  }
  
?>