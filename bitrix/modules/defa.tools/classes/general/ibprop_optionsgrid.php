<?

IncludeModuleLangFile(__FILE__);

if (!class_exists("DefaTools_IBProp_OptionsGrid"))
{

	class DefaTools_IBProp_OptionsGrid
	{
		function GetUserTypeDescription()
		{
			return array(
				"PROPERTY_TYPE"			=> "S",
				"USER_TYPE"				=> "DefaToolsOptionsGrid",
				"DESCRIPTION"			=> GetMessage("DEFATOOLS_PROP_NAME"),
				"GetSettingsHTML"		=> array("DefaTools_IBProp_OptionsGrid","GetSettingsHTML"),
				"PrepareSettings"		=> array("DefaTools_IBProp_OptionsGrid","PrepareSettings"),
				"ConvertFromDB"			=> array("DefaTools_IBProp_OptionsGrid","ConvertFromDB"),
				"ConvertToDB"			=> array("DefaTools_IBProp_OptionsGrid","ConvertToDB"),
				"GetPropertyFieldHtml"	=> array("DefaTools_IBProp_OptionsGrid","GetPropertyFieldHtml"),
				"GetPublicEditHTML"		=> array("DefaTools_IBProp_OptionsGrid","GetPublicEditHTML"),
				"GetPublicViewHTML"		=> array("DefaTools_IBProp_OptionsGrid","GetPublicViewHTML"),
			);
		}
		function CheckProperty(&$arFields)
		{
			global $APPLICATION;

			$arDescrip = self::GetUserTypeDescription();
			if($arFields["USER_TYPE"] == $arDescrip["USER_TYPE"])
			{
				if($arFields["MULTIPLE"] == "Y")
				{
					$APPLICATION->throwException(GetMessage("DEFATOOLS_ERR_MULTIPLE", array('#DESCRIP#' => $arDescrip["DESCRIPTION"])));

					return false;
				}
			}
		}
		function DeleteEnums($ID)
		{
			$arDescrip = self::GetUserTypeDescription();
			$rs = CIBlockProperty::GetList(array(), array("ID" => intval($ID), "USER_TYPE" => $arDescrip["USER_TYPE"]));
			if($ar = $rs->Fetch())
			{
				$arDBEnums = self::GetAllDBEnums($ar);
				foreach($arDBEnums as $e)
					CIBlockPropertyEnum::Delete($e["ID"]);
			}
		}
		function __GetAllPrefix()
		{
			return array(
				"ROW",
				"CELL",
				"ENUM"
			);
		}
		function OnAfterPropertyAdd($arFields)
		{
			$arDescrip = self::GetUserTypeDescription();
			if($arFields["USER_TYPE"] == $arDescrip["USER_TYPE"] && $arFields["ID"])
			{
				$ip = new CIBlockProperty();
				$ip->Update($arFields["ID"], array("NAME" => $arFields["NAME"]));
			}
		}
		function SetEnums(&$arFields)	 // сохраняет значения параметров в CIBlockPropertyEnum
		{
			$arDescrip = self::GetUserTypeDescription();
			if(!is_set($arFields, "USER_TYPE"))
				$arFields = CIBlockProperty::GetByID($arFields["ID"])->Fetch();
			if($arFields["USER_TYPE"] == $arDescrip["USER_TYPE"])
			{
				if(!is_array($arFields["USER_TYPE_SETTINGS"]))
					$arFields["USER_TYPE_SETTINGS"] = unserialize($arFields["USER_TYPE_SETTINGS"]);
				if(!is_array($arFields["USER_TYPE_SETTINGS"]))
					$arFields["USER_TYPE_SETTINGS"] = array();
				$arTmp = array();
				$arPx = self::__GetAllPrefix();
				foreach($arPx as $px)
				{
					$arrTmp = self::GetFieldArray($arFields, $px);
					foreach($arrTmp as $k => $v)
					{
						if(empty($v["XML_ID"])) $v["XML_ID"] = md5(uniqid(""));
						$arrTmp[$k]["XML_ID"] = $px.'_'.$v["XML_ID"];
					}
					$arTmp = array_merge($arTmp, $arrTmp);
				}

				$arIDs = array();
				$arDBEnums = self::GetAllDBEnums($arFields);
				foreach($arDBEnums as $e)
					$arIDs[$e["ID"]] = $e["ID"];

				$arUpd = array();
				$arValues = array();
				foreach($arTmp as $arRow)
				{
					$arRow["PROPERTY_ID"] = $arFields["ID"];

					$ibpenum = new CIBlockPropertyEnum;
					if(array_key_exists($arRow["ID"], $arIDs))
					{
						$ibpenum->Update($arRow["ID"], $arRow);
						$arUpd[] = $arRow["ID"];
						$arValues[$arRow["ID"]] = $ibpenum->GetById($arRow["ID"]);
					}
					else
					{
						$enumID = $ibpenum->Add($arRow);
						$arValues[$enumID] = $arRow;
					}	
				}

				foreach($arDBEnums as $e)
				{
					if(!in_array($e["ID"], $arUpd))
					{
						CIBlockPropertyEnum::Delete($e["ID"]);
					}
				}

				$arSettings = self::PrepareSettings(array("USER_TYPE_SETTINGS" => array())); // в USER_TYPE_SETTINGS остануться только необходимые значения
				foreach($arSettings as $k => $v)
					$arSettings[$k] = $arFields["USER_TYPE_SETTINGS"][$k];

				$arFields["VALUES"] = $arValues;

				$arFields["USER_TYPE_SETTINGS"] = $arSettings;
			}
		}

		function GetAllDBEnums($arProperty, $px="")
		{
			$arResult = array();

			$arFilter = array("PROPERTY_ID" => intval($arProperty["ID"]));
			if(!empty($px))
				$arFilter["XML_ID"] = $px.'_%';

			$arPx = self::__GetAllPrefix();
			$rs = CIBlockPropertyEnum::GetList(array("ID"=> "ASC"), $arFilter);
			while($ar = $rs->Fetch())
			{
				foreach($arPx as $px)
					$ar["XML_ID"] = preg_replace("/^(".$px."_)/i", "", $ar["XML_ID"]);
				$arResult[] = $ar;
			}

			return $arResult;
		}
		function GetFieldArray($arProperty, $px="")
		{
			$arResult = array();
			if(is_array($arProperty["USER_TYPE_SETTINGS"]))
			{
				for($i = 0; $i <= 50; $i++)
					if(
						array_key_exists($px."_VALUE__N".$i."_", $arProperty["USER_TYPE_SETTINGS"])
						&& !empty($arProperty["USER_TYPE_SETTINGS"][$px."_VALUE__N".$i."_"])
					)
					{
						$id = $arProperty["USER_TYPE_SETTINGS"][$px."_ID__N".$i."_"];
						if(empty($id)) $id = 'n'.($i+1);

						$arResult[] = array(
							"ID" => $id,
							"XML_ID" => $arProperty["USER_TYPE_SETTINGS"][$px."_XML__N".$i."_"],
							"VALUE" => str_replace(array("&lt;", "&gt;", "&quot;"), array("<", ">", "\""), $arProperty["USER_TYPE_SETTINGS"][$px."_VALUE__N".$i."_"])
						);
					}
			}
			if(empty($arResult))
				$arResult = self::GetAllDBEnums($arProperty, $px);

			return $arResult;

		}
		function GetRows($arProperty)
		{
			return self::GetFieldArray($arProperty, "ROW");
		}
		function GetCells($arProperty)
		{
			return self::GetFieldArray($arProperty, "CELL");
		}
		function GetEnums($arProperty)
		{
			return self::GetFieldArray($arProperty, "ENUM");
		}
		function GetTypes()
		{
			return array(
				"LIST" => GetMessage("DEFATOOLS_TYPE_LIST"),
				"STRING" => GetMessage("DEFATOOLS_TYPE_STRING")
			);
		}
		function initJS()
		{
			static $bInit = false;
			if($bInit) return true;

			$bInit = true;

			?><script language="JavaScript">
			<!--
			function addNewRow(tableID)
			{
				var tbl = document.getElementById(tableID);
				var cnt = tbl.rows.length;
				var oRow = tbl.insertRow(cnt-1);

				for (var cell = 0; cell < tbl.rows[cnt-2].cells.length; cell++)
				{
					var oCell = oRow.insertCell(cell);
					var sHTML=tbl.rows[cnt-2].cells[cell].innerHTML;

					var p = 0;
					while(true)
					{
						var s = sHTML.indexOf('__N',p);
						if(s<0)break;
						var e = sHTML.indexOf('_',s+3);
						if(e<0)break;
						var n = parseInt(sHTML.substr(s+3,e-s));
						sHTML = sHTML.substr(0, s)+'__N'+(++n)+'_'+sHTML.substr(e+1);

						p=e+1;
					}
					//alert(sHTML);
					oCell.innerHTML = sHTML;

					var patt = new RegExp ("<"+"script"+">[^\000]*?<"+"\/"+"script"+">", "ig");
					var code = sHTML.match(patt);
					if(code)
					{
						for(var i = 0; i < code.length; i++)
						{
							if(code[i] != '')
							{
								var s = code[i].substring(8, code[i].length-9);
								jsUtils.EvalGlobal(s);
							}
						}
					}
				}
			}
			//-->
		</script><?

		}
		function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
		{
			$arPropertyFields = array(
				"HIDE" => array("SEARCHABLE", "FILTRABLE", "ROW_COUNT", "COL_COUNT", "DEFAULT_VALUE", "WITH_DESCRIPTION", "MULTIPLE_CNT"), //will hide the field
				"SET" => array("SEARCHABLE" => "N", "FILTRABLE" => "N"), //if set then hidden field will get this value
				"USER_TYPE_SETTINGS_TITLE" => GetMessage("DEFATOOLS_USER_TYPE_SETTINGS_TITLE")
			);

			if(!is_array($arFields["USER_TYPE_SETTINGS"]))
				$arFields["USER_TYPE_SETTINGS"] = array();

			$arFields = self::GetRows($arProperty);

			$rows = count($arFields);
			if(!$rows) $rows = 1;

			ob_start();

			self::initJS();

			$arTypes = self::GetTypes();

			?><tr>
			<td><?=GetMessage("DEFATOOLS_ANSWER_TYPE")?>:</td>
			<td>
				<select name="<? echo $strHTMLControlName["NAME"] ?>[TYPE]"><?

					foreach($arTypes as $k => $v)
					{
						?><option value="<? echo $k ?>"<?if($arProperty["USER_TYPE_SETTINGS"]["TYPE"] == $k) echo ' selected="selected"'?>><? echo $v ?></option><?
					}

					?></select>
			</td>
		</tr>

		<tr>
			<td><?=GetMessage("DEFATOOLS_ANSWERS_LIST_TYPE")?></td>
			<td>
				<table cellpadding="3" cellspacing="1" border="0" class="internal" width="350">
					<tr>
						<td width="40%"><?=GetMessage("DEFATOOLS_MULTIPLE_TYPE")?>:</td>
						<td><input type="checkbox" name="<? echo $strHTMLControlName["NAME"] ?>[MULTIPLE]" value="Y"<?if($arProperty["USER_TYPE_SETTINGS"]["MULTIPLE"] == "Y") echo ' checked="checked"'?>/></td>
					<tr>
				</table>
			</td>
		</tr>

		<tr>
			<td><?=GetMessage("DEFATOOLS_ANSWERS_STRING_TYPE")?></td>
			<td>
				<table cellpadding="3" cellspacing="1" border="0" class="internal" width="350">
					<tr>
						<td width="40%"><?=GetMessage("DEFATOOLS_INPUT_SIZE")?>:</td>
						<td><input type="text" size="5" name="<? echo $strHTMLControlName["NAME"] ?>[SIZE]" value="<? echo htmlspecialchars($arProperty["USER_TYPE_SETTINGS"]["SIZE"]) ?>" maxlength="5" /></td>
					<tr>
				</table>
			</td>
		</tr>
		<?
			$arAligns = array(
				"left" => GetMessage("DEFATOOLS_ALIGN_LEFT"),
				"right" => GetMessage("DEFATOOLS_ALIGN_RIGHT"),
				"center" => GetMessage("DEFATOOLS_ALIGN_CENTER"),
			);
			?>
		<tr>
			<td><?=GetMessage("DEFATOOLS_ALIGN_IN_CELLS")?>:</td>
			<td>
				<select name="<? echo $strHTMLControlName["NAME"] ?>[ALIGN]"><?

					foreach($arAligns as $k => $v)
					{
						?><option value="<? echo $k ?>"<?if($arProperty["USER_TYPE_SETTINGS"]["ALIGN"] == $k) echo ' selected="selected"'?>><? echo $v ?></option><?
					}

					?></select>
			</td>
		</tr>

		<tr>
			<td></td>
			<td></td>
		</tr>

		<tr>
			<td><?=GetMessage("DEFATOOLS_ROWS")?>:</td>
			<td>
				<table class="internal" cellspacing="0" cellpadding="0" border="0" id="qtb<? echo md5($strHTMLControlName["NAME"]) ?>">
					<tr class="heading">
						<td width="10%">ID</td>
						<td>XML ID</td>
						<td><?=GetMessage("DEFATOOLS_VALUE")?></td>
					</tr>
					<?

					for ($i = 0;$i <= $rows; $i++)
					{
						?><tr>
						<td><input type="hidden" size="15" name="<? echo $strHTMLControlName["NAME"] ?>[ROW_ID__N<? echo $i ?>_]" value="<? echo htmlspecialchars($arFields[$i]["ID"]) ?>" /><? echo htmlspecialchars($arFields[$i]["ID"]) ?></td>
						<td><input type="text" size="15" name="<? echo $strHTMLControlName["NAME"] ?>[ROW_XML__N<? echo $i ?>_]" value="<? echo htmlspecialchars($arFields[$i]["XML_ID"]) ?>" maxlength="200" /></td>
						<td><input type="text" size="40" name="<? echo $strHTMLControlName["NAME"] ?>[ROW_VALUE__N<? echo $i ?>_]" value="<? echo htmlspecialchars($arFields[$i]["VALUE"]) ?>"></td>
					</tr><?
					}

					?><tr><td colspan="3" align="center"><input type="button" value="<?=GetMessage("DEFATOOLS_PROP_LIST_MORE") ?>" onclick="addNewRow('qtb<? echo md5($strHTMLControlName["NAME"]) ?>')" /></td></tr>

				</table>
			</td>

		</tr>
		<?

			$arFields = self::GetCells($arProperty);

			$rows = count($arFields);
			if(!$rows) $rows = 1;


			?>
		<tr>

			<td><?=GetMessage("DEFATOOLS_COLS")?>:</td>
			<td>
				<table class="internal" cellspacing="0" cellpadding="0" border="0" id="atb<? echo md5($strHTMLControlName["NAME"]) ?>">
					<tr class="heading">
						<td width="10%">ID</td>
						<td>XML ID</td>
						<td><?=GetMessage("DEFATOOLS_VALUE")?></td>
					</tr><?

					for ($i = 0;$i <= $rows; $i++)
					{
						?><tr>
						<td><input type="hidden" size="15" name="<? echo $strHTMLControlName["NAME"] ?>[CELL_ID__N<? echo $i ?>_]" value="<? echo htmlspecialchars($arFields[$i]["ID"]) ?>" /><? echo htmlspecialchars($arFields[$i]["ID"]) ?></td>
						<td><input type="text" size="15" name="<? echo $strHTMLControlName["NAME"] ?>[CELL_XML__N<? echo $i ?>_]" value="<? echo htmlspecialchars($arFields[$i]["XML_ID"]) ?>" maxlength="200" /></td>
						<td><input type="text" size="40" name="<? echo $strHTMLControlName["NAME"] ?>[CELL_VALUE__N<? echo $i ?>_]" value="<? echo htmlspecialchars($arFields[$i]["VALUE"]) ?>"></td>
					</tr><?
					}

					?><tr><td colspan="3" align="center"><input type="button" value="<? echo GetMessage("DEFATOOLS_PROP_LIST_MORE") ?>" onclick="addNewRow('atb<? echo md5($strHTMLControlName["NAME"]) ?>')" /></td></tr>

				</table>
			</td>
		</tr><?


			if(class_exists("CIBlockCustomPropTemplate"))
			{
				CIBlockCustomPropTemplate::GetSettingsHTML($arProperty, $strHTMLControlName);
			}


			$html .= ob_get_contents();
			ob_end_clean();

			return $html;
		}
		function PrepareSettings($arFields)
		{
			$arTypes = self::GetTypes();

			if(
				!isset($arFields["USER_TYPE_SETTINGS"]["TYPE"])
				|| !array_key_exists($arFields["USER_TYPE_SETTINGS"]["TYPE"], $arTypes))
			{
				$arFields["USER_TYPE_SETTINGS"]["TYPE"] = "LIST";
			}
			$arFields["USER_TYPE_SETTINGS"]["SIZE"] = intval($arFields["USER_TYPE_SETTINGS"]["SIZE"]);
			if(!$arFields["USER_TYPE_SETTINGS"]["SIZE"])
				$arFields["USER_TYPE_SETTINGS"]["SIZE"] = 5;

			$arFields["USER_TYPE_SETTINGS"]["ALIGN"] = trim($arFields["USER_TYPE_SETTINGS"]["ALIGN"]);
			if(empty($arFields["USER_TYPE_SETTINGS"]["ALIGN"]))
				$arFields["USER_TYPE_SETTINGS"]["ALIGN"] = 'center';

			$arFields["USER_TYPE_SETTINGS"]["MULTIPLE"] = $arFields["USER_TYPE_SETTINGS"]["MULTIPLE"] == "Y" ? "Y" : "N";

			$arFields["USER_TYPE_SETTINGS"]["THEME"] = trim($arFields["USER_TYPE_SETTINGS"]["THEME"]);

			return $arFields["USER_TYPE_SETTINGS"];
		}
		function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
		{
			$arSettings = $arProperty["USER_TYPE_SETTINGS"];

			$arQuestions = self::GetRows($arProperty);
			if(empty($arQuestions)) return "";

			$arAnswers = self::GetCells($arProperty);

			ob_start();

			?><table cellpadding="3" cellspacing="1" border="0" class="internal">

			<tr class="heading">

				<td>&nbsp;</td><?

				foreach($arAnswers as $arAnswer)
				{
					?><td><? echo (CFile::IsImage($arAnswer["VALUE"]) ? CFile::ShowImage($arAnswer["VALUE"], 100, 100) : $arAnswer["VALUE"]);  ?></td><?
				}

				?></tr><?

			foreach($arQuestions as $arQuestion)
			{

				?><tr>
				<td><? echo (CFile::IsImage($arQuestion["VALUE"]) ? CFile::ShowImage($arQuestion["VALUE"], 100, 100) : $arQuestion["VALUE"]);  ?></td><?

				for($i = 0; $i < count($arAnswers); $i++)
				{
					?><td align="<? echo $arSettings["ALIGN"] ?>"><?

					switch($arSettings["TYPE"])
					{
						case "LIST":

							if($arSettings["MULTIPLE"] == "Y")
							{
								?><input type="checkbox" name="<? echo $strHTMLControlName["VALUE"].'['.$arQuestion["ID"].']'; ?>[]" value="<? echo $arAnswers[$i]["ID"]; ?>"<?if(is_array($value["VALUE"][$arQuestion["ID"]]) && in_array($arAnswers[$i]["ID"], $value["VALUE"][$arQuestion["ID"]])) echo ' checked="checked"'?>><?
							}
							else
							{
								?><input type="radio" name="<? echo $strHTMLControlName["VALUE"].'['.$arQuestion["ID"].']'; ?>" value="<? echo $arAnswers[$i]["ID"]; ?>"<?if(isset($value["VALUE"][$arQuestion["ID"]]) && $value["VALUE"][$arQuestion["ID"]] == $arAnswers[$i]["ID"]) echo ' checked="checked"'?>><?
							}

							break;
						case "STRING":

							?><input type="text" name="<? echo $strHTMLControlName["VALUE"].'['.$arQuestion["ID"].']['.$arAnswers[$i]["ID"].']'; ?>" value="<? echo htmlspecialchars($value["VALUE"][$arQuestion["ID"]][$arAnswers[$i]["ID"]]) ?>" size="<? echo $arSettings["SIZE"] ?>" /><?

							break;
					}
					?></td><?
				}

				?></tr><?
			}

			?></table><?

			$html .= ob_get_contents();
			ob_end_clean();

			return $html;

		}
		function GetPublicEditHTML($arProperty, $value, $strHTMLControlName)
		{
			if(isset($arProperty["USER_TYPE_SETTINGS"]["THEME"]) && !empty($arProperty["USER_TYPE_SETTINGS"]["THEME"]))
			{
				return CIBlockCustomPropTemplate::GetHTML($arProperty["USER_TYPE_SETTINGS"]["THEME"], $arProperty, $value, $strHTMLControlName);
			}
			else
			{
				return self::GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName);
			}
		}
		function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
		{
			return implode(" | ", $value["VALUE"]);
		}
		function ConvertFromDB($arProperty, $value)
		{
			$value["VALUE"] = unserialize($value["VALUE"]);
			if(!is_array($value["VALUE"]))
				$value["VALUE"] = array();

			return $value;
		}
		function ConvertToDB($arProperty, $value)
		{
			if(!is_array($value["VALUE"]))
				$value["VALUE"] = array();

			foreach($value["VALUE"] as $k => $v)
			{
				if(
					($arProperty["USER_TYPE_SETTINGS"]["TYPE"] == "LIST" && $arProperty["USER_TYPE_SETTINGS"]["MULTIPLE"] == "Y")
					|| ($arProperty["USER_TYPE_SETTINGS"]["TYPE"] == "STRING")
				)
				{
					if(!is_array($v)) $v = array();
					foreach($v as $kk => $vv)
						if(trim($vv) == "")
							unset($v[$kk]);

					$value["VALUE"][$k] = $v;
				}
				else
				{
					if(trim($v) == "")
						unset($value["VALUE"][$k]);
				}
			}

			$value["VALUE"] = serialize($value["VALUE"]);

			return $value;
		}
	}
} // class exists
?>