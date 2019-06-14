<?
IncludeModuleLangFile(__FILE__);

class DSocialMediaPosterEventLog {
	
	static $PREFIX = "SOCIALMEDIAPOSTER";

	function GetErrorStrByCode($code)
	{
		$errorCodes = array(
			"1" => GetMessage("SOCIALMEDIAPOSTER_LOG_ERROR_1"),
			"5" => GetMessage("SOCIALMEDIAPOSTER_LOG_ERROR_5"),
			"6" => GetMessage("SOCIALMEDIAPOSTER_LOG_ERROR_6"),
			"10" => GetMessage("SOCIALMEDIAPOSTER_LOG_ERROR_10"),
		);
	
		return $errorCodes[$code];
	}

	function Add($itemID, $entityID, $errorCode, $description) {
		CEventLog::Log("EXPORT", ToUpper(self::$PREFIX."_".$entityID), DSocialMediaPoster::$MODULE_ID, $itemID, "==".base64_encode(nl2br(self::GetErrorStrByCode($errorCode)."\r\n\r\n".$description)));
	}
}

?>