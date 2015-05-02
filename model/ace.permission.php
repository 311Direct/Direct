<?php
  
  class AccessControlEntry
  {
    protected $projectID;
    protected $objectType;
    protected $objectID;
    protected $value;

    public function __construct($p, $t, $o, $v)
    {
      if($t == 'r' || $t == 'u')
        $this->objectType = $t;
      else
        die("Object had a type that exceeded current permission system. Aborting execution of this script. Details: $p $t $o $v");
        
    
      if($v > P_MAX_ENTRY)
        die("Object had a value that exceeded current permission version. Aborting execution of this script. Details: $p $t $o $v");
        
      $this->projectID = $p;
      $this->objectID = $o;
      $this->value = $v;
    } 
    
    public function projectID()
    {
      return $this->projectID;
    }
    
    public function objectType()
    {
      return $this->objectType;
    }
    
    public function objectID()
    {
      return $this->objectID;
    }
    
    public function value()
    {
      return $this->value;
    }
    
  }