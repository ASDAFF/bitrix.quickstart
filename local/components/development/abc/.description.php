<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => "Алфавитный каталог по свойству",
	"DESCRIPTION" => "Вывод алфавитный каталог по разделам содержащим элементы, по алфавитному порядку элементов или по указанному свойству элементов инфоблока",
	"ICON" => "/images/catalog.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
        "ID" => "development",
        "NAME" => "DEVELOPMENT",
		"CHILD" => array(
			"ID" => "lists",
			"NAME" => GetMessage("CD_BLL_LISTS"),
		)
	),
);

?>