<?php
  include "t4.php";
  $psw_error=0;
  if(isset($_POST['username']) && isset($_POST['password']) )
  { // проверка введенных имени и пароля
    if($_POST['username']=='Admin' && $_POST['password']=='1')
    {
      $user_name='Admin';
      setcookie ("userid", ($user_name.' '.com_create_guid()), time()+3600);
      return; 
    }
    $psw_error=1;
  }
  else
  { // имя и пароль не вводились, проверка куков
    if(substr($_COOKIE['userid'],0,5)==='Admin')
    {
      $user_name='Admin';
      return; 
    }
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
            <input type = "text" class = "form-control" name = "username" placeholder = "Имя" required autofocus></br>
            <input type = "password" class = "form-control" name = "password" placeholder = "Пароль" required></br>
            <input type = "checkbox" class = "form-control" name = "remeber" checked>Запомнить меня</br>
            <button class = "form-button" type = "submit">вход</button>
         </form>
        </div> 
        <?php if($psw_error>0) echo '<div class="pswerr">Неверные имя или пароль</div>'?>               
      </div> 
      
   </body>
</html>
<?php exit(0); ?>
