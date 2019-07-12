<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule('iblock'))
	return;

/* _________________________________________________________________________________ */
$arFields = Array(
	'ID' => 'catalog',
	'SECTIONS' => 'Y',
	'IN_RSS' => 'N',
	'SORT' => 101,
	'LANG' => Array(
		'en' => Array(
			'NAME' => GetMessage('EN_TYPE_NAME_catalog'),
			'ELEMENT_NAME' => GetMessage('EN_ELEMENT_NAME_catalog'),
			'SECTION_NAME' => GetMessage('EN_SECTION_NAME_catalog')
		),
		'ru' => Array(
			'NAME' => GetMessage('RU_TYPE_NAME_catalog'),
			'ELEMENT_NAME' => GetMessage('RU_ELEMENT_NAME_catalog'),
			'SECTION_NAME' => GetMessage('RU_SECTION_NAME_catalog')
		)
	)
);

$obBlocktype = new CIBlockType;
$DB->StartTransaction();
$res = $obBlocktype->Add($arFields);
if(!$res){
	$DB->Rollback(); // error part
}
else{
	$DB->Commit();
}

/* _________________________________________________________________________________ */
$arFields = Array(
	'ID' => 'presscenter',
	'SECTIONS' => 'Y',
	'IN_RSS' => 'N',
	'SORT' => 201,
	'LANG' => Array(
		'en' => Array(
			'NAME' => GetMessage('EN_TYPE_NAME_presscenter'),
			'ELEMENT_NAME' => GetMessage('EN_ELEMENT_NAME_presscenter'),
			'SECTION_NAME' => GetMessage('EN_SECTION_NAME_presscenter')
		),
		'ru' => Array(
			'NAME' => GetMessage('RU_TYPE_NAME_presscenter'),
			'ELEMENT_NAME' => GetMessage('RU_ELEMENT_NAME_presscenter'),
			'SECTION_NAME' => GetMessage('RU_SECTION_NAME_presscenter')
		)
	)
);

$obBlocktype = new CIBlockType;
$DB->StartTransaction();
$res = $obBlocktype->Add($arFields);
if(!$res){
	$DB->Rollback(); // error part
}
else{
	$DB->Commit();
}