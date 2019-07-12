<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Карта города");
?> 

<!-- Этот блок кода нужно вставить в ту часть страницы, где вы хотите разместить карту (начало) -->
 
<div style="width: 100%; height: 650px;" id="ymaps-map-id_136119499180458091358"></div>
 
<div style="width: 100%; text-align: right;"><a href="http://api.yandex.ru/maps/tools/constructor/index.xml" target="_blank" style="color: #1A3DC1; font: 13px Arial, Helvetica, sans-serif;" >Создано с помощью инструментов Яндекс.Карт</a></div>
 
<script type="text/javascript">function fid_136119499180458091358(ymaps) {var map = new ymaps.Map("ymaps-map-id_136119499180458091358", {center: [37.617671, 55.75576799999372], zoom: 10, type: "yandex#map"});map.controls.add("zoomControl").add("mapTools").add(new ymaps.control.TypeSelector(["yandex#map", "yandex#satellite", "yandex#hybrid", "yandex#publicMap"]));};</script>
 
<script type="text/javascript" src="http://api-maps.yandex.ru/2.0-stable/?lang=ru-RU&coordorder=longlat&load=package.full&wizard=constructor&onload=fid_136119499180458091358"></script>
 
<!-- Этот блок кода нужно вставить в ту часть страницы, где вы хотите разместить карту (конец) -->


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>