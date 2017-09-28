<?php 
  ////////////////////////////////////////////////////////////////////////////////////////
  function FirstInResult($res, $id)
  {
    $r=$res->fetch_assoc();
    return $r[$id];
  }

  ////////////////////////////////////////////////////////////////////////////////////////
  function CurrentDepositId($city_id)
  {
    global $mysqli;
    $deposit_id=NULL;
    if($res=$mysqli->query("SELECT deposit_id
                            FROM deposit 
                            WHERE city_id=".$city_id."
                            ORDER BY date DESC
                            LIMIT 1"))
    {
      if($res->num_rows==1)
      {
        $deposit_id=FirstInResult($res,'deposit_id');
      }
      $res->close();
    }
    return $deposit_id;
  }

  ////////////////////////////////////////////////////////////////////////////////////////
  function processPlayer($plrJSON)
  {
    global $mysqli;
    $plr=json_decode($plrJSON, true);
    foreach($plr['city'] as $val)
    {
      if(is_numeric($val['id']) && is_numeric($val['population']) && is_numeric($val['pos_ref']) 
         && is_numeric($val['pos_x']) && is_numeric($val['pos_y']) )
      {
        $climate_id=0;
        if($res=$mysqli->query("SELECT id FROM climate_name WHERE name='".$mysqli->real_escape_string($val['climate'])."'"))
        {
          if($res->num_rows==1)
          {
            $climate_id=FirstInResult($res,'id');
          }
          $res->close();
        }
        

        if($res=$mysqli->query("SELECT climate_id FROM city WHERE id=".$val['id'].""))
        {
          if($res->num_rows==1 && FirstInResult($res,'climate_id')==0)
          {
            $sql="UPDATE city
                  SET climate_id=".$climate_id.",
                      hill=".(strlen(trim($val['hill']))>1 ? '1' : '0').",
                      pos_ref=".$val['pos_ref'].",
                      pos_x=".$val['pos_x'].",
                      pos_y=".$val['pos_y']."
                  WHERE id=".$val['id'].";";
            $mysqli->query($sql);
          }
          $res->close();
        }
        
        $deposit_id=0;
        if(strlen($val['deposit'])>0)
        {
          if($res=$mysqli->query("SELECT id FROM deposit_name WHERE name='".$mysqli->real_escape_string($val['deposit'])."'"))
          {
            if($res->num_rows==1)
            {
              $deposit_id=FirstInResult($res,'id');
            }
            $res->close();
          }
        }

        if($deposit_id!=CurrentDepositId($val['id']))
        {
          $mysqli->query("INSERT INTO deposit (city_id, deposit_id)
                          VALUES (".$val['id'].",".$deposit_id.")");
        }

      }

    }
  }



  ////////////////////////////////////////////////////////////////////////////////////////
  function processServodata($servodata, $show)
  {
    global $mysqli;
    if($show) echo 'Данные обнаружены';
    if(!is_array($data=json_decode($servodata, true)))
    {
      if($show) echo '<br>*** ошибка первичной обработки';
      return false;
    }
    if(!is_string($data['account']['name']))
    {
      if($show) echo '<br>*** ошибка определения имени игрока';
      return false;
    }
    $player_name=$data['account']['name'];
    if($show) echo '<br>Данные о игроке '.htmlentities($player_name, ENT_COMPAT | ENT_HTML401,'UTF-8');

    $res=$mysqli->query("
      SELECT player.id
      FROM player
      WHERE player.name='".$player_name."'
    ");
    if($res->num_rows!=1)
    {
      if($show) echo "<br>*** Игрока ".htmlentities($player_name, ENT_COMPAT | ENT_HTML401,'UTF-8')." нет в списке игроков";
      return false;
    }
    $r=$res->fetch_assoc();
    $res->free();
    $player_id=$r['id'];

    if(is_array($data['town']))
    {
      if($show) echo '<br>Город: '.htmlentities($data['town']['name'], ENT_COMPAT | ENT_HTML401,'UTF-8');
      $town_name=$data['town']['name'];
      $atkFound=false;
      if(is_array($data['events']))
      {
        foreach($data['events'] as $ev)
        {
          if($ev['event']==101)
          {
            if( is_numeric($ev['start']) && is_numeric($ev['time']) 
                && is_numeric($ev['town1']) && is_numeric($ev['town2']) 
                && is_numeric($ev['data']['speed']) && is_numeric($ev['id']) ) 
            {
              $res=$mysqli->query("
                SELECT city.name AS c_name, player.id AS p_id, player.name AS p_name
                FROM city
                JOIN player ON player.id=city.player_id
                WHERE city.id=".$ev['town1']."
              ");
              if($res->num_rows!=1)
              {
                if($show) echo "<br>*** данные о городе ".$ev['town1']." не обнаружены в списке городов";
                continue;
              }
              $r=$res->fetch_assoc();
              $res->free();
              $town1_name=$r['c_name'];
              $town1_id=$ev['town1'];
              $player1_name=$r['p_name'];
              $player1_id=$r['p_id'];
              if($player1_id==$player_id) continue; // это исходящая атака, ее не учитываем

              $res=$mysqli->query("
                SELECT city.name AS c_name, player.id AS p_id, player.name AS p_name
                FROM city
                JOIN player ON player.id=city.player_id
                WHERE city.id=".$ev['town2']."
              ");
              if($res->num_rows!=1)
              {
                if($show) echo "<br>*** данные о городе ".$ev['town2']." не обнаружены в списке городов";
                continue;
              }
              $r=$res->fetch_assoc();
              $res->free();
              $town2_name=$r['c_name'];
              $town2_id=$ev['town1'];
              $player2_name=$r['p_name'];
              $player2_id=$r['p_id'];

              $res=$mysqli->query("SELECT id FROM atak_in WHERE id=".$ev['id']." ");
              $fSaved=false;
              if($res->num_rows!=0)
              {
                $fSaved=true;
              }

              if($show) echo "<br> ".($fSaved ? "Повторное сообщение об атаке" : "Обнаружена новая атака")." из 
                             ".htmlentities($town1_name, ENT_COMPAT | ENT_HTML401,'UTF-8')."
                             (".htmlentities($player1_name, ENT_COMPAT | ENT_HTML401,'UTF-8')."
                             ) в ".htmlentities($town2_name, ENT_COMPAT | ENT_HTML401,'UTF-8')./*"
                             отправлена ".date('H:i:s d.m',($ev['start']-3600)).*/" придет 
                             ".date('H:i:s d.m',($ev['time']+7*3600))." скорость ".$ev['data']['speed'].($ev['data']['type']=='a' ? ' Авиа' : '');

              if(!$fSaved)
              {
                $mysqli->query("
                  INSERT INTO atak_in (id, from_id, to_id, from_time, to_time, speed, type) 
                         VALUES (".$ev['id']."
                                 ,".$ev['town1']."
                                 ,".$ev['town2']."
                                 ,FROM_UNIXTIME(".$ev['start'].")
                                 ,FROM_UNIXTIME(".$ev['time'].")
                                 ,".$ev['data']['speed']."
                                 ,".($ev['data']['type']=='a' ? 2 :1)."
                                 )");
              }
        else
        {
                $mysqli->query("UPDATE atak_in
                      SET type=".($ev['data']['type']=='a' ? 2 : 1)."
                      WHERE id=".$ev['id'].";");
        }

              $atkFound=true;
            }
            else
            {
              if($show) echo '<br>*** Ошибка в формате данных об атаке';
              return false;
            }
          }
        }
      }

      if($show && (!$atkFound)) echo '<br> Данные об атаках не обнаружены';


      // выборка данных о застройке города
      if(is_array($data['town']['build']['state']))
      {
        if($show) echo '<br> Застройка города:';
        foreach($data['town']['build']['state'] as $slot_id => $slot_val)
        {
          if($show)
          {
            $res=$mysqli->query("SELECT name FROM build_name WHERE build_id=".$slot_val[0]." ");
            if($res->num_rows!=0)
            {
              $r=$res->fetch_assoc();
              echo '<br> Слот '.$slot_id.': '.$r['name'].'('.$slot_val[1].')';
            }
            else
            {
              echo '<br> Слот '.$slot_id.': '.$slot_val[0].'('.$slot_val[1].')';
            }
          }
          $res=$mysqli->query("SELECT city_id FROM city_build WHERE city_id=".$data['town']['id']." AND slot=".$slot_id." ");
          if($res->num_rows!=0)
          {
            $mysqli->query("UPDATE city_build
                  SET build_id=".($slot_val[0]).",
                      level=".($slot_val[1])."
                  WHERE id=".$ev['id'].";");
          }
          else
          {
            $mysqli->query("
              INSERT INTO city_build (city_id, slot, build_id, level) 
                     VALUES (".$data['town']['id']."
                             ,".$slot_id."
                             ,".$slot_val[0]."
                             ,".$slot_val[1]."
                             )");
          }
        }
      }
      
      /*
      $res=processTown($data['account'], $data['town']);
      if($res)
      {
        echo ' *** '.$res;
        break;
      }
      */
    }
    else
    {
      if($show) echo '<br>*** информация о городах не обнаружена';
      return false;
    }

    return true;
  }

?>