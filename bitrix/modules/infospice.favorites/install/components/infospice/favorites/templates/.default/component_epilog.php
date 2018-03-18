<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?

function PhpToJs($arVar) {
	foreach ($arVar as $key => $value) {
		$sResult.= "{$key}:'{$value}'" . (count($arVar) > ++$iCounter ? ',' : '');
	}
	return "{" . $sResult . "}";
}

if ($USER->GetID()) {
	if ($arUser = CUser::GetList($by = 'ID', $order = 'ASC', array('ID' => $USER->GetID()), array('SELECT' => array('UF_FAVORITES_POS')))->Fetch()) {
		if ($arUser['UF_FAVORITES_POS']) {
			$jsonPosition = PhpToJs(unserialize($arUser['UF_FAVORITES_POS']));
		}
	}
}

if ($jsonPosition) {
	$APPLICATION->AddHeadString('<script type="text/javascript">var itemsPos=' . $jsonPosition . '</script>');
}
$APPLICATION->AddHeadString('<script type="text/javascript">var sComponentPath="' . $componentPath . '";</script>');

$APPLICATION->AddHeadString('<script type="text/javascript" src="' . $templateFolder . '/js/jquery-1.8.2.min.js"></script>');
$APPLICATION->AddHeadString('<script type="text/javascript" src="' . $templateFolder . '/js/jquery-ui-1.8.24.custom.min.js"></script>');
$APPLICATION->AddHeadString('<script type="text/javascript" src="' . $templateFolder . '/js/jquery.main.js"></script>');
?>
