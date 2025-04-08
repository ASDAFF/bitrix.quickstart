<?php

namespace Helper\UserProp;

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
class CIBEditSimaiComplexProp
{
    static function OnBeforePrologHandler()
    {
        $GLOBALS['SCP_IN_LIST'] = ($_REQUEST['mode'] == 'frame' || /*$_REQUEST['grid_id'] ||*/ $_SERVER['PHP_SELF'] == '/bitrix/admin/iblock_element_admin.php');

        if ((is_array($_POST["SPROP"]) || is_array($_FILES["SPROP"])) && !$GLOBALS['SCP_IN_LIST'])
        {
            foreach ($_POST["SPROP"] as $pid => $parr)
                $_POST["PROP"][$pid] = $parr;

            if (is_array($_FILES["SPROP"]))
            {
                foreach ($_FILES["SPROP"] as $fid => $farr)
                {
                    foreach ($farr as $pid => $parr)
                    {
                        foreach ($parr as $tid => $tval)
                        {
                            if ($_FILES["SPROP"]["name"][$pid][$tid] != "")
                                $_FILES["PROP"][$fid][$pid][$tid] = $tval;
                        }
                    }
                }
            }

            foreach ($_POST as $pid => $parr)
            {
                if (substr_count($pid, "SPROP_"))
                {
                    $pid2 = str_replace("SPROP_","PROP_",$pid);
                    $_POST[$pid2] = $parr;
                    /*$pid2 = str_replace("PROP_s","PROP_",$pid2);
                    $_POST[$pid2] = $parr;*/
                    $pid2 = str_replace("_s","_",$pid2);
                    $_POST[$pid2] = $parr;
                }
            }
        }

        $GLOBALS["SIMAI_COMPLEXPROP_FORBIDDEN_UT"] = array(
            "simai_complex",
            "video",
            "map_yandex",
            "map_google",
            "ElementXmlID",
            "Sequence",
            "SKU",
        );
        $GLOBALS["SCP_PU"] = false;
        $GLOBALS["SCP_FROM_API"] = false;
        $GLOBALS["SCP_PVDesc"] = array();
        $GLOBALS["SCP_req_all"] = array();
        $GLOBALS["SCP_err_values"] = array();
    }

