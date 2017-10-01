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
                 var t;
                 t=$(this).find(".accTownImgWrp .map-layers");
                 if(t.length==1)
                 {
                   inf.town[idx].deposit="";
                 }
                 else
                 {
                   inf.town[idx].deposit=t.eq(1).attr("data-title");
                 }
                 t=$(this).find(".map-climate").attr("class");
                 inf.town[idx].climate_id=0;
                 if(t.indexOf("-id-2")>=0) inf.town[idx].climate_id=1;
                 if(t.indexOf("-id-3")>=0) inf.town[idx].climate_id=2;
                 if(t.indexOf("-id-4")>=0) inf.town[idx].climate_id=3;
                 if(t.indexOf("-id-5")>=0) inf.town[idx].climate_id=4;
                 inf.town[idx].hill=0;
                 if($(this).find(".map-hill").length>0)
                 {
                   inf.town[idx].hill=1;
                 }
                 inf.town[idx].pos_ref=0;
                 inf.town[idx].pos_x=0;
                 inf.town[idx].pos_y=0;
               }
              )
      transWin.postMessage(JSON.stringify(inf), "*");
      //setTimeout(PlayerInf, 500);
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
