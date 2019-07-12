<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

if (!CModule::IncludeModule('iblock')
    || !CModule::IncludeModule('catalog')
    || !CModule::IncludeModule('redsign.flyaway')
    || !CModule::IncludeModule('redsign.devfunc')) {
    return;
}

$listProp = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCurrentValues['IBLOCK_ID']);
$arCatalog = CCatalog::GetByID($arCurrentValues['IBLOCK_ID']);
$rsPrice = CCatalogGroup::GetList($v1="sort", $v2="asc");
while ($arr  =$rsPrice->Fetch()) {
  $arPrice[$arr["ID"]] = "[".$arr["NAME"]."] ".$arr["NAME_LANG"];
 }

$arViewModeList = array(
    'VIEW_SECTIONS' => GetMessage('RS.FLYAWAY.VIEW_SECTIONS'),
    'VIEW_ELEMENTS' => GetMessage('RS.FLYAWAY.VIEW_ELEMENTS'),
);

$sorterTemplates = array(
	"showcase" => "showcase",
	"list" => "list",
	"list_little" => "list_little",
	"showcase_little" => "showcase_little"
);

$arTemplateParameters = array(
    'SECTIONS_VIEW_MODE' => array(
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('RS.FLYAWAY.VIEW_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => $arViewModeList,
        'MULTIPLE' => 'N',
        'DEFAULT' => 'LIST',
    ),
    'PROPS_TABS' => array(
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('PROPS_TABS'),
        'TYPE' => 'LIST',
        'VALUES' => $listProp['ALL'],
        'MULTIPLE' => 'Y',
    ),
    'RSFLYAWAY_PROP_MORE_PHOTO' => array(
        'NAME' => Loc::getMessage('RS.FLYAWAY.PROP_MORE_PHOTO'),
        'TYPE' => 'LIST',
        'VALUES' => $listProp['F'],
    ),
    'RSFLYAWAY_PROP_ARTICLE' => array(
        'NAME' => Loc::getMessage('RS.FLYAWAY.PROP_ARTICLE'),
        'TYPE' => 'LIST',
        'VALUES' => $listProp['SNL'],
    ),
    'USE_CUSTOM_COLLECTION' => array(
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('RS.FLYAWAY.USE_CUSTOM_COLLECTION'),
        'TYPE' => 'CHECKBOX',
        'VALUE' => 'Y',
        'DEFAULT' => 'Y',
    ),
    'USE_BLOCK_MODS' => array(
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('USE_BLOCK_MODS'),
        'TYPE' => 'CHECKBOX',
        'VALUE' => 'Y',
        'DEFAULT' => 'Y',
    ),
    'MODS_BLOCK_NAME' => array(
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => GetMessage('MODS_BLOCK_NAME'),
        'TYPE' => 'STRING',
    ),
    'RSFLYAWAY_USE_FAVORITE' => array(
        'NAME' => GetMessage('RS.FLYAWAY.USE_FAVORITE'),
        'TYPE' => 'CHECKBOX',
        'VALUE' => 'Y',
        'DEFAULT' => 'Y',
    ),
    'FILTER_PROP_SEARCH' => array(
        'PARENT' => 'FILTER_SETTINGS',
        'NAME' => GetMessage('FILTER_PROP_SEARCH'),
        'TYPE' => 'LIST',
        'MULTIPLE' => 'Y',
        'VALUES' => $listProp['ALL'],
    ),
    'RSFLYAWAY_PROP_OFF_POPUP' => array(
        'NAME' => Loc::getMessage('RS.FLYAWAY.PROP_OFF_POPUP'),
        'TYPE' => 'CHECKBOX',
        'VALUE' => 'Y',
        'DEFAULT' => 'Y',
    ),
    'RSFLYAWAY_HIDE_BASKET_POPUP' => array(
        'NAME' => Loc::getMessage('RS.FLYAWAY.HIDE_BASKET_POPUP'),
        'TYPE' => 'CHECKBOX',
        'VALUE' => 'Y',
        'DEFAULT' => 'Y',
    ),
    'SORTER_USE_AJAX' => array(
        'NAME' => Loc::getMessage('RS.FLYAWAY.SORTER_USE_AJAX'),
        'TYPE' => 'LIST',
        'TYPE' => 'CHECKBOX',
        'VALUE' => 'Y',
    ),

    // Filter
    'FILTER_USE_AJAX' => array(
        'PARENT' => 'FILTER_SETTINGS',
        'NAME' => Loc::getMessage('RS.FLYAWAY.FILTER_USE_AJAX'),
        'TYPE' => 'CHECKBOX',
        'VALUE' => 'Y',
    ),

    'SHOW_SECTION_URL' => array(
        'NAME' => Loc::getMessage('RS.FLYAWAY.SHOW_SECTION_URL'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
    ),
);

if (intval($arCatalog['OFFERS_IBLOCK_ID'])) {
    $listProp2 = RSDevFuncParameters::GetTemplateParamsPropertiesList($arCatalog['OFFERS_IBLOCK_ID']);
    $arTemplateParameters['RSFLYAWAY_PROP_SKU_MORE_PHOTO'] = array(
        'NAME' => GetMessage('RS.FLYAWAY.PROP_SKU_MORE_PHOTO'),
        'TYPE' => 'LIST',
        'VALUES' => $listProp2['F'],
    );
    $arTemplateParameters['RSFLYAWAY_PROP_SKU_ARTICLE'] = array(
        'NAME' => GetMessage('RS.FLYAWAY.PROP_SKU_ARTICLE'),
        'TYPE' => 'LIST',
        'VALUES' => $listProp2['SNL'],
    );
}

if (\Bitrix\Main\Loader::includeModule('redsign.devcom')) {
    $arTemplateParameters['RSFLYAWAY_SHOW_SORTER'] = array(
        'NAME' => Loc::getMessage('RS.FLYAWAY.SHOW_SORTER'),
        'TYPE' => 'CHECKBOX',
        'VALUE' => 'Y',
        'DEFAULT' => 'Y',
        'REFRESH' => 'Y',
        'PARENT' => 'LIST_SETTINGS',
    );

    if ($arCurrentValues['RSFLYAWAY_SHOW_SORTER'] == 'Y') {
        $arTemplateParameters['RSFLYAWAY_SORTER_SHOW_TEMPLATE'] = array(
            'NAME' => Loc::getMessage('RS.FLYAWAY.SORTER_SHOW_TEMPLATE'),
            'TYPE' => 'CHECKBOX',
            'VALUE' => 'Y',
            'DEFAULT' => 'Y',
            'REFRESH' => 'Y',
            'PARENT' => 'LIST_SETTINGS',
        );

        $arTemplateParameters['RSFLYAWAY_SORTER_SHOW_SORTING'] = array(
            'NAME' => Loc::getMessage('RS.FLYAWAY.SORTER_SHOW_SORTING'),
            'TYPE' => 'CHECKBOX',
            'VALUE' => 'Y',
            'DEFAULT' => 'Y',
            'REFRESH' => 'Y',
            'PARENT' => 'LIST_SETTINGS',
        );

        $arTemplateParameters['RSFLYAWAY_SORTER_SHOW_PAGE_COUNT'] = array(
            'NAME' => Loc::getMessage('RS.FLYAWAY.SORTER_SHOW_PAGE_COUNT'),
            'TYPE' => 'CHECKBOX',
            'VALUE' => 'Y',
            'DEFAULT' => 'Y',
            'REFRESH' => 'Y',
            'PARENT' => 'LIST_SETTINGS',
        );

        $arTemplateParameters['RS_SORTER_OUTPUT_OF'] = array(
    				'NAME' => GetMessage('RS.SORTER_OUTPUT_OF'),
    				'TYPE' => 'STRING',
    				'MULTIPLE' => 'Y',
    				'DEFAULT' => array(5, 10, 15, 20),
    				'PARENT' => 'LIST_SETTINGS',
  			);
  			$arTemplateParameters['RS_SORTER_OUTPUT_DEFAULT'] = array(
    				'NAME' => GetMessage('RS.SORTER_OUTPUT_DEFAULT'),
    				'TYPE' => 'STRING',
    				'DEFAULT' => "15",
    				'PARENT' => 'LIST_SETTINGS',
  			);
  			$arTemplateParameters['RS_SORTER_OUTPUT_ALL'] = array(
    				'NAME' => GetMessage('RS.SORTER_OUTPUT_ALL'),
    				'TYPE' => 'CHECKBOX',
    				'DEFAULT' => "N",
    				'PARENT' => 'LIST_SETTINGS',
  			);
        $arTemplateParameters['RS_SORTER_AVAILABLE_SORTS'] = array(
          'NAME' => GetMessage('RS.SORTER_AVAILABLE_SORTS'),
          'TYPE' => 'LIST',
          'PARENT' => 'LIST_SETTINGS',
          'REFRESH' => 'Y',
          'VALUES' => array(
            'price' => GetMessage('RS.SORT_PROPERTY_PRICE_FALSE'),
            'name' => GetMessage('RS.SORT_name'),
            'sort' => GetMessage('RS.SORT_sort')
          ),
          'MULTIPLE' => 'Y',
          'ADDITIONAL_VALUES' => 'Y'
        );
        $arTemplateParameters['RS_SORTER_PRICE_USE_PROPERTY'] = array(
          'NAME' => GetMessage('RS.SORTER_PRICE_USE_PROPERTY'),
          'TYPE' => 'CHECKBOX',
          'DEFAULT' => 'Y',
          'PARENT' => 'LIST_SETTINGS',
          'REFRESH' => 'Y'
        );
        if ($arCurrentValues['RS_SORTER_PRICE_USE_PROPERTY'] == 'Y') {
          $arTemplateParameters['RS_SORTER_PRICE_CODE'] = array(
            'NAME' => GetMessage('RS.SORTER_PRICE_CODE'),
            'TYPE' => 'STRING',
            'PARENT' => 'LIST_SETTINGS',
            'DEFAULT' => 'PRICE_FALSE'
          );
        } else {
          $arTemplateParameters['RS_SORTER_PRICE_ID'] = array(
            'NAME' => GetMessage('RS.SORTER_PRICE_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arPrice,
            'PARENT' => 'LIST_SETTINGS',
          );
        }
        $arTemplateParameters['RS_DEFAULT_SORT'] = array(
    				'NAME' => GetMessage('RS.DEFAULT_SORT'),
    				'TYPE' => 'LIST',
            'VALUES' => array(
                'PROPERTY_PRICE_FALSE' => GetMessage('RS.SORT_PROPERTY_PRICE_FALSE'),
                'name' => GetMessage('RS.SORT_name'),
                'sort' => GetMessage('RS.SORT_sort'),
            ),
    				'DEFAULT' => 0,
    				'PARENT' => 'LIST_SETTINGS',
  			);
        $arTemplateParameters['RS_DEFAULT_SORT_TYPE'] = array(
    				'NAME' => GetMessage('RS.DEFAULT_SORT_TYPE'),
    				'TYPE' => 'LIST',
    				'DEFAULT' => "asc",
            'VALUES' => array(
                'asc' => GetMessage('RS.SORT_asc'),
                'desc' => GetMessage('RS.SORT_desc'),
            ),
    				'PARENT' => 'LIST_SETTINGS',
  			);


        if ($arCurrentValues['RSFLYAWAY_SORTER_SHOW_TEMPLATE'] == 'Y') {
            $arTemplateParameters['RSFLYAWAY_SORTER_TEMPLATE_DEFAULT'] = array(
                'NAME' => Loc::getMessage('RS.FLYAWAY.SORTER_TEMPLATE_DEFAULT'),
                'TYPE' => 'STRING',
                'VALUE' => '',
                'DEFAULT' => 'showcase',
                'PARENT' => 'LIST_SETTINGS',
            );

            $arTemplateParameters['RS_SORTER_TEMPLATES'] = array(
        				'NAME' => GetMessage('RS.SORTER_TEMPLATES'),
        				'TYPE' => 'LIST',
        				'MULTIPLE' => 'Y',
        				'PARENT' => 'LIST_SETTINGS',
        				'VALUES' => $sorterTemplates,
                'DEFAULT' => array(
                  0 => "showcase",
            			1 => "showcase_little",
            			2 => "list",
            			3 => "list_little"
                )
      			);
        }

        $arTemplateParameters['OFFER_TREE_PROPS'] = array(
            'PARENT' => 'OFFERS_SETTINGS',
            'NAME' => getMessage('RS.FLYAWAY.OFFER_TREE_PROPS'),
            'TYPE' => 'LIST',
            'VALUES' => $listProp2['SNL'],
            'MULTIPLE' => 'Y',
            'DEFAULT' => '-',
        );

        $arTemplateParameters['OFFER_TREE_COLOR_PROPS'] = array(
            'PARENT' => 'OFFERS_SETTINGS',
            'NAME' => getMessage('RS.FLYAWAY.OFFER_TREE_COLOR_PROPS'),
            'TYPE' => 'LIST',
            'VALUES' => $listProp2['HL'],
            'MULTIPLE' => 'Y',
            'DEFAULT' => '-',
        );
    }
}

$arTemplateParameters['RSFLYAWAY_SHOW_DELIVERY'] = array(
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('RS.FLYAWAY.SHOW_DELIVERY'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y',
);
if (isset($arCurrentValues['RSFLYAWAY_SHOW_DELIVERY']) && 'Y' == $arCurrentValues['RSFLYAWAY_SHOW_DELIVERY']) {
    $arTemplateParameters['RSFLYAWAY_DELIVERY_MODE'] = array(
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('RS.FLYAWAY.DELIVERY_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => array(
            'include_area' => Loc::getMessage('RS.FLYAWAY.DELIVERY_MODE_INCLUDE_AREA'),
            'auto' => Loc::getMessage('RS.FLYAWAY.DELIVERY_MODE_AUTO'),
        ),
        'REFRESH' => 'Y',
        'DEFAULT' => 'auto',
    );
    if (isset($arCurrentValues['RSFLYAWAY_DELIVERY_MODE']) && 'auto' == $arCurrentValues['RSFLYAWAY_DELIVERY_MODE']) {
        $arTemplateParameters['RSFLYAWAY_TAB_DELIVERY'] = array(
            'PARENT' => 'DETAIL_SETTINGS',
            'NAME' => Loc::getMessage('RS.FLYAWAY.SHOW_TAB_DELIVERY'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y',
        );
        $arTemplateParameters['RSFLYAWAY_SHOW_DELIVERY_PAYMENT_INFO'] = array(
            'PARENT' => 'DETAIL_SETTINGS',
            'NAME' => Loc::getMessage('RS.FLYAWAY.SHOW_DELIVERY_INFORMATION'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
            'REFRESH' => 'Y',
        );

        if (isset($arCurrentValues['RSFLYAWAY_SHOW_DELIVERY_PAYMENT_INFO']) && 'Y' == $arCurrentValues['RSFLYAWAY_SHOW_DELIVERY_PAYMENT_INFO']) {
            $arTemplateParameters['RSFLYAWAY_DELIVERY_LINK'] = array(
                'PARENT' => 'DETAIL_SETTINGS',
                'NAME' => Loc::getMessage('RS.FLYAWAY.DELIVERY_LINK'),
                'TYPE' => 'STRING',
                'DEFAULT' => '/delivery/',
            );
            $arTemplateParameters['RSFLYAWAY_PAYMENT_LINK'] = array(
                'PARENT' => 'DETAIL_SETTINGS',
                'NAME' => Loc::getMessage('RS.FLYAWAY.PAYMENT_LINK'),
                'TYPE' => 'STRING',
                'DEFAULT' => '/payment/',
            );
        }
    }
}

$arTemplateParameters['DETAIL_USE_COMMENTS'] = array(
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('RS.FLYAWAY.DETAIL_USE_COMMENTS'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y',
);
if (isset($arCurrentValues['DETAIL_USE_COMMENTS']) && 'Y' == $arCurrentValues['DETAIL_USE_COMMENTS']) {
    if (ModuleManager::isModuleInstalled('blog')) {
        $arTemplateParameters['DETAIL_BLOG_URL'] = array(
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('RS.FLYAWAY.DETAIL_BLOG_URL'),
            'TYPE' => 'STRING',
            'DEFAULT' => 'catalog_comments',
        );
        $arTemplateParameters['DETAIL_BLOG_EMAIL_NOTIFY'] = array(
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('RS.FLYAWAY.DETAIL_BLOG_EMAIL_NOTIFY'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
        );
    }
}

$arTemplateParameters['USE_BIG_DATA'] = array(
    'PARENT' => 'BIG_DATA_SETTINGS',
    'NAME' => Loc::getMessage('CP_BC_TPL_USE_BIG_DATA'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y',
);
if (isset($arCurrentValues['USE_BIG_DATA']) || $arCurrentValues['USE_BIG_DATA'] == 'Y') {
    $arTemplateParameters['BIG_DATA_RCM_TYPE'] = array(
        'PARENT' => 'BIG_DATA_SETTINGS',
        'NAME' => Loc::getMessage('CP_BC_TPL_BIG_DATA_RCM_TYPE'),
        'TYPE' => 'LIST',
        'VALUES' => array(
            'bestsell' => Loc::getMessage('CP_BC_TPL_RCM_BESTSELLERS'),
            'personal' => Loc::getMessage('CP_BC_TPL_RCM_PERSONAL'),
            'similar_sell' => Loc::getMessage('CP_BC_TPL_RCM_SOLD_WITH'),
            'similar_view' => Loc::getMessage('CP_BC_TPL_RCM_VIEWED_WITH'),
            'similar' => Loc::getMessage('CP_BC_TPL_RCM_SIMILAR'),
            'any_similar' => Loc::getMessage('CP_BC_TPL_RCM_SIMILAR_ANY'),
            'any_personal' => Loc::getMessage('CP_BC_TPL_RCM_PERSONAL_WBEST'),
            'any' => Loc::getMessage('CP_BC_TPL_RCM_RAND')
        )
    );
}

$arTemplateParameters['RSFLYAWAY_PROP_ADDITIONAL_MEASURE'] = array(
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('RS.FLYAWAY.PROP_ADDITIONAL_MEASURE'),
    'TYPE' => 'LIST',
    'VALUES' => $listProp['SNL']
);
$arTemplateParameters['RSFLYAWAY_PROP_ADDITIONAL_MEASURE_RATIO'] = array(
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('RS.FLYAWAY.PROP_ADDITIONAL_MEASURE_RATIO'),
    'TYPE' => 'LIST',
    'VALUES' => $listProp['SNL']
);
