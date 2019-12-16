<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule("iblock") || !CModule::IncludeModule("catalog"))
	return;

if(COption::GetOptionString("redsign.activelife", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
	return;

$IBLOCK_CATALOG_ID = 0;
if (isset($_SESSION["WIZARD_CATALOG_IBLOCK_ID"]))
{
	$IBLOCK_CATALOG_ID = (int)$_SESSION["WIZARD_CATALOG_IBLOCK_ID"];
	unset($_SESSION["WIZARD_CATALOG_IBLOCK_ID"]);
}
$IBLOCK_OFFERS_ID = 0;
if (isset($_SESSION["WIZARD_OFFERS_IBLOCK_ID"]))
{
	$IBLOCK_OFFERS_ID = (int)$_SESSION["WIZARD_OFFERS_IBLOCK_ID"];
	unset($_SESSION["WIZARD_OFFERS_IBLOCK_ID"]);
}

if ($IBLOCK_CATALOG_ID > 0)
{
	$index = \Bitrix\Iblock\PropertyIndex\Manager::createIndexer($IBLOCK_CATALOG_ID);
	$index->startIndex();
	$index->continueIndex(0);
	$index->endIndex();
}

if ($IBLOCK_OFFERS_ID)
{
	$index = \Bitrix\Iblock\PropertyIndex\Manager::createIndexer($IBLOCK_OFFERS_ID);
	$index->startIndex();
	$index->continueIndex(0);
	$index->endIndex();
}

if ($IBLOCK_OFFERS_ID > 0)
{
	$count = \Bitrix\Iblock\ElementTable::getCount(array(
		'=IBLOCK_ID' => $IBLOCK_OFFERS_ID,
		'=WF_PARENT_ELEMENT_ID' => null
	));
	if ($count > 0)
	{
		$catalogReindex = new CCatalogProductAvailable('', 0, 0);
		$catalogReindex->initStep($count, 0, 0);
		$catalogReindex->setParams(array('IBLOCK_ID' => $IBLOCK_OFFERS_ID));
		$catalogReindex->run();
		unset($catalogReindex);
	}
}

if ($IBLOCK_CATALOG_ID > 0)
{
	$count = \Bitrix\Iblock\ElementTable::getCount(array(
		'=IBLOCK_ID' => $IBLOCK_CATALOG_ID,
		'=WF_PARENT_ELEMENT_ID' => null
	));
	if ($count > 0)
	{
		$catalogReindex = new CCatalogProductAvailable('', 0, 0);
		$catalogReindex->initStep($count, 0, 0);
		$catalogReindex->setParams(array('IBLOCK_ID' => $IBLOCK_CATALOG_ID));
		$catalogReindex->run();
		unset($catalogReindex);
	}
}

\Bitrix\Iblock\PropertyIndex\Manager::checkAdminNotification();
?>