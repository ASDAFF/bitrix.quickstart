<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$arParams["CACHE_TIME"] = intval($arParams["CACHE_TIME"]);
if($arParams["CACHE_TIME"]>0)
	$arParams["CACHE_TIME"] = $arParams["CACHE_TIME"];
else
	$arParams["CACHE_TIME"] = 3600;

function know_cnt_elements($variant, $t_start, $t_end, $IBLOCK_ID)
{
	$KOLVO = 0;
	$arOrder = array($variant => "ASC");
	$arFilter = array(
		"IBLOCK_ID" => $IBLOCK_ID,
		"ACTIVE" => "Y",
		">=".$variant => ($t_start),
		"<=".$variant => ($t_end),
	);
	$arNavStartParams = array("nPageSize" => 100000);
	$arSelect = array("ID", $variant);
	$resDB = CIBlockElement::GetList($arOrder, $arFilter, false, $arNavStartParams, $arSelect);
	while($arFields = $resDB->Fetch())
	{
		$KOLVO++;
	}
	return $KOLVO;
}

if ($this->StartResultCache( $arParams["CACHE_TIME"] ))
{
	CModule::IncludeModule('iblock');
	
	$ERROR_DETECTED = false;

	$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
	if($arParams["IBLOCK_ID"]>0)
		$IBLOCK_ID = $arParams["IBLOCK_ID"];
	else
	{
		ShowError( GetMessage("IMYIE_ERROR_NO_IBLOCK_ID") );
		$ERROR_DETECTED = true;
		$this->AbortResultCache();
	}
	
	$KNOW_CNT_ELEMENTS = "N";
	if($arParams["KNOW_CNT_ELEMENTS"]=="Y")
		$KNOW_CNT_ELEMENTS = "Y";
	
	if(!$ERROR_DETECTED)
	{
		$arParams["CNT_MONTH"] = intval($arParams["CNT_MONTH"]);
		if($arParams["CNT_MONTH"]>0)
			$CNT_LAST_MONTH = $arParams["CNT_MONTH"];
		else
			$CNT_LAST_MONTH = 10;
			
		switch ($arParams["ORDERT_VARIANT"])
		{
			case "DATE_ACTIVE_FROM":
				$TIME_VARRIENT = "DATE_ACTIVE_FROM";
			break;
			case "DATE_ACTIVE_TO":
				$TIME_VARRIENT = "DATE_ACTIVE_TO";
			break;
			case "TIMESTAMP_X":
				$TIME_VARRIENT = "TIMESTAMP_X";
			break;
			default:
				$TIME_VARRIENT = "DATE_ACTIVE_FROM";
				$arParams["ORDERT_VARIANT"] = "DATE_ACTIVE_FROM";
			break;
		}
		
		$date_now = time();
		$arData = array();
		
		$date = date("Y-m-d", $date_now);
		$date2 = explode('-', $date);
		$date_year = $date2[0];
		$date_month = $date2[1];
		$date_day = 1;
		for($i=0;$i<$CNT_LAST_MONTH;$i++)
		{
			$date_unix = mktime(0, 0, 0, $date_month, $date_day, $date_year);
			$next_unix = mktime(0, 0, 0, ($date_month+1), $date_day, $date_year);
			if($arParams["ONLY_ACTIVE_ELEMENTS"]=="Y" && $next_unix>$date_now) { $next_unix = $date_now; }
			$start_date = date("d-m-Y", $date_unix);
			$end_date = date("d-m-Y", $next_unix);
			$arOrder = array($TIME_VARRIENT => "ASC");
			$arFilter = array(
				"IBLOCK_ID" => $IBLOCK_ID,
				"ACTIVE" => "Y",
				">=".$TIME_VARRIENT => ($start_date),
				"<=".$TIME_VARRIENT => ($end_date),
			);

			$arNavStartParams = array("nPageSize" => 1);
			$arSelect = array("ID", $TIME_VARRIENT);
			$resDB = CIBlockElement::GetList($arOrder, $arFilter, false, $arNavStartParams, $arSelect);
			if($arFields = $resDB->Fetch())
			{
				$CCNNTT = 0;
				$dateForData = date("Y-m-d", $date_unix);
				$dateForData2 = explode('-', $dateForData);
				if($KNOW_CNT_ELEMENTS=="Y")
					$CCNNTT = know_cnt_elements($TIME_VARRIENT, $start_date, $end_date, $IBLOCK_ID);
				$arData[] = array(
					"UNIX" => $date_unix,
					"DATE_FORMATED" => array(
						"DAY" => $dateForData2[2],
						"MONTH" => $dateForData2[1],
						"MONTH_NAME" => GetMessage("IMYIE_MONTH_NAME_".$dateForData2[1]),
						"YEAR" => $dateForData2[0],
					),
					"ISSET_ELEMENTS" => "Y",
					"CNT" => $CCNNTT,
				);
				$date1 = ConvertDateTime($arFields[$TIME_VARRIENT], "YYYY-MM-DD", "ru");
			} else {
				$CCNNTT = 0;
				$dateForData = date("Y-m-d", $date_unix);
				$dateForData2 = explode('-', $dateForData);
				$arData[] = array(
					"UNIX" => $date_unix,
					"DATE_FORMATED" => array(
						"DAY" => $dateForData2[2],
						"MONTH" => $dateForData2[1],
						"MONTH_NAME" => GetMessage("IMYIE_MONTH_NAME_".$dateForData2[1]),
						"YEAR" => $dateForData2[0],
					),
					"ISSET_ELEMENTS" => "N",
					"CNT" => $CCNNTT,
				);
			}
			$date_month--;
		}

		$arResult["MONTH"] = $arData;
		$this->IncludeComponentTemplate();
	}
}
?>