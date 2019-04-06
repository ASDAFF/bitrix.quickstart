<?
	$arSites= array();
	$db_res = CSite::GetList(($b = ""), ($o = ""), array("ACTIVE" => "Y"));
	if ($db_res && ($res = $db_res->Fetch())){
		do{
			$arSites[] = array("SITE_ID" => $res["LID"], "NAME" => $res["NAME"]);
		}while ($res = $db_res->Fetch());
	}

?>
<form action="<?echo $APPLICATION->GetCurPage()?>" name="form1">
<?=bitrix_sessid_post()?>
<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
<input type="hidden" name="id" value="mystery.thumbs">
<input type="hidden" name="install" value="Y">
<input type="hidden" name="step" value="2">
	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="list-table">
	<tr>
		<td><?= GetMessage("MYSTERY_THUMBS_NS_INSTALL_SITE")?>:</td>
		<td><select name="SITE_ID">
			<?foreach ($arSites as $res):?>
				<option value="<?=$res['SITE_ID']?>"><?=$res['NAME']?></option>
			<?endforeach;?>
		</td>
	</tr>

	</table>
	<input type="submit" name="inst" value="<?= GetMessage("MOD_INSTALL")?>">
</form>