<?php
if( !check_bitrix_sessid() ) return;
echo CAdminMessage::ShowMessage( array( "MESSAGE" => GetMessage( "MOD_UNINST_OK" ), "TYPE" => "OK" ) );
?>
<form action="<?=$APPLICATION->GetCurPage()?>" method="get">
	<p>
		<input type="hidden" name="lang" value="<?=LANG?>" />
		<input type="submit" value="<?=GetMessage( "MOD_BACK" )?>" />
	</p>
</form>