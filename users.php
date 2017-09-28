<?php $req_access=0; require 'login.php'; ?>
<?php
  if(isset($_POST['newname']) && isset($_POST['newpassword']) && isset($_POST['newaccess']) )
  { // добавление нового пользователя
    $mysqli->query("
    INSERT INTO users (name, password, access) 
      VALUES ('".$mysqli->real_escape_string($_POST['newname'])."'
             ,'".$mysqli->real_escape_string($_POST['newpassword'])."'
             ,".$mysqli->real_escape_string($_POST['newaccess'])." )
    ");
  }
?>
<div align="left" style="margin-bottom:2em;"><a href="index.php">Главная страница</a></div>
<div align="center">Список пользователей:</div>

<table cellspacing="0" cellpadding="2" border="0" width="100%">
  <thead>
    <tr>
      <th style="width:20%;">Имя</th>
      <th style="width:20%;">Пароль</th>
      <th style="width:20%;">Разрешение доступа</th>
      <th style="width:*;">Время входа</th>
    </tr>
  </thead>
  <tbody>
<?php
  $res=$mysqli->query("
  SELECT id, name, password, access, MAX(date) AS date
    FROM users
    LEFT JOIN user_cookie ON users.id=user_cookie.user_id
    GROUP BY id
    ORDER BY name
  ");
  if($res)
  {
    while ($r = $res->fetch_assoc()) 
    {
      echo '<tr>';
      echo '<td style="padding:5px;">'.$r['name'].'</td>';
      echo '<td style="padding:5px;">'.$r['password'].'</td>';
      echo '<td style="padding:5px;">'.$r['access'].'</td>';
      echo '<td style="padding:5px;">'.$r['date'].'</td>';
      echo '</tr>';
    }
    $res->free();
  }
?> 
  </tbody>
</table>

<div class="newuser">
Новый пользователь
<div class="newuser_info">
<form id="newuser" method="POST" action="users.php" name="newuser" enctype="application/x-www-form-urlencoded">
<input type = "text" class = "newuser_text" name = "newname" placeholder = "Имя" required>
<input type = "text" class = "newuser_text" name = "newpassword" placeholder = "Пароль" required>
<input type = "number" class = "newuser_text" name = "newaccess" placeholder = "Разрешения" required min="0" max="255">
<button class = "form-button" type = "submit">добавить</button>
</form>
</div>
</div>

</body>
</html>
