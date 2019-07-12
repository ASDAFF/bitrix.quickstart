<?
class ArealRealty_SiteWizard {

	static $last_error = "";
	static $allowed_editions = array('Старт','Стандарт','Эксперт','Малый бизнес','Бизнес','Портал','Большой бизнес');
	static $unallowed_editions = array('Первый сайт');

	function GetUpdateInfo ()
	{
		$errorMessage = "";

		$stableVersionsOnly = COption::GetOptionString("main", "stable_versions_only", "Y");
		$bLockUpdateSystemKernel = CUpdateSystem::IsInCommonKernel();

		if (!$bLockUpdateSystemKernel)
		{
			if (!$arUpdateList = CUpdateClient::GetUpdatesList($errorMessage, LANG, $stableVersionsOnly))
				$errorMessage .= GetMessage("SUP_CANT_CONNECT");
			else return $arUpdateList;
		}
		else
		{
			$errorMessage .= GetMessage("SUP_CANT_CONTRUPDATE");
		}
		self::$last_error = $errorMessage;
		return false;
	}
}

?>