<?
if(SITE_CHARSET == 'windows-1251') mysql_query('SET NAMES cp1251');
else mysql_query('SET NAMES utf8');

// die();

CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");

/**
 * Create index.php, .section.php
 * 
 * @param <String> iblock_type
 * @param <String> iblock_code
 * @param <Number> iblock_id
 * @param <String> iblock_name
 * @param <Array>  meta
 */
function createFiles4IB($iblock_type, $iblock_code, $iblock_id, $iblock_name, $meta)
{
	$dir = str_replace("catalog-", "", str_replace("_", "-", $iblock_type));
	$type_dir = $_SERVER["DOCUMENT_ROOT"]."/".$dir.'/';
				
	$dirI = str_replace("_", "-", $iblock_code);
	$iblock_dir = $type_dir.$dirI.'/';
	
	$link_to_iblock = '/'.$dir.'/'.$dirI.'/';
	
	$index =    '<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
	$APPLICATION->SetTitle("'.$iblock_name.'");?>
	<?$APPLICATION->IncludeComponent("bitrix:catalog", "catalog", array(
	"IBLOCK_TYPE" => "catalog_'.$iblock_type.'",
	"IBLOCK_ID" => "'.$iblock_id.'",
	"BLOCK_IMG_SMALL" => "3",
	"BLOCK_IMG_BIG" => "4",
	"LIST_IMG" => "3",
	"TABLE_IMG" => "5",
	"DETAIL_IMG_SMALL" => "7",
	"DETAIL_IMG_BIG" => "1",
	"DETAIL_IMG_ICON" => "6",
	"SETTINGS_HIDE" => array(
		0 => "MORE_PHOTO",
		1 => "SHOW_MAIN",
		2 => "NEW",
		3 => "SALE",
		4 => "HIT",
		5 => "DESCRIPTION",
		6 => "KEYWORDS",
		7 => "TITLE",
		8 => "url",
		9 => "DELIVERY",
		10 => "FOR_ORDER",
		11 => "H1",
	),
	"BASKET_URL" => "/personal/basket.php",
	"ACTION_VARIABLE" => "action",
	"PRODUCT_ID_VARIABLE" => "id",
	"SECTION_ID_VARIABLE" => "SECTION_ID",
	"SEF_MODE" => "Y",
	"SEF_FOLDER" => "'.$link_to_iblock.'",	
	"AJAX_MODE" => "N",
	"AJAX_OPTION_JUMP" => "N",
	"AJAX_OPTION_STYLE" => "Y",
	"AJAX_OPTION_HISTORY" => "N",
	"CACHE_TYPE" => "A",
	"CACHE_TIME" => "36000",
	"CACHE_FILTER" => "N",
	"CACHE_GROUPS" => "Y",
	"SET_TITLE" => "N",
	"SET_STATUS_404" => "Y",
	"USE_FILTER" => "Y",
	"FILTER_NAME" => "arrFilter",
	"FILTER_FIELD_CODE" => array(
		0 => "",
		1 => "",
	),
	"FILTER_PROPERTY_CODE" => array(		
		0 => "PRODUCER",
	),
	"FILTER_PRICE_CODE" => array(
		0 => "BASE",
	),
	"USE_REVIEW" => "Y",
	"MESSAGES_PER_PAGE" => "10",
	"USE_CAPTCHA" => "Y",
	"REVIEW_AJAX_POST" => "N",
	"PATH_TO_SMILE" => "/bitrix/images/forum/smile/",
	"FORUM_ID" => "1",
	"URL_TEMPLATES_READ" => "",
	"SHOW_LINK_TO_FORUM" => "Y",
	"POST_FIRST_MESSAGE" => "N",
	"USE_COMPARE" => "Y",
	"COMPARE_NAME" => "CATALOG_COMPARE_LIST",
	"COMPARE_FIELD_CODE" => array(
		0 => "NAME",
		1 => "",
	),
	"COMPARE_PROPERTY_CODE" => array(
		0 => "COUNTRY",
		1 => "PRODUCER",
		2 => "",
	),
	"COMPARE_ELEMENT_SORT_FIELD" => "sort",
	"COMPARE_ELEMENT_SORT_ORDER" => "asc",
	"DISPLAY_ELEMENT_SELECT_BOX" => "N",
	"PRICE_CODE" => array(
		0 => "BASE",
		1 => "WHOLESALE",
		2 => "RETAIL",
		3 => "'.GetMessage("PRICE_1").'",
	),
	"USE_PRICE_COUNT" => "N",
	"SHOW_PRICE_COUNT" => "1",
	"PRICE_VAT_INCLUDE" => "Y",
	"PRICE_VAT_SHOW_VALUE" => "N",
	"CONVERT_CURRENCY" => "Y",
	"CURRENCY_ID" => "RUB",
	"SHOW_TOP_ELEMENTS" => "Y",
	"TOP_ELEMENT_COUNT" => "9",
	"TOP_LINE_ELEMENT_COUNT" => "3",
	"TOP_ELEMENT_SORT_FIELD" => "sort",
	"TOP_ELEMENT_SORT_ORDER" => "asc",
	"TOP_PROPERTY_CODE" => array(
		0 => "",
		1 => "",
	),
	"PAGE_ELEMENT_COUNT" => "20",
	"LINE_ELEMENT_COUNT" => "3",
	"ELEMENT_SORT_FIELD" => "sort",
	"ELEMENT_SORT_ORDER" => "asc",
	"LIST_PROPERTY_CODE" => array(
		0 => "",
		1 => "PHOTO",
		2 => "",
	),
	"LIST_OFFERS_FIELD_CODE" => array(
		0 => "NAME",
		1 => "PREVIEW_PICTURE",
		2 => "DETAIL_PICTURE",
		3 => "",
	),
	"LIST_OFFERS_PROPERTY_CODE" => array(
		0 => "",
		1 => "",
	),
	"DETAIL_OFFERS_FIELD_CODE" => array(
		0 => "NAME",
		1 => "PREVIEW_PICTURE",
		2 => "DETAIL_PICTURE",
		3 => "",
	),
	"DETAIL_OFFERS_PROPERTY_CODE" => array(
		0 => "",
		1 => "",
	),
	"INCLUDE_SUBSECTIONS" => "Y",
	"LIST_META_KEYWORDS" => "-",
	"LIST_META_DESCRIPTION" => "-",
	"LIST_BROWSER_TITLE" => "-",
	"DETAIL_PROPERTY_CODE" => array(		
		1 => "MORE_PHOTO",
	),
	"DETAIL_META_KEYWORDS" => "-",
	"DETAIL_META_DESCRIPTION" => "-",
	"DETAIL_BROWSER_TITLE" => "-",
	"LINK_IBLOCK_TYPE" => "",
	"LINK_IBLOCK_ID" => "",
	"LINK_PROPERTY_SID" => "",
	"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
	"USE_ALSO_BUY" => "N",
	"DISPLAY_TOP_PAGER" => "N",
	"DISPLAY_BOTTOM_PAGER" => "Y",
	"PAGER_TITLE" => "'.$iblock_name.'",
	"PAGER_SHOW_ALWAYS" => "N",
	"PAGER_TEMPLATE" => "",
	"PAGER_DESC_NUMBERING" => "N",
	"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
	"PAGER_SHOW_ALL" => "N",
	"HIDE_ORDER_PRICE" => "Y",
	"AJAX_OPTION_ADDITIONAL" => "",
	"SEF_URL_TEMPLATES" => array(
		"sections" => "",
		"section" => "#SECTION_CODE#/",
		"element" => "#ELEMENT_CODE#.html",
		"compare" => "compare.php?action=#ACTION_CODE#",
	),
	"VARIABLE_ALIASES" => array(
		"compare" => array(
			"ACTION_CODE" => "action",
		),
	)
	),
	false
	);?> <?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>';
	

	$section = '
		<?
		$sSectionName = "'.str_replace('"',"", $iblock_name).'";
		$arDirProperties = Array(
			"title"			=> "'.$meta[0].'",
			"keywords"		=> "'.$meta[1].'",
			"description"	=> "'.$meta[2].'",
			"h1"			=> "'.$meta[3].'"
		);
        ?>';

	$section2 = '
		<?
		$sSectionName = "'.$arIBType["NAME"].'";
		$arDirProperties = array(	
			"title"	=> "'.$iblock_name.'"
		);
		?>
		';

	$filename = $iblock_dir."/index.php";
	$fh = fopen($filename, "w+");
	fwrite($fh, $index);
	fclose($fh);
 
	$filename = $iblock_dir."/index.php";
	$fh = fopen($filename, "w+");
	fwrite($fh, $index);
	fclose($fh);
 
	$filename = $iblock_dir."/.section.php";
	$fh = fopen($filename, "w+");
	fwrite($fh, $section);
	fclose($fh);
	
	$arUrlRewrite = array(); 
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."/urlrewrite.php"))
	{
		include($_SERVER["DOCUMENT_ROOT"]."/urlrewrite.php");
	}

	$sef = $link_to_iblock;
	
	$iblock_type = str_replace("_", "-", $iblock_type);
	$iblock_code = str_replace("_", "-", $iblock_code);
	
	$arNewUrlRewrite = array(
		array(
			"CONDITION"	=>	"#^/".$iblock_type.'/'.$iblock_code."/#",
			"RULE"	=>	"",
			"ID"	=>	"bitrix:catalog",
			"PATH"	=>	 $sef."index.php",
		), 

	);

	foreach ($arNewUrlRewrite as $arUrl)
	{
		if (!in_array($arUrl, $arUrlRewrite))
		{
			CUrlRewriter::Add($arUrl);
		}
	}
}

