<?php
//var_dump("Z");exit;
//require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); // первый общий пролог
$module_id = S2uRedirects::MODULE_ID;
global $MESS;
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/options.php');
IncludeModuleLangFile(__FILE__);
include_once($GLOBALS['DOCUMENT_ROOT'] . BX_ROOT . '/modules/' . $module_id . '/include.php');
$CAT_RIGHT = $MODULE_RIGHT = $APPLICATION->GetUserRight($module_id);

//var_dump("Z");
//var_dump($MODULE_RIGHT);exit;
//AddMessage2Log(var_export($MODULE_RIGHT, true));

if ($MODULE_RIGHT < "R")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));


if ($REQUEST_METHOD == "GET" && strlen($RestoreDefaults) > 0 && $MODULE_RIGHT == "W") {
    COption::RemoveOption($module_id);
    $z = CGroup::GetList($v1 = "id", $v2 = "asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
    while ($zr = $z->Fetch())
        $APPLICATION->DelGroupRight($module_id, array($zr["ID"]));
}


function ShowParamsHTMLByArray($arParams)
{
    global $module_id;
	foreach($arParams as $Option)
	{
		__AdmSettingsDrawRow($module_id, $Option);
	}
}


echo S2uRedirects::getLicenseRenewalBanner();


// Выбрать все инфоблоки
$arIblocks = array();
$arIblocksTypes = array();
CModule::IncludeModule("iblock");
$db_iblock_type = CIBlockType::GetList();
while($ar_iblock_type = $db_iblock_type->Fetch())
{
   if($arIBType = CIBlockType::GetByIDLang($ar_iblock_type["ID"], LANG))
   {
      $arIblocksTypes[$ar_iblock_type["ID"]] = $arIBType["NAME"];
   }   
}
$res = CIBlock::GetList(Array("iblock_type"=>"asc"));
while($ar_res = $res->Fetch()) {
    $arIblocks[$ar_res['ID']] = "[".$arIblocksTypes[$ar_res['IBLOCK_TYPE_ID']]."] ".$ar_res['NAME'];
    //var_dump($ar_res['NAME']); // $ar_res['NAME'].': '.$ar_res['ELEMENT_CNT'];
    
}

if (!COption::GetOptionString($module_id, 'INCLUDE_JQUERY'))
    COption::SetOptionString($module_id, 'INCLUDE_JQUERY', 'N');
if (!COption::GetOptionString($module_id, 'REDIRECTS_IS_ACTIVE'))
    COption::SetOptionString($module_id, 'REDIRECTS_IS_ACTIVE', 'Y');
if (!COption::GetOptionString($module_id, '404_IS_ACTIVE'))
    COption::SetOptionString($module_id, '404_IS_ACTIVE', 'Y');
if (!COption::GetOptionString($module_id, '404_LIMIT'))
    COption::SetOptionString($module_id, '404_LIMIT', '0');
if (!COption::GetOptionString($module_id, 'VALIDATE_URL_BY_RFC2396'))
    COption::SetOptionString($module_id, 'VALIDATE_URL_BY_RFC2396', 'Y');
if (!COption::GetOptionString($module_id, 'REPAIR_CONFLICTS'))
    COption::SetOptionString($module_id, 'REPAIR_CONFLICTS', 'N');
if (!COption::GetOptionString($module_id, 'BITRIX_EXTENTION'))
    COption::SetOptionString($module_id, 'BITRIX_EXTENTION', 'N');

$message = null;

$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage('S2U_MAIN'), "ICON" => "", "TITLE" => GetMessage("S2U_MAIN_TITLE")),
);




$rsSites = CSite::GetList($by = "id", $order = "asc");
$k = 1;
while ($arSite = $rsSites->Fetch()) {
    $aTabs[$k]['DIV'] = 'edit_' . $arSite['LID'];
    $aTabs[$k]['TAB'] = $arSite['LID'];
    $aTabs[$k]['ICON'] = '';
    $aTabs[$k]['TITLE'] = $arSite['NAME'];
    $sites[$arSite['LID']] = $arSite['NAME'];
    $k++;
}

$tabControl = new CAdminTabControl("tabControl", $aTabs);

