<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
CUtil::InitJSCore(array('popup'));

if($arParams["USE_FILTER"]=="Y")
{
	if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
		$arParams["FILTER_NAME"] = "arrFilter";
}
else
	$arParams["FILTER_NAME"] = "";

$arDefaultUrlTemplates404 = array(
	"sections" => "",
	"section" => "#SECTION_ID#/",
	"element" => "#SECTION_ID#/#ELEMENT_ID#/",
);

$arDefaultVariableAliases404 = array();

$arDefaultVariableAliases = array();

$arComponentVariables = array(
	"SECTION_ID",
	"SECTION_CODE",
	"ELEMENT_ID",
	"ELEMENT_CODE",
	"action",
);

if($arParams["SEF_MODE"] == "Y")
{
	$arVariables = array();

	$engine = new CComponentEngine($this);
	if (\Bitrix\Main\Loader::includeModule('iblock'))
	{
		$engine->addGreedyPart("#SECTION_CODE_PATH#");
		$engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));
	}
	$arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates($arDefaultUrlTemplates404, $arParams["SEF_URL_TEMPLATES"]);
	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases404, $arParams["VARIABLE_ALIASES"]);

	$componentPage = $engine->guessComponentPath(
		$arParams["SEF_FOLDER"],
		$arUrlTemplates,
		$arVariables
	);

	$bSection = false;
	$arFilter = array("IBLOCK_ID"=>$arParams["AUCTION_IBLOCK_ID"], "ACTIVE"=>"Y");
	$dbRes = CIBlockSection::GetList(array($by=>$order), $arFilter, false, array("ID"));
	if ($arRes = $dbRes->Fetch())
		$bSection = true;
	
	if (strlen($componentPage) <= 0)
	{
		if ($bSection)
			$componentPage = "sections";
		else
			$componentPage = "section";
	}
	
	if (!$bSection && is_set($arVariables["SECTION_ID"]) && $arVariables["SECTION_ID"] > 0)
	{
		$arVariables = array(
			"ELEMENT_ID" => $arVariables["SECTION_ID"]
		);
		$componentPage = "element";
	}
	
	if (!$bSection && is_set($arVariables["SECTION_CODE"]) && strlen($arVariables["SECTION_CODE"]) > 0)
	{
		$arVariables = array(
			"ELEMENT_CODE" => $arVariables["SECTION_CODE"]
		);
		$componentPage = "element";
	}

	$b404 = false;
	if(!$componentPage)
	{
		$componentPage = "sections";
		$b404 = true;
	}

	if($componentPage == "section")
	{
		if (isset($arVariables["SECTION_ID"]))
			$b404 |= (intval($arVariables["SECTION_ID"])."" !== $arVariables["SECTION_ID"]);
		else
			$b404 |= !isset($arVariables["SECTION_CODE"]);
	}

	if($b404 && $arParams["SET_STATUS_404"]==="Y")
	{
		$folder404 = str_replace("\\", "/", $arParams["SEF_FOLDER"]);
		if ($folder404 != "/")
			$folder404 = "/".trim($folder404, "/ \t\n\r\0\x0B")."/";
		if (substr($folder404, -1) == "/")
			$folder404 .= "index.php";

			if($folder404 != $APPLICATION->GetCurPage(true))
			CHTTP::SetStatus("404 Not Found");
	}

	CComponentEngine::InitComponentVariables($componentPage, $arComponentVariables, $arVariableAliases, $arVariables);
	
	if ($componentPage == "section" && is_array($arParams["SEF_URL_TEMPLATES"]))
	{	
		if (is_set($arVariables["SECTION_ID"]) && strlen($arVariables["SECTION_ID"]) > 0)
			$arParams["SECTION_ID"] = $arVariables["SECTION_ID"];
		
		if (is_set($arVariables["SECTION_CODE"]) && strlen($arVariables["SECTION_CODE"]) > 0)
			$arParams["SECTION_CODE"] = $arVariables["SECTION_CODE"];
	}
	
	if ($componentPage == "element" && is_array($arParams["SEF_URL_TEMPLATES"]))
	{
		if ($arVariables["ELEMENT_ID"] <= 0 && strlen($arVariables["ELEMENT_CODE"]) > 0)
		{
			$res = CIBlockElement::GetList(array(), array("CODE"=>$arVariables["ELEMENT_CODE"]), false, false, array("ID"));
			if ($arRes = $res->Fetch())
				$arVariables["ELEMENT_ID"] = $arRes["ID"];
		}
		
		$arParams["ELEMENT_ID"] = $arVariables["ELEMENT_ID"];
		$arParams["ELEMENT_CODE"] = $arVariables["ELEMENT_CODE"];
		$arParams["IBLOCK_URL"] = $APPLICATION->GetCurDir();
	}
	
	$arResult = array(
		"FOLDER" => $arParams["SEF_FOLDER"],
		"URL_TEMPLATES" => $arUrlTemplates,
		"VARIABLES" => $arVariables,
		"ALIASES" => $arVariableAliases
	);
}
else
{
	$arVariables = array();

	$arVariableAliases = CComponentEngine::MakeComponentVariableAliases($arDefaultVariableAliases, $arParams["VARIABLE_ALIASES"]);
	CComponentEngine::InitComponentVariables(false, $arComponentVariables, $arVariableAliases, $arVariables);

	$componentPage = "";

	if(isset($arVariables["ELEMENT_ID"]) && intval($arVariables["ELEMENT_ID"]) > 0)
		$componentPage = "element";
	elseif(isset($arVariables["ELEMENT_CODE"]) && strlen($arVariables["ELEMENT_CODE"]) > 0)
		$componentPage = "element";
	elseif(isset($arVariables["SECTION_ID"]) && intval($arVariables["SECTION_ID"]) > 0)
		$componentPage = "section";
	elseif(isset($arVariables["SECTION_CODE"]) && strlen($arVariables["SECTION_CODE"]) > 0)
		$componentPage = "section";
	else
		$componentPage = "sections";

	if ($componentPage == "section")
	{
		//$arParams["DETAIL_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPage())."?".$arVariableAliases["ELEMENT_ID"]."=#ELEMENT_ID#";
		
		$arParams["SECTION_ID"] = $arVariables["SECTION_ID"];
		$arParams["SECTION_CODE"] = $arVariables["SECTION_CODE"];
	}
	
	if ($componentPage == "element")
	{
		//$arParams["IBLOCK_URL"] = $APPLICATION->GetCurPage();
		
		$arParams["ELEMENT_ID"] = $arVariables["ELEMENT_ID"];
		$arParams["ELEMENT_CODE"] = $arVariables["ELEMENT_CODE"];
	}
	
	$arResult = array(
		"FOLDER" => "",
		"URL_TEMPLATES" => Array(
			"section" => htmlspecialcharsbx($APPLICATION->GetCurPage())."?".$arVariableAliases["SECTION_ID"]."=#SECTION_ID#",
			"element" => htmlspecialcharsbx($APPLICATION->GetCurPage())."?".$arVariableAliases["SECTION_ID"]."=#SECTION_ID#"."&".$arVariableAliases["ELEMENT_ID"]."=#ELEMENT_ID#",
		),
		"VARIABLES" => $arVariables,
		"ALIASES" => $arVariableAliases
	);
}

$this->IncludeComponentTemplate($componentPage);
?>
