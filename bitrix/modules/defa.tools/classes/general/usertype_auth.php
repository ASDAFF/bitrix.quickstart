<?

IncludeModuleLangFile(__FILE__);

if (!class_exists("DefaTools_UserType_Auth"))
{

	class DefaTools_UserType_Auth
	{
		function GetUserTypeDescription()
		{
			return array(
				"USER_TYPE_ID"	=> "defatoolsuserauth",
				"CLASS_NAME"	=> "DefaTools_UserType_Auth",
				"DESCRIPTION"	=> GetMessage("DEFATOOLS_PROP_NAME"),	 
				"BASE_TYPE"		=> "int",
			);
		}
	
		function GetDBColumnType($arUserField)
		{
			global $DB;
			switch(strtolower($DB->type))
			{
				case "mysql":
					return "int(1)";
				case "oracle":
					return "number(1)";
				case "mssql":
					return "int";
			}
		}
	
		function PrepareSettings($arUserField)
		{
			return array();
		}
	
		function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm)
		{
			return "";
		}
	
		function GetEditFormHTML($arUserField, $arHtmlControl)
		{
			return '<input type="submit" value="'.GetMessage("DEFATOOLS_ENTER").'" name="save_'.$arUserField["VALUE_ID"].'"'.($GLOBALS["USER"]->IsAdmin()? '':'disabled="disabled" ').'>
				<input type="hidden" name="'.$arHtmlControl["NAME"].'" value="'.$arUserField["VALUE_ID"].'">';
		}
	
		function GetFilterHTML($arUserField, $arHtmlControl)
		{
			return '';
		}
	
		function GetAdminListViewHTML($arUserField, $arHtmlControl)
		{
			preg_match("/FIELDS\[([0-9]+)\]/", $arHtmlControl["NAME"], $a);
			
			if ($a[1] > 0 && $GLOBALS["USER"]->IsAdmin()) {
				return '<input type="submit" value="'.GetMessage("DEFATOOLS_ENTER").'" name="save_'.$a[1].'"'.($GLOBALS["USER"]->IsAdmin()? '':'disabled="disabled" ').'>
					<input type="hidden" name="'.$arHtmlControl["NAME"].'" value="'.$a[1].'">
					<input type="hidden" name="save" value="'.GetMessage("DEFATOOLS_ENTER").'">';
			}
			
			return "&nbsp;";
			
		}
	
		function GetAdminListEditHTML($arUserField, $arHtmlControl)
		{
			return '';
		}
	
		function CheckFields($arUserField, $value)
		{
			$aMsg = array();
	
			if(intval($value)>0 && isset($_REQUEST["save_".intval($value)])) {
	
				if ($GLOBALS["USER"]->IsAdmin()) {
					$GLOBALS["USER"]->Authorize(intval($value));
					echo '<script>window.parent.location.href="/"</script>';
					LocalRedirect("/");
					die();
				}
				else {
					$aMsg[] = array(
						"id" => $arUserField["FIELD_NAME"],
						"text" => GetMessage("DEFATOOLS_ACCESS_DENIED"),
					);
				}
			}
			return $aMsg;
		}
	
		function OnSearchIndex($arUserField)
		{
			return "";
		}
	}

} // class exists

