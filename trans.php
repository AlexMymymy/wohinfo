<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
<head>
<title>передача данных</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<script type="text/javascript">

function notifyMe(text, data)
{
  document.getElementById("state").textContent=text;
  document.getElementById("data").textContent=data;
}

/*{
  if(!("Notification" in window))
  {
    alert(text);
  }
  else if(Notification.permission==="granted")
  {
    var notification=new Notification(text);
  }
  else if(Notification.permission!=="denied")
  {
    Notification.requestPermission(function (permission)
                                    {
                                      if(!('permission' in Notification))
                                      {
                                        Notification.permission=permission;
                                      }
                                      if(permission==="granted")
                                      {
                                        var notification=new Notification(text);
                                      }
                                      else
                                      {
                                        alert(text);
                                      }
                                    }
                                  );
  }
}*/

function sendInfo()
{
  var inf;
  inf=infQueue.pop();
  document.getElementById("state").textContent="Отправка данных";
  document.getElementById("data").textContent=inf;
  var xhr = new XMLHttpRequest();
  xhr.open('POST', window.location.origin+'/addinfo.php', true);
  xhr.onreadystatechange = function(){
       if (xhr.readyState != 4) return;
       if (xhr.status == 200) { notifyMe('Информация отправлена.'+xhr.statusText, inf); }
       else                   { notifyMe('Ошибка передачи, (' + xhr.status + ': ' + xhr.statusText+')', inf); }
       if(infQueue.length == 0) { isSending=false; return; }
       else { sendInfo(); return; }
    };
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.send('info='+encodeURIComponent(inf));
}

function listener(event)
{
  noInfCounter = 0;
  if(event.data=="keep") return; // посылка для поддержания открытости окна
  infQueue.push(event.data);
  if(!isSending)
  {
    isSending = true;
    sendInfo();
  }
}

function CheckClose()
{
  ++noInfCounter;
  if(noInfCounter>5)
  {
    window.close();
  }
  else
  {
    if(noInfCounter>2)
    {
      document.getElementById("state").textContent="Ожидание поступления данных ... " + noInfCounter;
      document.getElementById("data").textContent="";
    }
    setTimeout(CheckClose, 1000);
  }
}

var infQueue = new Array();
var isSending = false;
var noInfCounter = 0;
window.addEventListener("message", listener);
CheckClose();

</script>
</head>
<body>
<div id="state">Ожидание поступления данных ...</div>
<div id="data"></div>
</body>
</html>
