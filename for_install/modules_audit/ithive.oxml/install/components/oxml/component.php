<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

define ('DESCRIPTION_SIZE', 511);

if(!CModule::IncludeModule("iblock")) die();

$bCatalog = CModule::IncludeModule('catalog');

//$file = fopen($_SERVER['DOCUMENT_ROOT']. "/yandex.txt","w+");

/*************************************************************************
	Processing of received parameters
*************************************************************************/

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600;

global $$arParams["FILTER_NAME"];
$arrFilter = ${$arParams["FILTER_NAME"]};
if(!is_array($arrFilter)) $arrFilter = array();
		
$arParams["CACHE_FILTER"]=($arParams["CACHE_FILTER"]=="Y");
if(!$arParams["CACHE_FILTER"])
	$arParams["CACHE_TIME"] = 0;

function html_remove_tags($string, $quotes = ENT_COMPAT) {
	$string = preg_replace('/(<.*?\.*>)/', '', $string);
	$string = preg_replace("/[\x1-\x8\xB-\xC\xE-\x1F]/", "", $string);
	$text = htmlspecialchars($string);
	return preg_replace('/(<.*?\.*>)/', '', $text);
}

/**
 * Helper function for decode_entities_full().
 *
 * This contains the full HTML 4 Recommendation listing of entities, so the default to discard  
 * entities not in the table is generally good. Pass false to the second argument to return 
 * the faulty entity unmodified, if you're ill or something.
 * Per: http://www.lazycat.org/software/html_entity_decode_full.phps
 */
