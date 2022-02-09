<?
class CIBlockPropertyEnum
{
	function GetList($arOrder=Array("SORT"=>"ASC", "VALUE"=>"ASC"), $arFilter=Array())
	{
		global $DB;
		$arSqlSearch = Array();
		$filter_keys = array_keys($arFilter);
		for($i=0; $i<count($filter_keys); $i++)
		{
			$val = $arFilter[$filter_keys[$i]];
			$key = $filter_keys[$i];
			if($key[0]=="!")
			{
				$key = substr($key, 1);
				$bInvert = true;
			}
			else
				$bInvert = false;

			$key = strtoupper($key);
			switch($key)
			{
			case "CODE":
				$arSqlSearch[] = CIBlock::FilterCreate("P.CODE", $val, "string", $bInvert);
				break;
			case "IBLOCK_ID":
				$arSqlSearch[] = CIBlock::FilterCreate("P.IBLOCK_ID", $val, "number", $bInvert);
				break;
			case "DEF":
				$arSqlSearch[] = CIBlock::FilterCreate("BEN.DEF", $val, "string_equal", $bInvert);
				break;
			case "EXTERNAL_ID":
				$arSqlSearch[] = CIBlock::FilterCreate("BEN.XML_ID", $val, "string_equal", $bInvert);
				break;
			case "VALUE": case "XML_ID": case "TMP_ID":
				$arSqlSearch[] = CIBlock::FilterCreate("BEN.".$key, $val, "string", $bInvert);
				break;
			case "PROPERTY_ID":
				if(is_numeric(substr($val, 0, 1)))
					$arSqlSearch[] = CIBlock::FilterCreate("P.ID", $val, "number", $bInvert);
				else
					$arSqlSearch[] = CIBlock::FilterCreate("P.CODE", $val, "string", $bInvert);
				break;
			case "ID": case "SORT":
				$arSqlSearch[] = CIBlock::FilterCreate("BEN.".$key, $val, "number", $bInvert);
				break;
			}
		}

		$strSqlSearch = "";
		for($i=0; $i<count($arSqlSearch); $i++)
			if(strlen($arSqlSearch[$i])>0)
				$strSqlSearch .= " AND  (".$arSqlSearch[$i].") ";

		$arSqlOrder = Array();
		foreach($arOrder as $by=>$order)
		{
			$by = strtolower($by);
			$order = strtolower($order);
			if ($order!="asc")
				$order = "desc";

			if ($by == "id")				$arSqlOrder[] = " BEN.ID ".$order." ";
			elseif ($by == "property_id")	$arSqlOrder[] = " BEN.PROPERTY_ID ".$order." ";
			elseif ($by == "value")			$arSqlOrder[] = " BEN.VALUE ".$order." ";
			elseif ($by == "xml_id")		$arSqlOrder[] = " BEN.XML_ID ".$order." ";
			elseif ($by == "external_id")	$arSqlOrder[] = " BEN.XML_ID ".$order." ";
			elseif ($by == "property_sort")	$arSqlOrder[] = " P.SORT ".$order." ";
			elseif ($by == "property_code")	$arSqlOrder[] = " P.CODE ".$order." ";
			elseif ($by == "def")			$arSqlOrder[] = " BEN.DEF ".$order." ";
			else
			{
				$arSqlOrder[] = "  BEN.SORT ".$order." ";
				$by = "sort";
			}
		}

		$arSqlOrder = array_unique($arSqlOrder);
		$strSqlOrder = "";
		DelDuplicateSort($arSqlOrder); for ($i=0; $i<count($arSqlOrder); $i++)
		{
			if($i==0)
				$strSqlOrder = " ORDER BY ";
			else
				$strSqlOrder .= ",";

			$strSqlOrder .= $arSqlOrder[$i];
		}

		$strSql = "
			SELECT
				BEN.*,
				BEN.XML_ID as EXTERNAL_ID,
				P.NAME as PROPERTY_NAME,
				P.CODE as PROPERTY_CODE,
				P.SORT as PROPERTY_SORT
			FROM
				b_iblock_property_enum BEN,
				b_iblock_property P
			WHERE
				BEN.PROPERTY_ID=P.ID
			$strSqlSearch
			$strSqlOrder
			";
		//echo "<pre>".htmlspecialcharsbx($strSql)."</pre>";
		$rs = $DB->Query($strSql, false, "FILE: ".__FILE__."<br> LINE: ".__LINE__);
		return $rs;
	}

