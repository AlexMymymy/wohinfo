<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
<title>Атаки на страну</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<?php 
  include 'connectdb.php';
?>
<div align="left" style="margin-bottom:2em;"><a href="index.php">Главная страница</a></div>
<div align="center" style="width:100%;">Атаки:</div>
<div><form id="filter" method="get" action="viewatak.php" name="filter" enctype="application/x-www-form-urlencoded">
<div>
Атакует:<br>
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

<div>
<input type="checkbox" name="viewold" id="viewold" value="y" onchange="this.form.submit();" 
  <?php echo ($_GET['viewold']=='y' ? 'checked' : ''); ?> >
<label for="viewold">Показывать прошедшие атаки</label>
</div>

</form>
<div align="right">
<a href='atak.txt' download>Скачать файлом</a>
</div>
</div>
<?php
  $sql="
  SELECT city_from.name AS cf_name, city_from.id AS cf_id, city_from.population AS cf_pop, player_from.name AS pf_name, city_from.player_id AS pf_id, 
         city_to.name AS ct_name, city_to.id AS ct_id, city_to.population AS ct_pop, player_to.name AS pt_name, city_to.player_id AS pt_id, 
         deposit_name.name AS dt_name,
         atak_in.from_time, atak_in.to_time, atak_in.speed
    FROM atak_in
      JOIN city AS city_from ON atak_in.from_id=city_from.id
      JOIN city AS city_to ON atak_in.to_id=city_to.id
      JOIN player AS player_from ON city_from.player_id=player_from.id
      JOIN player AS player_to ON city_to.player_id=player_to.id
      LEFT JOIN (SELECT deposit.city_id, deposit.deposit_id 
                   FROM deposit 
                   JOIN (SELECT t.city_id, MAX(t.date) AS mdate FROM deposit AS t GROUP BY t.city_id ) AS t1
                   ON (deposit.city_id=t1.city_id AND deposit.date=t1.mdate)
                   ) AS dep ON city_to.id=dep.city_id
      LEFT JOIN deposit_name ON dep.deposit_id=deposit_name.id
      JOIN climate_name ON climate_name.id=city_to.climate_id
      JOIN (SELECT player_id, COUNT(player_id) AS n_city FROM city GROUP BY player_id) AS ncity ON city_to.player_id=ncity.player_id
    WHERE
      ". ($_GET['city'] ? ('city_from.id='.$_GET['city']) :
         ($_GET['player'] ? ('player_from.id='.$_GET['player']) : 
         ($_GET['country'] ? ('player_from.country_id='.$_GET['country']) :  '1')))."
      AND ". ($_GET['deposit'] ? ('dep.deposit_id='.$_GET['deposit']) : '1')."
      AND ". ($_GET['viewold']!='y' ? 'atak_in.to_time>NOW()' : '1')."
      
    ORDER BY from_time ASC
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
            <div class="divCell" style="width:10%; padding:10px 0;">Отправлена</div>
            <div class="divCell" style="width:4%; padding:10px 0;">Скор</div>
            <div class="divCell" style="width:10%; padding:10px 0;">Игрок</div>
            <div class="divCell" style="width:13%; padding:10px 0;">Город</div>
            <div class="divCell" style="width:8%; padding:10px 0;">Насел.</div>
            <div class="divCell" style="width:14%; padding:10px 0;">Месторождение</div>
            <div class="divCell" style="width:10%; padding:10px 0;">Приход</div>
          </div>';
    $fp = fopen("atak.txt", "w");
    while ($r = $res->fetch_assoc()) 
    {
      echo '<div class="divRow">';
      echo '<div class="divCell" style="width:10%;"><a href=https://w23.wofh.ru/#/account/'.$r['pf_id'].'>'.$r['pf_name'].'</a></div>';
      echo '<div class="divCell" style="width:13%;"><a href=https://w23.wofh.ru/#/townInfo/'.$r['cf_id'].'>'.$r['cf_name'].'</a></div>';
      echo '<div class="divCell" style="width:8%;">'.$r['cf_pop'].'</div>';
      echo '<div class="divCell" style="width:10%;">'.date('H:i:s d.m',strtotime($r['from_time'])).'</div>';
      echo '<div class="divCell" style="width:4%;">'.$r['speed'].'</div>';
      echo '<div class="divCell" style="width:10%;"><a href=https://w23.wofh.ru/#/account/'.$r['pt_id'].'>'.$r['pt_name'].'</a></div>';
      echo '<div class="divCell" style="width:13%;"><a href=https://w23.wofh.ru/#/townInfo/'.$r['ct_id'].'>'.$r['ct_name'].'</a></div>';
      echo '<div class="divCell" style="width:8%;">'.$r['ct_pop'].'</div>';
      echo '<div class="divCell" style="width:14%;">'.$r['dt_name'].'&nbsp</div>';
      echo '<div class="divCell" style="width:10%;">'.date('H:i:s d.m',strtotime($r['to_time'])).'</div>';
      echo '</div>';
      fwrite($fp, $r['pf_id']."\t".$r['pf_name']."\t".$r['cf_id']."\t".$r['cf_name']."\t".$r['cf_pop']."\t".$r['from_time']."\t".$r['speed']."\t
                  ".$r['pt_id']."\t".$r['pt_name']."\t".$r['ct_id']."\t".$r['ct_name']."\t".$r['ct_pop']."\t".$r['to_time']."\t
                  ".$r['dt_name']."\r\n");

    }
    fclose($fp);

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
