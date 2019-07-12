<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

if (!CModule::IncludeModule('iblock') || !CModule::IncludeModule('catalog'))
{
  return;
}

$xmlFile = WIZARD_SERVICE_RELATIVE_PATH . '/xml/' . LANGUAGE_ID . '/catalog.xml';
$type = 'prmedia_minimarket';
$code = 'prmedia.minimarket.catalog';

// get catalog iblock id
$iblockId = false;
$iblockParams = array(
	'filter' => array(
		'TYPE' => $type,
		'CODE' => $code
	)
);
$rsIblock = CIBlock::GetList(false, $iblockParams['filter']);
if ($iblock = $rsIblock->Fetch())
{
	$iblockId = $iblock['ID'];
}

// remove prev iblock and create new with demodata
if (WIZARD_INSTALL_DEMO_DATA)
{
	if ($iblockId > 0)
	{
		CIBlock::Delete($iblockId);
	}
	
	// import catalog iblock like iblock and catalog
  $iblockId = WizardServices::ImportIBlockFromXML(
    $xmlFile, $code, $type, $siteId = WIZARD_SITE_ID,
    $permissions = array(
			'1' => 'X',
			'2' => 'R'
		)
  );
}

// create iblock if doesn't exists
if ($iblockId == false)
{
  $iblock = new CIBlock;
  $fields = array(
    'ACTIVE' => 'Y',
    'NAME' => GetMessage('PRMEDIA_WMM_SERVICES_IBLOCK_CATALOG_NAME'),
    'CODE' => $code,
    'IBLOCK_TYPE_ID' => $type,
    'LIST_PAGE_URL' => '#SITE_DIR#/catalog/',
    'SECTION_PAGE_URL' => '#SITE_DIR#/catalog/#SECTION_CODE_PATH#/',
    'DETAIL_PAGE_URL' => '#SITE_DIR#/catalog/#SECTION_CODE_PATH#/#ID#/',
    'SITE_ID' => array(WIZARD_SITE_ID)
  );
  $iblockId = $iblock->Add($fields);
  CCatalog::Add(array(
    'IBLOCK_ID' => $iblockId,
    'YANDEX_EXPORT' => 'N',
    'SUBSCRIPTION' => 'N'
  ));
}

// set iblock rights to "READ" for all non-admin groups
$permissions = array();
$groupParams = array(
	'filter' => array(
		'ADMIN' => 'N'
	)
);
$rsGroup = CGroup::GetList($by = 'id', $sort = 'asc', $groupParams['filter']);
while ($group = $rsGroup->Fetch())
{
	$permissions[$group['ID']] = 'R';
}
CIBlock::SetPermission($iblockId, $permissions);

// replace macros
$paths = array(
	'index.php',
	'catalog/index.php',
	'catalog/.include.left_column.php',
	'search/index.php',
	'include_areas/.left.menu.php',
	'include_areas/catalog_iblock_id.php'
);
$replaceArray = array(
	'PRMEDIA_MINIMARKET_CATALOG_IBLOCK_ID' => $iblockId
);
foreach ($paths as $path)
{
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH . $path, $replaceArray);
}

// wizard installation completed
COption::SetOptionString('prmedia.minimarket', 'wizard_installed', 'Y', false, WIZARD_SITE_ID);