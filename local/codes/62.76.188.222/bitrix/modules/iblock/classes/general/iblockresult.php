<?
class CIBlockResult extends CDBResult
{
	var $arIBlockMultProps=false;
	var $arIBlockConvProps=false;
	var $arIBlockAllProps =false;
	var $arIBlockNumProps =false;
	var $arIBlockLongProps = false;

	var $nInitialSize;
	var $table_id;
	var $strDetailUrl = false;
	var $strSectionUrl = false;
	var $strListUrl = false;
	var $arSectionContext = false;

	var $_LAST_IBLOCK_ID = "";
	var $_FILTER_IBLOCK_ID = array();

	function CIBlockResult($res)
	{
		parent::CDBResult($res);
	}

	function SetUrlTemplates($DetailUrl = "", $SectionUrl = "", $ListUrl = "")
	{
		$this->strDetailUrl = $DetailUrl;
		$this->strSectionUrl = $SectionUrl;
		$this->strListUrl = $ListUrl;
	}

	function SetSectionContext($arSection)
	{
		if(is_array($arSection) && array_key_exists("ID", $arSection))
		{
			$this->arSectionContext = array(
				"ID" => intval($arSection["ID"]) > 0? intval($arSection["ID"]): "",
				"CODE" => urlencode(isset($arSection["~CODE"])? $arSection["~CODE"]: $arSection["CODE"]),
			);
		}
		else
		{
			$this->arSectionContext = false;
		}
	}

	function SetIBlockTag($iblock_id)
	{
		if(is_array($iblock_id))
		{
			foreach($iblock_id as $id)
			{
				if(!is_array($id))
				{
					$id = intval($id);
					if($id > 0)
						$this->_FILTER_IBLOCK_ID[$id] = true;
				}
			}
		}
		else
		{
			$id = intval($iblock_id);
			if($id > 0)
				$this->_FILTER_IBLOCK_ID[$id] = true;
		}
	}

