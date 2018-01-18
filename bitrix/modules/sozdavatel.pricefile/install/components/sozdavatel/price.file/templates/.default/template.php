<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<? if ( ($_REQUEST["szd_download_price"] == "Y") && ($_REQUEST["szd_price_id"] == $arParams["PRICE_ID"]) ): ?>
<? // формирование прайса
	$APPLICATION->RestartBuffer();
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Disposition: attachment;filename=".$arParams["FILENAME"].""); 
	header("Content-Transfer-Encoding: binary ");
	
	// заголовок прайса
	echo iconv(LANG_CHARSET, "windows-1251", $arParams["CAPTION"].";\r\n\r\n");
	
	// шапка прайса - название, описание
	echo iconv(LANG_CHARSET, "windows-1251", GetMessage("CSV_NAME").";");
	if ($arParams["SHOW_PREVIEW_TEXT"] == "Y")
	{
		echo iconv(LANG_CHARSET, "windows-1251", GetMessage("CSV_PREVIEW_TEXT").";");
	}
	if ($arParams["SHOW_DETAIL_TEXT"] == "Y")
	{
		echo iconv(LANG_CHARSET, "windows-1251", GetMessage("CSV_DETAIL_TEXT").";");
	}
	
		
	// шапка прайса - свойства
	foreach ($arResult["DISPLAY_PROPERTIES"] as $arProp)
	{
		echo iconv(LANG_CHARSET, "windows-1251", $arProp["NAME"].";");
	}
	
	// шапка прайса - цены
	foreach ($arResult["PRICES"] as $code=>$arPrice)
	{
		echo iconv(LANG_CHARSET, "windows-1251", $arPrice["TITLE"]." (".$arResult["ITEMS"][0]["PRICES"][$code]["CURRENCY"].");");
	}
	echo "\r\n";
		
	foreach ($arResult["SECTIONS"] as $arSection)
	{
		// название раздела
		echo iconv(LANG_CHARSET, "windows-1251", "\r\n\r\n".$arSection["NAME"]." (".$arSection["ELEMENT_CNT"].");");
		
		// тело прайса
		foreach ($arSection["ITEMS"] as $key=>$arItem)
		{			
			// название, описание
			echo iconv(LANG_CHARSET, "windows-1251", "\r\n".$arItem["NAME"].";");
			if ($arParams["SHOW_PREVIEW_TEXT"] == "Y")
			{
				$text = $arItem["PREVIEW_TEXT"];
				$text = str_replace(Array("<br>","<br/>","<br />"), "", $text);
				$text = str_replace(";", ",", $text);
				echo iconv(LANG_CHARSET, "windows-1251", $text.";");
			}
			if ($arParams["SHOW_DETAIL_TEXT"] == "Y")
			{
				$text = $arItem["DETAIL_TEXT"];
				$text = str_replace(Array("<br>","<br/>","<br />"), "", $text);
				$text = str_replace(";", ",", $text);
				echo iconv(LANG_CHARSET, "windows-1251", $text.";");
			}
			
			// свойства
			foreach ($arResult["DISPLAY_PROPERTIES"] as $code=>$arFields)
			{
				echo iconv(LANG_CHARSET, "windows-1251", $arItem["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"].";");
			}
			
			// шапка прайса - цены
			foreach ($arResult["PRICES"] as $code=>$arFields)
			{
				$arPrice = $arItem["PRICES"][$code];
				if ($arPrice["CAN_ACCESS"])
				{
					if ($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"])
					{
						echo iconv(LANG_CHARSET, "windows-1251", $arPrice["DISCOUNT_VALUE"].";");
					}
					else
					{
						echo iconv(LANG_CHARSET, "windows-1251", $arPrice["VALUE"].";");
					}
				}
				else
				{
					echo ";";
				}
			}
		}
	}
	
	die();
?>
<? else: // кнопка "скачать прайс" ?>
		<form method="get" action="" name="szd_download_price_form<?=$arParams["PRICE_ID"]?>">
			<input type="hidden" name="szd_download_price" value="Y" />
			<input type="hidden" name="szd_price_id" value="<?=$arParams["PRICE_ID"]?>" />
			<a class="szd-get-price-button" href="javascript:undefined;" onclick="document.szd_download_price_form<?=$arParams["PRICE_ID"]?>.submit();"><?=GetMessage("GET_PRICE")?></a>
		</form>
<? endif; ?>