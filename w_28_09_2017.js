// ==UserScript==
// @name        Test1
// @namespace   Test1
// @description test1
// @include     https://ru32.waysofhistory.com/*
// @version     1
// @grant       none
// ==/UserScript==

(function() {
    'use strict';

  function PlayerInf()
  {
      try {
    var twn=$("#view-account .accWrapper");
    if(twn.length>0)
    {
      var inf={};
      inf.account="123";
      inf.towns=twn.length;
      inf.events=1111;
      inf.town={};
      twn.each(function(idx) {
               inf.town[idx]={};
               inf.town[idx].id=$(this).find(".accTownInfoName a").attr("href").match(/\/(\d+)/)[1];
               inf.town[idx].pop=$(this).find(".accTownInfoPop").text();
               inf.town[idx].dep="--";
               var t;
               t=$(this).find("accTownImgWrp map-layers");
               inf.town[idx].dep=t.length;
               if(t.length==1)
                 {
                   inf.town[idx].dep="-";
                 }
               else
                 {
                   inf.town[idx].dep=t.eq(1).attr("data-title");
                 }
               }
              )
      transWin.postMessage(JSON.stringify(inf), "*");
    }
    else
    {
      setTimeout(PlayerInf, 500);
    }
      } catch(e) {
        alert('Ошибка ' + e.name + ":" + e.message + "\n" + e.stack);
      }
  }

  function KeepTransWin()
  {
    try {
      transWin.postMessage("keep", "*");
    } catch(e) {
      alert('Ошибка KeepTransWin ' + e.name + ":" + e.message + "\n" + e.stack);
    }
    setTimeout(KeepTransWin, 900);
  }
  
  var transWin = window.open("http://test1.ru/trans.php", "_blank", "width=1100,height=600");

  PlayerInf();
  
  setTimeout(KeepTransWin, 2000);

})();