	function Fetch()
	{
		global $DB;
		$res = parent::Fetch();

		if(!is_object($this))
			return $res;

		$arUpdate = array();
		if($res)
		{
			if(is_array($this->arIBlockLongProps))
			{
				foreach($res as $k=>$v)
				{
					if(preg_match("#^ALIAS_(\d+)_(.*)$#", $k, $match))
					{
						$res[$this->arIBlockLongProps[$match[1]].$match[2]] = $v;
						unset($res[$k]);
					}
				}
			}

			if(
				isset($res["IBLOCK_ID"])
				&& defined("BX_COMP_MANAGED_CACHE")
				&& $res["IBLOCK_ID"] != $this->_LAST_IBLOCK_ID
			)
			{
				$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$res["IBLOCK_ID"]);
				$this->_LAST_IBLOCK_ID = $res["IBLOCK_ID"];
			}

			if(isset($res["ID"]) && $res["ID"] != "" && is_array($this->arIBlockMultProps))
			{
				foreach($this->arIBlockMultProps as $field_name => $db_prop)
				{
					if(array_key_exists($field_name, $res))
					{
						if(is_object($res[$field_name]))
							$res[$field_name]=$res[$field_name]->load();

						if(preg_match("/(_VALUE)$/", $field_name))
							$descr_name = preg_replace("/(_VALUE)$/", "_DESCRIPTION", $field_name);
						else
							$descr_name = preg_replace("/^(PROPERTY_)/", "DESCRIPTION_", $field_name);

						if(strlen($res[$field_name]) <= 0)
						{
							$strSql = "
								SELECT VALUE,DESCRIPTION
								FROM b_iblock_element_prop_m".$db_prop["IBLOCK_ID"]."
								WHERE
									IBLOCK_ELEMENT_ID = ".intval($res["ID"])."
									AND IBLOCK_PROPERTY_ID = ".intval($db_prop["ORIG_ID"])."
								ORDER BY ID
							";
							$rs = $DB->Query($strSql);
							$res[$field_name] = array();
							$res[$descr_name] = array();
							while($ar=$rs->Fetch())
							{
								$res[$field_name][]=$ar["VALUE"];
								$res[$descr_name][]=$ar["DESCRIPTION"];
							}
							$arUpdate["b_iblock_element_prop_s".$db_prop["IBLOCK_ID"]]["PROPERTY_".$db_prop["ORIG_ID"]] = serialize(array("VALUE"=>$res[$field_name],"DESCRIPTION"=>$res[$descr_name]));
						}
						else
						{
							$tmp = unserialize($res[$field_name]);
							$res[$field_name] = $tmp["VALUE"];
							$res[$descr_name] = $tmp["DESCRIPTION"];
						}

						if(is_array($res[$field_name]) && $db_prop["PROPERTY_TYPE"]=="L")
						{
							$arTemp = array();
							foreach($res[$field_name] as $key=>$val)
							{
								$arEnum = CIBlockPropertyEnum::GetByID($val);
								if($arEnum!==false)
									$arTemp[$val] = $arEnum["VALUE"];
							}
							$res[$field_name] = $arTemp;
						}
					}
				}
				foreach($arUpdate as $strTable=>$arFields)
				{
					$strUpdate = $DB->PrepareUpdate($strTable, $arFields);
					if($strUpdate!="")
					{
						$strSql = "UPDATE ".$strTable." SET ".$strUpdate." WHERE IBLOCK_ELEMENT_ID = ".intval($res["ID"]);
						$DB->QueryBind($strSql, $arFields);
					}
				}
			}
			if(is_array($this->arIBlockConvProps))
			{
				foreach($this->arIBlockConvProps as $strFieldName=>$arCallback)
				{
					if(is_array($res[$strFieldName]))
					{

						foreach($res[$strFieldName] as $key=>$value)
						{
							$arValue = call_user_func_array($arCallback["ConvertFromDB"], array($arCallback["PROPERTY"], array("VALUE"=>$value,"DESCRIPTION"=>"")));
							$res[$strFieldName][$key] = $arValue["VALUE"];
						}
					}
					else
					{
						$arValue = call_user_func_array($arCallback["ConvertFromDB"], array($arCallback["PROPERTY"], array("VALUE"=>$res[$strFieldName],"DESCRIPTION"=>"")));
						$res[$strFieldName] = $arValue["VALUE"];
					}
				}
			}
			if(is_array($this->arIBlockNumProps))
			{
				foreach($this->arIBlockNumProps as $field_name => $db_prop)
				{
					if(strlen($res[$field_name]) > 0)
						$res[$field_name] = htmlspecialcharsex(CIBlock::NumberFormat($res[$field_name]));
				}
			}
		}
		elseif(
			defined("BX_COMP_MANAGED_CACHE")
			&& $this->_LAST_IBLOCK_ID == ""
			&& count($this->_FILTER_IBLOCK_ID)
		)
		{
			foreach($this->_FILTER_IBLOCK_ID as $iblock_id => $t)
				$GLOBALS["CACHE_MANAGER"]->RegisterTag("iblock_id_".$iblock_id);
		}