function convert_entity($matches, $destroy = true) {
  static $table = array('quot' => '&#34;','amp' => '&#38;','lt' => '&#60;','gt' => '&#62;','OElig' => '&#338;','oelig' => '&#339;','Scaron' => '&#352;','scaron' => '&#353;','Yuml' => '&#376;','circ' => '&#710;','tilde' => '&#732;','ensp' => '&#8194;','emsp' => '&#8195;','thinsp' => '&#8201;','zwnj' => '&#8204;','zwj' => '&#8205;','lrm' => '&#8206;','rlm' => '&#8207;','ndash' => '&#8211;','mdash' => '&#8212;','lsquo' => '&#8216;','rsquo' => '&#8217;','sbquo' => '&#8218;','ldquo' => '&#8220;','rdquo' => '&#8221;','bdquo' => '&#8222;','dagger' => '&#8224;','Dagger' => '&#8225;','permil' => '&#8240;','lsaquo' => '&#8249;','rsaquo' => '&#8250;','euro' => '&#8364;','fnof' => '&#402;','Alpha' => '&#913;','Beta' => '&#914;','Gamma' => '&#915;','Delta' => '&#916;','Epsilon' => '&#917;','Zeta' => '&#918;','Eta' => '&#919;','Theta' => '&#920;','Iota' => '&#921;','Kappa' => '&#922;','Lambda' => '&#923;','Mu' => '&#924;','Nu' => '&#925;','Xi' => '&#926;','Omicron' => '&#927;','Pi' => '&#928;','Rho' => '&#929;','Sigma' => '&#931;','Tau' => '&#932;','Upsilon' => '&#933;','Phi' => '&#934;','Chi' => '&#935;','Psi' => '&#936;','Omega' => '&#937;','alpha' => '&#945;','beta' => '&#946;','gamma' => '&#947;','delta' => '&#948;','epsilon' => '&#949;','zeta' => '&#950;','eta' => '&#951;','theta' => '&#952;','iota' => '&#953;','kappa' => '&#954;','lambda' => '&#955;','mu' => '&#956;','nu' => '&#957;','xi' => '&#958;','omicron' => '&#959;','pi' => '&#960;','rho' => '&#961;','sigmaf' => '&#962;','sigma' => '&#963;','tau' => '&#964;','upsilon' => '&#965;','phi' => '&#966;','chi' => '&#967;','psi' => '&#968;','omega' => '&#969;','thetasym' => '&#977;','upsih' => '&#978;','piv' => '&#982;','bull' => '&#8226;','hellip' => '&#8230;','prime' => '&#8242;','Prime' => '&#8243;','oline' => '&#8254;','frasl' => '&#8260;','weierp' => '&#8472;','image' => '&#8465;','real' => '&#8476;','trade' => '&#8482;','alefsym' => '&#8501;','larr' => '&#8592;','uarr' => '&#8593;','rarr' => '&#8594;','darr' => '&#8595;','harr' => '&#8596;','crarr' => '&#8629;','lArr' => '&#8656;','uArr' => '&#8657;','rArr' => '&#8658;','dArr' => '&#8659;','hArr' => '&#8660;','forall' => '&#8704;','part' => '&#8706;','exist' => '&#8707;','empty' => '&#8709;','nabla' => '&#8711;','isin' => '&#8712;','notin' => '&#8713;','ni' => '&#8715;','prod' => '&#8719;','sum' => '&#8721;','minus' => '&#8722;','lowast' => '&#8727;','radic' => '&#8730;','prop' => '&#8733;','infin' => '&#8734;','ang' => '&#8736;','and' => '&#8743;','or' => '&#8744;','cap' => '&#8745;','cup' => '&#8746;','int' => '&#8747;','there4' => '&#8756;','sim' => '&#8764;','cong' => '&#8773;','asymp' => '&#8776;','ne' => '&#8800;','equiv' => '&#8801;','le' => '&#8804;','ge' => '&#8805;','sub' => '&#8834;','sup' => '&#8835;','nsub' => '&#8836;','sube' => '&#8838;','supe' => '&#8839;','oplus' => '&#8853;','otimes' => '&#8855;','perp' => '&#8869;','sdot' => '&#8901;','lceil' => '&#8968;','rceil' => '&#8969;','lfloor' => '&#8970;','rfloor' => '&#8971;','lang' => '&#9001;','rang' => '&#9002;','loz' => '&#9674;','spades' => '&#9824;','clubs' => '&#9827;','hearts' => '&#9829;','diams' => '&#9830;','nbsp' => '&#160;','iexcl' => '&#161;','cent' => '&#162;','pound' => '&#163;','curren' => '&#164;','yen' => '&#165;','brvbar' => '&#166;','sect' => '&#167;','uml' => '&#168;','copy' => '&#169;','ordf' => '&#170;','laquo' => '&#171;','not' => '&#172;','shy' => '&#173;','reg' => '&#174;','macr' => '&#175;','deg' => '&#176;','plusmn' => '&#177;','sup2' => '&#178;','sup3' => '&#179;','acute' => '&#180;','micro' => '&#181;','para' => '&#182;','middot' => '&#183;','cedil' => '&#184;','sup1' => '&#185;','ordm' => '&#186;','raquo' => '&#187;','frac14' => '&#188;','frac12' => '&#189;','frac34' => '&#190;','iquest' => '&#191;','Agrave' => '&#192;','Aacute' => '&#193;','Acirc' => '&#194;','Atilde' => '&#195;','Auml' => '&#196;','Aring' => '&#197;','AElig' => '&#198;','Ccedil' => '&#199;','Egrave' => '&#200;','Eacute' => '&#201;','Ecirc' => '&#202;','Euml' => '&#203;','Igrave' => '&#204;','Iacute' => '&#205;','Icirc' => '&#206;','Iuml' => '&#207;','ETH' => '&#208;','Ntilde' => '&#209;','Ograve' => '&#210;','Oacute' => '&#211;','Ocirc' => '&#212;','Otilde' => '&#213;','Ouml' => '&#214;','times' => '&#215;','Oslash' => '&#216;','Ugrave' => '&#217;','Uacute' => '&#218;','Ucirc' => '&#219;','Uuml' => '&#220;','Yacute' => '&#221;','THORN' => '&#222;','szlig' => '&#223;','agrave' => '&#224;','aacute' => '&#225;','acirc' => '&#226;','atilde' => '&#227;','auml' => '&#228;','aring' => '&#229;','aelig' => '&#230;','ccedil' => '&#231;','egrave' => '&#232;','eacute' => '&#233;','ecirc' => '&#234;','euml' => '&#235;','igrave' => '&#236;','iacute' => '&#237;','icirc' => '&#238;','iuml' => '&#239;','eth' => '&#240;','ntilde' => '&#241;','ograve' => '&#242;','oacute' => '&#243;','ocirc' => '&#244;','otilde' => '&#245;','ouml' => '&#246;','divide' => '&#247;','oslash' => '&#248;','ugrave' => '&#249;','uacute' => '&#250;','ucirc' => '&#251;','uuml' => '&#252;','yacute' => '&#253;','thorn' => '&#254;','yuml' => '&#255;'
                       );
  if (isset($table[$matches[1]])) return $table[$matches[1]];
  // else 
  return $destroy ? '' : $matches[0];
}

