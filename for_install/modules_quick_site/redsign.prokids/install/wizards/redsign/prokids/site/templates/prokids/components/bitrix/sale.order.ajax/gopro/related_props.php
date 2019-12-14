<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props_format.php");

$style = (is_array($arResult['ORDER_PROP']['RELATED']) && count($arResult['ORDER_PROP']['RELATED'])) ? '' : 'display:none';

?><div class="section props" style="<?=$style?>"><?
	?><h4><?=GetMessage('SOA_TEMPL_RELATED_PROPS')?></h4><?
	?><div class="body"><?
		?><?=PrintPropsForm($arResult['ORDER_PROP']['RELATED'], $arParams['TEMPLATE_LOCATION'])?><?
	?></div><?
?></div>