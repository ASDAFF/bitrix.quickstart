<?php
IncludeModuleLangFile(__FILE__);

class CAllCatalogStore
{
	protected function CheckFields($action, &$arFields)
	{
		if (is_set($arFields["ADDRESS"]) && strlen($arFields["ADDRESS"])<=0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("CS_EMPTY_ADDRESS"));
			$arFields["ADDRESS"] = ' ';
		}
		if (($action == 'ADD') &&
			((is_set($arFields, "IMAGE_ID") && strlen($arFields["IMAGE_ID"])<0)))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("CS_WRONG_IMG"));
			return false;
		}
		if (($action == 'ADD') &&
			((is_set($arFields, "LOCATION_ID") && intval($arFields["LOCATION_ID"])<=0)))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("CS_WRONG_LOC"));
			return false;
		}
		if (($action == 'UPDATE') && is_set($arFields, "ID"))
			unset($arFields["ID"]);

		if (($action == 'UPDATE') && strlen($arFields["IMAGE_ID"])<=0)
			unset($arFields["IMAGE_ID"]);

		return true;
	}

	static function Update($id, $arFields)
	{
		global $DB;
		$id = intval($id);
		$bNeedConversion = false;
		if(array_key_exists('DATE_CREATE',$arFields))
			unset($arFields['DATE_CREATE']);
		if(array_key_exists('DATE_MODIFY', $arFields))
			unset($arFields['DATE_MODIFY']);
		if(array_key_exists('DATE_STATUS', $arFields))
			unset($arFields['DATE_STATUS']);
		if(array_key_exists('CREATED_BY', $arFields))
			unset($arFields['CREATED_BY']);

		$arFields['~DATE_MODIFY'] = $DB->GetNowFunction();

		$dbStore = CCatalogStore::GetList(array(), array("ID" => $id), false, false, array("ACTIVE"));
		if($arStore = $dbStore->Fetch())
		{
			if($arStore["ACTIVE"] != $arFields["ACTIVE"])
				$bNeedConversion = true;
		}

		if($id <= 0 || !self::CheckFields('UPDATE',$arFields))
			return false;
		$strUpdate = $DB->PrepareUpdate("b_catalog_store", $arFields);

		if(!empty($strUpdate))
		{
			$strSql = "UPDATE b_catalog_store SET ".$strUpdate." WHERE ID = ".$id." ";
			if(!$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__))
				return false;
		}
		if($bNeedConversion)
		{
			self::recalculateStoreBalances($id);
		}

		return $id;
	}

	static function Delete($id)
	{
		global $DB;
		$id = intval($id);
		if ($id > 0)
		{
			$dbDocs = $DB->Query("SELECT ID FROM b_catalog_docs_element WHERE STORE_FROM = ".$id." OR STORE_TO = ".$id." ", true);
			if($bStoreHaveDocs = $dbDocs->Fetch())
			{
				$GLOBALS["APPLICATION"]->ThrowException(GetMessage("CS_STORE_HAVE_DOCS"));
				return false;
			}

			$DB->Query("DELETE FROM b_catalog_store_product WHERE STORE_ID = ".$id." ", true);
			$DB->Query("DELETE FROM b_catalog_store WHERE ID = ".$id." ", true);
			self::recalculateStoreBalances($id);
			return true;
		}
		return false;
	}

	function recalculateStoreBalances($id)
	{
		global $DB;
		$arFields = array();
		if(COption::GetOptionString('catalog','default_use_store_control','N') != 'Y')
		{
			return false;
		}
		$dbStoreProduct = CCatalogStoreProduct::GetList(array(), array("STORE_ID" => $id, "!AMOUNT" => 0), false, false, array("PRODUCT_ID", "AMOUNT"));
		while($arStoreProduct = $dbStoreProduct->Fetch())
		{
			$dbAmount = $DB->Query("SELECT SUM(SP.AMOUNT) as SUM, CP.QUANTITY_RESERVED as RESERVED, CS.ACTIVE FROM b_catalog_store_product SP INNER JOIN b_catalog_product CP ON SP.PRODUCT_ID = CP.ID INNER JOIN b_catalog_store CS ON SP.STORE_ID = CS.ID WHERE SP.PRODUCT_ID = ".$arStoreProduct['PRODUCT_ID']." AND CS.ACTIVE = 'Y' GROUP BY QUANTITY_RESERVED, ACTIVE ", true);
			if($arAmount = $dbAmount->Fetch())
			{
				$arFields["QUANTITY"] = doubleval($arAmount["SUM"] - $arAmount["RESERVED"]);
			}
			else
			{
				if($arReservAmount = CCatalogProduct::GetByID($arStoreProduct['PRODUCT_ID']))
				{
					$arFields["QUANTITY"] = doubleval(0 - $arReservAmount["QUANTITY_RESERVED"]);
				}
			}
			if(!CCatalogProduct::Update($arStoreProduct["PRODUCT_ID"], $arFields))
			{
				$GLOBALS["APPLICATION"]->ThrowException(GetMessage("CAT_DOC_PURCHASING_INFO_ERROR"));
				return false;
			}
		}
		return true;
	}
}