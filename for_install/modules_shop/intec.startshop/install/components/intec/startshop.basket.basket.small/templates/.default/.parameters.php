<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?if (!CModule::IncludeModule('intec.startshop')) return;?>
<?
	$arTemplateParameters = array(
		"USE_COUNT" => array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage('SBBS_DEFAULT_USE_COUNT'),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
			"REFRESH" => "Y"
		)
	);
	
	if ($arCurrentValues['USE_COUNT'] == "Y")
	{
		$arTemplateParameters["USE_COUNT_IF_EMPTY"] = array(
			"PARENT" => "VISUAL",
			"NAME" => GetMessage('SBBS_DEFAULT_USE_COUNT_IF_EMPTY'),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N"
		);
	}
	
	$arTemplateParameters["USE_SUM"] = array(
		"PARENT" => "VISUAL",
		"NAME" => GetMessage('SBBS_DEFAULT_USE_SUM'),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N"
	);
?>