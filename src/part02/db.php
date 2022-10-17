<?php // db.php
  $host = 'mysql';
  $data = 'db';
  $user = 'db_user';         // Change as necessary
  $pass = 'app_pass';        // Change as necessary
  $chrs = 'utf8mb4';
  $attr = "mysql:host=$host;dbname=$data;charset=$chrs";
  $opts =
  [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
  ];
 
  try
  {
    $pdo = new PDO($attr, $user, $pass, $opts);
  }
  catch (\PDOException $e)
  {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
  }
 
  function intialize_db($pdo){
    $query1 = "CREATE TABLE IF NOT EXISTS raw_scrapes (
      pk INT NOT NULL AUTO_INCREMENT,
      link VARCHAR(256) NOT NULL,
      title VARCHAR(256),
      raw TEXT(65000),
      PRIMARY KEY(pk)
    )";
 
    $query2 = "CREATE TABLE IF NOT EXISTS stat (
      pk int NOT NULL AUTO_INCREMENT,
      word VARCHAR(50) NOT NULL,
      occurrence int,
      raw_scrapes_pk int,
      PRIMARY KEY (pk),
      FOREIGN KEY (raw_scrapes_pk) REFERENCES raw_scrapes(pk)
    )";
     
    try{
      $result1 = $pdo->query($query1);
      $result2 = $pdo->query($query2);
    }catch (\Exception $e)
    {
      throw new \Exception($e->getMessage(), (int)$e->getCode());
    }
  }
 
  function insert_raw($pdo, $raw, $l)
  {
    $stmt = $pdo->prepare('INSERT INTO raw_scrapes(link, raw) VALUES(?,?)');
 
    $stmt->bindParam(1, $l, PDO::PARAM_STR, 256);
    $stmt->bindParam(2, $raw, PDO::PARAM_STR, 65000);
 
    $stmt->execute([$l, $raw]);
  }
 
  function query_raw($pdo, $l){
    try{
        $sql = 'SELECT pk, link, raw FROM raw_scrapes WHERE link=?';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(1, $l, PDO::PARAM_STR, 256);
        $stmt->execute([$l]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }catch(\Exception $e){
        throw new \Exception($e->getMessage(), (int)$e->getCode());
    }      
  }
 
  function insert_words($pdo, $words, $raw_scrapes_pk)
  {   
    try{
      $qry = 'INSERT INTO stat(word, occurrence, raw_scrapes_pk) VALUES ';
      $values = array();
      foreach($words as $word => $count){
        $values2 = array($word, $count, $raw_scrapes_pk);
        $values = array_merge($values,$values2);
        $qry .= "(?,?,?),";
      }
      $qry = rtrim($qry, ",");
      $stmt = $pdo->prepare($qry);
      $stmt->execute($values);
    }catch(\Exception $e){
        throw new \Exception($e->getMessage(), (int)$e->getCode());
    } 
  }
 
  function query_stat($pdo, $l){
    try{
        $sql = 'SELECT word, occurrence FROM stat s join raw_scrapes r on s.raw_scrapes_pk = r.pk WHERE r.link=? order by s.occurrence desc, s.word';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(1, $l, PDO::PARAM_STR, 256);
        $stmt->execute([$l]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }catch(\Exception $e){
        throw new \Exception($e->getMessage(), (int)$e->getCode());
    }      
  }
 
  function SanitizeString($var)
  {
    $var = strip_tags($var);
    $var = htmlentities($var);
    return stripslashes($var);
  }
?>