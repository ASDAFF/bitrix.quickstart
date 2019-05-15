<?
if(!check_bitrix_sessid()) return;
IncludeModuleLangFile(__FILE__);

if($ex = $APPLICATION->GetException()){
	echo CAdminMessage::ShowMessage(Array(
		"TYPE" => "ERROR",
		"MESSAGE" => GetMessage("MOD_INST_ERR"),
		"DETAILS" => $ex->GetString(),
		"HTML" => true
	));
}
else{
	echo CAdminMessage::ShowNote(GetMessage("MOD_INST_OK"));
}
?>
<?if(strlen($_REQUEST["public_dir"]) > 0):?>
	<p><?=GetMessage("MOD_DEMO_DIR")?></p>
	<table border="0" cellspacing="0" cellpadding="3">
		<tr>
			<td align="center"><p><b><?=GetMessage("MOD_DEMO_SITE")?></b></p></td>
			<td align="center"><p><b><?=GetMessage("MOD_DEMO_LINK")?></b></p></td>
		</tr>
		<?
		$sites = CSite::GetList($by, $order, Array("ACTIVE"=>"Y"));
		?>
		<?while($site = $sites->Fetch()):?>
			<tr>
				<td width="0%"><p>[<?=$site["ID"]?>] <?=$site["NAME"]?></p></td>
				<td width="0%"><p><a href="<?if(strlen($site["SERVER_NAME"]) > 0) echo "http://".$site["SERVER_NAME"];?><?=$site["DIR"].$public_dir?>/index.php"><?=$site["DIR"].$public_dir?>/index.php</a></p></td>
			</tr>
		<?endwhile;?>
	</table>
<?endif;?>
<form action="<?=$APPLICATION->GetCurPage();?>">
	<input type="hidden" name="lang" value="<?=LANG;?>">
	<input type="submit" name="" value="<?=GetMessage("MOD_BACK");?>">
<form>