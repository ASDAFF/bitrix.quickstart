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
$tabControl->AddSection('PROFILE[HEADING_SHOP]', Loc::getMessage('AEAE_TAB_HEADING_SHOP_DESCRIPTION'));
$tabControl->AddEditField('PROFILE[SHOP_NAME]', $arFieldTitle['SHOP_NAME'], true, array('size' => 80), $profile['SHOP_NAME']);
$tabControl->AddEditField('PROFILE[SHOP_COMPANY]', $arFieldTitle['SHOP_COMPANY'], true, array('size' => 80), $profile['SHOP_COMPANY']);
$tabControl->AddEditField('PROFILE[SHOP_URL]', $arFieldTitle['SHOP_URL'], true, array('size' => 80), $profile['SHOP_URL']);

$tabControl->AddSection('PROFILE[HEADING_PRICE]', Loc::getMessage('AEAE_TAB_HEADING_PRICE_TYPE'));
?>
<? $tabControl->BeginCustomField('PROFILE[PRICE_TYPE]', $arFieldTitle['PRICE_TYPE'], true); ?>
<tr>
	<td><?=$tabControl->GetCustomLabelHTML()?></td>
	<td>
		<? if($arPriceTypes): ?>
			<select name="PROFILE[PRICE_TYPE]" size="<?=count($arPriceTypes)?>">
				<? foreach($arPriceTypes as $priceId => $priceName): ?>
					<? $selected = ($priceId == $profile['PRICE_TYPE'] ? ' selected' : ''); ?>
					<option value="<?=$priceId?>"<?=$selected?>>[<?=$priceId?>] <?=$priceName?></option>
				<? endforeach ?>
			</select>
		<? endif ?>
	</td>
</tr>
<? $tabControl->EndCustomField('PROFILE[PRICE_TYPE]'); ?>

<?
$tabControl->AddCheckBoxField('PROFILE[PRICE_VAT_INCLUDE]', $arFieldTitle['PRICE_VAT_INCLUDE'], false, array('Y','N'), $profile['PRICE_VAT_INCLUDE'] != 'N');
$tabControl->AddCheckBoxField('PROFILE[CONVERT_CURRENCY]', $arFieldTitle['CONVERT_CURRENCY'], false, array('Y','N'), $profile['CONVERT_CURRENCY'] != 'N');

$tabControl->BeginCustomField('PROFILE[CURRENCY_ID]', $arFieldTitle['CURRENCY_ID']); ?>
<tr>
	<td><?=$tabControl->GetCustomLabelHTML()?></td>
	<td>
		<? if($arCurrency): ?>
			<select name="PROFILE[CURRENCY_ID]" size="<?=count($arPriceTypes)?>">
				<? foreach($arCurrency as $code => $name): ?>
					<? $selected = ($code == $profile['CURRENCY_ID'] ? ' selected' : ''); ?>
					<option value="<?=$code?>"<?=$selected?>><?=$name?></option>
				<? endforeach ?>
			</select>
		<? endif ?>
	</td>
</tr>
<?
$tabControl->EndCustomField('PROFILE[CURRENCY_ID]');
?>


<? $tabControl->BeginCustomField('PROFILE[CURRENCY]', $arFieldTitle['CURRENCY'], true); ?>
<tr class="heading" align="center">
	<td colspan="2"><?=Tools::showHint('AEAE_TAB_HEADING_SHOP_CURRENCY')?><?=Loc::getMessage('AEAE_TAB_HEADING_SHOP_CURRENCY')?></td>
