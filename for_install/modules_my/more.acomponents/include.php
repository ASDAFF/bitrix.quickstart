<?
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

$arClasses = array(
	"CPPFormat" => "classes/general/PPFormat.php",
	"CPPFormatUF" => "classes/general/PPFormatUF.php",
	"CPPFormatSF" => "classes/general/PPformatSF.php",
	"CPPFormatParamsC" => "classes/general/PPFormatParamsC.php",
	"CPPAcomponents"=> "classes/general/PPAcomponents.php",
);
CModule::AddAutoloadClasses("more.acomponents", $arClasses);
?>
