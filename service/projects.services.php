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
  $ALLOWEDACTIONS = array('SEARCH_PROJECTS', 'PROJECT_COMMENT_ADD', 'PROJECT_ASSIGN_TASK', 'PROJECT_LIST_I_AM_MANAGING', 'PROJECT_LIST_ALL', 'PROJECT_CREATE', 'PROJECT_GET', 'PROJECT_EDIT', 'PROJECT_ATTACH_DELIVERABLE');
  $ACTIONRESPONSE = $ACTION.'_RESPONSE';
    
  /* Check that what we have requested to do is allowed. Return with error if we are not. */  
  if($_SERVER['REQUEST_METHOD'] == 'POST' && !in_array($ACTION, $ALLOWEDACTIONS))
      JSONResponse::printErrorResponseWithHeader("Project request specified was not valid. Please check your variables and try again.");
  
  $projectsDB = new ProjectDB();
  
  /* Functions that do things to make code easier to manage */
  function getProjectWithID()
  {
    global $projectsDB, $ACTION, $ACTIONRESPONSE;
    
    $projectID = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
    
    $projects = $projectsDB->retrieveProjectWithID($projectID);
    if ($projects) {
        $projectsCount = count($projects);
        $r = new JSONResponse($ACTIONRESPONSE, array("count"=>$projectsCount, "projects"=>$projects));
    } else {
        $r = new JSONResponse($ACTIONRESPONSE, array("count"=>0, "projects"=>NULL));
    }
    $r->printResponseWithHeader();  
  }
  
  function getProjectsAssignedToUser()
  {
    global $projectsDB, $ACTION, $ACTIONRESPONSE;
    
    $userID = filter_input(INPUT_POST, 'userId', FILTER_SANITIZE_STRING);
    
    $projects = $projectsDB->retrieveProjectsAssignedToUser($userID);
    if ($projects) {
        $projectsCount = count($projects);
        $r = new JSONResponse($ACTIONRESPONSE, array("count"=>$projectsCount, "projects"=>$projects));
    } else {
        $r = new JSONResponse($ACTIONRESPONSE, array("count"=>0, "projects"=>NULL));
    }
    $r->printResponseWithHeader();
  }
  
  function getAllSystemProjects()
  {
    global $projectsDB, $ACTION, $ACTIONRESPONSE;
    $projects = $projectsDB->retrieveAllProjectInSystem();
    if ($projects) {
        $projectsCount = count($projects);
        $r = new JSONResponse($ACTIONRESPONSE, array("count"=>$projectsCount, "projects"=>$projects));
    } else {
        $r = new JSONResponse($ACTIONRESPONSE, array("count"=>0, "projects"=>NULL));
    }
    $r->printResponseWithHeader();
  }
  
  function searchProjects()
  {
      global $projectsDB, $ACTION, $ACTIONRESPONSE;
      $criteria = filter_input(INPUT_POST,'criteria',    FILTER_SANITIZE_STRING);
      $value = filter_input(INPUT_POST,'value',    FILTER_SANITIZE_STRING);
      switch ($criteria) {
          case 'id':
              $projects = $projectsDB->retrieveProjectWithID($value);
              break;
          
          default:
              # code...
              break;
      }
      if ($projects) {
          $projectsCount = count($projects);
          $r = new JSONResponse($ACTIONRESPONSE, array("count"=>$projectsCount, "projects"=>$projects));
      } else {
          $r = new JSONResponse($ACTIONRESPONSE, array("count"=>0, "projects"=>NULL));
      }
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
    {
        $manaBy[$i] = filter_var($manaBy[$i], FILTER_SANITIZE_NUMBER_INT);
    }  

    if((!isset($title) || !isset($userID) || !isset($dStart) || !isset($dEnd) || !isset($allocB) || !isset($allocT) || !isset($desc))
       || (empty($title) || $userID < 0 || empty($dStart) || empty($dEnd) || empty($allocB) || empty($allocT) || empty($desc))
      )
    {
      JSONResponse::printErrorResponseWithHeader("One or more fields were absent or empty for this operation. Please check your values and try again.");
    }
        
    /* At the moment, we simply go about our business, not checking any inputs other than sanatising inputs */
    $doCreateProject = $projectsDB->saveProject($title, $userID, $dStart, $dEnd, $allocB, $allocT, $desc);
    if($doCreateProject)
    {
      $r = new JSONResponse($ACTIONRESPONSE, $doCreateProject);
      $r->printResponseWithHeader();
    } else 
    {
      JSONResponse::printErrorResponseWithHeader("Unable to create project."); 
    }
    
  }
  
  function editProject() {
      global $projectsDB, $ACTION, $ACTIONRESPONSE;
      
      $id  = filter_input(INPUT_POST,'id',    FILTER_SANITIZE_NUMBER_INT);
      $title  = filter_input(INPUT_POST,'title',    FILTER_SANITIZE_STRING);
      $status  = filter_input(INPUT_POST,'status',    FILTER_SANITIZE_STRING);
      $userID = filter_input(INPUT_POST,'userId',   FILTER_SANITIZE_NUMBER_INT);
      $dStart = filter_input(INPUT_POST,'dateStart',FILTER_SANITIZE_STRING); 
      $dEnd   = filter_input(INPUT_POST,'dateExpectedFinish',   FILTER_SANITIZE_STRING);
      $allocB = filter_input(INPUT_POST,'allocatedBudget',  FILTER_SANITIZE_STRING);
      $allocT = filter_input(INPUT_POST,'allocatedTime',    FILTER_SANITIZE_STRING);
      $desc   = filter_input(INPUT_POST,'description',      FILTER_SANITIZE_STRING);
      
      $manaBy = $_POST['projectManagerUserIds'];
      
      for($i = 0; $i < count($manaBy); $i++)
      {
          $manaBy[$i] = filter_var($manaBy[$i], FILTER_SANITIZE_NUMBER_INT);
      }  
      
      if(
         (!isset($id) || !isset($title) || !isset($userID) || !isset($dStart) || !isset($dEnd) || !isset($allocB) || !isset($allocT) || !isset($desc))
             || 
         (empty($id) || empty($title) || $userID < 0 || empty($dStart) || empty($dEnd) || empty($allocB) || empty($allocT) || empty($desc))
        )
      {
        JSONResponse::printErrorResponseWithHeader("One or more fields were absent or empty for this operation. Please check your values and try again.");
      }
          
      /* At the moment, we simply go about our business, not checking any inputs other than sanatising inputs */
      $doUpdateProject = $projectsDB->updateProject($id, $status, $title, $userID, $dStart, $dEnd, $allocB, $allocT, $desc);
      
      var_dump($doUpdateProject);
      if($doUpdateProject)
      {
        $r = new JSONResponse($ACTIONRESPONSE, $doUpdateProject);
        $r->printResponseWithHeader();
      } else 
      {
        JSONResponse::printErrorResponseWithHeader("Unable to update project."); 
      }
  }
  
  function uploadAttachement()
  {
      global $projectsDB, $ACTION, $ACTIONRESPONSE;
      
      $projectId  = filter_input(INPUT_POST,'projectId',    FILTER_SANITIZE_NUMBER_INT);
      $taskId  = filter_input(INPUT_POST,'projectId',    FILTER_SANITIZE_NUMBER_INT);
      $userId = filter_input(INPUT_POST,'userId',   FILTER_SANITIZE_NUMBER_INT);
      $fileUrl  = filter_input(INPUT_POST,'fileUrl',    FILTER_SANITIZE_STRING);
      
      if(
         (!isset($projectId) || !isset($userId) || !isset($fileUrl))
             || 
         (empty($projectId) || empty($userId) || $userId < 0 || empty($fileUrl))
        )
      {
        JSONResponse::printErrorResponseWithHeader("One or more fields were absent or empty for this operation. Please check your values and try again.");
      }
      
      $doAddAttachment = $projectsDB->addAttachement($projectId, $taskId, $userId, $fileUrl);
      
      if($doAddAttachment)
      {
        $r = new JSONResponse($ACTIONRESPONSE, $doAddAttachment);
        $r->printResponseWithHeader();
      } else 
      {
        JSONResponse::printErrorResponseWithHeader("Unable to add attachement."); 
      }
  }
  
  function addComment()
  {
      global $projectsDB, $ACTION, $ACTIONRESPONSE;
      
      $projectId  = filter_input(INPUT_POST,'projectId',    FILTER_SANITIZE_NUMBER_INT);
      $userId = filter_input(INPUT_POST,'userId',   FILTER_SANITIZE_NUMBER_INT);
      $comment  = filter_input(INPUT_POST,'comment',    FILTER_SANITIZE_STRING);
      
      if(
         (!isset($projectId) || !isset($userId) || !isset($comment))
             || 
         (empty($projectId) || empty($userId) || $userId < 0 || empty($comment))
        )
      {
        JSONResponse::printErrorResponseWithHeader("One or more fields were absent or empty for this operation. Please check your values and try again.");
      }
      
      $doAddComment = $projectsDB->addComment($projectId, $userId, $comment);
      
      if($doAddComment)
      {
        $r = new JSONResponse($ACTIONRESPONSE, $doAddComment);
        $r->printResponseWithHeader();
      } else 
      {
        JSONResponse::printErrorResponseWithHeader("Unable to add attachement."); 
      }
  }
    
  switch($ACTION)
  {
    case 'PROJECT_LIST_I_AM_MANAGING':  getProjectsAssignedToUser();        break;
    case 'PROJECT_LIST_ALL':            getAllSystemProjects();             break;
    case 'PROJECT_CREATE':              createProject();                    break;
    case 'PROJECT_GET':                 getProjectWithID();                 break;
    case 'PROJECT_EDIT':                editProject();                      break;
    case 'PROJECT_ATTACH_DELIVERABLE':  uploadAttachement();                break;
    case 'PROJECT_ASSIGN_TASK':         assignTask();                       break;
    case 'PROJECT_COMMENT_ADD':         addComment();                       break;
    case 'SEARCH_PROJECTS':             searchProjects();                   break;
    default: JSONResponse::printErrorResponseWithHeader("Project request specified was not valid. Please check your variables and try again."); break;
  }
    
  
?>