<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
    die();

if(!CModule::IncludeModule("iblock"))
    return;

// Редактирование объекта кредита
$res = CIBlock::GetList(
  Array(),
  Array(
    "CODE" => 'credit_elem', 
    "SORT"=>"ASC", 
    "ELEMENT_CNT"=>1
  ),
  true
);

$creditelemIBlock = $res->Fetch();
$Id = $creditelemIBlock['ID'];

$fields = CIBlock::getFields($Id);
$fields["IBLOCK_SECTION"]["IS_REQUIRED"] = "Y";
$fields["DETAIL_PICTURE"]["IS_REQUIRED"] = "Y";
$fields["DETAIL_TEXT"]["IS_REQUIRED"] = "Y";
$fields["PREVIEW_PICTURE"]["DEFAULT_VALUE"]["SCALE"] = "Y";
$fields["PREVIEW_PICTURE"]["DEFAULT_VALUE"]["WIDTH"] = 140;
$fields["PREVIEW_PICTURE"]["DEFAULT_VALUE"]["HEIGHT"] = 70;
$fields["PREVIEW_PICTURE"]["DEFAULT_VALUE"]["DELETE_WITH_DETAIL"] = "Y";
$fields["PREVIEW_PICTURE"]["DEFAULT_VALUE"]["UPDATE_WITH_DETAIL"] = "Y";
$fields["PREVIEW_PICTURE"]["DEFAULT_VALUE"]["FROM_DETAIL"] = "Y";
CIBlock::setFields($Id, $fields);

$ibp = new CIBlockProperty;
$priceProp = CIBlock::GetProperties($Id, Array(), Array("CODE" => 'price'))->Fetch();
$arFields = Array(
    'IS_REQUIRED' => 'Y'
);
$ibp->Update($priceProp['ID'], $arFields);

// Редактирование акций
$res = CIBlock::GetList(
  Array(),
  Array(
    "CODE" => 'company_actions', 
    "SORT"=>"ASC", 
    "ELEMENT_CNT"=>1
  ),
  true
);

$actionsIBlock = $res->Fetch();
$Id = $actionsIBlock['ID'];

$fields = CIBlock::getFields($Id);
$fields["NAME"]["IS_REQUIRED"] = "Y";
$fields["PREVIEW_PICTURE"]["IS_REQUIRED"] = "Y";
$fields["PREVIEW_PICTURE"]["DEFAULT_VALUE"]["SCALE"] = "Y";
$fields["PREVIEW_PICTURE"]["DEFAULT_VALUE"]["WIDTH"] = 500;
$fields["PREVIEW_PICTURE"]["DEFAULT_VALUE"]["HEIGHT"] = 200;
CIBlock::setFields($Id, $fields);

// Редактирование баннеров
$res = CIBlock::GetList(
  Array(),
  Array(
    "CODE" => 'credit_banner', 
    "SORT"=>"ASC", 
    "ELEMENT_CNT"=>1
  ),
  true
);

$bannerIBlock = $res->Fetch();
$Id = $bannerIBlock['ID'];

$fields = CIBlock::getFields($Id);
$fields["DETAIL_PICTURE"]["IS_REQUIRED"] = "Y";
$fields["NAME"]["IS_REQUIRED"] = "Y";
CIBlock::setFields($Id, $fields);

?>