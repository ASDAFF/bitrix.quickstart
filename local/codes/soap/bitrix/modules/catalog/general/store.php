<?php
IncludeModuleLangFile(__FILE__);

class CAllCatalogStore
{
	protected function CheckFields($action, &$arFields)
	{
		if (isset($arFields["ADDRESS"]) && strlen($arFields["ADDRESS"])<=0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("CS_EMPTY_ADDRESS"));
			unset($arFields["ADDRESS"]);
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
		$arFields["USER_ID"]=intval($arFields["USER_ID"]);
		if ($action == 'ADD')
		{
			$arFields["DATE_CREATE"]=ConvertTimeStamp(time(), "FULL");
			$arFields["DATE_MODIFY"]=ConvertTimeStamp(time(), "FULL");
		}
		$arFields["DATE_MODIFY"]=ConvertTimeStamp(time(), "FULL");

		return true;
	}

	static function Update($id, $arFields)
	{
		global $DB;
		$id = intval($id);
		if($id<=0 || !self::CheckFields('UPDATE',$arFields))
			return false;
		$strUpdate = $DB->PrepareUpdate("b_catalog_store", $arFields);
		$strSql = "UPDATE b_catalog_store SET ".$strUpdate." WHERE ID = ".$id." ";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		return $id;
	}

	static function Delete($id)
	{
		global $DB;
		$id = intval($id);
		if ($id > 0)
		{
			$DB->Query("DELETE FROM b_catalog_store_product WHERE STORE_ID = ".$id." ", true);
			$DB->Query("DELETE FROM b_catalog_store WHERE ID = ".$id." ", true);
			return true;
		}
		return false;
	}
}