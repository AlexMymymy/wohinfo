<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
<title>Застройка городов</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<?php 
  include 'connectdb.php';
?>
<div align="left" style="margin-bottom:2em;"><a href="index.php">Главная страница</a></div>
<div align="center" style="width:100%;">Экономика:</div>
<div><form id="filter" method="get" action="viewbuild.php" name="filter" enctype="application/x-www-form-urlencoded">
<div>
Застройка:<br>
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

<span>Игрок:
<select name="player" onchange="this.form.submit();">
  <option value="0"> </option>
<?php
  $sql="
  SELECT id, name
    FROM player
    ". ($_GET['country'] ? ('WHERE country_id='.$_GET['country']) : '')."
    ORDER BY population DESC
  ";
  $res=$mysqli->query($sql);
  if($res)
  {
    while ($cr = $res->fetch_assoc()) 
    {
      echo '<option value="'.$cr['id'].'"'.($cr['id']==$_GET['player'] ? ' selected' : '').'>'.$cr['name'].'</option>';
    }
    $res->free();
  }
?> 
</select>
</span>

<span>Город:
<select name="city" onchange="this.form.submit();">
  <option value="0"> </option>
<?php
  $sql="
  SELECT city.id, city.name
    FROM city
    JOIN player ON city.player_id=player.id
    WHERE ".($_GET['country'] ? ('player.country_id='.$_GET['country']) : '1')."
          AND ".($_GET['player'] ? ('player.id='.$_GET['player']) : '1')."
    ORDER BY player.population DESC, city.id
  ";
  $res=$mysqli->query($sql);
  if($res)
  {
    while ($cr = $res->fetch_assoc()) 
    {
      echo '<option value="'.$cr['id'].'"'.($cr['id']==$_GET['city'] ? ' selected' : '').'>'.$cr['name'].'</option>';
    }
    $res->free();
  }
?> 
</select>
</span>
</div>

</form>
<?php
  $sql="
  SELECT city.id AS city_id, player.id AS player_id, player.name AS p_name, city.name AS c_name, city.population AS c_pop, deposit_name.name AS dt_name,
         climate_name.name AS clim_name, ncity.n_city AS n_city
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
      ". ($_GET['city'] ? ('city.id='.$_GET['city']) :
         ($_GET['player'] ? ('player.id='.$_GET['player']) : 
         ($_GET['country'] ? ('player.country_id='.$_GET['country']) :  '1')))."
    ORDER BY player.population DESC, city.id ASC
  ";
  $res=$mysqli->query($sql);
  if($res)
  {
    echo '<br>Выбрано записей '.$res->num_rows;
    echo '<div class="divTable">';
    echo '<div class="divHead">
            <div class="divCell" style="width:10%; padding:10px 0;">Игрок</div>
            <div class="divCell" style="width:13%; padding:10px 0;">Город</div>
            <div class="divCell" style="width:8%; padding:10px 0;">Насел.</div>
            <div class="divCell" style="width:14%; padding:10px 0;">Месторождение</div>
            <div class="divCell" style="width:7%; padding:10px 0;">Климат</div>
            <div class="divCell" style="width:48%; padding:10px 0;">Строения</div>
          </div>';
    while ($r = $res->fetch_assoc()) 
    {
      $cls=($r['type']==2 ? "divCellA" : "divCell");
      echo '<div class="divRow">';
      echo '<div class="divCell"divCell" style="width:10%;"><a href=https://ru28.waysofhistory.com/#/account/'.$r['player_id'].'>'.$r['p_name'].'</a></div>';
      echo '<div class="divCell" style="width:13%;"><a href=https://ru28.waysofhistory.com/#/townInfo/'.$r['city_id'].'>'.$r['c_name'].'</a></div>';
      echo '<div class="divCell" style="width:8%;">'.$r['c_pop'].'</div>';
      echo '<div class="divCell" style="width:14%;">'.$r['dt_name'].'&nbsp</div>';
      echo '<div class="divCell" style="width:7%;">'.$r['clim_name'].'&nbsp</div>';
      echo '<div class="divCell" style="width:48%;">';
      $res1=$mysqli->query("SELECT city_build.slot, build_name.name, city_build.level
                              FROM city_build
                                JOIN build_name ON build_name.build_id=city_build.build_id
                              WHERE
                                city_build.city_id=".$r['city_id']."
                              ORDER BY city_build.slot
      ");
      $r1=$res1->fetch_assoc();
      for($n=0; $n<19; ++$n)
      {
        if($r1 && $r1['slot']==$n)
        {
          echo '<div style="width:25%; float:left;">Slot '.$n.' - '.$r1['name'].'('.$r1['level'].')</div>';
          $r1=$res1->fetch_assoc();
        }
        else
        {
          echo '<div style="width:25%; float:left;">Slot '.$n.' - ?????</div>';
        }
      }
      echo '</div>';
      echo '</div>';
    }

    echo '</div>';
    $res->free();
  }
  else
  {
    die($sql ."<br> error: " . $mysqli->error);
  }
?>
</body>
</html>
