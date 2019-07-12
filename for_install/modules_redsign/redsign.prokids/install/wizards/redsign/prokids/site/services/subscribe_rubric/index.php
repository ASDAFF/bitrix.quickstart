<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('subscribe'))
	return;

$arDataRubrics = array(
	array(
		'ACTIVE' => 'Y',
		'NAME' => GetMessage('RUBRIC_NAME_1'),
		'SORT' => 101,
		'DESCRIPTION' => GetMessage('RUBRIC_DISCRIPTION_1'),
		'LID' => WIZARD_SITE_ID
	),
	array(
		'ACTIVE' => 'Y',
		'NAME' => GetMessage('RUBRIC_NAME_2'),
		'SORT' => 201,
		'DESCRIPTION' => GetMessage('RUBRIC_DISCRIPTION_2'),
		'LID' => WIZARD_SITE_ID
	),
);

$rubric = new CRubric;
foreach($arDataRubrics as $arFields){
	$ID = $rubric->Add($arFields);
}