<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Контакты");
?> 
<h1> Контакты</h1>
 
<p>Агентство недвижимости &laquo;Уютный дом&raquo; 
  <br />
 190000, Москва, ул. Пятницкая 3а 
  <br />
 +7 (495) 777-77-77<a href="?bitrix_include_areas=Y#" id="bxid_688186" > 
    <br />
   info@example.com</a></p>
<h2>Схема проезда:</h2> 
<p><?$APPLICATION->IncludeComponent(
    "bitrix:map.google.view",
    ".default",
    Array(
        "INIT_MAP_TYPE" => "ROADMAP",
        "MAP_DATA" => "a:4:{s:10:\"google_lat\";d:55.74434367205435;s:10:\"google_lon\";d:37.62982001296688;s:12:\"google_scale\";i:15;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:4:\"TEXT\";s:99:\"АН \"Уютный дом\"###RN###190000, Москва, ул. Пятницкий переулок 2\";s:3:\"LON\";d:37.62950887672116;s:3:\"LAT\";d:55.743335126436286;}}}",
        "MAP_WIDTH" => "400",
        "MAP_HEIGHT" => "250",
        "CONTROLS" => array(),
        "OPTIONS" => array(),
        "MAP_ID" => ""
    )
);?></p>
 
<div class="addr"> </div>
 <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>