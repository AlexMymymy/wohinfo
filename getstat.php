<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
<title>Обновление статистики с сервера</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>

<?php 
  include 'connectdb.php';
  echo 'getting statistic ...';

  $opts = array('http' =>
    array(
        'method'  => 'GET',
        'timeout' => 120 
    )
  );

  $context  = stream_context_create($opts);

  //$inf = file_get_contents('https://ru28.waysofhistory.com/aj_statistics', false, $context);
  $inf = file_get_contents('aj_statistics.txt');

  if($inf)
  {
    echo 'geted<br>decode ...';
    $inf=json_decode($inf, true);
    if($inf)
    {
      echo 'decoded<br>processing accounts ...';
      $n=0;
      foreach($inf['accounts'] as $id=>$acc)
      {
        $name=$acc[0];
        $race_id=$acc[1];
        $sex_id=$acc[2];
        $country_id=$acc[3];
        $role_id=$acc[8];
        $sql="INSERT INTO player (id, name, country_id, race_id, sex_id, role_id)
              VALUES (".$id."
                      ,'".$mysqli->real_escape_string($name)."'
                      ,".$mysqli->real_escape_string($country_id)."
                      ,".$mysqli->real_escape_string($race_id)."
                      ,".$mysqli->real_escape_string($sex_id)."
                      ,".$mysqli->real_escape_string($role_id)."
                     )
              ON DUPLICATE KEY UPDATE
                      name='".$mysqli->real_escape_string($name)."'
                      ,country_id=".$mysqli->real_escape_string($country_id)."
                      ,race_id=".$mysqli->real_escape_string($race_id)."
                      ,sex_id=".$mysqli->real_escape_string($sex_id)."
                      ,role_id=".$mysqli->real_escape_string($role_id)."
              ";
        if(!$mysqli->query($sql))
        {
          echo "Could not add player info: " . $mysqli->error . "<br>";
          break;
        }
        $n=$n+1;
      }
      echo $n;

      echo '<br>processing countries ...';
      $n=0;
      foreach($inf['countries'] as $id=>$cou)
      {
        $name=$cou[0];
        if(is_numeric($id))
        {
          $sql="INSERT INTO country (id, name)
                VALUES (".$id."
                        ,'".$mysqli->real_escape_string($name)."'
                       )
                ON DUPLICATE KEY UPDATE
                        name='".$mysqli->real_escape_string($name)."'
                ";
          if(!$mysqli->query($sql))
          {
            echo "Could not add country info: " . $mysqli->error . "<br>";
            break;
          }
        }
        $n=$n+1;
      }
      echo $n;

      echo '<br>processing towns ...';
      $n=0;
      foreach($inf['towns'] as $id=>$twn)
      {
        $name=$twn[0];
        $player_id=$twn[1];
        $pop=$twn[2];
        $chudo_lvl=($twn[3] ? substr($twn[3],0,2) : 0);
        $chudo_id=($twn[3] ? substr($twn[3],2,3) : 0);
        if(is_numeric($id) && is_numeric($player_id) && is_numeric($pop) && is_numeric($chudo_id) && is_numeric($chudo_lvl))
        {
          $sql="INSERT INTO city (id, name, player_id, chudo_id, chudo_lvl, population)
                VALUES (".$id."
                        ,'".$mysqli->real_escape_string($name)."'
                        ,".$player_id."
                        ,".$chudo_id."
                        ,".$chudo_lvl."
                        ,".$pop."
                       )
                ON DUPLICATE KEY UPDATE
                        name='".$mysqli->real_escape_string($name)."'
                        ,player_id=".$player_id."
                        ,chudo_id=".$chudo_id."
                        ,chudo_lvl=".$chudo_lvl."
                        ,population=".$pop."
                ";
          if(!$mysqli->query($sql))
          {
            echo "Could not add city info: " . $mysqli->error;
            break;
          }
          $sql="INSERT INTO population (city_id, population)
                VALUES (".$id."
                        ,".$pop."
                       )
                ";
          if(!$mysqli->query($sql))
          {
            echo "Could not add city population info: " . $mysqli->error;
            break;
          }
        }
        $n=$n+1;
      }
      echo $n;


      echo '<br>recalculating players population ...';
      $mysqli->query("UPDATE player SET population=0");
      $sql="UPDATE player,(SELECT player_id,SUM(population) AS ppop 
                           FROM city 
                           GROUP BY player_id) plrpop
            SET player.population=plrpop.ppop
            WHERE plrpop.player_id=player.id
           ";
      if(!$mysqli->query($sql))
      {
        echo " could not modify population info: " . $mysqli->error ;
      }
      else
      {
        echo " affected ".$mysqli->affected_rows;
      }
      
      echo '<br>recalculating countriess population ...';
      $mysqli->query("UPDATE country SET population=0");
      $sql="UPDATE country,(SELECT country_id,SUM(population) AS cpop 
                            FROM player
                            GROUP BY country_id) cntpop
            SET country.population=cntpop.cpop
            WHERE cntpop.country_id=country.id
           ";
      if(!$mysqli->query($sql))
      {
        echo " could not modify population info: " . $mysqli->error . "<br>";
      }
      else
      {
        echo " affected ".$mysqli->affected_rows;
      }
      
      echo '<br>deleting empty players ...';
      if(!$mysqli->query("DELETE FROM player WHERE population=0"))
      {
        echo " could not delete empty players: " . $mysqli->error ;
      }
      else
      {
        echo " affected ".$mysqli->affected_rows;
      }
    
      echo '<br>deleting empty countries ...';
      if(!$mysqli->query("DELETE FROM country WHERE population=0"))
      {
        echo " could not delete empty countries: " . $mysqli->error ;
      }
      else
      {
        echo " affected ".$mysqli->affected_rows;
      }
    
    
    
    
    
    }
    else echo 'fail<br>';
  }
  else echo 'fail<br>';

?>

</body>
</html>