$arAllOptions[] = Array(
    array("INCLUDE_JQUERY", GetMessage("S2U_JQUERY") . " ", array("checkbox")),
    array("REDIRECTS_IS_ACTIVE", GetMessage("S2U_REDIRECTS_IS_ACTIVE") . " ", array("checkbox")),
    array("404_IS_ACTIVE", GetMessage("S2U_404_IS_ACTIVE") . " ", array("checkbox")),
    array("VALIDATE_URL_BY_RFC2396", GetMessage("S2U_VALIDATE_URL_BY_RFC2396") . " ", array("checkbox")),
    array("404_LIMIT", GetMessage("S2U_404_LIMIT") . " ", array("select", array(
                '0' => GetMessage('S2U_404_LIMIT_ALL'),
                '1000' => '1000 ' . GetMessage('S2U_404_LIMIT_LAST_N'),
                '2000' => '2000 ' . GetMessage('S2U_404_LIMIT_LAST_N'),
                '3000' => '3000 ' . GetMessage('S2U_404_LIMIT_LAST_N'),
                '4000' => '4000 ' . GetMessage('S2U_404_LIMIT_LAST_N'),
                '5000' => '5000 ' . GetMessage('S2U_404_LIMIT_LAST_N'),
            )),),
    array("REPAIR_CONFLICTS", GetMessage("REPAIR_CONFLICTS") . " ", array("checkbox")),
    array("COMPOSITE_ACTIVE", GetMessage("S2U_COMPOSITE_IS_ACTIVE") . " ", array("checkbox")),
    array("CACHE_REGEXP_RULES", GetMessage("S2U_CACHE_REGEXP_RULES") . " ", array("checkbox")),
    array("LEVELUP_REDIRECT_IF_404", GetMessage("S2U_LEVELUP_REDIRECT_IF_404") . " ", array("checkbox")),
    array("BITRIX_EXTENTION", GetMessage("S2U_BITRIX_EXTENTION") . " ", array("checkbox")),
    array("USE_CASE_SENSITIVITY", GetMessage("S2U_USE_CASE_SENSITIVITY") . " ", array("checkbox")),
    
);

foreach ($sites as $key => $value) {
    $arAllOptions[$key][] = Array("main_mirror_" . $key, GetMessage("S2U_MAIN_MIRROR"), Array("text", 25));
	$arAllOptions[$key][] = Array("slash_add_" . $key, GetMessage("S2U_SLASH_ADD"), array("checkbox"));
	$arAllOptions[$key][] = array("REDIR_WITHOUT_INDEX_". $key, GetMessage("atl_autoredirects_index"), array("checkbox"));
	$arAllOptions[$key][] = array("REDIR_TO_LOWER_". $key, GetMessage("atl_autoredirects_lower"), array("checkbox"));
	//$arAllOptions[$key][] = Array("remember_changing_code_" . $key, GetMessage("S2U_REDIRECT_RULE_CHANGING_CODE"), array("checkbox"));
	//$arAllOptions[$key][] = Array("id_to_code_" . $key, GetMessage("S2U_REDIRECT_ID_TO_CODE"), array("checkbox"));
	//$arAllOptions[$key][] = Array("remember_changing_section_" . $key, GetMessage("S2U_REDIRECT_SECTION_CHANGE"), array("checkbox"));
	//$arAllOptions[$key][] = Array("deactivation_" . $key, GetMessage("S2U_REDIRECT_DEACTIVATION"), array("checkbox"));
	
}



if ($REQUEST_METHOD == 'POST' && isset($_REQUEST['Update']) && check_bitrix_sessid()) {
    foreach ($arAllOptions as $arOpt) {
        foreach ($arOpt as $ar) {
			if(substr($ar[0], 0, 12) == "main_mirror_") {
				$val = ${$ar[0]};
				if(preg_match("/^(https?:\/\/)([\da-z\.-]+)\.([a-z\.]{2,6})$/", $val) || $val==""){
					$f = COption::SetOptionString($module_id, $ar[0], $val);					
				} else {
					$mirror_error = new CAdminMessage(GetMessage("S2U_MIRROR_ERROR"));
				}
			} else {
				 $val = ${$ar[0]};
				if ($ar[2][0] == 'checkbox' && $val != 'Y')
					$val = 'N';
				$f = COption::SetOptionString($module_id, $ar[0], $val);
			}
           
        }
    }
    //__AdmSettingsSaveOption("main", "atl_autoredirects_iblocks");
    //var_dump($_POST);exit;
    COption::SetOptionString($module_id, "autoredirects_iblocks", implode(',', $_POST['autoredirects_iblocks']));
    
    if(!$_POST['autoredirects_change_detail_url']) $_POST['autoredirects_change_detail_url'] = "N";
    COption::SetOptionString($module_id, "autoredirects_change_detail_url", $_POST['autoredirects_change_detail_url']);
    
    if(!$_POST['autoredirects_change_section_url']) $_POST['autoredirects_change_section_url'] = "N";
    COption::SetOptionString($module_id, "autoredirects_change_section_url", $_POST['autoredirects_change_section_url']);
    
    if(!$_POST['autoredirects_element_deactivate']) $_POST['autoredirects_element_deactivate'] = "N";
    COption::SetOptionString($module_id, "autoredirects_element_deactivate", $_POST['autoredirects_element_deactivate']);

    if(!$_POST['autoredirects_element_delete']) $_POST['autoredirects_element_delete'] = "N";
    COption::SetOptionString($module_id, "autoredirects_element_delete", $_POST['autoredirects_element_delete']);

    
}

