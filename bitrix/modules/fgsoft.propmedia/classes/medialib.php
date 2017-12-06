<?
IncludeModuleLangFile(__FILE__);

CModule::IncludeModule("fileman");
CMedialib::Init();

class FGSoftPropMediaLibUserType
{

	function GetUserTypeDescription()
	{
		return array(
			"USER_TYPE_ID"	=> "medialib",
			"CLASS_NAME"	=> "FGSoftPropMediaLibUserType",
			"DESCRIPTION"	=> GetMessage("FGSOFT_PROP_MEDIALIB_DESCRIPTION"),
			"BASE_TYPE"		=> "string",
		);
	}

	function OnSearchIndex($arUserField)
	{
		if (is_array($arUserField['VALUE']))
		{
			$return = Array();
			$arMediaLib = CMedialibCollection::GetList($Params = array('arFilter' =>
				array(
					'ACTIVE' => 'Y',
					'ID' => implode('|', $arUserField['VALUE'])
				)
			));
			foreach ($arMediaLib as $mediaLib)
			{
				$return[] = $mediaLib['NAME'];
			}
			return implode("\r\n", $return);
		}
		else
		{
			$arMediaLib = CMedialibCollection::GetList($Params = array('arFilter' =>
				array(
					'ACTIVE' => 'Y',
					'ID' => $arUserField['VALUE']
				)
			));
			if (count($arMediaLib) > 0)
			{
				return $arMediaLib[0]['NAME'];
			}
			else return '';
		}
	}

	function GetFilterHTML($arUserField, $arHtmlControl)
	{
		global $lAdmin;
		$lAdmin->InitFilter(Array($arHtmlControl["NAME"]));

		$values = is_array($arHtmlControl["VALUE"]) ? $arHtmlControl["VALUE"] : Array($arHtmlControl["VALUE"]);

		if ($arUserField["MULTIPLE"] === 'Y') $multiple = ' multiple size="5"';
		else $multiple = '';

		$html = "<select name='".$arHtmlControl['NAME'].($arUserField["MULTIPLE"] === "Y"?"[]":"")."' ".$multiple."><option value=''>".GetMessage("FGSOFT_PROP_MEDIALIB_NO")."</option>";

		$arMediaLib = CMedialibCollection::GetList();
		foreach ($arMediaLib as $mediaLib)
		{
			$html .= "<option ".(in_array($mediaLib['ID'], $values)?'selected':'')." value='".$mediaLib['ID']."'>[".$mediaLib['ID']."] ".$mediaLib['NAME']."</option>";
		}

		$html .= "</select>";

		return  $html;
	}

	function GetAdminListViewHTML($arUserField, $arHtmlControl)
	{
		if ($arHtmlControl['VALUE'])
		{
			$arMediaLib = CMedialibCollection::GetList($Params = array('arFilter' =>
				array(
					'ACTIVE' => 'Y',
					'ID' => $arHtmlControl['VALUE']
				)
			));
			if (count($arMediaLib) > 0)
			{
				return $arMediaLib[0]['NAME'];
			}
			else return '&nbsp;';
		}
		else return '&nbsp;';
	}

	function GetEditFormHTML($arUserField, $arHtmlControl)
	{
		$return = "<select name='".$arHtmlControl['NAME']."' ".($arUserField['EDIT_IN_LIST']==='N'?"disabled='disabled'":"")."><option value=''>".GetMessage("FGSOFT_PROP_MEDIALIB_NO")."</option>";

		$arMediaLib = CMedialibCollection::GetList();
		foreach ($arMediaLib as $mediaLib)
		{
			$return .= "<option ".($mediaLib['ID'] == $arHtmlControl["VALUE"]?'selected':'')." value='".$mediaLib['ID']."'>[".$mediaLib['ID']."] ".$mediaLib['NAME']."</option>";
		}

		$return .= "</select>";

		return $return;
	}

	function GetDBColumnType($arUserField)
	{
		global $DB;
		switch(strtolower($DB->type))
		{
			case "mysql":
                return "text";
            case "oracle":
                return "varchar2(2000 char)";
            case "mssql":
                return "varchar(2000)";
		}
	}

}