/** IN CASE OF NOT SETTING PARAMETERS AFTER INSTALL **/

$arOptions = unserialize(COption::GetOptionString('ithive.oxml', 'options'));

$arParams['OPTIONS'] = $arParams['OPTIONS'] ? $arParams['OPTIONS'] : $arOptions;

$arParams['PRICE_TYPE'] = $arParams['PRICE_TYPE'] ? $arParams['PRICE_TYPE'] : $arOptions['site']['price_type'];

$arParams['IBLOCKS'] = $arParams['IBLOCKS'] ? $arParams['IBLOCKS'] : $arOptions['iblocks'];
$arParams['SECTIONS'] = $arParams['SECTIONS'] ? $arParams['SECTIONS'] : $arOptions['sections_export'];
$arParams['PROPERS'] = $arParams['PROPERS'] ? $arParams['PROPERS'] : $arOptions['property_export'];
$arParams['SKUPROP'] = $arParams['SKUPROP'] ? $arParams['SKUPROP'] : $arOptions['skuprops_export'];
$arParams['MORE_PHOTO'] = $arParams['MORE_PHOTO'] ? $arParams['MORE_PHOTO'] : $arOptions['more_photo'];
	
/** END IN CASE **/

$baseCurrency = CCurrency::GetBaseCurrency();

$bDesignMode = is_object($GLOBALS["USER"]) && $GLOBALS["USER"]->IsAdmin();

if(!$bDesignMode)
{
	$APPLICATION->RestartBuffer();
	header("Content-Type: text/xml; charset=".SITE_CHARSET);
	header("Pragma: no-cache");
}
else
{
    echo "<br/><b>". GetMessage("HELLO"). "</b><br/>";
	return;
}



/*************************************************************************
			Work with cache
*************************************************************************/
$cache_id = serialize($arrFilter).serialize($arParams); 

