<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
{
	ShowError( GetMessage("ERROR_NOT_MODULE_IBLOCK") );
	return;
}
if(!CModule::IncludeModule("redsign.devcom"))
{
	ShowError( GetMessage("ERROR_EMPTY_MODULE_DEVCOM") );
	return;
}
if(IntVal($arParams["IBLOCK_ID"])<1)
{
	ShowError( GetMessage("ERROR_EMPTY_IBLOCK_ID") );
	return;
}
	

if( $this->StartResultCache() )
{
	if($arParams["UF_VALUE_NOT"]=="Y")
	{
		$CODE = "!".$arParams["UF_CODE"];
	} else {
		$CODE = $arParams["UF_CODE"];
	}
	
	$arOrder = array(
		"SORT" => "ASC",
		"RAND" => "ASC",
	);
	$arFilter = array(
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"ACTIVE" => "Y",
		"GLOBAL_ACTIVE" => "Y",
		$CODE => $arParams["UF_VALUE"],
	);
	$arSelect = array("*","UF_*");
	$res = CIBlockSection::GetList($arOrder,$arFilter,true,$arSelect);
	$res->NavStart( $arParams["COUNT"] );
	while($arSection = $res->GetNext())
	{
		$arSection["PICTURE"] = CFile::GetFileArray($arSection["PICTURE"]);
		if(IntVal($arParams["MAX_WIDTH"])>0 && IntVal($arParams["MAX_HEIGHT"])>0)
			$arSection["PICTURE"]["TRUE_SIZE"] = REDSIGNDevComGetProfiSize($arSection["PICTURE"]["WIDTH"], $arSection["PICTURE"]["HEIGHT"], $arParams["MAX_WIDTH"], $arParams["MAX_HEIGHT"]);
			
		$arSection["DETAIL_PICTURE"] = CFile::GetFileArray($arSection["DETAIL_PICTURE"]);
		if(IntVal($arParams["MAX_WIDTH"])>0 && IntVal($arParams["MAX_HEIGHT"])>0)
			$arSection["DETAIL_PICTURE"]["TRUE_SIZE"] = REDSIGNDevComGetProfiSize($arSection["DETAIL_PICTURE"]["WIDTH"], $arSection["DETAIL_PICTURE"]["HEIGHT"], $arParams["MAX_WIDTH"], $arParams["MAX_HEIGHT"]);
		
		$arResult["SECTIONS"][] = $arSection;
	}
	
	///////////////////// Include template /////////////////////
	$this->IncludeComponentTemplate();
}