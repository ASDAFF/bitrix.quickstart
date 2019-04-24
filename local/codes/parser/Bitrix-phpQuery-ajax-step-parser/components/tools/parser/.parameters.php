<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"URL" => Array(
			"PARENT" => "BASE",
			"NAME" => 'Адрес страницы с новостями',
			"TYPE" => "STRING",
			"DEFAULT" => '',
		),
		"NUM_NEWS" => Array(
			"PARENT" => "BASE",
			"NAME" => "Количество новостей для показа (0 - все что есть на странице)",
			"TYPE" => "STRING",
			"DEFAULT" => '3',
		),
		"CONTAINER" => Array(
			"PARENT" => "BASE",
			"NAME" => 'Основной искомый родитель (указать класс или айди контейнера элементов)',
			"TYPE" => "STRING",
			"DEFAULT" => '.news-list__item',
		),
		"ELEMENT" => Array(
			"PARENT" => "BASE",
			"NAME" => 'Основной искомый елемент (указать класс, айди элемента или его тег, так как мы ищем ссылку в которой, находится текст, название, картинка и дата)',
			"TYPE" => "STRING",
			"DEFAULT" => '.news-list__item',
		),
		"NAME" => Array(
			"PARENT" => "BASE",
			"NAME" => 'Название новости (указать класс или айди)',
			"TYPE" => "STRING",
			"DEFAULT" => '.news-item__header',
		),
		"DATE" => Array(
			"PARENT" => "BASE",
			"NAME" => 'Дата новости (указать класс или айди)',
			"TYPE" => "STRING",
			"DEFAULT" => '.news-item-date',
		),
		"TEXT" => Array(
			"PARENT" => "BASE",
			"NAME" => 'Текст анонса новости (указать класс или айди)',
			"TYPE" => "STRING",
			"DEFAULT" => '.news-item__text',
		),
		"CACHE_TIME"  =>  Array("DEFAULT"=>3600),
	),
);
?>
