<?
namespace WS\SaleUserProfilesPlus;

class Profile {
    static function GetPersonFieldsByID($personID) {
        if (!empty($personID)) {
            $res = \CSalePersonType::GetList(array(), array("ID" => $personID), false, array('nTopCount' => 1), array());
            if ($arRes = $res->Fetch()) {
                return $arRes;
            }
        }
        return false;
    }

    static function GetProfileFieldsByID($profileID) {
        if (!empty($profileID)) {
            $res = \CSaleOrderUserProps::GetList(array(), array("ID" => $profileID), false, array('nTopCount' => 1));
            while ($arRes = $res->Fetch()) {
                return $arRes;
            }
        }
        return false;
    }

    static function GetProfileProps($profileID = null, $personID = null) {
        // получаем значения свойств
        if (!empty($profileID)) {
            $props = array();
            $res = \CSaleOrderUserPropsValue::GetList(array(), array('USER_PROPS_ID'=>$profileID), false, false, array('ID', 'ORDER_PROPS_ID', 'VALUE'));
            while ($arRes = $res->Fetch()) {
                $props[$arRes['ORDER_PROPS_ID']] = array('VALUE' => $arRes['VALUE']);
            }
        }

        // если не задан $personID - выбираем первый
        if (empty($personID)) {
            $res = \CSalePersonType::GetList(array(), array(), false, array('nTopCount' => 1), array());
            if ($arRes = $res->Fetch()) {
                $personID = $arRes["ID"];
            }
        }

        // получаем свойства
        $arProps = array();
        $res = \CSaleOrderProps::GetList(array("SORT"=>"ASC"), array("PERSON_TYPE_ID" => $personID, "USER_PROPS" => "Y"), false, false, array());
        while ($arRes = $res->Fetch()) {
            if (in_array($arRes["TYPE"], array("SELECT", "MULTISELECT", "RADIO"))) {
                $rs = \CSaleOrderPropsVariant::GetList(array(), array("ORDER_PROPS_ID" => $arRes["ID"]));
                while ($arRs = $rs->Fetch()) {
                    $arRes["variants"][] = $arRs;
                }
            }
            if (!empty($props[$arRes['ID']])) {
                $arProps[$arRes['ID']] = array_merge($props[$arRes['ID']], $arRes);
            } else {
                $arProps[$arRes['ID']] = $arRes;
            }
        }
        return $arProps;
    }

    static function Update($profileID, $arFields) {

        global $DB;

        $result = new ErrorsContainer();
        if (empty($profileID)) {
            return $result->addErrorString(GetMessage("ws.saleuserprofilesplus_save_error_required_id"));
        }
        $DB->StartTransaction();

        if (!empty($arFields["PROPS"])) {
            $props = $arFields["PROPS"];
            unset($arFields["PROPS"]);
        }

        // сохраняем поля
        if (!empty($arFields)) {
            if(!$profileID = \CSaleOrderUserProps::Update($profileID, $arFields)){
                $result->addErrorString(GetMessage("ws.saleuserprofilesplus_save_error_save_fields"));
            } else {
                $arFields = \CSaleOrderUserProps::GetByID($profileID);
            }
        }

        // сохраняем свойства
        if (!empty($props) && !$result->getErrorsAsString()) {
            // удаляем все свойства
            \CSaleOrderUserPropsValue::DeleteAll($profileID);
            $res = \CSaleOrderProps::GetList(array(), array("PERSON_TYPE_ID" => $arFields["PERSON_TYPE_ID"], "USER_PROPS" => "Y"), false, false, array());
            while ($arRes = $res->Fetch()) {
                if ($arRes['REQUIED'] === 'Y' && empty($props[$arRes['ID']])) {
                    $result->addErrorString(GetMessage("ws.saleuserprofilesplus_save_error_required_field") . "\"" . $arRes["NAME"] . "\"");
                    continue;
                }

                $arValueTemp = $props[$arRes['ID']];
                if (is_array($arValueTemp)) {
                    $arValueTemp = "";

                    for ($i = 0; $i < count($props[$arRes['ID']]); $i++) {
                        if ($i > 0) {
                            $arValueTemp .= ",";
                        }
                        $arValueTemp .= $props[$arRes['ID']][$i];
                    }

                }

                $arProp = array(
                    "VALUE" => $arValueTemp,
                    "NAME" => $arRes["NAME"],
                    "ORDER_PROPS_ID" => $arRes['ID'],
                    "USER_PROPS_ID" => $profileID
                );
                \CSaleOrderUserPropsValue::Add($arProp);
            }
        }

        if ($result->getErrorsAsString()) {
            $DB->Rollback();
        } else {
            $DB->Commit();
        }
        return $result;
    }

