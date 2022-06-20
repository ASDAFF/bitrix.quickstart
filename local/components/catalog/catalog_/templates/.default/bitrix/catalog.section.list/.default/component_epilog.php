<?php

CModule::IncludeModule('iblock');

$res = CIBlock::GetByID($arParams['IBLOCK_ID']);
if($ar_res = $res->GetNext()) 
    $APPLICATION->AddChainItem($ar_res['NAME']);

$APPLICATION->SetTitle($ar_res['NAME']);
 