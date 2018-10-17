<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/**
 * Дизайн веб-форм
 * возможность изменения дизайна веб-форм расположенных на сайте
 */

$arParams["WEB_FORM"] = explode("||",$arParams["WEB_FORM_PARAMS"]);

$y = 0;
foreach($arParams["WEB_FORM"] as $WEB_FORM)
{
	
	$WEB_FORM_ID = explode("#",$WEB_FORM);
	
	if($WEB_FORM_ID[0] != "" && $WEB_FORM_ID[1] != "" && $WEB_FORM_ID[2] != "")
	{
		$i = 0;
		$PARAM_MORE = "";
		$PARAM_MORE_LIST = explode("(***)",$WEB_FORM_ID[3]);
		
		foreach($PARAM_MORE_LIST as $WEB_FORM_PARAM_MORE)
		{
			
			$PARAM_MORE_ID = explode("(*)",$WEB_FORM_PARAM_MORE);
			
			if($PARAM_MORE_ID[0] != "" && $PARAM_MORE_ID[1] != "" && $PARAM_MORE_ID[2] != "")
			{
				if($i > 0)
				{
					$PARAM_MORE .= "||";
				}
				
				$PARAM_MORE .= implode("#",$PARAM_MORE_ID);
				
				++$i;
			}
		}
		
		if($PARAM_MORE != "")
		{	
			$arResult["ITEMS"][$y]["TAG"] = $WEB_FORM_ID[0];
			$arResult["ITEMS"][$y]["PARAM"] = $WEB_FORM_ID[1];
			$arResult["ITEMS"][$y]["VALUE"] = $WEB_FORM_ID[2];
			$arResult["ITEMS"][$y]["MORE"] = $PARAM_MORE;
			++$y;
		}
	}
}

$arResult["JQUERY"] = "/bitrix/components/elipseart/form.design_modify/script/jquery-1.6.1.min.js";
$arResult["FUNCTION"] = "/bitrix/components/elipseart/form.design_modify/script/function.js";
$arResult["SCRIPT"] = "/bitrix/components/elipseart/form.design_modify/script/script.js";

if ($this->InitComponentTemplate()) {
	$template = & $this->GetTemplate();
	$TemplateName = ($template->GetSiteTemplate()) ? $template->GetSiteTemplate()."_" : "";
	$arResult["TEMPLATE"]["NAME"] = preg_replace("|[^a-zA-Z0-9_]|", "", $TemplateName.$template->GetName());
	$this->ShowComponentTemplate();
}

?>