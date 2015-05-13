<?php

class JSONResponse
{
  private $finalString;

  function __construct($action = NULL, $payload = NULL)
  {
    if($action === NULL || $payload === NULL)
      return;
      
    if(is_array($payload)){
      $this->finalString = array('action'=>$action, 'payload'=>$payload);
    } else {
      $this->finalString = array('action'=>$action, 'payload'=>array($payload));
    }
    
  }
  
  public function printResponseWithHeader()
  {
    header('Content-Type: application/json');
    echo json_encode($this->finalString);
    exit(0);
  }
  
  public static function printErrorResponseWithHeader($errorMsg)
  {
    # Should only be used when an exepception is reached!
    header('Content-Type: application/json');
    echo json_encode(array('action'=>"ERROR", 'payload'=>array("errorMessage"=>$errorMsg)));
    exit(0);
  }
}