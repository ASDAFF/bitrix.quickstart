<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

?><div class="storelist"><?
	
	if(strlen($arResult["ERROR_MESSAGE"])>0)
	{
		ShowError($arResult["ERROR_MESSAGE"]);
	}
	
	if(is_array($arResult["STORES"]) && !empty($arResult["STORES"]))
	{
		foreach($arResult["STORES"] as $pid => $arStore)
		{
			?><div class="item"><?
				?><div class="name"><?=$arStore["TITLE"]?></div><?
				if(isset($arStore["ADDRESS"]))
				{
					?><div class="phone"><?=$arStore["ADDRESS"]?></div><?
				}
				if(isset($arStore["PHONE"]))
				{
					?><div class="phone"><?=$arStore["PHONE"]?></div><?
				}
				if(isset($arProperty["SCHEDULE"]))
				{
					?><div class="schedule"><?=$arStore["SCHEDULE"]?></div><?
				}
			?></div><br /><?
		}
	}
	
?></div>