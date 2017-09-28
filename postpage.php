<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
<title>WOFH отправить данные</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<div align="left" style="margin-bottom:2em;"><a href="index.php">Главная страница</a></div>
<div align="left" style="margin-bottom:2em;">Для подготовки данных:
<br>- перейдите в город, на котоый идет атака
<br>- перейдите на карту
<br>- откройте исходный код страниц (нажать Ctrl + U)
<br>- выделите весь код (нажать Ctrl + A)
<br>- скопируйте (нажать Ctrl + С)
<br>- вставьте в окошко (указатель в окно ввода и нажать Ctrl + V)
<br>- нажать кнопку Передать
<br>- посмотреть что все правильно передалось
</div>
<div align="center" style="width:100%;">Исходный код страницы карты:</div>
<div>
<form id="pagecode" method="POST" action="getpagecode.php" name="pagecode" enctype="application/x-www-form-urlencoded">
  <div style="width:100%;">
    <textarea name="pagecode" class="inpText" placeholder="Вставьте сюда исходный текст страницы карты, открытой из города в который идут атаки" rows="20"></textarea>
  </div>
  <div align="center" style="width:100%;"><input type="submit" value="Передать"></div>
</form>
</div>
</body>
</html>
