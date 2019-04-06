<?
include($_SERVER["DOCUMENT_ROOT"].BX_ROOT.'/modules/gsa.modul/lang/ru/all.php');
//<title>GetShopApp</title>
// Выведем в файл данных название выбраного инфоблока
$strName = "";


foreach ($IBLOCK_PHOTOS as $value) {
	$arValue = explode('-', $value);
	$AR_IBLOCK_PHOTOS[$arValue[0]] = $arValue[1];
}
	if(!$OFFER_PHOTOS) $OFFER_PHOTOS = array();
foreach ($OFFER_PHOTOS as $value) {
	$arValue = explode('-', $value);
	$AR_OFFER_PHOTOS[$arValue[0]] = $arValue[1];
}

// Переменная $IBLOCK_ID должна быть установлена
// мастером экспорта или из профиля
// Переменная $SETUP_FILE_NAME должна быть установлена
// мастером экспорта или из профиля

// Модули каталога и инфоблоков уже подключены

function parseFields($arFields, $SERVER_NAME, $hasOffers, $isOffer) {
	$arResult = array();
	if (!is_array($arFields) || count($arFields) <=0) return $arResult;
	$arResult["ForeignID"] = intval($arFields["ID"]);
	$arResult["Name"] = htmlspecialchars($arFields["NAME"]);

	//выбираем все разделы элемента, может быть несколько
	$arResult['CIDs'] = array();
	$rsSections = CIBlockElement::GetElementGroups($arFields["ID"], false, array('ID', 'ADDITIONAL_PROPERTY_ID'));
	while ($arSection = $rsSections->Fetch())
	{
		if (0 < intval($arSection['ADDITIONAL_PROPERTY_ID']))
			continue;
		$arResult['CIDs'][] = intval($arSection["ID"]);
	}

	if (!$isOffer && count($arResult['CIDs']) == 0) {
		$arResult['CIDs'][] = 4294967295 - $arFields["IBLOCK_ID"];
	}

	$arResult["ShortDesc"] = strip_tags($arFields["PREVIEW_TEXT"]);
	if ($arFields["DETAIL_TEXT_TYPE"] == 'html') {
		$arResult["Desc"] = $arFields["DETAIL_TEXT"];
	} else {
		$arResult["Desc"] = strip_tags($arFields["DETAIL_TEXT"]);
	}

	//картинка
	if (intval($arFields["DETAIL_PICTURE"])>0 || intval($arFields["PREVIEW_PICTURE"])>0)

	{
		$pictNo = intval($arFields["DETAIL_PICTURE"]);
		if ($pictNo <= 0)
			$pictNo = intval($arFields["PREVIEW_PICTURE"]);

		if ($ar_file = CFile::GetFileArray($pictNo))
		{
			if(substr($ar_file["SRC"], 0, 1) == "/")
				$arResult["Pics"][] = "http://".$SERVER_NAME.implode("/", array_map("rawurlencode", explode("/", $ar_file["SRC"])));
			elseif(preg_match("/^(http|https):\\/\\/(.*?)\\/(.*)\$/", $ar_file["SRC"], $match))
				$arResult["Pics"][] = "http://".$match[2].'/'.implode("/", array_map("rawurlencode", explode("/", $match[3])));
			else
				$arResult["Pics"][] = $ar_file["SRC"];
		}
	}


	if (strlen($arFields['DETAIL_PAGE_URL']) <= 0)
		$arResult['URL'] = '/';
	else
		$arResult['URL'] = str_replace(' ', '%20', $arFields['DETAIL_PAGE_URL']);

	$arResult["URL"] =  "http://".$SERVER_NAME.htmlspecialcharsbx($arResult['URL']);

	return $arResult;
}

