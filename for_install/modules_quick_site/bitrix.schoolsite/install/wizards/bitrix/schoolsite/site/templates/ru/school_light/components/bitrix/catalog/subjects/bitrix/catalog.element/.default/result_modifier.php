<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//echo '<pre>';print_r($arResult['DISPLAY_PROPERTIES']['TEACHERS']['VALUE']);echo '</pre>';
/*TEACHERS*/
 $arSelect = Array("ID", "NAME", "PREVIEW_PICTURE", "PROPERTY_EMAIL", "PREVIEW_TEXT");
 $arFilter = Array("IBLOCK_CODE"=>"teachers_".SITE_ID, "ID"=>$arResult['DISPLAY_PROPERTIES']['TEACHERS']['VALUE'], "ACTIVE"=>"Y");
 $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
 while($ob = $res->GetNextElement()){  
    $arFields = $ob->GetFields(); 
    
     if($arFile = CFile::GetFileArray($arFields["PREVIEW_PICTURE"]))
      {
       $arFileTmp = CFile::ResizeImageGet(
              $arFile,
              array("width" => 50, 'height' => 50),
              BX_RESIZE_IMAGE_EXACT,
              false
          );
          $arSize = getimagesize($_SERVER["DOCUMENT_ROOT"].$arFileTmp["src"]);

          $arUser['PERSONAL_PHOTO'] = array(
              'SRC' => $arFileTmp["src"],
              'WIDTH' => IntVal($arSize[0]),
              'HEIGHT' => IntVal($arSize[1]),
          );
      }
        $arUser["MAIL"] = $arFields["PROPERTY_EMAIL_VALUE"];
        $arUser["NAME"] = $arFields["NAME"];
  $arUser["TEXT"] = $arFields["PREVIEW_TEXT"];
  $arResult["TEACHERS"][] = $arUser;
 }

 /*
$arResult["TEACHERS"] = Array();
$rsUsers = CUser::GetList(($by="last_name"), ($order="asc"), Array("UF_SUBJECTS"=>$arResult["ID"]), Array("SELECT"=>Array("UF_TEACHER_DESC")));
while($arUser = $rsUsers->Fetch())
{
 if($arFile = CFile::GetFileArray($arUser["PERSONAL_PHOTO"]))
 {
  $arFileTmp = CFile::ResizeImageGet(
   $arFile,
   array("width" => 50, 'height' => 50),
   BX_RESIZE_IMAGE_EXACT,
   false
  );
  $arSize = getimagesize($_SERVER["DOCUMENT_ROOT"].$arFileTmp["src"]);

  $arUser['PERSONAL_PHOTO'] = array(
   'SRC' => $arFileTmp["src"],
   'WIDTH' => IntVal($arSize[0]),
   'HEIGHT' => IntVal($arSize[1]),
  );
 }
 
 $arUser["HREF"] = "/company/personal/user/".$arUser["ID"]."/";
 */
 
/*}*/
?>