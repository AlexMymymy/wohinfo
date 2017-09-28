<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
<title>Статистика по странам</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<?php 
  include 'connectdb.php';
?>
<div align="left" style="margin-bottom:2em;"><a href="index.php">Главная страница</a></div>
<div align="center" style="width:100%;">Статистика:</div>

<?php
  include 'func.php';
  $res=$mysqli->query("SELECT name, value FROM postdata ORDER BY date ASC");
  while($val=$res->fetch_assoc())
  {
    if($val['name']=='player') processPlayer($val['value']);
  }

?>
<br>завершено
</body>
</html>