$wizard =& $this->GetWizard();
$site_id = $wizard->GetVar("siteID");

$sec = new CIBlockSection;

// Count of types IB
$query 	= "SELECT COUNT(*) AS CNT FROM `".$arResult["prefix"]."hierarchy` WHERE `domain_id`=1 AND `rel`=0 AND `type_id`=5 AND `tpl_id`=1";
$count 	= mysql_query($query, $link);
$count = mysql_fetch_assoc($count);

if($left > $count["CNT"])
{	
	// $left =  0;
	$left =  0;
	$right = 10;

	/* Две эти строчки непосредственно завершают шаг и скрипт переходит к следеющему файлу(если он существует) */
	$step += 1;
	$this->content .= $this->ShowHiddenField("step", $step);
}
else
{
	$query 	= "SELECT obj_id AS ID, alt_name AS CODE, id FROM `".$arResult["prefix"]."hierarchy` WHERE `domain_id`=1 AND `rel`=0 AND `type_id`=5 AND `tpl_id`=1 LIMIT {$left}, 5";
	// $query 	= "SELECT obj_id AS ID, alt_name AS CODE, id FROM `".$arResult["prefix"]."hierarchy` WHERE `domain_id`=1 AND `rel`=0 AND `type_id`=5 AND `tpl_id`=1";
	$res 	= mysql_query($query, $link);

	// Цикл по типам ИБ
	while($arItem = mysql_fetch_assoc($res))
	{
		$query = "SELECT obj_id, alt_name AS CODE, is_active, is_visible, id FROM `".$arResult["prefix"]."hierarchy` WHERE id IN
			( SELECT child_id FROM `".$arResult["prefix"]."hierarchy_relations` WHERE `level`=1 AND `rel_id`={$arItem['id']} AND `is_deleted`=0 ) AND `type_id`=5"; // get type_id from cms3_object_types as hierarchy_type_id
		
		// Цикл по Инфоблокам
		$result = mysql_query($query, $link);
		while($arRes = mysql_fetch_assoc($result))
		{
			if ($arItem['CODE'] !== 'barnoe_oborudovanie' && $arRes["CODE"] !== 'miksery')
				continue;
			
			$query = "SELECT name FROM `".$arResult['prefix']."objects` WHERE `id`={$arRes['obj_id']}";
			$queryRes = mysql_query($query, $link);
			$queryRes = mysql_fetch_assoc($queryRes);
			
			$meta = array(); // Meta information for .section.php
		
			$query = "SELECT varchar_val FROM `".$arResult['prefix']."object_content` WHERE `obj_id`={$arRes['obj_id']} AND `field_id`=25";
			$tmpres = mysql_query($query, $link);
			$tmpres = mysql_fetch_assoc($tmpres);
			$description = $tmpres['varchar_val'];
			
			$query = "SELECT varchar_val FROM `".$arResult['prefix']."object_content` WHERE `obj_id`={$arRes['obj_id']} AND `field_id`=24";
			$tmpres = mysql_query($query, $link);
			$tmpres = mysql_fetch_assoc($tmpres);
			$keywords = $tmpres['varchar_val'];
			
			$query = "SELECT varchar_val FROM `".$arResult['prefix']."object_content` WHERE `obj_id`={$arRes['obj_id']} AND `field_id`=23";
			$tmpres = mysql_query($query, $link);
			$tmpres = mysql_fetch_assoc($tmpres);
			$h1 = $tmpres['varchar_val'];
			
			$query = "SELECT varchar_val FROM `".$arResult['prefix']."object_content` WHERE `obj_id`={$arRes['obj_id']} AND `field_id`=22";
			$tmpres = mysql_query($query, $link);
			$tmpres = mysql_fetch_assoc($tmpres);
			$title = $tmpres['varchar_val'];
			
			array_push($meta, $title, $keywords, $description, $h1);
			
			$query = "SELECT text_val FROM `".$arResult['prefix']."object_content` WHERE `obj_id`={$arRes['obj_id']} AND `field_id`=8900";
			$tmpres = mysql_query($query, $link);
			$tmpres = mysql_fetch_assoc($tmpres);
			$descr = $tmpres['text_val'];
			
			$query = "SELECT text_val FROM `".$arResult['prefix']."object_content` WHERE `obj_id`={$arRes['obj_id']} AND `field_id`=9499";
			$tmpres = mysql_query($query, $link);
			$tmpres = mysql_fetch_assoc($tmpres);
			$seo = $tmpres['text_val'];
			
			if (empty($descr)) $descr = $seo;
			
			$is_active = ($arRes['is_active'] == 1 && $arRes['is_visible'] == 1) ? 'Y' : 'N';
			$ibCode = str_replace("_", "-", $arRes["CODE"]);
			$typeIBCode  = str_replace("_", "-", $arItem['CODE']);
			
			$iblock = new CIBlock;
			$rs = CIBlock::GetList(array(), array("CODE" => $arRes["CODE"], "TYPE" => "catalog_".$arItem['CODE']))->GetNext();
			if (empty($rs))
			{
				$tmpAr = CIBlock::GetList(array(), array("CODE" => $arRes["CODE"]))->GetNext();
				if (!empty($tmpAr))
				{
					$ibCode = $typeIBCode.'-'.$arRes["CODE"];
					$arRes["CODE"] = $ibCode;
				}
					
				$arFields = array(
					"SITE_ID" => $site_id,
					"ACTIVE" => $is_active,
					"IBLOCK_TYPE_ID" => "catalog_".$arItem['CODE'],
					"NAME" => $queryRes['name'],
					"CODE" => $ibCode,		
					"SORT" => "100",
					"DESCRIPTION_TYPE" => "HTML",
					"DESCRIPTION" => $descr,
					"LIST_PAGE_URL" => "/".$typeIBCode."/".$ibCode."/",
					"SECTION_PAGE_URL" => "/".$typeIBCode."/".$ibCode."/#SECTION_CODE#/",
					"DETAIL_PAGE_URL" => "/".$typeIBCode."/".$ibCode."/#ELEMENT_CODE#.html",
				);
				
				$id = $iblock->Add($arFields);
				
				$obUserField = new CUserTypeEntity;
							
				$arFields = Array(
					"ENTITY_ID" => "IBLOCK_{$id}_SECTION",
					"FIELD_NAME" => 'UF_TITLE',
					"USER_TYPE_ID" => "string",
					"EDIT_FORM_LABEL" => Array("ru"=>"TITLE", "en"=>"TITLE")
				);
				$obUserField->Add($arFields);
					
				$arFields["FIELD_NAME"] = 'UF_H1';
				$arFields["EDIT_FORM_LABEL"] = Array("ru"=>"H1", "en"=>"H1");
				$obUserField->Add($arFields);
					
				$arFields["FIELD_NAME"] = 'UF_DESCRIPTION';
				$arFields["EDIT_FORM_LABEL"] = Array("ru"=>"DESCRIPTION", "en"=>"DESCRIPTION");
				$obUserField->Add($arFields);
					
				$arFields["FIELD_NAME"] = 'UF_KEYWORDS';
				$arFields["EDIT_FORM_LABEL"] = Array("ru"=>"KEYWORDS", "en"=>"KEYWORDS");
				$obUserField->Add($arFields);
					
				unset($obUserField);
				
				if(!CCatalog::GetList(array(), array('IBLOCK_ID' => $id))->GetNext())
				{
					CCatalog::Add(array( "IBLOCK_ID" => $id,  "YANDEX_EXPORT" => "N",  "SUBSCRIPTION" => "N" ) );			
				}
				CIBlock::SetPermission($id, Array("1"=>"X", "2"=>"R"));
				
				// $r = CIBlock::GetList(array(), array('CODE'=>$arRes["CODE"]))->Fetch();
				
				// $type_dir 	= $_SERVER["DOCUMENT_ROOT"]."/".$arItem['CODE'];
				
				$dir = str_replace("catalog-", "", str_replace("_", "-", $arItem['CODE']));
				$type_dir = $_SERVER["DOCUMENT_ROOT"]."/".$dir;
				
				$dir = str_replace("_", "-", $arRes["CODE"]);
				$iblock_dir = $type_dir.'/'.$dir;
				
				mkdir($iblock_dir, 0777);
				
				createFiles4IB($arItem['CODE'], $arRes["CODE"], $id, $queryRes['name'], $meta);
				// createFiles4IB($arItem['CODE'], $arRes["CODE"], $r['ID'], $queryRes['name'], $meta);
				
				// Get Sections
				$query = "SELECT * FROM `".$arResult['prefix']."hierarchy` WHERE `type_id`=5 AND `rel`={$arRes['id']} AND `is_deleted`=0";
				$queryRes = mysql_query($query, $link);
			
				while( $arI = mysql_fetch_assoc($queryRes) )
				{
					$query = "SELECT name FROM `".$arResult['prefix']."objects` WHERE `id`={$arI['obj_id']}";
					$resu = mysql_query($query, $link);
					$resu = mysql_fetch_assoc($resu);
					
					$isSectionActive = ($arI['is_active'] == 1) ? 'Y' : 'N';
					
					$secCode = str_replace('_', '-', $arI['alt_name']);

					$array = array(
						"IBLOCK_ID" 		=> $id,
						"IBLOCK_SECTION_ID"	=> 0,
						"NAME" 				=> $resu['name'],
						"SORT" 				=> '100',
						"ACTIVE"			=> $isSectionActive,
						'CODE'				=> $secCode,
					);
					
					$secId = $sec->Add($array);
					
					// Set User Fields
					//
					$query = "SELECT varchar_val FROM `".$arResult['prefix']."object_content` WHERE `obj_id`={$arI['obj_id']} AND `field_id`=22";
					$resu = mysql_query($query, $link);
					$resu = mysql_fetch_assoc($resu);
					
					$arFields = Array(
						"UF_TITLE" => $resu['varchar_val']
					);
					$sec->Update($secId, $arFields);
					// -----------------------------
					
					$query = "SELECT varchar_val FROM `".$arResult['prefix']."object_content` WHERE `obj_id`={$arI['obj_id']} AND `field_id`=23";
					$resu = mysql_query($query, $link);
					$resu = mysql_fetch_assoc($resu);
					
					$arFields = Array(
						"UF_H1" => $resu['varchar_val']
					);
					$sec->Update($secId, $arFields);
					// -----------------------------
					
					$query = "SELECT varchar_val FROM `".$arResult['prefix']."object_content` WHERE `obj_id`={$arI['obj_id']} AND `field_id`=24";
					$resu = mysql_query($query, $link);
					$resu = mysql_fetch_assoc($resu);
					
					$arFields = Array(
						"UF_KEYWORDS" => $resu['varchar_val']
					);
					$sec->Update($secId, $arFields);
					// -----------------------------
					
					$query = "SELECT varchar_val FROM `".$arResult['prefix']."object_content` WHERE `obj_id`={$arI['obj_id']} AND `field_id`=25";
					$resu = mysql_query($query, $link);
					$resu = mysql_fetch_assoc($resu);
					
					$arFields = Array(
						"UF_DESCRIPTION" => $resu['varchar_val']
					);
					$sec->Update($secId, $arFields);
					// -----------------------------
					
					// ---------------- Get subsections ---------------------
					$query = "SELECT * FROM `".$arResult['prefix']."hierarchy` WHERE `type_id`=5 AND `rel`={$arI['id']} AND `is_deleted`=0";
					$resSecTmp = mysql_query($query, $link);
					while( $arTmpSec = mysql_fetch_assoc($resSecTmp) )
					{
						$query = "SELECT name FROM `".$arResult['prefix']."objects` WHERE `id`={$arTmpSec['obj_id']}";
						$resuTmp = mysql_query($query, $link);
						$resuTmp = mysql_fetch_assoc($resuTmp);
						
						$isSubSectionActive = ($arTmpSec['is_active'] == 1) ? 'Y' : 'N';

						$arraySubSec = array(
							"IBLOCK_ID" 		=> $id,
							"IBLOCK_SECTION_ID"	=> $secId,
							"NAME" 				=> $resuTmp['name'],
							"SORT" 				=> '100',
							"ACTIVE"			=> $isSubSectionActive,
							'CODE'				=> $arTmpSec['alt_name'],
						);
						
						// print_r($arraySubSec); die();
						
						$subSecId = $sec->Add($arraySubSec);
						
						// Set User Fields
						//
						$query = "SELECT varchar_val FROM `".$arResult['prefix']."object_content` WHERE `obj_id`={$arTmpSec['obj_id']} AND `field_id`=22";
						$resu = mysql_query($query, $link);
						$resu = mysql_fetch_assoc($resu);
						
						$arFields = Array(
							"UF_TITLE" => $resu['varchar_val']
						);
						$sec->Update($subSecId, $arFields);
						// -----------------------------
						
						$query = "SELECT varchar_val FROM `".$arResult['prefix']."object_content` WHERE `obj_id`={$arTmpSec['obj_id']} AND `field_id`=23";
						$resu = mysql_query($query, $link);
						$resu = mysql_fetch_assoc($resu);
						
						$arFields = Array(
							"UF_H1" => $resu['varchar_val']
						);
						$sec->Update($subSecId, $arFields);
						// -----------------------------
						
						$query = "SELECT varchar_val FROM `".$arResult['prefix']."object_content` WHERE `obj_id`={$arTmpSec['obj_id']} AND `field_id`=24";
						$resu = mysql_query($query, $link);
						$resu = mysql_fetch_assoc($resu);
						
						$arFields = Array(
							"UF_KEYWORDS" => $resu['varchar_val']
						);
						$sec->Update($subSecId, $arFields);
						// -----------------------------
						
						$query = "SELECT varchar_val FROM `".$arResult['prefix']."object_content` WHERE `obj_id`={$arTmpSec['obj_id']} AND `field_id`=25";
						$resu = mysql_query($query, $link);
						$resu = mysql_fetch_assoc($resu);
						
						$arFields = Array(
							"UF_DESCRIPTION" => $resu['varchar_val']
						);
						$sec->Update($subSecId, $arFields);
					}
				} // end while( $arI = mysql_fetch_assoc($queryRes) )
				
			} // end if (empty($rs))
		} // end while по Инфоблокам
	} // end while по типам ИБ
	
	// Увеличиваем левую и правую границу
	$left += 5;
	$right += 5;
}

/* Устанавливаем левую и правую границу */
$this->content .= $this->ShowHiddenField("left", $left);
$this->content .= $this->ShowHiddenField("right", $right);

?>