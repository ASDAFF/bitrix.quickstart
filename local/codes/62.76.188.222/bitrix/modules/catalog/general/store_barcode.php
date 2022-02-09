<?
IncludeModuleLangFile(__FILE__);

class CAllCatalogStoreBarCode
{
	protected function CheckFields($action, &$arFields)
	{
		return true;
	}

	static function Update($id, $arFields)
	{
		global $DB;
		$id = intval($id);

		if(array_key_exists('DATE_CREATE',$arFields))
			unset($arFields['DATE_CREATE']);
		if(array_key_exists('DATE_MODIFY', $arFields))
			unset($arFields['DATE_MODIFY']);
		if(array_key_exists('DATE_STATUS', $arFields))
			unset($arFields['DATE_STATUS']);
		if(array_key_exists('CREATED_BY', $arFields))
			unset($arFields['CREATED_BY']);

		$arFields['~DATE_MODIFY'] = $DB->GetNowFunction();

		if($id <= 0 || !self::CheckFields('UPDATE',$arFields))
			return false;
		$strUpdate = $DB->PrepareUpdate("b_catalog_store_barcode", $arFields);

		if(!empty($strUpdate))
		{
			$strSql = "UPDATE b_catalog_store_barcode SET ".$strUpdate." WHERE ID = ".$id." ";
			if(!$DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__))
				return false;
		}
		return $id;
	}

	static function Delete($id)
	{
		global $DB;
		$id = intval($id);
		if ($id > 0)
		{
			$DB->Query("DELETE FROM b_catalog_store_barcode WHERE ID = ".$id." ", true);
			return true;
		}
		return false;
	}
}