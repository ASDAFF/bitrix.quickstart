<?
$ModuleID = 'asdaff.mass';
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $ModuleID . '/prolog.php');
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/iblock/admin_tools.php");

IncludeModuleLangFile(__FILE__);

CModule::IncludeModule('fileman');
define('WDA_STEP_DURATION', '10');
define('WDA_STEP_PAUSE', '1');

if (!CModule::IncludeModule($ModuleID)) {
    die('Module "asdaff.mass" is not found!');
}

if (!($APPLICATION->GetGroupRight($ModuleID) >= 'R')) {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$bCanWrite = $APPLICATION->GetGroupRight($ModuleID) >= 'W';
if (!CModule::IncludeModule('iblock')) {
    die('Module "iblock" is not found!');
}

CJSCore::Init(array('file_input', 'fileinput', 'jquery'));
$arExcludeKeys = array('autosave_id',
                       'iblock_id',
                       's',
                       'sub',
                       'wda_value_text',
                       'action',
                       'params',
                       'WDA_Tabs_active_tab',
                       'profile_name',
                       'lang'
);

// IBlock
$IBlockID = IntVal($_GET['IBLOCK_ID']);
$arIBlocks = CWDA::GetIBlockList(true, false);
$strJSON = '';
foreach ($arIBlocks as $IBlockTypeCode => $arIBlockType) {
    if (is_array($arIBlockType['ITEMS']) && !empty($arIBlockType['ITEMS'])) {
        foreach ($arIBlockType['ITEMS'] as $arIBlock) {
            $arIBlockFields = CIBlock::GetFields($arIBlock['ID']);
            if (!CWDA::IsUtf()) {
                $arIBlockFields = CWDA::ConvertCharset($arIBlockFields, 'CP1251', 'UTF-8');
            }
            $strJSON .= 'var IBlock_' . $arIBlock['ID'] . ' = ' . json_encode($arIBlockFields) . ';';
        }
    }
}

$arHtmlEditorJS = array('range.js',
                        'html-actions.js',
                        'html-views.js',
                        'html-parser.js',
                        'html-base-controls.js',
                        'html-controls.js',
                        'html-components.js',
                        'html-snippets.js',
                        'html-editor.js'
);

$strHtmlEditorPath = '/bitrix/js/fileman/html_editor/';
foreach ($arHtmlEditorJS as $strHtmlEditorJS) {
    $APPLICATION->AddHeadScript($strHtmlEditorPath . $strHtmlEditorJS);
}
$APPLICATION->SetAdditionalCSS($strHtmlEditorPath . 'html-editor.css');
$APPLICATION->AddHeadScript('/bitrix/js/fileman/core_file_input.js');
$APPLICATION->AddHeadScript('/bitrix/js/iblock/iblock_edit.js');
$APPLICATION->AddHeadScript('/bitrix/js/' . $ModuleID . '/' . $ModuleID . '.js');
$APPLICATION->AddHeadString('<script>' . $strJSON . '</script>');
$APPLICATION->AddHeadString('<script>

var WDA = {
	Url: "' . $APPLICATION->GetCurPage(true) . '",
	Lang: "' . LANGUAGE_ID . '",
	Messages: {
		FilterItemTitle: "' . GetMessage('WDA_ADMIN_PROP_FILTERS_ITEM_TITLE') . '",
		IBlockNotSelectedTitle: "' . GetMessage('WDA_ERROR_IBLOCK_NOT_SELECTED_TITLE') . '",
		IBlockNotSelectedMessage: "' . GetMessage('WDA_ERROR_IBLOCK_NOT_SELECTED_MESSAGE') . '",
		SuccessTitle: "' . GetMessage('WDA_DONE_TITLE') . '",
		SuccessMessage: "' . GetMessage('WDA_DONE_MESSAGE') . '",
		ErrorTitle: "' . GetMessage('WDA_ERROR_TITLE') . '",
		ErrorMessage: "' . GetMessage('WDA_ERROR_MESSAGE') . '",
		ProfileSavePrompt: "' . GetMessage('WDA_ERROR_SAVE_PROFILE_PROMPT') . '",
		ProfileSavePromptDefaultName: "' . GetMessage('WDA_ERROR_SAVE_PROFILE_PROMPT_DEFAULT_NAME') . '",
		ProfileSaveErrorEmptyIBlock: "' . GetMessage('WDA_ERROR_SAVE_PROFILE_EMPTY_IBLOCK') . '",
		ProfileSaveErrorEmptyAction: "' . GetMessage('WDA_ERROR_SAVE_PROFILE_EMPTY_ACTION') . '"
	},
	StepPause: "' . IntVal(WDA_STEP_PAUSE) . '"
};
</script>');

// Action
$ActionCode = htmlspecialcharsbx(ToUpper($_GET['ACTION']));
$arActions = CWDA::GetActionsList();
foreach ($arActions as $arAction) {
    if (is_array($arAction)) {
        ob_start();
        $arAction['CLASS']::AddHeadData();
        $strJS = ob_get_clean();
        if (strlen($strJS)) {
            $APPLICATION->AddHeadString($strJS);
        }
    }
}

// Group actions
$arActionGroups = CWDA::GetActionsGroup();
$arActionsGrouped = array();
foreach ($arActionGroups as $GroupCode => $GroupName) {
    $arSubAction = array();
    foreach ($arActions as $arAction) {
        if (ToUpper($arAction['GROUP']) == $GroupCode) {
            $arSubAction[] = $arAction;
        }
    }
    if (!empty($arSubAction)) {
        $arActionsGrouped[] = array(
            'CODE' => $GroupCode,
            'NAME' => $GroupName,
            'ITEMS' => $arSubAction,
        );
    }
}

if ($_GET['process'] == 'Y') {
    header("Content-type: application/json");
    $arResult = array('status' => 0); // 0 - error, 1 - need continue, 2 - done (success)
    $IBlockID = IntVal($_POST['iblock_id']);
    // Get action
    $ActionCode = $_POST['action'];
    $Action = CWDA::GetAction($ActionCode, $arActions);
    if (is_array($Action) && $bCanWrite) {
        $Class = $Action['CLASS'];
        if ($IBlockID > 0) {
            // Get filter
            $arSectionsID = $_POST['s'];
            if (is_array($arSectionsID)) {
                $arSectionsID = array_filter($arSectionsID);
            }
            $FilterFields = CWDA::GetAllFields($IBlockID);
            $FilterParams = CWDA::CollectFilter($_POST['f_p2'], $_POST['f_e2'], $_POST['f_v2']);
            $FilterResult = CWDA::BuildFilter($IBlockID, $arSectionsID, $_POST['sub'] == 'Y' ? true : false,
                $FilterParams, $FilterFields);
            if (is_array($FilterResult) && !empty($FilterResult)) {
                if ($_GET['start'] == 'Y') {
                    // Unset old data
                    unset(
                        $_SESSION['WDA_LAST_ID_' . $Action['CODE']],
                        $_SESSION['WDA_COUNT_' . $Action['CODE']],
                        $_SESSION['WDA_DONE_' . $Action['CODE']],
                        $_SESSION['WDA_SUCCEED_' . $Action['CODE']],
                        $_SESSION['WDA_FAILED_' . $Action['CODE']],
                        $_SESSION['WDA_PARAMS_' . $Action['CODE']],
                        $_SESSION['WDA_CUSTOM_' . $Action['CODE']]
                    );
                    // Get count
                    $_SESSION['WDA_COUNT_' . $Action['CODE']] = CWDA::GetCount($FilterResult);
                    $_SESSION['WDA_LAST_ID_' . $Action['CODE']] = 0;
                    $_SESSION['WDA_DONE_' . $Action['CODE']] = 0;
                    $_SESSION['WDA_FAILED_' . $Action['CODE']] = 0;
                    $_SESSION['WDA_PARAMS_' . $Action['CODE']] = $_POST['params'];
                    $_SESSION['WDA_CUSTOM_' . $Action['CODE']] = array();
                    $_SESSION['WDA_START'] = true;
                    $_SESSION['WDA_FIRST'] = true;
                }
                $arResult['count'] = IntVal($_SESSION['WDA_COUNT_' . $Action['CODE']]);
                if ($_GET['start'] == 'Y') {
                    $arResult['index'] = 0;
                    $arResult['status'] = 1;
                }
                else {
                    // Process
                    $arData = array(
                        'IBLOCK_ID' => $IBlockID,
                        'FILTER' => $FilterResult,
                        'MAX_TIME' => WDA_STEP_DURATION,
                        'ACTION' => $Action,
                    );
                    $arParams = $_SESSION['WDA_PARAMS_' . $Action['CODE']];
                    foreach ($_POST as $Key => $Value) {
                        if (!in_array($Key, $arExcludeKeys)) {
                            $arParams[$Key] = $Value;
                        }
                    }
                    $arResult['status'] = CWDA::Process($arData, $arParams);
                    $arResult['index'] = IntVal($_SESSION['WDA_DONE_' . $Action['CODE']]);
                    $arResult['succeed'] = IntVal($_SESSION['WDA_SUCCEED_' . $Action['CODE']]);
                    $arResult['failed'] = IntVal($_SESSION['WDA_FAILED_' . $Action['CODE']]);
                    unset($_SESSION['WDA_START']);
                }
            }
        }
    }
    if ($arResult['status'] == 1) {
        $arResult['next'] = true;
    }
    elseif ($arResult['status'] == 2) {
        $arResult['done'] = true;
    }
    $APPLICATION->RestartBuffer();
    print json_encode($arResult);
    die();
}

if ($_GET['show_additional_settings'] == 'Y') {
    $APPLICATION->RestartBuffer();
    $IBlockID = IntVal($_GET['iblock_id']);
    $Action = htmlspecialcharsbx($_GET['action']);
    $Action = CWDA::GetAction($Action, $arActions);
    if (is_array($Action)) {
        $Class = $Action['CLASS'];
        if (method_exists($Class, 'ShowAdditionalSettings')) {
            ob_start();
            $Class::ShowAdditionalSettings();
            $Settings = trim(ob_get_clean());
            if (!CWDA::IsUtf()) {
                $Settings = CWDA::ConvertCharset($Settings);
            }
            print $Settings;
        }
    }
    die();
}

if ($_GET['show_action_settings'] == 'Y') {
    $APPLICATION->RestartBuffer();
    $IBlockID = IntVal($_GET['iblock_id']);
    $Action = htmlspecialcharsbx($_GET['action']);
    $Action = CWDA::GetAction($Action, $arActions);
    if (is_array($Action)) {
        $Class = $Action['CLASS'];
        ob_start();
        $Class::ShowSettings($IBlockID);
        $Settings = trim(ob_get_clean());
        if (!CWDA::IsUtf()) {
            $Settings = CWDA::ConvertCharset($Settings);
        }
        $Descr = false;
        if (method_exists($Class, 'GetDescription')) {
            $Descr = $Class::GetDescription();
        }
        if ($Descr !== false) {
            print '<div id="wda_action_description">' . $Descr . '</div>';
        }
        if ($Settings == '') {
            print GetMessage('WDA_SETTINGS_NO_SETTINGS');
        }
        else {
            ob_start();
            print '<fieldset id="fieldset_settings"><legend>' . GetMessage('WDA_SETTINGS_TITLE') . '</legend>' . $Settings . '</fieldset>';
            $Settings = ob_get_clean();
            print $Settings;
        }
    }
    die();
}

if ($_GET['load_property_enums'] == 'Y') {
    header("Content-type: application/json");
    $arResult = array();
    $IBlockID = IntVal($_GET['iblock_id']);
    $PropertyID = IntVal($_GET['property_id']);
    if ($IBlockID > 0 && $PropertyID > 0) {
        $arResult['ITEMS'] = CWDA::GetPropertyEnums($IBlockID, $PropertyID);
    }
    $APPLICATION->RestartBuffer();
    print json_encode($arResult);
    die();
}

if ($_GET['change_iblock'] == 'Y') {
    header("Content-type: application/json");
    $arResult = array(
        'SECTIONS' => array(),
        'FILTER_FIELDS' => array(),
    );
    $IBlockID = IntVal($_GET['iblock_id']);
    if ($IBlockID > 0) {
        $arResult['SECTIONS'] = CWDA::GetSections($IBlockID);
        $arResult['FILTER_FIELDS'] = CWDA::GetAllFields($IBlockID);
        $arResult['GROUPS'] = CWDA::GetFilterFieldsGroups();
    }
    $APPLICATION->RestartBuffer();
    print json_encode($arResult);
    die();
}

if ($_GET['check_filter_results'] == 'Y') {
    header("Content-type: application/json");
    $arResult = array();
    $IBlockID = IntVal($_POST['iblock_id']);
    if ($IBlockID > 0) {
        $arSectionsID = $_POST['s'];
        if (is_array($arSectionsID) && count($arSectionsID) === 1 && isset($arSectionsID[0]) && $arSectionsID[0] === '') {
            $arSectionsID = false;
        }
        $FilterFields = CWDA::GetAllFields($IBlockID);
        $FilterParams = CWDA::CollectFilter($_POST['f_p2'], $_POST['f_e2'], $_POST['f_v2']);
        $Filter = CWDA::BuildFilter($IBlockID, $arSectionsID, $_POST['sub'] == 'Y' ? true : false, $FilterParams,
            $FilterFields);
        if (is_array($Filter) && !empty($Filter)) {
            $arResult['count'] = CWDA::GetCount($Filter);
            $arResult['count_approximately'] = CWDA::GetApproximately($arResult['count']);
        }
    }
    $APPLICATION->RestartBuffer();
    print json_encode($arResult);
    die();
}

if ($_GET['save_profile'] == 'Y') {
    header("Content-type: application/json");
    $arResult = array();
    if (!CWDA::IsUtf()) {
        $_POST = CWDA::ConvertCharset($_POST, 'UTF-8', 'CP1251');
    }
    $arActionParams = array(
        'params' => $_POST['params'],
    );
    foreach ($_POST as $Key => $Value) {
        if (!in_array($Key, $arExcludeKeys)) {
            $arActionParams[$Key] = $_POST[$Key];
        }
    }
    $arProfile = array(
        'NAME' => htmlspecialcharsbx($_POST['profile_name']),
        'IBLOCK_ID' => IntVal($_POST['iblock_id']),
        'SECTIONS_ID' => is_array($_POST['s']) ? implode(',', $_POST['s']) : $_POST['s'],
        'WITH_SUBSECTIONS' => $_POST['sub'] == 'Y' ? 'Y' : 'N',
        'FILTER' => UrlDecode(http_build_query(array(
            'f_p1' => $_POST['f_p1'],
            'f_p2' => $_POST['f_p2'],
            'f_e1' => $_POST['f_e1'],
            'f_e2' => $_POST['f_e2'],
            'f_v1' => $_POST['f_v1'],
            'f_v2' => $_POST['f_v2'],
        ))),
        'ACTION' => htmlspecialcharsbx($_POST['action']),
        'PARAMS' => UrlDecode(http_build_query($arActionParams)),
        'DATE_CREATED' => date(CDatabase::DateFormatToPHP(FORMAT_DATETIME)),
    );
    $obProfile = new CWDA_Profile();
    $ProfileID = $obProfile->Add($arProfile);
    if ($ProfileID > 0) {
        $arResult['SUCCESS'] = true;
        $arResult['PROFILE_ID'] = $ProfileID;
    }
    $APPLICATION->RestartBuffer();
    print json_encode($arResult);
    die();
}

$Lang = LANGUAGE_ID;
$APPLICATION->SetTitle(GetMessage('WDA_ADMIN_PAGE_TITLE'));
$arTabs = array(array("DIV" => "wda_settigns",
                      "TAB" => GetMessage('WDA_ADMIN_TAB_1_NAME'),
                      "TITLE" => GetMessage('WDA_ADMIN_TAB_1_DESC')
                )
);
$tabControl = new CAdminTabControl("WDA_Tabs", $arTabs);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
$arComparisonTypesJSON = CWDA::GetComparisonTypesJSON();
$APPLICATION->AddHeadString('<script>var WdaComparisonTypes=' . $arComparisonTypesJSON . ';</script>');
?>

<? if (!$bCanWrite): ?>
    <? CAdminMessage::ShowMessage(array("MESSAGE" => GetMessage('WDA_ERROR_ACCESS_DENIED'),
                                        "DETAILS" => GetMessage('WDA_ERROR_ACCESS_DENIED_DETAILS'),
                                        "TYPE" => "ERROR"
    )); ?>
<? endif ?>

<?
$arSubMenu = array();
$resProfiles = CWDA_Profile::GetList(array('SORT' => 'ASC', 'NAME' => 'ASC'), array(), 1000);
while ($arProfile = $resProfiles->GetNext()) {
    $arJson = $arProfile;
    if (!CWDA::IsUtf()) {
        $arJson = CWDA::ConvertCharset($arJson, 'CP1251', 'UTF-8');
    }
    $APPLICATION->AddHeadString('<script>window.WdaProfile' . $arProfile['ID'] . '=' . json_encode($arJson) . '</script>');
    $arSubMenu[] = array(
        'TEXT' => '[' . $arProfile['ID'] . '] ' . $arProfile['NAME'],
        'LINK' => 'javascript:WDA_LoadProfile(' . $arProfile['ID'] . ');',
    );
}
if (!empty($arSubMenu)) {
    $aMenu[] = array(
        'TEXT' => GetMessage('WDA_TOOLBAR_PROFILES'),
        'MENU' => $arSubMenu,
    );
}
$aMenu[] = array(
    'TEXT' => GetMessage('WDA_TOOLBAR_PROFILES_MANAGE'),
    'LINK' => 'wda_profiles.php?lang=' . LANGUAGE_ID,
);
$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

    <form method="post" action="<?= POST_FORM_ACTION_URI; ?>" enctype="multipart/form-data" name="wda_form" id="wda_form">
        <? $tabControl->Begin(); ?>
        <? $tabControl->BeginNextTab(); ?>
        <tr class="heading">
            <td colspan="2"><?= GetMessage('WDA_ADMIN_PROP_SOURCE'); ?></td>
        </tr>
        <tr id="tr_moderated">
            <td colspan="2">
                <table width="100%">
                    <tbody>
                    <tr>
                        <td width="40%" class="adm-detail-content-cell-l"><?= GetMessage('WDA_ADMIN_PROP_IBLOCK'); ?></td>
                        <td width="60%" class="adm-detail-content-cell-r">
                            <select name="iblock_id" id="wda_select_iblock">
                                <option value=""><?= GetMessage('WDA_ADMIN_PROP_IBLOCK_EMPTY'); ?></option>
                                <? foreach ($arIBlocks as $IBlockTypeCode => $arIBlockType): ?>
                                    <? if (is_array($arIBlockType['ITEMS']) && !empty($arIBlockType['ITEMS'])): ?>
                                        <optgroup label="<?= $arIBlockType['NAME']; ?>">
                                            <? foreach ($arIBlockType['ITEMS'] as $arItem): ?>
                                                <option value="<?= $arItem['ID']; ?>"<? if ($IBlockID == $arItem['ID']): ?> selected="selected"<? endif ?>>
                                                    [<?= $arItem['ID']; ?>] [<?= $arItem['CODE']; ?>
                                                    ] <?= $arItem['NAME']; ?></option>
                                            <? endforeach ?>
                                        </optgroup>
                                    <? endif ?>
                                <? endforeach ?>
                            </select>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td width="40%" class="adm-detail-content-cell-l" valign="top"><?= GetMessage('WDA_ADMIN_PROP_SECTION'); ?></td>
            <td width="60%" class="adm-detail-content-cell-r">
                <div>
                    <select name="s[]" id="wda_select_sections" multiple="multiple" size="10">
                        <option value="" selected="selected"><?= GetMessage('WDA_ADMIN_PROP_SECTION_EMPTY'); ?></option>
                    </select>
                </div>
                <br/>
                <div>
                    <input type="checkbox" name="sub" value="Y" id="wda_include_subsections" checked="checked"/>
                    <label for="wda_include_subsections"><?= GetMessage('WDA_ADMIN_PROP_INCLUDE_SUBSECTIONS'); ?></label>
                </div>
            </td>
        </tr>
        <tr class="heading">
            <td colspan="2"><?= GetMessage('WDA_ADMIN_PROP_FILTER'); ?></td>
        </tr>
        <tr>
            <td colspan="2">
                <div id="wda_filter_add">
                    <select id="wda_filter_param">
                        <option value=""><?= GetMessage('WDA_ADMIN_PROP_PROPERTY_EMPTY'); ?></option>
                    </select>
                    <select id="wda_filter_equal">
                        <option value=""><?= GetMessage('WDA_ADMIN_PROP_PROPERTY_EMPTY'); ?></option>
                    </select>
                    <span id="wda_filter_value" style="display:none;">
						<input type="text" value="" id="wda_filter_value_text" name="wda_value_text" size="15"/>
                        <?= Calendar('wda_value_text', 'wda_form') ?>
                        <select id="wda_filter_value_list" style="display:none"></select>
					</span>
                    <span>&nbsp;</span>
                    <input type="button" id="wda_filter_add_button" value="<?= GetMessage('WDA_ADMIN_PROP_FILTER_BTN_ADD'); ?>"/>
                </div>
                <div id="wda_filters">
                    <div id="filter_no_filters"><?= GetMessage('WDA_ADMIN_PROP_FILTERS_EMPTY'); ?></div>
                    <div id="filter_check_status"></div>
                </div>
            </td>
        </tr>
        <tr class="heading">
            <td colspan="2"><?= GetMessage('WDA_ADMIN_PROP_ACTION'); ?></td>
        </tr>
        <tr>
            <td colspan="2">
                <div>
                    <select name="action" id="wda_select_action">
                        <option value=""><?= GetMessage('WDA_ADMIN_PROP_ACTION_EMPTY'); ?></option>
                        <? foreach ($arActionsGrouped as $arActionGroup): ?>
                            <optgroup label="<?= $arActionGroup['NAME']; ?>" data-code="<?= $arActionGroup['CODE']; ?>">
                                <? foreach ($arActionGroup['ITEMS'] as $arAction): ?>
                                    <option title="<?= $arAction['NAME']; ?>" value="<?= $arAction['CODE']; ?>"<? if ($ActionCode == $arAction['CODE']): ?> selected="selected"<? endif ?>><?= $arAction['NAME']; ?></option>
                                <? endforeach ?>
                            </optgroup>
                        <? endforeach ?>
                    </select>
                    <input type="button" value="<?= GetMessage('WDA_ADMIN_ACTION_REFRESH'); ?>" id="wda_select_action_refresh" style="height:26px; vertical-align:top;"/>
                    <?= CWDA::ShowHint(GetMessage('WDA_HINT_SELECT_ACTION')); ?>
                </div>
                <br/>
                <div id="wda_action_params"></div> <!-- ajax fields -->
            </td>
        </tr>
        <? $tabControl->Buttons(); ?>
        <? if ($bCanWrite): ?>
            <input type="button" id="wda_submit" class="adm-btn-green" value="<?= GetMessage('WDA_ADMIN_SUBMIT'); ?>"/>
            <input type="button" id="wda_save_profile" value="<?= GetMessage('WDA_ADMIN_SAVE_PROFILE'); ?>" style="float:right"/>
            <span id="wda_progressbar"><span class="wda_bar"></span><span class="wda_text"></span></span>
            
            <input type="button" id="wda_cancel" value="<?= GetMessage('WDA_ADMIN_CANCEL'); ?>"/>
            <div style="margin-top:8px;">
                <?= GetMessage('WDA_ADMIN_BACKUP_NOTICE'); ?>
            </div>
        <? endif ?>
        <? $tabControl->End(); ?>
    </form>

    <div id="wda_message"></div>

<?
if (!CWDA::WdaCheckCli()) {
    CAdminMessage::ShowMessage(array(
        "MESSAGE" => GetMessage("WDA_CLI_CHECK_TITLE"),
        "DETAILS" => GetMessage("WDA_CLI_CHECK_CONTENT"),
        "HTML" => true,
    ));
}
?>

<? require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
