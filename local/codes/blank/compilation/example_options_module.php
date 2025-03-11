<?php
/**
 * Created by Vitalii Sestrenskyi.
 * User: ITUA
 * Date: 26.01.2017
 * Time: 17:39
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)  die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Group\GroupTable;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

$request = Application::getInstance()->getContext()->getRequest();

$moduleID="itua.afa";
Loader::includeModule($moduleID);
if($_POST["submit"])
{
    Option::set($moduleID, "CONTROLLER_GROUP", $request->getPost("CONTROLLER_GROUP"));

    $uriString = $request->getRequestUri();
    LocalRedirect($uriString);
}


$aTabs = array();
$aTabs[] = array('DIV' => 'set', 'TAB' => Loc::getMessage($moduleID.'_TITLE'), 'TITLE' => Loc::getMessage($moduleID.'_TAB_TITLE'));

$tabControl = new CAdminTabControl('tabControl', $aTabs);

$arrGroupsSelectBox = [];
foreach (GroupTable::getList([])->fetchAll() as $group)
{
    if(!is_null($group['STRING_ID']))
        $arrGroupsSelectBox[$group['STRING_ID']] = $group['NAME'].' - ['.$group['STRING_ID'].']';
}

$arOptionsBase = array(
    array("CONTROLLER_GROUP", Loc::getMessage($moduleID."_CONTROLLER_GROUP"), Option::get($moduleID,"CONTROLLER_GROUP"), array("selectbox",$arrGroupsSelectBox)),
);


?>
<?
$tabControl->Begin();
?>
<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?=LANGUAGE_ID?>&mid=<?=$moduleID?>">
    <?$tabControl->BeginNextTab();?>
    <?=bitrix_sessid_post();?>
    <?__AdmSettingsDrawList($moduleID, $arOptionsBase);?>
    <?//$tabControl->BeginNextTab();?>
    <?$tabControl->Buttons();?>
    <input type="submit" name="submit" value="<?=Loc::getMessage($moduleID."_SAVE_BUTTON")?>">
    <?$tabControl->End();?>
    <style></style>
</form>

