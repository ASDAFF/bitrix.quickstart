<?php
$MODULE_ID = "mibix.yamexport";
$RIGHT = $APPLICATION->GetGroupRight($MODULE_ID);
if($RIGHT >= "R") :

    IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
    IncludeModuleLangFile(__FILE__);

    $module_status = CModule::IncludeModuleEx($MODULE_ID);
    if($module_status == '0')
    {
        echo GetMessage('DEMO_MODULE');
    }
    elseif($module_status == '3')
    {
        echo GetMessage('DEMO_MODULE');
    }

    // вкладки
    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "ib_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
    );
    $tabControl = new CAdminTabControl("tabControl", $aTabs);

    // путь для ссылки
    $settingsPath = 'http://'.$_SERVER['HTTP_HOST'].'/bitrix/admin/mibix.yamexport_service_index.php?lang=ru';

    $tabControl->Begin();
    $tabControl->BeginNextTab();
    ?>
    <tr>
        <td colspan="2" valign="top" width="50%"><a href="<?=$settingsPath?>"><?=GetMessage("MIBIX_YAM_LINK")?></a></td>
    </tr>
<?endif?>
    <?$tabControl->End();?>