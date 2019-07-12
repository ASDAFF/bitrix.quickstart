<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$aMenuLinks = Array(
	
	Array(
		"О магазине",
		"#SITE_DIR#about/",
		Array(),
		Array("STRONG"=>"0"),
		""
	),
	Array(
			"Как купить",
			"#SITE_DIR#about/howto/",
			Array(),
			Array(),
			""
	),
	
	Array(
			"Доставка",
			"#SITE_DIR#about/delivery/",
			Array(),
			Array(),
			""
	),
	Array(
		"Новости",
		"#SITE_DIR#news/",
		Array(),
		Array(),
		""
	),
	
	Array(
		"Обратная связь",
		"#feedBackModal",
		Array(),
		Array("DATA_TOGGLE"=>"modal", "CLASS" => "fback"), 
		""
	),
	Array(
			"Блог",
			"#SITE_DIR#blogs/",
			Array(),
			Array(),
			""
	),
    Array(
            "Карта сайта",
            "#SITE_DIR#map.php",
            Array(),
            Array(),
            ""
    ),
);
?>