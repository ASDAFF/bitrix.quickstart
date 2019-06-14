<?php

use MHT\Product;

//������������� 03/10: ������� local_delivery_cost, ��������� delivery-options �� ������� ��������

define ("MIBIX_DEBUG_YAMEXPORT", true);

if (!CModule::IncludeModule("iblock")) return false;
IncludeModuleLangFile(__FILE__);

global $DBType;

/**
 * ����� ������ ��� ������ � �������� "����� ��������� ��������"
 */
class CMibixModelGeneral
{
    private $arMsg = array(); // ��� ������ ��������� � ������

    public function getArMsg()
    {
        return $this->arMsg;
    }

    /**
     * �������� ������ �� ������� �� ID ������
     *
     * @param $ID
     * @return mixed
     */
    public function GetByID($ID)
    {
        global $DB;
        $ID = intval($ID);

        $strSql =
            "SELECT g.*, ".
            "	".$DB->DateToCharFunction("g.date_update", "FULL")." AS date_update, ".
            "	".$DB->DateToCharFunction("g.date_insert", "FULL")." AS date_insert ".
            "FROM b_mibix_yam_general g ".
            "WHERE g.id='".$ID."' ";

        return $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
    }

    /**
     * ������ "�������� ���������" �� ����
     *
     * @param array $aSort
     * @param array $arFilter
     * @param bool $arNavStartParams
     * @return CDBResult|mixed
     */
    public function GetList($aSort=Array(), $arFilter=Array(), $arNavStartParams=false)
    {
        global $DB;
        $arSqlSearch = Array();
        $from1 = "";
        if(is_array($arFilter))
        {
            foreach($arFilter as $key => $val)
            {
                if(!is_array($val))
                {
                    if( (strlen($val) <= 0) || ($val === "NOT_REF") )
                        continue;
                }
                switch(strtoupper($key))
                {
                    case "ID":
                        $arSqlSearch[] = GetFilterQuery("ps.id", $val, "N");
                        break;
                    case "PROFILE_ID":
                        $arSqlSearch[] = GetFilterQuery("ps.profile_id", $val, "N");
                        break;
                    case "NAME":
                        $arSqlSearch[] = GetFilterQuery("ps.name", $val, "Y", array("@", ".", "_"));
                        break;
                    case "UPDATE_1":
                        $arSqlSearch[] = "ps.date_update>=".$DB->CharToDateFunction($val);
                        break;
                    case "UPDATE_2":
                        $arSqlSearch[] = "ps.date_update<=".$DB->CharToDateFunction($val." 23:59:59");
                        break;
                    case "INSERT_1":
                        $arSqlSearch[] = "ps.date_insert>=".$DB->CharToDateFunction($val);
                        break;
                    case "INSERT_2":
                        $arSqlSearch[] = "ps.date_insert<=".$DB->CharToDateFunction($val." 23:59:59");
                        break;
                    case "ACTIVE":
                        $arSqlSearch[] = ($val=="Y") ? "ps.active='Y'" : "ps.active='N'";
                        break;
                }
            }
        }
        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);

        $arOrder = array();
        foreach($aSort as $by => $ord)
        {
            $by = strtoupper($by);
            $ord = (strtoupper($ord) <> "ASC"? "DESC": "ASC");
            switch($by)
            {
                case "ID": $arOrder[$by] = "ps.id ".$ord; break;
                case "PROFILE_ID": $arOrder[$by] = "ps.profile_id ".$ord; break;
                case "NAME": $arOrder[$by] = "ps.name ".$ord; break;
                case "DATE_INSERT": $arOrder[$by] = "ps.date_insert ".$ord; break;
                case "DATE_UPDATE": $arOrder[$by] = "ps.date_update ".$ord; break;
                case "ACT": $arOrder[$by] = "ps.active ".$ord; break;
            }
        }
        if(count($arOrder) <= 0) $arOrder["ID"] = "ps.id DESC";

        if(is_array($arNavStartParams))
        {
            $strSql = "
				SELECT count(".($from1 <> ""? "DISTINCT ps.id": "'x'").") as C
				FROM
					b_mibix_yam_general ps
					$from1
				WHERE
				".$strSqlSearch;

            $res_cnt = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
            $res_cnt = $res_cnt->Fetch();
            $cnt = $res_cnt["C"];

            $strSql = "
				SELECT
					ps.id, ps.profile_id, ps.active, ps.name,
					".$DB->DateToCharFunction("ps.date_update")." date_update,
					".$DB->DateToCharFunction("ps.date_insert")." date_insert
				FROM
					b_mibix_yam_general ps
					$from1
				WHERE
				$strSqlSearch
				".($from1 <> ""?
                    "GROUP BY ps.id, ps.profile_id, ps.active, ps.name":
                    ""
                )."
				ORDER BY ".implode(", ", $arOrder);

            $res = new CDBResult();
            $res->NavQuery($strSql, $cnt, $arNavStartParams);
            $res->is_filtered = (IsFiltered($strSqlSearch));

            return $res;
        }
        else
        {
            $strSql = "
				SELECT
					ps.id, ps.profile_id, ps.active, ps.name,
					".$DB->DateToCharFunction("ps.date_update")." date_update,
					".$DB->DateToCharFunction("ps.date_insert")." date_insert
				FROM
					b_mibix_yam_general ps
					$from1
				WHERE
				$strSqlSearch
				".($from1 <> ""?
                    "GROUP BY ps.id, ps.profile_id, ps.active, ps.name":
                    ""
                )."
				ORDER BY ".implode(", ", $arOrder);

            $res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
            $res->is_filtered = (IsFiltered($strSqlSearch));

            return $res;
        }
    }

    /**
     * ���������� ����� ������ � ������� "����� �������� ��������"
     *
     * @param $arFields
     * @param string $SITE_ID
     * @return bool|int
     */
    public function Add($arFields, $SITE_ID=SITE_ID)
    {
        global $DB;

        // ���� ���������� � ����
        $arFields["active"] = "Y";
        $arFields["~date_insert"] = $DB->CurrentTimeFunction();
        $arFields["~date_update"] = $DB->CurrentTimeFunction();

        // ��������� ����������� ���� �� ������ � ���������� false � ������ �� �������, ��� ���� ���� ������ ��������� � ���������� ������
        if(!$this->CheckFields($arFields, 0)) return false;

        // ���� ������ ���, �� ��������� ������ � �������
        $ID = $DB->Add("b_mibix_yam_general", $arFields);
        if($ID > 0)
        {
            // �������������� �������� ��� ���������� ������
        }
        return $ID;
    }

    // ���������� ������
    function Update($ID, $arFields, $SITE_ID=SITE_ID)
    {
        global $DB;
        $ID = intval($ID);

        // ��������� ����������� ���� �� ������ � ���������� false � ������ �� �������, ��� ���� ���� ������ ��������� � ���������� ������
        if(!$this->CheckFields($arFields, $ID)) return false;

        // ������� ����, ������� �� ������� ����������
        //unset($arFields["CONFIRM_CODE"]);

        // ���������� ������� � ���������� ������
        $strUpdate = $DB->PrepareUpdate("b_mibix_yam_general", $arFields);
        if (strlen($strUpdate)>0)
        {
            $strSql =
                "UPDATE b_mibix_yam_general SET ".
                $strUpdate.", ".
                "	date_update=".$DB->GetNowFunction()." ".
                "WHERE id=".$ID;
            if(!$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__)) return false;
        }

        return true;
    }

    /**
     * �������� ����������� ����� ����� "����� �������� ��������"
     *
     * @param $arFields
     * @param $ID
     * @return bool
     */
    private function CheckFields($arFields, $ID)
    {
        // �������� ����� �� ����������
        $this->arMsg = array(); // ������� ������� ��� ��������� (������)
        if(is_set($arFields, "name")) // ��������: �������� ��������
        {
            if(strlen($arFields["name"]) == 0)
            {
                $this->arMsg[] = array("id"=>"name", "text"=>GetMessage("MIBIX_YAM_ERR_NAME_NULL"));
            }
            elseif(strlen($arFields["name"]) > 20)
            {
                $this->arMsg[] = array("id"=>"name", "text"=>GetMessage("MIBIX_YAM_ERR_NAME_LIMIT"));
            }
        }
        if(is_set($arFields, "company")) // ��������: ��������
        {
            if(strlen($arFields["company"]) == 0)
            {
                $this->arMsg[] = array("id"=>"company", "text"=>GetMessage("MIBIX_YAM_ERR_COMPANY_NULL"));
            }
            elseif(strlen($arFields["company"]) > 255)
            {
                $this->arMsg[] = array("id"=>"company", "text"=>GetMessage("MIBIX_YAM_ERR_COMPANY_LIMIT"));
            }
        }
        if(is_set($arFields, "url")) // ��������: ������ �� ����
        {
            if(strlen($arFields["url"]) == 0)
            {
                $this->arMsg[] = array("id"=>"url", "text"=>GetMessage("MIBIX_YAM_ERR_URL_NULL"));
            }
            elseif(strlen($arFields["url"]) > 255)
            {
                $this->arMsg[] = array("id"=>"url", "text"=>GetMessage("MIBIX_YAM_ERR_URL_LIMIT"));
            }
        }
        if(is_set($arFields, "platform_version"))
        {
            if($arFields["platform_version"] != "Y" && $arFields["platform_version"] != "N")
            {
                $this->arMsg[] = array("id"=>"platform_version", "text"=>GetMessage("MIBIX_YAM_ERR_PLATFORM"));
            }
        }
        if(is_set($arFields, "cpa"))
        {
            if($arFields["cpa"] != "0" && $arFields["cpa"] != "1")
            {
                $this->arMsg[] = array("id"=>"cpa", "text"=>GetMessage("MIBIX_YAM_ERR_CPA"));
            }
        }
        if(is_set($arFields, "adult"))
        {
            if($arFields["adult"] != "Y" && $arFields["adult"] != "N")
            {
                $this->arMsg[] = array("id"=>"adult", "text"=>GetMessage("MIBIX_YAM_ERR_ADULT"));
            }
        }
        if(is_set($arFields, "step_limit"))
        {
            if(!empty($arFields["step_limit"]) && !is_numeric($arFields["step_limit"]))
            {
                $this->arMsg[] = array("id"=>"step_limit", "text"=>GetMessage("MIBIX_YAM_ERR_STEP_LIMIT"));
            }
        }
        if(is_set($arFields, "step_interval_run"))
        {
            if(!empty($arFields["step_limit"]) && !is_numeric($arFields["step_interval_run"]))
            {
                $this->arMsg[] = array("id"=>"step_interval_run", "text"=>GetMessage("MIBIX_YAM_ERR_STEP_INTERVAL_RUN"));
            }
        }

        // ���� ������ ���, �� ���������� true
        if (!empty($this->arMsg))
        {
            return false;
        }

        return true;
    }

    /**
     * ������� ������� �������� �� ���� �� ��� ID
     *
     * @param $ID
     * @return mixed
     */
    public function Delete($ID)
    {
        global $DB;
        $ID = intval($ID);

        $DB->StartTransaction();
        $res = $DB->Query("DELETE FROM b_mibix_yam_general WHERE id='".$ID."'", false, "File: ".__FILE__."<br>Line: ".__LINE__);

        if($res)
            $DB->Commit();
        else
            $DB->Rollback();

        return $res;
    }
}

/**
 * ����� ������ ��� ������ � �������� "��������� ������"
 */
class CMibixModelDataSource
{
    public $LAST_ERROR="";
    public $LAST_MESSAGE="";

    /**
     * SelectBox �� ������� �������� ���������
     *
     * @param $str_shop_id
     * @return string
     */
    public function getSelectBoxProfileShop($str_shop_id)
    {
        global $DB;
        $strHTML = '<select name="f_shop_id" id="f_shop_id" size="1">';

        $dbRes = $DB->Query("SELECT id, name FROM b_mibix_yam_general WHERE active='Y'");
        while($arRes = $dbRes->Fetch())
        {
            $selectField = "";
            if ($arRes["id"]==$str_shop_id) $selectField = " selected";

            $strHTML .= '<option value="'.$arRes["id"].'"'.$selectField.'>['.$arRes["id"].'] '.htmlspecialcharsEx($arRes["name"]).'</option>';
        }
        $strHTML .= '</select>';

        return $strHTML;
    }

    /**
     * SelectBox � ������ ������
     *
     * @param $str_site_id
     * @return string
     */
    public function getSelectBoxSiteId($str_site_id)
    {
        $strHTML = '<select name="f_site_id" id="f_site_id" size="1">';
        $strHTML .= '<option value="">('.GetMessage("MIBIX_YAM_IDS_SEL_ANY").')</option>';
        $dbRes = CSite::GetList(($by='sort'),($order='asc'));
        while ($arRes = $dbRes->Fetch())
        {
            $selectField = "";
            if ($arRes['LID']==$str_site_id) $selectField = " selected";

            $strHTML .= '<option value="'.$arRes['LID'].'"'.$selectField.'>('.$arRes['LID'].') '.$arRes['NAME'].'</option>';
        }
        $strHTML .= '</select>';

        return $strHTML;
    }

    /**
     * SelectBox � ������ ����������
     *
     * @param $str_iblock_type
     * @return string
     */
    public function getSelectBoxIBlockType($str_iblock_type)
    {
        $strHTML = '<select name="f_iblock_type" id="f_iblock_type" size="1">';
        $strHTML .= '<option value="">('.GetMessage("MIBIX_YAM_IDS_SEL_IBLOCK").')</option>';
        $dbRes = CIBlockType::GetList();
        while($ar_iblock_type = $dbRes->Fetch())
        {
            $selectField = "";
            if ($ar_iblock_type["ID"]==$str_iblock_type) $selectField = " selected";

            if($arRes = CIBlockType::GetByIDLang($ar_iblock_type["ID"], LANG))
            {
                $strHTML .= '<option value="'.$ar_iblock_type["ID"].'"'.$selectField.'>['.$ar_iblock_type["ID"].'] '.htmlspecialcharsEx($arRes["NAME"]).'</option>';
            }
            else
            {
                $strHTML .= '<option value="'.$ar_iblock_type["ID"].'"'.$selectField.'>['.$ar_iblock_type["ID"].']</option>';
            }
        }
        $strHTML .= '</select>';

        return $strHTML;
    }

    /**
     * SelectBox � ����������� ���������� ���� � �����
     *
     * @param $str_site_id
     * @param $str_iblock_type
     * @param $str_iblock_id
     * @return string
     */
    public function getSelectBoxIBlockId($str_site_id, $str_iblock_type, $str_iblock_id)
    {
        $strHTML = '<select name="f_iblock_id" id="f_iblock_id" size="1">';

        // �� �������������� ���������� ��� ���������
        if($str_iblock_id > 0)
        {
            $arParams = array();
            $arParams['TYPE'] = $str_iblock_type;
            if ($str_site_id!="") {
                $arParams['SITE_ID'] = $str_site_id;
            }
            $dbRes = CIBlock::GetList(array(), $arParams, false, false, array("ID","NAME"));
            while ($arRes = $dbRes->Fetch())
            {
                $selectField = "";
                if ($arRes['ID']==$str_iblock_id) $selectField = " selected";

                $strHTML .= '<option value="'.$arRes['ID'].'"'.$selectField.'>'.$arRes['NAME'].'</option>';
            }
        }
        else // ��� ���������� - ������ ������
        {
            $strHTML .= '<option>('.GetMessage("MIBIX_YAM_IDS_SEL_TYPE").')</option>';
        }
        $strHTML .= '</select>';
        return $strHTML;
    }

    /**
     * SelectBox � ��������� �������������� ���������
     *
     * @param $name string �������� ����������
     * @param $str_iblock_id int ID ���������
     * @param $str_sections
     * @return string HTML-��� ��������
     */
    public function getSelectBoxSections($name, $str_iblock_id, $str_sections)
    {
        $arSectSelected = explode(",", $str_sections);

        $strHTML = '<select class="typeselect" multiple="" name="'.$name.'[]" id="'.$name.'" size="10">';

        // �� �������������� ���������� ��� �������
        if($str_iblock_id > 0)
        {
            $arParams = array();
            $arParams['IBLOCK_ID'] = $str_iblock_id;

            // �������� ������� ��������� �� ��� ID � ���������� SelectBox
            $dbRes = $rsSections = CIBlockSection::GetList(array('LEFT_MARGIN'=>'ASC'), $arParams);
            while ($arRes = $dbRes->GetNext())
            {
                // ��������� ��� ���
                $selectField = "";
                if (in_array($arRes['ID'],$arSectSelected)) $selectField = " selected";
                // ��������� ��������
                $strHTML .= '<option value="'.$arRes['ID'].'"'.$selectField.'>'.str_repeat("..", ($arRes['DEPTH_LEVEL']-1)).trim($arRes['NAME']).'</option>';
            }
        }
        $strHTML .= '</select>';
        return $strHTML;
    }

    /**
     * ������� ��� ������ � ���������� ����������
     *
     * @param $iblock_id
     * @param $filter_name
     * @param $filter_unit
     * @param $filter_value
     * @return string
     */
    public function getControlFilter($iblock_id, $filter_name, $filter_unit, $filter_value)
    {
        $strHTML = '<div id="div_filter">';

        // ���� ���� ����������� ����
        if($iblock_id>0 && count($filter_name)>0 && count($filter_value)>0)
        {
            foreach($filter_name as $pKey=>$pName)
            {
                // �������� ��� �������� ���������
                if(!isset($filter_unit[$pKey])) continue;
                if(!isset($filter_value[$pKey])) continue;
                $pUnit = $filter_unit[$pKey];
                $pValue = $filter_value[$pKey];

                // ������������� ����� �� ������ ������� (�� ������ ��� � js ������ ����� ��� ���������� ����� �����)
                if($pKey==0)
                    $strHTML .= '<div id="first_filter">';
                else
                    $strHTML .= '<div>';

                // ��������� ���� ��������
                $strHTML .= self::getSelectBoxFilterName($iblock_id, $pName);
                $strHTML .= self::getSelectBoxFilterUnit($pUnit);
                $strHTML .= '<input type="text" name="f_filter_value[]" size="12" placeholder="'.GetMessage("MIBIX_YAM_IRU_SEL_FILTER_VALUE").'" value="'.$pValue.'" />';
                //$strHTML .= '<select name="f_filter_value[]" id="f_filter" size="1">'.self::getSelectBoxProperty($pValue, $iblock_id, array(""=>GetMessage("MIBIX_YAM_IRU_SEL_PARAMVALUE")), "S", false).'</select>';
                $strHTML .= '</div>';
            }
        }
        else
        {
            $strHTML .= '<div id="first_filter">';
            $strHTML .= self::getSelectBoxFilterName($iblock_id);
            $strHTML .= self::getSelectBoxFilterUnit();
            $strHTML .= '<input type="text" name="f_filter_value[]" size="12" placeholder="'.GetMessage("MIBIX_YAM_IRU_SEL_FILTER_VALUE").'" value="" />';
            $strHTML .= '</div>';
        }

        $strHTML .= '</div>';
        $strHTML .= '<div><a href="javascript:void(0);" id="filter_add">'.GetMessage("MIBIX_YAM_IRU_SEL_FILTER_ADDNEW").'</a></div>';

        return $strHTML;
    }