function getServerNameById($id) {
	$db_iblock = CIBlock::GetByID($id);
	if (!($ar_iblock = $db_iblock->Fetch()))
	{
		return false;
	}
	else {
		if (strlen($ar_iblock['SERVER_NAME']) <= 0)
		{
			$rsSite = CSite::GetList(($b="sort"), ($o="asc"), array("LID" => $ar_iblock["LID"]));
			if($arSite = $rsSite->Fetch())
				$ar_iblock["SERVER_NAME"] = $arSite["SERVER_NAME"];
			if(strlen($ar_iblock["SERVER_NAME"])<=0 && defined("SITE_SERVER_NAME"))
				$ar_iblock["SERVER_NAME"] = SITE_SERVER_NAME;
			if(strlen($ar_iblock["SERVER_NAME"])<=0)
				$ar_iblock["SERVER_NAME"] = COption::GetOptionString("main", "server_name", "");
		}

		return $ar_iblock["SERVER_NAME"];
	}
}

function parseProperties($arProperties, $SERVER_NAME, $hasOffers, $photoMap) {
	$arResult = array();
	if (!is_array($arProperties) || count($arProperties) <=0) return $arResult;

	foreach ($arProperties as $key => $arProperty) {
		switch ($arProperty["PROPERTY_TYPE"]) {
			case 'E':
				if (!empty($arProperty['VALUE']))
				{
					$arCheckValue = array();
					if (!is_array($arProperty['VALUE']))
					{
						$arProperty['VALUE'] = intval($arProperty['VALUE']);
						if (0 < $arProperty['VALUE'])
							$arCheckValue[] = $arProperty['VALUE'];
					}
					else
					{
						foreach ($arProperty['VALUE'] as &$intValue)
						{
							$intValue = intval($intValue);
							if (0 < $intValue)
								$arCheckValue[] = $intValue;
						}
						if (isset($intValue))
							unset($intValue);
					}
					if (!empty($arCheckValue))
					{
						$dbRes = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID'], 'ID' => $arCheckValue), false, false, array('NAME'));
						while ($arRes = $dbRes->Fetch())
						{
							$value .= ($value ? ', ' : '').$arRes['NAME'];
						}
					}
					$arResult[] = array("ID"=> $arProperty["ID"], "Name" => $arProperty["NAME"], "Value" => $value);
				}
				break;
			case 'F':
				if (!empty($arProperty['VALUE']))
				{
					$value = array();
					$arCheckValue = array();
					if (!is_array($arProperty['VALUE']))
					{
						$arProperty['VALUE'] = intval($arProperty['VALUE']);
						if (0 < $arProperty['VALUE'])
							$arCheckValue[] = $arProperty['VALUE'];
					}
					else
					{
						foreach ($arProperty['VALUE'] as $intValue)
						{
							$intValueM = intval($intValue);
							if (0 < $intValueM)
								$arCheckValue[] = $intValueM;
						}
					}
					if (!empty($arCheckValue))
					{
						foreach ($arCheckValue as $fid) {
							if ($ar_file = CFile::GetFileArray(intval($fid)))
							{
								if(substr($ar_file["SRC"], 0, 1) == "/")
									$strFile = "http://".$SERVER_NAME.implode("/", array_map("rawurlencode", explode("/", $ar_file["SRC"])));
								elseif(preg_match("/^(http|https):\\/\\/(.*?)\\/(.*)\$/", $ar_file["SRC"], $match))
									$strFile = "http://".$match[2].'/'.implode("/", array_map("rawurlencode", explode("/", $match[3])));
								else
									$strFile = $ar_file["SRC"];

								$value[] = $strFile;

							}
						}
						if ($photoMap && $arProperty['ID'] == $photoMap) {
							$arResult["GSA_ADDITIONAL_PHOTOS"] = $value;
						} else {
							$arResult[] = array("ID"=> $arProperty["ID"], "Name" => $arProperty["NAME"], "Value" => $value);
						}

					}
				}
				break;
			case 'G':
				if (!empty($arProperty['VALUE']))
				{
					$arCheckValue = array();
					if (!is_array($arProperty['VALUE']))
					{
						$arProperty['VALUE'] = intval($arProperty['VALUE']);
						if (0 < $arProperty['VALUE'])
							$arCheckValue[] = $arProperty['VALUE'];
					}
					else
					{
						foreach ($arProperty['VALUE'] as &$intValue)
						{
							$intValue = intval($intValue);
							if (0 < $intValue)
								$arCheckValue[] = $intValue;
						}
						if (isset($intValue))
							unset($intValue);
					}
					if (!empty($arCheckValue))
					{
						$dbRes = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID'], 'ID' => $arCheckValue), false, array('NAME'));
						while ($arRes = $dbRes->Fetch())
						{
							$value .= ($value ? ', ' : '').$arRes['NAME'];
						}
					}
					$arResult[] = array("ID"=> $arProperty["ID"], "Name" => $arProperty["NAME"], "Value" => $value);
				}
				break;
			case 'L':
				if ($arProperty['VALUE'])
				{
					if (is_array($arProperty['VALUE']))
						$value .= implode(', ', $arProperty['VALUE']);
					else
						$value .= $arProperty['VALUE'];
					$arResult[] = array("ID"=> $arProperty["ID"], "Name" => $arProperty["NAME"], "Value" => $value);
				}
				break;
			default:

					$value = is_array($arProperty['VALUE']) ? implode(', ', $arProperty['VALUE']) : $arProperty['VALUE'];
					$arResult[] = array("ID"=> $arProperty["ID"], "Name" => $arProperty["NAME"], "Value" => $value);
		}
	}
return $arResult;
}

