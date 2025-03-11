<?
Bitrix\Main\Config\Option::set("catalog", "product_form_simple_search", "N");
$arFilter['PARAM2'] = $this->getIblockId();
if (!empty($arFilter['SECTION_ID']))
{
  $arFilter['PARAMS'] = array('iblock_section' => $arFilter['SECTION_ID']);
}
$obSearch = new \CSearch();
$obSearch->Search($arFilter);

$cnt = 0;
$activeSectionId = $this->getSectionId();
while ($ar = $obSearch->Fetch())
{
  if (strpos($ar['ITEM_ID'], 'S') === 0)
  {
    $sectionId = preg_replace('#[^0-9]+#', '', $ar['ITEM_ID']);
    if ($sectionId != $activeSectionId)
      $arSearchedSectionIds[] = $sectionId;
  }
  else
    $arSearchedIds[] = $ar['ITEM_ID'];
  if (++$cnt >= 100)
    break;
}

