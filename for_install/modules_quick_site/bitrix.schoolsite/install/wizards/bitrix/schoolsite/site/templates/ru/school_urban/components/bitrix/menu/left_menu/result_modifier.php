<?
 $tmpResult = Array();
 $needAdd = false;
 foreach($arResult as $arItem)
 {
  if($arItem["DEPTH_LEVEL"] == 1)
  {
   if($arItem["SELECTED"])
    $needAdd = true;
   else
    $needAdd = false;
  }
  elseif($needAdd)
  {
   $arItem["DEPTH_LEVEL"] = $arItem["DEPTH_LEVEL"] - 1;
   $tmpResult[] = $arItem;
  }
 }
 
 $arResult = $tmpResult;
?>