    static function Add($arFields) {
        $result = new ErrorsContainer();
        $fields = array(
            "NAME"              => $arFields["NAME"],
            "PERSON_TYPE_ID"    => $arFields["PERSON_TYPE_ID"],
            "USER_ID"           => $arFields["USER_ID"],
            "DATE_UPDATE"       => $arFields["DATE_UPDATE"]
        );
        if (empty($fields["USER_ID"])) {
            $result->addErrorString(GetMessage("ws.saleuserprofilesplus_save_error_required_field") . "\"код пользователя, которому принадлежит профиль\"");
        }

        // сохраняем поля
        if (!$result->getErrorsAsString() && !empty($arFields)) {
            $id = \CSaleOrderUserProps::Add($arFields);

            if ($id) {
                return $id;
            }
        }
        return $result;
    }

    static function PrepareGetListArray($key, &$arFields, &$arPropIDsTmp) {
        $propIDTmp = false;
        if (StrPos($key, "PROPERTY_ID_") === 0)
            $propIDTmp = IntVal(substr($key, StrLen("PROPERTY_ID_")));
        elseif (StrPos($key, "PROPERTY_NAME_") === 0)
            $propIDTmp = IntVal(substr($key, StrLen("PROPERTY_NAME_")));
        elseif (StrPos($key, "PROPERTY_VALUE_") === 0)
            $propIDTmp = IntVal(substr($key, StrLen("PROPERTY_VALUE_")));

        if (strlen($propIDTmp) > 0 || $propIDTmp > 0)
        {
            if (!in_array($propIDTmp, $arPropIDsTmp))
            {
                $arPropIDsTmp[] = $propIDTmp;

                $arFields["PROPERTY_ID_".$propIDTmp] = array("FIELD" => "SP_".$propIDTmp.".ID", "TYPE" => "int", "FROM" => "INNER JOIN b_sale_user_props_value SP_".$propIDTmp." ON (SP_".$propIDTmp.".ORDER_PROPS_ID = ".$propIDTmp." AND P.ID = SP_".$propIDTmp.".USER_PROPS_ID)");
                $arFields["PROPERTY_USER_PROPS_ID_".$propIDTmp] = array("FIELD" => "SP_".$propIDTmp.".USER_PROPS_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_sale_user_props_value SP_".$propIDTmp." ON (SP_".$propIDTmp.".ORDER_PROPS_ID = ".$propIDTmp." AND P.ID = SP_".$propIDTmp.".USER_PROPS_ID)");
                $arFields["PROPERTY_NAME_".$propIDTmp] = array("FIELD" => "SP_".$propIDTmp.".NAME", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_user_props_value SP_".$propIDTmp." ON (SP_".$propIDTmp.".ORDER_PROPS_ID = ".$propIDTmp." AND P.ID = SP_".$propIDTmp.".USER_PROPS_ID)");
                $arFields["PROPERTY_VALUE_".$propIDTmp] = array("FIELD" => "SP_".$propIDTmp.".VALUE", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_user_props_value SP_".$propIDTmp." ON (SP_".$propIDTmp.".ORDER_PROPS_ID = ".$propIDTmp." AND P.ID = SP_".$propIDTmp.".USER_PROPS_ID)");
            }
        }
    }


    static function GetList($arOrder = array(), $arFilter = array(), $arGroupBy = false, $arNavStartParams = false, $arSelectFields = array()){
            global $DB;

            if (!is_array($arOrder) && !is_array($arFilter))
            {
                $arOrder = strval($arOrder);
                $arFilter = strval($arFilter);
                if (strlen($arOrder) > 0 && strlen($arFilter) > 0)
                    $arOrder = array($arOrder => $arFilter);
                else
                    $arOrder = array();
                if (is_array($arGroupBy))
                    $arFilter = $arGroupBy;
                else
                    $arFilter = array();
                $arGroupBy = false;
            }

            if (count($arSelectFields) <= 0)
                $arSelectFields = array("ID", "NAME", "USER_ID", "PERSON_TYPE_ID", "DATE_UPDATE");

            // FIELDS -->
            $arFields = array(
                "ID" => array("FIELD" => "P.ID", "TYPE" => "int"),
                "NAME" => array("FIELD" => "P.NAME", "TYPE" => "string"),
                "USER_ID" => array("FIELD" => "P.USER_ID", "TYPE" => "int"),
                "PERSON_TYPE_ID" => array("FIELD" => "P.PERSON_TYPE_ID", "TYPE" => "int"),
                "DATE_UPDATE" => array("FIELD" => "P.DATE_UPDATE", "TYPE" => "datetime"),
                "FORMAT_DATE_UPDATE" => array("FIELD" => "P.DATE_UPDATE", "TYPE" => "datetime"),
                "DATE_UPDATE_FORMAT" => array("FIELD" => "P.DATE_UPDATE", "TYPE" => "datetime"),
                "PROPERTY_ID" => array("FIELD" => "SP.ID", "TYPE" => "int", "FROM" => "INNER JOIN b_sale_user_props_value SP ON (P.ID = SP.USER_PROPS_ID)"),
                "PROPERTY_USER_PROPS_ID" => array("FIELD" => "SP.USER_PROPS_ID", "TYPE" => "int", "FROM" => "INNER JOIN b_sale_user_props_value SP ON (P.ID = SP.USER_PROPS_ID)"),
                "PROPERTY_NAME" => array("FIELD" => "SP.NAME", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_user_props_value SP ON (P.ID = SP.USER_PROPS_ID)"),
                "PROPERTY_VALUE" => array("FIELD" => "SP.VALUE", "TYPE" => "string", "FROM" => "INNER JOIN b_sale_user_props_value SP ON (P.ID = SP.USER_PROPS_ID)"),
            );
            // <-- FIELDS

            $arPropIDsTmp = array();
            foreach ($arOrder as $key => $value) {
                Profile::PrepareGetListArray($key, $arFields, $arPropIDsTmp);
            }

            foreach ($arFilter as $key => $value) {
                $arKeyTmp = \CSaleOrder::GetFilterOperation($key);
                $key = $arKeyTmp["FIELD"];

                Profile::PrepareGetListArray($key, $arFields, $arPropIDsTmp);
            }

            if ($arGroupBy) {
                foreach ($arGroupBy as $key => $value) {
                    Profile::PrepareGetListArray($key, $arFields, $arPropIDsTmp);
                }
            }

            $arSqls = \CSaleOrder::PrepareSql($arFields, $arOrder, $arFilter, $arGroupBy, $arSelectFields);


            $arSqls["SELECT"] = str_replace("%%_DISTINCT_%%", "DISTINCT", $arSqls["SELECT"]);

            if (is_array($arGroupBy) && count($arGroupBy)==0)
            {
                $strSql =
                    "SELECT ".$arSqls["SELECT"]." ".
                        "FROM b_sale_user_props P ".
                        "	".$arSqls["FROM"]." ";
                if (strlen($arSqls["WHERE"]) > 0)
                    $strSql .= "WHERE ".$arSqls["WHERE"]." ";
                if (strlen($arSqls["GROUPBY"]) > 0)
                    $strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";

                $dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
                if ($arRes = $dbRes->Fetch())
                    return $arRes["CNT"];
                else
                    return False;
            }

            $strSql =
                "SELECT ".$arSqls["SELECT"]." ".
                    "FROM b_sale_user_props P ".
                    "	".$arSqls["FROM"]." ";
            if (strlen($arSqls["WHERE"]) > 0)
                $strSql .= "WHERE ".$arSqls["WHERE"]." ";
            if (strlen($arSqls["GROUPBY"]) > 0)
                $strSql .= "GROUP BY ".$arSqls["GROUPBY"]." ";
            if (strlen($arSqls["ORDERBY"]) > 0)
                $strSql .= "ORDER BY ".$arSqls["ORDERBY"]." ";

            if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])<=0)
            {
                $strSql_tmp =
                    "SELECT COUNT('x') as CNT ".
                        "FROM b_sale_user_props P ".
                        "	".$arSqls["FROM"]." ";
                if (strlen($arSqls["WHERE"]) > 0)
                    $strSql_tmp .= "WHERE ".$arSqls["WHERE"]." ";
                if (strlen($arSqls["GROUPBY"]) > 0)
                    $strSql_tmp .= "GROUP BY ".$arSqls["GROUPBY"]." ";

                $dbRes = $DB->Query($strSql_tmp, false, "File: ".__FILE__."<br>Line: ".__LINE__);
                $cnt = 0;
                if (strlen($arSqls["GROUPBY"]) <= 0)
                {
                    if ($arRes = $dbRes->Fetch())
                        $cnt = $arRes["CNT"];
                }
                else
                {
                    // FOR MYSQL!!! ANOTHER CODE FOR ORACLE
                    $cnt = $dbRes->SelectedRowsCount();
                }

                $dbRes = new \CDBResult();
                $dbRes->NavQuery($strSql, $cnt, $arNavStartParams);
            }
            else
            {
                if (is_array($arNavStartParams) && IntVal($arNavStartParams["nTopCount"])>0) {
                    $strSql .= "LIMIT ".IntVal($arNavStartParams["nTopCount"]);
                }
                $dbRes = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
            }

            return $dbRes;
    }
}
?>