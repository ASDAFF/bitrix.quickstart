<?php

global $MESS;
global $APPLICATION;

$module_id = 'triggmine.abandonedcartrecovery';
CModule::IncludeModule($module_id);

include(GetLangFileName(dirname(__FILE__) . "/lang/", "/options.php"));

if ($REQUEST_METHOD == "POST" && check_bitrix_sessid())
{
    CTriggMine::updateOptions($_POST);
    $aTriggMineErrors = CTriggMine::getErrors();
    if (empty($aTriggMineErrors)) {
        // no errors - set selected status
        $aTriggMineErrors = CTriggMine::updateStatus($_POST);
    }
} else {
    $aTriggMineErrors = CTriggMine::getErrors();
}

if (!empty($aTriggMineErrors)) {
    // has errors - set inactive
    CTriggMine::deactivate();
}

$triggmine_is_on    = COption::GetOptionString($module_id, "triggmine_is_on");
$triggmine_token    = COption::GetOptionString($module_id, "triggmine_token");
$triggmine_rest_api = COption::GetOptionString($module_id, "triggmine_rest_api");

$aTabs = array(
    array("DIV" => "edit1", "TAB" => $MESS['triggmine_settings_title'], "TITLE" => "TriggMine " . $MESS['triggmine_settings_title']),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?echo LANG?>" name="ara">
    <?=bitrix_sessid_post();?>
    <?$tabControl->BeginNextTab();?>

    <? if (!empty($aTriggMineErrors)) {
        echo "<div class='adm-info-message' style='width:95%; margin-top: 0px;'>";
        foreach ($aTriggMineErrors as $aTriggMineError) {
            echo "<span class=\"required\">" . $aTriggMineError['message'] . "</span><br>";
        }
        echo "</div>";
    } elseif (isset($_POST['run_check'])) {
        echo "<div class='adm-info-message' style='width:95%; margin-top: 0px;'>";
        echo "<span style=\"color:green;\">" . date('d/m/Y H:i')  . ' - ' . $MESS['triggmine_plugin_is_ok'] . "</span>";
        if ($triggmine_is_on == 'N') {
            echo "<br/><span style=\"color:green;\">" . $MESS['triggmine_plugin_switch_on']  . "</span>";
        }
        echo "</div>";
    } elseif ($triggmine_is_on == 'N') {
        echo "<div class='adm-info-message' style='width:95%;  margin-top: 0px;'>";
        echo "<span class=\"required\">" . $MESS['plugin_can_be_active']  . "</span>";
        echo "</div>";
    } else {
        echo "<div class='adm-info-message' style='width:95%;  margin-top: 0px;'>";
        echo "<span style=\"color:green\">" . $MESS['triggmine_plugin_is_on']  . "</span>";
        echo "</div>";
    }

    ?>

    <td>
        <table width="100%">
            <col style="width: 20%">
            <tr>
                <td><label for="triggmine_is_on"><?=$MESS['triggmine_settings_on']?></label></td>
                <td><input type="checkbox" name="triggmine_is_on" id="triggmine_is_on" value="Y" <?=( $triggmine_is_on == 'Y' ? 'checked' : '' )?> ></td>
            </tr>
            <tr>
                <td><label for="triggmine_rest_api"><?=$MESS['triggmine_settings_url']?></label></td>
                <td><input type="text" name="triggmine_rest_api" id="triggmine_rest_api" value="<?=$triggmine_rest_api?>" style="width:99%"><br></td>
            </tr>
            <tr>
                <td><label for="triggmine_token"><?=$MESS['triggmine_settings_key']?></label></td>
                <td><input type="text" name="triggmine_token" id="triggmine_token" value="<?=$triggmine_token?>" style="width:99%"></td>
            </tr>
            <tr>
                <td><label for="triggmine_token"><?=$MESS['triggmine_cart_url']?></label></td>
                <td>
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="white-space: nowrap;">
                                <?=CTriggMine::triggmine_cart_host()?>/
                            </td>
                            <td width="100%"><input type="text" name="triggmine_cart_url" id="triggmine_cart_url" value="<?=CTriggMine::triggmine_cart_url(true)?>" style="width:99%"></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top: 10px;">
                    <input type="submit" value="<?=$MESS['triggmine_settings_save']?>" class="adm-btn-save">
                    <input type="reset" value="<?=$MESS['triggmine_settings_reset']?>">
                    <input type="submit" name="run_check" value="<?=$MESS['triggmine_run_check']?>" onclick="">
                </td>
            </tr>
        </table>
    </td>
    <?$tabControl->Buttons();?>
</form>

<div style="float:right; text-align: right; color: #181818;">
    <?php
    echo "<i>" . $MESS['triggmine_your_module_version'] . ":" . CTriggMine::getModuleVersion() . ", " . $MESS['triggmine_your_bitrix_version'] . ':' . SM_VERSION . "</i><br/>";
    echo "<i>" . $MESS['triggmine_check_updates'] . "</i>";
    echo "<i>" . $MESS['plugin_is_working'] . "</i>";
    ?>
</div>

<div style="float:left; color: #151515;">

    <i style="padding-bottom:5px;"><?=$MESS['triggmine_dashboard']?></i><br>
    <i><?=$MESS['triggmine_support']?></i>


</div>

<br><br>

<?$tabControl->End();?>




