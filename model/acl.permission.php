<?php

/* This will require the CONTROLLER to pass the correct value from and to SQL! */
  
class AccessControlList implements Serializable
{
  private $aces; //index of this array corresponds to the project ID 
  private $version;
  private $hasBeenConstructed;
  
  public function __construct($packedDatabaseVersion=null)
  {
        $this->version = 1;
        $this->aces = array();
        $this->hasBeenConstructed = true;
  }
  
  public function serialize()
  {
    $a = serialize($this->aces);
    $b = serialize($this->version);
    $c = array('version'=>$b, 'aces'=>$a);
    return serialize($c);
  }
  
  public function unserialize($anACL)
  {
    $c = unserialize($anACL);
    $this->version = unserialize($c['version']);
    $this->aces = unserialize($c['aces']);
  }

  public function generateBasePermissions()
  {
    if($this->hasBeenConstructed != true)
    {
      $this->aces = array();
    }
           
    $a = new AccessControlEntry(0, 'R', 0, P_READ);
    array_push($this->aces,$a);
  }

  public function getEveryonePermission()
  {
    return $this->aces[0];
  }
  
  public function deletePermission()
  {
    array_pop($this->aces[$newACE->projectID()][$newACE->objectType()][$newACE->objectID()]);
  }
  
  private function addPermission($newACE)
  {
    $this->aces[$newACE->projectID()][$newACE->objectType()][$newACE->objectID()] = $newACE;
  }
  
  public function getPermissionForUser($projectID, $userID, $roleID = NULL)
  {
    /* First off, we need to make sure we in the right project */
    if(!array_key_exists($projectID, $this->aces))
    {
      //Project does not exist.
      return $this->getEveryonePermission();
    }
    
    if(array_key_exists($projectID, $this->aces))
    {
      if(array_key_exists($roleID, $this->aces[$projectID]['r'][$roleID]))
        return $this->aces[$projectID()]['r'][$roleID]->value();
      
      if(array_key_exists($roleID, $this->aces[$projectID]['u'][$userID]))
        return $this->aces[$projectID()]['u'][$userID]->value();
    }
  }
  
  public function modifyPermissions($newACE,$deleteACE=false)
  {
    if(array_key_exists($newACE->projectID(), $this->aces))
    {
      if($deleteACE)
        $this->deletePermission($newACE);
      else
        $this->aces[$newACE->projectID()][$newACE->objectType()][$newACE->objectID()] = $newACE;
    }      
    else
    {
      $this->addPermission($newACE);
    }
  }
  
}