		return $res;
	}

	function GetNext($bTextHtmlAuto=true, $use_tilda=true)
	{
		$res = parent::GetNext($bTextHtmlAuto, $use_tilda);
		if($res)
		{
			//Handle List URL for Element, Section or IBlock
			if($this->strListUrl)
				$TEMPLATE = $this->strListUrl;
			elseif(array_key_exists("~LIST_PAGE_URL", $res))
				$TEMPLATE = $res["~LIST_PAGE_URL"];
			elseif(!$use_tilda && array_key_exists("LIST_PAGE_URL", $res))
				$TEMPLATE = $res["LIST_PAGE_URL"];
			else
				$TEMPLATE = "";

			if($TEMPLATE)
			{
				$res_tmp = $res;
				if((intval($res["IBLOCK_ID"]) <= 0) && (intval($res["ID"]) > 0))
				{
					$res_tmp["IBLOCK_ID"] = $res["ID"];
					$res_tmp["IBLOCK_CODE"] = $res["CODE"];
					$res_tmp["IBLOCK_EXTERNAL_ID"] = $res["EXTERNAL_ID"];
					if($use_tilda)
					{
						$res_tmp["~IBLOCK_ID"] = $res["~ID"];
						$res_tmp["~IBLOCK_CODE"] = $res["~CODE"];
						$res_tmp["~IBLOCK_EXTERNAL_ID"] = $res["~EXTERNAL_ID"];
					}
				}

				if($use_tilda)
				{
					$res["~LIST_PAGE_URL"] = CIBlock::ReplaceDetailUrl($TEMPLATE, $res_tmp, true, false);
					$res["LIST_PAGE_URL"] = htmlspecialcharsbx($res["~LIST_PAGE_URL"]);
				}
				else
				{
					$res["LIST_PAGE_URL"] = CIBlock::ReplaceDetailUrl($TEMPLATE, $res_tmp, true, false);
				}
			}

			//If this is Element or Section then process it's detail and section URLs
			if(strlen($res["IBLOCK_ID"]))
			{

				if(array_key_exists("GLOBAL_ACTIVE", $res))
					$type = "S";
				else
					$type = "E";

				if($this->strDetailUrl)
					$TEMPLATE = $this->strDetailUrl;
				elseif(array_key_exists("~DETAIL_PAGE_URL", $res))
					$TEMPLATE = $res["~DETAIL_PAGE_URL"];
				elseif(!$use_tilda && array_key_exists("DETAIL_PAGE_URL", $res))
					$TEMPLATE = $res["DETAIL_PAGE_URL"];
				else
					$TEMPLATE = "";

				if($TEMPLATE)
				{
					if($this->arSectionContext)
					{
						$TEMPLATE = str_replace("#SECTION_ID#", $this->arSectionContext["ID"], $TEMPLATE);
						$TEMPLATE = str_replace("#SECTION_CODE#", $this->arSectionContext["CODE"], $TEMPLATE);
					}

					if($use_tilda)
					{
						$res["~DETAIL_PAGE_URL"] = CIBlock::ReplaceDetailUrl($TEMPLATE, $res, true, $type);
						$res["DETAIL_PAGE_URL"] = htmlspecialcharsbx($res["~DETAIL_PAGE_URL"]);
					}
					else
					{
						$res["DETAIL_PAGE_URL"] = CIBlock::ReplaceDetailUrl($TEMPLATE, $res, true, $type);
					}
				}

				if($this->strSectionUrl)
					$TEMPLATE = $this->strSectionUrl;
				elseif(array_key_exists("~SECTION_PAGE_URL", $res))
					$TEMPLATE = $res["~SECTION_PAGE_URL"];
				elseif(!$use_tilda && array_key_exists("SECTION_PAGE_URL", $res))
					$TEMPLATE = $res["SECTION_PAGE_URL"];
				else
					$TEMPLATE = "";

				if($TEMPLATE)
				{
					if($use_tilda)
					{
						$res["~SECTION_PAGE_URL"] = CIBlock::ReplaceSectionUrl($TEMPLATE, $res, true, $type);
						$res["SECTION_PAGE_URL"] = htmlspecialcharsbx($res["~SECTION_PAGE_URL"]);
					}
					else
					{
						$res["SECTION_PAGE_URL"] = CIBlock::ReplaceSectionUrl($TEMPLATE, $res, true, $type);
					}
				}
			}
		}
		return $res;
	}

	function GetNextElement($bTextHtmlAuto=true, $use_tilda=true)
	{
		if(!($r = $this->GetNext($bTextHtmlAuto, $use_tilda)))
			return $r;

		$res = new _CIBElement;
		$res->fields = $r;
		if(count($this->arIBlockAllProps)>0)
			$res->props  = $this->arIBlockAllProps;
		return $res;
	}

	function SetTableID($table_id)
	{
		$this->table_id = $table_id;
	}

	function NavStart($nPageSize=20, $bShowAll=true, $iNumPage=false)
	{
		if($this->table_id)
		{
			if ($_REQUEST["mode"] == "excel")
				return;

			$nSize = CAdminResult::GetNavSize($this->table_id, $nPageSize);
			if(is_array($nPageSize))
			{
				$this->nInitialSize = $nPageSize["nPageSize"];
				$nPageSize["nPageSize"] = $nSize;
			}
			else
			{
				$this->nInitialSize = $nPageSize;
				$nPageSize = $nSize;
			}
		}
		parent::NavStart($nPageSize, $bShowAll, $iNumPage);
	}

	function GetNavPrint($title, $show_allways=true, $StyleText="", $template_path=false, $arDeleteParam=false)
	{
		if($this->table_id && ($template_path === false))
			$template_path = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/interface/navigation.php";
		return parent::GetNavPrint($title, $show_allways, $StyleText, $template_path, $arDeleteParam);
	}
}
?>