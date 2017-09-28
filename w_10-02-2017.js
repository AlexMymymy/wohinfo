// ==UserScript==
// @name         atkinfo
// @namespace    http://atk.site88.com/
// @version      0.1
// @description  try to get info from page
// @author       me
// @match        https://ru32.waysofhistory.com/*
// @grant        none
// ==/UserScript==

(function() {
    'use strict';


function showInf()
{

    function get_obj(v, lvl)
    {
      var x={};
      for(var elm in v)
      {
        //newWin.document.write('<br>'+elm+': '+v[elm]);
        if(typeof(v[elm])==='object')
        {
            if(lvl>0) x[elm]=get_obj(v[elm], lvl-1);
        }
        if(typeof(v[elm])==='number' || typeof(v[elm])==='boolean' || typeof(v[elm])==='string') x[elm]=v[elm];
      }
      return x;
    }

  if(false || (typeof(wofh) == 'object' && typeof(wofh.events) == 'object'))
  {
    try {
    
    var inf={};
    inf.account=get_obj(wofh.account, 1);
    inf.towns=get_obj(wofh.towns, 5);
    inf.events=get_obj(wofh.events.list, 3);
    //inf.wofh=get_obj(wofh, 2);
        
    newWin.postMessage(JSON.stringify(inf), "*");
    //setTimeout(showInf, 5000);
    } catch(e) {
     alert('Ошибка ' + e.name + ":" + e.message + "\n" + e.stack);
    }
  }
  else
  {
    setTimeout(showInf, 2000);
  }
}

var newWin = window.open("http://atk.site88.net/trans.php", "_blank", "width=1100,height=600");
showInf();

})();
