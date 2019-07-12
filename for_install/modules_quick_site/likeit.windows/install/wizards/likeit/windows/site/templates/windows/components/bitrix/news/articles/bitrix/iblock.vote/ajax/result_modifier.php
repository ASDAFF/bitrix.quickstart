<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$component = $this->__component;
//01*
//         ,
//    .
//02*
//  ( )      URL 
//     (  )
$arSessionParams = array(
	"PAGE_PARAMS" => array("ELEMENT_ID"),
);
//03*
//       
foreach($arParams as $k=>$v)
	if(strncmp("~", $k, 1) && !in_array($k, $arSessionParams["PAGE_PARAMS"]))
		$arSessionParams[$k] = $v;
//04*
// ""        AJAX 
$arSessionParams["COMPONENT_NAME"] = $component->GetName();
$arSessionParams["TEMPLATE_NAME"] = $component->GetTemplateName();
if($parent = $component->GetParent())
{
	$arSessionParams["PARENT_NAME"] = $parent->GetName();
	$arSessionParams["PARENT_TEMPLATE_NAME"] = $parent->GetTemplateName();
	$arSessionParams["PARENT_TEMPLATE_PAGE"] = $parent->GetTemplatePage();
}
//05*
//   !
$idSessionParams = md5(serialize($arSessionParams));

//06*
// arResult .
//      
//   
$component->arResult["AJAX"] = array(
	"SESSION_KEY" => $idSessionParams,
	"SESSION_PARAMS" => $arSessionParams,
);

//07*
//     
$arResult["~AJAX_PARAMS"] = array(
	"SESSION_PARAMS" => $idSessionParams,
	"PAGE_PARAMS" => array(
		"ELEMENT_ID" => $arParams["ELEMENT_ID"],
	),
	"sessid" => bitrix_sessid(),
	"AJAX_CALL" => "Y",
);
//08*
//      
$arResult["AJAX_PARAMS"] = CUtil::PhpToJSObject($arResult["~AJAX_PARAMS"]);
//09*
//    template.php
?>
