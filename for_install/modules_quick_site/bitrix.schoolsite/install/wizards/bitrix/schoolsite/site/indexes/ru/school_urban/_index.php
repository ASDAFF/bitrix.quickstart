<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("МОУ Средняя образовательная школа №8732, Москва");
?>
<img src="<?=SITE_DIR?>files/banner_main.jpg"  />
<br />
<div class="about blockAlt">
 <h2><a href="about/">О нашей школе</a></h2>
 <img src="<?=SITE_DIR?>images/director_woomen.jpg" alt=""/>

 <div class="txt">
     <p>Школа №8732 - это школа с полным днем пребывания детей. Обучение
     ведется по 55 учебно-методическим комплексам. Учащиеся 1 - 4 классов обучаются в
     отдельном корпусе, где в каждом кабинете предусмотрены учебная зона и место для
     отдыха.</p>

     <p>В каждой школе есть своя "изюминка". В нашей школе это, конечно же, содружество
         талантливых, одаренных, и очень любящих свое дело педагогов. </p>
     <h4>Наша школа это:</h4>
     <ul class="list">
         <li>1238 учеников</li>
         <li>21 преподаватель</li>
         <li>4 этажа в здании</li>
         <li>2 новых тренажерных зала</li>
         <li>102 новых парты</li>
         <li>4 компьютерных класса</li>
     </ul>
 </div>
</div>
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "index_video.php",
		"EDIT_TEMPLATE" => ""
	),
false
);?>
<br />
 <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
	"AREA_FILE_SHOW" => "file",
	"PATH" => "index_photo.php",
	"EDIT_TEMPLATE" => ""
	),
	false,
	array(
	"ACTIVE_COMPONENT" => "Y"
	)
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>