    /**
     * SelectBox ������ ����� ��������
     *
     * @param $pName
     * @return string
     */
    private function getSelectBoxFilterName($IBLOCK_ID, $pName="")
    {
        $strHTML = '';

        // --- ����� ������� ---
        $strHTML_main = '';
        $arFilterNameMain = array(
            //"" => GetMessage("MIBIX_YAM_IRU_SEL_FILTER_NAME"),
            "filter_price" => GetMessage("MIBIX_YAM_IRU_SEL_FILTER_PRICE"),
            "filter_quantity" => GetMessage("MIBIX_YAM_IRU_SEL_FILTER_QUANTITY"),
        );
        foreach($arFilterNameMain as $fNameKey => $fNameValue)
        {
            $selectField = "";
            if ($pName==$fNameKey) $selectField = " selected";
            $strHTML_main .= '<option value="'.$fNameKey.'"'.$selectField.'>'.$fNameValue.'</option>';
        }
        // ---/ ����� ������� ---

        // --- ������ �� ��������� ---
        $strHTML_prop = '';
        $iblockProps = CIBlockProperty::GetList(Array("sort"=>"asc","name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID));
        while ($arRes = $iblockProps->GetNext())
        {
            $selectField = "";
            if ('prop@'.$arRes["CODE"]==$pName) $selectField = " selected";

            $strHTML_prop .= '<option value="prop@'.$arRes["CODE"].'"'.$selectField.'>['.$arRes["CODE"].'] '.$arRes["NAME"].'</option>';
        }
        // ������� ������ ���� ���� ��������
        if (!empty($strHTML_prop))
        {
            $strHTML = '<option value="">'.GetMessage("MIBIX_YAM_IRU_SEL_FILTER_NAME").'</option>';
            $strHTML .= '<optgroup label="' . GetMessage("MIBIX_YAM_IRU_SEL_FILTER_GROUP_MAIN") . ':">' . $strHTML_main . '</optgroup>';
            $strHTML .= '<optgroup label="' . GetMessage("MIBIX_YAM_IRU_SEL_FILTER_GROUP_SETTINGS") . ':">' . $strHTML_prop . '</optgroup>';
        }
        // ---/ ������ �� ���������� ---

        // --- ������ �� ��������� SKU (���� �������� �������)
        $strIBlockOffersHTML = ""; // �������� ��������� �������� �����������
        $arOffersSKU = NULL;
        if(CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
        {
            $arOffersSKU = CCatalogSKU::GetInfoByProductIBlock($IBLOCK_ID);
        }
        if (!empty($arOffersSKU['IBLOCK_ID']))
        {
            $rsOfferIBlocks = CIBlock::GetByID($arOffersSKU['IBLOCK_ID']);
            if (($arOfferIBlock = $rsOfferIBlocks->Fetch()))
            {
                $iblockOfferProps = CIBlockProperty::GetList(Array("sort"=>"asc","name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arOffersSKU['IBLOCK_ID']));
                while ($arResOffers = $iblockOfferProps->GetNext())
                {
                    if($arOffersSKU["SKU_PROPERTY_ID"] == $arResOffers["ID"]) continue; // ���������� �������� ���� ��� �������� ��������� � ���������

                    $selectField = "";
                    if ('offer@'.$arResOffers["CODE"]==$pName) $selectField = " selected";

                    $strIBlockOffersHTML .= '<option value="offer@'.$arResOffers["CODE"].'"'.$selectField.'>['.$arResOffers["CODE"].'] '.$arResOffers["NAME"].'</option>';
                }
                if(!empty($strIBlockOffersHTML))
                {
                    $strHTML .= '<optgroup label="'.GetMessage("MIBIX_YAM_IRU_SEL_FILTER_GROUP_SETTINGS_SKU").':">'.$strIBlockOffersHTML.'</optgroup>';
                }
            }
        }
        // ---/ ������ �� ��������� SKU (���� �������� �������) ---

        $strHTML = '<select name="f_filter_name[]" id="f_filter" size="1">'.$strHTML.'</select>&nbsp;';
        return $strHTML;
    }

    /**
     * SelectBox ������ �������� ����������
     *
     * @param $pUnit
     * @return string
     */
    private function getSelectBoxFilterUnit($pUnit="")
    {
        $arFilterUnit = array(
            "equal" => GetMessage("MIBIX_YAM_IRU_SEL_FILTER_EQUAL"),
            "notequal" => GetMessage("MIBIX_YAM_IRU_SEL_FILTER_NOTEQUAL"),
            "more" => GetMessage("MIBIX_YAM_IRU_SEL_FILTER_MORE"),
            "less" => GetMessage("MIBIX_YAM_IRU_SEL_FILTER_LESS"),
            //"empty" => GetMessage("MIBIX_YAM_IRU_SEL_FILTER_EMPTY"),
            //"notempty" => GetMessage("MIBIX_YAM_IRU_SEL_FILTER_NOTEMPTY"),
        );

        $strHTML = '<select name="f_filter_unit[]" size="1">';
        foreach($arFilterUnit as $fUnitKey => $fUnitValue)
        {
            $selectField = "";
            if ($pUnit==$fUnitKey) $selectField = " selected";
            $strHTML .= '<option value="'.$fUnitKey.'"'.$selectField.'>'.$fUnitValue.'</option>';
        }
        $strHTML .= '</select>&nbsp;';

        return $strHTML;
    }

    /**
     * ���������� ����� ������ � ������� "����� �������� ��������"
     *
     * @param $arFields
     * @param string $SITE_ID
     * @return bool|int
     */
    public function Add($arFields, $SITE_ID=SITE_ID)
    {
        global $DB;

        // ����������� ���� ��� ������ � ����
        $arFields["include_sections"] = $this->MSelectPrepare($arFields["include_sections"]);
        $arFields["exclude_sections"] = $this->MSelectPrepare($arFields["exclude_sections"]);
        $arFields["include_items"] = $this->MSelectPrepare($arFields["include_items"]);
        $arFields["exclude_items"] = $this->MSelectPrepare($arFields["exclude_items"]);

        // ���� ���������� � ����
        $arFields["shop_id"] = 1;
        $arFields["~date_insert"] = $DB->CurrentTimeFunction();
        $arFields["~date_update"] = $DB->CurrentTimeFunction();

        // ��������� ����������� ���� �� ������ � ���������� false � ������ �� �������, ��� ���� ���� ������ ��������� � ���������� ������
        if(!$this->CheckFields($arFields, 0)) return false;

        // ���� ������ ���, �� ��������� ������ � �������
        $ID = $DB->Add("b_mibix_yam_datasource", $arFields);
        if($ID > 0)
        {
            // �������������� �������� ��� ���������� ������
        }
        return $ID;
    }

    /**
     * ���������� ������ �� ��������� ������
     *
     * @param $ID
     * @param $arFields
     * @param $SITE_ID
     * @return bool
     */
    public function Update($ID, $arFields, $SITE_ID=SITE_ID)
    {
        global $DB;
        $ID = intval($ID);
        $this->LAST_MESSAGE = "";

        if(!$this->CheckFields($arFields, $ID)) return false;

        // ����������� ���� ��� ������ � ����
        if(!empty($arFields["include_sections"]))
            $arFields["include_sections"] = $this->MSelectPrepare($arFields["include_sections"]);
        if(!empty($arFields["exclude_sections"]))
            $arFields["exclude_sections"] = $this->MSelectPrepare($arFields["exclude_sections"]);
        if(!empty($arFields["include_items"]))
            $arFields["include_items"] = $this->MSelectPrepare($arFields["include_items"]);
        if(!empty($arFields["exclude_items"]))
            $arFields["exclude_items"] = $this->MSelectPrepare($arFields["exclude_items"]);

        $strUpdate = $DB->PrepareUpdate("b_mibix_yam_datasource", $arFields);
        if (strlen($strUpdate)>0)
        {
            $strSql =
                "UPDATE b_mibix_yam_datasource SET ".
                $strUpdate.", ".
                "	date_update=".$DB->GetNowFunction()." ".
                "WHERE id=".$ID;
            if(!$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__)) return false;
        }
        return true;
    }

    public function GetByID($ID)
    {
        global $DB;
        $ID = intval($ID);

        $strSql =
            "SELECT ds.*, ".
            "	".$DB->DateToCharFunction("ds.date_update", "FULL")." AS date_update, ".
            "	".$DB->DateToCharFunction("ds.date_insert", "FULL")." AS date_insert ".
            "FROM b_mibix_yam_datasource ds ".
            "WHERE ds.id='".$ID."' ";

        return $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
    }

    /**
     * �������������� ������, ���������� �� multiselect ��� ������ � ����
     *
     * @param $mselect_field
     * @return string
     */
    private function MSelectPrepare($mselect_field)
    {
        if(!empty($mselect_field) && is_array($mselect_field))
        {
            return implode(",", array_diff($mselect_field, array("")));
        }

        return "";
    }

    /**
     * ������ "���������� ������" �� ����
     *
     * @param array $aSort
     * @param array $arFilter
     * @param bool $arNavStartParams
     * @return CDBResult|mixed
     */
    public function GetList($aSort=Array(), $arFilter=Array(), $arNavStartParams=false)
    {
        global $DB;
        $arSqlSearch = Array();
        $from1 = "";
        if(is_array($arFilter))
        {
            foreach($arFilter as $key => $val)
            {
                if(!is_array($val))
                {
                    if( (strlen($val) <= 0) || ($val === "NOT_REF") )
                        continue;
                }
                switch(strtoupper($key))
                {
                    case "ID":
                        $arSqlSearch[] = GetFilterQuery("ds.id", $val, "N");
                        break;
                    case "SHOP_ID":
                        $arSqlSearch[] = GetFilterQuery("ds.shop_id", $val, "N");
                        break;
                    case "IBLOCK_ID":
                        $arSqlSearch[] = GetFilterQuery("ds.iblock_id", $val, "N");
                        break;
                    case "NAME_DATA":
                        $arSqlSearch[] = GetFilterQuery("ds.name_data", $val, "Y", array("@", ".", "_"));
                        break;
                    case "UPDATE_1":
                        $arSqlSearch[] = "ds.date_update>=".$DB->CharToDateFunction($val);
                        break;
                    case "UPDATE_2":
                        $arSqlSearch[] = "ds.date_update<=".$DB->CharToDateFunction($val." 23:59:59");
                        break;
                    case "INSERT_1":
                        $arSqlSearch[] = "ds.date_insert>=".$DB->CharToDateFunction($val);
                        break;
                    case "INSERT_2":
                        $arSqlSearch[] = "ds.date_insert<=".$DB->CharToDateFunction($val." 23:59:59");
                        break;
                    case "ACTIVE":
                        $arSqlSearch[] = ($val=="Y") ? "ds.active='Y'" : "ds.active='N'";
                        break;
                }
            }
        }
        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);

        $arOrder = array();
        foreach($aSort as $by => $ord)
        {
            $by = strtoupper($by);
            $ord = (strtoupper($ord) <> "ASC"? "DESC": "ASC");
            switch($by)
            {
                case "ID": $arOrder[$by] = "ds.id ".$ord; break;
                case "SHOP_ID": $arOrder[$by] = "ds.shop_id ".$ord; break;
                case "IBLOCK_ID": $arOrder[$by] = "ds.iblock_id ".$ord; break;
                case "NAME_DATA": $arOrder[$by] = "ds.name_data ".$ord; break;
                case "DATE_INSERT": $arOrder[$by] = "ds.date_insert ".$ord; break;
                case "DATE_UPDATE": $arOrder[$by] = "ds.date_update ".$ord; break;
                case "ACT": $arOrder[$by] = "ds.active ".$ord; break;
            }
        }
        if(count($arOrder) <= 0) $arOrder["ID"] = "ds.id DESC";

        if(is_array($arNavStartParams))
        {
            $strSql = "
				SELECT count(".($from1 <> ""? "DISTINCT ds.id": "'x'").") as C
				FROM
					b_mibix_yam_datasource ds
					LEFT JOIN b_mibix_yam_general g ON (ds.shop_id=g.id)
					$from1
				WHERE
				".$strSqlSearch;

            $res_cnt = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
            $res_cnt = $res_cnt->Fetch();
            $cnt = $res_cnt["C"];

            $strSql = "
				SELECT
					ds.id, ds.shop_id, ds.iblock_id, ds.active, ds.name_data,
					".$DB->DateToCharFunction("ds.date_update")." date_update,
					".$DB->DateToCharFunction("ds.date_insert")." date_insert,
					g.name
				FROM
					b_mibix_yam_datasource ds
				LEFT JOIN b_mibix_yam_general g ON (ds.shop_id=g.id)
					$from1
				WHERE
				$strSqlSearch
				".($from1 <> ""?
                    "GROUP BY ds.id, ds.shop_id, ds.iblock_id, ds.active, ds.name_data, g.name":
                    ""
                )."
				ORDER BY ".implode(", ", $arOrder);

            $res = new CDBResult();
            $res->NavQuery($strSql, $cnt, $arNavStartParams);
            $res->is_filtered = (IsFiltered($strSqlSearch));

            return $res;
        }
        else
        {
            $strSql = "
				SELECT
					ds.id, ds.shop_id, ds.iblock_id, ds.active, ds.name_data,
					".$DB->DateToCharFunction("ds.date_update")." date_update,
					".$DB->DateToCharFunction("ds.date_insert")." date_insert,
					g.name
				FROM
					b_mibix_yam_datasource ds
					LEFT JOIN b_mibix_yam_general g ON (ds.shop_id=g.id)
					$from1
				WHERE
				$strSqlSearch
				".($from1 <> ""?
                    "GROUP BY ds.id, ds.shop_id, ds.iblock_id, ds.active, ds.name_data, g.name":
                    ""
                )."
				ORDER BY ".implode(", ", $arOrder);

            $res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
            $res->is_filtered = (IsFiltered($strSqlSearch));

            return $res;
        }
    }

    /**
     * �������� ������������ ����������� ����� ����� ��������� ������
     *
     * @param $arFields
     * @param $ID
     * @return bool
     */
    private function CheckFields($arFields, $ID)
    {
        global $DB;

        $this->LAST_ERROR = "";
        $aMsg = array();

        if(is_set($arFields, "name_data")) // ��������: ��������
        {
            if(strlen($arFields["name_data"]) == 0)
            {
                $aMsg[] = array("id"=>"name_data", "text"=>GetMessage("MIBIX_YAM_ERR_DS_NAME_NULL"));
            }
            elseif(strlen($arFields["name_data"]) > 255)
            {
                $aMsg[] = array("id"=>"name_data", "text"=>GetMessage("MIBIX_YAM_ERR_DS_NAME_LIMIT255"));
            }
        }
        if(is_set($arFields, "shop_id")) // ��������: ��������
        {
            if($arFields["shop_id"] < 1)
            {
                $aMsg[] = array("id"=>"shop_id", "text"=>GetMessage("MIBIX_YAM_ERR_DS_SHOP_EMPTY"));
            }
        }
        if(is_set($arFields, "iblock_id")) // ��������: ��������
        {
            if($arFields["iblock_id"] < 1)
            {
                $aMsg[] = array("id"=>"iblock_id", "text"=>GetMessage("MIBIX_YAM_ERR_DS_IBLOCK_EMPTY"));
            }
        }
        // �������� ������ �����, ����� ������������ ��������� ��������� (count>4)
        if(count($arFields)>4 && empty($arFields["include_sections"]) && empty($arFields["exclude_sections"]) && empty($arFields["include_items"]) && empty($arFields["exclude_items"]))
        {
            $aMsg[] = array("id"=>"iblock_id", "text"=>GetMessage("MIBIX_YAM_ERR_DS_SELECTED_EMPTY"));
        }

        // ���� ������ ���, �� ���������� true
        if(!empty($aMsg))
        {
            $e = new CAdminException($aMsg);
            $GLOBALS["APPLICATION"]->ThrowException($e);
            $this->LAST_ERROR = $e->GetString();
            return false;
        }
        return true;
    }

    /**
     * ������� �������� �� ���� �� ��� ID
     *
     * @param $ID
     * @return mixed
     */
    public function Delete($ID)
    {
        global $DB;
        $ID = intval($ID);

        $DB->StartTransaction();
        $res = $DB->Query("DELETE FROM b_mibix_yam_datasource WHERE id='".$ID."'", false, "File: ".__FILE__."<br>Line: ".__LINE__);

        if($res)
            $DB->Commit();
        else
            $DB->Rollback();

        return $res;
    }
}

/**
 * ����� ������ ��� ������ � �������� "�������"
 */
class CMibixModelRules
{
    public $LAST_ERROR="";
    public $LAST_MESSAGE="";

    /**
     * SelectBox � ����������� ������
     *
     * @param $str_datasource_id
     * @return string
     */
    public function getSelectBoxDataSource($str_datasource_id)
    {
        global $DB;
        $strHTML = '<select name="f_datasource_id" id="f_datasource_id" size="1">';
        $strHTML .= '<option value="">('.GetMessage("MIBIX_YAM_IRU_SEL_DS").')</option>';

        $dbRes = $DB->Query("SELECT id, name_data FROM b_mibix_yam_datasource WHERE active='Y'");
        while($arRes = $dbRes->Fetch())
        {
            $selectField = "";
            if ($arRes["id"]==$str_datasource_id) $selectField = " selected";

            $strHTML .= '<option value="'.$arRes["id"].'"'.$selectField.'>['.$arRes["id"].'] '.htmlspecialcharsEx($arRes["name_data"]).'</option>';
        }
        $strHTML .= '</select>';

        return $strHTML;
    }

    /**
     * SelectBox � ������ �������� ������
     *
     * @param $str_type
     * @return string
     */
    public function getSelectBoxYandexType($str_type)
    {
        global $DB;
        $arYandexTypes = array(
            "simple" => GetMessage("MIBIX_YAM_IRU_SEL_YT_SIMPLE"),
            "vendor.model" => GetMessage("MIBIX_YAM_IRU_SEL_YT_VM"),
            "book" => GetMessage("MIBIX_YAM_IRU_SEL_YT_BOOK"),
            "audiobook" => GetMessage("MIBIX_YAM_IRU_SEL_YT_AUDIOBOOK"),
            "artist.title.m" => GetMessage("MIBIX_YAM_IRU_SEL_YT_ARTTITLEM"),
            "artist.title.v" => GetMessage("MIBIX_YAM_IRU_SEL_YT_ARTTITLEV"),
            "tour" => GetMessage("MIBIX_YAM_IRU_SEL_YT_TOUR"),
            "event-ticket" => GetMessage("MIBIX_YAM_IRU_SEL_YT_EVTICKET"),
        );

        $strHTML = '<select name="f_type" id="f_type" size="1">';
        foreach($arYandexTypes as $yTypeK => $yTypeV)
        {
            $selectField = "";
            if ($yTypeK==$str_type) $selectField = " selected";

            $strHTML .= '<option value="'.$yTypeK.'"'.$selectField.'>'.$yTypeV.'</option>';
        }
        $strHTML .= '</select>';

        return $strHTML;
    }

    /**
     * SelectBox � ������ ������������� (������ �� ������������ ����� ����� JS)
     *
     * @param $str_category_id
     * @return string
     */
    public function getSelectBoxCategoryClass($str_category_id)
    {
        global $DB;

        $strHTML = '<select name="f_category_id" id="f_category_id" size="1">';
        $strHTML .= '<option value="">('.GetMessage("MIBIX_YAM_IRU_SEL_YCLASSIF").')</option>';

        $dbRes = $DB->Query("SELECT id, name_category FROM b_mibix_yam_classific_categories");
        while($arRes = $dbRes->Fetch())
        {
            $selectField = "";
            if ($arRes["id"]==$str_category_id) $selectField = " selected";

            $strHTML .= '<option value="'.$arRes["id"].'"'.$selectField.'>['.$arRes["id"].'] '.htmlspecialcharsEx($arRes["name_category"]).'</option>';
        }
        $strHTML .= '</select>';

        return $strHTML;
    }

    /**
     * SelectBox (options) ��������� ��� ������.�������
     * (���������� ��� �������� ��� � ����� ajax)
     *
     * @param $str_market_category_id
     * @param int $field
     * @param int $parent
     * @return string
     */
    public function getSelectBoxMarketCategory($str_market_category_id, $field=0, $parent=0)
    {
        global $DB;

        $strHTML = '<option value="">('.GetMessage("MIBIX_YAM_IRU_SEL_BMC").')</option>';

        $dbRes = $DB->Query("SELECT id, name_category FROM b_mibix_yam_market_categories WHERE parent_id=".$parent);
        while($arRes = $dbRes->Fetch())
        {
            $selectField = "";
            if ($arRes["id"]==$str_market_category_id) $selectField = " selected";

            $strHTML .= '<option value="'.$arRes["id"].'"'.$selectField.'>'.htmlspecialcharsEx($arRes["name_category"]).'</option>';
        }

        return $strHTML;
    }

    /**
     * ������� ���������� ��������� �� ������������� ID
     *
     * @param int $parent
     * @return mixed
     */
    public function getMarketCategoryCount($parent=0)
    {
        global $DB;

        $rs = $DB->Query("SELECT count(*) as CNT FROM b_mibix_yam_market_categories WHERE parent_id=".$parent, true);
        $row = $rs->Fetch();

        return $row["CNT"];
    }

    /**
     * ����������� Ajax-������� SekectBox'�� ��� ������ ������� ��������� ������.�������
     *
     * @param $str_market_category_id
     * @return string
     */
    public function getSelectBoxYMCategories($str_market_category_id)
    {
        $strHTML = "";
        $arCategories = array();

        // ����������� ������ ��������� �� ������
        if(!empty($str_market_category_id))
        {
            $arCategories = explode(",", $str_market_category_id);
        }

        // ���������� �� ���� ������� ����������� ��������� (����� �� 6)
        $parentCategory = 0;
        for($i=0;$i<6;$i++)
        {
            if ((isset($arCategories[$i]) && $arCategories[$i]>0) || $i<1) // ������������� SelectBox � ��������� ��� ������������ � ���� ���������
            {
                $strHTML .= '<div id="ymselect_'.$i.'"><select name="f_market_category_id_'.$i.'" id="f_market_category_id_'.$i.'" size="1">'.self::getSelectBoxMarketCategory($arCategories[$i],0,$parentCategory).'</select></div>';
                $parentCategory = $arCategories[$i];
            }
            else // ������������� ������ ���� ��� ��������� � �������� �� (��� ����������� ���������� ����� ajax)
            {
                $strHTML .= '<div id="ymselect_'.$i.'" style="display:none;"><select name="f_market_category_id_'.$i.'" id="f_market_category_id_'.$i.'" size="1"></select></div>';
            }
        }
        return $strHTML;
    }

    /**
     * Ajax-������� ������������ ������ ����������������� �������� � ������� ��������� �� ��� ID
     *
     * @param $SELECTED
     * @param int $IBLOCK_ID
     * @param array $arParams
     * @param string|bool $pType ���������� ����������� ���� ������� �������� (S - ������; N - �����; L - ������; F - ����; G - �������� � �������; E - �������� � ��������)
     * @param bool $useGroup
     * @return string
     */
    public function getSelectBoxProperty($SELECTED, $IBLOCK_ID=0, $arParams=Array("none"=>""), $pType=false, $useGroup=true)
    {
        $strHTML = '';
        $emptyOption = "";
        $arTypeInfo = Array("S"=>" (������)", "N"=>" (�����)", "L"=>" (������)", "F"=>" (����)", "G"=>" (�������� � �������)", "E"=>" (�������� � ��������)");

        if (isset($arParams["none"]) && $arParams["none"]=="")
        {
            $arParams["none"] = GetMessage("MIBIX_YAM_IRU_SEL_SELVAL");
        }

        if (array_key_exists("none", $arParams))
        {
            $emptyOption = '<option value="">'.$arParams["none"].'</option>'; // ���������� �������� (�����) ��������
            unset($arParams["none"]); // ������� ��� ��������� �������
        }

        // ����� �� ���. ����������
        foreach($arParams as $kParam=>$vParam)
        {
            // ���������� ��������� ��� ���������� (������) � ������������� (������) ��������
            $selectField = "";
            if(is_array($SELECTED))
            {
                if (in_array($kParam, $SELECTED)) $selectField = " selected";
            }
            else
            {
                if ($kParam==$SELECTED) $selectField = " selected";
            }

            $strHTML .= '<option value="'.$kParam.'"'.$selectField.'>'.$vParam.'</option>';
        }

        // ���� ���� ��������� � �������, ����������� �� � ������
        if(count($arParams)>0 && $useGroup)
        {
            $strHTML = '<optgroup label="'.GetMessage("MIBIX_YAM_IRU_SEL_OPTGROUP").':">'.$strHTML.'</optgroup>';
        }

        // ��������� ������ ������� ����� ������ �������
        $strHTML = $emptyOption.$strHTML;

        // �������� ���������� ��������� (������ ������)
        if ($IBLOCK_ID>0)
        {
            // ��������� ���������������� ���� ������
            $arUserTypes = array("UserID","DateTime","EList","FileMan","map_yandex","HTML","map_google","ElementXmlID","Sequence","EAutocomplete","SKU","video","TopicID");

            $strIBlockHTML = ""; // �������� ��������� ���������
            $iblockFilter = Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID);
            if ($pType)
            {
                // ���� �������� ���� ������� ������� �� ������ �������
                if(strlen($pType)==1)
                {
                    $iblockFilter["PROPERTY_TYPE"] = $pType;
                }
                elseif(in_array($pType,$arUserTypes)) // ���������������� ���� �������
                {
                    $iblockFilter["USER_TYPE"] = $pType;
                }
            }
            $iblockProps = CIBlockProperty::GetList(Array("sort"=>"asc","name"=>"asc"), $iblockFilter);
            while ($arRes = $iblockProps->GetNext())
            {
                $selectField = "";
                if(is_array($SELECTED))
                {
                    if (in_array($arRes["CODE"], $SELECTED)) $selectField = " selected";
                }
                else
                {
                    if ($arRes["CODE"]==$SELECTED) $selectField = " selected";
                }
                $strIBlockHTML .= '<option value="'.$arRes["CODE"].'"'.$selectField.'>['.$arRes["CODE"].'] '.$arRes["NAME"].'</option>';
            }
            if(strlen($strIBlockHTML)>0)
            {
                if($useGroup)
                    $strHTML .= '<optgroup label="'.GetMessage("MIBIX_YAM_IRU_SEL_OPTGROUPPROP").($pType?((array_key_exists($pType, $arTypeInfo)?" ".$arTypeInfo[$pType]:"").':">'):':">') .$strIBlockHTML.'</optgroup>';
                else
                    $strHTML .= $strIBlockHTML;
            }

            // C������� ��������� �������� ����������� SKU (������ ������)
            $strIBlockOffersHTML = ""; // �������� ��������� �������� �����������
            $arOffersSKU = NULL;
            if(CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
            {
                $arOffersSKU = CCatalogSKU::GetInfoByProductIBlock($IBLOCK_ID);
            }
            if (!empty($arOffersSKU['IBLOCK_ID']))
            {
                $rsOfferIBlocks = CIBlock::GetByID($arOffersSKU['IBLOCK_ID']);
                if (($arOfferIBlock = $rsOfferIBlocks->Fetch()))
                {
                    $iblockOfferFilter = Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arOffersSKU['IBLOCK_ID']);

                    // ���������� �� ����
                    if ($pType)
                    {
                        // ���� �������� ���� ������� ������� �� ������ �������
                        if(strlen($pType)==1)
                        {
                            $iblockOfferFilter["PROPERTY_TYPE"] = $pType;
                        }
                        elseif(in_array($pType,$arUserTypes)) // ���������������� ���� �������
                        {
                            $iblockOfferFilter["USER_TYPE"] = $pType;
                        }
                    }

                    $iblockOfferProps = CIBlockProperty::GetList(Array("sort"=>"asc","name"=>"asc"), $iblockOfferFilter);
                    while ($arResOffers = $iblockOfferProps->GetNext())
                    {
                        if($arOffersSKU["SKU_PROPERTY_ID"] == $arResOffers["ID"]) continue; // ���������� �������� ���� ��� �������� ��������� � ���������

                        $selectField = "";
                        if(is_array($SELECTED))
                        {
                            if (in_array('offer@'.$arResOffers["CODE"], $SELECTED)) $selectField = " selected";
                        }
                        else
                        {
                            if ('offer@'.$arResOffers["CODE"]==$SELECTED) $selectField = " selected";
                        }
                        $strIBlockOffersHTML .= '<option value="offer@'.$arResOffers["CODE"].'"'.$selectField.'>['.$arResOffers["CODE"].']'.($pType=='F'?'[SKU] ':' ').$arResOffers["NAME"].'</option>';
                    }
                    if(strlen($strIBlockOffersHTML)>0)
                    {
                        if($useGroup)
                            $strHTML .= '<optgroup label="'.GetMessage("MIBIX_YAM_IRU_SEL_OPTGROUPPROPSKU").($pType?((array_key_exists($pType, $arTypeInfo)?" ".$arTypeInfo[$pType]:"").':">'):':">').$strIBlockOffersHTML.'</optgroup>';
                        else
                            $strHTML .= $strIBlockOffersHTML;
                    }
                }
            }
        }

        return $strHTML;
    }

