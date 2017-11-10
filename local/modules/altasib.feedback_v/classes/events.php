<?
/**
 * Company developer: ALTASIB
 * Site: http://www.altasib.ru
 * E-mail: dev@altasib.ru
 * Copyright (c) 2006-2017 ALTASIB
 */

class AltasibFeedbackEvent
{
	function OnAfterIBlockUpdateHandler(&$arFields)
	{
		if($arFields["RESULT"])
		{
			if($arFields["CODE"] == "altasib_feedback" || $arFields["IBLOCK_TYPE_ID"] == "altasib_feedback")
			{
				BXClearCache(true, "/altasib/feedback");
			}
		}
	}
}
?>