function getPrices($PRODUCT_ID, $PRICE_TYPE, $iofferssite, $BASE_CURRENCY, $RUR) {
	$minPrice = -1;
	if ($PRICE_TYPE > 0)
	{
		$rsPrices = CPrice::GetListEx(array(),array(
			'PRODUCT_ID' => $PRODUCT_ID,
			'CATALOG_GROUP_ID' => $PRICE_TYPE,
			'CAN_BUY' => 'Y',
			'GROUP_GROUP_ID' => array(2),
			'+<=QUANTITY_FROM' => 1,
			'+>=QUANTITY_TO' => 1,
			)
		);
		if ($arPrice = $rsPrices->Fetch())
		{
			if ($arOptimalPrice = CCatalogProduct::GetOptimalPrice(
				$PRODUCT_ID,
				1,
				array(2),
				'N',
				array($arPrice),
				$iofferssite
			))
			{
				$minPrice = $arOptimalPrice['DISCOUNT_PRICE'];
				$minPriceCurrency = $BASE_CURRENCY;
				$minPriceRUR = CCurrencyRates::ConvertCurrency($minPrice, $BASE_CURRENCY, $RUR);
				$minPriceGroup = $arOptimalPrice['PRICE']['CATALOG_GROUP_ID'];
			}
		}
	}
	else
	{
		if ($arPrice = CCatalogProduct::GetOptimalPrice(
			$PRODUCT_ID,
			1,
			array(2), // anonymous
			'N',
			array(),
			$iofferssite
		))
		{
			$minPrice = $arPrice['DISCOUNT_PRICE'];
			$minPriceCurrency = $BASE_CURRENCY;
			$minPriceRUR = CCurrencyRates::ConvertCurrency($minPrice, $BASE_CURRENCY, $RUR);
			$minPriceGroup = $arPrice['PRICE']['CATALOG_GROUP_ID'];
		}
	}
	if ($minPrice <= 0) return 0;
	return array("NEW_PRICE" => $minPrice, "OLD_PRICE" => CCurrencyRates::ConvertCurrency($arPrice["PRICE"], $BASE_CURRENCY, $RUR) , "CUR" => $RUR);
}


//определение валют
if ($arCurrency = CCurrency::GetByID('RUR'))
	$RUR = 'RUR';
else
	$RUR = 'RUB';

$BASE_CURRENCY = CCurrency::GetBaseCurrency();


