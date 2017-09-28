<?php 
  function ProcessPlayerInfo($player)
  {
    $sql="SELECT settings.value
          FROM settings 
          WHERE settings.name=" . mysql_real_escape_string($param_name) ." ";
    $result = mysql_query($sql);
    if(mysql_num_rows($result) == 0)
    {
      mysql_free_result($result);
      return null;
    }
    $r=mysql_fetch_row($result);
    mysql_free_result($result);
    return $r[0];
  }

?>