if($this->StartResultCache(false, $cache_id, COption::GetOptionString('ithive.oxml', 'dir_to_install')))
{	
	$arResult["DATE"] = Date("Y-m-d H:i");

	// list of the element fields that will be used in selection
	$arSelect = array(
		"ID",
		"NAME",
		"IBLOCK_ID",
		"IBLOCK_SECTION_ID",
		"DETAIL_PAGE_URL",
		"DETAIL_TEXT",
		"DETAIL_PICTURE",
		"PREVIEW_TEXT",
		"PREVIEW_PICTURE",
	);
	
		
	$arFilter = array(
		"IBLOCK_LID" => SITE_ID,
		"IBLOCK_ID" => $arParams['IBLOCKS'],
		"SECTION_ID" => $arParams['SECTIONS'],
		"INCLUDE_SUBSECTIONS" => "Y",
		"IBLOCK_ACTIVE" => "Y",
		"ACTIVE_DATE" => "Y",
		"ACTIVE" => "Y",
		"CHECK_PERMISSIONS" => "Y",
		"SECTION_ACTIVE" => "Y",
		"SECTION_GLOBAL_ACTIVE" => "Y",
	);
	
	if ( $arParams["DO_NOT_INCLUDE_SUBSECTIONS"] == "Y" )
		$arFilter["INCLUDE_SUBSECTIONS"] = "N";

	$arSort = array(
		"ID" => "ASC",
	);


	$i=0;

	//EXECUTE

	foreach ($arParams['IBLOCKS'] as $ib) {
		$infoIBlock = CCatalogSKU::GetInfoByProductIBlock($ib);
        if ($infoIBlock)
			$arParams['IBLOCKS'][] = $infoIBlock['IBLOCK_ID'];
	}
	
	$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array( "ID" => $arParams['IBLOCKS'], "ACTIVE"=>"Y"));

	$arSKUiblockID = array();

	while($res = $rsIBlock->GetNext()) {
		$arResult["CATEGORIES"][$res["ID"]] = Array("ID" => $res["ID"], "NAME" => html_remove_tags($res["NAME"], true));
		$infoIBlock = CCatalogSKU::GetInfoByOfferIBlock($res['ID']);
        if ($infoIBlock)
			$arResult["CATEGORIES"][$res["ID"]]["OFFERS"] = $infoIBlock;
		
		if($bCatalog)
		{
			$rsSKU = CCatalog::GetList( array(), array("PRODUCT_IBLOCK_ID" => $res["ID"]),false, false, array("IBLOCK_ID") );
			if ($arSKUiBlock = $rsSKU->Fetch()) {
				$arSKUiblockID[$res["ID"]] = $arSKUiBlock["IBLOCK_ID"];
			}
		}
	}
	

//fetch sections into categories list
	if((count($arParams['SECTIONS']) == 1 && $arParams['SECTIONS'][0] == 0) || !$arParams['SECTIONS'])
	{
		$filter = Array("IBLOCK_ID"=>$arParams['IBLOCKS'], "ACTIVE"=>"Y", "IBLOCK_ACTIVE"=>"Y", "GLOBAL_ACTIVE"=>"Y");
	}
	else{
		$filter = Array("IBLOCK_ID"=>$arParams['IBLOCKS'], "ID" => $arParams['SECTIONS'],  "ACTIVE"=>"Y", "IBLOCK_ACTIVE"=>"Y", "GLOBAL_ACTIVE"=>"Y");
	}
	
	if((count($arParams['IBLOCKS']) == 1 && $arParams['IBLOCKS'][0] == 0) || !$arParams['IBLOCKS']){			
		unset($filter["IBLOCK_ID"]);
	}
	
	$db_acc = CIBlockSection::GetList(array("left_margin"=>"asc"), $filter, false, array("ID", "NAME", "IBLOCK_ID", "IBLOCK_SECTION_ID"));
		
	while($arAcc = $db_acc->Fetch())
	{
		$id = $arAcc["IBLOCK_ID"].$arAcc["ID"];
		$arResult["CATEGORIES"][$id] = Array(
			"ID" => $id,
			"NAME" => html_remove_tags($arAcc["NAME"], true),
			"PARENT" => $arAcc["IBLOCK_ID"]
			);
		if (IntVal($arAcc["IBLOCK_SECTION_ID"]) < 1) continue;

		$key = $arAcc["IBLOCK_ID"] . $arAcc["IBLOCK_SECTION_ID"];
		if (!array_key_exists($key, $arResult["CATEGORIES"])) continue;

		$arResult["CATEGORIES"][$id]["PARENT"] = $key;
	}

//fetch elements
	$rsElements = CIBlockElement::Getlist($arSort, $arFilter, false, false, $arSelect);
	while($arOffer = $rsElements->GetNext())
	{
						
		$arOfferID[] = $arOffer["ID"];
		$arOffer["SKU"] = array();
		$arOffers[$arOffer["ID"]] = $arOffer;
	}
	unset($rsElements);

