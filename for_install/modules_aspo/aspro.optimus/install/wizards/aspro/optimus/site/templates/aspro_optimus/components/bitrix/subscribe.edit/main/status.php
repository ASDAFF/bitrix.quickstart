<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//***********************************
//status and unsubscription/activation section
//***********************************
?>
<form action="<?=$arResult["FORM_ACTION"]?>" method="get">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="data-table">
	<thead><tr><td colspan="3"><h4><?echo GetMessage("subscr_title_status")?></h4></td></tr></thead>
	<tr valign="top">
		<td><?echo GetMessage("subscr_conf")?></td>
		<td class="note <?echo ($arResult["SUBSCRIPTION"]["CONFIRMED"] == "Y"? "notetext":"errortext")?>"><?echo ($arResult["SUBSCRIPTION"]["CONFIRMED"] == "Y"? GetMessage("subscr_yes"):GetMessage("subscr_no"));?></td>
		<td width="60%" rowspan="5" class="text_info">
			<div class="more_text_small">
				<?if($arResult["SUBSCRIPTION"]["CONFIRMED"] <> "Y"):?>
					<?echo GetMessage("subscr_title_status_note1")?>
				<?elseif($arResult["SUBSCRIPTION"]["ACTIVE"] == "Y"):?>
					<?echo GetMessage("subscr_title_status_note2")?><br/>
					<?echo GetMessage("subscr_status_note3")?>
				<?else:?>
					<?echo GetMessage("subscr_status_note4")?><br/>
					<?echo GetMessage("subscr_status_note5")?>
				<?endif;?>
			</div>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("subscr_act")?></td>
		<td class="note <?echo ($arResult["SUBSCRIPTION"]["ACTIVE"] == "Y"? "notetext":"errortext")?>"><?echo ($arResult["SUBSCRIPTION"]["ACTIVE"] == "Y"? GetMessage("subscr_yes"):GetMessage("subscr_no"));?></td>
	</tr>
	<tr>
		<td><?echo GetMessage("adm_id")?></td>
		<td class="note"><?echo $arResult["SUBSCRIPTION"]["ID"];?>&nbsp;</td>
	</tr>
	<tr>
		<td><?echo GetMessage("subscr_date_add")?></td>
		<td class="note"><?echo $arResult["SUBSCRIPTION"]["DATE_INSERT"];?>&nbsp;</td>
	</tr>
	<tr>
		<td><?echo GetMessage("subscr_date_upd")?></td>
		<td class="note"><?echo $arResult["SUBSCRIPTION"]["DATE_UPDATE"];?>&nbsp;</td>
	</tr>
	<?if($arResult["SUBSCRIPTION"]["CONFIRMED"] == "Y"):?>
		<tfoot><tr><td colspan="3">
		<br/>
		<div class="adaptive more_text">
			<div class="more_text_small">
				<?if($arResult["SUBSCRIPTION"]["CONFIRMED"] <> "Y"):?>
					<?echo GetMessage("subscr_title_status_note1")?>
				<?elseif($arResult["SUBSCRIPTION"]["ACTIVE"] == "Y"):?>
					<?echo GetMessage("subscr_title_status_note2")?><br/>
					<?echo GetMessage("subscr_status_note3")?>
				<?else:?>
					<?echo GetMessage("subscr_status_note4")?><br/>
					<?echo GetMessage("subscr_status_note5")?>
				<?endif;?>
			</div>
		</div>
		<br/>
		<?if($arResult["SUBSCRIPTION"]["ACTIVE"] == "Y"):?>
			<input type="submit" name="unsubscribe" class="button vbig_btn" value="<?echo GetMessage("subscr_unsubscr")?>" />
			<input type="hidden" name="action" class="button vbig_btn" value="unsubscribe" />
		<?else:?>
			<input type="submit" name="activate" class="button vbig_btn" value="<?echo GetMessage("subscr_activate")?>" />
			<input type="hidden" name="action" class="button vbig_btn" value="activate" />
		<?endif;?>
		</td></tr></tfoot>
	<?endif;?>
</table>

<input type="hidden" name="ID" value="<?echo $arResult["SUBSCRIPTION"]["ID"];?>" />
<?echo bitrix_sessid_post();?>
</form>
<br />