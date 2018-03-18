<?php
if(!$USER->IsAdmin()) { return; }

IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/options.php");
IncludeModuleLangFile(__FILE__);

$module_id = "newkaliningrad.typography";


if($REQUEST_METHOD=="POST" && strlen($Update.$Apply)>0 && check_bitrix_sessid()) {
	COption::SetOptionString($module_id, "nk_typography_autoiblocks", serialize($_POST['nk_typography_autoiblocks']), false);
}


$aTabs = array(
	array("DIV" => "edit1", 
		"TAB" => GetMessage("NK_OPTIONS_TAB1"), 
		"ICON" => "ib_settings", 
		"TITLE" => GetMessage("NK_OPTIONS_TAB1_DESC")
	),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();?>
<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?echo LANGUAGE_ID?>">
<?$tabControl->BeginNextTab();?>
	<tr>
		<td style="width:220px"><?=GetMessage('NK_OPTIONS_AUTOIBLOCKS')?>:</td>
		<td>
			<select name="nk_typography_autoiblocks[]" multiple="multiple">
			<?if (CModule::IncludeModule("iblock")) {
			$nk_typography_autoiblocks = COption::GetOptionString($module_id, "nk_typography_autoiblocks", false);
			$nk_typography_autoiblocks = unserialize($nk_typography_autoiblocks);
			$iblocks_res = CIBlock::GetList(Array('IBLOCK_TYPE'=>'ASC'), Array('ACTIVE'=>'Y'), true);
			$i=0;
			while($iblock = $iblocks_res->Fetch()){ $i++;
				if ($iblock['IBLOCK_TYPE_ID']!=$IBLOCK_TYPE_ID) {
					$IBLOCK_TYPE_ID = $iblock['IBLOCK_TYPE_ID'];
					if($arIBType = CIBlockType::GetByIDLang($iblock['IBLOCK_TYPE_ID'], LANG)) {
						$iblock['TYPE_NAME'] = $arIBType["NAME"];
					}
					if ($i!=1) {?></optgroup><?}?>	
					<optgroup label="<?=$iblock['TYPE_NAME']?>">
				<?}

				$selected = '';
				if (in_array($iblock['ID'], $nk_typography_autoiblocks)) {
					$selected = 'selected="selected"';
				}?>
				<option value="<?=$iblock['ID']?>" <?=$selected?>>-<?=$iblock['NAME']?></option>
			<?} // while($iblock = $iblocks...
			} // if (CModule::IncludeModule("ibloc... ?>	
			</optgroup></select>			
		</td>
	</tr>


<?$tabControl->Buttons();?>
	<input type="submit" name="Update" value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
	<input type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>
