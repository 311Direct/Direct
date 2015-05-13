<?php
  require_once '../config/db.config.php';
  require_once '../config/db.inc.php';
  require_once '../config/json.inc.php';

  require_once '../model/projects.php';
  
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
    $projects = $projectsDB->retrieveProjectsAssignedToUser($user);
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
  
  function createProject()
  {
    global $projectsDB, $ACTION, $ACTIONRESPONSE;
       
    $title  = filter_input(INPUT_POST,'title',    FILTER_SANITIZE_STRING);
    $userID = filter_input(INPUT_POST,'userId',   FILTER_SANITIZE_NUMBER_INT);
    $dStart = filter_input(INPUT_POST,'dateStart',FILTER_SANITIZE_STRING); 
    $dEnd   = filter_input(INPUT_POST,'dateExpectedFinish',   FILTER_SANITIZE_STRING);
    $manaBy = $_POST['projectManagerUserIds'];
    $allocB = filter_input(INPUT_POST,'allocatedBudget',  FILTER_SANITIZE_STRING);
    $allocT = filter_input(INPUT_POST,'allocatedTime',    FILTER_SANITIZE_STRING);
    $desc   = filter_input(INPUT_POST,'description',      FILTER_SANITIZE_STRING);
    
    for($i = 0; $i < count($manaBy); $i++)
      $manaBy[$i] = filter_var($manaBy[$i], FILTER_SANITIZE_NUMBER_INT);
    
    if((!isset($title) || !isset($userID) || !isset($dStart) || !isset($dEnd) || !isset($allocB) || !isset($allocT) || !isset($desc))
       || (empty($title) || $userID < 0 || empty($dStart) || empty($dEnd) || empty($allocB) || empty($allocT) || empty($desc))
      )
    {
      JSONResponse::printErrorResponseWithHeader("One or more fields were absent or empty for this operation. Please check your values and try again.");
    }
        
    /* At the moment, we simply go about our business, not checking any inputs other than sanatising inputs */
    $doCreateProject = $projectsDB->saveProject($title, $userID, $dStart, $dEnd, $allocB, $allocT, $desc, $manaBy);
    if($doCreateProject)
    {
      $r = new JSONResponse($ACTIONRESPONSE, $doCreateProject);
      $r->printResponseWithHeader();
    } else 
    {
      JSONResponse::printErrorResponseWithHeader("Unable to create project."); 
    }
    
  }
  
  $id = filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_STRING);
    
  switch($ACTION)
  {
    case 'PROJECT_LIST_I_AM_MANAGING': getProjectsAssignedToUser($id); break;
    case 'PROJECT_LIST_ALL': getAllSystemProjects($id); break;
    case 'PROJECT_CREATE': createProject(); break;
    case 'PROJECT_GET':      break;
    case 'PROJECT_EDIT':      break;
    case 'PROJECT_ATTACH_DELIVERABLE': break;
    default: JSONResponse::printErrorResponseWithHeader("Project request specified was not valid. Please check your variables and try again."); break;
  }
    
  
?>