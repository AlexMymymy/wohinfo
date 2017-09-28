<?php 
  function processTown($account, $town)
  {
    $player_id=$account['id'];
    $player_name=mysql_real_escape_string($account['name']);
    $country_id=$account['country']['main'][0];
    $town_id=$town['id'];
    $town_name=mysql_real_escape_string($town['name']);
    $climate_id=mysql_real_escape_string($town['climate']);
    if( (!is_numeric($player_id)) or 
        (!$player_name) or 
        (!is_numeric($country_id)) or 
        (!is_numeric($town_id)) or 
        (!$town_name) or 
        (!$climate_id) )
    {
      return 'Ошибка обработки';
    }
    $sql="REPLACE INTO player (player_id, name, country_id) 
                  VALUES ($player_id , '$player_name', $country_id)";
    mysql_query($sql);

    $sql="REPLACE INTO town (town_id, name, player_id, climate_id) 
                  VALUES ($town_id , '$town_name', $player_id, $climate_id)";
    mysql_query($sql);

    $infarr=array($town['resources']['inc'],
                  $town['resources']['stream'],
                  $town['resources']['dec']);
    $val=null;
    foreach($infarr as $ik=>$iv)
    {
      $inf=explode('^',$iv);
      foreach($inf as $vv)
      {
        if($vv)
        {
          if(!$val[$vv{0}]) $val[$vv{0}]=array('0','0','0');
          $val[$vv{0}][$ik]=substr($vv,1);
          if(!is_numeric($val[$vv{0}][$ik]))
          {
            return 'Ошибка обработки (ресурсное число не число)';
          }
        }
      }
    }
    $sql="DELETE FROM townresource WHERE town_id=$town_id";
    mysql_query($sql);
    foreach($val as $k=>$v)
    {
      $sql="REPLACE INTO townresource (town_id, res_letter, inc, stream, decr) 
            VALUES ($town_id , '$k', $v[0], $v[1], $v[2])";
      mysql_query($sql);
    }

    $sql="DELETE FROM townbuild WHERE town_id=$town_id";
    mysql_query($sql);
    foreach($town['build']['state'] as $k=>$v)
    {
      if( (!is_numeric($k)) || (!is_numeric($v[0])) || (!is_numeric($v[1])) )
      {
        return 'Ошибка обработки (запись о строении)';
      }
      $sql="REPLACE INTO townbuild (town_id, slot, build_id, level) 
                    VALUES ($town_id , $k, ".$v[0].", ".$v[1].")";
      mysql_query($sql);

    }


    return NULL;
  }
  
  //----------------------------------------------------------------------------------------
  function plus_minus($val)
  {
    return ($val==0 ? '0' : (($val>0 ? '+' : '').$val));
  }
  

  //----------------------------------------------------------------------------------------
  function echoAllTownsTable()
  {
    echo '<table style="border-collapse:collapse;">';
    $sql="SELECT DISTINCT town.town_id, town.name AS town_name, player.player_id, player.name AS player_name
          FROM townresource,town,player 
          WHERE townresource.town_id=town.town_id AND
                town.player_id=player.player_id
          ORDER BY player_id, town_id";
    $r1 = mysql_query($sql) or die("r1 fail");
    while ($twn = mysql_fetch_array($r1)) 
    {
      $sql="SELECT townresource.time
            FROM townresource
            WHERE townresource.town_id=".$twn['town_id']."
            ORDER BY townresource.time";
      $r2 = mysql_query($sql);
      $tim = mysql_fetch_array($r2);
      //date_default_timezone_set('Europe/Moscow');
      echo '<tr style="border-bottom: 1px solid #000;">
            <td align="center">
              <a href="http://w23.wofh.ru/account?id='.$twn['player_id'].'" target="_blank">'.htmlentities($twn['player_name'], ENT_COMPAT | ENT_HTML401,'UTF-8').'</a>
              <br><a href="http://w23.wofh.ru/account?id='.$twn['town_id'].'" target="_blank">'.htmlentities($twn['town_name'], ENT_COMPAT | ENT_HTML401,'UTF-8').'</a>
              <br><i>'.($tim['time'] ? date('d.m H:i',(strtotime($tim['time'])+7*3600)) : '---').'</i>
            </td>';
      
      echo '<td>';
      $sql="SELECT resource.name, townresource.inc, townresource.stream, townresource.decr, 
                   resource.imgoffset
            FROM townresource,resource 
            WHERE townresource.town_id=".$twn['town_id']." AND
                  townresource.res_letter=resource.letter
            ORDER BY resource.letter";
      $r2 = mysql_query($sql);
      echo '<table class="res" cellspacing="0" cellpadding="0" border="0">';
      while ($inc = mysql_fetch_array($r2)) 
      {
        echo '<tr class="res">
              <td class="res"><span class="imgres" title="'.$inc['name'].'" style="background-position:'.$inc['imgoffset'].'px 0px"></span></td>
              <td class="res">'.$inc['inc'].'</td>
              <td class="res">'.plus_minus($inc['stream']).'</td>
              <td class="res">'.plus_minus(0-$inc['decr']).'</td>
              </tr>';
      }
      mysql_free_result($r2);
      echo '</table>';
      echo '</td>';

      echo '<td>';
      $sql="SELECT builds.name, townbuild.level, townbuild.slot, townbuild.build_id
            FROM townbuild, builds 
            WHERE townbuild.town_id=".$twn['town_id']." AND
                  townbuild.build_id=builds.build_id
            ORDER BY townbuild.slot";
      $r2 = mysql_query($sql);
      $build=array();
      while ($b = mysql_fetch_array($r2)) 
      {
        $build[$b['slot']] = array( 'name'=>$b['name'] , 
                                    'level'=>$b['level'], 
                                    'pic'=>'./p/'.$b['build_id'].'_1.png');
      }
      mysql_free_result($r2);
      for($n=0; $n<20; ++$n)
      {
        echo '<div class="build">';
        if( $build[$n] && $build[$n]['level'] )
        {
          echo '<div style="background: url('.$build[$n]['pic'].'); height:100%" title="'.$build[$n]['name'].'">
                <div class="buildlvl">'.$build[$n]['level'].'</div>
               </div>';
        }
        else
        {
          echo '<div style="background: url(/p/sm.png); height:100%" title="пустое место">
               </div>';
        }
        echo '</div>';
      }
      echo '</td>';



      echo '</tr>';
    } 
    mysql_free_result($r1);
    echo '</table>';
  }

?>