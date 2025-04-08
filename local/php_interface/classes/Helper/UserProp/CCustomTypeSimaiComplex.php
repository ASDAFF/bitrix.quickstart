<?php

namespace Helper\UserProp;

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

$GLOBALS["err_m"] = false;

class CCustomTypeSimaiComplex
{
    public static function GetUserTypeDescription() 
    {
        return array(
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'simai_complex',
            'DESCRIPTION' => GetMessage('SMCP_COMPLEX_PROP'),
            'PrepareSettings' => array('CCustomTypeSimaiComplex', 'PrepareSettings'),
            'GetSettingsHTML' => array('CCustomTypeSimaiComplex', 'GetSettingsHTML'),
            'GetPropertyFieldHtml' => array('CCustomTypeSimaiComplex', 'GetPropertyFieldHtml'),
            'GetPropertyFieldHtmlMulty' => array('CCustomTypeSimaiComplex', 'GetPropertyFieldHtmlMulty'),
            'GetPublicEditHTML' => array('CCustomTypeSimaiComplex', 'GetPublicEditHTML'),
            'ConvertToDB' => array('CCustomTypeSimaiComplex', 'ConvertToDB'),
            'ConvertFromDB' => array('CCustomTypeSimaiComplex', 'ConvertFromDB'),
            'GetPublicViewHTML' => array('CCustomTypeSimaiComplex', 'GetPublicViewHTML'),
            'GetSearchContent' => array('CCustomTypeSimaiComplex', 'GetSearchContent'),
            'GetAdminFilterHTML' => array('CCustomTypeSimaiComplex', 'GetAdminFilterHTML'),
            'GetAdminListViewHTML' => array('CCustomTypeSimaiComplex', 'GetAdminListViewHTML'),
            'GetPublicFilterHTML' => array('CCustomTypeSimaiComplex', 'GetPublicFilterHTML'),
        );
    }
    
    public static function PrepareSettings($arFields)
    {
        $props = array();
        $reqs = array();
        for ($i = 0; $i < 7; $i++)
        {
            if (is_array($arFields["USER_TYPE_SETTINGS"]))
            {
                $prop = IntVal($arFields["USER_TYPE_SETTINGS"]["SUBPROPS"][$i]);
                $req = IntVal($arFields["USER_TYPE_SETTINGS"]["SUBPROPS_REQ"][$i]);
                if ($prop && !in_array($prop,$props))
                {
                    $props[] = $prop;
                    $reqs[] = $req;
                }
            }
        }
        return array("SUBPROPS" => $props, "SUBPROPS_REQ" => $reqs);
    }
    