$arGroups = array(
    'REFERENCE' => array(),
    'REFERENCE_ID' => array(),
);
$resGroups = CGroup::GetList($by = "c_sort", $order = "asc");
while ($arr = $resGroups->Fetch()) {
    $arGroups['REFERENCE'][] = '[' . $arr['ID'] . '] ' . $arr['NAME'];
    $arGroups['REFERENCE_ID'][] = $arr['ID'];
}
//arSelectedGroups = explode(',', COption::GetOptionString('s2u_step2use', 's2u_groups_to_send',''));

if (count($arMsg)) {
    if ($e = $APPLICATION->GetException())
        $message = new CAdminMessage(GetMessage("S2U_OPTIONS_ERROR"), $e);
    $bVarsFromForm = true;
}

if ($message)
    echo $message->Show();

if ($mirror_error)
    echo $mirror_error->Show();

$tabControl->Begin();
?>
<style>
    .field-name {
        width: 200px;
    }
</style>
<form method="post" action="<? echo $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($mid) ?>&amp;lang=<?= LANGUAGE_ID ?>">
<?
// ???????? ?????????
$tabControl->BeginNextTab();

if (is_array($arAllOptions)) {
    foreach ($arAllOptions[0] as $Option) {
        $val = COption::GetOptionString($module_id, $Option[0]);
        $type = $Option[2];
        if ($type[0] == 'checkbox')
            $label = '<label for="' . htmlspecialcharsbx($Option[0]) . '">' . $Option[1] . '</label>';
        else
            $label = $Option[1];
        if ($type[0] == 'checkbox'){

             $input = '<input type="checkbox" name="' . htmlspecialcharsbx($Option[0]) . '" id="' . htmlspecialcharsbx($Option[0]) . '" value="Y"' . ($val == 'Y' ? ' checked' : '') . '>';
        }
        elseif ($type[0] == 'text')
            $input = '<input type="text" size="' . $type[1] . '" maxlength="255" value="' . htmlspecialcharsbx($val) . '" name="' . htmlspecialcharsbx($Option[0]) . '">';
        elseif ($type[0] == 'textarea')
            $input = '<textarea rows="' . $type[1] . '" cols="' . $type[2] . '" name="' . htmlspecialcharsbx($Option[0]) . '">' . htmlspecialcharsbx($val) . '</textarea>';
        elseif ($type[0] == 'select') {
            $input = '<select name="' . htmlspecialcharsbx($Option[0]) . '">';
            foreach ($type[1] as $key => $valOpt) {
                $input .= '<option value="' . htmlspecialcharsbx($key) . '" ' . (($key == htmlspecialcharsbx($val)) ? 'selected="selected"' : '') . '>' . htmlspecialcharsbx($valOpt) . '</option>';
            }
            $input .= '</select>';
        }
        $hint = "";
        if($Option["0"] == "REPAIR_CONFLICTS"){
            $hint = '<span id="repair_conflicts"></span>';
        };
        echo '<tr>					
						<td valign="top">'. $hint . $label . '						
						</td>
						<td valign="top" nowrap>
							' . $input;
        if($Option["0"] == 'COMPOSITE_ACTIVE'){
            echo '<br><div class="adm-info-message">' . GetMessage('COMPOSITE_ACTIVE_WARNING')
                . '</div>';
        }
        echo '</td>
					</tr>';

    }
}

?>
	<tr class="heading">
		<td colspan="2"><b><?echo GetMessage("atl_autoredirect_title")?></b></td>
	</tr>
