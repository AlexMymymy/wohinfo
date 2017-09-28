<?php 
  header('Access-Control-Allow-Origin: https://ru28.waysofhistory.com');
  include 'connectdb.php';
  include 'func.php';
  $extip=$_SERVER['REMOTE_ADDR'];

  foreach($_POST as $key => $val)
  {
    if(strlen(trim($val))>0)
    {
      $sql="INSERT INTO postdata (extip,name,value)
            VALUES ('".$extip."',
            '".$mysqli->real_escape_string($key)."',
            '".$mysqli->real_escape_string($val)."');";
      $mysqli->query($sql);
      if($key=='player') processPlayer($val);
    }
  }        
?>