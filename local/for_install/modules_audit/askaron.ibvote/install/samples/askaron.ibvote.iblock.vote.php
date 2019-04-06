<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/*
This file displays the component "Correct voting" askaron:askaron.ibvote.iblock.vote

Solution URL: http://marketplace.1c-bitrix.ru/solutions/askaron.ibvote/
User documentation: http://askaron.ru/api_help/course1/lesson27/

Example 1. Include the file in the text.

Parameters are:
$arParams["IBLOCK_ID"] = 3;
$arParams["ELEMENT_ID"] = 15;

Use this example to include the file in the text

<!--askaron.include
askaron.ibvote.iblock.vote.php
<PARAMS>
    <IBLOCK_ID>3</IBLOCK_ID>
    <ELEMENT_ID>15</ELEMENT_ID>
</PARAMS>
-->


Example 2. bitrix:news.list template:

<?foreach($arResult["ITEMS"] as $arItem):?>
...
<!--askaron.include
askaron.ibvote.iblock.vote.php
<PARAMS>
    <IBLOCK_ID><?=$arItem["IBLOCK_ID"]?></IBLOCK_ID>
    <ELEMENT_ID><?=$arItem["ID"]?></ELEMENT_ID>
</PARAMS>
-->
...
<?endforeach;?>


Example 3. bitrix:news.detail template:

<!--askaron.include
askaron.ibvote.iblock.vote.php
<PARAMS>
    <IBLOCK_ID><?=$arResult["IBLOCK_ID"]?></IBLOCK_ID>
    <ELEMENT_ID><?=$arResult["ID"]?></ELEMENT_ID>
</PARAMS>
-->

*/
?><?$APPLICATION->IncludeComponent("askaron:askaron.ibvote.iblock.vote", "ajax", array(
	"IBLOCK_TYPE" => "news",
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
	"ELEMENT_ID" => $arParams["ELEMENT_ID"],
	"SESSION_CHECK" => "Y",
	"COOKIE_CHECK" => "N",
	"IP_CHECK_TIME" => "86400",
	"USER_ID_CHECK_TIME" => "0",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000000",
	"MAX_VOTE" => "5",
	"VOTE_NAMES" => array(
		0 => "1",
		1 => "2",
		2 => "3",
		3 => "4",
		4 => "5",
		5 => "",
	),
	"SET_STATUS_404" => "N",
	"DISPLAY_AS_RATING" => "rating"
	),
	false
);?>