    static function OnStartIBlockElementUpdateHandler(&$arF)
    {
        if (is_array($arF["PROPERTY_VALUES"]))
        {
            if (!$GLOBALS['SCP_IN_LIST'])
            {
                $GLOBALS["SCP_PU"] = true;
                foreach ($arF["PROPERTY_VALUES"] as $prop_id => $prop_values)
                {
                    if (!is_array($prop_values))
                        $prop_values = array();
                    foreach ($prop_values as $prop_value_id => $prop_value)
                    {
                        $svalues = array();
                        $req = array();

                        $vals_subprops = false;

                        $prop_value_id_rand = 'n'.rand();

                        if (is_array($prop_value))
                        {
                            if (isset($prop_value['SUBPROP_VALUES']))
                            {
                                $GLOBALS["SCP_FROM_API"] = true;

                                $file_multy_props = array();

                                if (is_array($prop_value['SUBPROP_VALUES']))
                                {
                                    foreach ($prop_value['SUBPROP_VALUES'] as $spv_pid_code => $spv_val)
                                    {
                                        $spv_pid = 0;

                                        if (intval($spv_pid_code) > 0)
                                        {
                                            $res = \CIBlockProperty::GetList(array("sort" => "asc"), array("ACTIVE" => "Y", "IBLOCK_ID" => intval($arF['IBLOCK_ID']), "ID" => $spv_pid_code));
                                            if ($arr = $res->fetch())
                                            {
                                                $spv_pid = intval($arr['ID']);

                                                if ($arr['PROPERTY_TYPE'] == 'F' && $arr["MULTIPLE"] == "Y")
                                                    $file_multy_props[$spv_pid] = $spv_pid;
                                            }
                                        }
                                        elseif(trim($spv_pid_code))
                                        {
                                            $res = \CIBlockProperty::GetList(array("sort" => "asc"), array("ACTIVE" => "Y", "IBLOCK_ID" => intval($arF['IBLOCK_ID']), "CODE" => $spv_pid_code));
                                            if ($arr = $res->fetch())
                                            {
                                                $spv_pid = intval($arr['ID']);

                                                if ($arr['PROPERTY_TYPE'] == 'F' && $arr["MULTIPLE"] == "Y")
                                                    $file_multy_props[$spv_pid] = $spv_pid;
                                            }
                                        }

                                        if ($spv_pid)
                                        {
                                            $arF["PROPERTY_VALUES"][$spv_pid][$prop_value_id_rand]["VALUE"] = $spv_val;
                                            $vals_subprops[$spv_pid] = $prop_value_id_rand;
                                        }

                                    }

                                    if ($arF['ID'] > 0 && count($file_multy_props) > 0)
                                    {
                                        foreach ($file_multy_props as $spv_pid)
                                        {
                                            if ($spv_pid > 0)
                                            {
                                                $res = \CIBlockElement::GetProperty($arF['IBLOCK_ID'], $arF['ID'], array("sort" => "asc"), array("ID"=>$spv_pid));
                                                while ($arr = $res->fetch())
                                                {
                                                    $prop_value_id_for_delete = $arr["PROPERTY_VALUE_ID"];

                                                    $arF["PROPERTY_VALUES"][$spv_pid][$prop_value_id_for_delete]["VALUE"]["del"] = "Y";
                                                    $arF["PROPERTY_VALUES"][$spv_pid][$prop_value_id_for_delete]["DESCRIPTION"] = "";
                                                }
                                            }
                                        }
                                    }
                                }
                            }


                            if (isset($prop_value['VALUE']['SUBPROPS']))
                            {
                                if (is_array($prop_value['VALUE']['SUBPROPS']))
                                    $vals_subprops = $prop_value['VALUE']['SUBPROPS'];
                            }
                            elseif (isset($prop_value['VALUE']['SUB_VAL_IDS']))
                            {
                                if (is_array($prop_value['VALUE']['SUB_VAL_IDS']))
                                    $vals_subprops = $prop_value['VALUE']['SUB_VAL_IDS'];
                            }
                        }

                        if (is_array($vals_subprops))
                        {
                            if (!$_REQUEST['no_'.$prop_id.'_'.$prop_value_id])
                            {
                                if (isset($prop_value['SUBPROP_VALUES']))
                                    $pdesc = "scp_".$prop_value_id_rand;
                                elseif (IntVal($prop_value_id))
                                    $pdesc = "scp_".$prop_value_id;
                                else
                                    $pdesc = "scp_".$prop_id.$prop_value_id;


                                if (isset($prop_value['VALUE']['FLEG']))
                                {
                                    if (is_array($prop_value['VALUE']['FLEG']))
                                    {
                                        foreach ($prop_value['VALUE']['FLEG'] as $subprop_id => $subprop_val)
                                            $GLOBALS["SCP_PVDesc"][$subprop_id][$subprop_val][$pdesc] = $pdesc;
                                    }
                                }

                                foreach ($vals_subprops as $subprop_id => $subprop_vid)
                                {
                                    if ($subprop_id > 0)
                                    {
                                        $subprop_val = array("VALUE" => "");
                                        $rq = $prop_value['VALUE']['REQ'][$subprop_id];
                                        $fl = $prop_value['VALUE']['FL'][$subprop_id];
                                        $fl_vid = $prop_value['VALUE']['FL_VID'][$subprop_id];
                                        $fl_id = $prop_value['VALUE']['FL_ID'][$subprop_id];
                                        if (!$fl_id && $prop_value['VALUE']['FLEG'][$subprop_id])
                                            $fl_id = $prop_value['VALUE']['FLEG'][$subprop_id];

                                        if (is_array($prop_value['VALUE']) && $prop_value['VALUE']['DEL'] == "y")
                                        {
                                            $arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid] = array(
                                                "VALUE" => false,
                                                "DESCRIPTION" => false
                                            );
                                            if ($fl)
                                            {
                                                $arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["VALUE"]["del"] = "Y";
                                                $arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["DESCRIPTION"] = "";
                                            }
                                        }
                                        else
                                        {
                                            $fl_name = "";
                                            $fl_del = "";
                                            $fl_arr = array();

                                            $fextcheck = false;
                                            if (isset($prop_value['VALUE']['FL_ID'][$subprop_id]))
                                            {
                                                if ($prop_value['VALUE']['FL_ID'][$subprop_id])
                                                    $fextcheck = true;
                                            }

                                            if (isset($arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["VALUE"]) || $fextcheck)
                                            {
                                                if (isset($arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["VALUE"]["name"]))
                                                    $fl_name = $arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["VALUE"]["name"];
                                                elseif (isset($arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["name"]))
                                                {
                                                    $fl_name = $arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["name"];
                                                    $fl_arr = $arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid];
                                                    $arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid] = array();
                                                    $arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["VALUE"] = $fl_arr;
                                                }

                                                if (isset($arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["VALUE"]["del"]))
                                                    $fl_del = $arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["VALUE"]["del"];

                                                if (isset($arF["PROPERTY_VALUES"][$subprop_id]["sel_".$prop_value_id]["VALUE"]) && is_array($arF["PROPERTY_VALUES"][$subprop_id]["sel_".$prop_value_id]))
                                                    $subprop_val = $arF["PROPERTY_VALUES"][$subprop_id]["sel_".$prop_value_id]["VALUE"];
                                                if (isset($arF["PROPERTY_VALUES"][$subprop_id]["sel_".$prop_value_id]))
                                                    $subprop_val = $arF["PROPERTY_VALUES"][$subprop_id]["sel_".$prop_value_id];
                                                elseif ($arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid])
                                                    $subprop_val = $arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid];

                                                if ($fl_id && strlen($fl_name) < 2 && $fl_del != "y" && $fl_del != "Y")
                                                {
                                                    //$arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["VALUE"] = \CFile::MakeFilearray($fl_id);
                                                    $GLOBALS["SCP_PVDesc"][$subprop_id][$fl_id][$pdesc] = $pdesc;
                                                    $svalues[$subprop_id][$subprop_vid] = $pdesc;
                                                }
                                                elseif (isset($subprop_val["VALUE"]["TEXT"]))
                                                {
                                                    if ($subprop_val["VALUE"]["TEXT"] != "")
                                                        $svalues[$subprop_id][$subprop_vid] = $pdesc;
                                                    elseif ($rq)
                                                        $req[$subprop_id] = $subprop_id;
                                                }
                                                elseif ($subprop_val["VALUE"] != "")
                                                {
                                                    $svalues[$subprop_id][$subprop_vid] = $pdesc;
                                                }
                                                elseif ($fl_name != "")
                                                {
                                                    //unset($arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["VALUE"]);
                                                    $svalues[$subprop_id][$subprop_vid] = $pdesc;
                                                }
                                                elseif ($rq)
                                                {
                                                    $req[$subprop_id] = $subprop_id;
                                                }

                                                if ($rq && ($fl_del == "Y" || $fl_del == "y"))
                                                    $req[$subprop_id] = $subprop_id;

                                                $arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["DESCRIPTION"] = $pdesc;

                                                $ncheck = false;
                                                if (isset($subprop_val["VALUE"]))
                                                {
                                                    if (isset($subprop_val["VALUE"]['name']))
                                                    {
                                                        if (strlen($subprop_val["VALUE"]['name']) > 2)
                                                        {
                                                            $subprop_val = $subprop_val["VALUE"]['name'];
                                                            $ncheck = true;
                                                        }
                                                    }

                                                    if (isset($subprop_val["VALUE"]))
                                                    {
                                                        if ($subprop_val["VALUE"] && !$ncheck)
                                                        {
                                                            $subprop_val = $subprop_val["VALUE"];
                                                        }
                                                    }
                                                }
                                                if (is_array($subprop_val) && !$ncheck)
                                                    $subprop_val = current($subprop_val);

                                                if ($subprop_val)
                                                {
                                                    $GLOBALS["SCP_PVDesc"][$subprop_id][$subprop_val][$pdesc] = $pdesc;
                                                }
                                            }
                                        }
                                    }
                                }
                                if (count($svalues) > 0)
                                {
                                    if (count($req) > 0)
                                        $GLOBALS["SCP_req_all"] = array_merge($GLOBALS["SCP_req_all"], $req);

                                    $GLOBALS["SCP_err_values"][$prop_id][$prop_value_id] = $prop_value_id;

                                    $arF["PROPERTY_VALUES"][$prop_id][$prop_value_id]["VALUE"] = array();
                                    $arF["PROPERTY_VALUES"][$prop_id][$prop_value_id]["DESCRIPTION"] = $pdesc;
                                }
                                else
                                {
                                    $arF["PROPERTY_VALUES"][$prop_id][$prop_value_id]["VALUE"] = false;
                                    $arF["PROPERTY_VALUES"][$prop_id][$prop_value_id]["DESCRIPTION"] = false;
                                }
                            }
                            else
                            {
                                $arF["PROPERTY_VALUES"][$prop_id][$prop_value_id]["VALUE"] = false;
                                $arF["PROPERTY_VALUES"][$prop_id][$prop_value_id]["DESCRIPTION"] = false;
                            }
                        }
                    }
                }
            }
            else
            {
                $sc_props = array();
                $res = \CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID" => $arF["IBLOCK_ID"]));
                while ($arr = $res->fetch())
                {
                    if ($arr["USER_TYPE"] == "simai_complex")
                    {
                        $sc_props[$arr["ID"]] = $arr["USER_TYPE_SETTINGS"]["SUBPROPS"];
                    }
                }
                foreach ($sc_props as $scpid => $subprops)
                {
                    foreach ($subprops as $subprop_id)
                    {
                        $res = \CIBlockElement::GetProperty($arF["IBLOCK_ID"], $arF["ID"], "sort", "asc", array("ID" => $subprop_id));
                        while ($arr = $res->fetch())
                        {
                            $subprop_vid = $arr["PROPERTY_VALUE_ID"];

                            if (isset($arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]))
                            {
                                $subprop_val_desc_current = $arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid];

                                if (!is_array($subprop_val_desc_current))
                                {
                                    $arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid] = array(
                                        "VALUE" => $subprop_val_desc_current,
                                    );
                                }

                                $arF["PROPERTY_VALUES"][$subprop_id][$subprop_vid]["DESCRIPTION"] = $arr["DESCRIPTION"];
                            }
                        }
                    }
                }
            }
        }
    }

    static function OnBeforeIBlockElementUpdateHandler(&$arF)
    {
        if (is_array($GLOBALS["SCP_req_all"]) && count($GLOBALS["SCP_req_all"]) > 0)
        {
            global $APPLICATION;
            $e = new \CAdminException($msg);
            foreach ($GLOBALS["SCP_req_all"] as $pid)
            {
                $res = \CIBlockProperty::GetByID($pid);
                if ($arr = $res->fetch())
                    $e->AddMessage(array("text" => GetMessage('SMCP_REQ_EMPTY')."&laquo;".$arr["NAME"]."&raquo;"));
            }
            $APPLICATION->ThrowException($e);
            return false;
        }
    }

    static function OnAfterIBlockElementUpdateHandler(&$arF)
    {
        global $PROP;

        if ($GLOBALS["SCP_PU"])
        {
            if($arF["ID"] > 0 && $arF["RESULT"])
            {
                /*
                $element_desc_from = $arF["ID"];
                if ($bCopy || $_REQUEST['action'] == 'copy')
                {
                    if ($_REQUEST["ID"])
                        $element_desc_from = intval($_REQUEST["ID"]);
                }
                */

                $sc_props = array();
                $sc_props_m = array();
                $res = \CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arF["IBLOCK_ID"]));
                while ($arr = $res->fetch())
                {
                    if ($arr["USER_TYPE"] == "simai_complex")
                    {
                        $sc_props[$arr["ID"]] = $arr["USER_TYPE_SETTINGS"]["SUBPROPS"];
                        $sc_props_m[$arr["ID"]] = ($arr["MULTIPLE"] == "Y");
                    }
                }
                foreach ($sc_props as $scpid => $subprops)
                {
                    $scvals = array();
                    $scval_s = false;
                    $res = \CIBlockElement::GetProperty($arF["IBLOCK_ID"], $arF["ID"], "sort", "asc", array("ID"=>$scpid));
                    while ($arr = $res->fetch())
                    {
                        if ($arr["DESCRIPTION"])
                            $scvals[$arr["DESCRIPTION"]] = $arr["PROPERTY_VALUE_ID"];
                        $scval_s = $arr["PROPERTY_VALUE_ID"];
                    }
                    $svalues_uf = array();
                    $svalues_ufs = array();
                    foreach ($subprops as $subprop_id)
                    {
                        $res = \CIBlockElement::GetProperty($arF["IBLOCK_ID"], $arF["ID"], "sort", "asc", array("ID"=>$subprop_id));
                        while ($arr = $res->fetch())
                        {
                            $valkey = $arr["VALUE"];
                            if (is_array($valkey))
                            {
                                if (isset($valkey['TEXT']))
                                    $valkey = $valkey['TEXT'];
                                else
                                    $valkey = current($valkey);
                            }

                            $valkey = (string) $valkey;

                            if (isset($GLOBALS["SCP_PVDesc"][$subprop_id][$valkey]))
                                $s_desc_arr = $GLOBALS["SCP_PVDesc"][$subprop_id][$valkey];
                            else
                                $s_desc_arr = false;

                            if ((is_array($PROP) && count($PROP) > 0) || $GLOBALS["SCP_FROM_API"])
                            {
                                if (!in_array($arr["PROPERTY_TYPE"], array("L","E","G")))
                                {
                                    if ($arr["PROPERTY_TYPE"] == "F")
                                    {
                                        $svalues_uf[$arr["DESCRIPTION"]][$arF["ID"]][$subprop_id][$arr["PROPERTY_VALUE_ID"]] = $arr["VALUE"];
                                        $svalues_ufs[$arF["ID"]][$subprop_id][$arr["PROPERTY_VALUE_ID"]] = $arr["VALUE"];

                                        if ($valkey && isset($GLOBALS["SCP_PVDesc"][$subprop_id][$valkey]))
                                        {
                                            if (is_array($GLOBALS["SCP_PVDesc"][$subprop_id][$valkey]))
                                            {
                                                $desc = current($GLOBALS["SCP_PVDesc"][$subprop_id][$valkey]);

                                                $svalues_uf[$desc][$arF["ID"]][$subprop_id][$arr["PROPERTY_VALUE_ID"]] = $arr["VALUE"];
                                                $svalues_ufs[$arF["ID"]][$subprop_id][$arr["PROPERTY_VALUE_ID"]] = $arr["VALUE"];
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $svalues_uf[$arr["DESCRIPTION"]][$arF["ID"]][$subprop_id][$arr["PROPERTY_VALUE_ID"]] = $arr["DESCRIPTION"];
                                        $svalues_ufs[$arF["ID"]][$subprop_id][$arr["PROPERTY_VALUE_ID"]] = $arr["DESCRIPTION"];
                                    }
                                }
                                elseif (is_array($s_desc_arr))
                                {
                                    foreach ($s_desc_arr as $s_desc)
                                    {
                                        $svalues_uf[$s_desc][$arF["ID"]][$subprop_id][$arr["PROPERTY_VALUE_ID"]] = $arr["VALUE"];
                                        $svalues_ufs[$arF["ID"]][$subprop_id][$arr["PROPERTY_VALUE_ID"]] = $arr["VALUE"];
                                    }
                                }

                                if ($arr["PROPERTY_TYPE"] == "F")
                                {

                                }
                            }
                            else
                            {
                                if (!in_array($arr["PROPERTY_TYPE"], array("F", "L","E","G")))
                                {
                                    $svalues_uf[$arr["DESCRIPTION"]][$arF["ID"]][$subprop_id][$arr["PROPERTY_VALUE_ID"]] = $arr["DESCRIPTION"];
                                    $svalues_ufs[$arF["ID"]][$subprop_id][$arr["PROPERTY_VALUE_ID"]] = $arr["DESCRIPTION"];
                                }
                                elseif (is_array($s_desc_arr))
                                {
                                    foreach ($s_desc_arr as $s_desc)
                                    {
                                        $svalues_uf[$s_desc][$arF["ID"]][$subprop_id][$arr["PROPERTY_VALUE_ID"]] = $arr["VALUE"];
                                        $svalues_ufs[$arF["ID"]][$subprop_id][$arr["PROPERTY_VALUE_ID"]] = $arr["VALUE"];
                                    }
                                }
                            }
                        }
                    }
                    $prop = array();
                    if ($sc_props_m[$scpid])
                    {
                        foreach ($scvals as $desc => $scvid)
                        {
                            $svalues = $svalues_uf[$desc];
                            if (is_array($svalues) && count($svalues) > 0)
                                $prop[$scpid][$scvid] = array("VALUE" => $svalues, "DESCRIPTION" => $desc);
                        }
                    }
                    else
                    {
                        $svalues = $svalues_ufs;
                        if (is_array($svalues) && count($svalues) > 0)
                            $prop[$scpid][$scval_s] = array("VALUE" => $svalues, "DESCRIPTION" => "");
                    }
                    \CIBlockElement::SetPropertyValuesEx($arF["ID"], $arF["IBLOCK_ID"], $prop);
                }
            }
        }
    }
}