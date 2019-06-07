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

$elementsFilter = $profile['ELEMENTS_FILTER'];
$offersFilter   = $profile['OFFERS_FILTER'];

//echo "<pre>"; print_r($elementsFilter);echo "</pre>";
//CAdminCalendar::ShowScript();
?>
<? $tabControl->BeginCustomField('PROFILE_TAB4', ''); ?>
	<tr class="heading" align="center">
		<td colspan="2"><?=Loc::getMessage('AEAE_TAB_HEADING_ELEMENTS_FILTER')?></td>
	</tr>
	<tr>
		<td width="50%"><?=Tools::showHint('AEAE_TAB_ELEMENTS_FILTER_ACTIVE')?>ACTIVE</td>
		<td width="50%">
			<input name="PROFILE[ELEMENTS_FILTER][ACTIVE]" value="Y" type="checkbox"<?=($elementsFilter['ACTIVE'] == 'Y' ? ' checked' : '')?>>
		</td>
	</tr>
	<tr>
		<td width="50%"><?=Tools::showHint('AEAE_TAB_ELEMENTS_FILTER_ACTIVE_DATE')?>ACTIVE_DATE</td>
		<td width="50%">
			<input name="PROFILE[ELEMENTS_FILTER][ACTIVE_DATE]" value="Y" type="checkbox"<?=($elementsFilter['ACTIVE_DATE'] == 'Y' ? ' checked' : '')?>>
		</td>
	</tr>
	<tr>
		<td width="50%"><?=Tools::showHint('AEAE_TAB_ELEMENTS_FILTER_SECTION_ACTIVE')?>SECTION_ACTIVE</td>
		<td width="50%">
			<input name="PROFILE[ELEMENTS_FILTER][SECTION_ACTIVE]" value="Y" type="checkbox"<?=($elementsFilter['SECTION_ACTIVE'] == 'Y' ? ' checked' : '')?>>
		</td>
	</tr>
	<tr>
		<td width="50%"><?=Tools::showHint('AEAE_TAB_ELEMENTS_FILTER_SECTION_GLOBAL_ACTIVE')?>SECTION_GLOBAL_ACTIVE</td>
		<td width="50%">
			<input name="PROFILE[ELEMENTS_FILTER][SECTION_GLOBAL_ACTIVE]" value="Y" type="checkbox"<?=($elementsFilter['SECTION_GLOBAL_ACTIVE'] == 'Y' ? ' checked' : '')?>>
		</td>
	</tr>
	<tr>
		<td width="50%"><?=Tools::showHint('AEAE_TAB_ELEMENTS_FILTER_CATALOG_AVAILABLE')?>CATALOG_AVAILABLE</td>
		<td width="50%">
			<input name="PROFILE[ELEMENTS_FILTER][CATALOG_AVAILABLE]" value="Y" type="checkbox"<?=($elementsFilter['CATALOG_AVAILABLE'] == 'Y' ? ' checked' : '')?>>
		</td>
	</tr>

<? if($bCatalog): ?>
	<tr id="tr_ELEMENTS_CONDITION">
		<td colspan="2">
			<div id="ELEMENTS_CONDITION" style="position: relative; z-index: 1;"></div>
			<?
			$obCond   = new CCatalogCondTree();
			$boolCond = $obCond->Init(
				 BT_COND_MODE_DEFAULT,
				 BT_COND_BUILD_CATALOG,
				 array(
						'FORM_NAME' => 'profile_form',
						'CONT_ID'   => 'ELEMENTS_CONDITION',
						'JS_NAME'   => 'JSCatCond',
						'PREFIX'    => 'PROFILE[ELEMENTS_CONDITION]',
				 )
			);
			if(!$boolCond) {
				if($ex = $APPLICATION->GetException())
					echo $ex->GetString() . "<br>";
			}
			else {
				$obCond->Show($profile['ELEMENTS_CONDITION']);
			}
			?>
		</td>
	</tr>
<? endif ?>

	<tr class="heading" align="center">
		<td colspan="2"><?=Loc::getMessage('AEAE_TAB_HEADING_OFFERS_FILTER')?></td>
	</tr>
	<tr>
		<td><?=Tools::showHint('AEAE_TAB_OFFERS_FILTER_ACTIVE')?>ACTIVE</td>
		<td>
			<input name="PROFILE[OFFERS_FILTER][ACTIVE]" value="Y" type="checkbox"<?=($offersFilter['ACTIVE'] == 'Y' ? ' checked' : '')?>>
		</td>
	</tr>
	<tr>
		<td><?=Tools::showHint('AEAE_TAB_OFFERS_FILTER_ACTIVE_DATE')?>ACTIVE_DATE</td>
		<td>
			<input name="PROFILE[OFFERS_FILTER][ACTIVE_DATE]" value="Y" type="checkbox"<?=($offersFilter['ACTIVE_DATE'] == 'Y' ? ' checked' : '')?>>
		</td>
	</tr>
	<tr>
		<td><?=Tools::showHint('AEAE_TAB_OFFERS_FILTER_CATALOG_AVAILABLE')?>CATALOG_AVAILABLE</td>
		<td>
			<input name="PROFILE[OFFERS_FILTER][CATALOG_AVAILABLE]" value="Y" type="checkbox"<?=($offersFilter['CATALOG_AVAILABLE'] == 'Y' ? ' checked' : '')?>>
		</td>
	</tr>
<? if($bCatalog): ?>
	<tr id="tr_OFFERS_CONDITIONS">
		<td colspan="2">
			<div id="OFFERS_CONDITION" style="position: relative; z-index: 1;"></div>
			<?
			$obCond   = new CCatalogCondTree();
			$boolCond = $obCond->Init(
				 BT_COND_MODE_DEFAULT,
				 BT_COND_BUILD_CATALOG,
				 array(
						'FORM_NAME' => 'profile_form',
						'CONT_ID'   => 'OFFERS_CONDITION',
						'JS_NAME'   => 'JSCatCond',
						'PREFIX'    => 'PROFILE[OFFERS_CONDITION]',
				 )
			);
			if(!$boolCond) {
				if($ex = $APPLICATION->GetException())
					echo $ex->GetString() . "<br>";
			}
			else {
				$obCond->Show($profile['OFFERS_CONDITION']);
			}
			?>
		</td>
	</tr>
<? endif ?>

<? $tabControl->EndCustomField('PROFILE_TAB4'); ?>