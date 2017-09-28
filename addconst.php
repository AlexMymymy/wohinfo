<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
<title>Обновление константных данных</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>

<?php 
  include 'connectdb.php';
  echo 'getting const.js ...';
  $s = file_get_contents("http://w23.wofh.ru/js/gen/const.js", "r");
  if(!preg_match('/(\{.*\}\}\})/', $s, $str))
  {
    echo ' fail<br>';
    exit();
  }
  echo ' got<br>';
  echo 'parsing ...<br>';
  $data=json_decode($str[1], true);
  foreach($data['builds'] as $k=>$b)
  {
    $servodata=mysql_real_escape_string($servodata);
    $k1=mysql_real_escape_string($servodata);
    $sql="REPLACE INTO builds (build_id,name) VALUES ('".mysql_real_escape_string($k)."','".mysql_real_escape_string($b["name"])."')";
    $result = mysql_query($sql);
  }
  echo '<br>DONE<br>';

  $sql="SELECT build_id,name FROM builds ORDER BY build_id";
  $result = mysql_query($sql);
  echo "<table>";
  while ($row = mysql_fetch_array($result, MYSQL_NUM)) 
  {
    echo "<tr><td>".$row[0]."</td><td>".$row[1]."</td></tr>";
  }
  echo "</table>";


?>

</body>
</html>
