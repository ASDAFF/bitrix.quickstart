<?php
IncludeModuleLangFile(__FILE__);

CModule::AddAutoloadClasses(
	'redsign.flyaway',
	array(
		'RsFlyaway' => 'classes/general/main.php',
	)
);

if( !function_exists('RSFLYAWAY_GetComponentTemplateList') ) {
	function RSFLYAWAY_GetComponentTemplateList($componentName='') {
		$arReturn = array();
		$arTemplateInfo = CComponentUtil::GetTemplatesList($componentName);
        if(!empty($arTemplateInfo)) {
            sortByColumn($arTemplateInfo, array('TEMPLATE' => SORT_ASC, 'NAME' => SORT_ASC));
            $arTemplateList = array();
            $arSiteTemplateList = array(
                '.default' => GetMessage('RS.FLYAWAY.TEMPLATE_SITE_DEFAULT'),
            );
            $arTemplateID = array();
            foreach ($arTemplateInfo as &$template) {
                if ('' != $template["TEMPLATE"] && '.default' != $template["TEMPLATE"])
                    $arTemplateID[] = $template["TEMPLATE"];
                if (!isset($template['TITLE']))
                    $template['TITLE'] = $template['NAME'];
            }
            unset($template);

            if (!empty($arTemplateID)) {
                $rsSiteTemplates = CSiteTemplate::GetList(
                    array(),
                    array("ID" => $arTemplateID),
                    array()
                );
                while ($arSitetemplate = $rsSiteTemplates->Fetch()) {
                    $arSiteTemplateList[$arSitetemplate['ID']] = $arSitetemplate['NAME'];
                }
            }

            foreach ($arTemplateInfo as &$template) {
                if (isset($arHiddenTemplates[$template['NAME']]))
                    continue;
                $strDescr = $template["TITLE"].' ('.('' != $template["TEMPLATE"] && '' != $arSiteTemplateList[$template["TEMPLATE"]] ? $arSiteTemplateList[$template["TEMPLATE"]] : GetMessage('RS.FLYAWAY.TEMPLATE_SITE_DEFAULT')).')';
                $arTemplateList[$template['NAME']] = $strDescr;
            }
            unset($template);
            $arReturn = $arTemplateList;
        }
        return $arReturn;
	}
}

if( !function_exists('RSFLYAWAY_AddComponentParameters') ) {
    function RSFLYAWAY_AddComponentParameters(&$arTemplateParameters,$arrParams=array()) {
        if( is_array($arTemplateParameters) && is_array($arrParams) && count($arrParams)>0 ) {
            if( in_array('blockName',$arrParams) ) {
                $arTemplateParameters['RSFLYAWAY_SHOW_BLOCK_NAME'] = array(
                    'NAME' => GetMessage('RS.FLYAWAY.SHOW_BLOCK_NAME'),
                    'TYPE' => 'CHECKBOX',
                    'VALUE' => 'Y',
                    'DEFAULT' => 'N',
                    'REFRESH' => 'Y',
                );
                $arTemplateParameters['RSFLYAWAY_BLOCK_NAME_IS_LINK'] = array(
                    'NAME' => GetMessage('RS.FLYAWAY.BLOCK_NAME_IS_LINK'),
                    'TYPE' => 'CHECKBOX',
                    'VALUE' => 'Y',
                    'DEFAULT' => 'N',
                );
            }
            if( in_array('owlSupport',$arrParams) ) {
                $arTemplateParameters['RSFLYAWAY_USE_OWL'] = array(
                    'NAME' => GetMessage('RS.FLYAWAY.USE_OWL'),
                    'TYPE' => 'CHECKBOX',
                    'VALUE' => 'Y',
                    'DEFAULT' => 'N',
                    'REFRESH' => 'Y',
                );
            }
            if( in_array('owlSettings',$arrParams) ) {
                $arTemplateParameters['RSFLYAWAY_OWL_CHANGE_SPEED'] = array(
                    'NAME' => GetMessage('RS.FLYAWAY.OWL_CHANGE_SPEED'),
                    'TYPE' => 'STRING',
                    'DEFAULT' => '2000',
                );
                $arTemplateParameters['RSFLYAWAY_OWL_CHANGE_DELAY'] = array(
                    'NAME' => GetMessage('RS.FLYAWAY.OWL_CHANGE_DELAY'),
                    'TYPE' => 'STRING',
                    'DEFAULT' => '8000',
                );
                $arTemplateParameters['RSFLYAWAY_OWL_PHONE'] = array(
                    'NAME' => GetMessage('RS.FLYAWAY.OWL_PHONE'),
                    'TYPE' => 'STRING',
                    'DEFAULT' => '1',
                );
                $arTemplateParameters['RSFLYAWAY_OWL_TABLET'] = array(
                    'NAME' => GetMessage('RS.FLYAWAY.OWL_TABLET'),
                    'TYPE' => 'STRING',
                    'DEFAULT' => '2',
                );
                $arTemplateParameters['RSFLYAWAY_OWL_PC'] = array(
                    'NAME' => GetMessage('RS.FLYAWAY.OWL_PC'),
                    'TYPE' => 'STRING',
                    'DEFAULT' => '3',
                );
            }
            if( in_array('bootstrapCols',$arrParams) ) {
                $arValues = array(
                    '12' => '1',
                    '6' => '2',
                    '4' => '3',
                    '3' => '4',
                    '2' => '6',
                );
                $arTemplateParameters['RSFLYAWAY_COLS_IN_ROW'] = array(
                    'NAME' => GetMessage('RS.FLYAWAY.COLS_IN_ROW'),
                    'TYPE' => 'LIST',
                    'VALUES' => $arValues,
                    'DEFAULT' => '4',
                );
            }
        }
    }
}

