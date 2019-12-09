<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arParams['form_index'] = $this->randString(4);
$arParams['FORM_ID'] = 'REPLIER'.$arParams['form_index'];
$arParams['MESSAGE_SEPARATOR'] = ':SEPARATOR:';

// review text
if(isset($arResult['REVIEW_TEXT']))
{
	$arrPostMessage = explode($arParams['MESSAGE_SEPARATOR'],$arResult["REVIEW_TEXT"]);
	$arResult["REVIEW_TEXT_EXT"] = array(
		"RATING" => $arrPostMessage[0],
		"PLUS" => $arrPostMessage[1],
		"MINUS" => $arrPostMessage[2],
		"COMMENT" => $arrPostMessage[3],
	);
}

if(!empty($arResult['MESSAGES']))
{
	foreach($arResult["MESSAGES"] as $key1 => $res)
	{
		// format messages
		$arrPostMessage = explode($arParams['MESSAGE_SEPARATOR'],$res["POST_MESSAGE_TEXT"]);
		$arResult["MESSAGES"][$key1]["POST_MESSAGE_TEXT_EXT"] = array(
			"RATING" => $arrPostMessage[0],
			"PLUS" => $arrPostMessage[1],
			"MINUS" => $arrPostMessage[2],
			"COMMENT" => $arrPostMessage[3],
		);
		
		// format date
		$ts = strtotime($res["POST_DATE"]);
		$date = date("G:i:s d.m.Y",$ts);
		$year_now = date("Y");
		$year = date("Y",$ts);
		$month = date("n",$ts);
		$day = date("j",$ts);
		if($year_now==$year)
		{
			$arResult["MESSAGES"][$key1]["POST_DATE_EXT"] = $day.' '.GetMessage("MONTH_NAME_".$month);
		} else {
			$arResult["MESSAGES"][$key1]["POST_DATE_EXT"] = $day.' '.GetMessage("MONTH_NAME_".$month).' '.$year;
		}
	}
}