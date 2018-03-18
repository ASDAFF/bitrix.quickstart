<?include $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php'?>
<?

$idElement = intval($_GET['ID']);

if ($idElement && $USER->GetID()) {
	CModule::IncludeModule('iblock');
	if ($arElement = CIBlockElement::GetByID($idElement)->Fetch()) {
		if ($arSection = CIBlockSection::GetList(array(), array('IBLOCK_ID'	 => $arElement['IBLOCK_ID'], 'NAME'		 => $USER->GetID()))->Fetch()) {
			if (CIBlockElement::Delete($idElement)) {
				echo htmlspecialchars($idElement);
			}
		}
	}
}