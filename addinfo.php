<?php 
/*  header('Access-Control-Allow-Origin: https://w23.wofh.ru');*/
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
//      if($key=='player') processPlayer($val);
    }
  }        

  
  /* 
  $servodata=$_POST['servodata'];
  $fp = fopen('log.txt', 'w');
  foreach($_POST as $key => $val)
  {
    fwrite($fp, $key." = ".$val."\r\n\r\n");
  }
  $servodata=mysql_real_escape_string($servodata);
  $sql="INSERT INTO info (ip,servodata) VALUES ('".$extip."','".$servodata."')";
  fclose($fp);
  $result = mysql_query($sql);
  */
?>