	function Add($arFields)
	{
		global $DB;

		if(strlen($arFields["VALUE"])<=0)
			return false;

		if(CACHED_b_iblock_property_enum !== false)
			$GLOBALS["CACHE_MANAGER"]->CleanDir("b_iblock_property_enum");

		if(is_set($arFields, "DEF") && $arFields["DEF"]!="Y")
			$arFields["DEF"]="N";

		if(is_set($arFields, "EXTERNAL_ID"))
			$arFields["XML_ID"] = $arFields["EXTERNAL_ID"];

		if(!is_set($arFields, "XML_ID"))
			$arFields["XML_ID"] = md5(uniqid(""));


		unset($arFields["ID"]);

		$ID = $DB->Add("b_iblock_property_enum", $arFields);

		return $ID;
	}

	function Update($ID, $arFields)
	{
		global $DB;
		$ID = IntVal($ID);

		if(is_set($arFields, "VALUE") && strlen($arFields["VALUE"])<=0)
			return false;

		if(CACHED_b_iblock_property_enum !== false)
			$GLOBALS["CACHE_MANAGER"]->CleanDir("b_iblock_property_enum");

		if(is_set($arFields, "EXTERNAL_ID"))
			$arFields["XML_ID"] = $arFields["EXTERNAL_ID"];

		if(is_set($arFields, "DEF") && $arFields["DEF"]!="Y")
			$arFields["DEF"]="N";

		$strUpdate = $DB->PrepareUpdate("b_iblock_property_enum", $arFields);
		if(strlen($strUpdate) > 0)
			$DB->Query("UPDATE b_iblock_property_enum SET ".$strUpdate." WHERE ID=".$ID);

		return true;
	}

	function DeleteByPropertyID($PROPERTY_ID, $bIgnoreError=false)
	{
		global $DB, $CACHE_MANAGER;

		if(CACHED_b_iblock_property_enum !== false)
			$CACHE_MANAGER->CleanDir("b_iblock_property_enum");

		return $DB->Query("
			DELETE FROM b_iblock_property_enum
			WHERE PROPERTY_ID=".IntVal($PROPERTY_ID)."
			", $bIgnoreError
		);
	}

	function Delete($ID)
	{
		global $DB, $CACHE_MANAGER;

		if(CACHED_b_iblock_property_enum !== false)
			$CACHE_MANAGER->CleanDir("b_iblock_property_enum");

		$DB->Query("
			DELETE FROM b_iblock_property_enum
			WHERE ID=".IntVal($ID)."
			"
		);

		return true;
	}

	function GetByID($ID)
	{
		global $DB, $CACHE_MANAGER;
		static $BX_IBLOCK_ENUM_CACHE = array();

		$ID=intval($ID);

		if(!array_key_exists($ID, $BX_IBLOCK_ENUM_CACHE))
		{
			if(CACHED_b_iblock_property_enum===false)
			{
				$rs = $DB->Query("SELECT * from b_iblock_property_enum WHERE ID=".$ID);
				$BX_IBLOCK_ENUM_CACHE[$ID] = $rs->Fetch();
			}
			else
			{
				$bucket_size = intval(CACHED_b_iblock_property_enum_bucket_size);
				if($bucket_size<=0) $bucket_size = 10;

				$bucket = intval($ID/$bucket_size);
				if($CACHE_MANAGER->Read(CACHED_b_iblock_property_enum, $cache_id="b_iblock_property_enum".$bucket, "b_iblock_property_enum"))
				{
					$arEnums = $CACHE_MANAGER->Get($cache_id);
				}
				else
				{
					$arEnums = array();
					$rs = $DB->Query("
						SELECT *
						FROM b_iblock_property_enum
						WHERE ID between ".($bucket*$bucket_size)." AND ".(($bucket+1)*$bucket_size-1)
					);
					while($ar = $rs->Fetch())
						$arEnums[$ar["ID"]]=$ar;
					$CACHE_MANAGER->Set($cache_id, $arEnums);
				}
				$max = ($bucket+1)*$bucket_size;
				for($i=$bucket*$bucket_size;$i<$max;$i++)
				{
					if(array_key_exists($i, $arEnums))
						$BX_IBLOCK_ENUM_CACHE[$i]=$arEnums[$i];
					else
						$BX_IBLOCK_ENUM_CACHE[$i]=false;
				}
			}
		}

		return $BX_IBLOCK_ENUM_CACHE[$ID];
	}
}
?>