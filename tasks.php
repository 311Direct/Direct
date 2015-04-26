<?php
  require_once 'db/db.config.php';
  require('db/db.inc.php');

  require_once 'classes/task.class.php';
  require_once 'get/gettasks.php';
  
  require_once 'classes/project.class.php';
  require_once 'get/getprojects.php';
  
  require_once 'db/json.inc.php';
  
  if($_SERVER['REQUEST_METHOD'] == 'POST')
  {
    if(filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING) != 'TASK_LIST')
    {
      JSONResponse::printErrorResponseWithHeader("Task request specified was not valid. Please check your variables and try again.");
    }
    
    $tasksDB = new TaskDB($DBUSER, $DBPASS);
    $tasks = $tasksDB->retrieveTasksAssignedToUser(filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_STRING));
    $tasksCount = count($tasks);
    
    $projectsDB = new ProjectDB($DBUSER, $DBPASS);
    
    for($i=0;$i<$tasksCount;$i++)
    {
      $tasks[$i]['projectTitle'] = $projectsDB->retrieveProjectWithID($tasks[$i]['project_id'])['name'];
    }
    
    $r = new JSONResponse('TASK_LIST_RESPONSE', array("count"=>$tasksCount, "tasks"=>$tasks));
    $r->printResponseWithHeader();
    
  } else {
      JSONResponse::printErrorResponseWithHeader("Task request specified was not valid. Error 405 - Method Not Allowed");    
  }
  
?>