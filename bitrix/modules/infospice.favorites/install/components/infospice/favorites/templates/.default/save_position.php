<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
$arData = array(
	'xPos' => intval($_GET['xPos']),
	'yPos' => intval($_GET['yPos']),
	'pageWidth' => intval($_GET['pageWidth']),
	'pageHeight' => intval($_GET['pageHeight']),
	'dropClasses' => htmlspecialchars($_GET['dropClasses']),
	'controlsClasses' => htmlspecialchars($_GET['controlsClasses'])
);

if ($USER->GetID()) {
	$obUser = new CUser;
	if (!$obUser->Update($USER->GetID(), array('UF_FAVORITES_POS' => serialize($arData)))) {
		echo $obUser->LAST_ERROR;
	}
}