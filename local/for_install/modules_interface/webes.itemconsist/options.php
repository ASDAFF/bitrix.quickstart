<?php

$MODULE_ID = "webes.itemconsist";
CModule::IncludeModule($MODULE_ID);

IncludeModuleLangFile(__FILE__);

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\Loader,
    Bitrix\Iblock,
    Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);


GLOBAL $sale_module;
$sale_module=true;

if(!Loader::includeModule('sale')) {
    $sale_module=false;
}


$MODULE_RIGHT = $APPLICATION->GetGroupRight($MODULE_ID);

if ($MODULE_RIGHT < "R")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));



CJSCore::Init(array("jquery"));





$aTabs = array(
    array("DIV" => "groups", "TAB" => Loc::GetMessage("webes_ic_options_INGRIDIENTS"), "ICON" => "main_settings", "TITLE" => Loc::GetMessage("webes_ic_options_INGRIDIENTS")),
    array("DIV" => "ib_options", "TAB" => Loc::GetMessage("webes_ic_options_PRICE_OPTIONS"), "ICON" => "main_settings", "TITLE" => Loc::GetMessage("webes_ic_options_PRICE_OPTIONS")),
    array("DIV" => "prices_recalc", "TAB" => Loc::GetMessage("webes_ic_options_RECALC"), "ICON" => "main_settings", "TITLE" => Loc::GetMessage("webes_ic_options_RECALC")),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
$tabControl->BeginNextTab();
?>

<input type="button" value="<?=Loc::GetMessage("webes_ic_options_ADD_GROUP")?>" class="ic-w-view_next_element">
<div class="ic-w-hidden ic-w-add-group-block">
    <br>
    <?=Loc::GetMessage("webes_ic_options_GROUP_NAME")?>:<br>
    <input type="text" name="grname"><br>
    <input type="button" value="<?=Loc::GetMessage("webes_ic_options_ADD")?>" class="ic-w-group-add-button">
    <br><br>
</div>
<div class="ic-w-ingridients"></div>



<?
$tabControl->BeginNextTab();
?>

<p><i><?=Loc::GetMessage("webes_ic_options_HERE_YOU_CAN")?></i></p>
<input type="button" class="ic-w-button ic-w-help_options_iblocks" value="<?=Loc::GetMessage("webes_ic_options_HELP")?>">&nbsp;
<input type="button" class="ic-w-button ic-w-view_next_element" value="<?=Loc::GetMessage("webes_ic_options_ADD_OPTION")?>">
<div class="ic-w-hidden">
    <select class="iblock_id_select"></select>
    <input type="button" class="ic-w-button ic-w-add_iblock_config" value="<?=Loc::GetMessage("webes_ic_options_ADD")?>">
    <br>
</div>
<br><br>
<div class="ic-w-ib_setting_table_block"></div>

<?
$tabControl->BeginNextTab();
?>

<?if(!$sale_module):?>
    <p><?=Loc::GetMessage("webes_ic_options_ID_PRICE")?>:</p>
    <input type="text" class="ic-w-id-price-input" value="<?=COption::GetOptionString("webes.itemconsist", "id_price_input", "");?>"><br>
<?endif;?>

<input type="button" value="<?=Loc::GetMessage("webes_ic_options_RECALC")?>" class="ic-w-recalc_all_button">

<p><i><?=Loc::GetMessage("webes_ic_options_RECALC_INFO")?></i></p>
<?
$tabControl->Buttons();
$tabControl->End();

?>


<div style="display:flex;justify-content: space-around;margin:40px 0;">
    <div>Модуль разработан Web Engine Sudio в 2018 году </div>
    <a href="https://webes.ru/" target="_blank"><img src="https://service.webes.ru/images/get-logo.php?service=itemconsist" /></a>
</div>


<script src='/bitrix/components/webes/itemconsist/js/configuration<?=(mb_strtolower(LANG_CHARSET)!='utf-8'?'-1251':'')?>.js'></script>
<link rel="stylesheet" href='/bitrix/components/webes/itemconsist/css/configuration.css' />
