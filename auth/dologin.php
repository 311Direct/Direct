<?php
  require('db/db.config.php');
  require('db/db.inc.php');  
  require('db/json.inc.php');

  if($_SERVER['REQUEST_METHOD'] == 'POST')
  {
    $db = new DatabaseAdaptor($DBUSER, $DBPASS);

    if($db->chkAuth($_POST['u'], $_POST['p'])){
        $r = new JSONResponse('LOGIN',array("isValid"=>1));
    }
    else
    {
        $r = new JSONResponse('LOGIN',array("isValid"=>0));
    }
    $r->printResponseWithHeader();
  }