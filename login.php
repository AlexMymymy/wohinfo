<?php
  require 'connectdb.php';
  $psw_error=0;
  $user_ok=0;
  if(isset($_POST['username']) && isset($_POST['password']) )
  { // проверка введенных имени и пароля
    $res=$mysqli->query("
    SELECT id, name, access
      FROM users
      WHERE name='".$mysqli->real_escape_string($_POST["username"])."'
            AND password='".$mysqli->real_escape_string($_POST["password"])."'
    ");
    if($res)
    {
      if($res->num_rows == 1)
      {
        $r=$res->fetch_row();
        $user_id=$r[0];
        $user_name=$r[1];
        $user_access=$r[2];
        if(isset($_POST['remember']))
        {
          $user_cookie=$user_name.' '.com_create_guid();
          if(setcookie ("userid", $user_cookie, time()+24*3600))
          {
            $mysqli->query("
            INSERT INTO user_cookie
              (user_id, cook_val) VALUES 
              (".$user_id.",'".$mysqli->real_escape_string($user_cookie)."')
            ");
          }
        }
        $user_ok=1;
      }
      $res->close();
    }
    $psw_error=1;
  }
  else
  { // имя и пароль не вводились, проверка куков
    $res=$mysqli->query("
    SELECT user_id
      FROM user_cookie
      WHERE cook_val='".$mysqli->real_escape_string($_COOKIE["userid"])."'
    ");
    if($res)
    {
      if($res->num_rows == 1)
      {
        $r=$res->fetch_row();
        $user_id=$r[0];
        $res->close();
        $res=$mysqli->query("
        SELECT access, name
          FROM users
          WHERE id=".$user_id."
        ");
        if($res)
        {
          if($res->num_rows == 1)
          {
            $r=$res->fetch_row();
            $user_access=$r[0];
            $user_name=$r[1];
            $mysqli->query("
            UPDATE user_cookie 
              SET date=CURRENT_TIMESTAMP
              WHERE cook_val='".$mysqli->real_escape_string($_COOKIE["userid"])."'
            ");
            $user_ok=1;
          }
          $res->close();
        }
      }
    }
  }
  if($user_ok && isset($req_access) && $req_access>0 && ($user_access & $req_access)==0)
  { // нет доступа к запрашиваемой странице
    echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <title>Доступ ограничен</title>
      <style>
         body {
            background-color: #FFCFCF;
            height: 95vh;
            width:  95vw;
         }
         .container {
            height: 100%;
            width: 100%;
            text-align: center;
         }
         .inner {
            margin-top: 30vh;
            padding: 20px;
            background-color: #FF7F7F;
            display: inline-block;
            text-align: center;
            border-radius: 5px;
            font-size: 30px;
            font-family: Geneva, Arial, Helvetica, sans-serif;
         }
      </style>
   </head>
   <body>
      <div class = "container">
        <div class = "inner">
          У Вас нет доступа к этой странице
        </div>
        <br><br><a href="/">Перейти на главную страницу</a>
      </div> 
   </body>
</html>';
    exit(0);
  }
  if($user_ok)
  { // у пользователя есть доступ
    echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
  <title>Общая информация</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link href ="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div align="center" style="font-family: Geneva, Arial, Helvetica, sans-serif;">
  <a href="logout.php" style="float:right; margin-left: 5em">Выход</a>
  <div><b>Здравствуйте, '.$user_name.'</b></div> 
</div>
    ';
    return;
  } 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <title>Вход в систему</title>
      
      <style>
         body {
            background-color: #CFCFCF;
            height: 95vh;
            width:  95vw;
         }

         .container {
            height: 100%;
            width: 100%;
            text-align: center;
         }

         .inner {
            margin-top: 20vh;
            background-color: #7F7F7F;
            display: inline-block;
            text-align: center;
            border-radius: 1em;
         }

         .form-signin {
            max-width: 330px;
            padding: 15px;
            margin: 0 auto;
            color: #017572;
         }
         
         .form-signin .checkbox {
            font-weight: normal;
            position: relative;
         }
         
         .form-signin .form-control {
            height: auto;
            padding: 10px;
            font-size: 16px;
            text-align: left;
            margin-bottom: 10px;
            border-color:#017572;
         }
         
         .form-signin .form-button {
            padding: 5px 1em 5px 1em;
            font-size: 16px;
         }
         
         .pswerr {
            font-size: 20px;
            font-family: Arial, Verdana;
            color: red;
            margin-top: 2em;
            text-align: center;
         }
         
      </style>
      
   </head>
        
   <body>
      
      <div class = "container">
        <div class = "inner">
         <form class = "form-signin" role = "form" method = "POST">
            <h4 class = "form-signin-heading"></h4>
            <input type = "text" class = "form-control" name = "username" placeholder = "Имя" required autofocus><br>
            <input type = "password" class = "form-control" name = "password" placeholder = "Пароль" required><br>
            <input type = "checkbox" class = "form-control" name = "remember" checked>Запомнить меня<br>
            <button class = "form-button" type = "submit">вход</button>
         </form>
        </div> 
        <?php if($psw_error>0) echo '<div class="pswerr">Неверные имя или пароль</div>'?>               
      </div> 
      
   </body>
</html>
<?php exit(0); ?>
