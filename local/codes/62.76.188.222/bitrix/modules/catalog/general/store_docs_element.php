<?
IncludeModuleLangFile(__FILE__);

class CCatalogStoreDocsElementAll
{
	protected static function CheckFields($action, &$arFields)
	{
	/*	if ((($action == 'ADD') || isset($arFields["STORE_ID"])) && intval($arFields["STORE_ID"])<=0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("CP_EMPTY_STORE"));
			return false;
		}
		if ((($action == 'ADD') || isset($arFields["PRODUCT_ID"])) && intval($arFields["PRODUCT_ID"])<=0)
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("CP_EMPTY_PRODUCT"));
			return false;
		}
		if  (floatval($arFields["AMOUNT"])<0 || !is_numeric($arFields["AMOUNT"]))
		{
			$GLOBALS["APPLICATION"]->ThrowException(GetMessage("CP_FALSE_AMOUNT"));
			return false;
		}*/

		return true;
	}

	public static function update($id, $arFields)
	{
		$id=intval($id);
		if($id<0 || !self::CheckFields('UPDATE',$arFields))
			return false;
		global $DB;
		$strUpdate = $DB->PrepareUpdate("b_catalog_docs_element", $arFields);
		$strSql = "UPDATE b_catalog_docs_element SET ".$strUpdate." WHERE ID = ".$id;
		if(!$DB->Query($strSql, true, "File: ".__FILE__."<br>Line: ".__LINE__))
			return false;
		return true;
	}

	public static function delete($id)
	{
		global $DB;
		$id = intval($id);
		if ($id > 0)
		{
			$DB->Query("DELETE FROM b_catalog_docs_barcode WHERE DOC_ELEMENT_ID = ".$id." ", true);
			$DB->Query("DELETE FROM b_catalog_docs_element WHERE ID = ".$id." ", true);
			return true;
		}
		return false;
	}

	static function OnDocumentBarcodeDelete($id)
	{
		global $DB;
		$id = intval($id);
		if(!$DB->Query("DELETE FROM b_catalog_docs_element WHERE DOC_ID = ".$id." ", true))
			return false;

		foreach(GetModuleEvents("catalog", "OnDocumentElementDelete", true) as $event)
			ExecuteModuleEventEx($event, array($id));
	}
}