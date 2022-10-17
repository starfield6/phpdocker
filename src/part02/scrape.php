
<?php 
  require_once 'db.php';

  intialize_db($pdo);

  $link = $_POST['link'] ? SanitizeString($_POST['link']) : "";
  $exist = query_raw($pdo, $link);
  if($exist){
    $raw_content = $exist->raw;
  }else{
    try{
        $raw_content = file_get_contents($link);
        insert_raw($pdo, $raw_content, $link);
        $doc = new DOMDocument();
        $doc->loadHTML('<?xml encoding="UTF-8">' . $raw_content);
        $htmlNodes = $doc->getElementsByTagName('p');
        //   $output = $doc->getElementByClass('r')->textContent;
        $db_link = query_raw($pdo, $link);
        $words = array();
        foreach($htmlNodes as $p){
            if(trim($p->textContent)){
                $current = explode(' ', $p->textContent);
                foreach($current as $w){
                    $c_w = preg_replace("/[^\w']|\d+/i", "", $w);
                    if(strlen($c_w) >= 2 && strlen($c_w) < 50){
                        $words[$c_w] = $words[$c_w] ? $words[$c_w] + 1 : 1;
                    }
                }
            }   
        }
        insert_words($pdo, $words, $db_link->pk);
    }catch(\Exception $e){
        throw new \Exception($e->getMessage(), (int)$e->getCode());
    }   
  }
  $result3 = query_stat($pdo, $link);
  $total_unique_words = count($result3);

echo <<< _END
<html>
  <head>
    <title>PHP Development with Docker Part 2</title>
    <link rel="stylesheet" href="styles.css"/>
  </head>
  <body>
  
  <h3>The words statistics in the article</h3>
  <a href="$link">$link</a>
  <hr/>
  <a href="index.php">Select different article</a>
  <hr/>
_END;

echo "<div class=\"table_container\"><table><tr><th>Word</th><th>Occurrence</th></tr><tbody>";
foreach($result3 as $r){
  echo "<tr><td>$r->word</td><td>$r->occurrence</td></tr>";
}
echo "<tfoot><tr><td>Total Unique Words</td><td>$total_unique_words</td></tr></tfoot></tbody></table> </div>";

echo <<< _END
  </body>
</html>
_END;
?>
