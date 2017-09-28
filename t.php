<?php
  require 't1.php'
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Работа</title>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
    <script  type="text/javascript">
      $(document).ready(function(){                            // по завершению загрузки страницы
        $('#bt1').click(function(){                    // вешаем на клик по элементу с id = bt1
          $.getJSON('t3.php', {}, function(json){  // загрузку JSON данных из файла tt.json   
            //$('#example-4').html('');
                                                         // заполняем DOM элемент данными из JSON объекта
            $('#ttt').append('Inf: '   + json.inf.data + '<br/>');
          });
        });
     });
    </script>
  </head>
        
  <body>
    <div align="center" style="font-family: Geneva, Arial, Helvetica, sans-serif;">
      <a href="t2.php" style="float:right; margin-left: 5em">Выход</a>
      <div><u>Здравствуйте, <?php echo $user_name?></u></div> 
    </div>
    <div id="bt1" style="display: inline-block; cursor:pointer; padding: 15px; margin: 5px; border-radius: 5px; color: #017572; background-color: #CFCFCF;">JSON</div>
    <div id="ttt">Information:<br/></div>

  </body>
</html>
