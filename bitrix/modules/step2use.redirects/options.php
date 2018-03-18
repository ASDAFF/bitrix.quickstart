<?php
$module_id = S2uRedirects::MODULE_ID;
global $MESS;
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].BX_ROOT.'/modules/main/options.php');
IncludeModuleLangFile(__FILE__);
include_once($GLOBALS['DOCUMENT_ROOT'].BX_ROOT. '/modules/' . $module_id . '/include.php');
$CAT_RIGHT = $MODULE_RIGHT = $APPLICATION->GetGroupRight($sModuleId);

if ($MODULE_RIGHT < "R")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));


if ($REQUEST_METHOD=="GET" && strlen($RestoreDefaults)>0 && $MODULE_RIGHT=="W")
{
    COption::RemoveOption($module_id);
    $z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
    while($zr = $z->Fetch())
        $APPLICATION->DelGroupRight($module_id, array($zr["ID"]));
}

if(!COption::GetOptionString($module_id, 'REDIRECTS_IS_ACTIVE'))
        COption::SetOptionString($module_id, 'REDIRECTS_IS_ACTIVE', 'Y');
if(!COption::GetOptionString($module_id, '404_IS_ACTIVE'))
        COption::SetOptionString($module_id, '404_IS_ACTIVE', 'Y');
if(!COption::GetOptionString($module_id, '404_LIMIT'))
        COption::SetOptionString($module_id, '404_LIMIT', '0');
if(!COption::GetOptionString($module_id, 'VALIDATE_URL_BY_RFC2396'))
        COption::SetOptionString($module_id, 'VALIDATE_URL_BY_RFC2396', 'Y');

$message = null;

$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage('S2U_MAIN'), "ICON" => "", "TITLE" => GetMessage("S2U_MAIN_TITLE")),
    //array("DIV" => "edit2", "TAB" => GetMessage('MAIN_TAB_RIGHTS'), "ICON" => "", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);

$arAllOptions = Array(
    //Array("max_levels", GetMessage("S2U_TEST_MAX_LEVELS")." ", Array("text", 5)),
    //Array("max_answers", GetMessage("S2U_TEST_MAX_ANSWERS")." ", Array("text", 5)),
    array("REDIRECTS_IS_ACTIVE", GetMessage("S2U_REDIRECTS_IS_ACTIVE")." ", array("checkbox")),
    array("404_IS_ACTIVE", GetMessage("S2U_404_IS_ACTIVE")." ", array("checkbox")),
    array("VALIDATE_URL_BY_RFC2396", GetMessage("S2U_VALIDATE_URL_BY_RFC2396")." ", array("checkbox")),
    array("404_LIMIT", GetMessage("S2U_404_LIMIT")." ", array("select", array(
        '0' => GetMessage('S2U_404_LIMIT_ALL'),
        '1000' => '1000 '.GetMessage('S2U_404_LIMIT_LAST_N'),
        '2000' => '2000 '.GetMessage('S2U_404_LIMIT_LAST_N'),
        '3000' => '3000 '.GetMessage('S2U_404_LIMIT_LAST_N'),
        '4000' => '4000 '.GetMessage('S2U_404_LIMIT_LAST_N'),
        '5000' => '5000 '.GetMessage('S2U_404_LIMIT_LAST_N'),
    )),),
);

if($REQUEST_METHOD == 'POST' && isset($_REQUEST['Update']) && check_bitrix_sessid()) {
    foreach ($arAllOptions as $ar) {
        $val = ${$ar[0]};
        if ($ar[2][0] == 'checkbox' && $val != 'Y')
            $val = 'N';
        COption::SetOptionString($module_id, $ar[0], $val);
    }
}

$arGroups=array(
    'REFERENCE'=>array(),
    'REFERENCE_ID'=>array(),
);
$resGroups = CGroup::GetList($by="c_sort", $order="asc");
while($arr = $resGroups->Fetch()) {
    $arGroups['REFERENCE'][] = '[' . $arr['ID'] . '] ' . $arr['NAME'];
    $arGroups['REFERENCE_ID'][] = $arr['ID'];
}
//arSelectedGroups = explode(',', COption::GetOptionString('s2u_step2use', 's2u_groups_to_send',''));

if(count($arMsg))
{
    if($e = $APPLICATION->GetException())
        $message = new CAdminMessage(GetMessage("S2U_OPTIONS_ERROR"), $e);
    $bVarsFromForm = true;
}

if ($message)
    echo $message->Show();

$tabControl->Begin();
?>
<style>
    .field-name {
        width: 200px;
    }
</style>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>"><?

// основные настройки
$tabControl->BeginNextTab();

if (is_array($arAllOptions)) {
    foreach ($arAllOptions as $Option) {
        $val = COption::GetOptionString($module_id, $Option[0]);
        $type = $Option[2];

        if ($type[0] == 'checkbox')
            $label = '<label for="' . htmlspecialchars($Option[0]) . '">' . $Option[1] . '</label>';
        else
            $label = $Option[1];

        if ($type[0] == 'checkbox')
            $input = '<input type="checkbox" name="' . htmlspecialchars($Option[0]) . '" id="' . htmlspecialchars($Option[0]) . '" value="Y"' . ($val == 'Y' ? ' checked' : '') . '>';
        elseif ($type[0] == 'text')
            $input = '<input type="text" size="' . $type[1] . '" maxlength="255" value="' . htmlspecialchars($val) . '" name="' . htmlspecialchars($Option[0]) . '">';
        elseif ($type[0] == 'textarea')
            $input = '<textarea rows="' . $type[1] . '" cols="' . $type[2] . '" name="' . htmlspecialchars($Option[0]) . '">' . htmlspecialchars($val) . '</textarea>';
        elseif ($type[0] == 'select') {
            $input = '<select name="' . htmlspecialchars($Option[0]) . '">';
            foreach($type[1] as $key=>$valOpt) {
                $input .= '<option value="'.htmlspecialchars($key).'" '.(($key==htmlspecialchars($val))? 'selected="selected"': '').'>'.htmlspecialchars($valOpt).'</option>';
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
<?//require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>

<?$tabControl->Buttons();?>
<script language="JavaScript">
function RestoreDefaults()
{
    if (confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
        window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo urlencode($mid)?>";
}
</script>
<input type="submit" <?if ($MODULE_RIGHT<"W") echo "disabled" ?> name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">
<input type="hidden" name="Update" value="Y">
<input type="reset" name="reset" value="<?echo GetMessage("MAIN_RESET")?>">
<input type="button" <?if ($MODULE_RIGHT<"W") echo "disabled" ?> title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="RestoreDefaults();" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">    
<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>