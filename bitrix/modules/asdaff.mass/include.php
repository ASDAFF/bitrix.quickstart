<?
global $DB, $DBType;
define('WDA_MODULE', 'asdaff.mass');
IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	WDA_MODULE,
	array(
		'CWDA_Plugin' => 'classes/general/CWDA_Plugin.php',
		'CWDA_Profile' => 'classes/'.$DBType.'/CWDA_Profile.php',
	)
);

class CWDA {
	
	/**
	 *	Check UTF-8 mode
	 */
	static function IsUtf(){
		return defined('BX_UTF') && BX_UTF===true;
	}
	
	/**
	 *	Debug function
	 */
	static function P($arData, $Return=false) {
		$strResult = "<style type='text/css'>pre {background:none repeat scroll 0 0 #FAFAFA; border-color:#AAB4BE #AAB4BE #AAB4BE #B4B4B4; border-style:dotted dotted dotted solid; border-width:1px 1px 1px 20px; font:normal 11px \"Courier New\",\"Courier\",monospace; margin:10px 0; padding:5px 0 5px 10px; position:relative; text-align:left; white-space:pre-wrap;}</style>";
		if (is_array($arData) && empty($arData)) $arData = "--- Array is empty ---";
		if ($arData===false) $arData = "false"; elseif ($arData===true) $arData = "true";
		$strResult .= "<pre>".print_r($arData,true)."</pre>";
		if ($Return) {
			return $strResult;
		} else {
			print $strResult;
		}
	}
	
	/**
	 *	Add message to log
	 */
	static function Log($Message, $ActionCode=false) {
		if (is_array($Message)) $Message = print_r($Message,1);
		if (strlen($ActionCode)) {
			$Message = '['.$ActionCode.'] '.$Message;
		}
		if (defined(LOG_FILENAME) && strlen(LOG_FILENAME)>0) {
			$LogFileName = LOG_FILENAME;
		} else {
			$LogFileName = $_SERVER['DOCUMENT_ROOT'].'/log_'.COption::GetOptionString('main','server_uniq_id').'.log';
		}
		$Handle = @fopen($LogFileName, 'a+');
		@flock($Handle, LOCK_EX);
		@fwrite($Handle, '['.date('d.m.Y H:i:s').'] '.$Message."\r\n");
		@flock($Handle, LOCK_UN);
		@fclose($Handle);
	}
	
	/**
	 *	Get current mictorime stamp
	 */
	static function GetMicroTime() {
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}
	
	/**
	 *	ConvertCharset recursively
	 */
	static function ConvertCharset($Data, $From='UTF-8', $To='CP1251') {
		global $APPLICATION;
		if (is_array($Data)) {
			foreach($Data as $Key => $Item) {
				$Data[$Key] = self::ConvertCharset($Item, $From, $To);
			}
		} else {
			$Data = $APPLICATION->ConvertCharset($Data, $From, $To);
		}
		return $Data;
	}
	
	/**
	 *	Get list of iblocks
	 */
	static function GetIBlockList($GroupByType=true, $ShowInActive=false) {
		$arResult = array();
		if (CModule::IncludeModule('iblock')) {
			$arFilter = array('CHECK_PERMISSIONS'=>'Y','MIN_PERMISSION'=>'W');
			if ($GroupByType!==false) {
				$resIBlockTypes = CIBlockType::GetList(array(),$arFilter);
				while ($arIBlockType = $resIBlockTypes->GetNext(false,false)) {
					$arIBlockTypeLang = CIBlockType::GetByIDLang($arIBlockType['ID'], LANGUAGE_ID, false);
					$arResult[$arIBlockType['ID']] = array(
						'NAME' => $arIBlockTypeLang['NAME'],
						'ITEMS' => array(),
					);
				}
			}
			if ($ShowInActive!==true) $arFilter['ACTIVE'] = 'Y';
			$resIBlock = CIBlock::GetList(array('SORT'=>'ASC'),$arFilter);
			while ($arIBlock = $resIBlock->GetNext(false,false)) {
				if ($GroupByType!==false) {
					$arResult[$arIBlock['IBLOCK_TYPE_ID']]['ITEMS'][] = $arIBlock;
				} else {
					$arResult[] = $arIBlock;
				}
			}
		}
		foreach(GetModuleEvents(WDA_MODULE, 'OnGetIBlockList', true) as $arEvent) {
			ExecuteModuleEventEx($arEvent, array(&$arResult, $GroupByType, $ShowInActive));
		}
		return $arResult;
	}
	