<?
//var_dump(COption::GetOptionString($module_id, "atl_autoredirects_iblocks", ""));exit;
// array(array(1 =>"zzz"), "adasd", 2=>"www"))
//var_dump(explode(",", COption::GetOptionString($module_id, "atl_autoredirects_iblocks", "")));exit;
ShowParamsHTMLByArray(array(
array(
    "autoredirects_iblocks", 
    GetMessage("atl_autoredirects_iblocks"), 
    '', 
    Array("multiselectbox", $arIblocks)
),
array(
    "autoredirects_change_detail_url", 
    GetMessage("atl_autoredirects_detail_url_change"), 
    "N",
    array("checkbox")
),
array(
    "autoredirects_change_section_url", 
    GetMessage("atl_autoredirects_section_url_change"), 
    "N",
    array("checkbox")
),
array(
    "autoredirects_element_deactivate", 
    GetMessage("atl_autoredirects_element_deactivate"), 
    "N",
    array("checkbox")
),
array(
    "autoredirects_element_delete", 
    GetMessage("atl_autoredirects_element_delete"), 
    "N",
    array("checkbox")
)

));



// ????? ???????
$tabControl->BeginNextTab();
?>
    <? //require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>


    <?php
    foreach ($sites as $key => $value) {

        foreach ($arAllOptions[$key] as $Option) {
			//???????? 
            $val = COption::GetOptionString($module_id, $Option[0]);
			//??? ????
            $type = $Option[2];
            if ($type[0] == 'checkbox')
                $label = '<label for="' . htmlspecialcharsbx($Option[0]) . '">' . $Option[1] . '</label>';
            else
                $label = '<label>' . $Option[1] . '</label>';

            if ($type[0] == 'checkbox')
                $input = '<input type="checkbox" name="' . htmlspecialcharsbx($Option[0]) . '" id="' . htmlspecialcharsbx($Option[0]) . '" value="Y"' . ($val == 'Y' ? ' checked' : '') . '>';
            elseif ($type[0] == 'text')
                $input = '<input type="text" size="' . $type[1] . '" maxlength="255" value="' . htmlspecialcharsbx($val) . '" name="' . htmlspecialcharsbx($Option[0]) . '">';
            elseif ($type[0] == 'textarea')
                $input = '<textarea rows="' . $type[1] . '" cols="' . $type[2] . '" name="' . htmlspecialcharsbx($Option[0]) . '">' . htmlspecialcharsbx($val) . '</textarea>';
            elseif ($type[0] == 'select') {
                $input = '<select name="' . htmlspecialcharsbx($Option[0]) . '">';
                foreach ($type[1] as $key => $valOpt) {
                    $input .= '<option value="' . htmlspecialcharsbx($key) . '" ' . (($key == htmlspecialcharsbx($val)) ? 'selected="selected"' : '') . '>' . htmlspecialcharsbx($valOpt) . '</option>';
                }
                $input .= '</select>';
            }
            $hint = "";
            $pos = strpos($Option["0"], "main_mirror_");
            if($pos !== false){
                $hint = '<span id="hint_mirror_'. $key. '"></span>';
            };
            echo '<tr>					
						<td valign="top">'.$hint . $label . '							
						</td>
						<td valign="top" nowrap>
							' . $input . '
						</td>
					</tr>';
        }
        $tabControl->BeginNextTab();
    }
    ?>


    <? $tabControl->Buttons(); ?>
    <script language="JavaScript">
        function RestoreDefaults()
        {
            if (confirm('<? echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING")) ?>'))
                window.location = "<? echo $APPLICATION->GetCurPage() ?>?RestoreDefaults=Y&lang=<? echo LANG ?>&mid=<? echo urlencode($mid) ?>";
        }
        BX.hint_replace(BX('repair_conflicts'), '<?=GetMessage("HELP_CONFLICTS")?>');
    </script>
	<?foreach ($sites as $key => $value) {?>
		<script language="JavaScript">
			BX.hint_replace(BX('hint_mirror_<?=$key?>'), '<?=GetMessage("S2U_VALID_MIRROR_LIST")?>');
		</script>
    <?}?>
    <input type="submit" <? if ($MODULE_RIGHT < "W") echo "disabled" ?> name="Update" value="<? echo GetMessage("MAIN_SAVE") ?>">
    <input type="hidden" name="Update" value="Y">
    <input type="reset" name="reset" value="<? echo GetMessage("MAIN_RESET") ?>">
    <input type="button" <? if ($MODULE_RIGHT < "W") echo "disabled" ?> title="<? echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS") ?>" OnClick="RestoreDefaults();" value="<? echo GetMessage("MAIN_RESTORE_DEFAULTS") ?>">    
<?= bitrix_sessid_post(); ?>
<? $tabControl->End(); ?>
</form>
