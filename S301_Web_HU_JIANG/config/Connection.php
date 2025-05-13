<?php
  class Connexion {
    static private $hostname = 'projets.iut-orsay.fr';
    static private $database = 'saes3-shu';     
    static private $login = 'saes3-shu';      
    static private $password = '20041110'; 
  
    static private $tabUTF8 = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
  
    static private $pdo;
  
    static public function pdo() {return self::$pdo;}
  
    static public function connect()  {
      $h = self::$hostname; $d = self::$database; $l = self::$login; $p = self::$password; $t = self::$tabUTF8;
      try {
        self::$pdo = new PDO("mysql:host=$h;dbname=$d",$l,$p,$t);
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch(PDOException $e) {
        file_put_contents('error_log.txt', $e->getMessage(), FILE_APPEND);

      }
    } 
  }
  ?>
  