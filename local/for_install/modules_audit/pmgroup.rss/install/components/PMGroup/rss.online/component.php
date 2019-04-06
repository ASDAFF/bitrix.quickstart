<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$sxml = simplexml_load_file($arParams["NEWS_BASE"]);
for ($key = 0, $size = $arParams["NEWS_COUNT"]; $key < $size; $key++) {
	$arResult[$key] = array(
		"NEWS_TITLE" => $sxml->channel->item[$key]->title,
		"NEWS_LINK" => $sxml->channel->item[$key]->link,
		"NEWS_DESCRIPTION" => $sxml->channel->item[$key]->description
		);
}	
	$this->IncludeComponentTemplate();
?>