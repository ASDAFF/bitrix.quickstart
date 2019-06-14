<?
AddEventHandler("main", "OnBeforeEventAdd", array("MainHandlers_iu", "OnBeforeEventAddHandler_iu"));
class MainHandlers_iu
{
	function OnBeforeEventAddHandler_iu($event, $lid, $arFields)
	{
		if ($event == "IU_FEEDBACK_FORM" && strlen($arFields["AR_FILE"]) > 0)
		{
			$arFiles_name = explode(",", $arFields["AR_FILE"]);
			$arFiles = array();
			foreach($arFiles_name as $file_n) {
				$ar_file_name = explode('*', $file_n);
				$arFiles[] = "/upload".$arFields["DIR_FILE"].$ar_file_name[0];
			}
			include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/informunity/feedback/attach/mail_attach.php");
			SendAttache($event, $lid, $arFields, $arFiles);
			$event = 'null'; $lid = 'null';
			if($arFields["DEL_FILE"] == "Y") {
				foreach($arFiles_name as $file_name) {
					$ar_file_name = explode('*', $file_name);
					if(preg_match('#^/(.+)/(.+)$#i', $ar_file_name[0], $arRes)) {
						DeleteDirFilesEx("/upload".$arFields["DIR_FILE"]."/".$arRes[1]);
						CFile::Delete($ar_file_name[1]);
					}
				}
			}
		}
	}
}
?>