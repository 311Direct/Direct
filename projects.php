<?php
  require_once 'db/db.config.php';
  require('db/db.inc.php');
    
  require_once 'classes/project.class.php';
  require_once 'get/getprojects.php';
  
  require_once 'db/json.inc.php';
  
  /* We can only use this page via POST. Die with a JSON error to inform the design team to check! */
  if($_SERVER['REQUEST_METHOD'] != 'POST')
    JSONResponse::printErrorResponseWithHeader("Project request specified was not valid. Error 405 - Method Not Allowed");
  
  /* Setup our allowable actions on this page (sans permissions) */    
  $ACTION = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);  
  $ALLOWEDACTIONS = array('PROJECT_LIST_I_AM_MANAGING', 'PROJECT_LIST_ALL', 'PROJECT_CREATE', 'PROJECT_GET', 'PROJECT_EDIT', 'PROJECT_ATTACH_DELIVERABLE');
  $ACTIONRESPONSE = $ACTION.'_RESPONSE';
    
  /* Check that what we have requested to do is allowed. Return with error if we are not. */  
  if($_SERVER['REQUEST_METHOD'] == 'POST' && !in_array($ACTION, $ALLOWEDACTIONS))
      JSONResponse::printErrorResponseWithHeader("Project request specified was not valid. Please check your variables and try again.");
  
  $projectsDB = new ProjectDB();
  
  /* Functions that do things to make code easier to manage */
  function getProjectsAssignedToUser($user)
  {
    global $projectsDB, $ACTION, $ACTIONRESPONSE;
    $projects = $projectsDB->retrieveProjectsAssignedToUser($id);
    $projectsCount = count($projects);
    $r = new JSONResponse($ACTIONRESPONSE, array("count"=>$projectsCount, "projects"=>$projects));
    $r->printResponseWithHeader();
  }
  
  function getAllSystemProjects($user)
  {
    global $projectsDB, $ACTION, $ACTIONRESPONSE;
    $projects = $projectsDB->retrieveAllProjectInSystem($user);
    $projectsCount = count($projects);
    $r = new JSONResponse($ACTIONRESPONSE, array("count"=>$projectsCount, "projects"=>$projects));
    $r->printResponseWithHeader();
  }
  
  function createProject($user)
  {
    global $projectsDB, $ACTION, $ACTIONRESPONSE;
  }
  
  $id = filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_STRING);
    
  switch($ACTION)
  {
    case 'PROJECT_LIST_I_AM_MANAGING': getProjectsAssignedToUser($id);      break;
    case 'PROJECT_LIST_ALL': getAllSystemProjects($id);       break;
    case 'PROJECT_CREATE':      break;
    case 'PROJECT_GET':      break;
    case 'PROJECT_EDIT':      break;
    case 'PROJECT_ATTACH_DELIVERABLE': break;
    default: JSONResponse::printErrorResponseWithHeader("Project request specified was not valid. Please check your variables and try again."); break;
  }
    
  
?>