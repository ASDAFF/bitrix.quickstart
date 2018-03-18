<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<table width = "100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<?if ($arResult["SHOW_POST_FORM"] == "Y"):?>
<td align=center width="50%">
<form action="<?=$arResult["FORM_ACTION"]?>" method="POST">
<?=bitrix_sessid_post();?>
<input type="hidden" name="PostAction" value="<?="Add"?>" />
<input type="hidden" name="ID" value="<?=(isset($arResult["POST_SUB"]["ID"]))?$arResult["POST_SUB"]["ID"]:0?>" />
	<table width = "250px" class="my-table" border="0" cellpadding="0" cellspacing="0">
		<thead>
		<tr>
			<td class="head-centered-content"><b><?=GetMessage('POST_DISPATCHES')?></b></td>	
		</tr>
		</thead>
		<tbody>
		<?foreach($arResult["ALL_RUBRICS"]["EMAIL"] as $itemID => $itemValue):?>
			<?if ($arResult["SHOW_ALL"] != "Y"):?>
				<?if (in_array($itemValue["ID"],$arResult["SHOWED_RUBS"])):?>
					<tr>
						<td><input <?=in_array($itemValue["ID"],$arResult["RUBRICS_POST"]) ? 'DISABLED' : ''?> type="checkbox" name="RUB_ID[]" id="RUB_ID_<?=$itemID?>" value="<?=$itemValue["ID"]?>" 
						<?if (in_array($itemValue["ID"],$arResult["RUBRICS_POST"])):?>checked<?else:?><?endif;?>/>
						<label for="sf_RUB_ID_<?=$itemID?>"><?=$itemValue["NAME"]?></label><?=in_array($itemValue["ID"],$arResult["RUBRICS_POST"]) ? '<input name="RUB_ID[]" type="hidden" value="'.$itemValue["ID"].'">' : ''?></td>
					</tr>
				<?endif;?>
			<?else:?>
					<tr>
						<td><input <?=in_array($itemValue["ID"],$arResult["RUBRICS_POST"]) ? 'DISABLED' : ''?> type="checkbox" name="RUB_ID[]" id="RUB_ID_<?=$itemID?>" value="<?=$itemValue["ID"]?>" 
						<?if (in_array($itemValue["ID"],$arResult["RUBRICS_POST"])):?>checked<?else:?><?endif;?>/> 
						<label for="sf_RUB_ID_<?=$itemID?>"><?=$itemValue["NAME"]?></label><?=in_array($itemValue["ID"],$arResult["RUBRICS_POST"]) ? '<input name="RUB_ID[]" type="hidden" value="'.$itemValue["ID"].'">' : ''?></td>
					</tr>
			<?endif;?>
		<?endforeach;?>
		<tr>
			<td class = "centered-content">
				<b>e-mail:</b>
			</td>
		</tr>
		<tr>
			<td class = "centered-content">
				<input type="text" size="20" <?=($arResult["POST_SUB"]["EMAIL"] <> '' ? 'DISABLED' : 'name="sf_EMAIL"')?> value="<?=$arResult["POST_SUB"]["EMAIL"]?>" title="<?=GetMessage("SUBSCR_EMAIL_TITLE")?>" />
				<input type="hidden" <?=($arResult["POST_SUB"]["EMAIL"] <> '' ? 'name="sf_EMAIL"' : '')?> value="<?=($arResult["POST_SUB"]["EMAIL"])?>" />
			</td>
		</tr>
		<tr>
			<td class = "centered-content">
				<input type="submit" value="<?if ($arResult["SUBMIT_POST_BUTTON_NAME"] == "UPD"):?><?=GetMessage("EDIT_SUBSCR_BUTTON")?><?else:?><?=GetMessage("SUBSCR_BUTTON")?><?endif;?>" />
			</td>
		</tr>
		<?if($arResult["POST_SUB"]["CONFIRMED"] == "Y" && $USER->IsAuthorized()):?>
		<tr>
			<?if($arResult["POST_SUB"]["ACTIVE"] == "Y"):?>
				<td class = "centered-content">
					<a href = "<?=$arResult["FORM_ACTION"]?>?action=unsubscribe&ID=<?=$arResult['POST_SUB']['ID']?>"><?=GetMessage('WANNA_UNSUBSCRIBE')?></a>
				</td>
			<?else:?>
				<td class = "centered-content">
					<a href = "<?=$arResult["FORM_ACTION"]?>?action=activate&ID=<?=$arResult['POST_SUB']['ID']?>"><?=GetMessage('WANNA_SUBSCRIBE')?></a>
				</td>
			<?endif;?>
		</tr>
		<?endif;?>
		</tbody>
	</table> 
</form>
</td>
<?endif;?>

