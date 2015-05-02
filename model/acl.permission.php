<?php

/* This will require the CONTROLLER to pass the correct value from and to SQL! */
  
class AccessControlList implements Serializable
{
  private $aces;
  private $version;
  private $acllist;
  
  public function __construct($packedDatabaseVersion)
  {
        $this->version = 1;
        $this->aces = array();
        
        for($i=0;$i<10;$i+=2)
        {
          $a = new AccessControlEntry(0, 'r', $i, (50*$i));
          array_push($this->aces,$a);
        }
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

  public function getPackedVersion()
  {  
    $arrayCount = count($this->aces);
    if($arrayCount < 1)
      return null;
    
    $returnValue = pack(P_DB_FORMAT_V1, $this->aces[0]->projectID(), $this->aces[0]->objectType(), $this->aces[0]->objectID(), $this->aces[0]->value());
    
    for($i = 1; $i < $arrayCount; $i++)
    {
      $returnValue .= pack(P_DB_FORMAT_V1, $this->aces[$i]->projectID(), $this->aces[$i]->objectType(), $this->aces[$i]->objectID(), $this->aces[$i]->value());
    }
    return $returnValue;
  }
}