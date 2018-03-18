<?include $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php'?>
<?
header('Content-type: text/html; charset=' . SITE_CHARSET);
$_GET['IBLOCK_ID'] = intval($_GET['IBLOCK_ID']);
if ($_GET['IBLOCK_ID'] && $_GET['URL'] && $USER->GetID()) {
	$_GET['URL'] = preg_replace('/#(.)*/', '', $_GET['URL']);
	$_GET['IBLOCK_ID'] = intval($_GET['IBLOCK_ID']);

	CModule::IncludeModule('iblock');
//check section by user
	if ($arSection = CIBlockSection::GetList(array(), array(
				'IBLOCK_ID'	 => $_GET['IBLOCK_ID'],
				'NAME'		 => $USER->GetID()))->Fetch()) {
		$obElement = new CIBlockElement;
		$arFields = array(
			'IBLOCK_ID'			 => $_GET['IBLOCK_ID'],
			'IBLOCK_SECTION_ID'	 => $arSection['ID'],
			'NAME'				 => iconv('UTF-8', SITE_CHARSET, $_GET['TITLE']),
			'PROPERTY_VALUES'	 => array('URL' => $_GET['URL']),
		);
		if ($ID = $obElement->Add($arFields)) {
			?>
			<li class="<?=$_GET['CURRENT_PAGE'] === 'Y' ? 'infospice-favorite-current-page' : ''?>" id = "item_<?=$ID?>">
				<span class = "infospice-favorite-handle">&nbsp;</span>
				<a href = "<?=htmlspecialchars($_GET['URL'])?>"><?=htmlspecialchars(iconv('UTF-8', SITE_CHARSET, $_GET['TITLE']))?></a>
				<a href = "#" class = "infospice-favorite-remove" onclick = "RemoveItemFavorite(<?=$ID?>)">remove</a>
			</li>
			<?
		}
	}
}