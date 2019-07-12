<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$bFirst = true;

foreach ($arResult["VALUE"] as $arRes):
 $res = '';
 if ($arParams['arUserField']['SETTINGS']['DETAIL_URL'])
  $res = '<a href="'.str_replace(Array('#ID#', '#SECTION_ID#'), Array(urlencode($arRes['ID']), urlencode($arRes['IBLOCK_SECTION_ID'])), $arParams['arUserField']['SETTINGS']['DETAIL_URL']).'">'.$arRes['NAME'].'</a>';
 elseif (StrLen($arParams['arUserField']['PROPERTY_VALUE_LINK']) > 0)
  $res = '<a href="'.str_replace('#VALUE#', urlencode($arRes['ID']), $arParams['arUserField']['PROPERTY_VALUE_LINK']).'">'.$arRes['NAME'].'</a>';
 else
  $res = $arRes['NAME'];

 if (!$bFirst):
  ?>, <?
 else:
  $bFirst = false;
 endif;

 ?><span class="fields enumeration"><?=$res?></span><?
endforeach;
?>