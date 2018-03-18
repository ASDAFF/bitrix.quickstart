<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
if ($arResult["SHOW_SMS_FORM"] && $arResult["SUBSCRIPTION"]["EMAIL"] <> '')
{
	$arResult["SUBSCRIPTION"]["EMAIL"] = kill_post_fix($arResult["SUBSCRIPTION"]["EMAIL"]);
}

//***********************************
//setting section
//***********************************

?>
<form action="<?=$arResult["FORM_ACTION"]?>" method="post">
<?=bitrix_sessid_post();?>
<hr />
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td><b><p><?=!$arResult["SHOW_SMS_FORM"] ? GetMessage('subscr_setting') : GetMessage('subscr_setting_sms')?></b>&nbsp; <input type="text" name="sf_EMAIL" value="<?=$arResult["SUBSCRIPTION"]["EMAIL"]?>" size="30" maxlength="255" /></p></td>
		<td><?=GetMessage("subscr_conf")?></td>
		<td nowrap class="<?=($arResult["SUBSCRIPTION"]["CONFIRMED"] == "Y"? "notetext":"errortext")?>"><?=($arResult["SUBSCRIPTION"]["CONFIRMED"] == "Y"? GetMessage("subscr_yes"):GetMessage("subscr_no"));?></td>
	</tr>
	<tr>
		<td></td>
		<td><?=GetMessage("subscr_act")?></td>
		<td class="<?=($arResult["SUBSCRIPTION"]["ACTIVE"] == "Y"? "notetext":"errortext")?>"><?=($arResult["SUBSCRIPTION"]["ACTIVE"] == "Y"? GetMessage("subscr_yes"):GetMessage("subscr_no"));?></td>
		<?if($arResult["SUBSCRIPTION"]["CONFIRMED"] == "Y"):?>
			<?if($arResult["SUBSCRIPTION"]["ACTIVE"] == "Y"):?>
				<td style = "padding:7px;">
					<a href = "?action=unsubscribe&ID=<?=$arResult['SUBSCRIPTION']['ID']?>"><?=GetMessage('WANNA_UNSUB')?></a>
				</td>
			<?else:?>
				<td style = "padding:7px;">
					<a href = "?action=activate&ID=<?=$arResult['SUBSCRIPTION']['ID']?>"><?=GetMessage('WANNA_SUB')?></a>
				</td>
			<?endif;?>
		<?endif;?>
	</tr>
</table>
<br / >
<hr />
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="my-table">
<thead>
	<tr>
		<td style = "text-align:left;padding-left:10px"><b><?=GetMessage('SUBSCR_HEADING')?></b></td>
		<td style = "text-align:left;padding-left:15px"><b><?=GetMessage('DESC')?></b></td>
		<td><b><?=GetMessage('SUBSCR_COUNT')?></b></td>
	</tr>
</thead>

	<?$i=0;?>
	<?foreach($arResult["RUBRICS"] as $itemID => $itemValue):?>
		<?if ($arResult["SHOW_ALL"] != "Y"):?>
			<?if (in_array($itemValue["ID"],$arResult["SHOWED_RUBS"])):?>
			<?$i++;?>
			<tr> 
				<td <?($i%2!=0)?$class='gray':$class='white'?> class = '<?=$class?>'>
					<label><input type="checkbox" name="RUB_ID[]" value="<?=$itemValue["ID"]?>"<?if($itemValue["CHECKED"]) echo " checked"?> /><?=$itemValue["NAME"]?></label><br />
				</td>
				<td <?($i%2!=0)?$class='gray':$class='white'?> class = '<?=$class?>'>
					<?=$itemValue["DESCRIPTION"]?>
				</td>
				<td style = "text-align:center;" <?($i%2!=0)?$class='gray':$class='white'?> class = '<?=$class?>'>
					<?=$itemValue["RUB_COUNT"]?>
				</td>
			</tr>
			<?endif;?>
		<?else:?>
			<?$i++;?>  
			<tr>
				<td <?($i%2!=0)?$class='gray':$class='white'?> class = '<?=$class?>'>
					<label><input type="checkbox" name="RUB_ID[]" value="<?=$itemValue["ID"]?>"<?if($itemValue["CHECKED"]) echo " checked"?> /><?=$itemValue["NAME"]?></label><br />
				</td>
				<td <?($i%2!=0)?$class='gray':$class='white'?> class = '<?=$class?>'>
					<?=$itemValue["DESCRIPTION"]?>
				</td>
				<td style = "text-align:center;" <?($i%2!=0)?$class='gray':$class='white'?> class = '<?=$class?>'>
					<?=$itemValue["RUB_COUNT"]?>
				</td>
			</tr>	
		<?endif;?> 
	<?endforeach;?></p>
<tr>
	<td></td>
	<td>
	<?
	if ($arResult["SHOW_SMS_FORM"])
	{
		echo '<input name="FORMAT" type="hidden" value="text">';
	}
	else
	{
		?>
			<label><input type="radio" name="FORMAT" value="text"<?if($arResult["SUBSCRIPTION"]["FORMAT"] == "text") echo " checked"?> /><?echo GetMessage("subscr_text")?></label>&nbsp;/&nbsp;<label><input type="radio" name="FORMAT" value="html"<?if($arResult["SUBSCRIPTION"]["FORMAT"] == "html") echo " checked"?> />HTML</label>
		<?
	}
	?>

	</td>
</tr>
<tfoot><tr><td colspan="3" align = "center">
	<input type="submit" style = "width:150px;" name="Save" value="<?=($arResult["ID"] > 0? GetMessage("subscr_upd"):GetMessage("subscr_add"))?>" />	
</td></tr></tfoot>
</table>
<input type="hidden" name="PostAction" value="<?=($arResult["ID"]>0? "Update":"Add")?>" />
<input type="hidden" name="ID" value="<?=$arResult["SUBSCRIPTION"]["ID"];?>" />
<?if($_REQUEST["register"] == "YES"):?>
	<input type="hidden" name="register" value="YES" />
<?endif;?>
<?if($_REQUEST["authorize"] == "YES"):?>
	<input type="hidden" name="authorize" value="YES" />
<?endif;?>
</form>
