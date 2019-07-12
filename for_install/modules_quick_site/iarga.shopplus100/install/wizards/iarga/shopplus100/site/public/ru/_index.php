<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Магазин +100");
?> <span class="experience"> 	 
  <p>Мы хотели создать максимально удобный магазин и вложили в этот сайт, всё, что знаем об этом. <br>А ещё магазин отлично работает на телефонах. Просто сожмите ваш браузер и посмотрите, как он перестроится.</p>
 </span> 
<!--.experience-end-->
 <?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section.list",
	"main",
	Array(
		"IBLOCK_TYPE" => "iarga_shopplus100",
		"IBLOCK_ID" => "#CATALOG_IBLOCK_ID#",
		"SECTION_ID" => "",
		"SECTION_CODE" => "",
		"COUNT_ELEMENTS" => "N",
		"TOP_DEPTH" => "2",
		"SECTION_FIELDS" => array(0=>"",1=>"",),
		"SECTION_USER_FIELDS" => array(0=>"",1=>"",),
		"SECTION_URL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "Y",
		"ADD_SECTIONS_CHAIN" => "Y"
	)
);?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>