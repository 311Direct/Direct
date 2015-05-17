<?php
  
class ArchiveLogModel
{
  protected $_id;
  protected $_eventID;
  protected $_eventName;
  protected $_title;
  protected $_description;
  
  
  public function __construct($id, $eventID, $title, $description)
  {
    $this->_id = $id;
    $this->_eventID = $eventID;
    $this->_title = $title;
    $this->_description = $description;
    
    $this->nameEventID($eventID);
    
  }
  
  private function nameEventID($anID)
  {
    
    switch($anID)
    {
      case 1000: $this->_eventName = "Success"; break;
      
      default: $this->_eventName = "Generic Error"; break;
    }
    
    
  }
  
}