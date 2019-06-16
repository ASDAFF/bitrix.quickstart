<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<div class="shop-list" >

<?
$index = 1; // Порядковый номер объекта на карте
foreach($arResult["ITEMS"] as $arItem) { ?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>


	<?
		//Разбиваем координаты яндекс карты на X и Y координату
		$Yandex = explode(",", $arItem["PROPERTIES"]["YANDEX_MAP"]["VALUE"]);
		$Yandex_X = $Yandex[0];
		$Yandex_Y = $Yandex[1];
	?>

	<!--Засовываем данные для формирования точки на карте в атрибуты контейнера div-->
	<div 

	class="shop-data"
	data-index="<?=$index?>"
	data-name="<?=$arItem["NAME"]?>"
	data-yandex-x="<?=$Yandex_X?>"
	data-yandex-y="<?=$Yandex_Y?>"
	data-address="<?=$arItem["PROPERTIES"]["ADDRESS"]["VALUE"];?>"
	data-hours="<?=$arItem["PROPERTIES"]["HOURS"]["VALUE"];?>"
	data-phone="<?=$arItem["PROPERTIES"]["PHONE"]["VALUE"];?>"
	data-shop-manager="<?=$arItem["PROPERTIES"]["SHOP_MANAGER"]["VALUE"];?>"

	 >
	 	<!--Выводим информацию для пользователя-->
		<b><?=$arItem["NAME"]?></b>
		<ul>
			<li><b>Адрес:</b> <?=$arItem["PROPERTIES"]["ADDRESS"]["VALUE"];?></li>
			<li><b>Часы работы:</b> <?=$arItem["PROPERTIES"]["HOURS"]["VALUE"];?></li>
			<li><b>Контактный телефон:</b> <?=$arItem["PROPERTIES"]["PHONE"]["VALUE"];?></li>
			<li><b>ФИО руководителя:</b> <?=$arItem["PROPERTIES"]["SHOP_MANAGER"]["VALUE"];?></li>
		</ul>
	</div>




<? ++$index; } unset($index); ?>

<!--Контейнер в который прилетит сформированная яндекс карта-->
<div id="map_container" ></div>

</div>
<script src="http://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>