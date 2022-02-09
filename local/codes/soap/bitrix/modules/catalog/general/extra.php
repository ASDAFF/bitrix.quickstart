<?
IncludeModuleLangFile(__FILE__);

class CAllExtra
{
	function GetByID($ID)
	{
		global $DB;
		$strSql =
			"SELECT ID, NAME, PERCENTAGE ".
			"FROM b_catalog_extra ".
			"WHERE ID = ".intval($ID)." ";
		$db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
		if ($res = $db_res->Fetch())
			return $res;
		return false;
	}

	function SelectBox($sFieldName, $sValue, $sDefaultValue = "", $JavaChangeFunc = "", $sAdditionalParams = "")
	{
		if (!isset($GLOBALS["MAIN_EXTRA_LIST_CACHE"]) || !is_array($GLOBALS["MAIN_EXTRA_LIST_CACHE"]) || count($GLOBALS["MAIN_EXTRA_LIST_CACHE"])<1)
		{
			unset($GLOBALS["MAIN_EXTRA_LIST_CACHE"]);

			$l = CExtra::GetList(array("NAME" => "ASC"));
			while ($l_res = $l->Fetch())
			{
				$GLOBALS["MAIN_EXTRA_LIST_CACHE"][] = $l_res;
			}
		}
		$s = '<select name="'.$sFieldName.'"';
		if (!empty($JavaChangeFunc))
			$s .= ' OnChange="'.$JavaChangeFunc.'"';
		if (!empty($sAdditionalParams))
			$s .= ' '.$sAdditionalParams.' ';
		$s .= '>'."\n";
		$found = false;

		$intCount = count($GLOBALS["MAIN_EXTRA_LIST_CACHE"]);
		for ($i=0; $i < $intCount; $i++)
		{
			$found = (intval($GLOBALS["MAIN_EXTRA_LIST_CACHE"][$i]["ID"]) == intval($sValue));
			$s1 .= '<option value="'.$GLOBALS["MAIN_EXTRA_LIST_CACHE"][$i]["ID"].'"'.($found ? ' selected':'').'>'.htmlspecialcharsbx($GLOBALS["MAIN_EXTRA_LIST_CACHE"][$i]["NAME"]).' ('.htmlspecialcharsbx($GLOBALS["MAIN_EXTRA_LIST_CACHE"][$i]["PERCENTAGE"]).'%)</option>'."\n";
		}
		if (!empty($sDefaultValue))
			$s .= "<option value='' ".($found ? "" : "selected").">".htmlspecialcharsbx($sDefaultValue)."</option>";
		return $s.$s1.'</select>';
	}

	function Update($ID, $arFields)
	{
		global $DB;

		$ID = intval($ID);
		if (!CExtra::CheckFields('UPDATE', $arFields, $ID))
			return false;

		$strUpdate = $DB->PrepareUpdate("b_catalog_extra", $arFields);
		$strSql = "UPDATE b_catalog_extra SET ".$strUpdate." WHERE ID = '".intval($ID)."'";
		$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);

		if (!empty($arFields["RECALCULATE"]) && $arFields["RECALCULATE"]=="Y")
		{
			CPrice::ReCalculate("EXTRA", $ID, $arFields["PERCENTAGE"]);
		}

		unset($GLOBALS["MAIN_EXTRA_LIST_CACHE"]);
		return true;
	}

	function Delete($ID)
	{
		global $DB;
		$ID = intval($ID);
		$DB->Query("UPDATE b_catalog_price SET EXTRA_ID = NULL WHERE EXTRA_ID = ".$ID." ");
		unset($GLOBALS["MAIN_EXTRA_LIST_CACHE"]);
		return $DB->Query("DELETE FROM b_catalog_extra WHERE ID = ".$ID." ", true);
	}

	function CheckFields($strAction, &$arFields, $ID = 0)
	{
		global $APPLICATION;

		$arMsg = array();
		$boolResult = true;

		if ($strAction != 'ADD' && $strAction != 'UPDATE')
			$boolResult = false;

		$ID = intval($ID);
		if ($strAction == 'UPDATE' && $ID <= 0)
		{
			$arMsg[] = $arMsg[] = array('id' => 'ID', 'text' => GetMessage('CAT_EXTRA_ERR_UPDATE_NOT_ID'));
			$boolResult = false;
		}

		if ($boolResult)
		{
			if (isset($arFields['ID']))
			{
				if ($strAction == 'UPDATE')
				{
					unset($arFields['ID']);
				}
				else
				{
					$arFields['ID'] = intval($arFields['ID']);
					if ($arFields['ID'] <= 0)
					{
						unset($arFields['ID']);
					}
					else
					{
						$mxRes = CExtra::GetByID($arFields['ID']);
						if ($mxRes)
						{
							$arMsg[] = $arMsg[] = array('id' => 'ID', 'text' => GetMessage('CAT_EXTRA_ERR_ADD_EXISTS_ID'));
							$boolResult = false;
						}
					}
				}
			}
		}

		if ($boolResult)
		{
			$arFields["NAME"] = trim($arFields["NAME"]);
			if (empty($arFields["NAME"]))
			{
				$arMsg[] = array('id' => 'NAME', 'text' => GetMessage('CAT_EXTRA_ERROR_NONAME'));
				$boolResult = false;
			}
			if (empty($arFields["PERCENTAGE"]))
				$arFields["PERCENTAGE"] = 0;
			$arFields["PERCENTAGE"] = DoubleVal($arFields["PERCENTAGE"]);
		}

		if (!$boolResult)
		{
			if (!empty($arMsg))
			{
				$obError = new CAdminException($arMsg);
				$APPLICATION->ThrowException($obError);
			}
		}
		return $boolResult;
	}

	function PrepareInsert(&$arFields, &$intID)
	{
		global $APPLICATION;
		global $DB;

		$arMsg = array();
		$boolResult = true;

		$intID = '';
		$arFieldsList = $DB->GetTableFieldsList("b_catalog_extra");
		foreach ($arFields as $key => $value)
		{
			if (in_array($key,$arFieldsList))
			{
				if ($key == 'ID')
				{
					$intID = $value;
					unset($arFields[$key]);
				}
				else
				{
					$arFields[$key] = "'".$DB->ForSql($value)."'";
				}
			}
			else
			{
				unset($arFields[$key]);
			}
		}
		if (empty($arFields))
		{
			$arMsg[] = array('id' => 'ID', 'text' => GetMessage('CAT_EXTRA_ERR_ADD_FIELDS_EMPTY'));
			$boolResult = false;
		}

		if (!$boolResult)
		{
			if (!empty($arMsg))
			{
				$obError = new CAdminException($arMsg);
				$APPLICATION->ThrowException($obError);
			}
		}
		return $boolResult;
	}
}
?>