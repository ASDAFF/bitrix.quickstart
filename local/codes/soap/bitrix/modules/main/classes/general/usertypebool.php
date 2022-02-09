<?
IncludeModuleLangFile(__FILE__);

class CUserTypeBoolean
{
	function GetUserTypeDescription()
	{
		return array(
			"USER_TYPE_ID" => "boolean",
			"CLASS_NAME" => "CUserTypeBoolean",
			"DESCRIPTION" => GetMessage("USER_TYPE_BOOL_DESCRIPTION"),
			"BASE_TYPE" => "int",
		);
	}

	function GetDBColumnType($arUserField)
	{
		global $DB;
		switch(strtolower($DB->type))
		{
			case "mysql":
				return "int(18)";
			case "oracle":
				return "number(18)";
			case "mssql":
				return "int";
		}
	}

	function PrepareSettings($arUserField)
	{
		$def = $arUserField["SETTINGS"]["DEFAULT_VALUE"];
		if($def!=1)
			$def = 0;
		$disp = $arUserField["SETTINGS"]["DISPLAY"];
		if($disp!="CHECKBOX" && $disp!="RADIO" && $disp!="DROPDOWN")
			$dist = "CHECKBOX";
		return array(
			"DEFAULT_VALUE" => $def,
			"DISPLAY" => $disp,
		);
	}

	function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm)
	{
		$result = '';
		if($bVarsFromForm)
			$value = intval($GLOBALS[$arHtmlControl["NAME"]]["DEFAULT_VALUE"]);
		elseif(is_array($arUserField))
			$value = intval($arUserField["SETTINGS"]["DEFAULT_VALUE"]);
		else
			$value = 1;
		$result .= '
		<tr>
			<td>'.GetMessage("USER_TYPE_BOOL_DEFAULT_VALUE").':</td>
			<td>
				<select name="'.$arHtmlControl["NAME"].'[DEFAULT_VALUE]">
				<option value="1" '.($value? 'selected="selected"': '').'>'.GetMessage("MAIN_YES").'</option>
				<option value="0" '.(!$value? 'selected="selected"': '').'>'.GetMessage("MAIN_NO").'</option>
				</select>
			</td>
		</tr>
		';
		if($bVarsFromForm)
			$value = $GLOBALS[$arHtmlControl["NAME"]]["DISPLAY"];
		elseif(is_array($arUserField))
			$value = $arUserField["SETTINGS"]["DISPLAY"];
		else
			$value = "CHECKBOX";
		$result .= '
		<tr>
			<td class="adm-detail-valign-top">'.GetMessage("USER_TYPE_BOOL_DISPLAY").':</td>
			<td>
				<label><input type="radio" name="'.$arHtmlControl["NAME"].'[DISPLAY]" value="CHECKBOX" '.("CHECKBOX"==$value? 'checked="checked"': '').'>'.GetMessage("USER_TYPE_BOOL_CHECKBOX").'</label><br>
				<label><input type="radio" name="'.$arHtmlControl["NAME"].'[DISPLAY]" value="RADIO" '.("RADIO"==$value? 'checked="checked"': '').'>'.GetMessage("USER_TYPE_BOOL_RADIO").'</label><br>
				<label><input type="radio" name="'.$arHtmlControl["NAME"].'[DISPLAY]" value="DROPDOWN" '.("DROPDOWN"==$value? 'checked="checked"': '').'>'.GetMessage("USER_TYPE_BOOL_DROPDOWN").'</label><br>
			</td>
		</tr>
		';
		return $result;
	}

	function GetEditFormHTML($arUserField, $arHtmlControl)
	{
		if($arUserField["ENTITY_VALUE_ID"]<1)
			$arHtmlControl["VALUE"] = intval($arUserField["SETTINGS"]["DEFAULT_VALUE"]);
		switch($arUserField["SETTINGS"]["DISPLAY"])
		{
			case "DROPDOWN":
				$arHtmlControl["VALIGN"] = "middle";
				return '
					<select name="'.$arHtmlControl["NAME"].'">
					<option value="1"'.($arHtmlControl["VALUE"]? ' selected': '').'>'.GetMessage("MAIN_YES").'</option>
					<option value="0"'.(!$arHtmlControl["VALUE"]? ' selected': '').'>'.GetMessage("MAIN_NO").'</option>
					</select>
				';
			case "RADIO":
				return '
					<label><input type="radio" value="1" name="'.$arHtmlControl["NAME"].'"'.($arHtmlControl["VALUE"]? ' checked': '').'>'.GetMessage("MAIN_YES").'</label><br>
					<label><input type="radio" value="0" name="'.$arHtmlControl["NAME"].'"'.(!$arHtmlControl["VALUE"]? ' checked': '').'>'.GetMessage("MAIN_NO").'</label>
				';
			default:
				$arHtmlControl["VALIGN"] = "middle";
				return '
					<input type="hidden" value="0" name="'.$arHtmlControl["NAME"].'">
					<input type="checkbox" value="1" name="'.$arHtmlControl["NAME"].'"'.($arHtmlControl["VALUE"]? ' checked': '').'>
				';
		}
	}

	function GetFilterHTML($arUserField, $arHtmlControl)
	{
		return '
			<select name="'.$arHtmlControl["NAME"].'">
			<option value=""'.(strlen($arHtmlControl["VALUE"])<1? ' selected': '').'>'.GetMessage("MAIN_ALL").'</option>
			<option value="1"'.($arHtmlControl["VALUE"]? ' selected': '').'>'.GetMessage("MAIN_YES").'</option>
			<option value="0"'.(strlen($arHtmlControl["VALUE"])>0 && !$arHtmlControl["VALUE"]? ' selected': '').'>'.GetMessage("MAIN_NO").'</option>
			</select>
		';
	}

	function GetAdminListViewHTML($arUserField, $arHtmlControl)
	{
		if($arHtmlControl["VALUE"])
			return GetMessage("MAIN_YES");
		else
			return GetMessage("MAIN_NO");
	}

	function GetAdminListEditHTML($arUserField, $arHtmlControl)
	{
		return '
			<input type="hidden" value="0" name="'.$arHtmlControl["NAME"].'">
			<input type="checkbox" value="1" name="'.$arHtmlControl["NAME"].'"'.($arHtmlControl["VALUE"]? ' checked': '').'>
		';
	}

	function OnBeforeSave($arUserField, $value)
	{
		if($value)
			return 1;
		else
			return 0;
	}
}
?>