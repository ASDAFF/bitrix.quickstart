<?
	if(!check_bitrix_sessid()) return;

	echo CAdminMessage::ShowNote(GetMessage("INSTALL_MESSAGE"));
?>