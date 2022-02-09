<?
class CIBlockSection extends CAllIBlockSection
{
	///////////////////////////////////////////////////////////////////
	// List of sections
	///////////////////////////////////////////////////////////////////
	function GetList($arOrder=Array("SORT"=>"ASC"), $arFilter=Array(), $bIncCnt = false, $arSelect = array(), $arNavStartParams=false)
	{
		global $DB, $USER, $USER_FIELD_MANAGER;

		if(isset($arFilter["IBLOCK_ID"]) && $arFilter["IBLOCK_ID"] > 0)
		{
			$obUserFieldsSql = new CUserTypeSQL;
			$obUserFieldsSql->SetEntity("IBLOCK_".$arFilter["IBLOCK_ID"]."_SECTION", "BS.ID");
			$obUserFieldsSql->SetSelect($arSelect);
			$obUserFieldsSql->SetFilter($arFilter);
			$obUserFieldsSql->SetOrder($arOrder);
		}
		else
		{
			foreach($arFilter as $key => $val)
			{
				$res = CIBlock::MkOperationFilter($key);
				if(preg_match("/^UF_/", $res["FIELD"]))
					trigger_error("arFilter parameter of the CIBlockSection::GetList contains user fields, but has no IBLOCK_ID field.", E_USER_WARNING);
			}
		}

		$arJoinProps = array();
		$bJoinFlatProp = false;

		$arSqlSearch = CIBlockSection::GetFilter($arFilter);

		$bCheckPermissions = !array_key_exists("CHECK_PERMISSIONS", $arFilter) || $arFilter["CHECK_PERMISSIONS"]!=="N";
		$bIsAdmin = is_object($USER) && $USER->IsAdmin();
		if($bCheckPermissions && !$bIsAdmin)
			$arSqlSearch[] = CIBlockSection::_check_rights_sql($arFilter["MIN_PERMISSION"]);

		if(array_key_exists("PROPERTY", $arFilter))
		{
			$val = $arFilter["PROPERTY"];
			foreach($val as $propID=>$propVAL)
			{
				$res = CIBlock::MkOperationFilter($propID);
				$propID = $res["FIELD"];
				$cOperationType = $res["OPERATION"];
				if($db_prop = CIBlockProperty::GetPropertyArray($propID, CIBlock::_MergeIBArrays($arFilter["IBLOCK_ID"], $arFilter["IBLOCK_CODE"])))
				{

					$bSave = false;
					if(array_key_exists($db_prop["ID"], $arJoinProps))
						$iPropCnt = $arJoinProps[$db_prop["ID"]];
					elseif($db_prop["VERSION"]!=2 || $db_prop["MULTIPLE"]=="Y")
					{
						$bSave = true;
						$iPropCnt=count($arJoinProps);
					}

					if(!is_array($propVAL))
						$propVAL = Array($propVAL);

					if($db_prop["PROPERTY_TYPE"]=="N" || $db_prop["PROPERTY_TYPE"]=="G" || $db_prop["PROPERTY_TYPE"]=="E")
					{
						if($db_prop["VERSION"]==2 && $db_prop["MULTIPLE"]=="N")
						{
							$r = CIBlock::FilterCreate("FPS.PROPERTY_".$db_prop["ORIG_ID"], $propVAL, "number", $cOperationType);
							$bJoinFlatProp = $db_prop["IBLOCK_ID"];
						}
						else
							$r = CIBlock::FilterCreate("FPV".$iPropCnt.".VALUE_NUM", $propVAL, "number", $cOperationType);
					}
					else
					{
						if($db_prop["VERSION"]==2 && $db_prop["MULTIPLE"]=="N")
						{
							$r = CIBlock::FilterCreate("FPS.PROPERTY_".$db_prop["ORIG_ID"], $propVAL, "string", $cOperationType);
							$bJoinFlatProp = $db_prop["IBLOCK_ID"];
						}
						else
							$r = CIBlock::FilterCreate("FPV".$iPropCnt.".VALUE", $propVAL, "string", $cOperationType);
					}

					if(strlen($r)>0)
					{
						if($bSave)
						{
							$db_prop["iPropCnt"] = $iPropCnt;
							$arJoinProps[$db_prop["ID"]] = $db_prop;
						}
						$arSqlSearch[] = $r;
					}
				}
			}
		}

		$strSqlSearch = "";
		foreach($arSqlSearch as $r)
			if(strlen($r)>0)
				$strSqlSearch .= "\n\t\t\t\tAND  (".$r.") ";

		if(isset($obUserFieldsSql))
		{
			$r = $obUserFieldsSql->GetFilter();
			if(strlen($r)>0)
				$strSqlSearch .= "\n\t\t\t\tAND (".$r.") ";
		}

		$strProp1 = "";
		foreach($arJoinProps as $propID=>$db_prop)
		{
			if($db_prop["VERSION"]==2)
				$strTable = "b_iblock_element_prop_m".$db_prop["IBLOCK_ID"];
			else
				$strTable = "b_iblock_element_property";
			$i = $db_prop["iPropCnt"];
			$strProp1 .= "
				LEFT JOIN b_iblock_property FP".$i." ON FP".$i.".IBLOCK_ID=B.ID AND
				".(IntVal($propID)>0?" FP".$i.".ID=".IntVal($propID)." ":" FP".$i.".CODE='".$DB->ForSQL($propID, 200)."' ")."
				LEFT JOIN ".$strTable." FPV".$i." ON FP".$i.".ID=FPV".$i.".IBLOCK_PROPERTY_ID AND FPV".$i.".IBLOCK_ELEMENT_ID=BE.ID ";
		}
		if($bJoinFlatProp)
			$strProp1 .= "
				LEFT JOIN b_iblock_element_prop_s".$bJoinFlatProp." FPS ON FPS.IBLOCK_ELEMENT_ID = BE.ID
			";

		$arFields = array(
			"ID" => "BS.ID",
			"CODE" => "BS.CODE",
			"XML_ID" => "BS.XML_ID",
			"EXTERNAL_ID" => "BS.XML_ID",
			"IBLOCK_ID" => "BS.IBLOCK_ID",
			"IBLOCK_SECTION_ID" => "BS.IBLOCK_SECTION_ID",
			"TIMESTAMP_X" =>  $DB->DateToCharFunction("BS.TIMESTAMP_X"),
			"TIMESTAMP_X_UNIX"=>'UNIX_TIMESTAMP(BS.TIMESTAMP_X)',
			"SORT" => "BS.SORT",
			"NAME" => "BS.NAME",
			"ACTIVE" => "BS.ACTIVE",
			"GLOBAL_ACTIVE" => "BS.GLOBAL_ACTIVE",
			"PICTURE" => "BS.PICTURE",
			"DESCRIPTION" => "BS.DESCRIPTION",
			"DESCRIPTION_TYPE" => "BS.DESCRIPTION_TYPE",
			"LEFT_MARGIN" => "BS.LEFT_MARGIN",
			"RIGHT_MARGIN" => "BS.RIGHT_MARGIN",
			"DEPTH_LEVEL" => "BS.DEPTH_LEVEL",
			"SEARCHABLE_CONTENT" => "BS.SEARCHABLE_CONTENT",
			"MODIFIED_BY" => "BS.MODIFIED_BY",
			"DATE_CREATE" =>  $DB->DateToCharFunction("BS.DATE_CREATE"),
			"DATE_CREATE_UNIX"=>'UNIX_TIMESTAMP(BS.DATE_CREATE)',
			"CREATED_BY" => "BS.CREATED_BY",
			"DETAIL_PICTURE" => "BS.DETAIL_PICTURE",
			"TMP_ID" => "BS.TMP_ID",

			"LIST_PAGE_URL" => "B.LIST_PAGE_URL",
			"SECTION_PAGE_URL" => "B.SECTION_PAGE_URL",
			"IBLOCK_TYPE_ID" => "B.IBLOCK_TYPE_ID",
			"IBLOCK_CODE" => "B.CODE",
			"IBLOCK_EXTERNAL_ID" => "B.XML_ID",
			"SOCNET_GROUP_ID" => "BS.SOCNET_GROUP_ID",
		);

		$arSqlSelect = array();
		foreach($arSelect as $field)
		{
			$field = strtoupper($field);
			if(array_key_exists($field, $arFields))
				$arSqlSelect[$field] = $arFields[$field]." AS ".$field;
		}

		if(array_key_exists("DESCRIPTION", $arSqlSelect))
			$arSqlSelect["DESCRIPTION_TYPE"] = $arFields["DESCRIPTION_TYPE"]." AS DESCRIPTION_TYPE";

		if(array_key_exists("LIST_PAGE_URL", $arSqlSelect) || array_key_exists("SECTION_PAGE_URL", $arSqlSelect))
		{
			$arSqlSelect["ID"] = $arFields["ID"]." AS ID";
			$arSqlSelect["CODE"] = $arFields["CODE"]." AS CODE";
			$arSqlSelect["EXTERNAL_ID"] = $arFields["EXTERNAL_ID"]." AS EXTERNAL_ID";
			$arSqlSelect["IBLOCK_TYPE_ID"] = $arFields["IBLOCK_TYPE_ID"]." AS IBLOCK_TYPE_ID";
			$arSqlSelect["IBLOCK_ID"] = $arFields["IBLOCK_ID"]." AS IBLOCK_ID";
			$arSqlSelect["IBLOCK_CODE"] = $arFields["IBLOCK_CODE"]." AS IBLOCK_CODE";
			$arSqlSelect["IBLOCK_EXTERNAL_ID"] = $arFields["IBLOCK_EXTERNAL_ID"]." AS IBLOCK_EXTERNAL_ID";
			$arSqlSelect["GLOBAL_ACTIVE"] = $arFields["GLOBAL_ACTIVE"]." AS GLOBAL_ACTIVE";
			//$arr["LANG_DIR"],
		}

		if(count($arSqlSelect))
			$sSelect = implode(",\n", $arSqlSelect);
		else
			$sSelect = "
				BS.*,
				B.LIST_PAGE_URL,
				B.SECTION_PAGE_URL,
				B.IBLOCK_TYPE_ID,
				B.CODE as IBLOCK_CODE,
				B.XML_ID as IBLOCK_EXTERNAL_ID,
				BS.XML_ID as EXTERNAL_ID,
				".$DB->DateToCharFunction("BS.TIMESTAMP_X")." as TIMESTAMP_X,
				".$DB->DateToCharFunction("BS.DATE_CREATE")." as DATE_CREATE
			";

		if(!$bIncCnt)
		{
			$strSelect = $sSelect.(isset($obUserFieldsSql)? $obUserFieldsSql->GetSelect(): "");
			$strSql = "
				FROM b_iblock_section BS
					INNER JOIN b_iblock B ON BS.IBLOCK_ID = B.ID
					".(isset($obUserFieldsSql)? $obUserFieldsSql->GetJoin("BS.ID"): "")."
				".(strlen($strProp1)>0?
					"	INNER JOIN b_iblock_section BSTEMP ON BSTEMP.IBLOCK_ID = BS.IBLOCK_ID
						LEFT JOIN b_iblock_section_element BSE ON BSE.IBLOCK_SECTION_ID=BSTEMP.ID
						LEFT JOIN b_iblock_element BE ON (BSE.IBLOCK_ELEMENT_ID=BE.ID
							AND ((BE.WF_STATUS_ID=1 AND BE.WF_PARENT_ELEMENT_ID IS NULL )
							AND BE.IBLOCK_ID = BS.IBLOCK_ID
					".($arFilter["CNT_ALL"]=="Y"?" OR BE.WF_NEW='Y' ":"").")
					".($arFilter["CNT_ACTIVE"]=="Y"?
						" AND BE.ACTIVE='Y'
						AND (BE.ACTIVE_TO >= ".$DB->CurrentTimeFunction()." OR BE.ACTIVE_TO IS NULL)
						AND (BE.ACTIVE_FROM <= ".$DB->CurrentTimeFunction()." OR BE.ACTIVE_FROM IS NULL)"
					:"").")
						".$strProp1." "
				:"")."
				WHERE 1=1
				".(strlen($strProp1)>0?
					"	AND BSTEMP.LEFT_MARGIN >= BS.LEFT_MARGIN
						AND BSTEMP.RIGHT_MARGIN <= BS.RIGHT_MARGIN "
				:""
				)."
				".$strSqlSearch."
			";
			$strGroupBy = "";
		}
		else
		{
			$strSelect = $sSelect.",COUNT(DISTINCT BE.ID) as ELEMENT_CNT".(isset($obUserFieldsSql)? $obUserFieldsSql->GetSelect(): "");
			$strSql = "
				FROM b_iblock_section BS
					INNER JOIN b_iblock B ON BS.IBLOCK_ID = B.ID
					".(isset($obUserFieldsSql)? $obUserFieldsSql->GetJoin("BS.ID"): "")."
				".($arFilter["ELEMENT_SUBSECTIONS"]=="N"?
					"	LEFT JOIN b_iblock_section_element BSE ON BSE.IBLOCK_SECTION_ID=BS.ID "
				:
					"	INNER JOIN b_iblock_section BSTEMP ON BSTEMP.IBLOCK_ID = BS.IBLOCK_ID
						LEFT JOIN b_iblock_section_element BSE ON BSE.IBLOCK_SECTION_ID=BSTEMP.ID "
				)."
					LEFT JOIN b_iblock_element BE ON (BSE.IBLOCK_ELEMENT_ID=BE.ID
						AND ((BE.WF_STATUS_ID=1 AND BE.WF_PARENT_ELEMENT_ID IS NULL )
						AND BE.IBLOCK_ID = BS.IBLOCK_ID
				".($arFilter["CNT_ALL"]=="Y"?" OR BE.WF_NEW='Y' ":"").")
				".($arFilter["CNT_ACTIVE"]=="Y"?
					" AND BE.ACTIVE='Y'
					AND (BE.ACTIVE_TO >= ".$DB->CurrentTimeFunction()." OR BE.ACTIVE_TO IS NULL)
					AND (BE.ACTIVE_FROM <= ".$DB->CurrentTimeFunction()." OR BE.ACTIVE_FROM IS NULL)"
				:"").")
					".$strProp1."
				WHERE 1=1
				".($arFilter["ELEMENT_SUBSECTIONS"]=="N"
				?
					"	"
				:
					"	AND BSTEMP.IBLOCK_ID = BS.IBLOCK_ID
						AND BSTEMP.LEFT_MARGIN >= BS.LEFT_MARGIN
						AND BSTEMP.RIGHT_MARGIN <= BS.RIGHT_MARGIN "
				)."
				".$strSqlSearch."
			";
			$strGroupBy = "GROUP BY BS.ID, B.ID";
		}

		$arSqlOrder = Array();
		foreach($arOrder as $by=>$order)
		{
			$by = strtolower($by);
			if(array_key_exists($by, $arSqlOrder))
				continue;
			$order = strtolower($order);
			if($order!="asc")
				$order = "desc";

			if($by == "id") $arSqlOrder[$by] = " BS.ID ".$order." ";
			elseif($by == "section") $arSqlOrder[$by] = " BS.IBLOCK_SECTION_ID ".$order." ";
			elseif($by == "name") $arSqlOrder[$by] = " BS.NAME ".$order." ";
			elseif($by == "code") $arSqlOrder[$by] = " BS.CODE ".$order." ";
			elseif($by == "active") $arSqlOrder[$by] = " BS.ACTIVE ".$order." ";
			elseif($by == "left_margin") $arSqlOrder[$by] = " BS.LEFT_MARGIN ".$order." ";
			elseif($by == "depth_level") $arSqlOrder[$by] = " BS.DEPTH_LEVEL ".$order." ";
			elseif($by == "sort") $arSqlOrder[$by] = " BS.SORT ".$order." ";
			elseif($by == "created") $arSqlOrder[$by] = " BS.DATE_CREATE ".$order." ";
			elseif($by == "created_by") $arSqlOrder[$by] = " BS.CREATED_BY ".$order." ";
			elseif($by == "modified_by") $arSqlOrder[$by] = " BS.MODIFIED_BY ".$order." ";
			elseif($bIncCnt && $by == "element_cnt")  $arSqlOrder[$by] = " ELEMENT_CNT ".$order." ";
			elseif(isset($obUserFieldsSql) && $s = $obUserFieldsSql->GetOrder($by))  $arSqlOrder[$by] = " ".$s." ".$order." ";
			else
			{
				$by = "timestamp_x";
				$arSqlOrder[$by] = " BS.TIMESTAMP_X ".$order." ";
			}
		}

		if(count($arSqlOrder) > 0)
			$strSqlOrder = "\n\t\t\t\tORDER BY ".implode(", ", $arSqlOrder);
		else
			$strSqlOrder = "";

		if(is_array($arNavStartParams))
		{
			$res_cnt = $DB->Query("SELECT COUNT(DISTINCT BS.ID) as C ".$strSql);
			$res_cnt = $res_cnt->Fetch();
			$res = new CDBResult();
			$res->NavQuery("SELECT DISTINCT ".$strSelect.$strSql.$strGroupBy.$strSqlOrder, $res_cnt["C"], $arNavStartParams);
		}
		else
		{
			$res = $DB->Query("SELECT DISTINCT ".$strSelect.$strSql.$strGroupBy.$strSqlOrder, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		}

		$res = new CIBlockResult($res);
		if(isset($arFilter["IBLOCK_ID"]) && $arFilter["IBLOCK_ID"] > 0)
		{
			$res->SetUserFields($USER_FIELD_MANAGER->GetUserFields("IBLOCK_".$arFilter["IBLOCK_ID"]."_SECTION"));
			$res->SetIBlockTag($arFilter["IBLOCK_ID"]);
		}

		return $res;
	}
}
?>
