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
  
  function getMilestoneWithID()
  {
      $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
      global $milestonesDB, $taskDB, $ACTION, $ACTIONRESPONSE;
      $milestones = $milestonesDB->retrieveMilestoneWithID($id);
      if ($milestones) {
          $milestoneCount = count($milestones);
          $r = new JSONResponse($ACTIONRESPONSE, array("count"=>$milestoneCount, "milestones"=>$milestones));
      } else {
          $r = new JSONResponse($ACTIONRESPONSE, array("count"=>0, "milestones"=>NULL));
      }
      $r->printResponseWithHeader();
  }
  
  function searchMilestones()
  {
      global $milestonesDB, $ACTION, $ACTIONRESPONSE;
      $criteria = filter_input(INPUT_POST,'criteria',    FILTER_SANITIZE_STRING);
      $value = filter_input(INPUT_POST,'value',    FILTER_SANITIZE_STRING);
      switch ($criteria) {
          case 'id':
              $milestones = $milestonesDB->retrieveMilestoneWithID($value);
              break;
          
          default:
              # code...
              break;
      }
      if ($milestones) {
          $milestoneCount = count($milestones);
          $r = new JSONResponse($ACTIONRESPONSE, array("count"=>$milestoneCount, "milestones"=>$milestones));
      } else {
          $r = new JSONResponse($ACTIONRESPONSE, array("count"=>0, "milestones"=>NULL));
      }
      $r->printResponseWithHeader(); 
  }
    
  function createMilestone()
  {
    global $milestonesDB, $ACTION, $ACTIONRESPONSE;
    
    $userId         = filter_input(INPUT_POST,'userId',   FILTER_SANITIZE_NUMBER_INT);
    $title          = filter_input(INPUT_POST,'title',    FILTER_SANITIZE_STRING);
    $dCreate        = filter_input(INPUT_POST,'createdDate',FILTER_SANITIZE_STRING); 
    $projectId      = filter_input(INPUT_POST,'projectId',   FILTER_SANITIZE_NUMBER_INT);
    $allocB         = filter_input(INPUT_POST,'allocatedBudget',  FILTER_SANITIZE_STRING);
    $allocT         = filter_input(INPUT_POST,'allocatedTime',    FILTER_SANITIZE_STRING);
    $desc           = filter_input(INPUT_POST,'description',      FILTER_SANITIZE_STRING);
    
   if(
        (!isset($userId)|| !isset($title) || !isset($dCreate) || !isset($allocB) || !isset($allocT) || !isset($desc) || !isset($projectId))
       || empty($userId) || $userId < 0 ||  (empty($title) || $projectId < 0 || empty($dCreate) || empty($allocB) || empty($allocT) || empty($desc))
     )
    {
      JSONResponse::printErrorResponseWithHeader("One or more fields were absent or empty for this operation. Please check your values and try again.");
    }
    
    if($doCreateMilestone = $milestonesDB->saveMilestone($userId, $title, $projectId, $allocB, $allocT, $dCreate, $desc))
    {
      $r = new JSONResponse($ACTIONRESPONSE, $doCreateMilestone);
      $r->printResponseWithHeader();
    }
    else
    {
      JSONResponse::printErrorResponseWithHeader("Unable to create milestone."); 
    }
  }
  
  function updateMilestone()
  {
    global $milestonesDB, $ACTION, $ACTIONRESPONSE;
    
    $id             = filter_input(INPUT_POST,'id',                 FILTER_SANITIZE_NUMBER_INT);
    $projectId      = filter_input(INPUT_POST,'projectId',          FILTER_SANITIZE_NUMBER_INT);
    $title          = filter_input(INPUT_POST,'title',              FILTER_SANITIZE_STRING);
    $status         = filter_input(INPUT_POST,'status',             FILTER_SANITIZE_STRING);
    $allocB         = filter_input(INPUT_POST,'allocatedBudget',    FILTER_SANITIZE_STRING);
    $allocT         = filter_input(INPUT_POST,'allocatedTime',      FILTER_SANITIZE_STRING);
    $desc           = filter_input(INPUT_POST,'description',        FILTER_SANITIZE_STRING);
    
   if(
        (
         !isset($id) || 
         !isset($projectId) || 
         !isset($title) || 
         !isset($status) || 
         !isset($allocB) || 
         !isset($allocT) || 
         !isset($desc)
        )
         || 
        (
         empty($id) || 
         $projectId < 0 || 
         empty($title) || 
         empty($status) || 
         empty($allocB) || 
         empty($allocT) || 
         empty($desc)
        )
     )
    {
      JSONResponse::printErrorResponseWithHeader("One or more fields were absent or empty for this operation. Please check your values and try again.");
    }
    
    if($doUpdateMilestone = $milestonesDB->updateMilestone($id, $status, $title, $projectId, $allocB, $allocT, $desc))
    {
      $r = new JSONResponse($ACTIONRESPONSE, $doUpdateMilestone);
      $r->printResponseWithHeader();
    }
    else
    {
      JSONResponse::printErrorResponseWithHeader("Unable to create milestone."); 
    }
  }
    
  switch($ACTION)
  {
    case 'MILESTONE_GET'  :       getMilestoneWithID(); break;
    case 'MILESTONE_CREATE':      createMilestone(); break;
    case 'MILESTONE_EDIT'  :      updateMilestone(); break;
    case 'MILESTONE_ASSIGN_TASK': break;
    case 'SEARCH_MILESTONES':     searchMilestones();          break;
    default: JSONResponse::printErrorResponseWithHeader("Milestone request specified was not valid. Please check your variables and try again."); break;
  }
  
?>