    /**
     * �������� ����� ��� ����� ���
     *
     * @param $str_price
     * @return string
     */
    public function getOptionsPriceType($str_price, $incNone=false)
    {
        $strHTML = '';

        if($incNone)
            $strHTML .= '<option value="">'.GetMessage("MIBIX_YAM_IRU_SEL_CODE_NONE").'</option>';

        if(CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
        {
            $dbRes = CCatalogGroup::GetList(array("SORT" => "ASC"));
            while ($arRes = $dbRes->Fetch()) {
                $selectField = "";
                if ($arRes["ID"] == $str_price) $selectField = " selected";
                $strHTML .= '<option value="' . $arRes["ID"] . '"' . $selectField . '>[' . $arRes["NAME"] . '] ' . $arRes["NAME_LANG"] . '</option>';
            }
        }

        return $strHTML;
    }

    /**
     * ������� ��� ������ ���������� ��� ������� ����������� ��������
     *
     * @param $field
     * @param $selected
     * @param $iblock_id
     * @param bool $pType
     * @param bool $useGroup
     * @return string
     */
    public function getControlParamsSelectBox($field, $selected, $iblock_id, $pType=false, $useGroup=true)
    {
        //TODO: ������� ���� ����� �� ��������� (catname)
        $strHTML = '';
        $arParams = self::GetArrayParamsByCODE($field);

        // ��������� �� ���������������� ��������, ���� ��� �����������, �� ���������� ��� (������������ �� �������� "self@")
        $inputSelf = "";
        if (preg_match("/^self@(.*?)/isU", $selected, $matches))
        {
            if(!empty($matches) && isset($matches[1]))
            {
                $selected = "self";
                $inputSelf = '<input type="text" name="self_'.$field.'" size="30" value="'.trim($matches[1]).'">';
            }
        }

        $strHTML .= '<select name="f_'.$field.'" id="f_'.$field.'" size="1">';
        $strHTML .= self::getSelectBoxProperty($selected, $iblock_id, $arParams, $pType, $useGroup);
        $strHTML .= '</select><div id="selfField_'.$field.'">'.$inputSelf.'</div>';

        return $strHTML;
    }

    /**
     * ������� (������) ��� ������ ������ ��� ���������� ����������
     * @param $field
     * @param $selected
     * @param $iblock_id
     * @param bool $pType
     * @param bool $useGroup
     * @return string
     */
    public function getControlParamsMultiSelectBox($field, $selected, $iblock_id, $pType=false, $useGroup=false)
    {
        $strHTML = '';
        $arParams = self::GetArrayParamsByCODE($field);

        $strHTML .= '<select multiple="" name="f_'.$field.'[]" id="f_'.$field.'" size="5">';
        $strHTML .= self::getSelectBoxProperty($selected, $iblock_id, $arParams, $pType, $useGroup);
        $strHTML .= '</select>';

        return $strHTML;
    }

    /**
     * ������� (������) ��� ������ ������ ��� ���������� ���������� ��� �����������
     * @param $field
     * @param $selected
     * @param $iblock_id
     * @return string
     */
    public function getControlParamsMultiSelectBoxPicture($field, $selected, $iblock_id)
    {
        $strHTML = '';
        $arParams = self::GetArrayParamsByCODE($field);

        $strHTML .= '<select multiple="" name="f_'.$field.'[]" id="f_'.$field.'" size="5">';
        $strHTML .= '<optgroup label="'.GetMessage("MIBIX_YAM_IRU_SEL_OPTGROUP_ALL").':">'.self::getSelectBoxProperty($selected, $iblock_id, $arParams, "F", false).'</optgroup>';
        $strHTML .= self::getSelectBoxProperty($selected, $iblock_id, array(), "S", true);
        $strHTML .= '</select>';

        return $strHTML;
    }

    /**
     * SelectBox �� ������� ����� ���
     *
     * @param $str_price
     * @return string
     */
    public function getSelectBoxPriceType($str_price, $iblock_id)
    {
        $strHTML = '<select name="f_price" id="f_price" size="1">';
        $strHTML .= self::getOptionsPriceType($str_price);
        $strHTML .= self::getSelectBoxProperty($str_price, $iblock_id, Array());
        $strHTML .= "</select>";
        return $strHTML;
    }

    /**
     * SelectBox �� ������� ����� ���
     *
     * @param $str_price
     * @return string
     */
    public function getSelectBoxOldPriceType($str_price, $iblock_id)
    {
        $strHTML = '<select name="f_oldprice" id="f_oldprice" size="1">';
        $strHTML .= self::getOptionsPriceType($str_price, true);
        $strHTML .= self::getSelectBoxProperty($str_price, $iblock_id, Array());
        $strHTML .= "</select>";

        return $strHTML;
    }

    /**
     * ������� ��� ������ � ���������� ����������
     *
     * @param $iblock_id
     * @param $param_name
     * @param $param_unit
     * @param $param_value
     * @return string
     */
    public function getControlParams($iblock_id, $param_name, $param_unit, $param_value)
    {
        //TODO: ������ ���������� ���� �������� ��� ������������� ������ (������ ��������)
        $strHTML = '<div id="div_params">';

        // ���� ���� ����������� ����
        if($iblock_id>0 && count($param_name)>0 && count($param_value)>0)
        {
            foreach($param_name as $pKey=>$pName)
            {
                // �������� ��� �������� ���������
                if(!isset($param_unit[$pKey])) continue;
                if(!isset($param_value[$pKey])) continue;
                $pUnit = $param_unit[$pKey];
                $pValue = $param_value[$pKey];

                // ������������� ����� �� ������ ������� (�� ������ ��� � js ������ ����� ��� ���������� ����� �����)
                if($pKey==0)
                    $strHTML .= '<div id="first_param">';
                else
                    $strHTML .= '<div>';

                // ��������� ���� ��������
                $strHTML .= '<input type="text" name="f_param_name[]" size="12" placeholder="'.GetMessage("MIBIX_YAM_IRU_SEL_PARAMNAME").'" value="'.$pName.'" />&nbsp;';
                $strHTML .= '<input type="text" name="f_param_unit[]" size="5" placeholder="'.GetMessage("MIBIX_YAM_IRU_SEL_PARAMUNIT").'" value="'.$pUnit.'" />&nbsp;';
                $strHTML .= '<select name="f_param_value[]" id="f_param" size="1">'.self::getSelectBoxProperty($pValue, $iblock_id, array(), false, true).'</select>';
                $strHTML .= '</div>';
            }
        }
        else
        {
            $strHTML .= '<div id="first_param">';
            $strHTML .= '<input type="text" name="f_param_name[]" size="12" placeholder="'.GetMessage("MIBIX_YAM_IRU_SEL_PARAMNAME").'" value="" />&nbsp;';
            $strHTML .= '<input type="text" name="f_param_unit[]" size="5" placeholder="'.GetMessage("MIBIX_YAM_IRU_SEL_PARAMUNIT").'" value="" />&nbsp;';
            $strHTML .= '<select name="f_param_value[]" id="f_param" size="1">'.self::getSelectBoxProperty("", $iblock_id, array(), false, true).'</select>';
            $strHTML .= '</div>';
        }

        $strHTML .= '</div>';
        $strHTML .= '<div><a href="javascript:void(0);" id="param_add">'.GetMessage("MIBIX_YAM_IRU_SEL_ADDNEWPARAM").'</a></div>';

        return $strHTML;
    }

    /**
     * �������� ����������� ����� ����� "������"
     *
     * @param $arFields
     * @param $ID
     * @return bool
     */
    private function CheckFields($arFields, $ID)
    {
        $this->LAST_ERROR = "";
        $aMsg = array(); // ������ ��� ��������� �� �������

        // �������� ����� �� ����������
        if(is_set($arFields, "datasource_id"))
        {
            if(IntVal($arFields["datasource_id"])<1)
            {
                $aMsg[] = array("id"=>"datasource_id", "text"=>GetMessage("MIBIX_YAM_ERR_RULE_DS_NULL"));
            }
        }
        if(is_set($arFields, "name_rule")) // ��������
        {
            if(strlen($arFields["name_rule"]) == 0)
            {
                $aMsg[] = array("id"=>"name_rule", "text"=>GetMessage("MIBIX_YAM_ERR_RULE_NAME_RULE_NULL"));
            }
            elseif(strlen($arFields["name_rule"]) > 255)
            {
                $aMsg[] = array("id"=>"name_rule", "text"=>GetMessage("MIBIX_YAM_ERR_RULE_NAME_RULE_LIMIT"));
            }
        }
        if(is_set($arFields, "type")) // ��� ������
        {
            if(!strlen($arFields["type"]))
            {
                $aMsg[] = array("id"=>"type", "text"=>GetMessage("MIBIX_YAM_ERR_RULE_TYPE_NULL"));
            }
            else
            {
                // �������� ��������� ����� � ����������� �� ���������� ����
                switch($arFields["type"])
                {
                    case "vendor.model":
                        if(strlen($arFields["model"]) == 0 || strlen($arFields["model"]) > 255) //model
                            $aMsg[] = array("id"=>"model", "text"=>GetMessage("MIBIX_YAM_ERR_RULE_MODEL_EMPTY"));
                        if(strlen($arFields["vendor"]) == 0 || strlen($arFields["vendor"]) > 255) //vendor
                            $aMsg[] = array("id"=>"vendor", "text"=>GetMessage("MIBIX_YAM_ERR_RULE_VENDOR_EMPTY"));
                        break;
                    case "artist.title.m":
                    case "artist.title.v":
                        if(strlen($arFields["title"]) == 0 || strlen($arFields["title"]) > 255) //title
                            $aMsg[] = array("id"=>"title", "text"=>GetMessage("MIBIX_YAM_ERR_RULE_TITLE_EMPTY"));
                        break;
                    case "tour":
                        if(strlen($arFields["name"]) == 0 || strlen($arFields["name"]) > 255) //name
                            $aMsg[] = array("id"=>"name", "text"=>GetMessage("MIBIX_YAM_ERR_RULE_NAME_NULLORBIG"));
                        if(strlen($arFields["days"]) == 0 || strlen($arFields["days"]) > 255) //days
                            $aMsg[] = array("id"=>"days", "text"=>GetMessage("MIBIX_YAM_ERR_RULE_DAYS_NULLORBIG"));
                        if(strlen($arFields["included"]) == 0 || strlen($arFields["included"]) > 255) //included
                            $aMsg[] = array("id"=>"included", "text"=>GetMessage("MIBIX_YAM_ERR_RULE_INCLUDED"));
                        if(strlen($arFields["transport"]) == 0 || strlen($arFields["transport"]) > 255) //transport
                            $aMsg[] = array("id"=>"transport", "text"=>GetMessage("MIBIX_YAM_ERR_RULE_TRANSPORT"));
                        break;
                    case "event-ticket":
                        if(strlen($arFields["name"]) == 0 || strlen($arFields["name"]) > 255) //name
                            $aMsg[] = array("id"=>"name", "text"=>GetMessage("MIBIX_YAM_ERR_RULE_NAME_NULLORBIG"));
                        if(strlen($arFields["place"]) == 0 || strlen($arFields["place"]) > 255) //place
                            $aMsg[] = array("id"=>"place", "text"=>GetMessage("MIBIX_YAM_ERR_RULE_PLACE"));
                        if(strlen($arFields["date"]) == 0 || strlen($arFields["date"]) > 255) //date
                            $aMsg[] = array("id"=>"date", "text"=>GetMessage("MIBIX_YAM_ERR_RULE_DATE"));
                        break;
                    case "book":
                    case "audiobook":
                    default:
                        if(strlen($arFields["name"]) == 0 || strlen($arFields["name"]) > 255) //name
                            $aMsg[] = array("id"=>"name", "text"=>GetMessage("MIBIX_YAM_ERR_RULE_NAME_NULLORBIG"));
                }
            }
        }

        // ���� ������ ���, �� ���������� true
        if(!empty($aMsg))
        {
            $e = new CAdminException($aMsg);
            $GLOBALS["APPLICATION"]->ThrowException($e);
            $this->LAST_ERROR = $e->GetString();
            return false;
        }
        return true;
    }

    /**
     * ���������� ������ ������� � ����
     *
     * @param $arFields
     * @return int
     */
    public function Add($arFields)
    {
        global $DB;

        // ���� ���������� � ����
        $arFields["active"] = "Y";
        $arFields["~date_insert"] = $DB->CurrentTimeFunction();
        $arFields["~date_update"] = $DB->CurrentTimeFunction();

        // ��������� ����������� ���� �� ������ � ���������� false � ������ �� �������, ��� ���� ���� ������ ��������� � ���������� ������
        if(!$this->CheckFields($arFields, 0)) return false;

        // ���� ������ ���, �� ��������� ������ � �������
        $ID = $DB->Add("b_mibix_yam_rules", $arFields);
        if($ID > 0)
        {
            // �������������� �������� ��� ���������� ������
        }
        return $ID;
    }

    /**
     * ���������� ������� �� ��� ID
     *
     * @param $ID
     * @param $arFields
     * @return bool
     */
    public function Update($ID, $arFields)
    {
        global $DB;
        $ID = intval($ID);
        $this->LAST_MESSAGE = "";

        if(!$this->CheckFields($arFields, $ID)) return false;

        $strUpdate = $DB->PrepareUpdate("b_mibix_yam_rules", $arFields);
        if (strlen($strUpdate)>0)
        {
            $strSql =
                "UPDATE b_mibix_yam_rules SET ".
                $strUpdate.", ".
                "	date_update=".$DB->GetNowFunction()." ".
                "WHERE id=".$ID;
            if(!$DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__)) return false;
        }
        return true;
    }

    /**
     * �������� ������� �� ID �� ����
     *
     * @param $ID
     * @return mixed
     */
    public function GetByID($ID)
    {
        global $DB;
        $ID = intval($ID);

        $strSql =
            "SELECT rs.*, ".
            "	".$DB->DateToCharFunction("rs.date_update", "FULL")." AS date_update, ".
            "	".$DB->DateToCharFunction("rs.date_insert", "FULL")." AS date_insert ".
            "FROM b_mibix_yam_rules rs ".
            "WHERE rs.id='".$ID."' ";

        return $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
    }

    /**
     * �������� ID ��������� �� ID ��������� ������
     * @param $datasource_id
     * @return bool
     */
    public function GetIBlockByDatasourceID($datasource_id)
    {
        global $DB;

        $rsIBlock = $DB->Query("SELECT iblock_id FROM b_mibix_yam_datasource WHERE id=".$datasource_id, true);
        if($rowIBlock = $rsIBlock->Fetch())
        {
            return $rowIBlock["iblock_id"];
        }
        return false;
    }

