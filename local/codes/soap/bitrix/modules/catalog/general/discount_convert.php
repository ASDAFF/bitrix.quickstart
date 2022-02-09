<?
IncludeModuleLangFile(__FILE__);

class CCatalogDiscountConvert
{
	public static $intConvertPerStep = 0;
	public static $intNextConvertPerStep = 0;
	public static $intConverted = 0;
	public static $intErrors = 0;

	public function __construct()
	{

	}

	public static function ConvertDiscount($intStep = 100, $intMaxExecutionTime = 15)
	{
		global $DBType;
		global $DB;

		$intStep = intval($intStep);
		if (0 >= $intStep)
			$intStep = 100;
		$startConvertTime = getmicrotime();

		$obDiscount = new CCatalogDiscount;

		$strQueryPriceTypes = '';
		$strQueryUserGroups = '';
		$strTableName = '';
		switch (ToUpper($DBType))
		{
			case 'MYSQL':
				$strQueryPriceTypes = 'select CATALOG_GROUP_ID from b_catalog_discount2cat where DISCOUNT_ID = #ID#';
				$strQueryUserGroups = 'select GROUP_ID from b_catalog_discount2group where DISCOUNT_ID = #ID#';
				$strTableName = 'b_catalog_discount';
				break;
			case 'MSSQL':
				$strQueryPriceTypes = 'select CATALOG_GROUP_ID from B_CATALOG_DISCOUNT2CAT where DISCOUNT_ID = #ID#';
				$strQueryUserGroups = 'select GROUP_ID from B_CATALOG_DISCOUNT2GROUP where DISCOUNT_ID = #ID#';
				$strTableName = 'B_CATALOG_DISCOUNT';
				break;
			case 'ORACLE':
				$strQueryPriceTypes = 'select CATALOG_GROUP_ID from B_CATALOG_DISCOUNT2CAT where DISCOUNT_ID = #ID#';
				$strQueryUserGroups = 'select GROUP_ID from B_CATALOG_DISCOUNT2GROUP where DISCOUNT_ID = #ID#';
				$strTableName = 'B_CATALOG_DISCOUNT';
				break;
		}

		CTimeZone::Disable();

		$rsDiscounts = CCatalogDiscount::GetList(
			array('ID' => 'ASC'),
			array(
				'TYPE' => DISCOUNT_TYPE_STANDART,
				'VERSION' => CATALOG_DISCOUNT_OLD_VERSION
			),
			false,
			array('nTopCount' => $intStep),
			array('ID', 'MODIFIED_BY', 'TIMESTAMP_X')
		);
		while ($arDiscount = $rsDiscounts->Fetch())
		{
			$arFields = array();
			$arFields['MODIFIED_BY'] = $arDiscount['MODIFIED_BY'];
			$arPriceTypes = array();
			$arUserGroups = array();

			$rsPriceTypes = $DB->Query(str_replace('#ID#', $arDiscount['ID'], $strQueryPriceTypes), false, "File: ".__FILE__."<br>Line: ".__LINE__);
			while ($arPrice = $rsPriceTypes->Fetch())
			{
				$arPrice['CATALOG_GROUP_ID'] = intval($arPrice['CATALOG_GROUP_ID']);
				if (0 < $arPrice['CATALOG_GROUP_ID'])
					$arPriceTypes[] = $arPrice['CATALOG_GROUP_ID'];
			}
			if (!empty($arPriceTypes))
			{
				$arPriceTypes = array_values(array_unique($arPriceTypes));
			}
			else
			{
				$arPriceTypes = array(-1);
			}

			$rsUserGroups = $DB->Query(str_replace('#ID#', $arDiscount['ID'], $strQueryUserGroups), false, "File: ".__FILE__."<br>Line: ".__LINE__);
			while ($arGroup = $rsUserGroups->Fetch())
			{
				$arGroup['GROUP_ID'] = intval($arGroup['GROUP_ID']);
				if (0 < $arGroup['GROUP_ID'])
					$arUserGroups[] = $arGroup['GROUP_ID'];
			}
			if (!empty($arUserGroups))
			{
				$arUserGroups = array_values(array_unique($arUserGroups));
			}
			else
			{
				$arUserGroups = array(-1);
			}

			$arFields['CATALOG_GROUP_IDS'] = $arPriceTypes;
			$arFields['GROUP_IDS'] = $arUserGroups;

			$arIBlockList = array();
			$arSectionList = array();
			$arElementList = array();
			$arConditions = array(
				'CLASS_ID' => 'CondGroup',
				'DATA' => array(
					'All' => 'AND',
					'True' => 'True',
				),
				'CHILDREN' => array(),
			);
			$intEntityCount = 0;

			$rsIBlocks = CCatalogDiscount::GetDiscountIBlocksList(array(), array('DISCOUNT_ID' => $arDiscount['ID']), false, false, array('IBLOCK_ID'));
			while ($arIBlock = $rsIBlocks->Fetch())
			{
				$arIBlock['IBLOCK_ID'] = intval($arIBlock['IBLOCK_ID']);
				if (0 < $arIBlock['IBLOCK_ID'])
					$arIBlockList[] = $arIBlock['IBLOCK_ID'];
			}
			if (!empty($arIBlockList))
			{
				$arIBlockList = array_values(array_unique($arIBlockList));
				$intEntityCount++;
			}

			$rsSections = CCatalogDiscount::GetDiscountSectionsList(array(), array('DISCOUNT_ID' => $arDiscount['ID']), false, false, array('SECTION_ID'));
			while ($arSection = $rsSections->Fetch())
			{
				$arSection['SECTION_ID'] = intval($arSection['SECTION_ID']);
				if (0 < $arSection['SECTION_ID'])
					$arSectionList[] = $arSection['SECTION_ID'];
			}
			if (!empty($arSectionList))
			{
				$arSectionList = array_values(array_unique($arSectionList));
				$intEntityCount++;
			}

			$rsElements = CCatalogDiscount::GetDiscountProductsList(array(), array('DISCOUNT_ID' => $arDiscount['ID']), false, false, array('PRODUCT_ID'));
			while ($arElement = $rsElements->Fetch())
			{
				$arElement['PRODUCT_ID'] = intval($arElement['PRODUCT_ID']);
				if (0 < $arElement['PRODUCT_ID'])
					$arElementList[] = $arElement['PRODUCT_ID'];
			}
			if (!empty($arElementList))
			{
				$arElementList = array_values(array_unique($arElementList));
				$intEntityCount++;
			}

			if (!empty($arIBlockList))
			{
				if (1 < count($arIBlockList))
				{
					$arList = array();
					foreach ($arIBlockList as &$intItemID)
					{
						$arList[] = array(
							'CLASS_ID' => 'CondIBIBlock',
							'DATA' => array(
								'logic' => 'Equal',
								'value' => $intItemID
							),
						);
					}
					if (isset($intItemID))
						unset($intItemID);
					if (1 == $intEntityCount)
					{
						$arConditions = array(
							'CLASS_ID' => 'CondGroup',
							'DATA' => array(
								'All' => 'OR',
								'True' => 'True',
							),
							'CHILDREN' => $arList,
						);
					}
					else
					{
						$arConditions['CHILDREN'][] = array(
							'CLASS_ID' => 'CondGroup',
							'DATA' => array(
								'All' => 'OR',
								'True' => 'True',
							),
							'CHILDREN' => $arList,
						);
					}
				}
				else
				{
					$arConditions['CHILDREN'][] = array(
						'CLASS_ID' => 'CondIBIBlock',
						'DATA' => array(
							'logic' => 'Equal',
							'value' => current($arIBlockList)
						),
					);
				}
			}

			if (!empty($arSectionList))
			{
				if (1 < count($arSectionList))
				{
					$arList = array();
					foreach ($arSectionList as &$intItemID)
					{
						$arList[] = array(
							'CLASS_ID' => 'CondIBSection',
							'DATA' => array(
								'logic' => 'Equal',
								'value' => $intItemID
							),
						);
					}
					if (isset($intItemID))
						unset($intItemID);
					if (1 == $intEntityCount)
					{
						$arConditions = array(
							'CLASS_ID' => 'CondGroup',
							'DATA' => array(
								'All' => 'OR',
								'True' => 'True',
							),
							'CHILDREN' => $arList,
						);
					}
					else
					{
						$arConditions['CHILDREN'][] = array(
							'CLASS_ID' => 'CondGroup',
							'DATA' => array(
								'All' => 'OR',
								'True' => 'True',
							),
							'CHILDREN' => $arList,
						);
					}
				}
				else
				{
					$arConditions['CHILDREN'][] = array(
						'CLASS_ID' => 'CondIBSection',
						'DATA' => array(
							'logic' => 'Equal',
							'value' => current($arSectionList)
						),
					);
				}
			}

			if (!empty($arElementList))
			{
				if (1 < count($arElementList))
				{
					$arList = array();
					foreach ($arElementList as &$intItemID)
					{
						$arList[] = array(
							'CLASS_ID' => 'CondIBElement',
							'DATA' => array(
								'logic' => 'Equal',
								'value' => $intItemID
							),
						);
					}
					if (isset($intItemID))
						unset($intItemID);
					if (1 == $intEntityCount)
					{
						$arConditions = array(
							'CLASS_ID' => 'CondGroup',
							'DATA' => array(
								'All' => 'OR',
								'True' => 'True',
							),
							'CHILDREN' => $arList,
						);
					}
					else
					{
						$arConditions['CHILDREN'][] = array(
							'CLASS_ID' => 'CondGroup',
							'DATA' => array(
								'All' => 'OR',
								'True' => 'True',
							),
							'CHILDREN' => $arList,
						);
					}
				}
				else
				{
					$arConditions['CHILDREN'][] = array(
						'CLASS_ID' => 'CondIBElement',
						'DATA' => array(
							'logic' => 'Equal',
							'value' => current($arElementList)
						),
					);
				}
			}

			$arFields['CONDITIONS'] = $arConditions;

			$mxRes = $obDiscount->Update($arDiscount['ID'], $arFields);
			if (!$mxRes)
			{
				self::$intErrors++;
			}
			else
			{
				$arTimeFields = array('~TIMESTAMP_X' => $DB->CharToDateFunction($arDiscount['TIMESTAMP_X'], "FULL"));
				$strUpdate = $DB->PrepareUpdate($strTableName, $arTimeFields);
				if (!empty($strUpdate))
				{
					$strQuery = "UPDATE ".$strTableName." SET ".$strUpdate." WHERE ID = ".$arDiscount['ID']." AND TYPE = ".DISCOUNT_TYPE_STANDART;
					$DB->Query($strQuery, false, "File: ".__FILE__."<br>Line: ".__LINE__);
				}

				self::$intConverted++;
				self::$intConvertPerStep++;
			}

			if ($intMaxExecutionTime > 0 && (getmicrotime() - $startConvertTime > $intMaxExecutionTime))
				break;
		}

		CTimeZone::Disable();

		if ($intMaxExecutionTime > (2*(getmicrotime() - $startConvertTime)))
			self::$intNextConvertPerStep = $intStep*2;
		else
			self::$intNextConvertPerStep = $intStep;
	}

	public static function GetCountOld()
	{
		global $DBType;
		global $DB;

		$strSql = '';
		switch(ToUpper($DBType))
		{
			case 'MYSQL':
				$strSql = "SELECT COUNT(*) CNT FROM b_catalog_discount WHERE TYPE=".DISCOUNT_TYPE_STANDART." AND VERSION=".CATALOG_DISCOUNT_OLD_VERSION;
				break;
			case 'MSSQL':
				$strSql = "SELECT COUNT(*) CNT FROM B_CATALOG_DISCOUNT WHERE TYPE=".DISCOUNT_TYPE_STANDART." AND VERSION=".CATALOG_DISCOUNT_OLD_VERSION;
				break;
			case 'ORACLE':
				$strSql = "SELECT COUNT(*) CNT FROM B_CATALOG_DISCOUNT WHERE TYPE=".DISCOUNT_TYPE_STANDART." AND VERSION=".CATALOG_DISCOUNT_OLD_VERSION;
				break;
		}
		$res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		if (!$res)
			return 0;

		if ($row = $res->Fetch())
			return intval($row['CNT']);
	}
}
?>