</tr>
<tr>
	<td colspan="2">
		<table align="center" width="100%">
			<thead>
			<tr class="heading">
				<td align="center" colspan="3"><?=Loc::getMessage('AEAE_TAB_HEADING_SHOP_CURRENCY_CODE')?></td>
				<td align="center"><?=Loc::getMessage('AEAE_TAB_HEADING_SHOP_CURRENCY_RATE')?></td>
				<td align="center"><?=Loc::getMessage('AEAE_TAB_HEADING_SHOP_CURRENCY_PLUS')?></td>
			</tr>
			</thead>
			<tbody>
			<? if($arCurrency): ?>
				<? foreach($arCurrency as $id => $fullName): ?>
					<?
					if(!isset($profile['CURRENCY'][ $id ]))
						$profile['CURRENCY'][ $id ] = array();
					?>
					<tr>
						<td>
							<?
							$checked = ($profile['CURRENCY'][ $id ]['ACTIVE'] == 'Y' ? ' checked' : '');
							?>
							<input type="checkbox" name="PROFILE[CURRENCY][<?=$id?>][ACTIVE]" value="Y"<?=$checked?>>
						</td>
						<td align="center">
							<?
							$convertFrom = ($profile['CURRENCY'][ $id ]['CONVERT_FROM'] ? $profile['CURRENCY'][ $id ]['CONVERT_FROM'] : $id);
							?>
							<input type="text" name="PROFILE[CURRENCY][<?=$id?>][CONVERT_FROM]" value="<?=$convertFrom?>" readonly>
						</td>
						<td><?=$fullName?></td>
						<td align="center">
							<select name="PROFILE[CURRENCY][<?=$id?>][RATE]">
								<? foreach($arCurrencyRates as $rate => $name): ?>
									<?
									$selected = ($rate == $profile['CURRENCY'][ $id ]['RATE'] ? ' selected' : '');
									?>
									<option value="<?=$rate?>"<?=$selected?>><?=$name?></option>
								<? endforeach ?>
							</select>
						</td>
						<td align="center">+&nbsp;<input type="text" size="5" name="PROFILE[CURRENCY][<?=$id?>][PLUS]" value="<?=$profile['CURRENCY'][ $id ]['PLUS']?>">&nbsp;%
						</td>
					</tr>
				<? endforeach; ?>
			<? endif ?>
			</tbody>
		</table>
	</td>
</tr>
<? $tabControl->EndCustomField('PROFILE[CURRENCY]'); ?>

<? $tabControl->BeginCustomField('PROFILE[DELIVERY]', $arFieldTitle['DELIVERY'], true); ?>
<tr class="heading" align="center">
	<td colspan="2">
		<?=Tools::showHint('AEAE_TAB_HEADING_DELIVERY_OPTIONS')?><?=Loc::getMessage('AEAE_TAB_HEADING_DELIVERY_OPTIONS')?>
	</td>
</tr>
<tr align="center">
	<td colspan="2">
		<?=BeginNote()?>
		<?=Loc::getMessage('AEAE_TAB_HEADING_DELIVERY_NOTE')?>
		<?=EndNote()?>
	</td>
</tr>
<tr align="center">
	<td colspan="2">
		<div class="copy_inner">
			<? foreach($profile['DELIVERY']['cost'] as $key => $val): ?>
				<?
				$cost         = $profile['DELIVERY']['cost'][ $key ];
				$days         = $profile['DELIVERY']['days'][ $key ];
				$order_before = $profile['DELIVERY']['order_before'][ $key ];
				?>
				<div class="copy_row">
					<div class="selectors">
						cost= <input type="text" name="PROFILE[DELIVERY][cost][]" value="<?=$cost?>" size="5">
						&nbsp;
						days= <input type="text" name="PROFILE[DELIVERY][days][]" value="<?=$days?>" size="5">
						&nbsp;
						order-before= <select name="PROFILE[DELIVERY][order_before][]">
							<? for($i = 0; $i <= 24; $i++): ?>
								<? $selected = ($i == $order_before ? 'selected' : ''); ?>
								<option value="<?=$i?>" <?=$selected?>><?=$i?></option>
							<? endfor ?>
						</select>
					</div>
					<div class="controls">
						<button type="button" class="adm-btn adm-btn-icon adm-btn-add"></button><button type="button" class="adm-btn adm-btn-icon adm-btn-delete"></button>
					</div>
				</div>
			<? endforeach; ?>
		</div>
	</td>
</tr>
<? $tabControl->EndCustomField('PROFILE[DELIVERY]'); ?>


