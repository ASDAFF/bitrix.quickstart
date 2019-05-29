<?
IncludeModuleLangFile(__FILE__);
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
<?=bitrix_sessid_post()?>
<input type="hidden" name="lang" value="<?echo LANG?>">
<input type="hidden" name="id" value="westpower.auction">
<input type="hidden" name="install" value="Y">
<input type="hidden" name="step" value="2">

<table class="adm-detail-content-table edit-table">
<tr>
	<td width="40%" class="adm-detail-content-cell-l"><?=GetMessage('BAY_CATALOG_ID')?>:</td>
	<td width="60%" class="adm-detail-content-cell-r"><input type="text" name="CATALOG_ID" value=""></td>
</tr>
<tr>
	<td width="40%" class="adm-detail-content-cell-l"><?=GetMessage('BAY_SITE_ID')?>:</td>
	<td width="60%" class="adm-detail-content-cell-r">
	<select name="SITE_ID">
	<?
		$rsSites = CSite::GetList($by="id", $order="asc", array("ACTIVE" => "Y"));
		while ($arSite = $rsSites->Fetch())
		{
		?>
			<option value="<?=$arSite["LID"]?>"><?=$arSite["NAME"]?></option>
		<?
		}
	?>
	</select>
	</td>
</tr>
<tr>
	<td width="40%" class="adm-detail-content-cell-l"></td>
	<td width="60%" class="adm-detail-content-cell-r"><input type="submit" name="btn_save" value="<?=GetMessage('BAY_BTN_SAVE')?>"></td>
</tr>
</table>