<?
if(!$USER->IsAdmin())
    return;

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

$module_id = "epir.oldbrowser";
// для выбора браузеров
$ie_option = array('ie6','ie7','ie8');
$ie_val = array('Y','Y','Y');
$ie_option_descr = array(GetMessage("OBM_TEXT_MSIE6"),GetMessage("OBM_TEXT_MSIE7"),GetMessage("OBM_TEXT_MSIE8"));

$option_code = array("active_oldbrowser","string_1_oldbrowser","string_2_oldbrowser","string_3_oldbrowser","include_jquery");
$option_val = array("Y",GetMessage("OBM_TEXT_VAL_1"),GetMessage("OBM_TEXT_VAL_2"),GetMessage("OBM_TEXT_VAL_3"),"N");
$option_descr = array(GetMessage("OBM_ACTIVE"),GetMessage("OBM_TEXT_1"),GetMessage("OBM_TEXT_2"),GetMessage("OBM_TEXT_3"),GetMessage("OBM_JQUERY"));

$aTabs = array(
    array(
        "DIV" => "edit1",
        "TAB" => GetMessage("MAIN_TAB_SET"),
        "ICON" => "ib_settings",
        "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")
    ),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);

if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid())
{



    if(strlen($RestoreDefaults)>0)
    {
        COption::RemoveOption($module_id);

        COption::SetOptionString($module_id, $option_code[0], $option_val[0], $option_descr[0]);

        COption::SetOptionString($module_id, $option_code[1], $option_val[1], $option_descr[1]);
        COption::SetOptionString($module_id, $option_code[2], $option_val[2], $option_descr[2]);
        COption::SetOptionString($module_id, $option_code[3], $option_val[3], $option_descr[3]);
        COption::SetOptionString($module_id, $option_code[4], $option_val[4], $option_descr[4]);
        // для браузеров
        COption::SetOptionString($module_id, $ie_option[0], $ie_val[0],$ie_option_descr[0]);
        COption::SetOptionString($module_id, $ie_option[1], $ie_val[1],$ie_option_descr[1]);
        COption::SetOptionString($module_id, $ie_option[2], $ie_val[2],$ie_option_descr[2]);
    }
    else
    {

        ////если галка стоит
        if($_REQUEST[$option_code[0]] == "Y" ){
            RegisterModuleDependences("main", "OnEpilog", $module_id, "oldbrowser_class", "oldbrowser_addScript");
            COption::SetOptionString($module_id, $option_code[0], 'Y');
        }else{
            /// усли нет галки
            UnRegisterModuleDependences("main", "OnEpilog", $module_id, "oldbrowser_class", "oldbrowser_addScript");
        }

        $val = $_POST[htmlspecialchars($option_code[0])];
        $val_1 = $_POST[htmlspecialchars($option_code[1])];
        $val_2 = $_POST[htmlspecialchars($option_code[2])];
        $val_3 = $_POST[htmlspecialchars($option_code[3])];
        $jquery = $_POST[htmlspecialchars($option_code[4])];
        // добавляем возможность выбирать браузеры
        $ie6 = $_POST[htmlspecialchars($ie_option[0])];
        $ie7 = $_POST[htmlspecialchars($ie_option[1])];
        $ie8 = $_POST[htmlspecialchars($ie_option[2])];

        COption::SetOptionString($module_id, $option_code[0], $val, $option_descr[0]);

        COption::SetOptionString($module_id, $option_code[1], $val_1, $option_descr[1]);
        COption::SetOptionString($module_id, $option_code[2], $val_2, $option_descr[2]);
        COption::SetOptionString($module_id, $option_code[3], $val_3, $option_descr[3]);
        COption::SetOptionString($module_id, $option_code[4], $jquery, $option_descr[4]);
        // для браузеров
        COption::SetOptionString($module_id, $ie_option[0], $ie6, $ie_val[0]);
        COption::SetOptionString($module_id, $ie_option[1], $ie7, $ie_val[1]);
        COption::SetOptionString($module_id, $ie_option[2], $ie8, $ie_val[2]);


    }
    if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
        LocalRedirect($_REQUEST["back_url_settings"]);
    else
        LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
}
$tabControl->Begin();
?>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?echo LANGUAGE_ID?>">
    <?$tabControl->BeginNextTab();?>
    <?$val = COption::GetOptionString($module_id, $option_code[0], $option_val[0]);?>
    <tr class="heading">
        <td valign="top" width="30%"><?=GetMessage("OBM_ACTIVE")?></td>
        <td valign="top" width="70%">
            <input type="checkbox" id="<?=htmlspecialchars($option_code[0])?>" name="<?=htmlspecialchars($option_code[0])?>" test="<?=$val?>" value="Y"<?if($val =="Y") echo ' checked="checked"';?> />
        </td>
    </tr>
    <?$jquery = COption::GetOptionString($module_id, $option_code[4], $option_val[4]);?>
    <tr class="heading">
        <td valign="top" width="30%"><?=GetMessage("OBM_JQUERY")?></td>
        <td valign="top" width="70%">
            <input type="checkbox" id="<?=htmlspecialchars($option_code[4])?>" name="<?=htmlspecialchars($option_code[4])?>" value="Y"<?if($jquery=="Y") echo ' checked="checked"';?> />
        </td>
    </tr>
    <tr class="heading">
        <td valign="top" width="30%"><?=GetMessage("OBM_TEXT_1")?></td>
        <td valign="top" width="70%">
            <input type="text" size="90" id="<?=htmlspecialchars($option_code[1])?>" name="<?=htmlspecialchars($option_code[1])?>" value="<?=COption::GetOptionString($module_id, $option_code[1],$option_val[1]);?>" />
        </td>
    </tr>
    <tr class="heading">
        <td valign="top" width="30%"><?=GetMessage("OBM_TEXT_2")?></td>
        <td valign="top" width="70%">
            <input type="text" size="90" id="<?=htmlspecialchars($option_code[2])?>" name="<?=htmlspecialchars($option_code[2])?>" value="<?=COption::GetOptionString($module_id, $option_code[2],$option_val[2]);?>" />
        </td>
    </tr>
    <tr class="heading">
        <td valign="top" width="30%"><?=GetMessage("OBM_TEXT_3")?></td>
        <td valign="top" width="70%">
            <input type="text" size="90" id="<?=htmlspecialchars($option_code[3])?>" name="<?=htmlspecialchars($option_code[3])?>" value="<?=COption::GetOptionString($module_id, $option_code[3],$option_val[3]);?>" />
        </td>
    </tr>
    <!--browsers-->
    <?/*$ie6 = COption::GetOptionString($module_id, $ie_option[0], $ie_val[0]);?>
    <tr class="heading">
        <td valign="top" width="30%"><?=GetMessage("OBM_TEXT_MSIE6")?></td>
        <td valign="top" width="70%">
            <input type="checkbox" id="<?=htmlspecialchars($ie_option[0])?>" name="<?=htmlspecialchars($ie_option[0])?>" value="Y"<?if($ie6=="Y") echo ' checked="checked"';?> />
        </td>
    </tr>
    <?*/$ie7 = COption::GetOptionString($module_id, $ie_option[1], $ie_val[1]);?>
    <tr class="heading">
        <td valign="top" width="30%"><?=GetMessage("OBM_TEXT_MSIE7")?></td>
        <td valign="top" width="70%">
            <input type="checkbox" id="<?=htmlspecialchars($ie_option[1])?>" name="<?=htmlspecialchars($ie_option[1])?>" value="Y"<?if($ie7=="Y") echo ' checked="checked"';?> />
        </td>
    </tr>
    <?$ie8 = COption::GetOptionString($module_id, $ie_option[2], $ie_val[2]);?>
    <tr class="heading">
        <td valign="top" width="30%"><?=GetMessage("OBM_TEXT_MSIE8")?></td>
        <td valign="top" width="70%">
            <input type="checkbox" id="<?=htmlspecialchars($ie_option[2])?>" name="<?=htmlspecialchars($ie_option[2])?>" value="Y"<?if($ie8=="Y") echo ' checked="checked"';?> />
        </td>
    </tr>
    <!--end browsers-->
    <?$tabControl->Buttons();?>

    <input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>" class="adm-btn-save">
    <input type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
    <input type="submit" name="RestoreDefaults" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="return confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
    <?=bitrix_sessid_post();?>
    <?$tabControl->End();?>
</form>