//главный цикл
$arItems = array();
foreach ($IBLOCKS as $iblockid) {
	$ioffersid = false;
	$hasOffers = false;
	$arIblockInfo = CCatalog::GetByID($iblockid);
	$iblocksite = $arIblockInfo["LID"];
	if (!empty($arIblockInfo)) //ищем инфоблок торговых предложений
	{
		$arOffers = CCatalogSKU::GetInfoByProductIBlock($iblockid);
		if ($arOffers["IBLOCK_ID"]) {
			$arOffersInfo = CCatalog::GetByID($arOffers["IBLOCK_ID"]);
			if ($arOffersInfo) {
				$ioffersid = $arOffersInfo["ID"];
				$ioffersname = $arOffersInfo["NAME"];
				$iofferssite = $arOffersInfo["LID"];
				$hasOffers = true;
			}
		}
	}


	$SERVER_NAME = getServerNameById($iblockid);
	if ($SERVER_NAME === false) {
		$arRunErrors[] = GetMessage("GSA_MISSINFO");
	};

	//получаем ИД-свойства привязки к СКУ
	$SKU_PROP_ID = false;
	$SKU_PROP_CODE = false;

	$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$ioffersid, "USER_TYPE" => "SKU"));
	while ($prop_fields = $properties->GetNext())
	{
	  $SKU_PROP_ID = $prop_fields["ID"];
	  $SKU_PROP_CODE = $prop_fields["CODE"];
	}


	$arSelect = array("ID", "LID", "IBLOCK_ID", "IBLOCK_SECTION_ID", "ACTIVE", "ACTIVE_FROM", "ACTIVE_TO", "NAME", "PREVIEW_PICTURE", "PREVIEW_TEXT", "DETAIL_TEXT", "DETAIL_TEXT_TYPE", "PREVIEW_TEXT_TYPE", "DETAIL_PICTURE", "DETAIL_PAGE_URL");
	$arFilter = Array("IBLOCK_ID"=>intval($iblockid), "ACTIVE_DATE"=>"Y", "ACTIVE"=>"Y");
	$arFilter["INCLUDE_SUBSECTIONS"] = "Y";
	$rsItems = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
	while ($obItem = $rsItems->GetNextElement())
	{
		$arItem = parseFields($obItem->GetFields(), $SERVER_NAME, $hasOffers, false);
		if (array_key_exists($iblockid, $AR_IBLOCK_PHOTOS)) {
			$photoProp = $AR_IBLOCK_PHOTOS[$iblockid];
		} else {
			$photoProp = false;
		}
		$arItem['Specs'] = parseProperties($obItem->GetProperties(), $SERVER_NAME, $hasOffers, $photoProp);
		if (count($arItem['Specs']["GSA_ADDITIONAL_PHOTOS"]) > 0) {
			$arItem["Pics"] = array_merge($arItem["Pics"], $arItem['Specs']["GSA_ADDITIONAL_PHOTOS"]);
			unset($arItem['Specs']["GSA_ADDITIONAL_PHOTOS"]);
		}
		$arItem['Variants'] = array(new ArrayObject());

		//торговые предложения
		if ($hasOffers) {
			$arItem['Variants'] = array();
			$arOfferSelect = array("ID", "LID", "IBLOCK_ID", "ACTIVE", "NAME", "PREVIEW_PICTURE", "PREVIEW_TEXT", "PREVIEW_TEXT_TYPE", "DETAIL_TEXT", "DETAIL_TEXT_TYPE", "DETAIL_PICTURE", "DETAIL_PAGE_URL");
			$arOfferFilter = array('IBLOCK_ID' => $ioffersid, 'PROPERTY_'.$SKU_PROP_CODE => $arItem["ForeignID"], "ACTIVE" => "Y", "ACTIVE_DATE" => "Y");

			$rsOfferItems = CIBlockElement::GetList(array(),$arOfferFilter,false,false,$arOfferSelect);

			while ($obOfferItem = $rsOfferItems->GetNextElement())
			{
				$arOfferItem = parseFields($obOfferItem->GetFields(), $SERVER_NAME, false, true);
				if (is_array($AR_OFFER_PHOTOS) && array_key_exists($ioffersid, $AR_OFFER_PHOTOS)) {
					$photoProp = $AR_OFFER_PHOTOS[$ioffersid];
				} else {
					$photoProp = false;
				}
				$arOfferItem['Specs'] = parseProperties($obOfferItem->GetProperties(), $SERVER_NAME, false, $photoProp);

				if (count($arOfferItem['Specs']["GSA_ADDITIONAL_PHOTOS"]) > 0) {
					$arOfferItem["Pics"] = array_merge($arOfferItem["Pics"], $arOfferItem['Specs']["GSA_ADDITIONAL_PHOTOS"]);
					unset($arOfferItem['Specs']["GSA_ADDITIONAL_PHOTOS"]);
				}

				$prices = getPrices($arOfferItem["ForeignID"], $PRICE_TYPE, $iofferssite, $BASE_CURRENCY, $RUR);

				$arOfferItem["Price"] = $prices["NEW_PRICE"];
				$arOfferItem["OldPrice"] = $prices["OLD_PRICE"];
				$arOfferItem["Curr"] = $prices["CUR"];

				$ar_res = CCatalogProduct::GetByID($arOfferItem["ForeignID"]);
				if ($ar_res) {
					$arOfferItem["Quantity"] = intval($ar_res["QUANTITY"]);
					if ($arOfferItem["Quantity"] > 0) {
						$arOfferItem["Avail"] = true;
					} else {
						$arOfferItem["Avail"] = false;
					}
				}
				unset($arOfferItem["CIDs"]);
				$arItem["Variants"][] = $arOfferItem;
			} //вайл по торговым предложениям

		} else {
			$ar_res = CCatalogProduct::GetByID($arItem["ForeignID"]);
			if ($ar_res) {
				$arItem["Quantity"] = intval($ar_res["QUANTITY"]);
				if ($arItem["Quantity"] > 0) {
					$arItem["Avail"] = true;
				} else {
					$arItem["Avail"] = false;
				}
			}
			$prices = getPrices($arItem["ForeignID"], $PRICE_TYPE, $iblocksite, $BASE_CURRENCY, $RUR);

			$arItem["Price"] = $prices["NEW_PRICE"];
			$arItem["OldPrice"] = $prices["OLD_PRICE"];
			$arItem["Curr"] = $prices["CUR"];
		}
		if ($hasOffers) {
			if (count($arItem["Variants"]) > 0) {
				$arItems[] = $arItem;
			} else {
				$ar_res = CCatalogProduct::GetByID($arItem["ForeignID"]);
				if ($ar_res) {
					$arItem["Quantity"] = intval($ar_res["QUANTITY"]);
					if ($arItem["Quantity"] > 0) {
						$arItem["Avail"] = true;
					} else {
						$arItem["Avail"] = false;
					}
				}

				$prices = getPrices($arItem["ForeignID"], $PRICE_TYPE, $iblocksite, $BASE_CURRENCY, $RUR);
				$arItem["Price"] = $prices["NEW_PRICE"];
				$arItem["OldPrice"] = $prices["OLD_PRICE"];
				$arItem["Curr"] = $prices["CUR"];

				$arItem['Variants'] = array(new ArrayObject());

				if ($arItem["Price"] > 0) {
					$arItems[] = $arItem;
				}
			}
		} elseif (!$hasOffers) {
			$arItems[] = $arItem;
		}
	}

}

