<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * Copyright (c) 19/9/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

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

if (!empty($arResult['ITEMS']))
{
	$templateData = array(	//Тема шаблона компонента (находятся в папке themes/ папки шаблона компонента, фича 14.x)
		'TEMPLATE_THEME' => $this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css',
		'TEMPLATE_CLASS' => 'bx_'.$arParams['TEMPLATE_THEME']
	);
	
	CJSCore::Init(array("popup"));
	$arSkuTemplate = array();

?>
	<? include 'offers_props_selected_create.php'; //Формирование контейнеров свойств торгового предложения ?>
	
	
	<?

	//Постраничная навигация
	if ($arParams["DISPLAY_TOP_PAGER"])
		{	
			echo $arResult["NAV_STRING"]; 	
		} 
?>

<?
	$strElementEdit = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_EDIT");
	$strElementDelete = CIBlock::GetArrayByID($arParams["IBLOCK_ID"], "ELEMENT_DELETE");
	$arElementDeleteParams = array("CONFIRM" => GetMessage('CT_BCS_TPL_ELEMENT_DELETE_CONFIRM'));
?>

<div class="bx_catalog_list_home col<? echo $arParams['LINE_ELEMENT_COUNT']; ?> <? echo $templateData['TEMPLATE_CLASS']; ?>">

<? foreach ($arResult['ITEMS'] as $key => $arItem) //Основной foreach, вывод элементов раздела
{
	//Функции для работы интерфейса "ЭРМИТАЖ"
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
	$strMainID = $this->GetEditAreaId($arItem['ID']);
	?>
	
	<? include 'ids_array.php'; //Массив идентификаторов элемента, необходим для работы JavaScript объектов  ?>
	
<div class="<? echo ($arItem['SECOND_PICT'] ? 'bx_catalog_item double' : 'bx_catalog_item'); ?>" >
	<div class="bx_catalog_item_container" id="<? echo $strMainID; ?>">	<!-- Основной контейнер элемента -->
	
	
	<? include 'first_picture.php'; // Основная картинка товара ?>
	
	<? include 'second_picture.php'; //Дополнительная картинка (отображается при наведении мышки на элемент) ?>
	
	<? include 'product_title.php'; //Заголовок товара ?>	
		
	<? include 'product_price.php'; //Цена товара ?>
	

	

	
	<?
	//Ели НЕТ торгового предложения
	if (!isset($arItem['OFFERS']) || empty($arItem['OFFERS']))
	{
		echo "<br/>НЕТ торгового предложения!<br/>";
		
		?>

		<? include 'catalog_item_controls.php'; //Контроллеры (кнопка Купить/Подписаться, количество +/-)?>
		
		<? include 'display_properties.php'; //Свойства из DISPLAY_PROPERTIES ?>
	
		<? include 'properties_add_to_basket.php'; //Свойства добавляемые в корзину ?>

		<? include 'create_js_object.php'; // Создание Java Script объекта класса JCCatalogSection ?>


<?	}
	else
	{
		echo "<br/>ЕСТЬ торговое предложение!<br/>";
	?>
		
		<? include 'offers_catalog_item_controls.php'; //Контроллеры (кнопка Купить/Подписаться, количество +/-) ?>
		
		<? include 'offers_display_properties.php'; //Отображение свойств из DISPLAY_PROPERTIES ?>
		
		<? include 'offers_props_and_js_object.php'; ?>

<?	
	}	//Конец if ЕСТЬ торговое предложение
?>
</div>	<!-- Конец основного контейнера элемента -->

</div>
<?	} //Конец основного foreach	?>


<div style="clear: both;"></div>
</div>

<script type="text/javascript">
BX.message({	//Засовываем языковые константы в JS
	MESS_BTN_BUY: '<? echo ('' != $arParams['MESS_BTN_BUY'] ? CUtil::JSEscape($arParams['MESS_BTN_BUY']) : GetMessageJS('CT_BCS_TPL_MESS_BTN_BUY')); ?>',
	MESS_BTN_ADD_TO_BASKET: '<? echo ('' != $arParams['MESS_BTN_ADD_TO_BASKET'] ? CUtil::JSEscape($arParams['MESS_BTN_ADD_TO_BASKET']) : GetMessageJS('CT_BCS_TPL_MESS_BTN_ADD_TO_BASKET')); ?>',
	MESS_NOT_AVAILABLE: '<? echo ('' != $arParams['MESS_NOT_AVAILABLE'] ? CUtil::JSEscape($arParams['MESS_NOT_AVAILABLE']) : GetMessageJS('CT_BCS_TPL_MESS_PRODUCT_NOT_AVAILABLE')); ?>',
	BTN_MESSAGE_BASKET_REDIRECT: '<? echo GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_BASKET_REDIRECT'); ?>',
	BASKET_URL: '<? echo $arParams["BASKET_URL"]; ?>',
	ADD_TO_BASKET_OK: '<? echo GetMessageJS('ADD_TO_BASKET_OK'); ?>',
	TITLE_ERROR: '<? echo GetMessageJS('CT_BCS_CATALOG_TITLE_ERROR') ?>',
	TITLE_BASKET_PROPS: '<? echo GetMessageJS('CT_BCS_CATALOG_TITLE_BASKET_PROPS') ?>',
	TITLE_SUCCESSFUL: '<? echo GetMessageJS('ADD_TO_BASKET_OK'); ?>',
	BASKET_UNKNOWN_ERROR: '<? echo GetMessageJS('CT_BCS_CATALOG_BASKET_UNKNOWN_ERROR') ?>',
	BTN_MESSAGE_SEND_PROPS: '<? echo GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_SEND_PROPS'); ?>',
	BTN_MESSAGE_CLOSE: '<? echo GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_CLOSE') ?>'
});
</script>

<?	//Постраничная навигация
	if ($arParams["DISPLAY_BOTTOM_PAGER"])
	{
		?><? echo $arResult["NAV_STRING"]; ?><?
	}
} //Есть ли вообще элементы в разделе
?>