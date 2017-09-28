<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
<title>Список страны</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<?php 
  include 'connectdb.php';
?>
<div align="left" style="margin-bottom:2em;"><a href="index.php">Главная страница</a></div>
<div align="center" style="width:100%;">Список:</div>
<div><form id="filter" method="get" action="viewcountry.php" name="filter" enctype="application/x-www-form-urlencoded">
<div>
<span>Страна:
<select name="country" onchange="this.form.submit();">
  <option value="0"> </option>
<?php
  $res=$mysqli->query("
  SELECT id, name
    FROM country
    ORDER BY population DESC
  ");
  if($res)
  {
    while ($cr = $res->fetch_assoc()) 
    {
      echo '<option value="'.$cr['id'].'"'.($cr['id']==$_GET['country'] ? ' selected' : '').'>'.$cr['name'].'</option>';
    }
    $res->free();
  }
?> 
</select>
</span>

</div>


</form>
</div>
<?php
  $sql="
  SELECT city.name AS c_name, city.id AS c_id, city.population, player.name AS p_name, city.player_id AS p_id, 
         deposit_name.name AS d_name, climate_name.name AS climate, ncity.n_city
    FROM city 
      JOIN player ON city.player_id=player.id
      LEFT JOIN (SELECT deposit.city_id, deposit.deposit_id 
                   FROM deposit 
                   JOIN (SELECT t.city_id, MAX(t.date) AS mdate FROM deposit AS t GROUP BY t.city_id ) AS t1
                   ON (deposit.city_id=t1.city_id AND deposit.date=t1.mdate)
                   ) AS dep ON city.id=dep.city_id
      LEFT JOIN deposit_name ON dep.deposit_id=deposit_name.id
      JOIN climate_name ON climate_name.id=city.climate_id
      JOIN (SELECT player_id, COUNT(player_id) AS n_city FROM city GROUP BY player_id) AS ncity ON city.player_id=ncity.player_id
    WHERE
      ". 'player.country_id='.($_GET['country'] ? $_GET['country'] : '0')."
    ORDER BY player.population DESC, city.id ASC
  ";
  $res=$mysqli->query($sql);
  if($res)
  {
    echo '<br>Выбрано городов '.$res->num_rows;
    echo '<div class="divTable">';
    echo '<div class="divHead">
            <div class="divCell" style="width:8%; padding:10px 0;">id игрока</div>
            <div class="divCell" style="width:27%; padding:10px 0;">Игрок</div>
            <div class="divCell" style="width:8%; padding:10px 0;">id города</div>
            <div class="divCell" style="width:27%; padding:10px 0;">Город</div>
            <div class="divCell" style="width:8%; padding:10px 0;">Население</div>
            <div class="divCell" style="width:12%; padding:10px 0;">Месторождение</div>
            <div class="divCell" style="width:10%; padding:10px 0;">Климат</div>
          </div>';
    $n_city=0;
    $n_pop=0;
    while ($r = $res->fetch_assoc()) 
    {
      echo '<div class="divRow">';
      echo '<div class="divCell" style="width:8%;">'.$r['p_id'].'</div>';
      echo '<div class="divCell" style="width:27%;">'.$r['p_name'].'</div>';
      echo '<div class="divCell" style="width:8%;">'.$r['c_id'].'</div>';
      echo '<div class="divCell" style="width:27%;">'.$r['c_name'].'</div>';
      echo '<div class="divCell" style="width:8%;">'.$r['population'].'</div>';
      echo '<div class="divCell" style="width:12%;">'.$r['d_name'].'&nbsp</div>';
      echo '<div class="divCell" style="width:10%;">'.$r['climate'].'&nbsp</div>';
      echo '</div>';
      $n_city=$n_city+1;
      $n_pop=$n_pop+$r['population'];

    }
    /*
    echo '<div class="divRow">';
    echo '<div class="divCell" style="width:35%; padding:10px 0;">ИТОГО</div>';
    echo '<div class="divCell" style="width:35%; padding:10px 0;">'.$n_city.'</div>';
    echo '<div class="divCell" style="width:15%; padding:10px 0;">'.$n_pop.'</div>';
    echo '<div class="divCell" style="width:15%; padding:10px 0;">&nbsp</div>';
    echo '</div>';
    */

    echo '</div>';
    $res->free();
  }
  else
  {
    die(/*$sql .*/"<br> error: " . $mysqli->error);
  }
  
?>
</body>
</html>
