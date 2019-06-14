<?

IncludeModuleLangFile(__FILE__);

if (!class_exists("DefaTools_IBProp_ElemListDescr"))
{

class DefaTools_IBProp_ElemListDescr
{
		function GetUserTypeDescription()
		{
			return array(
				"PROPERTY_TYPE"			=> "E",
				"USER_TYPE"				=> "DefaToolsElemListDescr",
				"DESCRIPTION"			=> GetMessage("DEFATOOLS_PROP_NAME"),
				"GetPropertyFieldHtml"	=> array("DefaTools_IBProp_ElemListDescr","GetPropertyFieldHtml"),
			);
		}
		function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
		{
				$arItem = Array(
						"ID" => 0,
						"IBLOCK_ID" => 0,
						"NAME" => ""
				);
				if(intval($value["VALUE"]) > 0)
				{
						$arFilter = Array(
								"ID" => intval($value["VALUE"]),
								"IBLOCK_ID" => $arProperty["LINK_IBLOCK_ID"],
						);
						$rsItem = CIBlockElement::GetList(Array(), $arFilter, false, false, Array("ID", "IBLOCK_ID", "NAME"));
						$arItem = $rsItem->GetNext();
				}
				$html.=
						'<input name="'.$strHTMLControlName["VALUE"].'" id="'.$strHTMLControlName["VALUE"].'" value="'.htmlspecialcharsex($value["VALUE"]).'" size="5" type="text">'.
						'<input type="button" value="..." onClick="jsUtils.OpenWindow(\'/bitrix/admin/iblock_element_search.php?lang='.LANG.'&amp;IBLOCK_ID='.$arProperty["LINK_IBLOCK_ID"].'&amp;n='.$strHTMLControlName["VALUE"].'\', 600, 500);">'.
						'&nbsp;<input type="text" name="'.$strHTMLControlName["DESCRIPTION"].'" value="'.htmlspecialcharsex($value["DESCRIPTION"]).'" />'.
						'&nbsp;<span id="sp_'.md5($strHTMLControlName["VALUE"]).'_'.$key.'" >'.$arItem["NAME"].'</span>';

				return  $html;
		}
}

} // class exists 
?>