<?if ($arResult["SHOW_SMS_FORM"] == "Y"):?>
<td align=center>
	<form action="<?=$arResult["FORM_ACTION"]?>" method="POST">
	<?=bitrix_sessid_post();?>
	<input type="hidden" name="PostAction" value="<?="Add"?>" />
	<input type="hidden" name="b_SMS_SUB" value="Y" />
	<input type="hidden" name="ID" value="<?=(isset($arResult["SMS_SUB"]["ID"]))?$arResult["SMS_SUB"]["ID"]:0?>" />
	<table width = "250px" class="my-table" border="0" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<td class="head-centered-content"><b><?=GetMessage('SMS_DISPATCHES')?></b></td>    
		</tr>
	</thead>
	<tbody>
		<?foreach($arResult["ALL_RUBRICS"]["SMS"] as $itemID => $itemValue):?>
			<?if ($arResult["SHOW_ALL"] != "Y"):?>
				<?if (in_array($itemValue["ID"],$arResult["SHOWED_RUBS"])):?>
					<tr>
						<td><?=$itemValue["ID"]?><input <?=in_array($itemValue["ID"],$arResult["RUBRICS_SMS"]) ? 'DISABLED' : ''?> type="checkbox" name="RUB_ID[]" id="RUB_ID_<?=$itemID?>" value="<?=$itemValue["ID"]?>"
						<?if (in_array($itemValue["ID"],$arResult["RUBRICS_SMS"])):?>checked<?else:?><?endif;?>/>
						<label for="sf_RUB_ID_<?=$itemID?>"><?=$itemValue["NAME"]?></label><?=in_array($itemValue["ID"],$arResult["RUBRICS_SMS"]) ? '<input name="RUB_ID[]" type="hidden" value="'.$itemValue["ID"].'">' : ''?></td>
					</tr>
				<?endif;?>
			<?else:?>
				<tr>
					<td><input <?=in_array($itemValue["ID"],$arResult["RUBRICS_SMS"]) ? 'DISABLED' : ''?> type="checkbox" name="RUB_ID[]" id="RUB_ID_<?=$itemID?>" value="<?=$itemValue["ID"]?>" 
						<?if (in_array($itemValue["ID"],$arResult["RUBRICS_SMS"])):?>checked<?else:?><?endif;?>/> 
						<label for="sf_RUB_ID_<?=$itemID?>"><?=$itemValue["NAME"]?></label><?=in_array($itemValue["ID"],$arResult["RUBRICS_SMS"]) ? '<input name="RUB_ID[]" type="hidden" value="'.$itemValue["ID"].'">' : ''?></td>
				</tr>
			<?endif;?>
		<?endforeach;?>
		<tr>
			<td class = "centered-content">
				<b><?=GetMessage("TEL_NUM")?></b>
			</td>
		</tr>
		<tr>
			<td class = "centered-content">
				<input type="text" size="20" <?=($arResult["SMS_SUB"]["EMAIL"] <> '' ? 'DISABLED' : 'name="sf_EMAIL"')?> value="<?=kill_post_fix($arResult["SMS_SUB"]["EMAIL"])?>" title="<?=GetMessage("SUBSCR_SMS_TITLE")?>" />
				<input type="hidden" <?=($arResult["SMS_SUB"]["EMAIL"] <> '' ? 'name="sf_EMAIL"' : '')?> value="<?=($arResult["SMS_SUB"]["EMAIL"])?>" />
				<input name="FORMAT" type="hidden" value="text">
			</td>
		</tr>
		<tr>
			<td class = "centered-content">
				<input type="submit" value="<?if ($arResult["SUBMIT_SMS_BUTTON_NAME"] == "UPD"):?><?=GetMessage("EDIT_SUBSCR_BUTTON")?><?else:?><?=GetMessage("SUBSCR_BUTTON")?><?endif;?>" />
			</td>
		</tr>
		<?if($arResult["SMS_SUB"]["CONFIRMED"] == "Y" && $USER->IsAuthorized()):?>
		<tr>
			<?if($arResult["SMS_SUB"]["ACTIVE"] == "Y"):?>
				<td class = "centered-content">
					<a href = "<?=$arResult["FORM_ACTION"]?>?action=unsubscribe&ID=<?=$arResult['SMS_SUB']['ID']?>"><?=GetMessage('WANNA_UNSUBSCRIBE')?></a>
				</td>
			<?else:?>
				<td class = "centered-content">
					<a href = "<?=$arResult["FORM_ACTION"]?>?action=activate&ID=<?=$arResult['SMS_SUB']['ID']?>"><?=GetMessage('WANNA_SUBSCRIBE')?></a>         
				</td>
			<?endif;?>
		</tr>
		<?endif;?>
		</tbody>
	</table> 
</form>
</td>
<?endif;?>
</tr>
</table>