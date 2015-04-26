<?php
  require_once 'db/db.config.php';
  require('db/db.inc.php');
    
  require_once 'classes/project.class.php';
  require_once 'get/getprojects.php';
  
  require_once 'db/json.inc.php';
  
  if($_SERVER['REQUEST_METHOD'] == 'POST')
  {
    if(filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING) != 'PROJECT_LIST_I_AM_MANAGING')
    {
      JSONResponse::printErrorResponseWithHeader("Project request specified was not valid. Please check your variables and try again.");
    }
    
    $projectsDB = new ProjectDB($DBUSER, $DBPASS);
    $id = filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_STRING);
    $projects = $projectsDB->retrieveProjectsAssignedToUser($id);
    $projectsCount = count($projects);
    
    $r = new JSONResponse('PROJECT_LIST_I_AM_MANAGING_RESPONSE', array("count"=>$projectsCount, "projects"=>$projects));
    $r->printResponseWithHeader();
    
  } else {
      JSONResponse::printErrorResponseWithHeader("Project request specified was not valid. Error 405 - Method Not Allowed");    
  }
  
?>