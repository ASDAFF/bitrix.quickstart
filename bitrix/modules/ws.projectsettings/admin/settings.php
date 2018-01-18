<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/ws.projectsettings/prolog.php");
/* @var $APPLICATION CMain */
if ($APPLICATION->GetGroupRight("ws.projectsettings") == "D") {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

#--- save fields ---
if ($fields = $_REQUEST['fields']) {
    WS_PSettings::clearAll();
    foreach ($fields as $fName => $fData) {
        WS_PSettings::setupField($fData);
    }
}
#--- view fields ---
if (CModule::IncludeModule('iblock')) {
    $iblocks = array();
    $rsIblock = CIblock::GetList(null, array('ACTIVE' => 'Y'));
    while ($arIblock = $rsIblock->fetch()) {
        $iblocks[$arIblock['ID']] = $arIblock['NAME'];
    }
}

$groups = array();
$rsUserGroups = CGroup::GetList($ugBy = 'name', $ugOrder = 'asc', array('ACTIVE' => 'Y'));
while ($arUGroup = $rsUserGroups->fetch()) {
    $groups[$arUGroup['ID']] = $arUGroup['NAME'];
}

$tab = new CAdminTabControl('WS_ProgectSettings_tab', array(
    array(
        'TAB' => GetMessage('ws_product_settings_page_title'),
        'DIV' => 'edit1'
    )
));
$APPLICATION->SetTitle(GetMessage('ws_product_settings_page_title'));
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
CJSCore::Init(array('ws_progectssettings_fields'));
$jsParams = array(
    'fields' => array(),
    'curUri' => $APPLICATION->GetCurPage(),
    'newFieldButton' => 'ws_project_settings_add_field_button',
    'toogleSimCodesButton' => 'ws_project_settings_toogle_sim_codes_button',
    'submitButton' => 'ws_project_settings_save_button',
    'defaultButton' => 'ws_project_settings_default_button',
    'cancelButton' => 'ws_project_settings_cancel_button',
    'types' => array(
        WS_PSettings::FIELD_TYPE_NUMERIC => GetMessage('ws_projectsettings_field_type_number'),
        WS_PSettings::FIELD_TYPE_STRING => GetMessage('ws_projectsettings_field_type_string'),
        WS_PSettings::FIELD_TYPE_LIST => GetMessage('ws_projectsettings_field_type_list'),
        WS_PSettings::FIELD_TYPE_SIGN => GetMessage('ws_projectsettings_field_type_sign'),
        WS_PSettings::FIELD_TYPE_USER => GetMessage('ws_projectsettings_field_type_user'),
        WS_PSettings::FIELD_TYPE_USER_GROUP => GetMessage('ws_projectsettings_field_type_user_group'),
        WS_PSettings::FIELD_TYPE_IBLOCK => GetMessage('ws_projectsettings_field_type_iblock'),
    ),
    'variants' => array(
        WS_PSettings::FIELD_TYPE_IBLOCK => $iblocks,
        WS_PSettings::FIELD_TYPE_USER_GROUP => $groups
    ),
    'customLists' => array()
);


$typeBuildEvents  = GetModuleEvents("ws.projectsettings", "OnBuildTypes");
while ($arEvent = $typeBuildEvents->Fetch()) {
    $typeDescription = ExecuteModuleEventEx($arEvent);
    if (!$typeDescription['name']) {
        continue;
    }
    if (!$typeDescription['variants'] || !is_array($typeDescription['variants']) || empty($typeDescription['variants'])) {
        continue;
    }

    $tName = $typeDescription['name'];
    $jsParams['types'][$tName] = ' * '.$tName;
    foreach ($typeDescription['variants'] as $tVariantValue => $tVariantName) {
        $jsParams['customLists'][$tName][$tVariantValue] = $tVariantName;
    }
}

foreach (WS_PSettings::getFieldsList() as $fieldName) {
    $field = WS_PSettings::getField($fieldName);
    if (!$jsParams['types'][$field->getType()]) {
        continue ;
    }
    $value = $field->getValue();
    $default = $field->getDefault();
    $fData = array(
        'label' => $field->getLabel(),
        'name' => $field->getName(),
        'type' => $field->getType(),
        'value' => $value,
        'isMany' => $field->isMany(),
        'sort' => $field->getSort(),
        'default' => $default,
        'variants' => array()
    );
    if ($field->getType() == WS_PSettings::FIELD_TYPE_LIST){
        $fData['variants'] = $field->getVariants();
        $variants = array();
        foreach ($fData['variants'] as $value => $name) {
            $variants[$value] = $name;
        }
    }
    $jsParams['fields'][$field->getName()] = $fData;

}
?><div class="project_settings"><?
$tab->Begin();
$tab->BeginNextTab();
?>
    <tr class="heading">
        <td valign="top" colspan="2" style="text-align: center; padding-right: 130px;" align="left">
            <a href="#" class="action" id="<?=$jsParams['newFieldButton']?>"><?= GetMessage("ws_projectsettings_add_field_link") ?></a>&nbsp;&nbsp;
            <a href="#" class="action" id="<?=$jsParams['toogleSimCodesButton']?>"><?= GetMessage("ws_projectsettings_toogle_sim_codes_link") ?></a>
        </td>
    </tr>
<?
$tab->EndTab();
$tab->Buttons();
?><input type="button" id="<?=$jsParams['submitButton']?>" class="button" name="" disabled="disabled" value="<?= GetMessage("ws_projectsettings_save_button_name") ?>" /><?
?><input type="button" id="<?=$jsParams['defaultButton']?>" class="button" name="" value="<?= GetMessage("ws_projectsettings_default_button_name") ?>" /><?
?><input type="button" id="<?=$jsParams['cancelButton']?>" class="button" name="" value="<?= GetMessage("ws_projectsettings_cancel_button_name") ?>" /><?
$tab->End();
?></div>
<script type="text/javascript">WS.ProjectSettings(<?=CUtil::PhpToJSObject($jsParams, true)?>);</script>
<style>
.project_settings a.action {
    text-decoration: none;
    border-bottom: 1px dashed;
}
</style><?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");