<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//Разрабочик решения Антон Почкин Email:Kopernik83@gmail.com Skype:Odisei83

$arServices = Array(
	"main" => Array(
		"NAME" => GetMessage("SERVICE_MAIN_SETTINGS"),
		"STAGES" => Array(
			"files.php", // Copy bitrix files
			"template.php", // Install template
			"theme.php",
			"macro.php"
			 // Install theme
			//"group.php", // Install group
			//"menu.php", // Install menu
		),
	),
	
	
    
	"iblock" => Array(
		"NAME" => GetMessage("SERVICE_IBLOCK_DEMO_DATA"),
		"STAGES" => Array(                        
			"types.php",
			 "news.php",
			 "products.php"
			 //IBlock types
                        //"category.php",
                        //"active_category.php",
                        //"news.php",
						//"products.php",
                        //"notes.php",
                        //"article.php",
                        //"answer.php",
                        //"course.php",
                        //"review.php",
                        //"instructors.php",
                        //"teachers.php",
                        //"vacancy.php",
                        //"gallery.php",
                        //"license.php",
                        //"macro.php"

                        
			//"feedback.php",
                        //"category.php",
                        //"article.php",
			//"news.php",
                        
		),
	),
	
);

?>