$arSectionsAll = array();
$arSectionsAll[] = array(
	"CID"  => 0,
	"Pred" => null,
	"Name" => "Корневая",
	"Desc" => "",
	"Icon" => "",
	);
$iblockGsaNames = COption::GetOptionString("gsa.modul", "IBLOCK_GSA_NAMES");
$iblockGsaNames = ($iblockGsaNames) ? unserialize($iblockGsaNames) : "";
foreach ($IBLOCKS as $iblockid) {
	$SERVER_NAME = getServerNameById($iblockid);
	$arFilter = array('IBLOCK_ID' => $iblockid);
	$rsSect = CIBlockSection::GetList(array('sort' => 'asc'), $arFilter);
	if ($iblockGsaNames[$iblockid]) {$iblockname = $iblockGsaNames[$iblockid];} else {
		$res = CIBlock::GetByID($iblockid);
		if($ar_res = $res->GetNext()) $iblockname = $ar_res['NAME'];
	}
	$arSectionsAll[] = array(
			"CID"  => 4294967295 - $iblockid,
			"Pred" => 0,
			"Name" => $iblockname,
			"Desc" => "",
			"Icon" => "",
		);
	while ($arSect = $rsSect->GetNext())
	{
		if (!intval($arSect["IBLOCK_SECTION_ID"])) {$pred = 4294967295 - $iblockid;} else {$pred = intval($arSect["IBLOCK_SECTION_ID"]);};
		if (CFile::GetPath($arSect["PICTURE"])) {$pic = "http://".$SERVER_NAME.CFile::GetPath($arSect["PICTURE"]);} else {$pic = "";};
	   $arSectionsAll[] = array(
			"CID"  => intval($arSect["ID"]),
			"Pred" => $pred,
			"Name" => $arSect["NAME"],
			"Desc" => $arSect["DESCRIPTION"],
			"Icon" => $pic,
	   	);
	}
}