	/**
	 *	Get types of comparison
	 */
	static function GetComparisonTypes($PropertyType=false, $Operation=false) {
		$arResult = array();
		$arResult['C'] = array( // ACTIVE
			'CH' => array('NAME' => GetMessage('WDA_Y'), 'MASK_PARAM' => '#', 'MASK_VALUE' => 'Y', 'VALUE' => 'N'),
			'NCH' => array('NAME' => GetMessage('WDA_N'), 'MASK_PARAM' => '#', 'MASK_VALUE' => 'N', 'VALUE' => 'N'),
		);
		$arResult['E'] = array( // ELEMENT-LINK
			'EQ' => array('NAME' => GetMessage('WDA_EQ'), 'MASK_PARAM' => '=#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'NOT' => array('NAME' => GetMessage('WDA_NOT'), 'MASK_PARAM' => '!#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'GT' => array('NAME' => GetMessage('WDA_GT'), 'MASK_PARAM' => '>#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'GTE' => array('NAME' => GetMessage('WDA_GTE'), 'MASK_PARAM' => '>=#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'LT' => array('NAME' => GetMessage('WDA_LT'), 'MASK_PARAM' => '<#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'LTE' => array('NAME' => GetMessage('WDA_LTE'), 'MASK_PARAM' => '<=#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'ISS' => array('NAME' => GetMessage('WDA_ISS'), 'MASK_PARAM' => '!#', 'MASK_VALUE' => false, 'VALUE' => 'N', 'NULL' => 'Y'),
			'NISS' => array('NAME' => GetMessage('WDA_NISS'), 'MASK_PARAM' => '#', 'MASK_VALUE' => false, 'VALUE' => 'N', 'NULL' => 'Y'),
		);
		$arResult['F'] = array( // FILE
			'EQ' => array('NAME' => GetMessage('WDA_EQ'), 'MASK_PARAM' => '=#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'NOT' => array('NAME' => GetMessage('WDA_NOT'), 'MASK_PARAM' => '!#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'GT' => array('NAME' => GetMessage('WDA_GT'), 'MASK_PARAM' => '>#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'GTE' => array('NAME' => GetMessage('WDA_GTE'), 'MASK_PARAM' => '>=#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'LT' => array('NAME' => GetMessage('WDA_LT'), 'MASK_PARAM' => '<#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'LTE' => array('NAME' => GetMessage('WDA_LTE'), 'MASK_PARAM' => '<=#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'ISS' => array('NAME' => GetMessage('WDA_ISS'), 'MASK_PARAM' => '!#', 'MASK_VALUE' => false, 'VALUE' => 'N', 'NULL' => 'Y'),
			'NISS' => array('NAME' => GetMessage('WDA_NISS'), 'MASK_PARAM' => '#', 'MASK_VALUE' => false, 'VALUE' => 'N', 'NULL' => 'Y'),
		);
		$arResult['G'] = array( // SECTION-LINK
			'EQ' => array('NAME' => GetMessage('WDA_EQ'), 'MASK_PARAM' => '=#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'NOT' => array('NAME' => GetMessage('WDA_NOT'), 'MASK_PARAM' => '!#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'GT' => array('NAME' => GetMessage('WDA_GT'), 'MASK_PARAM' => '>#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'GTE' => array('NAME' => GetMessage('WDA_GTE'), 'MASK_PARAM' => '>=#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'LT' => array('NAME' => GetMessage('WDA_LT'), 'MASK_PARAM' => '<#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'LTE' => array('NAME' => GetMessage('WDA_LTE'), 'MASK_PARAM' => '<=#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'ISS' => array('NAME' => GetMessage('WDA_ISS'), 'MASK_PARAM' => '!#', 'MASK_VALUE' => false, 'VALUE' => 'N', 'NULL' => 'Y'),
			'NISS' => array('NAME' => GetMessage('WDA_NISS'), 'MASK_PARAM' => '#', 'MASK_VALUE' => false, 'VALUE' => 'N', 'NULL' => 'Y'),
		);
		$arResult['L'] = array( // LIST
			'CH' => array('NAME' => GetMessage('WDA_EQ'), 'MASK_PARAM' => '#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'NCH' => array('NAME' => GetMessage('WDA_NOT'), 'MASK_PARAM' => '!#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
		);
		$arResult['N'] = array( // NUMERIC
			'EQ' => array('NAME' => GetMessage('WDA_EQ'), 'MASK_PARAM' => '=#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'NOT' => array('NAME' => GetMessage('WDA_NOT'), 'MASK_PARAM' => '!#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'GT' => array('NAME' => GetMessage('WDA_GT'), 'MASK_PARAM' => '>#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'GTE' => array('NAME' => GetMessage('WDA_GTE'), 'MASK_PARAM' => '>=#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'LT' => array('NAME' => GetMessage('WDA_LT'), 'MASK_PARAM' => '<#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'LTE' => array('NAME' => GetMessage('WDA_LTE'), 'MASK_PARAM' => '<=#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'ISS' => array('NAME' => GetMessage('WDA_ISS'), 'MASK_PARAM' => '!#', 'MASK_VALUE' => false, 'VALUE' => 'N', 'NULL' => 'Y'),
			'NISS' => array('NAME' => GetMessage('WDA_NISS'), 'MASK_PARAM' => '#', 'MASK_VALUE' => false, 'VALUE' => 'N', 'NULL' => 'Y'),
		);
		$arResult['P'] = array( // PRICES
			'EQ' => array('NAME' => GetMessage('WDA_EQ'), 'MASK_PARAM' => '=#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'NOT' => array('NAME' => GetMessage('WDA_NOT'), 'MASK_PARAM' => '!#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'GT' => array('NAME' => GetMessage('WDA_GT'), 'MASK_PARAM' => '>#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'GTE' => array('NAME' => GetMessage('WDA_GTE'), 'MASK_PARAM' => '>=#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'LT' => array('NAME' => GetMessage('WDA_LT'), 'MASK_PARAM' => '<#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'LTE' => array('NAME' => GetMessage('WDA_LTE'), 'MASK_PARAM' => '<=#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'ISS' => array('NAME' => GetMessage('WDA_ISS'), 'MASK_PARAM' => '!#', 'MASK_VALUE' => false, 'VALUE' => 'N', 'NULL' => 'Y'),
			'NISS' => array('NAME' => GetMessage('WDA_NISS'), 'MASK_PARAM' => '#', 'MASK_VALUE' => false, 'VALUE' => 'N', 'NULL' => 'Y'),
		);
		$arResult['S'] = array( // STRING
			'EQ' => array('NAME' => GetMessage('WDA_EQ'), 'MASK_PARAM' => '=#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'NOT' => array('NAME' => GetMessage('WDA_NOT'), 'MASK_PARAM' => '!#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'ISS' => array('NAME' => GetMessage('WDA_ISS'), 'MASK_PARAM' => '!#', 'MASK_VALUE' => false, 'VALUE' => 'N', 'NULL' => 'Y'),
			'NISS' => array('NAME' => GetMessage('WDA_NISS'), 'MASK_PARAM' => '#', 'MASK_VALUE' => false, 'VALUE' => 'N', 'NULL' => 'Y'),
			'CON' => array('NAME' => GetMessage('WDA_CON'), 'MASK_PARAM' => '%#', 'MASK_VALUE' => '#', 'VALUE' => 'Y'),
			'NCON' => array('NAME' => GetMessage('WDA_NCON'), 'MASK_PARAM' => '!%#', 'MASK_VALUE' => '#', 'VALUE' => 'Y'),
			'BEG' => array('NAME' => GetMessage('WDA_BEG'), 'MASK_PARAM' => '#', 'MASK_VALUE' => '#%', 'VALUE' => 'Y'),
			'NBEG' => array('NAME' => GetMessage('WDA_NBEG'), 'MASK_PARAM' => '!#', 'MASK_VALUE' => '#%', 'VALUE' => 'Y'),
			'END' => array('NAME' => GetMessage('WDA_END'), 'MASK_PARAM' => '#', 'MASK_VALUE' => '%#', 'VALUE' => 'Y'),
			'NEND' => array('NAME' => GetMessage('WDA_NEND'), 'MASK_PARAM' => '!#', 'MASK_VALUE' => '%#', 'VALUE' => 'Y'),
		);
		$arResult['S:DATETIME'] = array( // DATETIME
			'EQ' => array('NAME' => GetMessage('WDA_EQ'), 'MASK_PARAM' => '=#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'NOT' => array('NAME' => GetMessage('WDA_NOT'), 'MASK_PARAM' => '!#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'GT' => array('NAME' => GetMessage('WDA_GT_DATE'), 'MASK_PARAM' => '>#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'LT' => array('NAME' => GetMessage('WDA_LT_DATE'), 'MASK_PARAM' => '<#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'ISS' => array('NAME' => GetMessage('WDA_ISS'), 'MASK_PARAM' => '!#', 'MASK_VALUE' => false, 'VALUE' => 'N', 'NULL' => 'Y'),
			'NISS' => array('NAME' => GetMessage('WDA_NISS'), 'MASK_PARAM' => '#', 'MASK_VALUE' => false, 'VALUE' => 'N', 'NULL' => 'Y'),
		);
		$arResult['T'] = array( // PREVIEW_TEXT_TYPE, DETAIL_TEXT_TYPE
			'EQ' => array('NAME' => GetMessage('WDA_EQ'), 'MASK_PARAM' => '#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
			'NOT' => array('NAME' => GetMessage('WDA_NOT'), 'MASK_PARAM' => '!#', 'MASK_VALUE' => '', 'VALUE' => 'Y'),
		);
		$arResult['Y'] = array( // ACTIVE_DATE, SECTION_GLOBAL_ACTIVE
			'Y' => array('NAME' => GetMessage('WDA_Y'), 'MASK_PARAM' => '#', 'MASK_VALUE' => 'Y', 'VALUE' => 'N', 'NULL' => 'N',),
		);
		$arResultTmp = array();
		if ($PropertyType!==false) {
			$PropertyType = ToUpper($PropertyType);
			$arPropertyType = explode(':',$PropertyType);
			$PropertyType1 = $arPropertyType[0];
			$PropertyType2 = $arPropertyType[1];
			if (strlen($PropertyType)) {
				if (isset($arResult[$PropertyType])) {
					$arResultTmp = $arResult[$PropertyType];
				} elseif (isset($arResult[$PropertyType1])) {
					$arResultTmp = $arResult[$PropertyType1];
				}
				if ($Operation!==false) {
					$Operation = ToUpper($Operation);
					if (isset($arResultTmp[$Operation])) {
						$arResultTmp = $arResultTmp[$Operation];
					}
				}
				return $arResultTmp;
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get types of comparison (JSON format)
	 */
	static function GetComparisonTypesJSON($PropertyType=false, $Operation=false) {
		$arComparisonTypes = self::GetComparisonTypes($PropertyType=false, $Operation=false);
		$bUTF = self::IsUtf();
		if (!$bUTF) {
			$arComparisonTypes = self::ConvertCharset($arComparisonTypes, 'CP1251', 'UTF-8');
		}
		return json_encode($arComparisonTypes);
	}
	
	/**
	 *	Get sections
	 */
	static function GetSections($IBlockID) {
		$arResult = array();
		if (CModule::IncludeModule('iblock') && $IBlockID>0) {
			$bUTF = self::IsUtf();
			$resSections = CIBlockSection::GetList(array('LEFT_MARGIN'=>'ASC'),array('IBLOCK_ID'=>$IBlockID),false,array('ID','NAME','IBLOCK_SECTION_ID','DEPTH_LEVEL'));
			while ($arSection = $resSections->GetNext(false,false)) {
				if (!$bUTF) {
					$arSection = self::ConvertCharset($arSection, 'CP1251', 'UTF-8');
				}
				$arResult[] = $arSection;
			}
		}
		return $arResult;
	}
	
	static function GetFieldName($Code) {
		$bUTF = self::IsUtf();
		$strResult = GetMessage('IBLOCK_FIELD_'.$Code);
		if (!$bUTF) {
			$strResult = self::ConvertCharset($strResult, 'CP1251', 'UTF-8');
		}
		return $strResult;
	}
	
	static function GetFields() {
		$bShowCode = false;
		$arResult = array();
		$arResultTmp = array();
		$arResultTmp[] = array('WDA_TYPE'=>'N:INT', 				'WDA_CODE'=>'ID');
		$arResultTmp[] = array('WDA_TYPE'=>'S', 						'WDA_CODE'=>'NAME');
		$arResultTmp[] = array('WDA_TYPE'=>'S', 						'WDA_CODE'=>'CODE');
		$arResultTmp[] = array('WDA_TYPE'=>'C', 						'WDA_CODE'=>'ACTIVE');
		$arResultTmp[] = array('WDA_TYPE'=>'Y', 						'WDA_CODE'=>'ACTIVE_DATE');
		$arResultTmp[] = array('WDA_TYPE'=>'Y', 						'WDA_CODE'=>'SECTION_GLOBAL_ACTIVE');
		$arResultTmp[] = array('WDA_TYPE'=>'S', 						'WDA_CODE'=>'XML_ID');
		$arResultTmp[] = array('WDA_TYPE'=>'N:INT', 				'WDA_CODE'=>'SORT');
		$arResultTmp[] = array('WDA_TYPE'=>'S:HTML', 				'WDA_CODE'=>'PREVIEW_TEXT');
		$arResultTmp[] = array('WDA_TYPE'=>'T', 						'WDA_CODE'=>'PREVIEW_TEXT_TYPE');
		$arResultTmp[] = array('WDA_TYPE'=>'F', 						'WDA_CODE'=>'PREVIEW_PICTURE');
		$arResultTmp[] = array('WDA_TYPE'=>'S:HTML', 				'WDA_CODE'=>'DETAIL_TEXT');
		$arResultTmp[] = array('WDA_TYPE'=>'T', 						'WDA_CODE'=>'DETAIL_TEXT_TYPE');
		$arResultTmp[] = array('WDA_TYPE'=>'F', 						'WDA_CODE'=>'DETAIL_PICTURE');
		$arResultTmp[] = array('WDA_TYPE'=>'S:DateTime', 		'WDA_CODE'=>'DATE_ACTIVE_FROM');
		$arResultTmp[] = array('WDA_TYPE'=>'S:DateTime', 		'WDA_CODE'=>'DATE_ACTIVE_TO');
		$arResultTmp[] = array('WDA_TYPE'=>'N:INT', 				'WDA_CODE'=>'SHOW_COUNTER');
		$arResultTmp[] = array('WDA_TYPE'=>'S', 						'WDA_CODE'=>'TAGS');
		$arResultTmp[] = array('WDA_TYPE'=>'S:DateTime', 		'WDA_CODE'=>'DATE_CREATE');
		$arResultTmp[] = array('WDA_TYPE'=>'S:UserID', 			'WDA_CODE'=>'CREATED_BY');
		$arResultTmp[] = array('WDA_TYPE'=>'S:DateTime', 		'WDA_CODE'=>'TIMESTAMP_X');
		$arResultTmp[] = array('WDA_TYPE'=>'S:UserID', 			'WDA_CODE'=>'MODIFIED_BY');
		foreach($arResultTmp as $Key => $arItem) {
			$Code = self::GetFieldName($arItem['WDA_CODE']);
			$arItem['WDA_NAME_FULL'] = ($bShowCode?'['.$arItem['WDA_CODE'].'] ':'').$Code;
			$arItem['WDA_NAME'] = ($bShowCode?$arItem['WDA_CODE']:'').$Code;
			$arItem['WDA_GROUP'] = 'FIELDS';
			$arResult[$arItem['WDA_CODE']] = $arItem;
		}
		return $arResult;
	}
	
	static function GetProperties($IBlockID) {
		$bShowCode = true;
		$arResult = array();
		if (CModule::IncludeModule('iblock')) {
			$bUTF = self::IsUtf();
			$resProperty = CIBlockProperty::GetList(array('SORT'=>'ASC'),array('IBLOCK_ID'=>$IBlockID,'ACTIVE'=>'Y'));
			while ($arProperty = $resProperty->GetNext(false,false)) {
				if (!$bUTF) {
					$arProperty = self::ConvertCharset($arProperty, 'CP1251', 'UTF-8');
				}
				$arProperty['WDA_TYPE'] = $arProperty['PROPERTY_TYPE'].(strlen($arProperty['USER_TYPE'])?':'.ToUpper($arProperty['USER_TYPE']):'');
				$arProperty['WDA_CODE'] = 'PROPERTY_'.$arProperty['ID'];
				$strCode = '';
				if ($bShowCode) {
					$strCode = ' ['.(strlen($arProperty['CODE'])?ToUpper($arProperty['CODE']):$arProperty['ID']).', '.$arProperty['PROPERTY_TYPE'].(strlen($arProperty['USER_TYPE'])?':'.$arProperty['USER_TYPE']:'').($arProperty['MULTIPLE']=='Y'?', +':'').']';
				}
				$arProperty['WDA_NAME_FULL'] = $arProperty['NAME'].$strCode;
				$arProperty['WDA_NAME'] = $arProperty['NAME'];
				$arProperty['WDA_GROUP'] = 'PROPERTIES';
				$arResult[$arProperty['WDA_CODE']] = $arProperty;
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get list of prices
	 */
	static function GetPriceTypeList($IBlockID=false) {
		$arResult = array();
		if (CModule::IncludeModule('catalog')) {
			$bIsCatalog = false;
			if ($IBlockID>0) {
				$bIsCatalog = self::IsCatalog($IBlockID);
			}
			if ($bIsCatalog || $IBlockID===false) {
				$resPrices = CCatalogGroup::GetList(array('SORT'=>'ASC'));
				$bUTF = self::IsUtf();
				while ($arPrice = $resPrices->GetNext(false,false)) {
					$arItem = array(
						'WDA_TYPE' => 'P',
						'WDA_CODE' => 'CATALOG_PRICE_'.$arPrice['ID'],
						'WDA_NAME_FULL' => '['.$arPrice['NAME'].'] '.$arPrice['NAME_LANG'],
						'WDA_NAME' => $arPrice['NAME_LANG'],
						'WDA_GROUP' => 'PRICES'
					);
					if (!$bUTF) {
						$arPrice = self::ConvertCharset($arPrice, 'CP1251', 'UTF-8');
						$arItem = self::ConvertCharset($arItem, 'CP1251', 'UTF-8');
					}
					$arResult['CATALOG_PRICE_'.$arPrice['ID']] = array_merge($arPrice,$arItem);
				}
			}
		}
		return $arResult;
	}
	
	
	/**
	 *	Get catalog fields
	 */
	static function GetCatalogFields($IBlockID) {
		$arResult = array();
		if (CModule::IncludeModule('catalog') && $IBlockID>0 && self::IsCatalog($IBlockID)) {
			$arResult[] = array(
				'WDA_TYPE' => 'N:INT',
				'WDA_CODE' => 'CATALOG_QUANTITY',
				'WDA_NAME_FULL' => GetMessage('WDA_CATALOG_QUANTITY'),
				'WDA_NAME' => GetMessage('WDA_CATALOG_QUANTITY'),
				'WDA_GROUP' => 'CATALOG'
			);
			$arResult[] = array(
				'WDA_TYPE' => 'N:INT',
				'WDA_CODE' => 'CATALOG_WEIGHT',
				'WDA_NAME_FULL' => GetMessage('WDA_CATALOG_WEIGHT'),
				'WDA_NAME' => GetMessage('WDA_CATALOG_WEIGHT'),
				'WDA_GROUP' => 'CATALOG'
			);
			$arResult[] = array(
				'WDA_TYPE' => 'C',
				'WDA_CODE' => 'CATALOG_AVAILABLE',
				'WDA_NAME_FULL' => GetMessage('WDA_CATALOG_AVAILABLE'),
				'WDA_NAME' => GetMessage('WDA_CATALOG_AVAILABLE'),
				'WDA_GROUP' => 'CATALOG'
			);
			$arResult[] = array(
				'WDA_TYPE' => 'N',
				'WDA_CODE' => 'CATALOG_PURCHASING_PRICE',
				'WDA_NAME_FULL' => GetMessage('WDA_CATALOG_PURCHASING_PRICE'),
				'WDA_NAME' => GetMessage('WDA_CATALOG_PURCHASING_PRICE'),
				'WDA_GROUP' => 'CATALOG'
			);
			$bUTF = self::IsUtf();
			if (!$bUTF) {
				foreach($arResult as $Key => $arItem) {
					$arResult[$Key] = self::ConvertCharset($arItem, 'CP1251', 'UTF-8');
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get all fields
	 */
	static function GetAllFields($IBlockID) {
		$arResult = array();
		if (CModule::IncludeModule('iblock') && $IBlockID>0) {
			// Get fields
			$arFields = self::GetFields();
			// Get properties
			$arProps = self::GetProperties($IBlockID);
			// Get catalog prices
			$arPrices = self::GetPriceTypeList($IBlockID);
			// Get catalog fields
			$arCatalogFields = self::GetCatalogFields($IBlockID);
			// Merge
			$arResult = array_merge($arFields, $arProps, $arPrices, $arCatalogFields);
		}
		return $arResult;
	}
	
	/**
	 *	Prepare (before build) filter from $_POST
	 */
	static function CollectFilter($Fields, $Equals, $Values){
		$arResult = array();
		if (is_array($Fields) && is_array($Equals) && is_array($Values) && count($Fields)===count($Equals) && count($Fields)===count($Values)) {
			foreach($Fields as $Key => $Value){
				$arItem = array(
					'PARAM' => $Fields[$Key],
					'EQUAL' => $Equals[$Key],
					'VALUE' => preg_replace('/{html_([a-z]+)}/i','&$1;',$Values[$Key]),
				);
				$arResult[] = $arItem;
				if (!self::IsUtf()) {
					foreach($arResult as $Key => $arItem) {
						$arResult[$Key]['VALUE'] = self::ConvertCharset($arItem['VALUE']);
					}
				}
			}
		}
		return $arResult;
	}
	
	static function GetFilterItem(&$Param, &$Value, $arComparison) {
		if (is_array($arComparison) && !empty($arComparison)) {
			if ($arComparison['MASK_PARAM']=='') {
				$arComparison['MASK_PARAM'] = '#';
			}
			if ($arComparison['MASK_VALUE']=='') {
				$arComparison['MASK_VALUE'] = '#';
			}
			$Param = str_replace('#',$Param,$arComparison['MASK_PARAM']);
			if ($arComparison['NULL']=='Y') {
				$Value = false;
			} else {
				$Value = str_replace('#',$Value,$arComparison['MASK_VALUE']);
			}
		}
	}
	
	/**
	 *	Build filter
	 */
	static function BuildFilter($IBlockID, $Sections, $IncludeSubsections, $FilterParams, $Fields) {
		$arResult = array();
		$arResult['IBLOCK_ID'] = $IBlockID;
		if(is_array($Sections) && count($Sections)===1 && isset($Sections[0]) && $Sections[0]==='') {
			$Sections = false;
		}
		if (is_array($Sections) && !empty($Sections)) {
			if (count($Sections)===1) {
				foreach($Sections as $SectionID) {
					if ($SectionID>0) {
						$arResult['SECTION_ID'] = $SectionID;
					}
				}
			} else {
				$arResult['SECTION_ID'] = $Sections;
			}
		}
		if ($IncludeSubsections) {
			$arResult['INCLUDE_SUBSECTIONS'] = 'Y';
		} else {
			$arResult['INCLUDE_SUBSECTIONS'] = 'N';
			if(empty($Sections)) {
				$arResult['SECTION_ID'] = false;
			}
		}
		if (is_array($FilterParams)) {
			foreach($FilterParams as $arItem) {
				$Param = $arItem['PARAM'];
				$Equal = $arItem['EQUAL'];
				$Value = $arItem['VALUE'];
				foreach($Fields as $arField) {
					if ($arField['WDA_CODE']==$Param) {
						$PropType = $arField['WDA_TYPE'];
						$arComparison = self::GetComparisonTypes($PropType, $arItem['EQUAL']);
						$FilterKey = $arField['WDA_CODE'];
						$Key = $arItem['PARAM'];
						$Value = $arItem['VALUE'];
						$Value = str_replace(array(
							'\n',
							'\r',
							'\t',
						),array(
							"\n",
							"\r",
							"\t",
						),$Value);
						self::GetFilterItem($Key, $Value, $arComparison);
						if(isset($arResult[$Key])) {
							if(!is_array($arResult[$Key])) {
								$arResult[$Key] = array($arResult[$Key]);
							}
							$arResult[$Key][] = $Value;
						} else {
							$arResult[$Key] = $Value;
						}
					}
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get property enums for type "List"
	 */
	static function GetPropertyEnums($IBlockID, $PropertyID) {
		$arResult = array();
		if (CModule::IncludeModule('iblock')) {
			$bUTF = self::IsUtf();
			$resPropertyEnums = CIBlockPropertyEnum::GetList(array('SORT'=>'ASC'),array('IBLOCK_ID'=>$IBlockID,'PROPERTY_ID'=>$PropertyID));
			while ($arPropertyEnum = $resPropertyEnums->GetNext(false,false)) {
				if (!$bUTF) {
					$arPropertyEnum = self::ConvertCharset($arPropertyEnum, 'CP1251', 'UTF-8');
				}
				$arResult[$arPropertyEnum['ID']] = $arPropertyEnum;
			}
		}
		return $arResult;
	}
	
	/**
	 *	Get groups (FIELDS, PROPERTIES, PRICE, CATALOG)
	 */
	static function GetFilterFieldsGroups() {
		$arResult = array(
			'FIELDS' => array('NAME'=>GetMessage('WDA_GROUP_FIELDS'),'SORT'=>'10'),
			'PROPERTIES' => array('NAME'=>GetMessage('WDA_GROUP_PROPERTIES'),'SORT'=>'20'),
			'PRICES' => array('NAME'=>GetMessage('WDA_GROUP_PRICES'),'SORT'=>'30'),
			'CATALOG' => array('NAME'=>GetMessage('WDA_GROUP_CATALOG'),'SORT'=>'40'),
		);
		$bUTF = self::IsUtf();
		if (!$bUTF) {
			$arResult = self::ConvertCharset($arResult, 'CP1251', 'UTF-8');
		}
		return $arResult;
	}
	
	/**
	 *	Get approximately count
	 */
	static function GetApproximately($Count) {
		$Count = IntVal($Count);
		if ($Count>=500000) {
			$Count = (round($Count/100000)/10).'m';
		} elseif ($Count>500) {
			$Count = (round($Count/100)/10).'k';
		}
		return $Count;
	}
	
	/**
	 *	Check if IBlock is catalog
	 */
	static function IsCatalog($IBlockID){
		if (CModule::IncludeModule('catalog')) {
			$arCatalog = CCatalog::GetByID($IBlockID);
			if(is_array($arCatalog) && $arCatalog['IBLOCK_ID']==$IBlockID) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 *	Get actions list
	 */
	static function GetActionsList() {
		$arResult = array();
		$ActionsPath = BX_ROOT.'/modules/'.WDA_MODULE.'/include/actions/';
		if (is_dir($_SERVER['DOCUMENT_ROOT'].$ActionsPath)) {
			$Handle = opendir($_SERVER['DOCUMENT_ROOT'].$ActionsPath);
			while (($File = readdir($Handle))!==false) {
				if ($File != '.' && $File != '..') {
					if (is_file($_SERVER['DOCUMENT_ROOT'].$ActionsPath.$File)) {
						$arPathInfo = pathinfo($File);
						if (ToUpper($arPathInfo['extension'])=='PHP') {
							require_once($_SERVER['DOCUMENT_ROOT'].$ActionsPath.$File);
						}
					}
				}
			}
			closedir($Handle);
		}
		foreach(GetModuleEvents(WDA_MODULE, 'GetActionsList', true) as $arEvent) { // Compatibility
			ExecuteModuleEventEx($arEvent);
		}
		foreach(GetModuleEvents(WDA_MODULE, 'OnGetActionsList', true) as $arEvent) {
			ExecuteModuleEventEx($arEvent);
		}
		$arDeclaredClasses = get_declared_classes();
		foreach($arDeclaredClasses as $ClassName) {
			if (stripos($ClassName,'CWDA_')===0 && !in_array($ClassName,array('CWDA','CWDA_Plugin')) && method_exists($ClassName,'WDA_PLUGIN')) {
				$Group = $ClassName::GetGroup();
				$Code = $ClassName::GetCode();
				$Name = $ClassName::GetName();
				$arResult[$ClassName] = array(
					'GROUP' => $Group,
					'CLASS' => $ClassName,
					'NAME' => $Name,
					'CODE' => $Code,
				);
			}
		}
		uasort($arResult, function ($a, $b) {
			return strnatcmp($a['NAME'],$b['NAME']);
		});
		return $arResult;
	}
	
	/**
	 *	Get actions groups
	 */
	function GetActionsGroup() {
		$arResult = array(
			'GENERAL' => GetMessage('WDA_GROUP_GENERAL'),
			'IMAGES' => GetMessage('WDA_GROUP_IMAGES'),
			'PRICES' => GetMessage('WDA_GROUP_PRICES'),
			'OTHERS' => GetMessage('WDA_GROUP_OTHERS'),
		);
		foreach(GetModuleEvents(WDA_MODULE, 'OnGetActionsGroup', true) as $arEvent) {
			ExecuteModuleEventEx($arEvent, array(&$arResult));
		}
		return $arResult;
	}

	/**
	 *	Get count of filtered items
	 */
	function GetCount($Filter) {
		if (CModule::IncludeModule('iblock')) {
			return IntVal(CIBlockElement::GetList(array('ID'=>'ASC'),$Filter,array()));
		}
		return false;
	}
	
	/**
	 *	Get single action
	 */
	function GetAction($Code,$arActions=false) {
		if ($arActions===false) {
			$arActions = self::GetActionsList();
		}
		if (is_array($arActions)) {
			foreach($arActions as $arAction){
				if($arAction['CODE']==$Code) {
					return $arAction;
				}
			}
		}
		return false;
	}
	
	function Process($Data, $Params){
		$Filter = $Data['FILTER'];
		$Action = $Data['ACTION'];
		$MaxTime = IntVal($Data['MAX_TIME']);
		if ($MaxTime==0) {
			$MaxTime = 10;
		}
		if (!is_array($Action)) {
			return 0;
		}
		if (!is_array($Params)) {
			$Params = array();
		}
		$TimeStart = self::GetMicroTime();
		if (CModule::IncludeModule('iblock')) {
			$Class = $Action['CLASS'];
			$SessLastID = &$_SESSION['WDA_LAST_ID_'.$Action['CODE']];
			$resItems = CIBlockElement::GetList(array('ID'=>'ASC'),$Filter,false,false,array('ID'));
			while ($arItem = $resItems->GetNext()) {
				if ($SessLastID>0 && $arItem['ID']<=$SessLastID) {
					continue;
				}
				$TimeCurrent = self::GetMicroTime();
				if ($TimeCurrent-$TimeStart>=$MaxTime) {
					return 1;
				}
				$arElement = CWDA::GetElementByID($arItem['ID']);
				$bResult = $Class::Process($arItem['ID'], $arElement, $Params);
				$_SESSION['WDA_DONE_'.$Action['CODE']]++;
				if ($bResult) {
					$_SESSION['WDA_SUCCEED_'.$Action['CODE']]++;
				} else {
					$_SESSION['WDA_FAILED_'.$Action['CODE']]++;
				}
				unset($_SESSION['WDA_FIRST']);
				$SessLastID = $arItem['ID'];
			}
			unset($SessLastID);
			return 2;
		}
		return 0;
	}
	
	/**
	 *	Get element array by element id
	 */
	function GetElementByID($ElementID) {
		$arResult = array();
		$arPrices = self::GetPriceTypeList();
		$arSelect = array('*');
		foreach($arPrices as $arPrice) {
			$arSelect[] = 'CATALOG_GROUP_'.$arPrice['ID'];
		}
		$resElement = CIBlockElement::GetList(array('ID'=>'ASC'),array('ID'=>$ElementID),false,array('nTopCount'=>'1'),$arSelect);
		if ($obElement = $resElement->GetNextElement()) {
			$arResult = $obElement->GetFields();
			$arResult['PROPERTIES'] = $obElement->GetProperties();
		}
		return $arResult;
	}
	
	function IsField($Code) {
		$arFields = array('ID','CODE','NAME','ACTIVE','ACTIVE_DATE','SECTION_GLOBAL_ACTIVE','XML_ID','SORT','PREVIEW_TEXT','PREVIEW_TEXT_TYPE','PREVIEW_PICTURE','DETAIL_TEXT','DETAIL_TEXT_TYPE','DETAIL_PICTURE','DATE_ACTIVE_FROM','DATE_ACTIVE_TO','SHOW_COUNTER','DATE_CREATE','CREATED_BY','TIMESTAMP_X','MODIFIED_BY');
		return in_array(ToUpper($Code),$arFields);
	}
	
	function IsProperty($Code) {
		if (preg_match('#^PROPERTY_(\d+)$#',$Code,$M)) {
			return $M[1];
		}
		return false;
	}
	
	function IsPrice($Code) {
		if (preg_match('#^CATALOG_PRICE_(\d+)$#i',$Code,$M)) {
			return $M[1];
		}
		return false;
	}
	
	function IsCatalogField($Code) {
		if (preg_match('#^CATALOG_[A-z0-9_]+$#i',$Code,$M)) {
			return true;
		}
		return false;
	}
	
	function GetPropertyFromArrayById($Properties, $PropertyID) {
		$arResult = array();
		if(is_array($Properties) && $PropertyID>0){
			foreach($Properties as $arProperty) {
				if ($arProperty['ID']==$PropertyID) {
					$arResult = $arProperty;
					break;
				}
			}
		}
		return $arResult;
	}
	
	/**
	 *	Set catalog price for product
	 */
	function SetProductPrice($ProductID, $PriceID, $PriceValue, $Currency='RUB', $ExtraID=false) {
		$bResult = false;
		foreach(GetModuleEvents(WDA_MODULE, 'OnBeforeSetProductPrice', true) as $arEvent) {
			if (!ExecuteModuleEventEx($arEvent, array($ProductID, $PriceID, &$PriceValue, &$Currency))) {
				return false;
			}
		}
		$arProductFields = array(
			'ID' => $ProductID,
		);
		CCatalogProduct::Add($arProductFields);
		$arPriceFields = array(
			'PRODUCT_ID' => $ProductID,
			'CATALOG_GROUP_ID' => $PriceID,
			'PRICE' => $PriceValue,
			'CURRENCY' => $Currency,
			'EXTRA_ID' => false,
		);
		$bRecalc = false;
		if(is_numeric($ExtraID) && $ExtraID>0) {
			$arPriceFields['EXTRA_ID'] = $ExtraID;
			$bRecalc = true;
			unset($arPriceFields['PRICE']);
		}
		$resPrice = CPrice::GetList(array(),array('PRODUCT_ID'=>$ProductID,'CATALOG_GROUP_ID'=>$PriceID));
		if ($arPrice = $resPrice->GetNext(false,false)) {
			$bResult = CPrice::Update($arPrice['ID'], $arPriceFields, $bRecalc);
		} else {
			$bResult = CPrice::Add($arPriceFields, $bRecalc)>0;
		}
		foreach(GetModuleEvents(WDA_MODULE, 'OnAfterSetProductPrice', true) as $arEvent) {
			ExecuteModuleEventEx($arEvent, array($ProductID, $PriceID, $PriceValue, $Currency));
		}
		return $bResult;
	}
	
	/**
	 *	Set quantity at a one store
	 */
	function SetProductStoreQuantity($ProductID, $StoreID, $Quantity) {
		$bResult = false;
		foreach(GetModuleEvents(WDA_MODULE, 'OnBeforeSetProductStoreQuantity', true) as $arEvent) {
			if (!ExecuteModuleEventEx($arEvent, array($ProductID, $StoreID, &$Quantity))) {
				return false;
			}
		}
		$arStoreFields = array(
			'PRODUCT_ID' => $ProductID,
			'AMOUNT' => $Quantity,
			'STORE_ID' => $StoreID,
		);
		$resItem = CCatalogStoreProduct::GetList(array(),array('STORE_ID'=>$StoreID,'PRODUCT_ID'=>$ProductID),false,false,array('ID'));
		if ($arItem = $resItem->GetNext(false,false)) {
			$bResult = CCatalogStoreProduct::Update($arItem['ID'], $arStoreFields)>0;
		} else {
			$bResult = CCatalogStoreProduct::Add($arStoreFields)>0;
		}
		foreach(GetModuleEvents(WDA_MODULE, 'OnAfterSetProductStoreQuantity', true) as $arEvent) {
			ExecuteModuleEventEx($arEvent, array($ProductID, $StoreID, $Quantity));
		}
		return $bResult;
	}
	
	/**
	 *	Get list of stores
	 */
	function GetStoresList() {
		$arResult = array();
		if (CModule::IncludeModule('catalog') && class_exists('CCatalogStore')) {
			$resStores = CCatalogStore::GetList(array('SORT'=>'ASC'));
			while ($arStore = $resStores->GetNext(false,false)) {
				$arItem = array(
					'WDA_CODE' => 'CATALOG_STORE_'.$arStore['ID'],
					'WDA_NAME_FULL' => '['.$arStore['ID'].'] '.$arStore['TITLE'],
					'WDA_NAME' => $arStore['TITLE'],
				);
				$arResult[] = $arItem;
			}
		}
		return $arResult;
	}
	
	function RoundEx($Value, $Exp) {
		$Pow = Pow(10,$Exp);
		return (round($Value/$Pow)*$Pow);
	}
	
	function GetCurrencyList() {
		$arResult = array();
		if (CModule::IncludeModule('currency')) {
			$resCurrency = CCurrency::GetList($by='SORT', $order='ASC', LANGUAGE_ID);
			while ($arCurrency = $resCurrency->GetNext(false,false)) {
				$arCurrency['IS_BASE'] = FloatVal($arCurrency['AMOUNT'])==1 ? true: false;
				if (isset($arCurrency['DEAULT']) && !isset($arCurrency['DEFAULT'])) {
					$arCurrency['DEFAULT'] = $arCurrency['DEAULT'];
					unset($arCurrency['DEAULT']);
				}
				if (!self::IsUtf()) {
					$arCurrency = self::ConvertCharset($arCurrency, 'CP1251', 'UTF-8');
				}
				$arResult[ToUpper($arCurrency['CURRENCY'])] = $arCurrency;
			}
			foreach(GetModuleEvents(WDA_MODULE, 'OnGetCurrencyList', true) as $arEvent) {
				ExecuteModuleEventEx($arEvent, array(&$arResult));
			}
		}
		return $arResult;
	}
	
	function GetVatList() {
		$arResult = array(array('NAME'=>GetMessage('WDA_CATALOG_VAT_EMPTY')));
		if (CModule::IncludeModule('catalog')) {
			$resVat = CCatalogVat::GetList();
			while ($arVat = $resVat->GetNext(false,false)) {
				$arResult[$arVat['ID']] = $arVat;
			}
		}
		return $arResult;
	}
	
	function GetMeasureList() {
		$arResult = array();
		if (CModule::IncludeModule('catalog')) {
			$resMeasure = CCatalogMeasure::GetList(array(),array());
			while ($arMeasure = $resMeasure->GetNext(false,false)) {
				$arResult[$arMeasure['ID']] = $arMeasure;
			}
		}
		return $arResult;
	}
	
	function ShowHint($Text) {
		$Code = ToLower(RandString(12));
		$Text = str_replace('"','\"',$Text);
		return '<span id="hint_'.$Code.'"></span><script>BX.hint_replace(BX("hint_'.$Code.'"), "'.$Text.'");</script>';
	}
	
	/**
	 *	Замена всем разделителей пути на системные
	 */
	public static function ReplaceDirectorySeparators($Path){
		return str_replace(array('/','\\'),DIRECTORY_SEPARATOR,$Path);
	}
	
	/**
	 *	-Проверка корректности кодировки
	 */
	public static function WdaCheckCli() {
		$bSuccess = true;
		// Get php.exe
		if(DIRECTORY_SEPARATOR=='/') {
			exec('which php',$Result);
		} else {
			exec('where php',$Result);
		}
		$PhpExe = false;
		if(is_array($Result) && !empty($Result[0])){
			$PhpExe = $Result[0];
		}
		unset($Result);
		$PhpExe = CWDA::ReplaceDirectorySeparators($PhpExe);
		// Get php.ini
		$PhpIni = false;
		$ModulePath = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.WDA_MODULE;
		if(is_file($ModulePath.'/php.ini')) {
			$PhpIni = $ModulePath.'/php.ini';
		}
		$PhpIni = CWDA::ReplaceDirectorySeparators($PhpIni);
		// Get check file
		$CheckFile = false;
		if(is_file($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.WDA_MODULE.'/include/cli_check.php')) {
			$CheckFile = $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.WDA_MODULE.'/include/cli_check.php';
		}
		$CheckFile = CWDA::ReplaceDirectorySeparators($CheckFile);
		//
		if(is_file($PhpExe) && is_file($CheckFile)) {
			if(strpos($PhpExe,' ')!==false) {
				$PhpExe = '"'.$PhpExe.'"';
			}
			$Command = $PhpExe;
			if(is_file($PhpIni)) {
				if(strpos($PhpIni,' ')!==false) {
					$PhpIni = '"'.$PhpIni.'"';
				}
				$Command .= ' -c '.$PhpIni;
			}
			if(strpos($CheckFile,' ')!==false) {
				$PhpIni = '"'.$CheckFile.'"';
			}
			$Command .= ' -f '.$CheckFile;
			exec($Command,$Result);
			if(is_array($Result)) {
				$Result = array_filter($Result);
			}
			if(is_array($Result)){
				if(count($Result)==1 && $Result[0]==GetMessage('WDA_CLI_CHECK_ENG')) {
					$bSuccess = true;
				} else {
					$bSuccess = false;
				}
			}
		}
		return $bSuccess;
	}
	
	/**
	 *	Запуск процесса из планировщика
	 */
	public static function CronExec($Arguments){
		unset($Arguments[0]);
		$arParams = array(); // array like a $_GET
		foreach($Arguments as $Argument){
			$arArgument = array();
			parse_str($Argument, $arArgument);
			if(is_array($arArgument)) {
				$arParams = array_merge($arParams,$arArgument);
			}
		}
		$DateStart = date(CDatabase::DateFormatToPHP(FORMAT_DATETIME));
		if (CModule::IncludeModule('iblock')) {
			if(is_numeric($arParams['profile']) && $arParams['profile']>0) {
				$resProfile = CWDA_Profile::GetList(array(),array('ID'=>$arParams['profile']));
				if($arProfile = $resProfile->GetNext()){
					$IBlockID = IntVal($arProfile['IBLOCK_ID']);
					if($IBlockID>0) {
						$Action = $arProfile['ACTION'];
						if(!empty($Action)) {
							$arActions = CWDA::GetActionsList();
							$Action = CWDA::GetAction($Action,$arActions);
							if(is_array($Action)){
								$Class = $Action['CLASS'];
								$arSectionsID = array_filter(explode(',',$arProfile['SECTIONS_ID']));
								$arFilterData = array();
								parse_str($arProfile['~FILTER'],$arFilterData);
								$arActionParams = array();
								parse_str($arProfile['~PARAMS'],$arActionParams);
								if(!is_array($arActionParams)){
									$arActionParams = array();
								}
								//
								$arActionParamsFirst = $arActionParams['params'];
								unset($arActionParams['params']);
								$arActionParams = array_merge($arActionParams,$arActionParamsFirst);
								//
								$FilterFields = CWDA::GetAllFields($IBlockID);
								$FilterParams = CWDA::CollectFilter($arFilterData['f_p2'],$arFilterData['f_e2'],$arFilterData['f_v2']);
								$FilterResult = CWDA::BuildFilter($IBlockID, $arSectionsID, $arProfile['WITH_SUBSECTIONS']=='Y'?true:false, $FilterParams, $FilterFields);
								$Count = CWDA::GetCount($FilterResult);
								//
								$SuccessCount = 0;
								$FailedCount = 0;
								$Class = $Action['CLASS'];
								$GLOBALS['WDA_CUSTOM'] = array();
								$GLOBALS['WDA_START'] = true;
								$GLOBALS['WDA_FIRST'] = true;
								$resItems = CIBlockElement::GetList(array('ID'=>'ASC'),$FilterResult,false,false,array('ID'));
								while ($arItem = $resItems->GetNext()) {
									$arElement = CWDA::GetElementByID($arItem['ID']);
									$bResult = $Class::Process($arItem['ID'], $arElement, $arActionParams);
									unset($GLOBALS['WDA_START']);
									unset($GLOBALS['WDA_FIRST']);
									if($bResult) {
										$SuccessCount++;
									} else {
										$FailedCount++;
									}
								}
								CWDA_Profile::SetFieldValue($arProfile['ID'],array('DATE_SUCCESS'=>date(CDatabase::DateFormatToPHP(CWDA_Profile::MYSQL_DATE_FORMAT))));
								if($arProfile['SEND_EMAIL']=='Y') {
									self::CreateEventType();
									$arEventFields = array(
										'PROFILE_ID' => $arProfile['ID'],
										'PROFILE_NAME' => $arProfile['NAME'],
										'PROFILE_DESCRIPTION' => $arProfile['DESCRIPTION'],
										'DATETIME_START' => $DateStart,
										'DATETIME_FINISH' => date(CDatabase::DateFormatToPHP(FORMAT_DATETIME)),
										'ACTION' => $Action['NAME'],
										'COUNT_ALL' => $Count,
										'COUNT_SUCCESS' => $SuccessCount,
										'COUNT_FAILED' => $FailedCount,
									);
									$arSitesID = array();
									$resSites = CSite::GetList($by='sort',$order='asc');
									while ($arSite = $resSites->GetNext()) {
										$arSitesID[] = $arSite['LID'];
									}
									CEvent::Send('WD_ANTIRUTIN_CRON_NOTICE',$arSitesID,$arEventFields);
									CEvent::CheckEvents();
								}
								print 'Done: '.$SuccessCount.'/'.$Count.PHP_EOL;
							}
						}
					}
				}
			}
		}
	}
	
	/**
	 *	Определение (справочно) команды запуска профиля для Cron
	 */
	function GetCronCommand($ProfileID) {
		$strResult = false;
		$LocalFileName = '/bitrix/modules/'.WDA_MODULE.'/cron.php';
		$PhpIni = '/bitrix/modules/'.WDA_MODULE.'/php.ini';
		$arPhpPath = false;
		$Exec = exec('which php', $arPhpPath);
		if(!empty($Exec) && !empty($arPhpPath[0])) {
			$PhpIni = !empty($PhpIni) && is_file($_SERVER['DOCUMENT_ROOT'].$PhpIni) ? '-c '.$_SERVER['DOCUMENT_ROOT'].$PhpIni : '';
			$PhpIni = str_replace('//','/',$PhpIni);
			$LocalFileName = str_replace('//','/',$_SERVER['DOCUMENT_ROOT'].$LocalFileName);
			$strResult = "{$arPhpPath[0]} {$PhpIni} -f {$LocalFileName} profile=".$ProfileID;
		}
		return $strResult;
	}
	
	/**
	 *	Создание типа почтового события и шаблона к нему
	 */
	public static function CreateEventType() {
		$EventType = new CEventType;
		$EventMessage = new CEventMessage;
		$arSitesID = array();
		$resSites = CSite::GetList($By='SORT',$Order='ASC');
		while ($arSite = $resSites->GetNext(false,false)) {
			$arSitesID[] = $arSite['LID'];
		}
		$arEventTypeFields = array(
			'LID' => LANGUAGE_ID,
			'EVENT_NAME' => 'WD_ANTIRUTIN_CRON_NOTICE',
			'NAME' => GetMessage('WDA_EVENT_TYPE_NAME'),
			'DESCRIPTION' => GetMessage('WDA_EVENT_TYPE_DESCRIPTION'),
		);
		$resEventTypes = CEventType::GetList(array('TYPE_ID'=>$arEventTypeFields['EVENT_NAME'],'LID'=>$arEventTypeFields['LID']));
		if ($arEventType = $resEventTypes->GetNext(false,false)) {
			$EventType->Update(array('ID'=>$arEventType['ID']), $arEventTypeFields);
		} else {
			$EventType->Add($arEventTypeFields);
			$arEventMessageFields = array(
				'ACTIVE' => 'Y',
				'LID' => $arSitesID,
				'LANGUAGE_ID' => $arEventTypeFields['LID'],
				'EVENT_NAME' => $arEventTypeFields['EVENT_NAME'],
				'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
				'EMAIL_TO' => '#DEFAULT_EMAIL_FROM#',
				'SUBJECT' => GetMessage('WDA_EVENT_MESSAGE_SUBJECT'),
				'BODY_TYPE' => 'text',
				'MESSAGE' => GetMessage('WDA_EVENT_MESSAGE_BODY'),
			);
			$EventMessage->Add($arEventMessageFields);
		}
	}
	
}

?>