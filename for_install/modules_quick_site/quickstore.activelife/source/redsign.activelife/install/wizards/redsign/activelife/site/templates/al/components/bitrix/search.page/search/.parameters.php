<?php

use \Bitrix\Main\Loader;


if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

$defaultListValues = array('-' => getMessage('RS_SLINE.UNDEFINED'));

$arTemplateParameters = array(
	"USE_SUGGEST" => Array(
		"NAME" => GetMessage("TP_BSP_USE_SUGGEST"),
		"TYPE" => "CHECKBOX",
		"DEFAULT" => "N",
	),
	'SHOW_OLD_PRICE' => array(
		'PARENT' => 'PRICES',
		'NAME' => getMessage('RS_SLINE.SHOW_OLD_PRICE'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y',
	),
	'SHOW_DISCOUNT_PERCENT' => array(
		'PARENT' => 'PRICES',
		'NAME' => getMessage('RS_SLINE.SHOW_DISCOUNT_PERCENT'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'Y'
	),
	'USE_FAVORITE' => array(
		'PARENT' => 'VISUAL',
		'NAME' => getMessage('RS_SLINE.USE_FAVORITE'),
		'TYPE' => 'CHECKBOX',
		'VALUE' => 'Y',
		'DEFAULT' => 'Y',
	)
);

if(COption::GetOptionString("search", "use_social_rating") == "Y")
{
	$arTemplateParameters["SHOW_RATING"] = Array(
		"NAME" => GetMessage("TP_BSP_SHOW_RATING"),
		"TYPE" => "LIST",
		"VALUES" => Array(
			"" => GetMessage("TP_BSP_SHOW_RATING_CONFIG"),
			"Y" => GetMessage("MAIN_YES"),
			"N" => GetMessage("MAIN_NO"),
		),
		"MULTIPLE" => "N",
		"DEFAULT" => "",
	);
	$arTemplateParameters["RATING_TYPE"] = Array(
		"NAME" => GetMessage("TP_BSP_RATING_TYPE"),
		"TYPE" => "LIST",
		"VALUES" => Array(
			"" => GetMessage("TP_BSP_RATING_TYPE_CONFIG"),
			"like" => GetMessage("TP_BSP_RATING_TYPE_LIKE_TEXT"),
			"like_graphic" => GetMessage("TP_BSP_RATING_TYPE_LIKE_GRAPHIC"),
			"standart_text" => GetMessage("TP_BSP_RATING_TYPE_STANDART_TEXT"),
			"standart" => GetMessage("TP_BSP_RATING_TYPE_STANDART_GRAPHIC"),
		),
		"MULTIPLE" => "N",
		"DEFAULT" => "",
	);
	$arTemplateParameters["PATH_TO_USER_PROFILE"] = Array(
		"NAME" => GetMessage("TP_BSP_PATH_TO_USER_PROFILE"),
		"TYPE" => "STRING",
		"DEFAULT" => "",
	);
}


if (
    !Loader::includeModule('redsign.devfunc') ||
    !Loader::includeModule('iblock') ||
    !Loader::includeModule('catalog')
) {
	return;
}


if (is_array($arCurrentValues['IBLOCK_ID']) && count($arCurrentValues['IBLOCK_ID']) > 0) {

    foreach ($arCurrentValues['IBLOCK_ID'] as $iblockID)
    {
        $arProperty = array();
        if (0 < intval($iblockID))
        {
            $rsProp = CIBlockProperty::GetList(array('sort' => 'asc', 'name' => 'asc'), array('IBLOCK_ID' => $iblockID, 'ACTIVE' => 'Y'));
            while ($arr=$rsProp->Fetch())
            {
                $arProperty[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
            }
        }
        
        $arTemplateParameters['ADDITIONAL_PICT_PROP_'.$iblockID] = array(
            'PARENT' => 'VISUAL',
            'NAME' => getMessage('RS_SLINE.ADDITIONAL_PICT_PROP'),
            'TYPE' => 'LIST',
            'VALUES' => array_merge($defaultListValues, $arProperty),
            'DEFAULT' => '-',
        );
        
        $arSKU = CCatalogSKU::GetInfoByProductIBlock($iblockID);
        if (!empty($arSKU) && is_array($arSKU))
        {
            $arProperty_Offers = array();
            if (0 < intval($iblockID))
            {
                $rsProp = CIBlockProperty::GetList(array('sort' => 'asc', 'name' => 'asc'), array('IBLOCK_ID' => $arSKU['IBLOCK_ID'], 'ACTIVE' => 'Y'));
                while ($arr=$rsProp->Fetch())
                {
                    $arProperty_Offers[$arr['CODE']] = '['.$arr['CODE'].'] '.$arr['NAME'];
                }
            }
            $arTemplateParameters['OFFER_ADDITIONAL_PICT_PROP_'.$iblockID] = array(
                'PARENT' => 'VISUAL',
                'NAME' => getMessage('RS_SLINE.ADDITIONAL_PICT_PROP'),
                'TYPE' => 'LIST',
                'VALUES' => array_merge($defaultListValues, $arProperty_Offers),
                'DEFAULT' => '-',
            );
        }
    }
}

$arTemplateParameters = array_merge($arTemplateParameters, RSDevFuncParameters::GetTemplateParamsCatalog($arCurrentValues));