class FGSoftPropMediaLibIblockProperty
{
	function GetUserTypeDescription()
	{
		return Array(
			"PROPERTY_TYPE"			=> "S",
			"USER_TYPE"				=> "MediaLibIblockProperty",
			"DESCRIPTION"			=> GetMessage("FGSOFT_PROP_MEDIALIB_DESCRIPTION"),
			"GetSettingsHTML"		=> Array("FGSoftPropMediaLibIblockProperty", "GetSettingsHTML"),
			"GetPropertyFieldHtml"	=> Array("FGSoftPropMediaLibIblockProperty", "GetPropertyFieldHtml"),
			"GetAdminListViewHTML"	=> Array("FGSoftPropMediaLibIblockProperty", "GetAdminListViewHTML"),
			"GetAdminFilterHTML"	=> Array("FGSoftPropMediaLibIblockProperty", "GetAdminFilterHTML"),
			"GetPublicViewHTML"		=> Array("FGSoftPropMediaLibIblockProperty", "GetPublicViewHTML"),
		);
	}

	function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
	{
		$arPropertyFields = Array("HIDE" => array("ROW_COUNT", "COL_COUNT", "DEFAULT_VALUE"));

		return '';
	}

	function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
	{
		if ($value['VALUE'])
		{
			CModule::IncludeModule("fileman");
			CMedialib::Init();
			$arMediaLib = CMedialibCollection::GetList($Params = array('arFilter' =>
				array(
					'ACTIVE' => 'Y',
					'ID' => $value['VALUE']
				)
			));

			if (count($arMediaLib) > 0)
			{
				return $arMediaLib[0]['NAME'];
			}
			else return '&nbsp;';
		}
		else return '&nbsp;';
	}

	function GetAdminFilterHTML($arProperty, $strHTMLControlName)
	{
		$lAdmin = new CAdminList($strHTMLControlName["TABLE_ID"]);
		$lAdmin->InitFilter(Array($strHTMLControlName["VALUE"]));
		$filterValue = $GLOBALS[$strHTMLControlName["VALUE"]];

		if (isset($filterValue) && is_array($filterValue)) $values = $filterValue;
		else $values = Array();

		if ($arProperty["MULTIPLE"] === 'Y') $multiple = ' multiple size="5"';
		else $multiple = '';

		$html = "<select name='".$strHTMLControlName['VALUE']."' ".$multiple."><option value=''>".GetMessage("FGSOFT_PROP_MEDIALIB_NO")."</option>";

		$arMediaLib = CMedialibCollection::GetList();
		foreach ($arMediaLib as $mediaLib)
		{
			$html .= "<option ".($mediaLib['ID'] == $filterValue["VALUE"]?'selected':'')." value='".$mediaLib['ID']."'>[".$mediaLib['ID']."] ".$mediaLib['NAME']."</option>";
		}

		$html .= "</select>";

		return  $html;
	}

	function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        if ($value['VALUE'])
		{
			$arMediaLib = CMedialibCollection::GetList($Params = array('arFilter' =>
				array(
					'ACTIVE' => 'Y',
					'ID' => $value['VALUE']
				)
			));
			if (count($arMediaLib) > 0)
			{
				return $arMediaLib[0]['NAME'];
			}
			else return '&nbsp;';
		}
		else return '&nbsp;';
    }

	function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{

		$return = "<select name='".$strHTMLControlName['VALUE']."'><option value=''>".GetMessage("FGSOFT_PROP_MEDIALIB_NO")."</option>";

		$arMediaLib = CMedialibCollection::GetList();
		foreach ($arMediaLib as $mediaLib){
			$return .= "<option ".($mediaLib['ID'] == $value["VALUE"]?'selected':'')." value='".$mediaLib['ID']."'>[".$mediaLib['ID']."] ".$mediaLib['NAME']."</option>";
		}

		$return .= "</select>";

		return $return;
	}
}
?>