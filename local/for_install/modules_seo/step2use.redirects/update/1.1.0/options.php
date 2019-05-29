<?php
$module_id = S2uRedirects::MODULE_ID;
global $MESS;
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/options.php');
IncludeModuleLangFile(__FILE__);
include_once($GLOBALS['DOCUMENT_ROOT'] . BX_ROOT . '/modules/' . $module_id . '/include.php');
$CAT_RIGHT = $MODULE_RIGHT = $APPLICATION->GetGroupRight($sModuleId);

if ($MODULE_RIGHT < "R")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));


if ($REQUEST_METHOD == "GET" && strlen($RestoreDefaults) > 0 && $MODULE_RIGHT == "W") {
    COption::RemoveOption($module_id);
    $z = CGroup::GetList($v1 = "id", $v2 = "asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
    while ($zr = $z->Fetch())
        $APPLICATION->DelGroupRight($module_id, array($zr["ID"]));
}

if (!COption::GetOptionString($module_id, 'REDIRECTS_IS_ACTIVE'))
    COption::SetOptionString($module_id, 'REDIRECTS_IS_ACTIVE', 'Y');
if (!COption::GetOptionString($module_id, '404_IS_ACTIVE'))
    COption::SetOptionString($module_id, '404_IS_ACTIVE', 'Y');
if (!COption::GetOptionString($module_id, '404_LIMIT'))
    COption::SetOptionString($module_id, '404_LIMIT', '0');
if (!COption::GetOptionString($module_id, 'VALIDATE_URL_BY_RFC2396'))
    COption::SetOptionString($module_id, 'VALIDATE_URL_BY_RFC2396', 'Y');

$message = null;

$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage('S2U_MAIN'), "ICON" => "", "TITLE" => GetMessage("S2U_MAIN_TITLE")),
);




$rsSites = CSite::GetList($by = "id", $order = "asc");
$k = 1;
while ($arSite = $rsSites->Fetch()) {
    $aTabs[$k]['DIV'] = 'edit' . $arSite['NAME'];
    $aTabs[$k]['TAB'] = $arSite['LID'];
    $aTabs[$k]['ICON'] = '';
    $aTabs[$k]['TITLE'] = $arSite['NAME'];
    $sites[$arSite['LID']] = $arSite['NAME'];
    $k++;
}

$tabControl = new CAdminTabControl("tabControl", $aTabs);

$arAllOptions[] = Array(
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
);

foreach ($sites as $key => $value) {
    $arAllOptions[$key][] = Array("main_mirror_" . $key, GetMessage("S2U_MAIN_MIRROR"), Array("text", 25));
	$arAllOptions[$key][] = Array("slash_add_" . $key, GetMessage("S2U_SLASH_ADD"), array("checkbox"));
	$arAllOptions[$key][] = Array("remember_changing_code_" . $key, GetMessage("S2U_REDIRECT_RULE_CHANGING_CODE"), array("checkbox"));
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
// основные настройки
$tabControl->BeginNextTab();

if (is_array($arAllOptions)) {
    foreach ($arAllOptions[0] as $Option) {
        $val = COption::GetOptionString($module_id, $Option[0]);
        $type = $Option[2];
        if ($type[0] == 'checkbox')
            $label = '<label for="' . htmlspecialcharsbx($Option[0]) . '">' . $Option[1] . '</label>';
        else
            $label = $Option[1];
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
        echo '<tr>
						<td valign="top">
							' . $label . '
						</td>
						<td valign="top" nowrap>
							' . $input . '
						</td>
					</tr>';
    }
}

// права доступа
$tabControl->BeginNextTab();
?>
    <? //require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>


    <?php
    foreach ($sites as $key => $value) {

        foreach ($arAllOptions[$key] as $Option) {
			
			//значение 
            $val = COption::GetOptionString($module_id, $Option[0]);
			//тип поля
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

            echo '<tr>					
						<td valign="top">
							<span id="hint_mirror_'. $key. '"></span>&nbsp;'. $label . '							
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