<?php
IncludeModuleLangFile(__FILE__);

$propertyList = $profileUtils->createFieldset2($arProfile['IBLOCK_ID'], true);
$catalogPrice = array('CATALOG' => $propertyList['CATALOG']);
unset($catalogPrice['CATALOG']['QUANTITY']);
$propertyList = $profileUtils->selectFieldset2($propertyList, '');
$catalogPrice = $profileUtils->selectFieldset2($catalogPrice, $arProfile['VARIANT']['PRICE']);

$variantChecked = $arProfile['USE_VARIANT'] == 'Y' ? 'checked="checked"' : '';
?>

<tr>
	<td width="50%">
        <span id="hint_PROFILE[USE_VARIANT]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[USE_VARIANT]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_VARIANT_LIST_USE_VARIANT_HELP" )?>' );</script>
        <?=GetMEssage('ACRIT_EXPORTPRO_VARIANT_LIST_USE_VARIANT')?>
    </td>
	<td width="50%">
		<input type="checkbox" value="Y" name="PROFILE[USE_VARIANT]" <?=$variantChecked?> />
	</td>
</tr>
<tr align="center" class="heading">
	<td colspan="2"><?=GetMessage('ACRIT_EXPORTPRO_VARIANT_LIST_SETTINGS')?></td>
</tr>
<tr align="center">
	<td colspan="2">
		<?
			echo BeginNote();
			echo GetMessage('ACRIT_EXPORTPRO_VARIANT_LIST_SEX_DESCRIPTION');
			echo EndNote();
		?>
	</td>
</tr>
<tr>
	<td width="50%">
        <span id="hint_PROFILE[VARIANT][SEX]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[VARIANT][SEX]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_VARIANT_LIST_SEX_HELP" )?>' );</script>
        <?=GetMessage('ACRIT_EXPORTPRO_VARIANT_LIST_SEX')?>
    </td>
	<td width="50%">
		<input type="text" name="PROFILE[VARIANT][SEX]" size="50" data-value="PROFILE[VARIANT][SEX_VALUE]" value="<?=$arProfile['VARIANT']['SEX']?>" onclick="ShowPropertyList(this)" />
		<input type="hidden" name="PROFILE[VARIANT][SEX_VALUE]" value="<?=$arProfile['VARIANT']['SEX_VALUE']?>"/>
	</td>
</tr>
<tr>
	<td width="50%">
        <span id="hint_PROFILE[VARIANT][SEXOFFER]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[VARIANT][SEXOFFER]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_VARIANT_LIST_SEX_OFFER_HELP" )?>' );</script>
        <?=GetMessage('ACRIT_EXPORTPRO_VARIANT_LIST_SEX_OFFER')?>
    </td>
	<td width="50%">
		<input type="text" name="PROFILE[VARIANT][SEXOFFER]" size="50" data-value="PROFILE[VARIANT][SEXOFFER_VALUE]" value="<?=$arProfile['VARIANT']['SEXOFFER']?>" onclick="ShowPropertyList(this)" />
		<input type="hidden" name="PROFILE[VARIANT][SEXOFFER_VALUE]" value="<?=$arProfile['VARIANT']['SEXOFFER_VALUE']?>"/>
	</td>
</tr>
<tr>
    <td width="50%">
        <span id="hint_PROFILE[VARIANT][SEX_CONST]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[VARIANT][SEX_CONST]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_VARIANT_LIST_SEX_CONST_HELP" )?>' );</script>
        <?=GetMessage('ACRIT_EXPORTPRO_VARIANT_LIST_SEX_CONST')?>
    </td>
    <td width="50%">
        <input type="text" name="PROFILE[VARIANT][SEX_CONST]" size="50" value="<?=$arProfile['VARIANT']['SEX_CONST']?>" />
    </td>
</tr>
<tr align="center">
	<td colspan="2" style="width: 200%; height: 205px; display: inline-block;">
		<?
			echo BeginNote();
			echo GetMessage('ACRIT_EXPORTPRO_VARIANT_LIST_COLOR_DESCRIPTION');
			echo EndNote();
		?>
	</td>
</tr>
<tr>
	<td width="50%">
        <span id="hint_PROFILE[VARIANT][COLOR]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[VARIANT][COLOR]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_VARIANT_LIST_COLOR_HELP" )?>' );</script>
        <?=GetMessage('ACRIT_EXPORTPRO_VARIANT_LIST_COLOR')?>
    </td>
	<td width="50%">
		<input type="text" name="PROFILE[VARIANT][COLOR]" size="50" data-value="PROFILE[VARIANT][COLOR_VALUE]" value="<?=$arProfile['VARIANT']['COLOR']?>" onclick="ShowPropertyList(this)" />
		<input type="hidden" name="PROFILE[VARIANT][COLOR_VALUE]" value="<?=$arProfile['VARIANT']['COLOR_VALUE']?>"/>
	</td>
</tr>
<tr>
	<td width="50%">
        <span id="hint_PROFILE[VARIANT][COLOROFFER]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[VARIANT][COLOROFFER]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_VARIANT_LIST_COLOR_OFFER_HELP" )?>' );</script>
        <?=GetMessage('ACRIT_EXPORTPRO_VARIANT_LIST_COLOR_OFFER')?>
    </td>
	<td width="50%">
		<input type="text" name="PROFILE[VARIANT][COLOROFFER]" size="50" data-value="PROFILE[VARIANT][COLOROFFER_VALUE]" value="<?=$arProfile['VARIANT']['COLOROFFER']?>" onclick="ShowPropertyList(this)" />
		<input type="hidden" name="PROFILE[VARIANT][COLOROFFER_VALUE]" value="<?=$arProfile['VARIANT']['COLOROFFER_VALUE']?>"/>
	</td>