    public static function GetAllProps($IBLOCK_ID, $PID)
    {
        \CModule::IncludeModule("iblock");
        
        $IBLOCK_ID = IntVal($IBLOCK_ID);
        $PID = IntVal($PID);
        $props_ = array();
        $props_f = array();
        $props = array();
        $res = \CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID));
        while ($arr = $res->fetch())
        {
            if ($arr["USER_TYPE"] == "simai_complex" && $arr["ID"] != $PID)
            {
                if (isset($arr["USER_TYPE_SETTINGS"]["SUBPROPS"]))
                {
                    if (is_array($arr["USER_TYPE_SETTINGS"]["SUBPROPS"]))
                    {
                        foreach($arr["USER_TYPE_SETTINGS"]["SUBPROPS"] as $p_id)
                            $props_f[$p_id] = $p_id;
                    }
                }
            }
            if (!in_array($arr["USER_TYPE"], $GLOBALS["SIMAI_COMPLEXPROP_FORBIDDEN_UT"]))
                $props_[$arr["ID"]] = $arr;
        }
        foreach ($props_ as $p_id => $arr)
        {
            if (!$props_f[$p_id])
                $props[$arr["ID"]] = $arr;
        }
        return $props;
    }
    
    public static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
    {
        $arPropertyFields = array(
            "HIDE" => array("FILTRABLE", "ROW_COUNT", "COL_COUNT", "DEFAULT_VALUE", "WITH_DESCRIPTION", "SEARCHABLE"),
            "SET" => array("FILTRABLE" => "N", "SEARCHABLE" => "N", "SMART_FILTER" => "N", "MULTIPLE_CNT"=>"1"),
            "USER_TYPE_SETTINGS_TITLE" => GetMessage('SMCP_SELECT_SUBPROPS')
        );
        
        $props = self::GetAllProps($_REQUEST["PARAMS"]["IBLOCK_ID"],$_REQUEST["PARAMS"]["ID"]);
        
        $return = "";
        
        for ($i = 0; $i < 7; $i++)
        {
            $return .= '
            <tr>
            <td>'.GetMessage('SMCP_PROP_NUM').($i + 1).':</td>
            <td>
            <select name="'.$strHTMLControlName["NAME"].'[SUBPROPS]['.$i.']" style="width:270px;">
                <option value="">---</option>';
            foreach ($props as $pid => $parr)
            {
                $sel = "";
                if (isset($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS"][$i]))
                {
                    $sel = ($pid == $arProperty["USER_TYPE_SETTINGS"]["SUBPROPS"][$i] ? " selected" : "");
                }
                $return .= '<option value="'.$pid.'"'.$sel.'>['.$parr["CODE"].'] '.$parr["NAME"].'</option>';
            }
            $return .= '</select>';
            $ch = "";
            if (isset($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS_REQ"][$i]))
            {
                $ch = ($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS_REQ"][$i] ? " checked" : "");
            }
            $return .= ' <input type="checkbox" value="1" id="subprop_req_'.$arProperty["ID"].'_'.$i.'" name="'.$strHTMLControlName["NAME"].'[SUBPROPS_REQ]['.$i.']"'.$ch.'> <label for="subprop_req_'.$arProperty["ID"].'_'.$i.'">'.GetMessage('SMCP_PROP_REQ').'</label>
            </td>
            </tr>';
        }
        return $return;
    }
    
    public static function ShowListPropertyField($name, $property_fields, $values, $bInitDef = false, $def_text = false)
    {
        $name = htmlspecialcharsbx($name);

        if (!is_array($values))
            $values = array();

        foreach($values as $key => $value)
        {
            if(is_array($value) && array_key_exists("VALUE", $value))
                $values[$key] = $value["VALUE"];
        }

        $id = $property_fields["ID"];
        $multiple = $property_fields["MULTIPLE"];
        $res = "";
        if($property_fields["LIST_TYPE"]=="C") //list property as checkboxes
        {
            $cnt = 0;
            $wSel = false;
            $prop_enums = \CIBlockProperty::GetPropertyEnum($id);
            while($ar_enum = $prop_enums->fetch())
            {
                $cnt++;
                if($bInitDef)
                    $sel = ($ar_enum["DEF"]=="Y");
                else
                    $sel = in_array($ar_enum["ID"], $values);
                if($sel)
                    $wSel = true;

                $uniq = md5(uniqid(rand(), true));
                if($multiple=="Y") //multiple
                    $res .= '<input type="checkbox" name="'.$name.'[]" value="'.htmlspecialcharsbx($ar_enum["ID"]).'"'.($sel?" checked":"").' id="'.$uniq.'"><label for="'.$uniq.'">'.htmlspecialcharsex($ar_enum["VALUE"]).'</label><br>';
                else //if(MULTIPLE=="Y")
                    $res .= '<input type="radio" name="'.$name.'[]" id="'.$uniq.'" value="'.htmlspecialcharsbx($ar_enum["ID"]).'"'.($sel?" checked":"").'><label for="'.$uniq.'">'.htmlspecialcharsex($ar_enum["VALUE"]).'</label><br>';

                if($cnt==1)
                    $res_tmp = '<input type="checkbox" name="'.$name.'[]" value="'.htmlspecialcharsbx($ar_enum["ID"]).'"'.($sel?" checked":"").' id="'.$uniq.'"><br>';
            }


            $uniq = md5(uniqid(rand(), true));

            if($cnt==1)
                $res = $res_tmp;
            elseif($multiple!="Y")
                $res = '<input type="radio" name="'.$name.'[]" value=""'.(!$wSel?" checked":"").' id="'.$uniq.'"><label for="'.$uniq.'">'.htmlspecialcharsex(($def_text ? $def_text : GetMessage("IBLOCK_AT_PROP_NO") )).'</label><br>'.$res;

            /*if($multiple=="Y" || $cnt==1)
                $res = '<input type="hidden" name="'.$name.'" value="">'.$res;*/
        }
        else //list property as list
        {
            $bNoValue = true;
            $prop_enums = \CIBlockProperty::GetPropertyEnum($id);
            while($ar_enum = $prop_enums->fetch())
            {
                if($bInitDef)
                    $sel = ($ar_enum["DEF"]=="Y");
                else
                    $sel = in_array($ar_enum["ID"], $values);
                if($sel)
                    $bNoValue = false;
                $res .= '<option value="'.htmlspecialcharsbx($ar_enum["ID"]).'"'.($sel?" selected":"").'>'.htmlspecialcharsex($ar_enum["VALUE"]).'</option>';
            }

            if($property_fields["MULTIPLE"]=="Y" && (int)$property_fields["ROW_COUNT"]<2)
                $property_fields["ROW_COUNT"] = 5;
            if($property_fields["MULTIPLE"]=="Y")
                $property_fields["ROW_COUNT"]++;
            $res = '<select name="'.$name.'[]" size="'.$property_fields["ROW_COUNT"].'" '.($property_fields["MULTIPLE"]=="Y"?"multiple":"").'>'.
                    '<option value=""'.($bNoValue?' selected':'').'>'.htmlspecialcharsex(($def_text ? $def_text : GetMessage("IBLOCK_AT_PROP_NA") )).'</option>'.
                    $res.
                    '</select>';
        }
        echo $res;
    }
    
    public static function ShowFilePropertyField($name, $property_fields, $values, $max_file_size_show=50000, $bVarsFromForm = false)
    {
        global $bCopy, $historyId;

        $name = htmlspecialcharsbx($name);

        static $maxSize = array();
        if (empty($maxSize))
        {
            //$detailImageSize = (int) \Main\Config\Option::get('iblock', 'detail_image_size');
            $maxSize = array(
                'W' => "200",
                'H' => "200"
            );
        }

        \CModule::IncludeModule('fileman');
        
        if (empty($values) || !is_array($values))
        {
            $values = array(
                "n0" => NULL,
            );
        }

        if ($historyId > 0)
        {
            $inputParams = array(
                'upload' => false,
                'medialib' => false,
                'file_dialog' => false,
                'cloud' => false,
                'del' => false,
                'description' => false
            );
            $newUploaderParams = array(
                'delete' => false,
                'edit' => false
            );
        }
        else
        {
            $inputParams = array(
                'upload' => true,
                'medialib' => true,
                'file_dialog' => true,
                'cloud' => true,
                'del' => true,
                'description' => $property_fields["WITH_DESCRIPTION"] == "Y",
            );
            $newUploaderParams = array(
                "upload" => true,
                "medialib" => true,
                "fileDialog" => true,
                "cloud" => true
            );
        }
        $oldUploaderParams = array(
            "IMAGE" => "Y",
            "PATH" => "Y",
            "FILE_SIZE" => "Y",
            "DIMENSIONS" => "Y",
            "IMAGE_POPUP" => "Y",
            "MAX_SIZE" => $maxSize
        );
        
        foreach($values as $key => $val)
        {
            if(is_array($val))
                $file_id = $val["VALUE"];
            else
                $file_id = $val;
            
            if ($bCopy || ($_REQUEST['action'] == 'copy'))
                $file_id = NULL;
            
            echo \CFileInput::Show($name."[".$key."]", $file_id, $oldUploaderParams, $inputParams);
            
            break;
        }
    }

    public static function ShowPropertyField($name, $property_fields, $values, $bInitDef = false, $bVarsFromForm = false, $max_file_size_show = 50000, $form_name = "form_element", $bCopy = false)
    {
        $type = $property_fields["PROPERTY_TYPE"];
        if($property_fields["USER_TYPE"]!="")
            _ShowUserPropertyField($name, $property_fields, $values, $bInitDef, $bVarsFromForm, $max_file_size_show, $form_name, $bCopy);
        elseif($type=="L") //list property
            self::ShowListPropertyField($name, $property_fields, $values, $bInitDef);
        elseif($type=="F") //file property
            self::ShowFilePropertyField($name, $property_fields, $values, $max_file_size_show, $bVarsFromForm);
        elseif($type=="G") //section link
        {
            if(function_exists("_ShowGroupPropertyField_custom"))
                _ShowGroupPropertyField_custom($name, $property_fields, $values, $bVarsFromForm);
            else
                _ShowGroupPropertyField($name, $property_fields, $values, $bVarsFromForm);
        }
        elseif($type=="E") //element link
            _ShowElementPropertyField($name, $property_fields, $values, $bVarsFromForm);
        else
            _ShowStringPropertyField($name, $property_fields, $values, $bInitDef, $bVarsFromForm);
    }
    
    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName) 
    {
        global $bVarsFromForm, $bCopy, $PROP;
        $ID = IntVal($_REQUEST['ID']);
        
        $strResult = "";
        
        $value_id = "n0";
        
        $value_arr = $value;
        
        $subprops = false;
        
        ob_start();
        
        $sel_pvals = array();
        
        if (is_array($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS"]) && is_array($PROP) && count($PROP) > 0)
        {
            echo '<table style="width:100%; background:#e0e8ea; margin-bottom:20px;">';
            foreach ($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS"] as $sp_num => $sprop)
            {
                $subprops = true;
                
                $pval_id = false;
                
                if (isset($value_arr["VALUE"]["SUB_VAL_IDS"][$sprop]))
                    $pval_id = $value_arr["VALUE"]["SUB_VAL_IDS"][$sprop];
                    
                if (!$pval_id && is_array($value_arr["VALUE"]) && is_array($value_arr["VALUE"]["SUBPROPS"]))
                    $pval_id = $value_arr["VALUE"]["SUBPROPS"][$sprop];
                    
                if (!$pval_id)
                    $pval_id = $value_id;
                    
                $sel_pvals[$sprop] = "sel_".$value_id;
                
                foreach($PROP as $prop_code_ => $prop_fields_)
                {
                    if ($prop_fields_["ID"] == $sprop && $prop_fields_["MULTIPLE"] != "Y" && !in_array($prop_fields_["USER_TYPE"], $GLOBALS["SIMAI_COMPLEXPROP_FORBIDDEN_UT"]))
                    {
                        $prop_fields__ = $prop_fields_;
                        
                        if (in_array($prop_fields__["PROPERTY_TYPE"], array("L","E","G")))
                            $pval_id_ = "sel_".$value_id;
                        else
                            $pval_id_ = $pval_id;
                        
                        $prop_fields__["MULTIPLE"] = "N";
                        $prop_fields__["WITH_DESCRIPTION"] = "N";
                        
                        if ($pval_id)
                            $pvalue = array($pval_id_ => $prop_fields__["VALUE"][$pval_id]);
                        else
                            $pvalue = array($value_id => false);
                        
                        $prop_fields__["VALUE"] = $pvalue;
                        $prop_fields__["~VALUE"] = $pvalue;
                        
                        $req = ($prop_fields__["IS_REQUIRED"] == "Y" || $arProperty["USER_TYPE_SETTINGS"]["SUBPROPS_REQ"][$sp_num]);
                        $fl = ($prop_fields__["PROPERTY_TYPE"] == "F");
                        
                        echo '<tr><td class="adm-detail-valign-top" style="text-align:right; padding:10px 0 20px 10px;">'.($req ? '<b>' : '').'<i>'.$prop_fields__["NAME"].':</i>'.($req ? '</b>' : '').'</td><td style="padding:10px 20px;">';
                        self::ShowPropertyField('PROP['.$prop_fields__["ID"].']', $prop_fields__, $pvalue, (($historyId <= 0) && (!$bVarsFromForm) && ($ID <= 0)), false, 50000, $strHTMLControlName["FORM_NAME"], $bCopy);
                        
                        echo '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'[SUBPROPS]['.IntVal($sprop).']" value="'.htmlspecialcharsbx($pval_id_).'">';
                        if ($req)
                            echo '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'[REQ]['.IntVal($sprop).']" value="y">';
                        if ($fl)
                        {
                            echo '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'[FL]['.IntVal($sprop).']" value="y">';
                            
                            if (is_array($prop_fields__["VALUE"][$pval_id]) && isset($prop_fields__["VALUE"][$pval_id]["VALUE"]))
                                $flid = $prop_fields__["VALUE"][$pval_id]["VALUE"];
                            else
                                $flid = $prop_fields__["VALUE"][$pval_id];
                            
                            if (IntVal($flid))
                            {
                                echo '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'[FL_VID]['.IntVal($sprop).']" value="'.htmlspecialcharsbx($pval_id).'">';
                                echo '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'[FL_ID]['.IntVal($sprop).']" value="'.IntVal($flid).'">';
                            }
                        }
                        echo '</td></tr>';
                    }
                }
            }
            echo '</table>';
            
        }
        
        $strResult = ob_get_contents();
        ob_end_clean();
        
        if (is_array($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS"]) && is_array($PROP) && count($PROP) > 0)
        {
            foreach ($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS"] as $sprop)
            {
                $strResult = str_replace('PROP['.$sprop.'][]', 'PROP['.$sprop.']['.$sel_pvals[$sprop].']', $strResult);
                $strResult = str_replace('PROP['.$sprop.']', 'SPROP['.$sprop.']', $strResult);
                $strResult = str_replace('PROP_'.$sprop, 'SPROP_'.$sprop, $strResult);
                $strResult = str_replace('_prop_'.$sprop, '_sprop_'.$sprop, $strResult);
                $strResult = str_replace('_'.$sprop, '_s'.$sprop, $strResult);
                $strResult = str_replace('sel_s'.$sprop, 'sel_'.$sprop, $strResult);
                $strResult = str_replace('map_yandex__s'.$sprop, 'map_yandex__s'.$sprop.'_'.$value_id, $strResult);
                
                $strResult = str_replace($sprop.'_s'.$sprop.'_1', $sprop.'_'.$sprop.'_1', $strResult);
                $strResult = str_replace($sprop.'0_s'.$sprop.'0_1', $sprop.'0_'.$sprop.'0_1', $strResult);
                $strResult = str_replace($sprop.'00_s'.$sprop.'00_1', $sprop.'00_'.$sprop.'00_1', $strResult);
                $strResult = str_replace($sprop.'_s'.$sprop.'_s1', $sprop.'_'.$sprop.'_1', $strResult);
                $strResult = str_replace($sprop.'0_s'.$sprop.'0_s1', $sprop.'0_'.$sprop.'0_1', $strResult);
                $strResult = str_replace($sprop.'00_s'.$sprop.'00_s1', $sprop.'00_'.$sprop.'00_1', $strResult);
                $strResult = str_replace('_200_s1', '_200_1', $strResult);
                
                $strResult = str_replace(md5('PROP['.$sprop.']'), md5('SPROP['.$sprop.']'), $strResult);
            }
        }
            
        if ($GLOBALS["err_m"] && is_array($PROP) && count($PROP) > 0)
            $strResult = '<span style="color:#a00">'.$GLOBALS["err_m"].'</span>';

        return $strResult;
    }
    
    public static function GetPropertyFieldHtmlMulty($arProperty, $value, $strHTMLControlName) 
    {
        global $historyId, $bVarsFromForm, $bCopy, $PROP;
        $ID = IntVal($_REQUEST['ID']);
        
        $hides = array();
        $i = -1;
        $k = 0;
        
        if (is_array($GLOBALS["SCP_err_values"][$arProperty["ID"]]))
        {
            foreach ($GLOBALS["SCP_err_values"][$arProperty["ID"]] as $value_id => $value_arr)
            {
                if (substr($value_id, 0, 1) == "n")
                {
                    if (IntVal(substr($value_id, 1)) > $i)
                        $i = IntVal(substr($value_id, 1));
                    $k++;
                }
            }
        }
        
        if ($bCopy || $_REQUEST['action'] == 'copy')
        {
            $temp_values = array();
            foreach ($value as $value_id => $value_arr)
            {
                $i++;
                $k++;
                
                /*
                if (isset($prop_value['VALUE']['SUBPROPS']))
                {
                    if (is_array($prop_value['VALUE']['SUBPROPS']))
                    {
                        
                    }
                }
                */
                //unset($value_arr["VALUE"]["SUB_VAL_IDS"]);
                
                $temp_values["n".$i] = $value_arr;
            }
            
            $value = $temp_values;
            unset($temp_values);
        }
        
        while ($k < $arProperty["MULTIPLE_CNT"])
        {
            $i++;
            $k++;
            if (!isset($GLOBALS["SCP_err_values"][$arProperty["ID"]]["n".$i]))
            {
                $value["n".$i] = array("VALUE" => "", "DESCRIPTION"=>"");
                $hides["n".$i] = true;
            }
        }
        
        $strResult = "";
        
        foreach ($value as $value_id => $value_arr)
        {
            $subprops = false;
            
            ob_start();
            
            $sel_pvals = array();
            
            if (is_array($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS"]) && is_array($PROP) && count($PROP) > 0)
            {
                if ($hides[$value_id])
                {
                    echo '<div id="simai_complex_add_area_'.IntVal($arProperty["ID"]).'_'.htmlspecialcharsbx($value_id).'" style="display:none">';
                    
                    echo '<input type="hidden" id="simai_complex_add_hidden_'.IntVal($arProperty["ID"]).'_'.htmlspecialcharsbx($value_id).'" name="no_'.IntVal($arProperty["ID"]).'_'.htmlspecialcharsbx($value_id).'" value="y">';
                }
                
                echo '<div style="background:#e0e8ea; margin-bottom:10px; padding: 10px 0;"><table style="width:100%;">';
                foreach ($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS"] as $sp_num => $sprop)
                {
                    $subprops = true;
                    
                    $pval_id = false;
                    
                    if (isset($value_arr["VALUE"]["SUB_VAL_IDS"][$sprop]))
                        $pval_id = $value_arr["VALUE"]["SUB_VAL_IDS"][$sprop];
                    
                    if (isset($value_arr["VALUE"]["SUBPROPS"]))
                    {
                        if (!$pval_id && is_array($value_arr["VALUE"]["SUBPROPS"]))
                            $pval_id = $value_arr["VALUE"]["SUBPROPS"][$sprop];
                    }
                        
                    if (!$pval_id)
                        $pval_id = $value_id;
                        
                    $sel_pvals[$sprop] = "sel_".$value_id;
                    
                    foreach($PROP as $prop_code_ => $prop_fields_)
                    {
                        if ($prop_fields_["ID"] == $sprop && $prop_fields_["MULTIPLE"] == "Y" && !in_array($prop_fields_["USER_TYPE"], $GLOBALS["SIMAI_COMPLEXPROP_FORBIDDEN_UT"]))
                        {
                            $prop_fields__ = $prop_fields_;
                            
                            $prop_fields__["MULTIPLE"] = "N";
                            $prop_fields__["WITH_DESCRIPTION"] = "N";
                            
                            if (in_array($prop_fields__["PROPERTY_TYPE"], array("L","E","G")))
                                $pval_id_ = "sel_".$value_id;
                            else
                                $pval_id_ = $pval_id;
                            
                            if (!in_array($prop_fields__["PROPERTY_TYPE"], array("L","E","G")))
                                $pvalue = array($pval_id_ => $prop_fields__["VALUE"][$pval_id]);
                            elseif ($bCopy || $_REQUEST['action'] == 'copy')
                                $pvalue = array($value_id => $prop_fields__["VALUE"][$pval_id]);
                            elseif ($pval_id)
                                $pvalue = array($pval_id_ => $prop_fields__["VALUE"][$pval_id]);
                            else
                                $pvalue = array($value_id => false);
                            
                            if ($bCopy || $_REQUEST['action'] == 'copy')
                            {
                                if (!in_array($prop_fields__["PROPERTY_TYPE"], array("L","E","G")))
                                {
                                    $pval_id = $value_id;
                                    $pval_id_ = $value_id;
                                }
                            }
                            
                            $prop_fields__["VALUE"] = $pvalue;
                            $prop_fields__["~VALUE"] = $pvalue;
                            
                            $req = ($prop_fields__["IS_REQUIRED"] == "Y" || $arProperty["USER_TYPE_SETTINGS"]["SUBPROPS_REQ"][$sp_num]);
                            $fl = ($prop_fields__["PROPERTY_TYPE"] == "F");
                            
                            echo '<tr><td class="adm-detail-valign-top" style="text-align:right;">'.($req ? '<b>' : '').$prop_fields__["NAME"].':'.($req ? '</b>' : '').'</td><td style="padding:4px 5px;">';
                            self::ShowPropertyField('PROP['.$prop_fields__["ID"].']', $prop_fields__, $pvalue, (($historyId <= 0) && (!$bVarsFromForm) && ($ID <= 0)), false, 50000, $strHTMLControlName["FORM_NAME"], false /*$bCopy*/);
                            
                            echo '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'['.$value_id.'][VALUE][SUBPROPS]['.IntVal($sprop).']" value="'.htmlspecialcharsbx($pval_id_).'">';
                            if ($req)
                                echo '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'['.$value_id.'][VALUE][REQ]['.IntVal($sprop).']" value="y">';
                            if ($fl)
                            {
                                echo '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'['.$value_id.'][VALUE][FL]['.IntVal($sprop).']" value="y">';
                                
                                if (is_array($prop_fields__["VALUE"][$pval_id]) && isset($prop_fields__["VALUE"][$pval_id]["VALUE"]))
                                    $flid = $prop_fields__["VALUE"][$pval_id]["VALUE"];
                                else
                                    $flid = $prop_fields__["VALUE"][$pval_id];
                                
                                if (IntVal($flid) > 0)
                                {
                                    echo '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'['.$value_id.'][VALUE][FL_VID]['.IntVal($sprop).']" value="'.htmlspecialcharsbx($pval_id).'">';
                                    echo '<input type="hidden" name="'.$strHTMLControlName["VALUE"].'['.$value_id.'][VALUE][FL_ID]['.IntVal($sprop).']" value="'.IntVal($flid).'">';
                                }
                            }
                            echo '</td></tr>';
                        }
                    }
                }
                if (is_array($value_arr["VALUE"]) && IntVal($value_id))
                {
                    $ch = ($value_arr["VALUE"]["DEL"] == "y" ? " checked" : "");
                    echo '<tr><td colspan="2" style="padding:4px 20px;"><input type="checkbox" name="'.$strHTMLControlName["VALUE"].'['.$value_id.'][VALUE][DEL]" value="y" id="scp_del_'.$arProperty["ID"].'_'.$value_id.'"'.$ch.'> <label for="scp_del_'.$arProperty["ID"].'_'.$value_id.'">'.GetMessage('SMCP_DEL_CVAL').'</label></td>';
                }
                echo '</table></div>';
                
                if ($hides[$value_id])
                    echo '</div>';
                    
            }
            
            $strResult_ = ob_get_contents();
            ob_end_clean();
            
            if (is_array($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS"]) && is_array($PROP) && count($PROP) > 0)
            {
                foreach ($arProperty["USER_TYPE_SETTINGS"]["SUBPROPS"] as $sprop)
                {
                    $strResult_ = str_replace('PROP['.$sprop.'][]', 'PROP['.$sprop.']['.$sel_pvals[$sprop].']', $strResult_);
                    $strResult_ = str_replace('PROP['.$sprop.']', 'SPROP['.$sprop.']', $strResult_);
                    $strResult_ = str_replace('PROP_'.$sprop, 'SPROP_'.$sprop, $strResult_);
                    $strResult_ = str_replace('_prop_'.$sprop, '_sprop_'.$sprop, $strResult_);
                    $strResult_ = str_replace('_'.$sprop, '_s'.$sprop, $strResult_);
                    
                    $strResult_ = str_replace('sel_s'.$sprop, 'sel_'.$sprop, $strResult_);
                    $strResult_ = str_replace('map_yandex__s'.$sprop, 'map_yandex__s'.$sprop.'_'.$value_id, $strResult_);
                    $strResult_ = str_replace('simai_complex_add_area_s'.$sprop, 'simai_complex_add_area_'.$sprop, $strResult_);
                    $strResult_ = str_replace('simai_complex_add_hidden_s'.$sprop, 'simai_complex_add_hidden_'.$sprop, $strResult_);
                    $strResult_ = str_replace('name="no_s'.$sprop, 'name="no_'.$sprop, $strResult_);
                    $strResult_ = str_replace('scp_del_s'.$sprop, 'scp_del_s'.$sprop, $strResult_);
                    $strResult_ = str_replace($sprop.'_s'.$sprop.'_1', $sprop.'_'.$sprop.'_1', $strResult_);
                    $strResult_ = str_replace($sprop.'0_s'.$sprop.'0_1', $sprop.'0_'.$sprop.'0_1', $strResult_);
                    $strResult_ = str_replace($sprop.'00_s'.$sprop.'00_1', $sprop.'00_'.$sprop.'00_1', $strResult_);
                    $strResult_ = str_replace($sprop.'_s'.$sprop.'_s1', $sprop.'_'.$sprop.'_1', $strResult_);
                    $strResult_ = str_replace($sprop.'0_s'.$sprop.'0_s1', $sprop.'0_'.$sprop.'0_1', $strResult_);
                    $strResult_ = str_replace($sprop.'00_s'.$sprop.'00_s1', $sprop.'00_'.$sprop.'00_1', $strResult_);
                    $strResult_ = str_replace('_200_s1', '_200_1', $strResult_);
                    
                    $strResult_ = str_replace(md5('PROP['.$sprop.']'), md5('SPROP['.$sprop.']'), $strResult_);
                    
                    if ($bCopy || $_REQUEST['action'] == 'copy')
                    {
                        $strResult_ = str_replace('SPROP['.$sprop.'][n0]', 'SPROP['.$sprop.']['.$value_id.']', $strResult_);
                        $strResult_ = str_replace('__n0__', '__'.$value_id.'__', $strResult_);
                    }
                }
            }
            
            $strResult .= $strResult_;
        }
        if (count($hides) > 0  && is_array($PROP) && count($PROP) > 0)
        {
            $strResult .= '
            <script type="text/javascript">
            function SimaiComplexAddValue'.IntVal($arProperty["ID"]).'(what)
            {
                var hides_js = new Array(';
            $hides_js = array();
            foreach ($hides as $hid => $hval)
                $hides_js[$hid] = '"'.htmlspecialcharsbx($hid).'"';
            $hides_js = implode(",", $hides_js);
            $strResult .= $hides_js;
            $strResult .= ');
                var sc_show = false;
                var sc_bt_hide = 0;
                for (var i in hides_js) 
                {
                    var aid = hides_js[i];
                    var sc_area_id = "simai_complex_add_area_'.IntVal($arProperty["ID"]).'_" + aid;
                    var sc_inp_id = "simai_complex_add_hidden_'.IntVal($arProperty["ID"]).'_" + aid;
                    var sc_area = document.getElementById(sc_area_id);
                    var sc_inp = document.getElementById(sc_inp_id);
                    if (sc_area != null)
                    {
                        if (sc_area.style.display == "none")
                        {
                            sc_bt_hide++;
                            if (sc_show == false)
                            {
                                sc_area.style.display = "block";
                                sc_show = true;
                                
                                sc_inp.value="";
                            }
                        }
                    }
                }
                if (sc_bt_hide < 2)
                    what.style.display = "none";
            }
            </script>
            <input type="button" name="apply" class="adm-btn" value="'.GetMessage('SMCP_ADD_CVAL').'" onclick="SimaiComplexAddValue'.IntVal($arProperty["ID"]).'(this)">';
        }
        
        if ($GLOBALS["err_m"] && is_array($PROP) && count($PROP) > 0)
            $strResult = '<span style="color:#a00">'.$GLOBALS["err_m"].'</span>';
        
        return $strResult;
    }
    
    public static function GetPublicEditHTML($arProperty, $value, $strHTMLControlName)
    {
        return '';
    }
    
    public static function ConvertToDB($arProperty, $value)
    {
        if (is_array($value["VALUE"]))
            $value["VALUE"] = serialize($value["VALUE"]);
        else
            $value["VALUE"] = false;
        return $value;
    }
    
    public static function ConvertFromDB($arProperty, $value)
    {
        if (strlen($value["VALUE"]) > 2)
        {
            $value_us = unserialize($value["VALUE"]);
            
            if (!$GLOBALS['SCP_IN_LIST'])
            {
                $value["VALUE"] = array();
                if (is_array($value_us))
                {
                    foreach ($value_us as $el_id => $subprops)
                    {
                        $value["VALUE"]["ELEMENT"] = $el_id;
                        if ($el_id > 0)
                        {
                            $res = \CIBlockElement::GetById($el_id);
                            if ($obRes = $res->GetNextElement())
                            {
                                if (is_array($subprops))
                                {
                                    foreach ($subprops as $sprop => $pval_vd)
                                    {
                                        if (is_array($pval_vd))
                                            $pval_vd = current($pval_vd);
                                        
                                        if ($arProperty["MULTIPLE"] == "Y"/* && $pval_vd*/)
                                        {
                                            $arr = $obRes->GetProperty($sprop);
                                            $pid = (trim($arr["CODE"]) ? $arr["CODE"] : $arr["ID"]);
                                            if (!is_array($arr["PROPERTY_VALUE_ID"]))
                                            {
                                                $arr["VALUE"] = array("0" => $arr["VALUE"]);
                                                $arr["DESCRIPTION"] = array("0" => $arr["DESCRIPTION"]);
                                                $arr["VALUE_ENUM"] = array("0" => $arr["VALUE_ENUM"]);
                                                $arr["VALUE_XML_ID"] = array("0" => $arr["VALUE_XML_ID"]);
                                                $arr["VALUE_SORT"] = array("0" => $arr["VALUE_SORT"]);
                                                $arr["VALUE_ENUM_ID"] = array("0" => $arr["VALUE_ENUM_ID"]);
                                                $arr["~VALUE"] = array("0" => $arr["~VALUE"]);
                                                $arr["~DESCRIPTION"] = array("0" => $arr["~DESCRIPTION"]);
                                                $arr["PROPERTY_VALUE_ID"] = array("0" => $arr["PROPERTY_VALUE_ID"]);
                                            }
                                            foreach ($arr["VALUE"] as $val_id => $val)
                                            {
                                                $arr_ = $arr;
                                                $arr_["VALUE"] = $val;
                                                $arr_["DESCRIPTION"] = $arr["DESCRIPTION"][$val_id];
                                                $arr_["VALUE_ENUM"] = $arr["VALUE_ENUM"][$val_id];
                                                $arr_["VALUE_XML_ID"] = $arr["VALUE_XML_ID"][$val_id];
                                                $arr_["VALUE_SORT"] = $arr["VALUE_SORT"][$val_id];
                                                $arr_["VALUE_ENUM_ID"] = $arr["VALUE_ENUM_ID"][$val_id];
                                                $arr_["~VALUE"] = $arr["~VALUE"][$val_id];
                                                $arr_["~DESCRIPTION"] = $arr["~DESCRIPTION"][$val_id];
                                                $arr_["PROPERTY_VALUE_ID"] = $arr["PROPERTY_VALUE_ID"][$val_id];
                                                if ($arr_["PROPERTY_TYPE"] == "F")
                                                {
                                                    if ($arr_["VALUE"] == $pval_vd)
                                                    {
                                                        $value["VALUE"]["SUB_VAL_IDS"][$sprop] = $arr_["PROPERTY_VALUE_ID"];
                                                        $value["VALUE"]["FLEG"][$sprop] = $arr_["VALUE"];
                                                        $value["VALUE"]["SUB_VALUES"][$pid] = $arr_;
                                                    }
                                                }
                                                elseif ($arr_["PROPERTY_TYPE"] == "L")
                                                {
                                                    if ($arr_["VALUE_ENUM_ID"] == $pval_vd)
                                                    {
                                                        $value["VALUE"]["SUB_VAL_IDS"][$sprop] = $arr_["PROPERTY_VALUE_ID"];
                                                        $value["VALUE"]["FLEG"][$sprop] = $arr_["VALUE_ENUM_ID"];
                                                        $value["VALUE"]["SUB_VALUES"][$pid] = $arr_;
                                                    }
                                                }
                                                elseif (in_array($arr_["PROPERTY_TYPE"], array("E","G")))
                                                {
                                                    if ($arr_["VALUE"] == $pval_vd)
                                                    {
                                                        $value["VALUE"]["SUB_VAL_IDS"][$sprop] = $arr_["PROPERTY_VALUE_ID"];
                                                        $value["VALUE"]["FLEG"][$sprop] = $arr_["VALUE"];
                                                        $value["VALUE"]["SUB_VALUES"][$pid] = $arr_;
                                                    }
                                                }
                                                elseif ($arr_["DESCRIPTION"] == $pval_vd)
                                                {
                                                    $value["VALUE"]["SUB_VAL_IDS"][$sprop] = $arr_["PROPERTY_VALUE_ID"];
                                                    $value["VALUE"]["SUB_VALUES"][$pid] = $arr_;
                                                }
                                            }
                                        }
                                        elseif ($arProperty["MULTIPLE"] != "Y")
                                        {
                                            $arr = $obRes->GetProperty($sprop);
                                            $pid = (trim($arr["CODE"]) ? $arr["CODE"] : $arr["ID"]);
                                            if (is_array($arr["PROPERTY_VALUE_ID"]))
                                            {
                                                $arr["VALUE"] = current($arr["VALUE"]);
                                                $arr["DESCRIPTION"] = current($arr["DESCRIPTION"]);
                                                //$arr["VALUE_ENUM"] = current($arr["VALUE_ENUM"]);
                                                //$arr["VALUE_XML_ID"] = current($arr["VALUE_XML_ID"]);
                                                //$arr["VALUE_SORT"] = current($arr["VALUE_SORT"]);
                                                //$arr["VALUE_ENUM_ID"] = current($arr["VALUE_ENUM_ID"]);
                                                $arr["~VALUE"] = current($arr["~VALUE"]);
                                                $arr["~DESCRIPTION"] = current($arr["~DESCRIPTION"]);
                                                $arr["PROPERTY_VALUE_ID"] = current($arr["PROPERTY_VALUE_ID"]);
                                            }
                                            $value["VALUE"]["SUB_VAL_IDS"][$sprop] = $arr["PROPERTY_VALUE_ID"];
                                            $value["VALUE"]["SUB_VALUES"][$pid] = $arr;
                                            if ($arr["PROPERTY_TYPE"] == "F")
                                                $value["VALUE"]["FLEG"][$sprop] = $arr["VALUE"];
                                            elseif ($arr["PROPERTY_TYPE"] == "L")
                                                $value["VALUE"]["FLEG"][$sprop] = $arr["VALUE_ENUM_ID"];
                                            elseif (in_array($arr["PROPERTY_TYPE"], array("E","G")))
                                                $value["VALUE"]["FLEG"][$sprop] = $arr["VALUE"];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                unset($value["DESCRIPTION"]);
            }
            else
            {
                $value["VALUE"] = $value_us;
            }
        }
        
        return $value;
    }
    
    public static function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
    {
        /*
        $return = array();
        if (is_array($value["VALUE"]))
        {
            if ($value["VALUE"]["ELEMENT"] > 0 && isset($value["VALUE"]["SUB_VALUES"]))
            {
                $return = array();
                $res = \CIBlockElement::GetByID($value["VALUE"]["ELEMENT"]);
                if($arE = $res->fetch())
                {
                    $return["ELEMENT"] = $value["VALUE"]["ELEMENT"];
                    if (is_array($value["VALUE"]["SUB_VALUES"]))
                    {
                        foreach ($value["VALUE"]["SUB_VALUES"] as $pid => $parr)
                        {
                            $p_mult = $parr["MULTIPLE"];
                            $parr["MULTIPLE"] = "N";
                            $return["SUB_VALUES"][$pid] = \CIBlockFormatProperties::GetDisplayValue($arE, $parr, "simai_complex");
                            $return["SUB_VALUES"][$pid]["MULTIPLE"] = $p_mult;
                        }
                    }
                }
            }
            else
            {
                $return = array();
            }
        }
        else
        {
            $return = array();
        }
        */
        
        $return = '';
        if (is_array($value["VALUE"]))
        {
            if ($value["VALUE"]["ELEMENT"] > 0 && isset($value["VALUE"]["SUB_VALUES"]))
            {
                $res = \CIBlockElement::GetByID($value["VALUE"]["ELEMENT"]);
                if($arE = $res->fetch())
                {
                    if (is_array($value["VALUE"]["SUB_VALUES"]))
                    {
                        $return .= '<table>';
                        foreach ($value["VALUE"]["SUB_VALUES"] as $pid => $parr)
                        {
                            $parr["MULTIPLE"] = "N";
                            $sub_val = \CIBlockFormatProperties::GetDisplayValue($arE, $parr, "simai_complex");
                            if (isset($sub_val['DISPLAY_VALUE']))
                                $sub_val = $sub_val['DISPLAY_VALUE'];
                            else
                                $sub_val = false;
                            if (is_array($sub_val))
                                $sub_val = implode(' / ', $sub_val);
                            
                            $return .= '<tr><td>' . $parr['NAME'] . ':</td><td>' . $sub_val . '</td></tr>';
                        }
                        $return .= '</table>';
                    }
                }
            }
        }
        return $return;
    }
    
    public static function GetSearchContent($arProperty, $value, $strHTMLControlName)
    {
        return '';
    }
    
    public static function GetAdminFilterHTML($arProperty, $strHTMLControlName)
    {
        return '';
    }
    
    public static function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        return GetMessage('SMCP_COMPLEX_PROP_LIST');
    }
    
    public static function GetPublicFilterHTML($arProperty, $strHTMLControlName)
    {
        return '';
    }
} ?>