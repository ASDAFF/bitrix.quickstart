<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!CModule::IncludeModule('redsign.devfunc'))
	return;

// get no photo
$arResult['NO_PHOTO'] = RSDevFunc::GetNoPhoto(array('MAX_WIDTH'=>95,'MAX_HEIGHT'=>55));
// /get no photo

if(!CModule::IncludeModule('redsign.location'))
	return;
if(!CModule::IncludeModule('sale'))
	return;

$COM_SESS_PREFIX = "RSLOCATION";
$detectedLocID = 0;
$detectedLocID = IntVal($_SESSION[$COM_SESS_PREFIX]['LOCATION']['ID']);
$arResult['RSDETECTED_LOCATION_VALUE'] = '-';
if( $detectedLocID>0 )
{
	$arResult['RSDETECTED_LOCATION_VALUE'] = $detectedLocID;
} else {
	$detected = array();
	$detected = CRS_Location::GetCityName();
	
	if( isset($detected['CITY_NAME']) )
	{
		$dbRes = CSaleLocation::GetList(
			array('SORT'=>'ASC','CITY_NAME_LANG'=>'ASC'),
			array('LID'=>LANGUAGE_ID,'CITY_NAME'=>$detected['CITY_NAME'])
		);
		if($arFields = $dbRes->Fetch())
		{
			$arResult['RSDETECTED_LOCATION_VALUE'] = $arFields['ID'];
		}
	}
}