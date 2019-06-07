<?
/**
 * Bitrix vars
 *
 * @var array      $arFieldTitle
 * @var array      $profile
 * @var CAdminForm $tabControl
 *
 * @var CUser      $USER
 * @var CMain      $APPLICATION
 *
 */
use \Bitrix\Main\Localization\Loc;
use Api\Export\Tools;

Loc::loadMessages(__FILE__);
?>
<?
$tabControl->AddCheckBoxField('PROFILE[USE_CATALOG]', $arFieldTitle['USE_CATALOG'], false, array('Y','N'), ($profile['USE_CATALOG'] == 'Y'));
$tabControl->AddCheckBoxField('PROFILE[USE_SUBSECTIONS]', $arFieldTitle['USE_SUBSECTIONS'], false, array('Y','N'), ($profile['USE_SUBSECTIONS'] == 'Y'));
?>
<? $tabControl->BeginCustomField('IBLOCK_TYPE_ID', $arFieldTitle['IBLOCK_TYPE_ID'], true); ?>
	<tr class="heading" align="center">
		<td colspan="2"><?=Tools::showHint('AYPE_TAB_HEADING_EXPORT_DATA')?><?=Loc::getMessage('AYPE_TAB_HEADING_EXPORT_DATA')?></td>
	</tr>
	<tr>
		<td colspan="2">
			<div id="api_iblock_type_id">
				<select name="PROFILE[IBLOCK_TYPE_ID]" size="5">
					<? if($arCatalogs): ?>
						<? foreach($arCatalogs as $arCatalog): ?>
							<option value="<?=$arCatalog['ID']?>"<?=((!isset($profile['IBLOCK_TYPE_ID']) && $arCatalog['DEF'] == 'Y') || ($arCatalog['ID'] == $profile['IBLOCK_TYPE_ID'])) ? " selected" : ""?>><?=$arCatalog['NAME']?></option>
						<? endforeach; ?>
					<? endif ?>
				</select>
			</div>

			<div id="api_iblock_id">
				<select name="PROFILE[IBLOCK_ID]" size="5">
					<? if($profile['IBLOCK_ID'] && $arCatalogs): ?>
						<? foreach($arCatalogs as $arCatalog): ?>
							<? if($arCatalog['IBLOCK']): ?>
								<? foreach($arCatalog['IBLOCK'] as $id => $iblock): ?>
									<?
									if($arCatalog['ID'] != $profile['IBLOCK_TYPE_ID'])
										continue;

									$selected = ($id == $profile['IBLOCK_ID'] ? ' selected' : '')
									?>
									<option value="<?=$id?>"<?=$selected?>><?=$iblock?></option>
								<? endforeach ?>
							<? endif ?>
						<? endforeach ?>
					<? endif ?>
				</select>
			</div>
			<?
			$cnt       = count($arCatalogSections);
			$attr_size = ($cnt > 20 ? 20 : $cnt);
			?>
			<div id="api_iblock_section_id">
				<select name="PROFILE[SECTION_ID][]" size="<?=$attr_size?>"
					 <? if(!$profile['SECTION_ID'] && !$arCatalogSections): ?>  style="display:none"<? endif ?> multiple>
					<option value=""<? if(!$profile['SECTION_ID'][0]): ?> selected<? endif ?>><?=Loc::getMessage('AYI_SELECT_OPTION_ALL')?></option>
					<? if($profile['SECTION_ID'] || $arCatalogSections): ?>
						<? foreach($arCatalogSections as $id => $section): ?>
							<?
							$selected = '';
							if($profile['SECTION_ID'] && in_array($id, $profile['SECTION_ID']))
								$selected = ' selected';
							?>
							<option value="<?=$id?>"<?=$selected?>><?=$section?></option>
						<? endforeach; ?>
					<? endif ?>
				</select>
			</div>
		</td>
	</tr>
<? $tabControl->EndCustomField('IBLOCK_TYPE_ID'); ?>