<? $tabControl->BeginCustomField('PROFILE[DIMENSIONS]', $arFieldTitle['DIMENSIONS'], true); ?>
<tr class="heading" align="center">
	<td colspan="2">
		<?=Tools::showHint('AEAE_TAB_HEADING_DIMENSIONS')?><?=Loc::getMessage('AEAE_TAB_HEADING_DIMENSIONS')?>
	</td>
</tr>
<tr align="center">
	<td colspan="2">
		<?=BeginNote()?>
		<?=Loc::getMessage('AEAE_TAB_HEADING_DIMENSIONS_NOTE')?>
		<?=EndNote()?>
	</td>
</tr>
<tr align="center">
	<td colspan="2">
		<input type="text" name="PROFILE[DIMENSIONS]" value="<?=$profile['DIMENSIONS']?>" size="40" placeholder="#LENGTH#/#WIDTH#/#HEIGHT#">
	</td>
</tr>
<? $tabControl->EndCustomField('PROFILE[DIMENSIONS]'); ?>


<? $tabControl->BeginCustomField('PROFILE[UTM_TAGS]', $arFieldTitle['UTM_TAGS'], true); ?>
<tr class="heading" align="center">
	<td colspan="2">
		<?=Tools::showHint('AEAE_TAB_HEADING_UTM_TAGS')?><?=Loc::getMessage('AEAE_TAB_HEADING_UTM_TAGS')?>
	</td>
</tr>
<tr align="center">
	<td colspan="2">
		<div style="display: inline-block; text-align: left">
			<?=BeginNote()?>
			<?=Loc::getMessage('AEAE_TAB_HEADING_UTM_TAGS_NOTE')?>
			<?=EndNote()?>
		</div>
	</td>
</tr>
<tr align="center">
	<td colspan="2">
		<div class="copy_inner">
			<?
			//Default value
			if(!$profile['UTM_TAGS']){
				$profile['UTM_TAGS'] = array(
					 'NAME' => array(0 => ''),
					 'VALUE' => array(0 => '')
        );
			}
			?>
			<? foreach($profile['UTM_TAGS']['NAME'] as $pKey => $pName): ?>
				<?
				$pValue = $profile['UTM_TAGS']['VALUE'][ $pKey ];
				?>
				<div class="copy_row">
					<div class="selectors">
						<select name="PROFILE[UTM_TAGS][NAME][]">
							<option value=""><?=Loc::getMessage('AEAE_OPTION_EMPTY')?></option>
							<option value="utm_source" <?=($pName == 'utm_source' ? 'selected' : '')?>>utm_source</option>
							<option value="utm_medium" <?=($pName == 'utm_medium' ? 'selected' : '')?>>utm_medium</option>
							<option value="utm_campaign" <?=($pName == 'utm_campaign' ? 'selected' : '')?>>utm_campaign</option>
							<option value="utm_content" <?=($pName == 'utm_content' ? 'selected' : '')?>>utm_content</option>
							<option value="utm_term" <?=($pName == 'utm_term' ? 'selected' : '')?>>utm_term</option>
						</select>&nbsp;<input type="text" name="PROFILE[UTM_TAGS][VALUE][]" value="<?=$pValue?>" size="40">
					</div>
					<div class="controls">
						<button type="button" class="adm-btn adm-btn-icon adm-btn-add"></button><button type="button" class="adm-btn adm-btn-icon adm-btn-delete"></button>
					</div>
				</div>
			<? endforeach; ?>
		</div>
	</td>
</tr>
<? $tabControl->EndCustomField('PROFILE[UTM_TAGS]'); ?>




<? $tabControl->BeginCustomField('PROFILE[STOP_WORDS]', $arFieldTitle['STOP_WORDS'], true); ?>
<tr class="heading" align="center">
	<td colspan="2">
		<?=Tools::showHint('AEAE_TAB_HEADING_STOP_WORDS')?><?=Loc::getMessage('AEAE_TAB_HEADING_STOP_WORDS')?>
	</td>
</tr>
<tr>
	<td colspan="2" align="center">
		<input type="text" name="PROFILE[STOP_WORDS]" value="<?=$profile['STOP_WORDS']?>" style="width: 100%">
	</td>
</tr>
<? $tabControl->EndCustomField('PROFILE[STOP_WORDS]'); ?>
