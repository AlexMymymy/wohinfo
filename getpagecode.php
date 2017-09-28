<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
<title>WOFH - Прием данных</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<div align="left" style="margin-bottom:1em;"><a href="index.php">Главная страница</a></div>
<div align="left" style="margin-bottom:2em;"><a href="postpage.php">Передать еще данные</a></div>
<div align="center" style="width:100%;">
Результат обработки данных:
<div align="left" style="margin:1em; padding:1em; border: 1px solid black;">
<?php
  do
  {
    include 'connectdb.php';
    include 'func.php';
    $extip=$_SERVER['REMOTE_ADDR'];
    $pagedata=$_POST['pagecode'];
    if(!preg_match('/var servodata = (.*)/', $pagedata, $m))
    {
      echo '<br>*** данные для обработки не обнаружены';
      break;
    }
    $servodata=$m[1];
    $sql="INSERT INTO postdata (extip,name,value)
          VALUES ('".$extip."',
          'servodata',
          '".$mysqli->real_escape_string($servodata)."');";
    $mysqli->query($sql);
    if(!is_array($data=json_decode($servodata, true)))
    {
      echo '<br>*** ошибка первичной обработки';
    }
    else if(isset($data['socket']))
    {
      echo '<br>*** Эта страница города в новой графике, в ней нет нужных данных. Для перехода 
           к старой графике перейдите по ссылке 
           <a href="https://w23.wofh.ru/oldtown" target="_blank">https://w23.wofh.ru/oldtown</a>';
    }
    else if(processServodata($servodata, true))
    {
      echo '<br>Обработка завершена';
    }

    /*
    echo 'Данные обнаружены';
    if(!is_array($data=json_decode($servodata, true)))
    {
      echo '<br>*** ошибка первичной обработки';
      break;
    }
    if(!is_string($data['account']['name']))
    {
      echo '<br>*** ошибка определения имени игрока';
      break;
    }
    $player_name=$data['account']['name'];
    echo '<br>Данные о игроке '.htmlentities($player_name, ENT_COMPAT | ENT_HTML401,'UTF-8');
    if(is_array($data['town']))
    {
      echo '<br>Классический город: '.htmlentities($data['town']['name'], ENT_COMPAT | ENT_HTML401,'UTF-8');
      $atkFound=false;
      if(is_array($data['events']))
      {
        foreach($data['events'] as $ev)
        {
          if($ev['event']==101)
          {
          }
        }
      }*/

      /*
      $res=processTown($data['account'], $data['town']);
      if($res)
      {
        echo ' *** '.$res;
        return false;
      }
    }
    else
    {
      echo '<br>*** информация о городах не обнаружена';
      break;
    }

    echo '<br>Обработка завершена';
      */
  }while(0);
?>

</div>
</body>
</html>
