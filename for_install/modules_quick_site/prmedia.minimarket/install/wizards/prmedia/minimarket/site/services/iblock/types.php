<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<?php

if (!CModule::IncludeModule('iblock'))
{
  return;
}

$languages = array();
$rsLanguage = CLanguage::GetList($by = 'lid', $order = 'asc', $filter = array());
while ($language = $rsLanguage->Fetch())
{
  $languages[] = $language['LID'];
}

$types = array(
  array(
    'ID' => 'prmedia_minimarket',
    'SECTIONS' => 'Y',
    'IN_RSS' => 'N',
    'SORT' => 100,
    'LANG' => array()
  )
);

$iblockType = new CIBlockType;
foreach ($types as $type)
{
	$typeParams = array(
		'filter' => array(
			'ID' => $type['ID']
		)
	);
  $rsType = CIBlockType::GetList(false, $typeParams['filter']);
  if ($rsType->Fetch())
	{
    continue;
	}

  foreach ($languages as $langId)
	{
    WizardServices::IncludeServiceLang('type.php', $langId);

    $code = 'PRMEDIA_WMM_SERVICES_IBLOCK_TYPE';
    $type['LANG'][$langId]['NAME'] = GetMessage($code . '_TYPE_NAME');
    if ($type['SECTIONS'] == 'Y')
		{
      $type['LANG'][$langId]['SECTION_NAME'] = GetMessage($code . '_SECTION_NAME');
		}
    $type['LANG'][$langId]['ELEMENT_NAME'] = GetMessage($code . '_ELEMENT_NAME');
  }

  $iblockType->Add($type);
}