if( !function_exists('RSFLYAWAY_addPrices') ) {
    function RSFLYAWAY_addPrices($arItem,$params=array()) {
        $back = array();
        if(
            $params['RSFLYAWAY_PROP_PRICE']!='' &&
            $params['RSFLYAWAY_PROP_DISCOUNT']!='' &&
            $params['RSFLYAWAY_PROP_CURRENCY']!='' &&
            $params['RSFLYAWAY_PROP_PRICE_DECIMALS']!='' &&
            $arItem['PROPERTIES'][$params['RSFLYAWAY_PROP_CURRENCY']]['VALUE']!=''
            ) {
                $value = floatval($arItem['PROPERTIES'][$params['RSFLYAWAY_PROP_PRICE']]['VALUE']);
                $formated_value = number_format($value, $params['RSFLYAWAY_PROP_PRICE_DECIMALS'], '.', ' ');
                $discount = floatval($arItem['PROPERTIES'][$params['RSFLYAWAY_PROP_DISCOUNT']]['VALUE']);
                $formated_discount = number_format($discount, $params['RSFLYAWAY_PROP_PRICE_DECIMALS'], '.', ' ');
                $discount_price = $value - $discount;
                $formated_price = number_format($discount_price, $params['RSFLYAWAY_PROP_PRICE_DECIMALS'], '.', ' ');
                $back['VALUE'] = $value;
                $back['PRINT_VALUE'] = str_replace('#', $formated_value, $arItem['PROPERTIES'][$params['RSFLYAWAY_PROP_CURRENCY']]['VALUE']);
                $back['DISCOUNT_DIFF'] = number_format($discount, $params['RSFLYAWAY_PROP_PRICE_DECIMALS'], '.', ' ');
                $back['DISCOUNT_DIFF_PERCENT'] = 0;
                if($discount>0 && $value>0) {
                    $back['DISCOUNT_DIFF_PERCENT'] = round( $discount/$value*100 );
                }
                $back['DISCOUNT_VALUE'] = $discount_price;
                $back['PRINT_DISCOUNT_DIFF'] = str_replace('#', $formated_discount, $arItem['PROPERTIES'][$params['RSFLYAWAY_PROP_CURRENCY']]['VALUE']);
                $back['PRINT_DISCOUNT_VALUE'] = str_replace('#', $formated_price, $arItem['PROPERTIES'][$params['RSFLYAWAY_PROP_CURRENCY']]['VALUE']);
        }
        return $back;
    }
}
