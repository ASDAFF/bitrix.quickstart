<?
$myResult;
if(CModule::IncludeModule("iblock")){
  $arList = CIBlockSection::GetList(Array("sort"=>ASC), Array("IBLOCK_ID" => $CatalogID, "DEPTH_LEVEL" => 1), false);
  $count = 0;
  while($arSection = $arList->GetNext()){
  	
  	$arList2 = CIBlockSection::GetList(Array(), Array("IBLOCK_ID" => $CatalogID, "SECTION_ID" => $arSection["ID"]), false);
  	$count2 = 0;
    $arMass = array();
    while($arSubSec = $arList2->GetNext()){
      $arMass[] = $arSubSec;
      $count2++;
    }
    $arParent = '1';
    if($count2==0) $arParent = ''; 
    
  	$myResult[] = array(
  	        'TEXT' => $arSection["NAME"],
            'LINK' => $arSection["SECTION_PAGE_URL"],
            'SELECTED' => '',
            'PERMISSION' => 'X',
            'ADDITIONAL_LINKS' => array(),
            'ITEM_TYPE' => 'P',
            'ITEM_INDEX' => $count,
            'DEPTH_LEVEL' => '1',
            'IS_PARENT' => $arParent
  	      );
    $count++;   
    
    $count2 = 0;
    foreach($arMass as $arSub){  
    
      $arList3 = CIBlockSection::GetList(Array(), Array("IBLOCK_ID" => $CatalogID, "SECTION_ID" => $arSub["ID"]), false);
  	  $count3 = 0;
      $arMass2 = array();
      while($arSubSec = $arList3->GetNext()){
        $arMass2[] = $arSubSec;
        $count3++;
      }
      $arParent = '1';
      if($count3==0) $arParent = '';
      
      $myResult[] = array(
              'TEXT' => $arSub["NAME"],
              'LINK' => $arSub["SECTION_PAGE_URL"],
              'SELECTED' => '',
              'PERMISSION' => 'X',
              'ADDITIONAL_LINKS' => array(),
              'ITEM_TYPE' => 'P',
              'ITEM_INDEX' => $count2,
              'DEPTH_LEVEL' => '2',
              'IS_PARENT' => $arParent
            );
      $count2++;
      
      $count3 = 0;
      foreach($arMass2 as $arSubSec){
      	$myResult[] = array(
              'TEXT' => $arSubSec["NAME"],
              'LINK' => $arSubSec["SECTION_PAGE_URL"],
              'SELECTED' => '',
              'PERMISSION' => 'X',
              'ADDITIONAL_LINKS' => array(),
              'ITEM_TYPE' => 'P',
              'ITEM_INDEX' => $count3,
              'DEPTH_LEVEL' => '3',
              'IS_PARENT' => ''
            );
        $count3++;
      }
    }
  }
}
$arResult = $myResult;
?>
<?

if (!empty($arResult)):

	$top_key = -1;
	foreach($arResult as $key => $arItem):
		if ($arItem["DEPTH_LEVEL"] == 1):
			$top_key++;
			$arFormatted["TOP"][$top_key] = $arItem;
		elseif ($arItem['PERMISSION'] > 'D'):
			$arFormatted["TOP"][$top_key]["ITEMS"][] = $arItem;
		endif;
	endforeach;

	foreach($arFormatted["TOP"] as $key => $arTopItem):
		if (count($arTopItem["ITEMS"]) > 12)
			$arFormatted["TOP"][$key]["LARGE"] = true;
		else
			$arFormatted["TOP"][$key]["LARGE"] = false;
	endforeach;

endif;

$arResult = $arFormatted["TOP"];
?>