//work with module 'catalog'

	if ($bCatalog) {
		if (empty($arSKUiblockID)) {
			$arAllID = $arOfferID; //ID of SKU and offers without any SKU
		} else {
			//fetch SKU
			$arOfferInOb = CIBlockElement::GetList(array($arParams['SKUPROP'] => 'DESC'),
				array("IBLOCK_ID" => $arSKUiblockID, $arParams['SKUPROP'] => $arOfferID, 'ACTIVE' => 'Y'), false, false, $arSelect);

			$arAllID = array(); //ID of SKU and offers without any SKU
			$productKey = $arParams['SKUPROP'] . '_VALUE';

			while($arOfferIn = $arOfferInOb->GetNext())
			{
				$arAllID[] = $arOfferIn["ID"];
				$productID = $arOfferIn[$productKey];
				$arOffers[$productID]["SKUE"][] = $arOfferIn["ID"];
				$arOffers[$arOfferIn["ID"]] = $arOfferIn;
			}
			unset($arOfferInOb);

			foreach ($arOfferID as $offerID) {
				if (empty($arOffers[$offerID]["SKUE"])) $arAllID[] = $offerID;
			}
		}

		//fetch price types
		/*$dbPriceTypes = CCatalogGroup::GetList( array("SORT" => "ASC"), array("NAME" => getPrice(), "CAN_BUY" => "Y") );

		while($arPriceType = $dbPriceTypes -> Fetch()) {
			$arPriceTypesID[] = $arPriceType['ID'];
		}*/

		//fetch and process product prices
		$dbProductPrices = CPrice::GetList(array("PRODUCT_ID" => "DESC"), array("@PRODUCT_ID" => $arAllID, "@CATALOG_GROUP_ID" => $arParams['PRICE_TYPE']));

		$arPrices = array();
		
		while ($arPrices[0] = $dbProductPrices->GetNext())
		{
			$price = 0;
			$product_id = $arPrices[0]['PRODUCT_ID'];
			foreach ($arPrices as $arProductPrice)
			{
				if($arProductPrice['PRICE'] && ($arProductPrice['PRICE'] < $price || !$price)) {
					$price = $arProductPrice['PRICE'];
					$arOffers[$product_id]["CURRENCY"] = $arProductPrice["CURRENCY"];
					$arOffersPrice[$product_id]["CURRENCY"] = $arProductPrice["CURRENCY"];
				}
				
				$arDiscounts = CCatalogDiscount::GetDiscountByProduct($product_id, $GLOBALS["USER"]->GetUserGroupArray(),  "N", $arProductPrice['CATALOG_GROUP_ID'], SITE_ID);
				foreach($arDiscounts as $arDiscount)
				{
					switch ($arDiscount["VALUE_TYPE"]) {
						case 'P': $price_buf = $arProductPrice["PRICE"] - $arDiscount["VALUE"] * $arProductPrice["PRICE"] / 100;
							break;
						case 'F': $price_buf = $arProductPrice["PRICE"] - $arDiscount["VALUE"];
							break;
						default:  $price_buf = $arDiscount["VALUE"];
							break;
					}

					if($price_buf && ($price_buf < $price || !$price)) {
						$price = $price_buf;
						$arOffers[$product_id]["CURRENCY"] = $arProductPrice["CURRENCY"];
						$arOffersPrice[$product_id]["CURRENCY"] = $arProductPrice["CURRENCY"];
					}
				}
			}
			$arOffers[$product_id]["PRICE"] = $price;
			$arOffersPrice[$product_id]["PRICE"] = $price;
		}
		unset($dbProductPrices);
	}

	$arResult['OFFER'] = array();
	$arResult['CURRENCIES'] = array();

	global $ib_code, $offers_code;
	
	// Set offers code for later decisions
	foreach($arParams['IBLOCKS'] as $ib) {
		$rsIb = CIBlock::GetList(array(), array("ID" => $ib));
		
		while($arIb = $rsIb->Fetch()) {

			if ((strpos($arIb['IBLOCK_TYPE_ID'], 'offers') !== false) || preg_match('|offers|', $arIb['CODE']) ) 
				$offers_code = $arIb['CODE'];
			else {
                if ($arIb['IBLOCK_TYPE_ID'] == 'catalog')
	            	$ib_code = $arIb['CODE'];
			}
			
			if ($arParams['PROPERS'][0] == 0) {
				$dbProperty = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $arIb['ID']));
				while($arProperty = $dbProperty->Fetch()) {
					if ($arProperty['CODE'] == 'CML2_LINK' || $arProperty["PROPERTY_TYPE"] == "F") continue;
					
					$arParams["PROPERS"][$arIb['CODE'].'_'.$arProperty['CODE']] = "{$arIb['CODE']}_{$arProperty['CODE']}";
					
				}
			}		
		}
	}

	// Get name of props and decide whether it should be in sku elements or not
	$SKUProp = Array();
	$Prop = Array();
	
	foreach($arParams["PROPERS"] as $k=>$v) {
		if (strpos($v, $offers_code) !== false) {
			$SKUProp[str_replace(array('[',']',$offers_code.'_'), '', $v)] = str_replace($offers_code.'_', '', $v);
		}
		else if (strpos($v, $ib_code) !== false) {
			$Prop[str_replace(array('[',']',$ib_code.'_'), '', $v)] = str_replace($ib_code.'_', '', $v);
		}	
	}
	if ($arParams['MORE_PHOTO']) {
		if (strpos($arParams['MORE_PHOTO'], $offers_code) !== false) {
			$arParams['MORE_PHOTO'] = str_replace($offers_code.'_', '', $arParams['MORE_PHOTO']);
		}
		else if (strpos($arParams['MORE_PHOTO'], $ib_code) !== false) {
			$arParams['MORE_PHOTO'] = str_replace($ib_code.'_', '', $arParams['MORE_PHOTO']);
		}
	}