</tr>
<tr>
	<td width="50%">
        <span id="hint_PROFILE[VARIANT][SIZE]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[VARIANT][SIZE]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_VARIANT_LIST_SIZE_HELP" )?>' );</script>
        <?=GetMessage('ACRIT_EXPORTPRO_VARIANT_LIST_SIZE')?>
    </td>
	<td width="50%">
		<input type="text" name="PROFILE[VARIANT][SIZE]" size="50" data-value="PROFILE[VARIANT][SIZE_VALUE]" value="<?=$arProfile['VARIANT']['SIZE']?>" onclick="ShowPropertyList(this)" />
		<input type="hidden" name="PROFILE[VARIANT][SIZE_VALUE]" value="<?=$arProfile['VARIANT']['SIZE_VALUE']?>"/>
	</td>
</tr>
<tr>
	<td width="50%">
        <span id="hint_PROFILE[VARIANT][SIZEOFFER]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[VARIANT][SIZEOFFER]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_VARIANT_LIST_SIZE_OFFER_HELP" )?>' );</script>
        <?=GetMessage('ACRIT_EXPORTPRO_VARIANT_LIST_SIZE_OFFER')?>
    </td>
	<td width="50%">
		<input type="text" name="PROFILE[VARIANT][SIZEOFFER]" size="50" data-value="PROFILE[VARIANT][SIZEOFFER_VALUE]" value="<?=$arProfile['VARIANT']['SIZEOFFER']?>" onclick="ShowPropertyList(this)" />
		<input type="hidden" name="PROFILE[VARIANT][SIZEOFFER_VALUE]" value="<?=$arProfile['VARIANT']['SIZEOFFER_VALUE']?>"/>
	</td>
</tr>
<tr>
    <td width="50%">
        <span id="hint_PROFILE[VARIANT][WEIGHT]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[VARIANT][WEIGHT]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_VARIANT_LIST_WEIGHT_HELP" )?>' );</script>
        <?=GetMessage('ACRIT_EXPORTPRO_VARIANT_LIST_WEIGHT')?>
    </td>
    <td width="50%">
        <input type="text" name="PROFILE[VARIANT][WEIGHT]" size="50" data-value="PROFILE[VARIANT][WEIGHT_VALUE]" value="<?=$arProfile['VARIANT']['WEIGHT']?>" onclick="ShowPropertyList(this)" />
        <input type="hidden" name="PROFILE[VARIANT][WEIGHT_VALUE]" value="<?=$arProfile['VARIANT']['WEIGHT_VALUE']?>"/>
    </td>
</tr>
<tr>
    <td width="50%">
        <span id="hint_PROFILE[VARIANT][WEIGHTOFFER]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[VARIANT][WEIGHTOFFER]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_VARIANT_LIST_WEIGHT_OFFER_HELP" )?>' );</script>
        <?=GetMessage('ACRIT_EXPORTPRO_VARIANT_LIST_WEIGHT_OFFER')?>
    </td>
    <td width="50%">
        <input type="text" name="PROFILE[VARIANT][WEIGHTOFFER]" size="50" data-value="PROFILE[VARIANT][WEIGHTOFFER_VALUE]" value="<?=$arProfile['VARIANT']['WEIGHTOFFER']?>" onclick="ShowPropertyList(this)" />
        <input type="hidden" name="PROFILE[VARIANT][WEIGHTOFFER_VALUE]" value="<?=$arProfile['VARIANT']['WEIGHTOFFER_VALUE']?>"/>
    </td>
</tr>
<tr align="center" class="heading">
	<td colspan="2"><?=GetMessage('ACRIT_EXPORTPRO_VARIANT_LIST_PRICE_TITLE')?></td>
</tr>
<tr>
	<td width="50%">
        <span id="hint_PROFILE[VARIANT][PRICE]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[VARIANT][PRICE]' ), '<?=GetMessage( "ACRIT_EXPORTPRO_VARIANT_LIST_PRICE_HELP" )?>' );</script>
        <?=GetMessage('ACRIT_EXPORTPRO_VARIANT_LIST_PRICE')?>
    </td>
	<td width="50%">
		<select name="PROFILE[VARIANT][PRICE]">
		<?
			echo implode("\n", $catalogPrice);
			unset($catalogPrice);
		?>
		</select>
	</td>
</tr>
<tr align="center" class="heading">
	<td colspan="2"><?=GetMessage('ACRIT_EXPORTPRO_VARIANT_LIST_CATEGORY')?></td>
</tr>                    

<?$categories = $profileUtils->GetSections($arProfile['IBLOCK_ID'], true);?>

<?foreach($categories as $id => $category):?>
<tr>
	<td><?=$category['NAME']?></td>
	<td>
		<?
			$variantCategory = CExportproVariant::GetCategorySelect(array(
				'NAME' => "PROFILE[VARIANT][CATEGORY][{$category['ID']}]",
				'DEFAULT' => $arProfile['VARIANT']['CATEGORY'][$category['ID']],
			));
			echo $variantCategory;
		?>
	</td>
</tr>
<tr>
	<td><?=$category['NAME']?> <?=GetMessage('ACRIT_EXPORTPRO_VARIANT_LIST_CATEGORY_EXT')?></td>
	<td>
		<?
			$variantCategory = CExportproVariant::GetCategorySelect(array(
				'NAME' => "PROFILE[VARIANT][CATEGORY_EXT][{$category['ID']}]",
				'DEFAULT' => $arProfile['VARIANT']['CATEGORY_EXT'][$category['ID']],
			));
			echo $variantCategory;
		?>
	</td>
</tr>
<?endforeach?>

<div id="property_list" style="display: none">
	<select size="35">
	<?
		echo implode("\n", $propertyList);
		unset($propertyList);
	?>
	</select>
</div>
