<?

class CCitrusRealtyIblockPropertyList extends CUserTypeEnum
{
	function GetUserTypeDescription()
	{
		return array(
			"USER_TYPE_ID" => "iblock_elements",
			"CLASS_NAME" => __CLASS__,
			"DESCRIPTION" => "Привязка к свойствам инф. блоков",
			"BASE_TYPE" => "int",
		);
	}

	function PrepareSettings($arUserField)
	{
		$height = intval($arUserField["SETTINGS"]["LIST_HEIGHT"]);
		$disp = $arUserField["SETTINGS"]["DISPLAY"];
		if($disp!="CHECKBOX" && $disp!="LIST")
			$disp = "LIST";
		$iblock_id = intval($arUserField["SETTINGS"]["IBLOCK_ID"]);
		if($iblock_id <= 0)
			$iblock_id = "";
		return array(
			"DISPLAY" => $disp,
			"LIST_HEIGHT" => ($height < 1? 5: $height),
			"IBLOCK_ID" => $iblock_id,
		);
	}

	function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm)
	{
		if (!empty($_GET["ENTITY_ID"]) && strpos($_GET["ENTITY_ID"], "IBLOCK") !== false)
		{			
			$iblockID = preg_replace("/IBLOCK_([0-9]+)_(.*)/i", "$1", $_GET["ENTITY_ID"]);			
		}
	
		$result = '';

		if($bVarsFromForm)
			$value = $GLOBALS[$arHtmlControl["NAME"]]["IBLOCK_ID"];
		elseif(is_array($arUserField))
			$value = $arUserField["SETTINGS"]["IBLOCK_ID"];
		else
			$value = "";			
			
		$result .= '
		<tr valign="top">
			<td>Свойство:</td>
			<td>
				'.GetIBlockDropDownList($value, $arHtmlControl["NAME"].'[IBLOCK_TYPE_ID]', $arHtmlControl["NAME"].'[IBLOCK_ID]').'
			</td>
		</tr>
		';		
		
		if($bVarsFromForm)
			$value = $GLOBALS[$arHtmlControl["NAME"]]["DISPLAY"];
		elseif(is_array($arUserField))
			$value = $arUserField["SETTINGS"]["DISPLAY"];
		else
			$value = "LIST";
		$result .= '
		<tr valign="top">
			<td>Вид списка:</td>
			<td>
				<label><input type="radio" name="'.$arHtmlControl["NAME"].'[DISPLAY]" value="LIST" '.("LIST"==$value? 'checked="checked"': '').'>Список</label><br>
				<label><input type="radio" name="'.$arHtmlControl["NAME"].'[DISPLAY]" value="CHECKBOX" '.("CHECKBOX"==$value? 'checked="checked"': '').'>Флажки</label><br>
			</td>
		</tr>
		';
		if($bVarsFromForm)
			$value = intval($GLOBALS[$arHtmlControl["NAME"]]["LIST_HEIGHT"]);
		elseif(is_array($arUserField))
			$value = intval($arUserField["SETTINGS"]["LIST_HEIGHT"]);
		else
			$value = 5;
		$result .= '
		<tr valign="top">
			<td>Высота списка:</td>
			<td>
				<input type="text" name="'.$arHtmlControl["NAME"].'[LIST_HEIGHT]" size="10" value="'.$value.'">
			</td>
		</tr>
		';
		return $result;
	}

	function GetFilterHTML($arUserField, $arHtmlControl)
	{
		if(!is_array($arHtmlControl["VALUE"]))
			$arHtmlControl["VALUE"] = array();
		$result = '';
		$rsEnum = call_user_func_array(
			array($arUserField["USER_TYPE"]["CLASS_NAME"], "getlist"),
			array(
				$arUserField,
			)
		);

		if($arUserField["SETTINGS"]["LIST_HEIGHT"] < 5)
			$size = ' size="5"';
		else
			$size = ' size="'.$arUserField["SETTINGS"]["LIST_HEIGHT"].'"';		
		
		$result = '<select multiple name="'.$arHtmlControl["NAME"].'[]"'.$size.'>';
		//$result .= '<option value=""'.(!$arHtmlControl["VALUE"]? ' selected': '').'>'.GetMessage("MAIN_NO").'</option>';		
		while($arEnum = $rsEnum->GetNext())
		{
			$result .= '<option value="'.$arEnum["ID"].'"'.(in_array($arEnum["ID"], $arHtmlControl["VALUE"])? ' selected': '').'>'.$arEnum["VALUE"].'</option>';
		}
		$result .= '</select>';
		return $result;
	}

	function CheckFields($arUserField, $value)
	{
		$aMsg = array();
		return $aMsg;
	}

	function GetList($arUserField)
	{
		$rsElement = false;
		if(CModule::IncludeModule('iblock'))
		{
			$obElement = new CIBlockElementCEnum;
			$rsElement = $obElement->GetEnumList($arUserField["SETTINGS"]["IBLOCK_ID"]);			
		}
		return $rsElement;
	}
	
	
	function GetEditFormHTMLMulty($arUserField, $arHtmlControl)
	{
		
		if(($arUserField["ENTITY_VALUE_ID"]<1) && strlen($arUserField["SETTINGS"]["DEFAULT_VALUE"])>0)
			$arHtmlControl["VALUE"] = array(intval($arUserField["SETTINGS"]["DEFAULT_VALUE"]));
		elseif(!is_array($arHtmlControl["VALUE"]))
			$arHtmlControl["VALUE"] = array();

		$rsEnum = call_user_func_array(
			array($arUserField["USER_TYPE"]["CLASS_NAME"], "getlist"),
			array(
				$arUserField,
			)
		);
		if(!$rsEnum)
			return '';

		$result = '';

		if($arUserField["SETTINGS"]["DISPLAY"]=="CHECKBOX")
		{
			$result .= '<input type="hidden" value="" name="'.$arHtmlControl["NAME"].'">';
			$bWasSelect = false;
			while($arEnum = $rsEnum->GetNext())
			{
				$bSelected = (
					(in_array($arEnum["ID"], $arHtmlControl["VALUE"])) ||
					($arUserField["ENTITY_VALUE_ID"]<=0 && $arEnum["DEF"]=="Y")
				);
				$bWasSelect = $bWasSelect || $bSelected;
				$result .= '<label><input type="checkbox" value="'.$arEnum["ID"].'" name="'.$arHtmlControl["NAME"].'"'.($bSelected? ' checked': '').($arUserField["EDIT_IN_LIST"]!="Y"? ' disabled="disabled" ': '').'>'.$arEnum["VALUE"].'</label><br>';
			}
		}
		else
		{
			$result = '<select multiple name="'.$arHtmlControl["NAME"].'" size="'.$arUserField["SETTINGS"]["LIST_HEIGHT"].'"'.($arUserField["EDIT_IN_LIST"]!="Y"? ' disabled="disabled" ': ''). '>';
			while($arEnum = $rsEnum->GetNext())
			{
				if ($arEnum["ID"] == 1) $arEnum["VALUE"] = "Фото";
				$bSelected = (
					(in_array($arEnum["ID"], $arHtmlControl["VALUE"])) ||
					($arUserField["ENTITY_VALUE_ID"]<=0 && $arEnum["DEF"]=="Y")
				);
				$result .= '<option value="'.$arEnum["ID"].'"'.($bSelected? ' selected': '').'>'.$arEnum["VALUE"].'</option>';
			}
			$result .= '</select>';			
		}
		
		return $result;
	}

}


	class CIBlockElementCEnum extends CDBResult
	{
		
		function GetEnumList($IBLOCK_ID)
		{
			$rs = false;
			if(CModule::IncludeModule('iblock'))
			{
				$arFields = CIBlock::GetFields($IBLOCK_ID);				
				$arProps = array();
				$arNewFields = array(
					"DETAIL_PICTURE" => array_merge(array("ID" => 1), $arFields["DETAIL_PICTURE"]),
					"NAME" => array_merge(array("ID" => 2), $arFields["NAME"])
				);
				
				$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID));
				while($ob = $properties->GetNext())
					$arNewFields[] = $ob;					
				
				$rs2 = new CDBResult;
				$rs2->InitFromArray($arNewFields);
				$rs = new CIBlockElementCEnum($rs2);
			}			
			
			return $rs;
		}

		function GetNext($bTextHtmlAuto=true, $use_tilda=true)
		{
			$r = parent::GetNext($bTextHtmlAuto, $use_tilda);
			if($r)
				$r["VALUE"] = $r["NAME"];
			return $r;
		}
	}


?>