<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
<title>Состояние всех городов</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<div align="left" style="margin-bottom:2em;"><a href="index.htm">Главная страница</a></div>
<div align="center" style="width:100%;">Состояние городов:</div>
<?php 
  include 'f1.php';
  include 'connectdb.php';
  echoAllTownsTable();
?>
</body>
</html>
