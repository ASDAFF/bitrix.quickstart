<?
	IncludeModuleLangFile(__FILE__);

	require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/intec.startshop/web/include.php');
?>
<form id="StartshopInstall">
	<?=bitrix_sessid_post(); ?>
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID;?>">
	<input type="hidden" name="id" value="intec.startshop">
	<input type="hidden" name="install" value="Y">
	<input type="hidden" name="step" value="1">
	<p><?=GetMessage('module.install.form.description'); ?></p>
	<?
		$arSites = array();
		$dbSites = CSite::GetList($by = "sort", $order = "asc");
		
		$arTabs = array();
		
		while ($arSite = $dbSites->Fetch())
		{
			$arSites[] = $arSite;
			
			$arTab = array();
			$arTab['DIV'] = $arSite['ID'];
			$arTab['TAB'] = $arSite['NAME'].' ('.$arSite['ID'].')';
			$arTab['TITLE'] = $arSite['NAME'].' ('.$arSite['ID'].')';
			$arTabs[] = $arTab;
		}
	
		$tabControl = new CAdminTabControl("tabControl", $arTabs);
		$tabControl->Begin();
		
		foreach ($arSites as $arSite)
		{
			$tabControl->BeginNextTab();
	?>
	<tr>
		<td>
			<h2 style="margin-top: 0px; margin-bottom: 20px;"><?=GetMessage('module.install.sections.sections')?></h2>
			<p>
				<input class="adm-checkbox adm-designed-checkbox startshop-area-switch" type="checkbox" name="startshopInstallSections_<?=$arSite['ID']?>[CATALOG]" id="startshopInstallSections_<?=$arSite['ID']?>_CATALOG" value="Y">
				<label class="adm-checkbox adm-designed-checkbox-label" for="startshopInstallSections_<?=$arSite['ID']?>_CATALOG"></label>
				<label class="adm-checkbox-label" for="startshopInstallSections_<?=$arSite['ID']?>_CATALOG"><?=GetMessage('module.install.sections.sections.install_catalog'); ?></label>
			</p>
			<div class="startshopInstallSections_<?=$arSite['ID']?>_CATALOG" style="display: none;">
				<table class="adm-detail-content-table">
					<tr>
						<td class="adm-detail-content-cell-l" style="white-space: nowrap;">
							<?=GetMessage('module.install.sections.sections.path_to_section')?>:
						</td>
						<td class="adm-detail-content-cell-r" style="width: 100%;">
							<input type="text" name="startshopInstallSectionsPaths_<?=$arSite['ID']?>[CATALOG]" value="/catalog/" style="margin-left: 20px;" />
						</td>
					</tr>
					<tr>
						<?
							$dbIBlocks = CIBlock::GetList(array(), array('SITE_ID' => $arSite['ID']));
						?>
						<td class="adm-detail-content-cell-l" style="white-space: nowrap;">
							<?=GetMessage('module.install.sections.sections.iblock_product')?>:
						</td>
						<td class="adm-detail-content-cell-r" style="width: 100%;">
							<select name="startshopInstallSectionsIBlocks_<?=$arSite['ID']?>[CATALOG]" style="margin-left: 20px;">
								<?while ($arIBlock = $dbIBlocks->Fetch()):?>
									<option value="<?=$arIBlock['ID']?>">[<?=$arIBlock['ID']?>] <?=$arIBlock['NAME']?></option>
								<?endwhile?>
							</select>
						</td>
					</tr>
				</table>
			</div>
			<p>
				<input class="adm-checkbox adm-designed-checkbox startshop-area-switch" type="checkbox" name="startshopInstallSections_<?=$arSite['ID']?>[CART]" id="startshopInstallSections_<?=$arSite['ID']?>_CART" value="Y">
				<label class="adm-checkbox adm-designed-checkbox-label" for="startshopInstallSections_<?=$arSite['ID']?>_CART"></label>
				<label class="adm-checkbox-label" for="startshopInstallSections_<?=$arSite['ID']?>_CART"><?=GetMessage('module.install.sections.sections.install_cart'); ?></label>
			</p>
			<div class="startshopInstallSections_<?=$arSite['ID']?>_CART" style="display: none;">
				<table class="adm-detail-content-table">
					<tr>
						<td class="adm-detail-content-cell-l" style="white-space: nowrap;">
							<?=GetMessage('module.install.sections.sections.path_to_section')?>:
						</td>
						<td class="adm-detail-content-cell-r" style="width: 100%;">
							<input type="text" name="startshopInstallSectionsPaths_<?=$arSite['ID']?>[CART]" value="/cart/" style="margin-left: 20px;" />
						</td>
					</tr>
				</table>
			</div>
			<p>
				<input class="adm-checkbox adm-designed-checkbox startshop-area-switch" type="checkbox" name="startshopInstallSections_<?=$arSite['ID']?>[PERSONAL]" id="startshopInstallSections_<?=$arSite['ID']?>_PERSONAL" value="Y">
				<label class="adm-checkbox adm-designed-checkbox-label" for="startshopInstallSections_<?=$arSite['ID']?>_PERSONAL"></label>
				<label class="adm-checkbox-label" for="startshopInstallSections_<?=$arSite['ID']?>_PERSONAL"><?=GetMessage('module.install.sections.sections.install_personal'); ?></label>
			</p>
			<div class="startshopInstallSections_<?=$arSite['ID']?>_PERSONAL" style="display: none;">
				<table class="adm-detail-content-table">
					<tr>
						<td class="adm-detail-content-cell-l" style="white-space: nowrap;">
							<?=GetMessage('module.install.sections.sections.path_to_section')?>:
						</td>
						<td class="adm-detail-content-cell-r" style="width: 100%;">
							<input type="text" name="startshopInstallSectionsPaths_<?=$arSite['ID']?>[PERSONAL]" value="/personal/" style="margin-left: 20px;" />
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<?
			$tabControl->EndTab();
		}
		
		$tabControl->End();
	?>
	<script type="text/javascript">
		$(document).ready(function() {
			var $oRoot = $('#StartshopInstall');

			$oRoot.find('.startshop-area-switch').change(function() {

				if ($(this).prop('checked') == true) {
					$('.' + $(this).attr('id')).css('display', 'block');
				} else {
					$('.' + $(this).attr('id')).css('display', 'none');
				}
			});
		});
	</script>
	<div style="padding-top: 20px;"></div>
	<input type="submit" class="adm-btn-save" name="inst" value="<?=GetMessage('module.install.form.submit'); ?>">
	<a class="adm-btn" href="/bitrix/admin/partner_modules.php?lang=<?=LANG; ?>"><?=GetMessage('module.install.form.back'); ?></a>
</form>