/* OFFER ITERATION */
	foreach ($arOfferID as &$offerID)
	{
		
		$arOffer = & $arOffers[$offerID];

		$arResult['arRsOff'][1] = $arOffer;
		//setting offer pictures
		if( $arOffer["DETAIL_PICTURE"] )
		{
			$db_file = CFile::GetByID($arOffer["DETAIL_PICTURE"]);
			if ($ar_file = $db_file->Fetch())
				$arOffer["PICTURE"] = "http://".$_SERVER["HTTP_HOST"]."/".( COption::GetOptionString("main", "upload_dir", "upload"))."/".$ar_file["SUBDIR"]."/".implode("/", array_map("rawurlencode", explode("/", $ar_file["FILE_NAME"])));
		}

		if( $arOffer["PREVIEW_PICTURE"] && !$arOffer["PICTURE"] )
		{
			$db_file = CFile::GetByID($arOffer["PREVIEW_PICTURE"]);
			if ($ar_file = $db_file->Fetch())
				$arOffer["PICTURE"] = "http://".$_SERVER["HTTP_HOST"]."/".( COption::GetOptionString("main", "upload_dir", "upload"))."/".$ar_file["SUBDIR"]."/".implode("/", array_map("rawurlencode", explode("/", $ar_file["FILE_NAME"])));
		}
		
		foreach ($Prop as $pr) {
			$dbRes = CIBlockElement::GetProperty($arOffer["IBLOCK_ID"], $arOffer["ID"], array(), Array("CODE" => $pr));
			$arRs = $dbRes->GetNext();
				$res = CIBlockFormatProperties::GetDisplayValue($arRs['VALUE'], $arRs, '');
				$ob['VALUE_ENUM'] = $res['DISPLAY_VALUE'];
				if (!$arRs['VALUE_ENUM'])$arRs['VALUE_ENUM'] = $ob['VALUE_ENUM'];
			
			if (isset($arRs['VALUE'])) 
				$arOffer["PROPERTIES"][] = Array(
					"NAME" => $arRs["NAME"],
					"CODE" => $arRs["CODE"],
					"VALUE" => $arRs["VALUE"],
					"VALUE_E" => html_remove_tags($arRs["VALUE_ENUM"])
				);
		}


		if( isset( $arParams["MORE_PHOTO"] ) )
		{
			$ph = CIBlockElement::GetProperty( $arOffer["IBLOCK_ID"], $arOffer["ID"], array("value_id" => "asc"), Array("CODE" => $arParams["MORE_PHOTO"]) );
			
			while( $ob = $ph->GetNext() )
			{
				$arFile = CFile::GetFileArray( $ob["VALUE"] );
				if ( !empty( $arFile ) )
				{
					if ( strpos( $arFile["SRC"], "http" ) === false )
					{
						$pic = "http://".$_SERVER["HTTP_HOST"].implode( "/", array_map( "rawurlencode", explode( "/", $arFile["SRC"] ) ) );
					}	
					else
					{
						$ar = explode( "http://", $arFile["SRC"] );
						$pic = "http://".implode( "/", array_map( "rawurlencode", explode( "/", $ar[1] ) ) );	
					}
					$arOffer["MORE_PHOTO"][] = $pic;
				}
			}

			if (!$arOffer["PICTURE"] && is_array($arOffer["MORE_PHOTO"]))
				$arOffer['PICTURE'] = array_shift($arOffer["MORE_PHOTO"]);
		}
		//offer URL
		$arOffer["URL"] = "http://".$_SERVER["HTTP_HOST"]. $arOffer["DETAIL_PAGE_URL"];

		//setting offer description
		if ($arOffer["PREVIEW_TEXT"])
			$arOffer["PREVIEW_TEXT"] = html_remove_tags($arOffer['PREVIEW_TEXT']);

		if ($arOffer["DETAIL_TEXT"])
			$arOffer["DETAIL_TEXT"] = html_remove_tags($arOffer['DETAIL_TEXT']);

		$arOffer["DESCRIPTION"] = $arOffer["PREVIEW_TEXT"] ? $arOffer["PREVIEW_TEXT"] : $arOffer["DETAIL_TEXT"];

		if ($arParams["DETAIL_TEXT_PRIORITET"] == "Y")
		{
			$arOffer["DESCRIPTION"] = $arOffer["DETAIL_TEXT"] ? $arOffer["DETAIL_TEXT"] : $arOffer["PREVIEW_TEXT"];
		}
			
		$arOffer["CATEGORY"] = $arOffer["IBLOCK_ID"] . $arOffer["IBLOCK_SECTION_ID"];

		if (!array_key_exists($arOffer["CATEGORY"], $arResult["CATEGORIES"]) && $arOffer["IBLOCK_SECTION_ID"])
		{
			$arGr = CIBlockElement::GetElementGroups($arOffer["ID"]);
			while ($ar_group = $arGr->Fetch()) {
				if (!array_key_exists($arOffer["IBLOCK_ID"].$ar_group["ID"], $arResult["CATEGORIES"])) continue;
				$arOffer["CATEGORY"] = $arOffer["IBLOCK_ID"] . $ar_group["ID"];
			}
		}
		
		$arOffer["MODEL"] = $arOffer["~NAME"];
		
		$arOffer["AVALIABLE"] = "true";
		if( isset( $arParams["IBLOCK_QUANTITY"] ) )
		{
			$av = CIBlockElement::GetProperty( $arOffer["IBLOCK_ID"], $arOffer["ID"], array("sort" => "asc"), Array("CODE" => $arParams["IBLOCK_QUANTITY"]) )->Fetch();
			if( IntVal($av["VALUE"]) > 0 )
				$arOffer["AVALIABLE"] = "true";
			else
			{
				if( $arParams["IBLOCK_ORDER"] == "Y" )
					$arOffer["AVALIABLE"] = "false";
				else
					continue;
			}
		}
		$price = CPrice::GetById($arOffer['ID']);
		
		if (!$arOffer['CURRENCY'])
			$arOffer['CURRENCY'] = ($price['CURRENCY'])?$price['CURRENCY']:$baseCurrency;
		
		if ( !in_array($baseCurrency, $arResult["CURRENCIES"]) )
			$arResult["CURRENCIES"][] = $baseCurrency;
		
		$arOffer["IBLOCK_ID_CATALOG"] = $arOffer["IBLOCK_ID"];
		$arOffer["GROUP_ID"] = $arOffer["ID"];

		$arOffer["MODEL"] = html_remove_tags($arOffer["MODEL"], true);

		$arResult["OFFER"][$offerID]=$arOffer;
		
		//work with offer SKU
		$flag = 0;
		$lowest_price = 0;
		if (!$arOffer['SKUE']) continue;
		foreach ($arOffer["SKUE"] as &$arOfferInID)
		{
			$arOfferIn = & $arOffers[$arOfferInID];
			
			if ($arOffersPrice[$arOfferInID]['CURRENCY'] != $baseCurrency) {
				
				$arOfferIn["PRICE"] = CCurrencyRates::ConvertCurrency($arOffersPrice[$arOfferInID]['PRICE'], $arOffersPrice[$arOfferInID]['CURRENCY'], $baseCurrency);
			}
			else {
				$arOfferIn["PRICE"] = $arOffersPrice[$arOfferInID]['PRICE'];
			}
			
			
			if ( isset($arParams['MORE_PHOTO']) && !$arResult['OFFER'][$offerID]['MORE_PHOTO'] ) {
				$ph = CIBlockElement::GetProperty( $arOfferIn["IBLOCK_ID"], $arOfferIn["ID"], array("sort" => "asc"), Array("CODE" => $arParams['MORE_PHOTO']) );

				while( $ob = $ph->GetNext() )
				{
					$arFile = CFile::GetFileArray( $ob["VALUE"] );
					if ( !empty( $arFile ) )
					{
						if ( strpos( $arFile["SRC"], "http" ) === false )
						{
							$pic = "http://".$_SERVER["HTTP_HOST"].implode( "/", array_map( "rawurlencode", explode( "/", $arFile["SRC"] ) ) );
						}	
						else
						{
							$ar = explode( "http://", $arFile["SRC"] );
							$pic = "http://".implode( "/", array_map( "rawurlencode", explode( "/", $ar[1] ) ) );
							
						}
						$arResult['OFFER'][$offerID]['MORE_PHOTO'][] = $pic;
					}
				}
			}
			
			foreach ($SKUProp as $prop) {
								
				$pr = CIBlockElement::GetProperty( $arOfferIn["IBLOCK_ID"], $arOfferIn["ID"], array("sort" => "asc"), array("CODE" => $prop) );
				
				while ($ob = $pr->GetNext()) {
					
					if ($ob['USER_TYPE_SETTINGS']) {
						$res = CIBlockFormatProperties::GetDisplayValue($ob['VALUE'], $ob, '');
						$ob['VALUE_ENUM'] = $res['DISPLAY_VALUE'];
					}
					
					if ($ob['VALUE_ENUM']) {
						$arOfferIn['SKU_PROP'][$ob['CODE']]['NAME'] = $ob['NAME'];
						$arOfferIn['SKU_PROP'][$ob['CODE']]['VALUE'] = $ob['VALUE_ENUM'];
					} elseif ($ob['VALUE']) {
						$arOfferIn['SKU_PROP'][$ob['CODE']]['NAME'] = $ob['NAME'];
						$arOfferIn['SKU_PROP'][$ob['CODE']]['VALUE'] = $ob['VALUE'];
					}
				}
			}
			
			if ($lowest_price == 0) $lowest_price = $arOfferIn["PRICE"];
			else if ($arOfferIn["PRICE"] < $lowest_price) $lowest_price = $arOfferIn["PRICE"];
						
			$arResult["OFFER"][$offerID]["SKU"][] = $arOfferIn;
		}
		$arResult["OFFER"][$offerID]["PRICE"] = $lowest_price;
		unset($arResult['OFFER'][$offerID]['SKUE']);
		$i++;
	}
	unset($arOffers);

	$this->IncludeComponentTemplate();
}

if(!$bDesignMode)
{
	$r = $APPLICATION->EndBufferContentMan();
	echo $r;
	if(defined("HTML_PAGES_FILE") && !defined("ERROR_404")) CHTMLPagesCache::writeFile(HTML_PAGES_FILE, $r);
	die();
}

?>