<?
    IncludeModuleLangFile(__FILE__);

    $iResultsCount = CStartShopFormResult::GetList(array(), array('FORM' => $arItem['ID']))->SelectedRowsCount();
    $iFieldsCount = CStartShopFormProperty::GetList(array(), array('FORM' => $arItem['ID']))->SelectedRowsCount();
    $arSections = array();

    if ($bRightsEdit)
        $arSections["FORM"] = array("NAME" => GetMessage('sections.form'), "LINK" => "/bitrix/admin/startshop_forms_edit.php?lang=".LANG."&action=edit&ID=".$arItem['ID']);

    $arSections["RESULTS"] = array("NAME" => GetMessage('sections.results', array('#COUNT#' => $iResultsCount)), "LINK" => "/bitrix/admin/startshop_forms_results.php?lang=".LANG."&FORM_ID=".$arItem['ID']);
    $arSections["FIELDS"] = array("NAME" => GetMessage('sections.fields', array('#COUNT#' => $iFieldsCount)), "LINK" => "/bitrix/admin/startshop_forms_fields.php?lang=".LANG."&FORM_ID=".$arItem['ID']);
?>
<div class="adm-list-table-top" style="margin-bottom: 10px">
    <?foreach ($arSections as $sSection => $arSection):?>
        <a href="<?=$arSection['LINK']?>" class="adm-btn<?=$sSection == $sSectionCurrent ? ' adm-btn-active' : ''?>"><?=$arSection['NAME']?></a>
    <?endforeach?>
</div>
<?
    unset($arSectionsLinks);
?>