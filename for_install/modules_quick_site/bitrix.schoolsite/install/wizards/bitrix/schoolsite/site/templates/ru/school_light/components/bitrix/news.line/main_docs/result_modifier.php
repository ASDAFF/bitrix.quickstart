<?
 foreach($arResult["ITEMS"] as $index=>$arItem)
 {
  $rsProp = CIBlockElement::GetProperty($arItem["IBLOCK_ID"], $arItem["ID"], "sort", "asc", Array("CODE"=>"FILE"));
  $arProp = $rsProp->Fetch();
  $arFile = CFile::GetFileArray($arProp["VALUE"]);  
  if(is_array($arFile))
  {
   $strFileExt = strrchr($arFile["FILE_NAME"], ".");
   switch($strFileExt)
   {
    case ".doc":
    case ".docx":
     $ext = "doc";
    break;
    
    case ".xls":
    case ".xlsx":
     $ext = "xls";
    break;
    
    case ".pdf":
     $ext = "pdf";
    break;

    case ".pps":
    case ".ppt":
     $ext = "ppt";
     break;
     
    case ".zip": 
    case ".rar":
     $ext = "zip";
     break;

    case ".jpg":
    case ".jpeg":
    case ".png":
    case ".gif":
    case ".bmp":
     $ext = "img";
     break;
    
    default:
     $ext = "none";
   }
     $arResult["ITEMS"][$index]["FILE_SRC"] = $arFile["SRC"];
  }
  
  $arResult["ITEMS"][$index]["FILE_TYPE"] = $ext;
  $arResult["ITEMS"][$index]["FILE_ICON"] = $icon_img;
 }
?>