$arExport = array(
		"ItemsList" => $arItems,
		"CatsList" => $arSectionsAll,
	);


$json_result = json_encode_cyr($arExport);
//$strName.= $json_result;
$strName=$json_result;
// $strName.=print_r($arExport, true);

if (strlen($strName)>0)
{
	if ($fp = @fopen($_SERVER["DOCUMENT_ROOT"].$SETUP_FILE_NAME,
					 'wb'))
	{
		@fwrite($fp, $strName);
		@fclose($fp);
	}
	else
	{
		$strExportErrorMessage = GetMessage("GSA_OPENERROR");
	}
}
else
{
     $strExportErrorMessage = GetMessage("GSA_MISSINFOBLOCK");
}
function json_encode_cyr($str) {
    $arr_replace_utf = array('\u0410', '\u0430','\u0411','\u0431','\u0412','\u0432',
    '\u0413','\u0433','\u0414','\u0434','\u0415','\u0435','\u0401','\u0451','\u0416',
    '\u0436','\u0417','\u0437','\u0418','\u0438','\u0419','\u0439','\u041a','\u043a',
    '\u041b','\u043b','\u041c','\u043c','\u041d','\u043d','\u041e','\u043e','\u041f',
    '\u043f','\u0420','\u0440','\u0421','\u0441','\u0422','\u0442','\u0423','\u0443',
    '\u0424','\u0444','\u0425','\u0445','\u0426','\u0446','\u0427','\u0447','\u0428',
    '\u0448','\u0429','\u0449','\u042a','\u044a','\u042b','\u044b','\u042c','\u044c',
    '\u042d','\u044d','\u042e','\u044e','\u042f','\u044f');
    $arr_replace_cyr = array('А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е',
    'Ё', 'ё', 'Ж','ж','З','з','И','и','Й','й','К','к','Л','л','М','м','Н','н','О','о',
    'П','п','Р','р','С','с','Т','т','У','у','Ф','ф','Х','х','Ц','ц','Ч','ч','Ш','ш',
    'Щ','щ','Ъ','ъ','Ы','ы','Ь','ь','Э','э','Ю','ю','Я','я');
    if(strtolower(SITE_CHARSET)!='utf-8')
    {    	
    	array_walk_recursive($str, function(&$value, $key)  
    		{
    			if (is_string($value)) 
    				{
    					$value = $APPLICATION->ConvertCharset($value, SITE_CHARSET, 'utf-8'); 
    			    }
    		});
    }
    $str1 = json_encode($str);
    $str2 = str_replace($arr_replace_utf,$arr_replace_cyr,$str1);
return $str2;
}
?>