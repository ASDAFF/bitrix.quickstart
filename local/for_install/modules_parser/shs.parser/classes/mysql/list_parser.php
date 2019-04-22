<?
class ShsParserContent extends ShsParserContentGeneral
{
	function GetList($aSort=Array(), $aFilter=Array())
	{
		global $DB;
		$this->LAST_ERROR = "";
		$arFilter = array();
		if (is_array($aFilter))
		{
			foreach($aFilter as $key=>$val)
			{
                if($key!="CATEGORY_ID")if (!is_array($val) && (strlen($val)<=0 || $val=="NOT_REF"))
					continue;

				switch(strtoupper($key))
				{
				case "NAME":
					$arFilter[] = "P.NAME like '%".$val."%'";
					break;
				case "ID":
					$arFilter[] = GetFilterQuery("P.ID",$val,"N");
					break;
                case "RSS":
					$arFilter[] = "P.RSS like '%".$val."%'";
					break;
				case "TIMESTAMP_1":
					if($DB->IsDate($val))
						$arFilter[] = "P.TIMESTAMP_X>=".$DB->CharToDateFunction($val, "SHORT");
					else
						$this->LAST_ERROR .= GetMessage("PARSER_WRONG_TIMESTAMP_FROM")."<br>";
					break;
                case "TYPE":
					$arFilter[] = "P.TYPE='".$val."'";
					break;
                case "ACTIVE":
					$arFilter[] = "P.ACTIVE='".$val."'";
					break;

                case "IBLOCK_ID":
					$arFilter[] = "P.IBLOCK_ID='".$val."'";
					break;
                case "SECTION_ID":
					$arFilter[] = GetFilterQuery("P.SECTION_ID",$val,"N");
					break;
                case "CATEGORY_ID":
					$arFilter[] = "P.CATEGORY_ID='".$val."'";
					break;
                case "SELECTOR":
					$arFilter[] = GetFilterQuery("P.SELECTOR",$val,"Y");
					break;
				case "ENCODING":
					$arFilter[] = GetFilterQuery("P.ENCODING",$val,"N");
					break;
                case "START_AGENT":
					$arFilter[] = "P.START_AGENT='".$val."'";
					break;
                case "TIME_AGENT":
					$arFilter[] = "P.TIME_AGENT='".$val."'";
					break;
                case "START_LAST_TIME_1":
					if($DB->IsDate($val))
						$arFilter[] = "P.START_LAST_TIME_X>=".$DB->CharToDateFunction($val, "SHORT");
					else
						$this->LAST_ERROR .= GetMessage("PARSER_WRONG_START_LAST_TIME_FROM")."<br>";
					break;
				}
			}
		}
		$arOrder = array();
		foreach($aSort as $key => $ord)
		{
			$key = strtoupper($key);
			$ord = (strtoupper($ord) <> "ASC"? "DESC": "ASC");
			switch($key)
			{
				case "ID":		$arOrder[$key] = "P.ID ".$ord; break;
                case "TYPE":		$arOrder[$key] = "P.TYPE ".$ord; break;
				case "TIMESTAMP":	$arOrder[$key] = "P.TIMESTAMP_X ".$ord; break;
				case "NAME":		$arOrder[$key] = "P.NAME ".$ord; break;
				case "RSS":	$arOrder[$key] = "P.RSS ".$ord; break;
				case "ACTIVE":		$arOrder[$key] = "P.ACTIVE ".$ord; break;
				case "IBLOCK_ID":	$arOrder[$key] = "P.IBLOCK_ID ".$ord; break;
				case "ENCODING":	$arOrder[$key] = "P.ENCODING ".$ord; break;
                case "START_AGENT":	$arOrder[$key] = "P.START_AGENT ".$ord; break;
                case "TIME_AGENT":	$arOrder[$key] = "P.TIME_AGENT ".$ord; break;
                case "SORT":    $arOrder[$key] = "P.SORT ".$ord; break;
                case "START_LAST_TIME":	$arOrder[$key] = "P.START_LAST_TIME_X ".$ord; break;
                case "CATEGORY_ID":		$arOrder[$key] = "P.CATEGORY_ID ".$ord; break;
			}
		}
		if(count($arOrder) == 0)
			$arOrder[] = "P.ID DESC";
		$sOrder = "\nORDER BY ".implode(", ",$arOrder);
		if(count($arFilter) == 0)
			$sFilter = "";
		else
			$sFilter = "\nWHERE ".implode("\nAND ", $arFilter);
		$strSql = "
			SELECT
                 P.ID
                ,P.TYPE
				,P.NAME
				,P.RSS
				,P.SORT
				,P.ACTIVE
				,P.SECTION_ID
				,P.SELECTOR
				,P.ENCODING
                ,P.START_AGENT
                ,P.TIME_AGENT
                ,P.CATEGORY_ID
				,".$DB->DateToCharFunction("P.TIMESTAMP_X")." TIMESTAMP_X
                ,".$DB->DateToCharFunction("P.START_LAST_TIME_X")." START_LAST_TIME_X
			FROM b_shs_parser P
			".$sFilter.$sOrder;
		return $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
	}

    function GetMixedList($aSort=Array(), $arFilter=Array(), $show="all")
    {
        $filter = array();
        if($show=="all" || $show=="section")
        {
            if(isset($arFilter["CATEGORY_ID"]))
            {
                $filter["PARENT_CATEGORY_ID"] = $arFilter["CATEGORY_ID"];
            }

            $rsSection = ShsParserSectionTable::getList(array(
                'limit' =>null,
                'offset' => null,
                'select' => array("*"),
                //'order' => $aSort,
                "filter" => $filter
            ));

            while($arSection = $rsSection->Fetch())
            {
                $arSection["T"]="S";
                $arResult[]=$arSection;
            }    
        }/*else{
            $arResult[] = array();    
        }*/
        
        if($show=="all" || $show=="parser")
        {
            $cData = new ShsParserContent;
            $rsData = $cData->GetList($aSort, $arFilter);
            while($arData = $rsData->Fetch())
            {
                $arData["T"]="P";
                $arResult[] = $arData;
            }
            unset($cData);    
        }/*else{
            $arResult[] = array();    
        }*/
        $rsResult = new CDBResult;
        $rsResult->InitFromArray($arResult);

        return $rsResult;
    }
}
?>