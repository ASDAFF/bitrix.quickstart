<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$aMenuLinks = Array(
	Array(
		"Новости", 
		"news/", 
		Array(), 
		Array(), 
		"" 
	),	
	Array(
		"Каталог",
		"catalog/",
		Array(),
		Array(),
		""
	),
	Array(
		"Корзина",
		"cart/",
		Array(),
		Array(),
		""
	),
	Array(
		"Мой кабинет",
		"personal/",
		Array(),
		Array(),
		"CUser::IsAuthorized()"
	),
);
?>