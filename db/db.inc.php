<?php  
  class DatabaseAdaptor
  {
    
    protected $dbh = NULL;
    
    public function __construct($u, $p)
    {
      try {
        $this->dbh = new PDO("mysql:host=127.0.0.1;dbname=directdb_v2", $u, $p);
      } catch (PDOException $e)
      {
        print("Error connecting to the database. Please contact your system administrator.");
        print($e);
        die();
      }
    }
      
    function chkAuth($u, $p)
    {
      $p = sha1($p);

      $stmt = $this->dbh->prepare("SELECT * FROM `Users` WHERE `username` = :user AND `password` = :pass");
      $stmt->bindParam(':user', $u);
      $stmt->bindParam(':pass', $p);

      $stmt->execute();
      
      $rs = $stmt->fetchAll();
      if(count($rs) == 1)
        return true;
        
      return false;
    }
  }