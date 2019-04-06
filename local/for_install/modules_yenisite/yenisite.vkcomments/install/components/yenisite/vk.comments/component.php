<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if($arParams["AUTO_PUBLISH"] == 'Y')
	$arResult["AUTO_PB"] = 1;
else
	$arResult["AUTO_PB"] = 0;

	
if($arParams["NO_REAL_TIME"] == 'Y')
	$arResult["NO_REAL_TIME"] = 0;
else
	$arResult["NO_REAL_TIME"] = 1;

	
$arAttachments = array();
if($arParams["ATTACH_GRAFFITI"] == 'Y')
	$arAttachments[] = 'graffiti';
	
if($arParams["ATTACH_PHOTO"] == 'Y')
	$arAttachments[] = 'photo';
		
if($arParams["ATTACH_AUDIO"] == 'Y')
	$arAttachments[] = 'audio';
		
if($arParams["ATTACH_VIDEO"] == 'Y')
	$arAttachments[] = 'video';
	
if(count($arAttachments != 0))
{
	$arResult["ATTACHMENTS"] = $arAttachments[0];	
	for($i = 1; $i < count($arAttachments); $i++)
	{
		$arResult["ATTACHMENTS"] = $arResult["ATTACHMENTS"] .','. $arAttachments[$i];
	}
}

	
$this->IncludeComponentTemplate();
?>