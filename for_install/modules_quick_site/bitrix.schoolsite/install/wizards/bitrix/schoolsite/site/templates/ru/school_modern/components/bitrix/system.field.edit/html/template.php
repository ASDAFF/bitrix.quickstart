<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<div class="fields string" id="main_<?=$arParams["arUserField"]["FIELD_NAME"]?>"><?
foreach ($arResult["VALUE"] as $res):
 $res = htmlspecialchars_decode(htmlspecialchars_decode($res));
?>
<div class="fields string"><?
	$LHE = new CLightHTMLEditor;
 $LHE->Show(array(
  'id' => preg_replace("/[^a-z0-9]/i", '', $arParams["arUserField"]["FIELD_NAME"]),
  'width' => '100%',
  'height' => '200px',
  'inputName' => $arParams["arUserField"]["FIELD_NAME"],
  'content' => $res,
  'bUseFileDialogs' => false,
  'bFloatingToolbar' => false,
  'bArisingToolbar' => false,
  'toolbarConfig' => array(
   'Bold', 'Italic', 'Underline', 'RemoveFormat',
   'CreateLink', 'DeleteLink', 'Image', 'Video',
   'BackColor', 'ForeColor',
   'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyFull',
   'InsertOrderedList', 'InsertUnorderedList', 'Outdent', 'Indent',
   'StyleList', 'HeaderList',
   'FontList', 'FontSizeList',
  ),
 ));
?></div><?
endforeach;
?></div>