    /**
     * �������� ������ ���������� �� �������� ����
     *
     * @param $code
     * @return array
     */
    public function GetArrayParamsByCODE($code)
    {
        $arParams = array(
            "none" => GetMessage("MIBIX_YAM_IRU_SEL_CODE_NONE"),
        );
        switch($code)
        {
            case "available": // �����������
                unset($arParams["none"]);
                $arParams["val@true"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_AVAILABLE_T");
                $arParams["val@false"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_AVAILABLE_F");
                break;

            case "bid": // ���. ������
            case "cbid": // ������ �� ����
                $arParams["self"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_CBID_SELF");
                break;

            // ps: ��� ������ ���� �������� none ���������� � �������, ����� ��� ajax ������� ���������� ���������
            case "price": // ����
            case "oldprice": // ������ ����
                unset($arParams["none"]);
                break;

            case "picture": // ������ �� ����
                unset($arParams["none"]);
                $arParams["PREVIEW_PICTURE"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_PIC_PREV");
                $arParams["DETAIL_PICTURE"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_PIC_DET");
                $arParams["sku@PREVIEW_PICTURE"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_PIC_PREV_SKU");
                $arParams["sku@DETAIL_PICTURE"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_PIC_DET_SKU");
                break;

            case "typeprefix":
                $arParams["val@catname"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_CAT");
                $arParams["self"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_SELF");
                break;

            case "model":
                $arParams["self"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_SELF");
                break;

            case "store":
                $arParams["val@true"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_TRUE");
                $arParams["val@false"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_FALSE");
                break;

            case "pickup":
                $arParams["val@true"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_TRUE");
                $arParams["val@false"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_FALSE");
                break;

            case "delivery":
                $arParams["val@true"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_TRUE");
                $arParams["val@false"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_FALSE");
                break;

            case "name":
                unset($arParams["none"]);
                $arParams["val@catname"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_NAMECAT");
                $arParams["val@catnamesku"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_NAMESKUCAT");
                $arParams["val@catnameboth"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_NAMECAT_BOTH");
                break;

            case "description":
                //unset($arParams["none"]);
                $arParams["PREVIEW_TEXT"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_PREV_TEXT");
                $arParams["DETAIL_TEXT"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_DET_TEXT");
                break;

            case "vendor":
            case "vendorcode":
                break;

            case "local_delivery_cost":
                $arParams["self"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_SELF");
                break;

            case "sales_notes":
                $arParams["self"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_SELF");
                break;

            case "manufacturer_warranty":
                $arParams["self"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_SELF");
                $arParams["val@true"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_MW_TRUE");
                $arParams["val@false"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_MW_FALSE");
                break;

            case "seller_warranty":
                $arParams["self"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_SELF");
                $arParams["val@true"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_SW_TRUE");
                $arParams["val@false"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_SW_FALSE");
                break;

            case "country_of_origin":
                $arParams["self"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_SELF");
                break;

            case "adult":
                $arParams["val@true"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_ADULT");
                break;

            case "downloadable":
                $arParams["val@true"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_DOWN_TRUE");
                $arParams["val@false"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_DOWN_FALSE");
                break;

            case "rec":
                break;

            case "age":
                $arParams["self"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_SELF");
                break;

            case "ageunit":
                $arParams["val@year"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_AGEUNIT_YEAR");
                $arParams["val@month"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_AGEUNIT_MONTH");
                break;

            case "barcode":
                $arParams["self"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_SELF");
                break;

            case "expiry":
                $arParams["self"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_SELF");
                break;

            case "weight":
                $arParams["val@catalog"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_USECAT");
                break;

            case "dimensions":
                $arParams["val@catalog"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_USECATS");
                break;

            case "param":
                break;

            case "cpa":
                unset($arParams["none"]);
                $arParams["val@0"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_CPA0");
                $arParams["val@1"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_CPA1");
                break;

            case "author":
            case "publisher":
            case "series":
            case "year":
            case "isbn":
            case "volume":
            case "part":
            case "language":
            case "binding":
            case "page_extent":
            case "table_of_contents":
            case "performed_by":
            case "performance_type":
            case "format":
            case "storage":
            case "recording_length":
            case "artist":
            case "title":
            case "media":
            case "starring":
            case "director":
            case "originalname":
            case "country":
            case "worldregion":
            case "region":
            case "days":
            case "datatour":
            case "hotel_stars":
            case "room":
            case "meal":
            case "included":
            case "transport":
            case "place":
                break;

            case "hall_plan":
                $arParams["self"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_SELF");
                break;

            case "date":
                break;

            case "is_premiere":
                $arParams["val@0"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_ISPREM0");
                $arParams["val@1"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_ISPREM1");
                break;

            case "is_kids":
                $arParams["val@0"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_ISKIDS0");
                $arParams["val@1"] = GetMessage("MIBIX_YAM_IRU_SEL_CODE_ISKIDS1");
                break;
        }
        return $arParams;
    }

    /**
     * ������ "������" �� ����
     *
     * @param array $aSort
     * @param array $arFilter
     * @param bool $arNavStartParams
     * @return CDBResult|mixed
     */
    public function GetList($aSort=Array(), $arFilter=Array(), $arNavStartParams=false)
    {
        global $DB;
        $arSqlSearch = Array();
        $from1 = "";
        if(is_array($arFilter))
        {
            foreach($arFilter as $key => $val)
            {
                if(!is_array($val))
                {
                    if( (strlen($val) <= 0) || ($val === "NOT_REF") )
                        continue;
                }
                switch(strtoupper($key))
                {
                    case "ID":
                        $arSqlSearch[] = GetFilterQuery("rs.id", $val, "N");
                        break;
                    case "DATASOURCE_ID":
                        $arSqlSearch[] = GetFilterQuery("rs.datasource_id", $val, "N");
                        break;
                    case "NAME_RULE":
                        $arSqlSearch[] = GetFilterQuery("rs.name_rule", $val, "Y", array("@", ".", "_"));
                        break;
                    case "UPDATE_1":
                        $arSqlSearch[] = "rs.date_update>=".$DB->CharToDateFunction($val);
                        break;
                    case "UPDATE_2":
                        $arSqlSearch[] = "rs.date_update<=".$DB->CharToDateFunction($val." 23:59:59");
                        break;
                    case "INSERT_1":
                        $arSqlSearch[] = "rs.date_insert>=".$DB->CharToDateFunction($val);
                        break;
                    case "INSERT_2":
                        $arSqlSearch[] = "rs.date_insert<=".$DB->CharToDateFunction($val." 23:59:59");
                        break;
                    case "ACTIVE":
                        $arSqlSearch[] = ($val=="Y") ? "rs.active='Y'" : "rs.active='N'";
                        break;
                }
            }
        }
        $strSqlSearch = GetFilterSqlSearch($arSqlSearch);

        $arOrder = array();
        foreach($aSort as $by => $ord)
        {
            $by = strtoupper($by);
            $ord = (strtoupper($ord) <> "ASC"? "DESC": "ASC");
            switch($by)
            {
                case "ID": $arOrder[$by] = "rs.id ".$ord; break;
                case "DATASOURCE_ID": $arOrder[$by] = "rs.datasource_id ".$ord; break;
                case "NAME_RULE": $arOrder[$by] = "rs.name_rule ".$ord; break;
                case "DATE_INSERT": $arOrder[$by] = "rs.date_insert ".$ord; break;
                case "DATE_UPDATE": $arOrder[$by] = "rs.date_update ".$ord; break;
                case "ACT": $arOrder[$by] = "rs.active ".$ord; break;
            }
        }
        if(count($arOrder) <= 0) $arOrder["ID"] = "rs.id DESC";

        if(is_array($arNavStartParams))
        {
            $strSql = "
				SELECT count(".($from1 <> ""? "DISTINCT rs.id": "'x'").") as C
				FROM
					b_mibix_yam_rules rs
				LEFT JOIN b_mibix_yam_datasource ds ON (rs.datasource_id=ds.id)
					$from1
				WHERE
				".$strSqlSearch;

            $res_cnt = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
            $res_cnt = $res_cnt->Fetch();
            $cnt = $res_cnt["C"];

            $strSql = "
				SELECT
					rs.id, rs.datasource_id, rs.active, rs.name_rule,
					".$DB->DateToCharFunction("rs.date_update")." date_update,
					".$DB->DateToCharFunction("rs.date_insert")." date_insert,
					ds.name_data
				FROM
					b_mibix_yam_rules rs
				LEFT JOIN b_mibix_yam_datasource ds ON (rs.datasource_id=ds.id)
					$from1
				WHERE
				$strSqlSearch
				".($from1 <> ""?
                    "GROUP BY rs.id, rs.datasource_id, rs.active, rs.name_rule, ds.name_data":
                    ""
                )."
				ORDER BY ".implode(", ", $arOrder);

            $res = new CDBResult();
            $res->NavQuery($strSql, $cnt, $arNavStartParams);
            $res->is_filtered = (IsFiltered($strSqlSearch));

            return $res;
        }
        else
        {
            $strSql = "
				SELECT
					rs.id, rs.datasource_id, rs.active, rs.name_rule,
					".$DB->DateToCharFunction("rs.date_update")." date_update,
					".$DB->DateToCharFunction("rs.date_insert")." date_insert,
					ds.name_data
				FROM
					b_mibix_yam_rules rs
					LEFT JOIN b_mibix_yam_datasource ds ON (rs.datasource_id=ds.id)
					$from1
				WHERE
				$strSqlSearch
				".($from1 <> ""?
                    "GROUP BY rs.id, rs.datasource_id, rs.active, rs.name_rule, ds.name_data":
                    ""
                )."
				ORDER BY ".implode(", ", $arOrder);

            $res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
            $res->is_filtered = (IsFiltered($strSqlSearch));

            return $res;
        }
    }

    /**
     * ������� ������� �� ���� �� ��� ID
     *
     * @param $ID
     * @return mixed
     */
    public function Delete($ID)
    {
        global $DB;
        $ID = intval($ID);

        $DB->StartTransaction();
        $res = $DB->Query("DELETE FROM b_mibix_yam_rules WHERE id='".$ID."'", false, "File: ".__FILE__."<br>Line: ".__LINE__);

        if($res)
            $DB->Commit();
        else
            $DB->Rollback();

        return $res;
    }
}

/**
 * ����� ��� ������ � ��������� � ������.������
 */
class CMibixYandexExport
{
    private $encoding = 'windows-1251';

    private static $bCreate = false;
    private static $urlShop = "";

    private static $bStepEnd = false;
    private static $arSectionIDs = array(); // ������ array([IBLOCK] => array(SECTIONS))
    private static $intMaxSectionID = array(); // ������ array([IBLOCK] => MAX_ID)

    /**
     * �������� YML ����� ��� ������.�������
     *
     * @param $YML_FILE
     * @param $STEP_LIMIT
     * @return bool
     */
    public function CreateYML($YML_FILE, $STEP_LIMIT, $CRON=false, $SHOP_ID=1)
    {
        self::$bCreate = true;
		
		
		// ������� ��������� ��������
        $curStatus = self::get_step_status($SHOP_ID);
        $TMP_YML_FILE = $YML_FILE . ".tmp";
		
		//self::steps_update_noncheck(0,0,0,0,0,$SHOP_ID);		
		
		print_r ($curStatus);

        // ���� ������ ����� CRON + ��������� ���� ���� ��������� �������� (���� ���, �� ������ �������� ��� ���� ��������)
        if($CRON && !empty($curStatus["last_run_time"]))
        {
            // ��������, ��������� �� ����� ��� ������������ ��������
            $stepTime = self::get_step_interval($SHOP_ID);

            // ����� ���������� ������� + �������� �������� (� ��������)
            $next_time_run = strtotime($curStatus["last_run_time"]) + ($stepTime["step_interval_run"] * 60);

            // ���� ����� ������������ �� ���������, �� ������� �� �������
            if(time() < $next_time_run)
                return false;
        }

        // �������� �� ��������� ������ (����
        self::check_freeze_process($SHOP_ID);

		
        // �������� ���������� (���� �������� ��� �������� � ������ ������)
        if($curStatus["in_blocked"] == "Y")
            return false;

        // ������ ���������� (�� ������ ���������� ������� ������� �� ����� ��������)
        self::set_block_status("Y", $SHOP_ID);

		
	
		
        // ������������� ����� ��������� ��������
        if($curStatus["in_proccess"] != "Y")
        {
            // ���� ���� ��������� ����, �� ������� ���
            if (file_exists($TMP_YML_FILE)) unlink($TMP_YML_FILE);

            // ������� ����� ��������� ����
            if ($fp = @fopen($TMP_YML_FILE, "wb"))
            {
                @fwrite($fp, "<?xml version=\"1.0\" encoding=\"windows-1251\"?>\n");
                @fwrite($fp, "<!DOCTYPE yml_catalog SYSTEM \"shops.dtd\">\n");
                @fwrite($fp, "<yml_catalog date=\"".Date("Y-m-d H:i")."\">\n");
                @fwrite($fp, "<shop>\n");
                foreach(self::get_yml_shop($SHOP_ID) as $elShop)
                {
                    @fwrite($fp, $elShop . "\n");
                }
                @fwrite($fp, "<offers>\n");
                @fclose($fp);

                // ������������� ������ "� ��������" ��� ����� 1
                self::set_proccess_status("Y", $SHOP_ID);

                // ������� ����������
                self::set_block_status("N", $SHOP_ID);

                // ���������� ������� ����
                self::update_last_time_step($SHOP_ID);

                return true;
            }
            //else
            //{
            //echo "Error create YML file while write!";
            //}
        }
        else // ��������� ��� ��������
        {
			
			if ($fp = @fopen($TMP_YML_FILE, "ab")) // ���������� ����
            {
                // DEBUG
                //if (defined('MIBIX_DEBUG_YAMEXPORT') && MIBIX_DEBUG_YAMEXPORT==true)
                    //self::writeLOG("������ ������");
				
				
				foreach(self::get_yml_offers($STEP_LIMIT, $SHOP_ID) as $elOffer)
                {
					@fwrite($fp, $elOffer . "\n");
                }

                // DEBUG
                //if (defined('MIBIX_DEBUG_YAMEXPORT') && MIBIX_DEBUG_YAMEXPORT==true)
                ////self::writeLOG("[INFO] function:".__FUNCTION__." (STEP_2)", self::$bStepEnd);
				//self::writeLOG("��������� ������");
				

                // ���� ������ �������� �� ���������� ������ ����, ���������� ��������� ���
                if(self::$bStepEnd)
                {
                    // DEBUG
                    //if (defined('MIBIX_DEBUG_YAMEXPORT') && MIBIX_DEBUG_YAMEXPORT==true)
                    //self::writeLOG("���� ������ �������� �� ���������� ������ ����, ���������� ��������� ���");

                    // ��������� ������ � ���� � �������� �� ��������� ���
                    @fclose($fp);

                    // ������� ����������
                    self::set_block_status("N", $SHOP_ID);

                    // ���������� ������� ����
                    self::update_last_time_step($SHOP_ID);

                    return true;
                }
                else // ��������� "�����" ��� yml-�����
                {
					//self::writeLOG("����� �����");
                    // ��������� "������" yml-�����
                    @fwrite($fp, "</offers>\n");
                    @fwrite($fp, "</shop>\n");
                    @fwrite($fp, "</yml_catalog>\n");
                    @fclose($fp);

                    // ������� ������ ������������ YML-���� � �� ��� ����� ������ �����
                    if (file_exists($YML_FILE)) unlink($YML_FILE);
                    rename($TMP_YML_FILE, $YML_FILE);

                    // ������ ������� ��������� ��������
                    self::steps_update(0,0,0,0,0,$SHOP_ID);

                    // ������������� ������ "���������" ��� ����� 1
                    self::set_proccess_status("N", $SHOP_ID);

                    // ������ ��������� ����� ��������� �������� ��� ����� 1
                    self::set_last_time_run($SHOP_ID);

                    // ���������� ������� ����
                    self::update_last_time_step($SHOP_ID);
                }
            }
            else
            {
                //self::writeLOG("error writing!:".__FUNCTION__);				
            }
        }

		
        // ������� ����������
        self::set_block_status("N", $SHOP_ID);

        // ������� �-�� ��� � ajax
        return false;
    }

    // ��������� YML ����� "�� ����"
    public function GetYML($SHOP_ID=1)
    {
        self::$bCreate = false;

        // ������ �� ������ �� ����������� ��������� ��������� ��������
        self::steps_update_noncheck(0,0,0,0,0,$SHOP_ID);

        ob_clean(); // ������� ����� ������

        // ������������� ���������
        header("Content-Type: text/xml; charset=windows-1251");
        echo "<"."?xml version=\"1.0\" encoding=\"windows-1251\"?".">\n";
        echo "<!DOCTYPE yml_catalog SYSTEM \"shops.dtd\">\n";
        echo "<yml_catalog date=\"".Date("Y-m-d H:i")."\">\n";
        echo "<shop>\n";
        foreach(self::get_yml_shop($SHOP_ID) as $elShop)
        {
            echo $elShop . "\n";
        }
        echo "<offers>\n";
        foreach(self::get_yml_offers(0, $SHOP_ID) as $elOffer)
        {
            echo $elOffer . "\n";
        }
        echo "</offers>\n";
        echo "</shop>\n";
        echo "</yml_catalog>\n";
    }


    // ��������� ������� YML ����� �� �������
    public function GetYMLToFile($YML_FILE, $SHOP_ID=1)
    {
        self::$bCreate = false;

        // ������ �� ������ �� ����������� ��������� ��������� ��������
        self::steps_update_noncheck(0,0,0,0,0,$SHOP_ID);

        ob_clean(); // ������� ����� ������

        $TMP_YML_FILE = $YML_FILE . ".tmp";

        // ���� ���� ��������� ����, �� ������� ���
        if (file_exists($TMP_YML_FILE)) unlink($TMP_YML_FILE);

        // ������� ����� ��������� ����
        if ($fp = @fopen($TMP_YML_FILE, "wb")) {

            @fwrite($fp, "<?xml version=\"1.0\" encoding=\"windows-1251\"?>\n");
            @fwrite($fp, "<!DOCTYPE yml_catalog SYSTEM \"shops.dtd\">\n");
            @fwrite($fp, "<yml_catalog date=\"" . Date("Y-m-d H:i") . "\">\n");
            @fwrite($fp, "<shop>\n");


            foreach (self::get_yml_shop($SHOP_ID) as $elShop) {
                @fwrite($fp, $elShop . "\n");
            }
            @fwrite($fp, "<offers>\n");

            $count = 0;

            foreach (self::get_yml_offers(0, $SHOP_ID) as $elOffer) {
                @fwrite($fp, $elOffer . "\n");
                $count++;
            }

            @fwrite($fp, "</offers>\n");
            @fwrite($fp, "</shop>\n");
            @fwrite($fp, "</yml_catalog>\n");
            @fclose($fp);

        }

        echo "\n\r in work - ".$count."\n\r";

        // ������� ������ ������������ YML-���� � �� ��� ����� ������ �����
        if (file_exists($YML_FILE) && file_exists($TMP_YML_FILE)) {

            echo "\n\r del old - ".$YML_FILE."\n\r";
            unlink($YML_FILE);
        }

        rename($TMP_YML_FILE, $YML_FILE);

        \CEventLog::Add(array(
            "SEVERITY"      => "INFO",
            "AUDIT_TYPE_ID" => "EXPORT",
            "MODULE_ID"     => "catalog",
            "ITEM_ID"       => 123,
            "DESCRIPTION"   => "�������� ".$YML_FILE." ���������. ���������� ".$count." �����������",
        ));

        // ������ ������� ��������� ��������
        self::steps_update(0,0,0,0,0,$SHOP_ID);

    }


    /**
     * �������� YML ����� � ������ <shop>, ����� <offers>
     *
     * @param int $shop_id
     * @return array
     */
    private function get_yml_shop($shop_id=1)
    {
        global $DB, $APPLICATION;

        // ���� � ���������� ��������� ��� ������� � ���.�����
        $arShopData = array();
        $rsShop = $DB->Query("SELECT name,company,salon,url,platform_version,agency,email,local_delivery_cost,cpa,adult,currency_rate,currency_rub,currency_rub_plus,currency_byr,currency_byr_plus,currency_uah,currency_uah_plus,currency_kzt,currency_kzt_plus,currency_usd,currency_usd_plus,currency_eur,currency_eur_plus FROM b_mibix_yam_general WHERE id='".$shop_id."' AND active='Y'", true);
        if ($rowShop = $rsShop->Fetch())
        {
            // ���������� ��������� �����
            $siteCharset = 'windows-1251';
            if (defined('BX_UTF') && BX_UTF==true)
            {
                $siteCharset = 'UTF-8';
            }

            // ���, ��������, url (������������) // COption::GetOptionString("main", "site_name", "")
            $arShopData["name"] = "<name>".$APPLICATION->ConvertCharset(htmlspecialcharsbx($rowShop["name"]), $siteCharset, 'windows-1251')."</name>";
            $arShopData["company"] = "<company>".$APPLICATION->ConvertCharset(htmlspecialcharsbx($rowShop["company"]), $siteCharset, 'windows-1251')."</company>";
            $arShopData["url"] = "<url>".htmlspecialcharsbx($rowShop["url"])."</url>";

            // ������������� URL �������� � ���������� ����������
            self::$urlShop = htmlspecialcharsbx($rowShop["url"]);

            // ��������� (�� ����)
            if($rowShop["platform"]=="Y")
            {
                $arShopData["platform"] = "<platform>".$APPLICATION->ConvertCharset(htmlspecialcharsbx("CMS 1C-Bitrix"), $siteCharset, 'windows-1251')."</platform>";
                $arShopData["version"] = "<version>".SM_VERSION."</version>";
            }

            // �������� (�� ����)
            if(strlen($rowShop["agency"])>0)
            {
                $arShopData["agency"] = "<agency>".$APPLICATION->ConvertCharset(htmlspecialcharsbx($rowShop["agency"]), $siteCharset, 'windows-1251')."</agency>";
            }

            // email (�� ����)
            if(strlen($rowShop["email"])>0)
            {
                $arShopData["email"] = "<email>".$APPLICATION->ConvertCharset(htmlspecialcharsbx($rowShop["email"]), $siteCharset, 'windows-1251')."</email>";
            }

            // ������ ( currencies )
            $arShopData["currencies"] = "<currencies>";
            foreach(self::get_array_currencies($rowShop) as $cur)
            {
                $arShopData["currencies"] .= "\n".$cur;
            }
            $arShopData["currencies"] .= "\n</currencies>";

            // ������ ��������� ��� ���� ���������� ������ ( categories )
            $arShopData["categories"] = "<categories>";
            foreach(self::get_array_categories() as $cur)
            {
                $arShopData["categories"] .= "\n".$cur;
            }
            $arShopData["categories"] .= "\n</categories>";

            // ���� ��������
            if(strlen($rowShop["local_delivery_cost"])>0)
            {
                $arShopData["local_delivery_cost"] = "<delivery-options><option cost=\"".$rowShop["local_delivery_cost"]."\" days=\"1-2\" /></delivery-options>";
            }
			
	        // �����, ������� ��������� � ����� (�� ����)
            if($rowShop["adult"]=="Y")
            {
                $arShopData["adult"] = "<adult>true</adult>";
            }

            // �����, ������� ��������� � ����� (�� ����)
            if(intval($rowShop["cpa"])>0)
            {
                $arShopData["cpa"] = "<cpa>1</cpa>";
            }
        }

        return $arShopData;
    }

    /**
     * �������� YML �������� ������ �����
     *
     * @param $rowShop
     * @return array
     */
    private function get_array_currencies($rowShop)
    {
        $arCurrencies = array();

        // ��������� yml ������� ������, ������������ �����������
        $arCurrencies[] = "<currency id=\"".$rowShop["currency_rate"]."\" rate=\"1\"/>";

        // ��������� yml �����
        if ($rowShop["currency_rate"]!='RUB')
        {
            if ($yml_currency_value = self::get_yml_currency_value($rowShop["currency_rub"], $rowShop["currency_rub_plus"], 'RUB'))
                $arCurrencies[] = $yml_currency_value;
        }

        // ��������� yml �������
        if ($yml_currency_value = self::get_yml_currency_value($rowShop["currency_usd"], $rowShop["currency_usd_plus"], 'USD'))
            $arCurrencies[] = $yml_currency_value;

        // ��������� yml ������������ �����
        if ($rowShop["currency_rate"]!='BYR')
        {
            if ($yml_currency_value = self::get_yml_currency_value($rowShop["currency_byr"], $rowShop["currency_byr_plus"], 'BYR'))
                $arCurrencies[] = $yml_currency_value;
        }

        // ��������� yml �����
        if ($rowShop["currency_rate"]!='KZT')
        {
            if ($yml_currency_value = self::get_yml_currency_value($rowShop["currency_kzt"], $rowShop["currency_kzt_plus"], 'KZT'))
                $arCurrencies[] = $yml_currency_value;
        }

        // ��������� yml ����
        if ($yml_currency_value = self::get_yml_currency_value($rowShop["currency_eur"], $rowShop["currency_eur_plus"], 'EUR'))
            $arCurrencies[] = $yml_currency_value;

        // ��������� yml ������
        if ($rowShop["currency_rate"]!='UAH')
        {
            if ($yml_currency_value = self::get_yml_currency_value($rowShop["currency_uah"], $rowShop["currency_uah_plus"], 'UAH'))
                $arCurrencies[] = $yml_currency_value;
        }

        return $arCurrencies;
    }

    /**
     * �������� YML �������� ��� ������������� ������ (���: currency)
     *
     * @param $row_currency
     * @param $row_currency_plus
     * @param $currency
     * @return bool|string
     */
    private function get_yml_currency_value($row_currency, $row_currency_plus, $currency)
    {
        global $APPLICATION;

        // ���� ������� ������������ ��� ������
        if(strlen($row_currency)>0)
        {
            // ���� ������� ����� �������� �� ������ �����
            if($row_currency=="MODULE" && CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
            {
                if ($arCur = CCurrency::GetByID($currency))
                {
                    if($arCur["AMOUNT"]>0 && $arCur["AMOUNT"]!=1)
                    {
                        return "<currency id=\"".$currency."\" rate=\"".$arCur["AMOUNT"]."\"/>";
                    }
                }
            }
            else
            {
                // ���� ������ ���������� �����, ��������� ���
                $curPlus = "";
                if ($row_currency_plus>0) $curPlus = " plus=\"".$row_currency_plus."\"";

                // ���������� ��������� �����
                $siteCharset = 'windows-1251';
                if (defined('BX_UTF') && BX_UTF==true)
                {
                    $siteCharset = 'UTF-8';
                }

                // ���������� �������� ������
                return "<currency id=\"".$currency."\" rate=\"".$APPLICATION->ConvertCharset(htmlspecialcharsbx($row_currency), $siteCharset, 'windows-1251')."\"".$curPlus."/>";
            }
        }

        return false;
    }

    /**
     * ���������� YML �������� ������ ��������� ��� ���� ����������, ��������� � "��������� ������" �������������
     *
     * @return array
     */
    private function get_array_categories()
    {
        global $DB;

        self::$arSectionIDs = array();
        self::$intMaxSectionID = array();

        // ����������� ��� ��������� "����������"
        $arIBlocks = array();
        $dbRes = $DB->Query("SELECT iblock_id FROM b_mibix_yam_datasource ORDER BY id ASC");
        while($arRes = $dbRes->Fetch())
        {
            $arIBlocks[] = intval($arRes["iblock_id"]);
        }

        // ������� ������������� ��������
        $arIBlocks = array_unique($arIBlocks);

        // ���������� ������ ��������� ��� ������� ���������
        $arCategories = array();
        foreach($arIBlocks as $iblock_id)
        {
            self::$intMaxSectionID[$iblock_id] = 0;

            // ������� �������� � ���������
            $res = CIBlock::GetByID($iblock_id);
            $iblock_arr = $res->GetNext();
            if($iblock_arr) {
                $arCategories[] = "<category id=\"" . $iblock_id . "\">" . self::yandex_text2xml($iblock_arr['NAME'], true) . "</category>";

            }
            $rsSections = CIBlockSection::GetList(array('LEFT_MARGIN'=>'ASC'), array('IBLOCK_ID'=>$iblock_id));
            while ($arSection = $rsSections->Fetch())
            {
                //$strParentId = "";
                if(intval($arSection['IBLOCK_SECTION_ID'])>0)
                    $strParentId = " parentId=\"".$arSection['IBLOCK_SECTION_ID']."\"";
                else
                    $strParentId = " parentId=\"".$iblock_id."\"";
                $arCategories[] = "<category id=\"".$arSection['ID']."\"".$strParentId.">".self::yandex_text2xml($arSection['NAME'], true)."</category>";

                // ���������� ID �������� ��������� � ������
                self::$arSectionIDs[$iblock_id][] = $arSection['ID'];

                // ��� ��������� ��������� ������������ ������
                if (self::$intMaxSectionID[$iblock_id] < $arSection["ID"]) self::$intMaxSectionID[$iblock_id] = $arSection["ID"];
            }
        }

        return $arCategories;
    }

    /**
     * �������� �������, �� ������ ���� ������ � ������������� � ��� ���������� ������
     */
    private function get_yml_offers($STEP_LIMIT=0, $SHOP_ID=1)
    {
        global $DB;
		
		$arOffers = array();
        $COUNTER = 0;
        self::$bStepEnd = false;

        // ���� ����� �����, �� ��������� ��� (������ ������ ������ ��������� ��������)
        $nTopCount = false;
        if($STEP_LIMIT>0)
        {
            $nTopCount = array("nTopCount" => $STEP_LIMIT);
        }

        // DEBUG
        //if (defined('MIBIX_DEBUG_YAMEXPORT') && MIBIX_DEBUG_YAMEXPORT==true)
        //self::writeLOG("[INFO] function:".__FUNCTION__." (nTopCount)", $nTopCount);

        // ��������� �� ������� �������� ���� ��������
        $arSaveSteps = self::get_save_steps($SHOP_ID);

        // DEBUG
        //if (defined('MIBIX_DEBUG_YAMEXPORT') && MIBIX_DEBUG_YAMEXPORT==true)
            //self::writeLOG("[INFO] function:".__FUNCTION__." (arSaveSteps)".$SHOP_ID, $arSaveSteps);

		
		
        // ������� ��� �������� ������� ��������
        $strRulesSQL = "
				SELECT
					ds.iblock_id, ds.include_sections, ds.exclude_sections, ds.include_items, ds.exclude_items, ds.include_sku, ds.dpurl_use_sku, ds.filters,
					r.*,
					g.salon, g.url as url_shop, g.utm
				FROM
					b_mibix_yam_datasource ds
				JOIN b_mibix_yam_rules r ON (ds.id=r.datasource_id)
				JOIN b_mibix_yam_general g ON (ds.shop_id=g.id)
				WHERE
                    ds.active = 'Y' AND ds.shop_id = ".$SHOP_ID." AND r.active = 'Y' AND r.id >= ".$arSaveSteps["rule_id"]."
                ORDER BY r.id ASC";
				
		
		
		
        $dbRulesRes = $DB->Query($strRulesSQL);
        while($arRule = $dbRulesRes->Fetch())
        {
			
			//self::writeLOG("�������");
			//self::writeLOG($arRule);
			
			// ������� � ��������, ������� ������ ������������
            $arIncSections = array_diff(explode(",", $arRule["include_sections"]), array(''));

            // ������, ������� ������ ������������
            $arIncItems = array_diff(explode(",", $arRule["include_items"]), array(''));

            // ������� � ��������, ������� �������� ������������
            $arExcSections = array_diff(explode(",", $arRule["exclude_sections"]), array(''));

            // ������, ������� �������� ������������
            $arExcItems = array_diff(explode(",", $arRule["exclude_items"]), array(''));

            // ��������� ������ ��� ������� ���������
            $arFilterOffers = Array(
                "IBLOCK_ID"=>IntVal($arRule["iblock_id"]),
                "ACTIVE"=>"Y",
            );

            // ��������� ������ ���������� �� ������� �������� ���� ��������
            $arSaveSteps = self::get_save_steps($SHOP_ID);

            // ��������� �� ��� �� �������� �� SKU? ���� ��, �� ���������� ������������ ID ��������� �������� (������� ">=")
            if(self::steps_include_sku($SHOP_ID))
                $arFilterOffers[">=ID"] = $arSaveSteps["element_id"];
            else
                $arFilterOffers[">ID"] = $arSaveSteps["element_id"];

            // DEBUG
            //if (defined('MIBIX_DEBUG_YAMEXPORT') && MIBIX_DEBUG_YAMEXPORT==true)
                //self::writeLOG("[INFO] function:".__FUNCTION__." (arFilterOffers)", $arFilterOffers);

            // ���������� ��������� ��� �������� �����������
            // D - �������� �������� �������� ���������
            // O - �������� �������� �������� ����������� (SKU)
            // +P - �������� �������, ������� �������� �����������, �� ��� �������� ��������� �� ��������
            // +X - �������� �������, ������� �������� �����������, ��� ��� ��� �������� ���� �������� �������� ���������.
            $intOfferIBlockID = 0;
            $boolOffersSKU = false;
            $arOffersSKU = array('SKU_PROPERTY_ID'=>0); // �� ���������
            $arCatalog = NULL;
            if(CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
                $arCatalog = CCatalog::GetByIDExt(IntVal($arRule["iblock_id"]));
            if (!empty($arCatalog))
            {
                $arOffersSKU = CCatalogSKU::GetInfoByProductIBlock(IntVal($arRule["iblock_id"]));
                if (!empty($arOffersSKU['IBLOCK_ID']))
                {
                    $intOfferIBlockID = $arOffersSKU['IBLOCK_ID'];
                    $rsOfferIBlocks = CIBlock::GetByID($intOfferIBlockID);
                    if (($arOfferIBlock = $rsOfferIBlocks->Fetch()))
                    {
                        $boolOffersSKU = true; // ���� �������� � ��������� �������������
                    }
                }
            }

            //�������� ������ ��������� ������� �� moshoztorg.ru/aktsii/
            $arActionElementIDs = array();
            $arSelect = Array("ID", "NAME", "PROPERTY_ELEMENT_ID");
            $arFilter = Array("IBLOCK_ID" => getIBlockIdByCode("actions"), "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
            $res      = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields                                                   = $ob->GetFields();
                $arActionElementIDs[$arFields['PROPERTY_ELEMENT_ID_VALUE']] = $arFields['PROPERTY_ELEMENT_ID_VALUE'];
            }

            // DEBUG
            //if (defined('MIBIX_DEBUG_YAMEXPORT') && MIBIX_DEBUG_YAMEXPORT==true)
                //self::writeLOG("[INFO] function:".__FUNCTION__." (boolOffersSKU)", $boolOffersSKU);

            // ������� ��� �������� ������� ������� � ����������� �� ID
            $resItems = CIBlockElement::GetList(array("ID" => "ASC"), $arFilterOffers, false, $nTopCount);
            while ($obItem = $resItems->GetNextElement())
            {
                $emptyItem = false;
                $arItem = $obItem->GetFields();
                $arItem["PROPERTIES"] = $obItem->GetProperties();

                //��������� �������� ��� ����� ���������
                if ( $arActionElementIDs[$arItem['ID']] ) {
                    $arItem['isActionElement'] = TRUE;
                }

				// ������� ��� ������, ������������� ��������
                $arItemGroups = Array();
                if(IntVal($arItem["IBLOCK_SECTION_ID"]) > 0)
                    $arItemGroups[] = $arItem["IBLOCK_SECTION_ID"];

                $dbGroups = CIBlockElement::GetElementGroups($arItem["ID"], true);
                while($arGroup = $dbGroups->Fetch())
                {
                    $arItemGroups[] = $arGroup["ID"];
                }

                // === ���������� �� �������� � ��������� ===
                // ���������� ������� ���� �� �� ����������� �� ������ �������
                if (empty($arItemGroups))
                {
					//self::writeLOG("���������� ������� ���� �� �� ����������� �� ������ �������");
                    $emptyItem = true;
                }
                // ���� �� ������ �� ���� ������ � ���� �� ������ �������, �� �� ���������
                elseif (empty($arIncSections) && !in_array($arItem["ID"], $arIncItems))
                {
					//self::writeLOG("���� �� ������ �� ���� ������ � ���� �� ������ �������, �� �� ���������");
                    $emptyItem = true;
                }
                // �� ��������� ������� ���� ���� ������ ������������� � ������ ����������
                elseif(empty($arIncItems) || !in_array($arItem["ID"], $arIncItems))
                {
                    // ���������� �������, ���� �� ���� ������ � ������ �����������
                    if(count($arExcItems)>0 && in_array($arItem["ID"], $arExcItems)) {
						$emptyItem = true;
						//self::writeLOG("�� ��������� ������� ���� ���� ������ ������������� � ������ ����������");
					}
                        

                    // ������� ��� �������, �������������� ��������
                    foreach ($arItemGroups as $itemGroupID)
                    {
                        if ($itemGroupID > 0)
                        {
                            // ���������� �������, ���� �� �� ����������� ���� �� ������ �������, ������� ������ ������������
                            if (count($arIncSections) > 0 && !in_array($itemGroupID, $arIncSections)) {
                                $emptyItem = true;
								//self::writeLOG("���������� �������, ���� �� �� ����������� ���� �� ������ �������, ������� ������ ������������");
								
                            } else { // ���� ��� �� ���� ������, ������� ������ ������������, �� �������� ���������� ������� � ������� �� �����
                                $emptyItem = false;
								//self::writeLOG("���� ��� �� ���� ������, ������� ������ ������������, �� �������� ���������� ������� � ������� �� �����");
                                break;
                            }

                            // ���������� �������, ���� �� ����������� ���� �� ������ �������, ������� ������������ ��������� � �����������
                            if (count($arExcSections) > 0 && in_array($itemGroupID, $arExcSections))
                                $emptyItem = true;
                        }
                    }
                }
				
				
				
		
		
		
                // === ~���������� �� �������� � ��������� ===

                // DEBUG
                //if (defined('MIBIX_DEBUG_YAMEXPORT') && MIBIX_DEBUG_YAMEXPORT==true)
                //{
                    //self::writeLOG("[INFO] function:" . __FUNCTION__ . " (COUNTER_1)", $COUNTER);
                    //self::writeLOG("[INFO] function:" . __FUNCTION__ . " (STEP_4)", self::$bStepEnd);
                //}

                // �������� YML-�������� � ����������� �� ���� ��������
                if( $emptyItem )
                {
                    $COUNTER++; //counter for empty elements
                }
                elseif (('P' == $arCatalog['CATALOG_TYPE'] || 'X' == $arCatalog['CATALOG_TYPE']) && $boolOffersSKU && IntVal($arOffersSKU['SKU_PROPERTY_ID'])>0)
                {
                    // DEBUG
                    //if (defined('MIBIX_DEBUG_YAMEXPORT') && MIBIX_DEBUG_YAMEXPORT==true)
                        //self::writeLOG("��� �������� �����������");

					// �������� YML-�������� ��� �������� offer � ��� �������� �����������, ���� ��� ����
                    $arTmpOffersSku = self::get_yml_offer_sku($intOfferIBlockID, $arOffersSKU['SKU_PROPERTY_ID'], $arRule, $arItem, $nTopCount, $COUNTER, $arSaveSteps, $SHOP_ID);


					//self::writeLOG($arTmpOffersSku);
					
                    if(!empty($arTmpOffersSku))
                        $arOffers = array_merge($arOffers, $arTmpOffersSku);
                }
                else
                {
                    // DEBUG
                    //if (defined('MIBIX_DEBUG_YAMEXPORT') && MIBIX_DEBUG_YAMEXPORT==true)
                        //self::writeLOG("[INFO] function:".__FUNCTION__." (CATALOG_TYPE)", "SIMPLE");

                    $COUNTER++; // ��������� ������� ������������� ��������

					
			
		
		
		
                    // �������� YML-�������� ��� �������� offer (�� ���������� �������� �����������)
                    $arTmpOffers = self::get_yml_offer($arRule, $arItem);
                    if(!empty($arTmpOffers))
                        $arOffers[] = $arTmpOffers;
                }
				
				
				
			
		
		

                // DEBUG
                //if (defined('MIBIX_DEBUG_YAMEXPORT') && MIBIX_DEBUG_YAMEXPORT==true)
                //{
                    //self::writeLOG("[INFO] function:" . __FUNCTION__ . " (COUNTER_2)", $COUNTER);
                    //self::writeLOG("[INFO] function:" . __FUNCTION__ . " (STEP_5)", self::$bStepEnd);
                //}

                // ���������, ������������ �� ����� sku ���� ���������� ����� ���������
                if(self::$bStepEnd)
                {
                    break 2; // ������������� ������� �� ���� (����) ������
                }
                else
                {
                    // ������ � ���� �������� ��������� ������������� ��������
                    $elementID = IntVal($arItem["ID"]);
                    self::steps_update($arRule["id"], $arRule["iblock_id"], $elementID, 0, 0, $SHOP_ID);

                    // �������� ������ ����
                    if(is_array($nTopCount) && isset($nTopCount["nTopCount"]) && intval($nTopCount["nTopCount"])>0)
                    {
                        // ���� ������� ������ ������, ������ �������� �� ��������� ��� (��������� ��������� � ����)
                        if($COUNTER >= intval($nTopCount["nTopCount"]))
                        {
                            // DEBUG
                            //if (defined('MIBIX_DEBUG_YAMEXPORT') && MIBIX_DEBUG_YAMEXPORT==true)
                                //self::writeLOG("[INFO] function:".__FUNCTION__." (STEP_7)", self::$bStepEnd);

                            self::$bStepEnd = true; // ��������, ��� ���� �������� ��-�� ���������� �������������� ������
                            break 2; // ������������� ������� �� ���� (����) ������
                        }
                    }
                }

            } // while end elements of rule

            self::steps_update($arRule["id"], $arRule["iblock_id"], 0, 0, 0, $SHOP_ID);

        } // while end rules

        return $arOffers;
    }

    /**
     * ��������� (����������) ���������� �� ������������ ID �������� (������)
     *
     * @param $ruleID
     * @param $IBlockID
     * @param $elementID
     * @param int $skuIBlockID
     * @param int $skuElementID
     */
    private function steps_update($ruleID, $IBlockID, $elementID, $skuIBlockID=0, $skuElementID=0, $shop=1)
    {
        // �����, ���� ������ ����� ������� ��������� ����� (��� ������� ���������)
        if(!self::$bCreate) return;

        self::steps_update_noncheck($ruleID, $IBlockID, $elementID, $skuIBlockID, $skuElementID, $shop);
    }

    /**
     * ��������� (����������) ���������� �� ������������ ID �������� (������) ��� �������� �� ����� �������
     * (����� ��� �������� ��������� ��� ������ ������� ��� ������� ��������)
     *
     * @param $ruleID
     * @param $IBlockID
     * @param $elementID
     * @param int $skuIBlockID
     * @param int $skuElementID
     */
    private function steps_update_noncheck($ruleID, $IBlockID, $elementID, $skuIBlockID=0, $skuElementID=0, $shop=1)
    {
        global $DB;

        $arSaveField = array(
			"id" => $shop,
            "rule_id" => $ruleID,
            "iblock_id" => $IBlockID,
            "element_id" => $elementID,
            "sku_iblock_id" => $skuIBlockID,
            "sku_element_id" => $skuElementID,
        );
        $dbResYMC = $DB->Query("SELECT id FROM b_mibix_yam_steps_load WHERE id='".$shop."'");
        if($dbArYMC = $dbResYMC->Fetch()) // update
        {
            $strStepsUpdateSQL = $DB->PrepareUpdate("b_mibix_yam_steps_load", $arSaveField);
            if (strlen($strStepsUpdateSQL)>0)
            {
                $strSql = "UPDATE b_mibix_yam_steps_load SET ".$strStepsUpdateSQL." WHERE id='".$dbArYMC["id"]."'";
                //echo $strSql;
                $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
            }
        }
        else // insert
        {
            $DB->Add("b_mibix_yam_steps_load", $arSaveField);
        }
    }

    /**
     * ��� ��������� �� �������� SKU?
     *
     * @param int $shop
     * @return bool
     */
    private function steps_include_sku($shop=1)
    {
        global $DB;

        // �����, ���� ������ ����� ������� ��������� ����� (��� ������� ���������)
        if(!self::$bCreate) return false;

        $dbResYMC = $DB->Query("SELECT sku_element_id FROM b_mibix_yam_steps_load WHERE id='".$shop."'");
        if($dbArYMC = $dbResYMC->Fetch())
        {
            if(intval($dbArYMC["sku_element_id"])>0) return true;
        }

        return false;
    }

    /**
     * �������� ������� ������ ��������� �������� (� �������� ��� �������)
     *
     * @param int $shop
     * @return int
     */
    private function get_step_status($shop=1)
    {
        global $DB;

        $arStep = array();

        $dbStepsLoadRes = $DB->Query("SELECT in_proccess,in_blocked,last_run_time FROM b_mibix_yam_steps_load WHERE id='".$shop."'");
        if ($arStepsLoadRes = $dbStepsLoadRes->Fetch())
        {
            $arStep["in_proccess"] = $arStepsLoadRes["in_proccess"];
            $arStep["in_blocked"] = $arStepsLoadRes["in_blocked"];
            $arStep["last_run_time"] = $arStepsLoadRes["last_run_time"];
        }

        return $arStep;
    }

    /**
     * �������� ������������� �������� ��������
     *
     * @param int $shop
     * @return int
     */
    private function get_step_interval($shop=1)
    {
        global $DB;

        $arStep = array();

        $dbStepsLoadRes = $DB->Query("SELECT step_interval_run FROM b_mibix_yam_general WHERE id='".$shop."'");
        if ($arStepsLoadRes = $dbStepsLoadRes->Fetch())
        {
            $arStep["step_interval_run"] = $arStepsLoadRes["step_interval_run"];
        }

        return $arStep;
    }

    /**
     * �������� ������������� �������� ��������
     *
     * @param int $shop
     * @return int
     */
    public function get_step_settings($shop=1)
    {
        global $DB;

        $arStep = array();

        $dbStepsLoadRes = $DB->Query("SELECT step_limit,step_path FROM b_mibix_yam_general WHERE id='".$shop."'");
        if ($arStepsLoadRes = $dbStepsLoadRes->Fetch())
        {
            if (!empty($arStepsLoadRes["step_limit"]) && !empty($arStepsLoadRes["step_path"]))
            {
                $arStep["step_limit"] = $arStepsLoadRes["step_limit"];
                $arStep["step_path"] = $arStepsLoadRes["step_path"];
            }
        }

        return $arStep;
    }

    /**
     * �������� ������� ������ ����
     *
     * @param int $shop
     * @return array
     */
    public function get_save_steps($shop=1)
    {
        global $DB;

        // ��������� �� ������� �������� ���� ��������
        $arSaveSteps = array( // ����������, � ������� ����� ������� ����������� ������ ��� ��������
            "id" => 0,
            "in_proccess" => 0,
            "rule_id" => 0,
            "iblock_id" => 0,
            "element_id" => 0,
            "sku_iblock_id" => 0,
            "sku_element_id" => 0,
        );
        $dbStepsLoadRes = $DB->Query("SELECT * FROM b_mibix_yam_steps_load WHERE id='".$shop."'");
        if ($arStepsLoadRes = $dbStepsLoadRes->Fetch())
        {
            $arSaveSteps = $arStepsLoadRes;
        }

        return $arSaveSteps;
    }

    /**
     * ������������� ������� ������ ��������� �������� (� �������� ��� �������)
     *
     * @param string $value
     * @param int $shop
     */
    private function set_proccess_status($value, $shop=1)
    {
        global $DB;

        $strSql = "UPDATE b_mibix_yam_steps_load SET in_proccess='".$value."' WHERE id='".$shop."'";
        $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
    }

    /**
     * ������������� ������� ������ ���������� ��� ��������� ��������
     *
     * @param string $value
     * @param int $shop
     */
    private function set_block_status($value, $shop=1)
    {
        global $DB;

        $strSql = "UPDATE b_mibix_yam_steps_load SET in_blocked='".$value."' WHERE id='".$shop."'";
        $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
    }

    /**
     * ������������� ������� ������ ���������� ��� ��������� ��������
     *
     * @param int $shop
     */
    private function set_last_time_run($shop=1)
    {
        global $DB;

        $strSql = "UPDATE b_mibix_yam_steps_load SET last_run_time=".$DB->GetNowFunction()." WHERE id='".$shop."'";
        $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
    }

    /**
     * ���������� ������� ��������� ���������� ����
     *
     * @param int $shop
     */
    private function update_last_time_step($shop=1)
    {
        global $DB;

        $strSql = "UPDATE b_mibix_yam_steps_load SET last_step_time=".$DB->GetNowFunction()." WHERE id='".$shop."'";
        $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
    }

    /**
     * �������� � ���������� ��������� ��������� (���� ������ ������� ���������� �� �����-�� ��������)
     * (600 -> 10 �����, - ����� ������ �������� ������ ��������� ��������)
     *
     * @param int $shop
     */
    private function check_freeze_process($shop=1)
    {
        global $DB;

        $dbStepsLoadRes = $DB->Query("SELECT last_step_time FROM b_mibix_yam_steps_load WHERE in_proccess='Y' AND in_blocked='Y' AND id='".$shop."'");
        if ($arStepsLoadRes = $dbStepsLoadRes->Fetch())
        {
            $strTimeDiff = time() - strtotime($arStepsLoadRes["last_step_time"]);
            if($strTimeDiff > 600)
            {
                self::set_proccess_status("N", $shop);
                self::set_block_status("N", $shop);
            }
        }
    }

    /**
     * ���������� YML-�������� �������� ����������� ��� �������� � ����������� �� ��� ���� � �.������
     *
     * @param $skuIBlockID int ID ��������� � ��������� �������������
     * @param $skuPropertyID int ID �������� �������� � �������� ������������
     * @param $arRule array ������ � ������� �������
     * @param $arItem array ������ � ������� ��������
     * @param $COUNTER int ������� ���������� ������������ ��������� (�������� �� ������)
     * @param $nTopCount array|bool �������� ���������� � ���������� ���������� ���������
     * @param $arSaveSteps array ������ � ������� ���������� ��������
     * @return string ������� <offer> � ������������ ���������� �������� ����
     */
    private function get_yml_offer_sku($skuIBlockID, $skuPropertyID, $arRule, $arItem, $nTopCount, &$COUNTER, $arSaveSteps, $SHOP_ID=1)
    {
        $arOffers = array();
        $existOffers = false;


        // DEBUG
        //if (defined('MIBIX_DEBUG_YAMEXPORT') && MIBIX_DEBUG_YAMEXPORT==true)
        //self::writeLOG("������� SKU ".$arItem['ID'], $COUNTER);

		// ���������� �� ���� �������� ������������� �������� ��������
        if ($arRule["include_sku"]=="Y")
        {
            $arOfferFilter = array(
                'IBLOCK_ID' => $skuIBlockID,
                'PROPERTY_'.$skuPropertyID => 0,
                ">ID" => $arSaveSteps["sku_element_id"],
                "ACTIVE" => "Y",
                "ACTIVE_DATE" => "Y"
            );
            $arOfferFilter['PROPERTY_'.$skuPropertyID] = $arItem['ID'];
			
		
				
				
				
            $rsOfferItems = CIBlockElement::GetList(array(), $arOfferFilter, false, $nTopCount/*,$arOfferSelect*/);
            while ($obOfferItem = $rsOfferItems->GetNextElement())
            {
				
				
		
                $existOffers = true;
                $arOfferItem = $obOfferItem->GetFields();
                $arOfferItem["PROPERTIES"] = $obOfferItem->GetProperties();
                //
                //

                $tmpOffer = self::get_yml_offer($arRule, $arItem, $arOfferItem);
                if(!empty($tmpOffer))
                    $arOffers[] = $tmpOffer;

                // ������ � ���� �������� ��������� ������������� ��������
                $elementID = IntVal($arItem["ID"]);
                self::steps_update($arRule["id"], $arRule["iblock_id"], $elementID, $skuIBlockID, $arOfferItem["ID"], $SHOP_ID);

                // ����������� ������� �� �������� ������ ����
                $COUNTER++;

                // DEBUG
                //if (defined('MIBIX_DEBUG_YAMEXPORT') && MIBIX_DEBUG_YAMEXPORT==true)
                //{
                    //self::writeLOG("[INFO] function:" . __FUNCTION__ . " (ITEM_ID)", $arItem["ID"]);
                    //self::writeLOG("[INFO] function:" . __FUNCTION__ . " (COUNTER_4)", $COUNTER);
                    //self::writeLOG("[INFO] function:" . __FUNCTION__ . " (nTopCount_sku)", $nTopCount);
                //}

                // �������� �� ����� ����
                if(is_array($nTopCount) && isset($nTopCount["nTopCount"]) && intval($nTopCount["nTopCount"])>0)
                {
                    // ���� ������� ������ ������, ������ �������� �� ��������� ��� (��������� ��������� � ����)
                    if($COUNTER >= intval($nTopCount["nTopCount"]))
                    {
                        // DEBUG
                        //if (defined('MIBIX_DEBUG_YAMEXPORT') && MIBIX_DEBUG_YAMEXPORT==true)
                            //self::writeLOG("[INFO] function:".__FUNCTION__." (STEP_6)", self::$bStepEnd);

                        self::$bStepEnd = true; // ��������, ��� ���� �������� ��-�� ���������� �������������� ������
                        break; // ��������� ���� ��� ���������� ������
                    }
                }

            }
        }

        // DEBUG
        //if (defined('MIBIX_DEBUG_YAMEXPORT') && MIBIX_DEBUG_YAMEXPORT==true)
            //self::writeLOG("[INFO] function:".__FUNCTION__." (COUNTER_5)", $COUNTER);

		
		
				
				
        // ���� � �������� ����������� �����������, �� �������� ���������� ��� ��� ������� �������
        if(!$existOffers)
        {
			//self::writeLOG("���� � �������� ����������� �����������, �� �������� ���������� ��� ��� ������� �������");
			//self::writeLOG($arRule);
			//self::writeLOG($arItem);
			
			$tmpOffer = self::get_yml_offer($arRule, $arItem);
				
			//self::writeLOG("��� ������� ������� ��������");
			//self::writeLOG($tmpOffer);
				
            if(!empty($tmpOffer))
                $arOffers[] = $tmpOffer;

            // ������ � ���� �������� ��������� ������������� ��������
            $elementID = IntVal($arItem["ID"]);
            self::steps_update($arRule["id"], $arRule["iblock_id"], $elementID, 0, 0, $SHOP_ID);

            $COUNTER++;
        }

        // DEBUG
        //if (defined('MIBIX_DEBUG_YAMEXPORT') && MIBIX_DEBUG_YAMEXPORT==true)
        //self::writeLOG("��������� ��������� sku", $COUNTER);

		
		
		
		
        return $arOffers;
    }

    /**
     * ���������� YML-�������� �������� � ����������� �� ���� �.������
     *
     * @param $arRule array ������ � ������� �������
     * @param $arItem array ������ � ������� ��������
     * @param $arOfferItemSKU array ������ � ������� ��������� ����������� (���� ��� ��������)
     * @return string ������� <offer> � ������������ ���������� �������� ����
     */
    private function get_yml_offer($arRule, $arItem, $arOfferItemSKU=array())
    {
		//self::writeLOG("������ � get_yml_offer");


        // ������������ �������� ������ �� ���������
        $strOffers = '';

        // �������� ���������������� ����������
        if(!self::check_filter($arRule, $arItem, $arOfferItemSKU)) {
			//self::writeLOG("********************************");
			//self::writeLOG($arRule);
			//self::writeLOG($arItem);
			//self::writeLOG($arOfferItemSKU);
			
			//self::writeLOG("�� ������ ���������� check_filter");
			return $strOffers;			
		}
            
		//self::writeLOG("��� ".$arRule["type"], $COUNTER);
		
			
        // ���������� ��� �� �.������� � ��������� ��������
        $yType = '';
        $yOfferGroupID = '';
        $arOffer = array();


		switch($arRule["type"])
        {
            case "vendor.model":
                $arOffer = self::get_yml_offer_vendor_model($arRule, $arItem, $arOfferItemSKU);
                $yType = " type=\"vendor.model\"";
                break;
            case "book":
                $arOffer = self::get_yml_offer_book($arRule, $arItem, $arOfferItemSKU);
                $yType = " type=\"book\"";
                break;
            case "audiobook":
                $arOffer = self::get_yml_offer_audiobook($arRule, $arItem, $arOfferItemSKU);
                $yType = " type=\"audiobook\"";
                break;
            case "artist.title.m":
                $arOffer = self::get_yml_offer_artist_title_m($arRule, $arItem, $arOfferItemSKU);
                $yType = " type=\"artist.title\"";
                break;
            case "artist.title.v":
                $arOffer = self::get_yml_offer_artist_title_v($arRule, $arItem, $arOfferItemSKU);
                $yType = " type=\"artist.title\"";
                break;
            case "tour":
                $arOffer = self::get_yml_offer_tour($arRule, $arItem, $arOfferItemSKU);
                $yType = " type=\"tour\"";
                break;
            case "event-ticket":
                $arOffer = self::get_yml_offer_event_ticket($arRule, $arItem, $arOfferItemSKU);
                $yType = " type=\"event-ticket\"";
                break;
			case "simple":
                $arOffer = self::get_yml_offer_simple($arRule, $arItem, $arOfferItemSKU);
                break;
            default:
                $arOffer = self::get_yml_offer_simple($arRule, $arItem, $arOfferItemSKU);
        }
		
		//self::writeLOG("��� ��� ��� ���� ���������", $COUNTER);
		//self::writeLOG($arOffer, $COUNTER);

        //echo $arRule["type"];

		//print_R( $arOffer  );

		//exit;

        // ���� �������� ��������� ��������
        if(!empty($arOffer))
        {
            // Id-������
            $offerID = $arItem['ID'];
            if(!empty($arOfferItemSKU))
                $offerID = $arOfferItemSKU['ID'];

            // ������������ ��������� ��� ���� <offer>
            $yBid = self::get_property_attribute_tag("bid", $arRule, $arItem, '', $arOfferItemSKU);
            $yCbid = self::get_property_attribute_tag("cbid", $arRule, $arItem, '', $arOfferItemSKU);
            $yAvailable = self::get_property_attribute_tag("available", $arRule, $arItem, "true", $arOfferItemSKU);

            // ���.������� group_id ��� ��������� ������
            if($arRule["adt_dress_group_id"]=="Y" && count($arOfferItemSKU) > 0)
            {
                $yOfferGroupID = " group_id=\"".$arItem["ID"]."\"";
            }

            // ��������� �������� � �������� ��� <offer>
            $strOffers = "<offer id=\"".$offerID."\"".$yType.$yBid.$yCbid.$yAvailable.$yOfferGroupID.">\n";
            foreach($arOffer as $ofParam)
            {
                if (count($ofParam)>0)
                    $strOffers .= $ofParam . "\n";
            }
            $strOffers .= "</offer>";
        }

        return $strOffers;
    }

    /**
     * �������� yml ��� URL
     *
     * @param $arRule array ������ �� ���������� �������
     * @param $arItem array ������ �� ���������� ��������������� ��������
     * @param $arOfferItemSKU array ������ �� ���������� ��������� ����������� (���� ��� ��������)
     * @return string �������������� ��� ��� ������ ������
     */
    private function get_yml_offer_url($arRule, $arItem, $arOfferItemSKU=array())
    {
        $strReturn = '';

        if($arRule["salon"]=="Y" && $arRule["url"]!="Y") {}
        else
        {
            // ��������� ������
            $tmpURL = $arItem["DETAIL_PAGE_URL"];
            $outURL = htmlspecialcharsbx($arItem["~DETAIL_PAGE_URL"]);

            // ���� ������������ ������ "����� ������ �� ����� �� SKU-���������"
            if(!empty($arOfferItemSKU) && $arRule["dpurl_use_sku"]=="Y")
            {
                $tmpURL = $arOfferItemSKU["DETAIL_PAGE_URL"];
                $outURL = htmlspecialcharsbx($arOfferItemSKU["~DETAIL_PAGE_URL"]);
            }

            // UTM-����� ���� ����
            if(strlen($arRule["utm"]))
            {
                // [������������ utm] - ������� ������� �� ������������ ID �������� (��� �������� ������ ��� sku)
                if(empty($arOfferItemSKU))
                    $arRule["utm"] = str_replace("#ITEM_ID#", $arItem["ID"], $arRule["utm"]);
                else
                    $arRule["utm"] = str_replace("#ITEM_ID#", $arOfferItemSKU["ID"], $arRule["utm"]);

                // [������������ utm] - ������� ������� �� ������������ ID �������� (��� sku)
                $arRule["utm"] = str_replace("#PARENT_ID#", $arItem["ID"], $arRule["utm"]);

                $outURL = $outURL . (strstr($tmpURL, '?') === false ? '?' : '&amp;') . $arRule["utm"];
            }

            // ���������� ������ ��� ��������� �����������
            if(!empty($arOfferItemSKU))
            {
                $outURL = $outURL . (strstr($outURL, '?') === false ? '?' : '&amp;') . "offer=" . $arOfferItemSKU["ID"];
            }

            $strReturn = "<url>".CMibixYandexTools::getFixURL($arRule["url_shop"], $outURL, false)."</url>";
        }

        return $strReturn;
    }

    /**
     * �������� yml ��� ����
     *
     * @param $arRule array ������ �� ���������� �������
     * @param $arItem array ������ �� ���������� ��������������� ��������
     * @param $arOfferItemSKU array ������ �� ���������� ��������� ����������� (���� ��� ��������)
     * @return string �������������� ��� ��� ������ ������
     */
    private function get_yml_offer_price($arRule, $arItem, $arOfferItemSKU=array())
    {

        $arReturn = array();
		$arReturnOld = array();
		
		//self::writeLOG("������ � ����");
		
        // ���������� ID ������ ��� ��������� �����������, � �������� ���������� ������ ����
        $tmpItemID = $arItem['ID'];
        if(!empty($arOfferItemSKU))
            $tmpItemID = $arOfferItemSKU['ID'];

        // ������������ ���� <oldprice>
        if (!empty($arRule["oldprice"]))
        {
			
			//self::writeLOG("���� ������ ����");
			
            // �������� ���� ������� � ID ����� ���, ��������� � ���������� ����� �������
            if(is_numeric($arRule["oldprice"])) // ������ ��� ����
            {
				//self::writeLOG("� ����");
				
                $arReturnOld = self::get_need_price($tmpItemID, $arRule["oldprice"], $arItem["LID"], $arRule["oldprice_optimal"], true);
				$maxPrice = intval($arReturnOld["maxprice"]);
            }
            else // ������� ��������
            {
				//self::writeLOG("� ��������");
				
                $strTag = self::get_property_value($arRule["oldprice"], $arItem, false, 0, $arOfferItemSKU);
                if(!empty($strTag))
                {
                    $maxPrice = intval($strTag);
                    $arReturnOld["oldprice"] = "<oldprice>" . $maxPrice . "</oldprice>";
                }
            }
        }
		
		//self::writeLOG("��� ����");	
		//self::writeLOG($arRule["price"]);	
		
			

        // ������������ ���� <price>
        if(is_numeric($arRule["price"])) // ������ ��� ����
        {
			//self::writeLOG("����������� ������ ���� =".$tmpItemID.'+'.$arRule["price"].'+'.$arItem["LID"].'+'.$arRule["price_optimal"]);
			
			//446071+1+el+Y
			
			$arReturn = self::get_need_price($tmpItemID, $arRule["price"], $arItem["LID"], $arRule["price_optimal"]);
			//self::writeLOG(print_r ($arReturn, 1));	
            $minPrice = $arReturn["minprice"];
        }
        else // ������� ��������
        {
            $strTag = self::get_property_value($arRule["price"], $arItem, false, 0, $arOfferItemSKU);
            if(!empty($strTag))
            {
                $minPrice = $strTag;

                if(!empty($arRule["price_currency"]))
                    $minPriceCurrency = $arRule["price_currency"]; // ��� ������ ������������ ������������� � ����������
                else
                    $minPriceCurrency = "RUB"; // ����� �� ���������

                $arReturn["minprice"] = $minPrice;
                $arReturn["price"] = "<price>".$minPrice."</price>";
                $arReturn["currency"] = "<currencyId>".$minPriceCurrency."</currencyId>";

            }
        }

		//self::writeLOG("�� ������");
		//self::writeLOG(print_r ($arReturn, 1));	
		//self::writeLOG(print_r ($arReturnOld, 1));	
		
		
		$arReturn = array_merge($arReturn, $arReturnOld);
		
		//self::writeLOG("�����");	
		//self::writeLOG(print_r ($arReturn, 1));	
		
		
	    // ������ ��� �� ��������� � ��������� ����������
        if(!empty($maxPrice) && !empty($minPrice))
        {
            if($minPrice >= $maxPrice) unset($arReturn["oldprice"]);
        }

        return $arReturn;
    }

    /**
     * ���������� � �������� ���� ������ � ������ ���������� ����������
     *
     * @param $itemID
     * @param $catalogID
     * @param $siteLID
     * @param $optimalPrice
     * @param bool $isOld
     * @return array
     */
    private function get_need_price($itemID, $catalogID, $siteLID, $optimalPrice, $isOld=false)
    {
        $arReturn = Array();
		
		//self::writeLOG("������ � ����������� ����");

        if(CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
        {
            $rsPrices = CPrice::GetListEx(array(), array(
                    'PRODUCT_ID' => $itemID,
                    'CATALOG_GROUP_ID' => $catalogID,
                    'CAN_BUY' => 'Y'
                )
            );
			
			
			
            if ($arPrice = $rsPrices->Fetch())
            {
				//self::writeLOG("���� ����");				
				//self::writeLOG(print_r($arPrice,1));
				
                // �������� ����������� ���� ��� ������ ���� <oldprice>
                $mPrice = 0;
                if($optimalPrice=="Y")
                {
					
					//self::writeLOG("���� �����������");
					
                    if ($arOptimalOldPrice = CCatalogProduct::GetOptimalPrice(
                        $itemID,
                        1,
                        array(2), // anonymous
                        'N',
                        array($arPrice),
                        $siteLID
                    )
                    ) {
						
						//self::writeLOG("����� �����������");
						//self::writeLOG($arOptimalOldPrice['DISCOUNT_PRICE']);
						
                        $mPrice = $arOptimalOldPrice['DISCOUNT_PRICE'];




                    }
                } else {
                    $mPrice = $arPrice["PRICE"];
                }

                print_r($mPrice);

                // ����� ��� ������ ����
                if($isOld)
                {
                    $arReturn["maxprice"] = $mPrice;
                    $arReturn["oldprice"] = "<oldprice>" . $mPrice . "</oldprice>";


                }
                else
                {
                    $minPriceCurrency = CCurrency::GetBaseCurrency();
                    $arReturn["minprice"] = $mPrice;
                    $arReturn["price"] = "<price>".$mPrice."</price>";
                    $arReturn["currency"] = "<currencyId>".$minPriceCurrency."</currencyId>";
                }

            } else {
				//self::writeLOG("��� ���");
			}
        } else {
			//self::writeLOG("�� ������� �������");
		}
		
		
		//self::writeLOG("������ �����������");
		//self::writeLOG(print_r($arReturn, 1));

        return $arReturn;
    }

    /**
     * �������� yml ��������� ������
     *
     * @param $arRule array ������ �� ���������� �������
     * @param $arItem array ������ �� ���������� ��������������� ��������
     * @return string �������������� ��� ��� ������ ������
     */
    private function get_yml_offer_category($arRule, $arItem)
    {
        $strReturn = '';

        $boolCurrentSections = false;
        $bNoActiveGroup = true;

        // ��� ���������� ��������� ��������, ������ � ����������� $arSectionIDs ����� ������, �������� ���
        if(empty(self::$arSectionIDs))
        {
            self::get_array_categories();
        }

        $db_res1 = CIBlockElement::GetElementGroups($arItem['ID'], false, array('ID', 'ADDITIONAL_PROPERTY_ID'));
        while ($ar_res1 = $db_res1->Fetch())
        {
            // ���� ������� �������� � ������� ����� ��������
            if (intval($ar_res1['ADDITIONAL_PROPERTY_ID']) > 0) continue;

            $boolCurrentSections = true;
            if (in_array(intval($ar_res1["ID"]), self::$arSectionIDs[$arRule["iblock_id"]]))
            {
                $strReturn = "<categoryId>".$ar_res1["ID"]."</categoryId>";
                $bNoActiveGroup = false;
            }
        }
        if (!$boolCurrentSections)
        {
            //TODO: ���. ��� ���������� ������ ��������� � <shop> => $boolNeedRootSection = true;
            $strReturn = "<categoryId>".self::$intMaxSectionID[$arRule["iblock_id"]]."</categoryId>";
        }
        else
        {
            if ($bNoActiveGroup) return array();
        }


        return $strReturn;
    }

    /**
     * �������� yml ��� ���� ����������� � �������� �� ������.�������
     *
     * @param $arRule array ������ �� ���������� �������
     * @param $arItem array ������ �� ���������� ��������������� ��������
     * @return string �������������� ��� ��� ������ ������
     */
    private function get_yml_offer_market_category($arRule, $arItem)
    {
        global $DB;
        $strReturn = '';

        if(!empty($arRule["market_category_id"]))
        {
            $arCategoryYMarket = array();
            $dbResYMC = $DB->Query("SELECT name_category FROM b_mibix_yam_market_categories WHERE id IN(".$DB->ForSql($arRule["market_category_id"]).")");
            if($dbResYMC)
            {
                while($arResYMC = $dbResYMC->Fetch())
                {
                    $arCategoryYMarket[] = $arResYMC["name_category"];
                }
                if(!empty($arCategoryYMarket))
                {
                    $strReturn = "<market_category>".self::yandex_text2xml(implode('/',$arCategoryYMarket), true)."</market_category>";
                }
            }
        }


        return $strReturn;
    }

    /**
     * �������� yml �������� ������
     *
     * @param $arRule array ������ �� ���������� �������
     * @param $arItem array ������ �� ���������� ��������������� ��������
     * @return string �������������� ��� ��� ������ ������
     */
    private function get_yml_offer_pictures($arRule, $arItem, $arOfferItemSKU=Array())
    {
        $arReturn = array();

        $arPictureSettings = explode(",", $arRule["picture"]);
        if(count($arPictureSettings)>0)
        {
            foreach($arPictureSettings as $picSetting)
            {
                if($picSetting == "PREVIEW_PICTURE")
                {
                    if (intval($arItem["PREVIEW_PICTURE"])>0)
                    {
                        $pictNo = intval($arItem["PREVIEW_PICTURE"]);
                        $arReturn[] = self::get_yml_picture_by_code($pictNo, $arRule["url_shop"]);
                    }
                }
                elseif($picSetting == "DETAIL_PICTURE")
                {
                    if (intval($arItem["DETAIL_PICTURE"])>0)
                    {
                        $pictNo = intval($arItem["DETAIL_PICTURE"]);
                        $arReturn[] = self::get_yml_picture_by_code($pictNo, $arRule["url_shop"]);
                    }
                }
                elseif($picSetting == "sku@PREVIEW_PICTURE")
                {
                    if (intval($arOfferItemSKU["PREVIEW_PICTURE"])>0)
                    {
                        $pictNo = intval($arOfferItemSKU["PREVIEW_PICTURE"]);
                        $arReturn[] = self::get_yml_picture_by_code($pictNo, $arRule["url_shop"]);
                    }
                }
                elseif($picSetting == "sku@DETAIL_PICTURE")
                {
                    if (intval($arOfferItemSKU["DETAIL_PICTURE"])>0)
                    {
                        $pictNo = intval($arOfferItemSKU["DETAIL_PICTURE"]);
                        $arReturn[] = self::get_yml_picture_by_code($pictNo, $arRule["url_shop"]);
                    }
                }
                else
                {
                    $arProperty = self::get_property_array($picSetting, $arItem, $arOfferItemSKU); // ���������� �������� ������ ��� sku
                    if(!empty($arProperty))
                    {
                        if($arProperty["PROPERTY_TYPE"]=="F") {

                            if(is_array($arProperty["VALUE"]) && count($arProperty["VALUE"])>0)
                            {
								$counter = 0;
                                foreach($arProperty["VALUE"] as $pictNo)
                                {
									if ($counter > 6) break;
                                    $arReturn[] = self::get_yml_picture_by_code($pictNo, $arRule["url_shop"]);
									$counter++;
                                }
                            }
                            elseif(intval($arProperty["VALUE"])>0)
                            {
                                $pictNo = intval($arProperty["VALUE"]);
                                $arReturn[] = self::get_yml_picture_by_code($pictNo, $arRule["url_shop"]);
                            }
                        } elseif($arProperty["PROPERTY_TYPE"]=="S" && strlen($arProperty["VALUE"])) {

                            $arReturn[] = "<picture>".CMibixYandexTools::getFixURL($arRule["url_shop"], $arProperty["VALUE"])."</picture>";
                        }
                    }
                }
            }
        }

        return $arReturn;
    }

    /**
     * �������� yml ��� �������� ������
     *
     * @param $rule_name string ������ �� ��������� ����� ��� ������
     * @param $arItem array ������ �� ���������� ��������������� ��������
     * @param $arOfferItemSKU array ������ �� ���������� ��������� ����������� (���� ��� ��������)
     * @return string �������������� ��� ��� ������ ������
     */
    private function get_yml_offer_name($rule_name, $arItem, $arOfferItemSKU=array())
    {
        $strReturn = '';

        if($strTag = self::get_property_value($rule_name, $arItem, false, 0, $arOfferItemSKU))
        {
            if(strlen($strTag))
            {
                if($strTag=="catname" || $strTag=="catnamesku" || $strTag=="catnameboth") // �� �������� ��������
                {
                    if(!empty($arOfferItemSKU) && $strTag=="catnamesku")
                        $strReturn = "<name>".self::yandex_text2xml($arOfferItemSKU["NAME"], true)."</name>";
                    elseif(!empty($arOfferItemSKU) && $strTag=="catnameboth")
                        $strReturn = "<name>".self::yandex_text2xml($arItem["NAME"], true) . " " . self::yandex_text2xml($arOfferItemSKU["NAME"], true)."</name>";
                    else
                        $strReturn = "<name>".self::yandex_text2xml($arItem["NAME"], true)."</name>";
                }
                else // �� �������� ��������
                {
                    $strReturn = "<name>".$strTag."</name>";
                }
            }
        }

        return $strReturn;
    }

    /**
     * �������� yml ��� ��������
     *
     * @param $arRule array ������ �� ���������� �������
     * @param $arItem array ������ �� ���������� ��������������� ��������
     * @return string �������������� ��� ��� ������ ������
     */
    private function get_yml_offer_description($arRule, $arItem, $arOfferItemSKU=Array())
    {
        $strReturn = '';

        if(strlen($arRule["description"]))
        {
            if($arRule["description"] == "PREVIEW_TEXT")
            {
                if(strlen($arItem["PREVIEW_TEXT"]))
                {
                    if(strlen($arItem["PREVIEW_TEXT"]))
                        $strReturn = self::yandex_text2xml($arItem["~PREVIEW_TEXT"], true, false, true, 255);
                }
            }
            elseif($arRule["description"] == "DETAIL_TEXT")
            {
                if(strlen($arItem["DETAIL_TEXT"]))
                    $strReturn = self::yandex_text2xml($arItem["~DETAIL_TEXT"], true, false, true, 255);
            }
            else
            {
                if($strTag = self::get_property_value($arRule["description"], $arItem, true, 255, $arOfferItemSKU))
                    if(strlen($strTag))
                        $strReturn = $strTag;
            }

            // ����������� ��������
            if (strlen($strReturn) && $arRule["description_frm"]=="Y")
            {
                $strReturn = self::sentence_cap($strReturn);
            }

            // ����������� � ���
            if(strlen($strReturn))
            {
                $strReturn = "<description>".$strReturn."</description>";
            }
        }

        return $strReturn;
    }

    /**
     * �������� yml ��� ����������� �� ��������
     *
     * @param $arRule array ������ �� ���������� �������
     * @param $arItem array ������ �� ���������� ��������������� ��������
     * @return string �������������� ��� ��� ������ ������
     */
    private function get_yml_offer_age($arRule, $arItem, $arOfferItemSKU=Array())
    {
        $strReturn = '';

        if($strTag = self::get_property_value($arRule["age"], $arItem, false, 0, $arOfferItemSKU))
        {
            if(in_array($strTag, array('0','1','2','3','4','5','6','7','8','9','10','11','12','16','18')))
            {
                $ymlAgeUnit = "";
                if($strUnitVal = self::get_property_value($arRule["ageunit"], $arItem, false, 0, $arOfferItemSKU))
                {
                    if(in_array($strUnitVal,array('month','year')))
                    {
                        $ymlAgeUnit = " unit=\"".$strUnitVal."\"";
                    }
                }
                $strReturn = "<age".$ymlAgeUnit.">".$strTag."</age>";
            }
        }

        return $strReturn;
    }

    /**
     * �������� yml ��� �����-���
     *
     * @param $arRule array ������ �� ���������� �������
     * @param $arItem array ������ �� ���������� ��������������� ��������
     * @return string �������������� ��� ��� ������ ������
     */
    private function get_yml_offer_barcode($arRule, $arItem, $arOfferItemSKU=Array())
    {
        $arReturn = array();

        if($strTag = self::get_property_value($arRule["barcode"], $arItem, false, 0, $arOfferItemSKU))
        {
            if(strlen($strTag))
            {
                $arBarcode = explode(",", $strTag);
                foreach($arBarcode as $barcode)
                {
                    $arReturn[] = "<barcode>".trim($barcode)."</barcode>";
                }
            }
        }

        return $arReturn;
    }

    /**
     * �������� yml ��� ����������
     *
     * @param $arRule array ������ �� ���������� �������
     * @param $arItem array ������ �� ���������� ��������������� ��������
     * @return string �������������� ��� ��� ������ ������
     */
    private function get_yml_offer_param($arRule, $arItem, $arOfferItemSKU=Array())
    {
        $arReturn = array();

        if(strlen($arRule["param"])>0)
        {
            // �� ������ ��������� ������ ����������
            $arParams = explode("|", $arRule["param"]);
            if(count($arParams)>0)
            {
                foreach($arParams as $str_param)
                {
                    // ��������� ��������� ������� ��� ��������� ������� ���������
                    $arParamElements = explode(",", $str_param);
                    if(count($arParamElements)==3)
                    {
                        // ���� ��� ����� ��� ��������, �� �� ��������� ���
                        if(!isset($arParamElements[0]) || !strlen($arParamElements[0])) continue;
                        if(!isset($arParamElements[2]) || !strlen($arParamElements[2])) continue;

                        // �������� ���������
                        $arParamElements[2] = self:: get_property_value($arParamElements[2], $arItem, false, 0, $arOfferItemSKU);
                        if(!strlen($arParamElements[2])) continue; // �� ��������� ���� �������� ������

                        // ������������ ��� ���������
                        $arParamElements[0] = self::yandex_text2xml($arParamElements[0], true);

                        // �������� unit (��������������)
                        $paramUnit = "";
                        if(!empty($arParamElements[1]))
                        {
                            $arParamElements[1] = self::yandex_text2xml($arParamElements[1], true);
                            $paramUnit = " unit=\"".$arParamElements[1]."\"";
                        }

                        // ��������� ���
                        $arReturn[] = "<param name=\"".$arParamElements[0]."\"".$paramUnit.">".$arParamElements[2]."</param>";
                    }
                }
            }
        }

        return $arReturn;
    }

    /**
     * ��������� YML-offer ���� "���������� ��������"
     *
     * @param $arRule array ������ �� ���������� �������
     * @param $arItem array ������ �� ���������� ��������������� ��������
     * @param $arOfferItemSKU array ������ �� ���������� ��������� ����������� SKU (���� ��� ����������)
     * @return array ������ �� ��������������� YML-������ �������� <offer>
     */
    private function get_yml_offer_simple($arRule, $arItem, $arOfferItemSKU=array())
    {
        // � ���� ������� �������� offer
        $arOfferElem = array();

        // <url> + utm (������ �� �����, ������������ ����� �������)
        $tagTmp = self::get_yml_offer_url($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

		// <price> + <currencyId> (���� + ������)
        $tagTmp = self::get_yml_offer_price($arRule, $arItem, $arOfferItemSKU);

//        self::writeLOG("����� ���������");
//        self::writeLOG(print_r($tagTmp, 1));

        if(!empty($tagTmp) && $tagTmp["minprice"]>0)
        {
            $arOfferElem[] = $tagTmp["price"];
			$price = $tagTmp["minprice"];
            if(array_key_exists('oldprice', $tagTmp)) {
                $arOfferElem[] = $tagTmp["oldprice"]; // ��� <oldprice> ���� ����
            }
            $arOfferElem[] = $tagTmp["currency"];


            //TODO ��� ������������ ������� ������
            if ($arRule['utm'] == 'utm_source=retail') {

                //����� �������� � ������� ����� moshoztorg.ru/aktsii/
                 if ($arItem['isActionElement']) {
                    $arOfferElem[] = '<param name="������">�����</param>';
                 }

                 //�������� ����_���������_����� (SAYT_AKTSIONNYY_TOVAR)
                 if ($arItem['PROPERTIES']['SAYT_AKTSIONNYY_TOVAR']['VALUE_XML_ID'] == 'true') {
                    $arOfferElem[] = '<param name="������">����������</param>';
                 }

                 //�������� ����_������� (SAYT_NOVINKA)
                 if ($arItem['PROPERTIES']['SAYT_NOVINKA']['VALUE_XML_ID'] == 'true') {
                    $arOfferElem[] = '<param name="������">�������</param>';
                 }

                    $product = new Product($arItem,$arItem['PROPERTIES']);

                    if ($product->get('old-price')) {

                        $oldPrice = intval(str_replace(' ','',$product->get('old-price')));
                        $newPrice = intval(str_replace(' ','',$product->get('price')));

                        if ( $newPrice < $oldPrice ) {

                            $intPercent = round( ( $oldPrice - $newPrice ) / ( $oldPrice / 100 ) );

                            $arOfferElem["discount_percent"] = '<param name="discount">1</param><param name="discount_percent">' . $intPercent . '</param>';

                        }
                    }
            } else {
                if (($arItem['PROPERTIES']["OLD_PRICE_1"]) && ($arItem['PROPERTIES']['SAYT_AKTSIONNYY_TOVAR']['VALUE_XML_ID'] == 'true')) {
                    $arOfferElem[] = '<oldprice>'.$arItem['PROPERTIES']["OLD_PRICE_1"]["VALUE"].'</oldprice>';
                 }
            }

        }
        else {
			//self::writeLOG("� ����-�� ����");
            return array(); // ��� ���� �� ���������
		}

        // <categoryId> (������ ������)
        $tagTmp = self::get_yml_offer_category($arRule, $arItem);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;
        else
            return array(); // ��� �������� � �������� �� ���������

        // <market_category> (��������� ��������� ����������� �� �������)
        $tagTmp = self::get_yml_offer_market_category($arRule, $arItem);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <picture> (��������. ���� � �������� ��� ��������, ������� ������ ����������)
        $tagTmp = self::get_yml_offer_pictures($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem = array_merge($arOfferElem, $tagTmp);

        // <store> (����������� ��������)
        $tagTmp = self::get_property_value_tag("store", $arRule["store"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <pickup> (����������� ��������)
        $tagTmp = self::get_property_value_tag("pickup", $arRule["pickup"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <delivery> (����������� ��������)
        $tagTmp = self::get_property_value_tag("delivery", $arRule["delivery"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <local_delivery_cost> (��������� ��������)
        //
		if ($price > 2000) {
			$tagTmp = "<delivery-options><option cost=\"0\" days=\"1-2\" /></delivery-options>";
		} else {
			$tagTmp = self::get_property_value_tag("local_delivery_cost", $arRule["local_delivery_cost"], $arItem, $arOfferItemSKU);
		}
		if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;
		/*
		 $arShopData["local_delivery_cost"] = 
		*/
		//��� 442236

		
		// <name> (�������� - ������������)
        $tagTmp = self::get_yml_offer_name($arRule["name"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;
        else
            return array(); // ��������� ������������ <offer> - ������������ ��������

        // <vendor> (�������������)
        $tagTmp = self::get_property_value_tag("vendor", $arRule["vendor"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <vendorCode> (��� ������)
        $tagTmp = self::get_property_value_tag("vendorCode", $arRule["vendorcode"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <description> (��������)
        $tagTmp = self::get_yml_offer_description($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <sales_notes> (����������� ����� ������)
        $tagTmp = self::get_property_value_tag("sales_notes", $arRule["sales_notes"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <manufacturer_warranty> (�������� �������������)
        $tagTmp = self::get_property_value_tag("manufacturer_warranty", $arRule["manufacturer_warranty"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <country_of_origin> (������ ������������ ������)
        $tagTmp = self::get_property_value_tag("country_of_origin", $arRule["country_of_origin"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <adult> (����� ��� ��������)
        $tagTmp = self::get_property_value_tag("adult", $arRule["adult"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <age> (���������� �����������)
        $tagTmp = self::get_yml_offer_age($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <barcode> (�������� ������������ ������)
        $tagTmp = self::get_yml_offer_barcode($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem = array_merge($arOfferElem, $tagTmp);

        // <cpa> (������� � ��������� �������� �� �������)
        $tagTmp = self::get_property_value_tag("cpa", $arRule["cpa"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <param> (�������������� ������)
        $tagTmp = self::get_yml_offer_param($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem = array_merge($arOfferElem, $tagTmp);

		
		
		
		
        return $arOfferElem;
    }

    /**
     * ��������� YML-offer ���� "������������ �����"
     *
     * @param $arRule array ������ �� ���������� �������
     * @param $arItem array ������ �� ���������� ��������������� ��������
     * @param $arOfferItemSKU array ������ �� ���������� ��������� ����������� SKU (���� ��� ����������)
     * @return array ������ �� ��������������� YML-������ �������� <offer>
     */
    private function get_yml_offer_vendor_model($arRule, $arItem, $arOfferItemSKU=array())
    {
        $arOfferElem = array();

        // <url> + utm (������ �� �����, ������������ ����� �������)
        $tagTmp = self::get_yml_offer_url($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <price> + <currencyId> (���� + ������)
        $tagTmp = self::get_yml_offer_price($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp) && $tagTmp["minprice"]>0)
        {
            $arOfferElem[] = $tagTmp["price"];
            if(array_key_exists('oldprice', $tagTmp)) $arOfferElem[] = $tagTmp["oldprice"]; // ��� <oldprice> ���� ����
            $arOfferElem[] = $tagTmp["currency"];
        }
        else
            return array(); // ��� ���� �� ���������

        // <categoryId> (������ ������)
        $tagTmp = self::get_yml_offer_category($arRule, $arItem);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;
        else
            return array(); // ��� �������� � �������� �� ���������

        // <market_category> (��������� ��������� ����������� �� �������)
        $tagTmp = self::get_yml_offer_market_category($arRule, $arItem);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <picture> (��������. ���� � �������� ��� ��������, ������� ������ ����������)
        $tagTmp = self::get_yml_offer_pictures($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem = array_merge($arOfferElem, $tagTmp);

        // <store> (����������� ��������)
        $tagTmp = self::get_property_value_tag("store", $arRule["store"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <pickup> (����������� ��������)
        $tagTmp = self::get_property_value_tag("pickup", $arRule["pickup"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <delivery> (����������� ��������)
        $tagTmp = self::get_property_value_tag("delivery", $arRule["delivery"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <local_delivery_cost> (��������� ��������)
        $tagTmp = self::get_property_value_tag("local_delivery_cost", $arRule["local_delivery_cost"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <typePrefix> (������ �������/���������)
        $tagTmp = self::get_property_value_tag("typePrefix", $arRule["typeprefix"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <vendor> (�������������)
        $tagTmp = self::get_property_value_tag("vendor", $arRule["vendor"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <vendorCode> (��� ������)
        $tagTmp = self::get_property_value_tag("vendorCode", $arRule["vendorcode"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <model> (������)
        $tagTmp = self::get_property_value_tag("model", $arRule["model"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <description> (��������)
        $tagTmp = self::get_yml_offer_description($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <sales_notes> (����������� ����� ������)
        $tagTmp = self::get_property_value_tag("sales_notes", $arRule["sales_notes"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <manufacturer_warranty> (�������� �������������)
        $tagTmp = self::get_property_value_tag("manufacturer_warranty", $arRule["manufacturer_warranty"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <seller_warranty> (�������� ��������)
        $tagTmp = self::get_property_value_tag("seller_warranty", $arRule["seller_warranty"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <country_of_origin> (������ ������������ ������)
        $tagTmp = self::get_property_value_tag("country_of_origin", $arRule["country_of_origin"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <downloadable> (����� ����� �������)
        $tagTmp = self::get_property_value_tag("downloadable", $arRule["downloadable"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <adult> (����� ��� ��������)
        $tagTmp = self::get_property_value_tag("adult", $arRule["adult"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <age> (���������� �����������)
        $tagTmp = self::get_yml_offer_age($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <barcode> (�������� ������������ ������)
        $tagTmp = self::get_yml_offer_barcode($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem = array_merge($arOfferElem, $tagTmp);

        // <cpa> (������� � ��������� �������� �� �������)
        $tagTmp = self::get_property_value_tag("cpa", $arRule["cpa"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <rec> (������������� ��� ������� ������ � �������)
        $tagTmp = self::get_property_value_tag("rec", $arRule["rec"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <expiry> (���� ��������/����� ������)
        $tagTmp = self::get_property_value_tag("expiry", $arRule["expiry"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <weight> (��� ������ � ������ ��������)
        $tagTmp = self::get_property_value_tag("weight", $arRule["weight"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <dimensions> (�������� ��������� ������)
        $tagTmp = self::get_property_value_tag("dimensions", $arRule["dimensions"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <param> (�������������� ������)
        $tagTmp = self::get_yml_offer_param($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem = array_merge($arOfferElem, $tagTmp);

        return $arOfferElem;
    }

    /**
     * ��������� YML-offer ���� "�����"
     *
     * @param $arRule array ������ �� ���������� �������
     * @param $arItem array ������ �� ���������� ��������������� ��������
     * @param $arOfferItemSKU array ������ �� ���������� ��������� ����������� SKU (���� ��� ����������)
     * @return array ������ �� ��������������� YML-������ �������� <offer>
     */
    private function get_yml_offer_book($arRule, $arItem, $arOfferItemSKU=array())
    {
        $arOfferElem = array();

        // <url> + utm (������ �� �����, ������������ ����� �������)
        $tagTmp = self::get_yml_offer_url($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <price> + <currencyId> (���� + ������)
        $tagTmp = self::get_yml_offer_price($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp) && $tagTmp["minprice"]>0)
        {
            $arOfferElem[] = $tagTmp["price"];
            $arOfferElem[] = $tagTmp["currency"];
        }
        else
            return array(); // ��� ���� �� ���������

        // <categoryId> (������ ������)
        $tagTmp = self::get_yml_offer_category($arRule, $arItem);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;
        else
            return array(); // ��� �������� � �������� �� ���������

        // <market_category> (��������� ��������� ����������� �� �������)
        $tagTmp = self::get_yml_offer_market_category($arRule, $arItem);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <picture> (��������. ���� � �������� ��� ��������, ������� ������ ����������)
        $tagTmp = self::get_yml_offer_pictures($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem = array_merge($arOfferElem, $tagTmp);

        // <store> (����������� ��������)
        $tagTmp = self::get_property_value_tag("store", $arRule["store"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <pickup> (����������� ��������)
        $tagTmp = self::get_property_value_tag("pickup", $arRule["pickup"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <delivery> (����������� ��������)
        $tagTmp = self::get_property_value_tag("delivery", $arRule["delivery"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <local_delivery_cost> (��������� ��������)
        $tagTmp = self::get_property_value_tag("local_delivery_cost", $arRule["local_delivery_cost"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <author> (����� ������������)
        $tagTmp = self::get_property_value_tag("author", $arRule["author"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <name> (�������� - ������������)
        $tagTmp = self::get_yml_offer_name($arRule["name"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;
        else
            return array(); // ��������� ������������ <offer> - ������������ ��������

        // <publisher> (������������)
        $tagTmp = self::get_property_value_tag("publisher", $arRule["publisher"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <series> (�����)
        $tagTmp = self::get_property_value_tag("series", $arRule["series"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <year> (��� �������)
        $tagTmp = self::get_property_value_tag("year", $arRule["year"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <ISBN> (��� �����)
        $tagTmp = self::get_property_value_tag("ISBN", $arRule["isbn"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <volume> (���������� �����)
        $tagTmp = self::get_property_value_tag("volume", $arRule["volume"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <part> (����� ����)
        $tagTmp = self::get_property_value_tag("part", $arRule["part"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <language> (���� ������������)
        $tagTmp = self::get_property_value_tag("language", $arRule["language"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <binding> (��������)
        $tagTmp = self::get_property_value_tag("binding", $arRule["binding"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <page_extent> (���������� ������� � �����)
        $tagTmp = self::get_property_value_tag("page_extent", $arRule["page_extent"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <table_of_contents> (����������)
        $tagTmp = self::get_property_value_tag("table_of_contents", $arRule["table_of_contents"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <description> (��������)
        $tagTmp = self::get_yml_offer_description($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <downloadable> (����� ����� �������)
        $tagTmp = self::get_property_value_tag("downloadable", $arRule["downloadable"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <age> (���������� �����������)
        $tagTmp = self::get_yml_offer_age($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        return $arOfferElem;
    }

    /**
     * ��������� YML-offer ���� "����������"
     *
     * @param $arRule array ������ �� ���������� �������
     * @param $arItem array ������ �� ���������� ��������������� ��������
     * @param $arOfferItemSKU array ������ �� ���������� ��������� ����������� SKU (���� ��� ����������)
     * @return array ������ �� ��������������� YML-������ �������� <offer>
     */
    private function get_yml_offer_audiobook($arRule, $arItem, $arOfferItemSKU=array())
    {
        $arOfferElem = array();

        // <url> + utm (������ �� �����, ������������ ����� �������)
        $tagTmp = self::get_yml_offer_url($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <price> + <currencyId> (���� + ������)
        $tagTmp = self::get_yml_offer_price($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp) && $tagTmp["minprice"]>0)
        {
            $arOfferElem[] = $tagTmp["price"];
            $arOfferElem[] = $tagTmp["currency"];
        }
        else
            return array(); // ��� ���� �� ���������

        // <categoryId> (������ ������)
        $tagTmp = self::get_yml_offer_category($arRule, $arItem);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;
        else
            return array(); // ��� �������� � �������� �� ���������

        // <market_category> (��������� ��������� ����������� �� �������)
        $tagTmp = self::get_yml_offer_market_category($arRule, $arItem);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <picture> (��������. ���� � �������� ��� ��������, ������� ������ ����������)
        $tagTmp = self::get_yml_offer_pictures($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem = array_merge($arOfferElem, $tagTmp);

        // <author> (����� ������������)
        $tagTmp = self::get_property_value_tag("author", $arRule["author"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <name> (�������� - ������������)
        $tagTmp = self::get_yml_offer_name($arRule["name"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;
        else
            return array(); // ��������� ������������ <offer> - ������������ ��������

        // <publisher> (������������)
        $tagTmp = self::get_property_value_tag("publisher", $arRule["publisher"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <series> (�����)
        $tagTmp = self::get_property_value_tag("series", $arRule["series"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <year> (��� �������)
        $tagTmp = self::get_property_value_tag("year", $arRule["year"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <ISBN> (��� �����)
        $tagTmp = self::get_property_value_tag("ISBN", $arRule["isbn"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <volume> (���������� �����)
        $tagTmp = self::get_property_value_tag("volume", $arRule["volume"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <part> (����� ����)
        $tagTmp = self::get_property_value_tag("part", $arRule["part"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <language> (���� ������������)
        $tagTmp = self::get_property_value_tag("language", $arRule["language"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <table_of_contents> (����������)
        $tagTmp = self::get_property_value_tag("table_of_contents", $arRule["table_of_contents"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <performed_by> (�����������)
        $tagTmp = self::get_property_value_tag("performed_by", $arRule["performed_by"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <performance_type> (��� ����������)
        $tagTmp = self::get_property_value_tag("performance_type", $arRule["performance_type"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <storage> (��������)
        $tagTmp = self::get_property_value_tag("storage", $arRule["storage"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <format> (������ ����������)
        $tagTmp = self::get_property_value_tag("format", $arRule["format"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <recording_length> (����� ��������)
        $tagTmp = self::get_property_value_tag("recording_length", $arRule["recording_length"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <description> (��������)
        $tagTmp = self::get_yml_offer_description($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <downloadable> (����� ����� �������)
        $tagTmp = self::get_property_value_tag("downloadable", $arRule["downloadable"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <age> (���������� �����������)
        $tagTmp = self::get_yml_offer_age($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        return $arOfferElem;
    }

    /**
     * ��������� YML-offer ���� "����������� � ����� ��������� (������)"
     *
     * @param $arRule array ������ �� ���������� �������
     * @param $arItem array ������ �� ���������� ��������������� ��������
     * @param $arOfferItemSKU array ������ �� ���������� ��������� ����������� SKU (���� ��� ����������)
     * @return array ������ �� ��������������� YML-������ �������� <offer>
     */
    private function get_yml_offer_artist_title_m($arRule, $arItem, $arOfferItemSKU=array())
    {
        // � ���� ������� �������� offer
        $arOfferElem = array();

        // <url> + utm (������ �� �����, ������������ ����� �������)
        $tagTmp = self::get_yml_offer_url($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <price> + <currencyId> (���� + ������)
        $tagTmp = self::get_yml_offer_price($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp) && $tagTmp["minprice"]>0)
        {
            $arOfferElem[] = $tagTmp["price"];
            $arOfferElem[] = $tagTmp["currency"];
        }
        else
            return array(); // ��� ���� �� ���������

        // <categoryId> (������ ������)
        $tagTmp = self::get_yml_offer_category($arRule, $arItem);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;
        else
            return array(); // ��� �������� � �������� �� ���������

        // <market_category> (��������� ��������� ����������� �� �������)
        $tagTmp = self::get_yml_offer_market_category($arRule, $arItem);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <picture> (��������. ���� � �������� ��� ��������, ������� ������ ����������)
        $tagTmp = self::get_yml_offer_pictures($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem = array_merge($arOfferElem, $tagTmp);

        // <store> (����������� ��������)
        $tagTmp = self::get_property_value_tag("store", $arRule["store"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <pickup> (����������� ��������)
        $tagTmp = self::get_property_value_tag("pickup", $arRule["pickup"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <delivery> (����������� ��������)
        $tagTmp = self::get_property_value_tag("delivery", $arRule["delivery"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <artist> (�����������)
        $tagTmp = self::get_property_value_tag("artist", $arRule["artist"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <title> (�������� - ������������)
        $tagTmp = self::get_yml_offer_name($arRule["title"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;
        else
            return array(); // ��������� ������������ <offer> - ������������ ��������

        // <title> (��������)
        $tagTmp = self::get_property_value_tag("title", $arRule["title"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <year> (��� �������)
        $tagTmp = self::get_property_value_tag("year", $arRule["year"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <year> (��� �������)
        $tagTmp = self::get_property_value_tag("media", $arRule["media"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <description> (��������)
        $tagTmp = self::get_yml_offer_description($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <age> (���������� �����������)
        $tagTmp = self::get_yml_offer_age($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <barcode> (�������� ������������ ������)
        $tagTmp = self::get_yml_offer_barcode($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem = array_merge($arOfferElem, $tagTmp);

        return $arOfferElem;
    }

    /**
     * ��������� YML-offer ���� "����������� � ����� ��������� (�����)"
     *
     * @param $arRule array ������ �� ���������� �������
     * @param $arItem array ������ �� ���������� ��������������� ��������
     * @param $arOfferItemSKU array ������ �� ���������� ��������� ����������� SKU (���� ��� ����������)
     * @return array ������ �� ��������������� YML-������ �������� <offer>
     */
    private function get_yml_offer_artist_title_v($arRule, $arItem, $arOfferItemSKU=array())
    {
        // � ���� ������� �������� offer
        $arOfferElem = array();

        // <url> + utm (������ �� �����, ������������ ����� �������)
        $tagTmp = self::get_yml_offer_url($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <price> + <currencyId> (���� + ������)
        $tagTmp = self::get_yml_offer_price($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp) && $tagTmp["minprice"]>0)
        {
            $arOfferElem[] = $tagTmp["price"];
            $arOfferElem[] = $tagTmp["currency"];
        }
        else
            return array(); // ��� ���� �� ���������

        // <categoryId> (������ ������)
        $tagTmp = self::get_yml_offer_category($arRule, $arItem);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;
        else
            return array(); // ��� �������� � �������� �� ���������

        // <market_category> (��������� ��������� ����������� �� �������)
        $tagTmp = self::get_yml_offer_market_category($arRule, $arItem);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <picture> (��������. ���� � �������� ��� ��������, ������� ������ ����������)
        $tagTmp = self::get_yml_offer_pictures($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem = array_merge($arOfferElem, $tagTmp);

        // <store> (����������� ��������)
        $tagTmp = self::get_property_value_tag("store", $arRule["store"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <pickup> (����������� ��������)
        $tagTmp = self::get_property_value_tag("pickup", $arRule["pickup"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <delivery> (����������� ��������)
        $tagTmp = self::get_property_value_tag("delivery", $arRule["delivery"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <title> (�������� ������ - ������������)
        $tagTmp = self::get_yml_offer_name($arRule["title"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;
        else
            return array(); // ��������� ������������ <offer> - ������������ ��������

        // <year> (��� �������)
        $tagTmp = self::get_property_value_tag("year", $arRule["year"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <media> (��������)
        $tagTmp = self::get_property_value_tag("media", $arRule["media"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <starring> (������)
        $tagTmp = self::get_property_value_tag("starring", $arRule["starring"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <director> (��������)
        $tagTmp = self::get_property_value_tag("director", $arRule["director"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <originalName> (������������ ��������)
        $tagTmp = self::get_property_value_tag("originalName", $arRule["originalname"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <country> (������)
        $tagTmp = self::get_property_value_tag("country", $arRule["country"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <description> (��������)
        $tagTmp = self::get_yml_offer_description($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <adult> (����� ��� ��������)
        $tagTmp = self::get_property_value_tag("adult", $arRule["adult"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <age> (���������� �����������)
        $tagTmp = self::get_yml_offer_age($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <barcode> (�������� ������������ ������)
        $tagTmp = self::get_yml_offer_barcode($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem = array_merge($arOfferElem, $tagTmp);

        return $arOfferElem;
    }

    /**
     * ��������� YML-offer ���� "����"
     *
     * @param $arRule array ������ �� ���������� �������
     * @param $arItem array ������ �� ���������� ��������������� ��������
     * @param $arOfferItemSKU array ������ �� ���������� ��������� ����������� SKU (���� ��� ����������)
     * @return array ������ �� ��������������� YML-������ �������� <offer>
     */
    private function get_yml_offer_tour($arRule, $arItem, $arOfferItemSKU=array())
    {
        $arOfferElem = array();

        // <url> + utm (������ �� �����, ������������ ����� �������)
        $tagTmp = self::get_yml_offer_url($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <price> + <currencyId> (���� + ������)
        $tagTmp = self::get_yml_offer_price($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp) && $tagTmp["minprice"]>0)
        {
            $arOfferElem[] = $tagTmp["price"];
            $arOfferElem[] = $tagTmp["currency"];
        }
        else
            return array(); // ��� ���� �� ���������

        // <categoryId> (������ ������)
        $tagTmp = self::get_yml_offer_category($arRule, $arItem);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;
        else
            return array(); // ��� �������� � �������� �� ���������

        // <market_category> (��������� ��������� ����������� �� �������)
        $tagTmp = self::get_yml_offer_market_category($arRule, $arItem);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <picture> (��������. ���� � �������� ��� ��������, ������� ������ ����������)
        $tagTmp = self::get_yml_offer_pictures($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem = array_merge($arOfferElem, $tagTmp);

        // <store> (����������� ��������)
        $tagTmp = self::get_property_value_tag("store", $arRule["store"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <pickup> (����������� ��������)
        $tagTmp = self::get_property_value_tag("pickup", $arRule["pickup"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <delivery> (����������� ��������)
        $tagTmp = self::get_property_value_tag("delivery", $arRule["delivery"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <worldRegion> (����� �����)
        $tagTmp = self::get_property_value_tag("worldRegion", $arRule["worldregion"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <country> (������)
        $tagTmp = self::get_property_value_tag("country", $arRule["country"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <region> (������ ��� �����)
        $tagTmp = self::get_property_value_tag("region", $arRule["region"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <days> (���������� ���� ����)
        $tagTmp = self::get_property_value_tag("days", $arRule["days"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;
        else
            return array(); // �� ��������� - ������������ ��������

        // <dataTour> (���� �������)
        //TODO: ������ ���������� ������ � ����� ��� ���������� (������������� ��������)
        $tagTmp = self::get_property_value_tag("dataTour", $arRule["datatour"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <name> (�������� - ������������)
        $tagTmp = self::get_yml_offer_name($arRule["name"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;
        else
            return array(); // ��������� ������������ <offer> - ������������ ��������

        // <hotel_stars> (������ �����)
        $tagTmp = self::get_property_value_tag("hotel_stars", $arRule["hotel_stars"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <room> (��� �������)
        $tagTmp = self::get_property_value_tag("room", $arRule["room"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <meal> (��� �������)
        $tagTmp = self::get_property_value_tag("meal", $arRule["meal"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <included> (��� ��������)
        $tagTmp = self::get_property_value_tag("included", $arRule["included"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;
        else
            return array(); // ��������� ������������ <offer> - ������������ ��������

        // <transport> (���������)
        $tagTmp = self::get_property_value_tag("transport", $arRule["transport"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <description> (��������)
        $tagTmp = self::get_yml_offer_description($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <age> (���������� �����������)
        $tagTmp = self::get_yml_offer_age($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        return $arOfferElem;
    }

    /**
     * ��������� YML-offer ���� "������ �� �����������"
     *
     * @param $arRule array ������ �� ���������� �������
     * @param $arItem array ������ �� ���������� ��������������� ��������
     * @param $arOfferItemSKU array ������ �� ���������� ��������� ����������� SKU (���� ��� ����������)
     * @return array ������ �� ��������������� YML-������ �������� <offer>
     */
    private function get_yml_offer_event_ticket($arRule, $arItem, $arOfferItemSKU=array())
    {
        $arOfferElem = array();

        // <url> + utm (������ �� �����, ������������ ����� �������)
        $tagTmp = self::get_yml_offer_url($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <price> + <currencyId> (���� + ������)
        $tagTmp = self::get_yml_offer_price($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp) && $tagTmp["minprice"]>0)
        {
            $arOfferElem[] = $tagTmp["price"];
            $arOfferElem[] = $tagTmp["currency"];
        }
        else
            return array(); // ��� ���� �� ���������

        // <categoryId> (������ ������)
        $tagTmp = self::get_yml_offer_category($arRule, $arItem);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;
        else
            return array(); // ��� �������� � �������� �� ���������

        // <market_category> (��������� ��������� ����������� �� �������)
        $tagTmp = self::get_yml_offer_market_category($arRule, $arItem);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <picture> (��������. ���� � �������� ��� ��������, ������� ������ ����������)
        $tagTmp = self::get_yml_offer_pictures($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem = array_merge($arOfferElem, $tagTmp);

        // <store> (����������� ��������)
        $tagTmp = self::get_property_value_tag("store", $arRule["store"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <pickup> (����������� ��������)
        $tagTmp = self::get_property_value_tag("pickup", $arRule["pickup"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <delivery> (����������� ��������)
        $tagTmp = self::get_property_value_tag("delivery", $arRule["delivery"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <name> (�������� - ������������)
        $tagTmp = self::get_yml_offer_name($arRule["name"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;
        else
            return array(); // ��������� ������������ <offer> - ������������ ��������

        // <place> (����� ���������� - ������������)
        $tagTmp = self::get_property_value_tag("place", $arRule["place"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;
        else
            return array(); // ��������� ������������ <offer> - ������������ ��������

        // <hall plan=""> (������ �� ����������� � ������ ����)
        //TODO: ������ ������������ ��� � �������
        $tagTmp = self::get_property_value_tag("hall plan", $arRule["hall_plan"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <date> (���� � ����� ������)
        $tagTmp = self::get_property_value_tag("date", $arRule["date"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;
        else
            return array(); // ��������� ������������ <offer> - ������������ ��������

        // <is_premiere> (������� ������������ �����������)
        $tagTmp = self::get_property_value_tag("is_premiere", $arRule["is_premiere"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <is_kids> (������� �������� �����������)
        $tagTmp = self::get_property_value_tag("is_kids", $arRule["is_kids"], $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <description> (��������)
        $tagTmp = self::get_yml_offer_description($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        // <age> (���������� �����������)
        $tagTmp = self::get_yml_offer_age($arRule, $arItem, $arOfferItemSKU);
        if(!empty($tagTmp))
            $arOfferElem[] = $tagTmp;

        return $arOfferElem;
    }

    /**
     * ���������� ��� <picture> � URL �������� �� ���� ����������� �������
     *
     * @param $pictNo
     * @param $urlShop
     * @param $showTag
     * @return string
     */
    private function get_yml_picture_by_code($pictNo, $urlShop, $showTag=true)
    {
        $strFile = '';

        // �������� ������ �� ������ �����
        if ($arFile = CFile::GetFileArray($pictNo))
            $strFile = CMibixYandexTools::getFixURL($urlShop, $arFile["SRC"]);

        // ���� ��������� ������ � ����� ������� ���
        if (!empty($strFile) && $showTag)
            return "<picture>".$strFile."</picture>";

        return $strFile;
    }

    /**
     * �������� �������� ��������, ��������������� ��� ���� (������: " bid="10")
     *
     * @param $attribute
     * @param $arRule
     * @param $arItem
     * @param $default
     * @return string
     */
    private function get_property_attribute_tag($attribute, $arRule, $arItem, $default='', $arOfferItemSKU=Array())
    {
        $atVal = self::get_property_value($arRule[$attribute], $arItem, false, 0, $arOfferItemSKU);
        if(!empty($atVal))
        {
            
			/*
			// ���. ���������� ��� ���� available //��� ������
            if ($attribute=="available") {
                if ($atVal==1) $atVal = "true";
                elseif ($atVal==0) $atVal = "false";
            }*/

            return " ".$attribute."=\"".$atVal."\"";
        }

        // �������� �� ��������� ���� ����
        if(strlen($default))
        {
            return " ".$attribute."=\"".$default."\"";
        }

        return "";
    }

    /**
     * �������� �������� ��������, ��������� ��� � ���� YML-���� � ��������� ���
     *
     * @param $PARAM
     * @param $PROPERTY
     * @param $arItem
     * @return string
     */
    private function get_property_value_tag($PARAM, $PROPERTY, $arItem, $arOfferItemSKU=Array())
    {
        $strProperty = '';

		//$tagTmp = self::get_property_value_tag("country_of_origin", $arRule["country_of_origin"], $arItem, $arOfferItemSKU);
		
        // �������� ��������
        $value = self::get_property_value($PROPERTY, $arItem, false, 0, $arOfferItemSKU);
        if(strlen($value))
        {
			$marketing_shit_from = array("������ ������������: ��������", "������ ������: ������", "������ ������������: ������", "Germany (��������)", "���.", "������", "�������");
			$marketing_shit_to 	 = array("��������", "�����", "������", "��������", "���", "��������������", "�������");
			
			$value = str_replace($marketing_shit_from, $marketing_shit_to, $value);
			
			
			
            // ���� ����� ������� ��� �������� ��� typePrefix
            if($PARAM=="typePrefix" && $value=="catname")
            {
                $resSection = CIBlockSection::GetByID($arItem["IBLOCK_SECTION_ID"]);
                if($arResSection = $resSection->GetNext())
                    $value = self::yandex_text2xml($arResSection['NAME'], true);
                else
                    $value = "";
            }

            // ���������� ������������ ��������
            $param_h = self::yandex_text2xml($PARAM, true);
            $strProperty = '<'.$param_h.'>'.$value.'</'.$param_h.'>';
        }

        return $strProperty;
    }

    /**
     * �������� �������� �������� � ����������� �� ��� ���� � ���������� ���
     *
     * @param $PROPERTY
     * @param $arItem
     * @param bool $bSR
     * @param int $iTryncate
     * @return string
     */
    private function get_property_value($PROPERTY, $arItem, $bSR=false, $iTryncate=0, $arOfferItemSKU=Array())
    {
        // ���� �������� �������� ����������� �������� "self@", �� ���������� ���
        if (preg_match("/^self@(.*?)/isU", $PROPERTY, $matches))
        {
            if(!empty($matches) && isset($matches[1]))
            {
                return self::yandex_text2xml($matches[1], true);
            }
        }

        // ���� �������� �������� ������������� ��� ���� ������� �������� "val@", �� ���������� ���
        if (preg_match("/^val@(.*?)/isU", $PROPERTY, $matches))
        {
            if(!empty($matches) && isset($matches[1]))
            {
                return self::yandex_text2xml($matches[1], true);
            }
        }

        // ��������� ��������, � ������ ��������� ��� �������� (�������� ��� sku)
        $arProperty = self::get_property_array($PROPERTY, $arItem, $arOfferItemSKU);

        // �������� �������� �������� � ����������� �� ����
        $value = '';
        if (isset($arProperty) && !empty($arProperty))
        {
            // �������� �� ��������������� ��� HTML
            $userTypeFormat = "";
            if (strlen($arProperty["USER_TYPE"]))
            {
                $arUserType = CIBlockProperty::GetUserType($arProperty["USER_TYPE"]);
                if (array_key_exists("GetPublicViewHTML", $arUserType))
                {
                    $userTypeFormat = $arUserType["GetPublicViewHTML"];
                    $arProperty['PROPERTY_TYPE'] = 'USER_TYPE';
                }
            }

            // ������������ �������� � ����������� �� ��� ����
            switch ($arProperty['PROPERTY_TYPE'])
            {
                // ���������������� ���
                case 'USER_TYPE':
                    if (!empty($arProperty['VALUE']))
                    {
                        if (is_array($arProperty['VALUE']))
                        {
                            $arValues = array();
                            foreach($arProperty["VALUE"] as $oneValue)
                            {
                                $arValues[] = call_user_func_array($userTypeFormat,
                                    array(
                                        $arProperty,
                                        array("VALUE" => $oneValue),
                                        array('MODE' => 'SIMPLE_TEXT'),
                                    ));
                            }
                            $value = implode(', ', $arValues);
                        }
                        else
                        {
                            $value = call_user_func_array($userTypeFormat,
                                array(
                                    $arProperty,
                                    array("VALUE" => $arProperty["VALUE"]),
                                    array('MODE' => 'SIMPLE_TEXT'),
                                ));
                        }
                    }
                    break;
                case 'E':
                    if (!empty($arProperty['VALUE']))
                    {
                        $arCheckValue = array();
                        if (!is_array($arProperty['VALUE']))
                        {
                            $arProperty['VALUE'] = intval($arProperty['VALUE']);
                            if (0 < $arProperty['VALUE'])
                                $arCheckValue[] = $arProperty['VALUE'];
                        }
                        else
                        {
                            foreach ($arProperty['VALUE'] as &$intValue)
                            {
                                $intValue = intval($intValue);
                                if (0 < $intValue)
                                    $arCheckValue[] = $intValue;
                            }
                            if (isset($intValue))
                                unset($intValue);
                        }
                        if (!empty($arCheckValue))
                        {
                            $dbRes = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID'], 'ID' => $arCheckValue), false, false, array('NAME'));
                            while ($arRes = $dbRes->Fetch())
                            {
                                $value .= ($value ? ', ' : '').$arRes['NAME'];
                            }
                        }
                    }
                    break;
                case 'G':
                    if (!empty($arProperty['VALUE']))
                    {
                        $arCheckValue = array();
                        if (!is_array($arProperty['VALUE']))
                        {
                            $arProperty['VALUE'] = intval($arProperty['VALUE']);
                            if (0 < $arProperty['VALUE'])
                                $arCheckValue[] = $arProperty['VALUE'];
                        }
                        else
                        {
                            foreach ($arProperty['VALUE'] as &$intValue)
                            {
                                $intValue = intval($intValue);
                                if (0 < $intValue)
                                    $arCheckValue[] = $intValue;
                            }
                            if (isset($intValue))
                                unset($intValue);
                        }
                        if (!empty($arCheckValue))
                        {
                            $dbRes = CIBlockSection::GetList(array(), array('IBLOCK_ID' => $arProperty['LINK_IBLOCK_ID'], 'ID' => $arCheckValue), false, array('NAME'));
                            while ($arRes = $dbRes->Fetch())
                            {
                                $value .= ($value ? ', ' : '').$arRes['NAME'];
                            }
                        }
                    }
                    break;
                case 'L':
                    if (!empty($arProperty['VALUE']))
                    {
                        if (is_array($arProperty['VALUE']))
                            $value .= implode(', ', $arProperty['VALUE']);
                        else
                            $value .= $arProperty['VALUE'];
                    }
                    break;
                case 'F';
                    if (!empty($arProperty['VALUE']))
                    {
                        $value = self::get_yml_picture_by_code(IntVal($arProperty['VALUE']), self::$urlShop, false);
                    }
                    break;
                default:
                    $value = is_array($arProperty['VALUE']) ? implode(', ', $arProperty['VALUE']) : $arProperty['VALUE'];
            }
        }

        if(strlen($value))
            return self::yandex_text2xml($value, true, false, $bSR, $iTryncate);

        return '';
    }

    /**
     * �������� ������ ������� ������ ��� ��������� �����������
     * � ����������� �� �������������� ��������
     *
     * @param $PROPERTY
     * @param $arItem
     * @param $arOfferItemSKU
     * @return array
     */
    private function get_property_array($PROPERTY, $arItem, $arOfferItemSKU)
    {
        $arProperty = Array();
        if (preg_match("/^offer@(.*?)/isU", $PROPERTY, $matches))
        {
            if(!empty($matches) && isset($matches[1]) && !empty($arOfferItemSKU))
            {
                $arProperty = $arOfferItemSKU["PROPERTIES"][$matches[1]];
            }
        }
        if(empty($arProperty)) // ���� �� SKU, �� �������� �������� ��� �������� ��������
        {
            $arProperty = $arItem["PROPERTIES"][$PROPERTY];
        }

        return $arProperty;
    }

    /**
     * ���������������� ���������� ������������� ����������� ��������
     *
     * @param $arRule array ������ �� ���������� �������
     * @param $arItem array ������ �� ���������� ��������������� ��������
     * @param $arOfferItemSKU array ������ �� ���������� SKU ���������
     * @return string �������������� ��� ��� ������ ������
     */
    private function check_filter($arRule, $arItem, $arOfferItemSKU=array())
    {
        if(strlen($arRule["filters"])>0)
        {
            // �� ������ ��������� ������ ����������
            $arFilters = explode("|", $arRule["filters"]);
            if(count($arFilters)>0)
            {
                // ���������� �� ������� �������
                foreach($arFilters as $str_filter)
                {
                    // ��������� ��������� ������� ��� ��������� ������� ���������
                    $arParamFilters = explode(",", $str_filter);
                    if(count($arParamFilters)==3)
                    {
                        // ���� ��� ������ ���� ���������, �� �� ������������
                        if(!isset($arParamFilters[0]) || !strlen($arParamFilters[0])) continue;
                        if(!isset($arParamFilters[1]) || !strlen($arParamFilters[1])) continue;
                        if(!isset($arParamFilters[2]) || !strlen($arParamFilters[2])) continue;

                        // ������ �� �������� ������� ��������� � SKU
                        if (preg_match("/^offer@(.*?)/isU", $arParamFilters[0], $matches))
                        {
                            if(!empty($matches) && isset($matches[1]) && !empty($arOfferItemSKU))
                            {
                                $arProperty = $arOfferItemSKU["PROPERTIES"][$matches[1]];
                            }
                        }
                        elseif (preg_match("/^prop@(.*?)/isU", $arParamFilters[0], $matches))
                        {
                            if(!empty($matches) && isset($matches[1]))
                            {
                                $arProperty = $arItem["PROPERTIES"][$matches[1]];
                            }
                        }
                        // ���� ������� �������� � ��� ��������, ��������� ������
                        if (isset($arProperty["PROPERTY_TYPE"]) && count($arProperty["PROPERTY_TYPE"])>0)
                        {
                            // ����������� �����: S - ������; N - �����; L - ������;
                            if ($arProperty["PROPERTY_TYPE"]=="S" || $arProperty["PROPERTY_TYPE"]=="N" || ($arProperty["PROPERTY_TYPE"]=="L" && !is_array($arProperty["VALUE"])))
                            {
                                if ($arParamFilters[1] == "equal") { // �����
                                    if (!(trim($arProperty["VALUE"]) == trim($arParamFilters[2]))) return false;
                                } elseif ($arParamFilters[1] == "notequal") { // �� �����
                                    if (!(trim($arProperty["VALUE"]) != trim($arParamFilters[2]))) return false;
                                } elseif ($arParamFilters[1] == "more") { // ������
                                    if (!(intval($arProperty["VALUE"]) > intval($arParamFilters[2]))) return false;
                                } elseif ($arParamFilters[1] == "less") { // ������
                                    if (!(intval($arProperty["VALUE"]) < intval($arParamFilters[2]))) return false;
                                }
                            }
                            elseif ($arProperty["PROPERTY_TYPE"]=="L" && is_array($arProperty["VALUE"])) // ������ �� ��������� ������ (��������� ��� �� ���������)
                            {
                                if ($arParamFilters[1] == "equal") { // �����
                                    if (!in_array(trim($arParamFilters[2]), $arProperty["VALUE"])) return false;
                                } elseif ($arParamFilters[1] == "notequal") { // �� �����
                                    if (in_array(trim($arParamFilters[2]), $arProperty["VALUE"])) return false;
                                }
                            }
                        }

                        // ��������� ������� �������������� �������
                        switch($arParamFilters[0])
                        {
                            // ���� (������)
                            case "filter_price":
                                // �������� ���� ������
                                $arPrice = self::get_yml_offer_price($arRule, $arItem, $arOfferItemSKU);
                                // ���������� ������������� ������������� ��������
                                switch($arParamFilters[1])
                                {
                                    case "equal"; // �����
                                        if(!(intval($arPrice["minprice"])==intval($arParamFilters[2]))) return false;
                                        break;
                                    case "notequal"; // �� �����
                                        if(!(intval($arPrice["minprice"])!=intval($arParamFilters[2]))) return false;
                                        break;
                                    case "more";
                                        if(!(intval($arPrice["minprice"])>intval($arParamFilters[2]))) return false;
                                        break;
                                    case "less";
                                        if(!(intval($arPrice["minprice"])<intval($arParamFilters[2]))) return false;
                                        break;
                                    //case "empty";
                                    //case "notempty";
                                }
                                break;

                            // ���������� �� ������ (������)
                            case "filter_quantity":
                                // ���������� ������ ��� �������� ����� ������ � ������
                                if(CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
                                {
                                    $tmpItemID = $arItem['ID'];
                                    if(!empty($arOfferItemSKU))
                                        $tmpItemID = $arOfferItemSKU['ID'];

                                    // �������� ��������� ������
                                    $arResProduct = CCatalogProduct::GetByID($tmpItemID);
                                    
                                    // ���������� ������������� ������������� ��������
                                    switch($arParamFilters[1])
                                    {
                                        case "equal"; // �����
                                            if(!(intval($arResProduct["QUANTITY"])==intval($arParamFilters[2]))) return false;
                                            break;
                                        case "notequal"; // �� �����
                                            if(!(intval($arResProduct["QUANTITY"])!=intval($arParamFilters[2]))) return false;
                                            break;
                                        case "more";
                                            if(!(intval($arResProduct["QUANTITY"])>intval($arParamFilters[2]))) return false;
                                            break;
                                        case "less";
                                            if(!(intval($arResProduct["QUANTITY"])<intval($arParamFilters[2]))) return false;
                                            break;
                                        //case "empty";
                                        //case "notempty";
                                    }
                                }
                                break;
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * �������� ����� � ������ ������� � ����������� �������� � ��������� ����
     *
     * @param $string
     * @return string
     */
    private function sentence_cap($string) {

        $newtext = array();
        $ready = str_replace(array(". ","? ","! "), ". ", $string);
        $textbad = explode(". ", $ready);

        foreach ($textbad as $sentence) {

            if (defined('BX_UTF') && BX_UTF==true) {
                $sentencegood = self::my_ucfirst($sentence);
            } else {
                $sentencegood = ucfirst(strtolower($sentence));
            }
            $newtext[] = $sentencegood;
        }

        return implode(". ", $newtext);
    }

    /**
     * ���������������� �������, ���������� ������ ����������� � ��������� �����
     *
     * @param $string
     * @return string
     */
    function my_ucfirst($string) {

        if (function_exists('mb_strtoupper') && function_exists('mb_substr')) {
            $string = mb_strtolower($string, 'cp1251');
            preg_match_all("/^(.)(.*)$/isU", $string, $arr);
            $string = mb_strtoupper($arr[1][0], 'cp1251').$arr[2][0];
        }
        else {
            $string = ucfirst(strtolower($string));
        }
        return $string;
    }

    /**
     * ������ ����������� �������� �� ��������
     * (���������� ����� preg_replace_callback)
     *
     * @param $arg
     * @return string
     */
    private static function yandex_replace_special($arg)
    {
        if (in_array($arg[0], array("&quot;", "&amp;", "&lt;", "&gt;")))
            return $arg[0];
        else
            return " ";
    }

    /**
     * ������� ������ � ������ XML
     *
     * @param $text
     * @param bool $bHSC
     * @param bool $bDblQuote
     * @param bool $bSR
     * @param int $iTryncate
     * @return mixed|string
     */
    private function yandex_text2xml($text, $bHSC = false, $bDblQuote = false, $bSR=false, $iTryncate=0)
    {
        global $APPLICATION;

        $bHSC = (true == $bHSC ? true : false);
        $bDblQuote = (true == $bDblQuote ? true: false);

        if($bSR) // ���.��������� ��� HTML-������
        {
            $text = strip_tags(preg_replace_callback("'&[^;]*;'", "self::yandex_replace_special", $text));

            if($iTryncate>0)
                $text = TruncateText($text, $iTryncate);
        }

        if ($bHSC)
        {
            $text = htmlspecialcharsbx($text);
            if ($bDblQuote)
                $text = str_replace('&quot;', '"', $text);
        }

        // ���������� ��������� �����
        $siteCharset = 'windows-1251';
        if (defined('BX_UTF') && BX_UTF==true)
        {
            $siteCharset = 'UTF-8';
        }

        $text = preg_replace("/[\x1-\x8\xB-\xC\xE-\x1F]/", "", $text);
        $text = str_replace("'", "&apos;", $text);
        $text = $APPLICATION->ConvertCharset($text, $siteCharset, 'windows-1251');
        return $text;
    }

    /**
     * ������� ���������� ��� ������ ��� ������� � ���� � ����� ������
     *
     * @param $name
     * @param $value
     */
    private function writeLOG($name, $value)
    {
        // ��������� ���� ��� ������ � ������ ��������� �������
        $fp = @fopen(dirname(__FILE__)."/report.log", "a+");
        if($fp)
        {
            // ����� ������� ����
            @fwrite($fp, "[".date("Y-m-d H:i:s.").str_pad(substr((float)microtime(), 2), 6, '0', STR_PAD_LEFT)."]\r\n");

            // ���� �������� ������, ���������� ��
            if(is_array($value))
            {
                $dumpOut = print_r($value, true);
            }
            else
            {
                ob_start();
                var_dump($value);
                $dumpOut = ob_get_clean();
            }
            @fwrite($fp, $name.": ".$dumpOut."\r\n");
            @fclose($fp);
        }
    }
}

/**
 * ����� � ��������� ������, ����������� ��� ������
 * TODO: ����� �����������
 */
class CMibixYandexTools
{
    /**
     * ������������ URL �����, ���� � ���������� �������� �� �� ������ �� �� �������
     *
     * @param $siteURL
     * @return string
     */
    public static function getSiteURL($siteURL)
    {
        // ������� �� ����� ���� ���� ����
        if (substr($siteURL, -1) == '/')
            $siteURL = substr($siteURL, 0, -1);

        // ��������� ��������, ���� �� ������
        if (!preg_match("~^(?:f|ht)tps?://~i", $siteURL))
            $siteURL = "http://" . $siteURL;

        return $siteURL;
    }

    /**
     * �������� ���������� URL ������, � ����������� �� ���������� ����������
     *
     * @param $siteURL - ����� �����
     * @param $srcURL - ���� � ��������
     * @param $urlEncode - ����� �� ���������� ������
     * @return string - ������ ����� � ������ �������
     */
    public static function getFixURL($siteURL, $srcURL, $urlEncode=true)
    {
        $siteURL = self::getSiteURL($siteURL);

        // ���� url �������� ������������� �������
        if(substr($srcURL, 0, 1) == "/" || !preg_match("/[^.]+\\.[^.]+$/", $srcURL))
        {
            $pageUrl = $srcURL;
            if ($urlEncode)
                $pageUrl = implode("/", array_map("rawurlencode", explode("/", $srcURL)));

            if (substr($srcURL, 0, 1) == "/")
                $strFile = $siteURL . $pageUrl;
            else
                $strFile = $siteURL . "/" . $pageUrl;
        }
        else
            $strFile = $srcURL;

        return $strFile;
    }
}


?>