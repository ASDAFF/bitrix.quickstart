<?global $iblockSecID;?>
<?
$myResult;
if(CModule::IncludeModule("iblock")){
  $arList = CIBlockSection::GetList(Array("sort"=>ASC), Array("IBLOCK_ID" => $CatalogID, "SECTION_ID" => $iblockSecID), false);
  $count = 0;
  while($arSection = $arList->GetNext()){
        $selected = '';
        if($arSection["SECTION_PAGE_URL"]==$APPLICATION->GetCurPage())
          $selected = 1;
  	$myResult[] = array(
  	        'TEXT' => $arSection["NAME"],
            'LINK' => $arSection["SECTION_PAGE_URL"],
            'SELECTED' => $selected,
            'PERMISSION' => 'X',
            'ADDITIONAL_LINKS' => array(),
            'ITEM_TYPE' => 'P',
            'ITEM_INDEX' => $count,
            'DEPTH_LEVEL' => '1',
            'IS_PARENT' => ''
  	      );
    $count++;
  }
}
$arResult = $myResult;
?>