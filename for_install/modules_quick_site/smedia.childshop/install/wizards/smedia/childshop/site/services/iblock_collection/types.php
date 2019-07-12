<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock"))
	return;


$arTypes = Array(	
		Array(
			'ID' => 'catalog',
			'SECTIONS' => 'Y',
			'EDIT_FILE_BEFORE' => '',
			'EDIT_FILE_AFTER' => '',
			'IN_RSS' => 'N',
			'SORT' => '100',
			'LANG' => array(
					'ru' => array(
							'NAME' => GetMessage('catalog_LANG_ru_NAME'),
							'SECTION_NAME' => GetMessage('catalog_LANG_ru_SECTION_NAME'),
							'ELEMENT_NAME' => GetMessage('catalog_LANG_ru_ELEMENT_NAME'),
							),
					),
			),
	Array(
			'ID' => 'news',
			'SECTIONS' => 'N',
			'EDIT_FILE_BEFORE' => '',
			'EDIT_FILE_AFTER' => '',
			'IN_RSS' => 'Y',
			'SORT' => '200',
			'LANG' => array(
					'ru' => array(
							'NAME' => GetMessage('news_LANG_ru_NAME'),
							'SECTION_NAME' => '',
							'ELEMENT_NAME' => GetMessage('news_LANG_ru_ELEMENT_NAME'),
							),
					),
			),
	Array(
			'ID' => 'services',
			'SECTIONS' => 'Y',
			'EDIT_FILE_BEFORE' => '',
			'EDIT_FILE_AFTER' => '',
			'IN_RSS' => 'N',
			'SORT' => '500',
			'LANG' => array(
					'ru' => array(
							'NAME' => GetMessage('services_LANG_ru_NAME'),
							'SECTION_NAME' => GetMessage('services_LANG_ru_SECTION_NAME'),
							'ELEMENT_NAME' => GetMessage('services_LANG_ru_ELEMENT_NAME'),
							),
					),
			),
);

$languageID = 'ru';

$iblockType = new CIBlockType;
foreach($arTypes as $arType)
{	
	$dbType = CIBlockType::GetList(Array(),Array("=ID" => $arType["ID"]));
	if($dbType->Fetch())
		continue;		
	
	$iblockType->Add($arType);	
}

?>