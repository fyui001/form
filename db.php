<?php
function getDb() {
  $dsn = 'mysql:dbname=test; host=localhost; charset=utf8;';
  $usr = 'root';
  $passwd = 'passwd';

 try{
   $db = new PDO($dsn,$usr,$passwd);
   $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
   $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
 } catch(PDOException $e){
    die("にゃーん:{$e->getMessage()}");
 }
  return $db;
}
