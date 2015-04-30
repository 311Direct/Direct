<?php
  require_once '../config/db.config.php';
  require_once '../config/db.inc.php';
  require_once '../config/json.inc.php';

  require_once '../model/milestones.php';
  
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
  
  /* Functions that do things to make code easier to manage */
  
  function getMilestoneWithID($id)
  {
    global $milestonesDB, $ACTION, $ACTIONRESPONSE;
    
    if($milestones = $milestonesDB->retrieveMilestoneWithID($id)){
      $r = new JSONResponse($ACTIONRESPONSE, array("milestone"=>$milestones));
      $r->printResponseWithHeader();
    }
    else
    {
      JSONResponse::printErrorResponseWithHeader("Unable to retrieve milestone."); 
    }    
  }
    
  function createMilestone()
  {
    global $milestonesDB, $ACTION, $ACTIONRESPONSE;
       
    $title  = filter_input(INPUT_POST,'title',    FILTER_SANITIZE_STRING);
    $dStart = filter_input(INPUT_POST,'createdDate',FILTER_SANITIZE_STRING); 
    $pID    = filter_input(INPUT_POST,'projectId',   FILTER_SANITIZE_STRING);
    $allocB = filter_input(INPUT_POST,'allocatedBudget',  FILTER_SANITIZE_STRING);
    $allocT = filter_input(INPUT_POST,'allocatedTime',    FILTER_SANITIZE_STRING);
    $desc   = filter_input(INPUT_POST,'description',      FILTER_SANITIZE_STRING);
    
   if((!isset($title) || !isset($dStart) || !isset($allocB) || !isset($allocT) || !isset($desc) || !isset($pID))
       || (empty($title) || $pID < 0 || empty($dStart) || empty($allocB) || empty($allocT) || empty($desc))
      )
    {
      JSONResponse::printErrorResponseWithHeader("One or more fields were absent or empty for this operation. Please check your values and try again.");
    }
    
    if($doCreateMilestone = $milestonesDB->saveMilestone($title, $mID, $pID, $allocB, $allocT, $dStart, $desc))
    {
      $r = new JSONResponse($ACTIONRESPONSE, $doCreateMilestone);
      $r->printResponseWithHeader();
    }
    else
    {
      JSONResponse::printErrorResponseWithHeader("Unable to create milestone."); 
    }
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