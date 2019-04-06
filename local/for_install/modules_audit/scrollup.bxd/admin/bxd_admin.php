<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
$APPLICATION->SetTitle("Bitrix Debug Options");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

IncludeModuleLangFile(__FILE__);

$aTabs = array(
    array("DIV" => "general", "TAB" => GetMessage("SCROLLUP_BXD_OBSIE_NASTROYKI"), "TITLE" => GetMessage("SCROLLUP_BXD_OBSIE_NASTROYKI"))
    );



$tabControl = new CAdminTabControl("tabControl", $aTabs);?>

<form method="post" action="<?=$APPLICATION->GetCurPage()?>" enctype="multipart/form-data" name="sbxd_form" id="sbxd_form">
    <?$tabControl->Begin();

    $tabControl->BeginNextTab();
    ?>
    <tr valign="top">
        <td width="40%" class="field-name"><?=GetMessage("SCROLLUP_BXD_PODKLUCATQ")?> jQuery:<br/><small><?=GetMessage("SCROLLUP_BXD_VEROATNEY_VSEGO")?>, jQuery <?=GetMessage("SCROLLUP_BXD_UJE_PODKLUCENA_U_VAS")?></small></td>
        <td valign="middle">
            <?$optionJquery = COption::GetOptionString("scrollup.bxd", "SBXD_JQUERY", "false");?>
            <input type="hidden" name="SBXD_JQUERY" value="false"/>
            <input type="checkbox" <?=$optionJquery == "true"?"checked=\"checked\"":"";?> name="SBXD_JQUERY" value="true"/>
        </td>
    </tr>
    <tr valign="top">
        <td><?=GetMessage("SCROLLUP_BXD_GRUPPY_DLA_VYVODA_DA")?><br/><small><?=GetMessage("SCROLLUP_BXD_ISPOLQZUYTE")?> Ctrl+<?=GetMessage("SCROLLUP_BXD_KLIK_DLA_VYDELENIA_N")?></small></td>
        <td>
            <select multiple="multiple" name="SBXD_GROUPS[]" size="8">
                <?
                $rsGroups = CGroup::GetList ($by = "c_sort", $order = "asc", Array("ACTIVE" => "Y"));
                while($rsGroup = $rsGroups->Fetch()){
                    $optionGroups = explode(",", COption::GetOptionString("scrollup.bxd", "SBXD_GROUPS", ""));
                    if(in_array($rsGroup["ID"], $optionGroups)){?>
                        <option selected="selected" value="<?=$rsGroup["ID"]?>"><?=$rsGroup["NAME"]?></option>
                        <?}else{?>
                        <option value="<?=$rsGroup["ID"]?>"><?=$rsGroup["NAME"]?></option>
                        <?}}?>
            </select>
        </td>
    </tr>
    <?
    $tabControl->EndTab();

    $tabControl->Buttons();?>

    <input type="submit" value="<?=GetMessage("SCROLLUP_BXD_SOHRANITQ")?>"/>

    <?$tabControl->End();?>
</form>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>