<?php
class Task implements JsonSerializable
{
  public $id, $priority, $taskTitle, $projectTitle, $status;    
  
  private $json;
    
  public function jsonSerialize() {
      return $this->json;
  }
}