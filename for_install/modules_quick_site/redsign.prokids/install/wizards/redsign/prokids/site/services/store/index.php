<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
	
if(!CModule::IncludeModule('catalog'))
	return;
	
global $USER;

$arStoreData = array(
	array(
		'TITLE' => GetMessage('STORE_DATA_TITLE_1'),
		'ACTIVE' => 'Y',
		'ADDRESS' => GetMessage('STORE_DATA_ADDRESS_1'),
		'DESCRIPTION' => GetMessage('STORE_DATA_DESCRIPTION_1'),
		'IMAGE_ID' => '',
		'USER_ID' => $USER->GetID(),
		'GPS_N' => GetMessage('STORE_DATA_GPS_N_1'),
		'GPS_S' => GetMessage('STORE_DATA_GPS_S_1'),
		'PHONE' => GetMessage('STORE_DATA_PHONE_1'),
		'SCHEDULE' => GetMessage('STORE_DATA_SCHEDULE_1'),
		'XML_ID' => GetMessage('STORE_DATA_XML_ID_1'),
	),
	array(
		'TITLE' => GetMessage('STORE_DATA_TITLE_2'),
		'ACTIVE' => 'Y',
		'ADDRESS' => GetMessage('STORE_DATA_ADDRESS_2'),
		'DESCRIPTION' => GetMessage('STORE_DATA_DESCRIPTION_2'),
		'IMAGE_ID' => '',
		'USER_ID' => $USER->GetID(),
		'GPS_N' => GetMessage('STORE_DATA_GPS_N_2'),
		'GPS_S' => GetMessage('STORE_DATA_GPS_S_2'),
		'PHONE' => GetMessage('STORE_DATA_PHONE_2'),
		'SCHEDULE' => GetMessage('STORE_DATA_SCHEDULE_2'),
		'XML_ID' => GetMessage('STORE_DATA_XML_ID_2'),
	),
	array(
		'TITLE' => GetMessage('STORE_DATA_TITLE_3'),
		'ACTIVE' => 'Y',
		'ADDRESS' => GetMessage('STORE_DATA_ADDRESS_3'),
		'DESCRIPTION' => GetMessage('STORE_DATA_DESCRIPTION_3'),
		'IMAGE_ID' => '',
		'USER_ID' => $USER->GetID(),
		'GPS_N' => GetMessage('STORE_DATA_GPS_N_3'),
		'GPS_S' => GetMessage('STORE_DATA_GPS_S_3'),
		'PHONE' => GetMessage('STORE_DATA_PHONE_3'),
		'SCHEDULE' => GetMessage('STORE_DATA_SCHEDULE_3'),
		'XML_ID' => GetMessage('STORE_DATA_XML_ID_3'),
	),
);

foreach($arStoreData as $arFields){
	$ID = CCatalogStore::Add($arFields);
}