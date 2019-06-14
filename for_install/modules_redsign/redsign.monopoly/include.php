<?php

use \Bitrix\Main\Loader;


IncludeModuleLangFile(__FILE__);

$arClasses = array(
  'RSMonopoly' => 'classes/general/main.php',
);

Loader::registerAutoLoadClasses(
    'redsign.monopoly',
    $arClasses
);

if( !function_exists('RSMONOPOLY_GetComponentTemplateList') ) {
	function RSMONOPOLY_GetComponentTemplateList($componentName='') {
		$arReturn = array();
		$arTemplateInfo = CComponentUtil::GetTemplatesList($componentName);
        if(!empty($arTemplateInfo)) {
            sortByColumn($arTemplateInfo, array('TEMPLATE' => SORT_ASC, 'NAME' => SORT_ASC));
            $arTemplateList = array();
            $arSiteTemplateList = array(
                '.default' => GetMessage('RS.MONOPOLY.TEMPLATE_SITE_DEFAULT'),
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
                $strDescr = $template["TITLE"].' ('.('' != $template["TEMPLATE"] && '' != $arSiteTemplateList[$template["TEMPLATE"]] ? $arSiteTemplateList[$template["TEMPLATE"]] : GetMessage('RS.MONOPOLY.TEMPLATE_SITE_DEFAULT')).')';
                $arTemplateList[$template['NAME']] = $strDescr;
            }
            unset($template);
            $arReturn = $arTemplateList;
        }
        return $arReturn;
	}
}

if( !function_exists('RSMONOPOLY_AddComponentParameters') ) {
    function RSMONOPOLY_AddComponentParameters(&$arTemplateParameters,$arrParams=array()) {
        if( is_array($arTemplateParameters) && is_array($arrParams) && count($arrParams)>0 ) {
            if( in_array('blockName',$arrParams) ) {
                $arTemplateParameters['RSMONOPOLY_SHOW_BLOCK_NAME'] = array(
                    'NAME' => GetMessage('RS.MONOPOLY.SHOW_BLOCK_NAME'),
                    'TYPE' => 'CHECKBOX',
                    'VALUE' => 'Y',
                    'DEFAULT' => 'N',
                    'REFRESH' => 'Y',
                );
                $arTemplateParameters['RSMONOPOLY_BLOCK_NAME_IS_LINK'] = array(
                    'NAME' => GetMessage('RS.MONOPOLY.BLOCK_NAME_IS_LINK'),
                    'TYPE' => 'CHECKBOX',
                    'VALUE' => 'Y',
                    'DEFAULT' => 'N',
                );
            }
            if( in_array('owlSupport',$arrParams) ) {
                $arTemplateParameters['RSMONOPOLY_USE_OWL'] = array(
                    'NAME' => GetMessage('RS.MONOPOLY.USE_OWL'),
                    'TYPE' => 'CHECKBOX',
                    'VALUE' => 'Y',
                    'DEFAULT' => 'N',
                    'REFRESH' => 'Y',
                );
            }
            if( in_array('owlSettings',$arrParams) ) {
                $arTemplateParameters['RSMONOPOLY_OWL_CHANGE_SPEED'] = array(
                    'NAME' => GetMessage('RS.MONOPOLY.OWL_CHANGE_SPEED'),
                    'TYPE' => 'STRING',
                    'DEFAULT' => '2000',
                );
                $arTemplateParameters['RSMONOPOLY_OWL_CHANGE_DELAY'] = array(
                    'NAME' => GetMessage('RS.MONOPOLY.OWL_CHANGE_DELAY'),
                    'TYPE' => 'STRING',
                    'DEFAULT' => '8000',
                );
                $arTemplateParameters['RSMONOPOLY_OWL_PHONE'] = array(
                    'NAME' => GetMessage('RS.MONOPOLY.OWL_PHONE'),
                    'TYPE' => 'STRING',
                    'DEFAULT' => '1',
                );
                $arTemplateParameters['RSMONOPOLY_OWL_TABLET'] = array(
                    'NAME' => GetMessage('RS.MONOPOLY.OWL_TABLET'),
                    'TYPE' => 'STRING',
                    'DEFAULT' => '2',
                );
                $arTemplateParameters['RSMONOPOLY_OWL_PC'] = array(
                    'NAME' => GetMessage('RS.MONOPOLY.OWL_PC'),
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
                $arTemplateParameters['RSMONOPOLY_COLS_IN_ROW'] = array(
                    'NAME' => GetMessage('RS.MONOPOLY.COLS_IN_ROW'),
                    'TYPE' => 'LIST',
                    'VALUES' => $arValues,
                    'DEFAULT' => '4',
                );
            }
        }
    }
}

if( !function_exists('RSMONOPOLY_addPrices') ) {
    function RSMONOPOLY_addPrices($arItem,$params=array()) {
        $back = array();
        if(
            $params['RSMONOPOLY_PROP_PRICE']!='' &&
            $params['RSMONOPOLY_PROP_DISCOUNT']!='' &&
            $params['RSMONOPOLY_PROP_CURRENCY']!='' &&
            $params['RSMONOPOLY_PROP_PRICE_DECIMALS']!='' &&
            $arItem['PROPERTIES'][$params['RSMONOPOLY_PROP_CURRENCY']]['VALUE']!=''
            ) {
                $value = floatval($arItem['PROPERTIES'][$params['RSMONOPOLY_PROP_PRICE']]['VALUE']);
                $formated_value = number_format($value, $params['RSMONOPOLY_PROP_PRICE_DECIMALS'], '.', ' ');
                $discount = floatval($arItem['PROPERTIES'][$params['RSMONOPOLY_PROP_DISCOUNT']]['VALUE']);
                $formated_discount = number_format($discount, $params['RSMONOPOLY_PROP_PRICE_DECIMALS'], '.', ' ');
                $discount_price = $value - $discount;
                $formated_price = number_format($discount_price, $params['RSMONOPOLY_PROP_PRICE_DECIMALS'], '.', ' ');
                $back['VALUE'] = $value;
                $back['PRINT_VALUE'] = str_replace('#', $formated_value, $arItem['PROPERTIES'][$params['RSMONOPOLY_PROP_CURRENCY']]['VALUE']);
                $back['DISCOUNT_DIFF'] = number_format($discount, $params['RSMONOPOLY_PROP_PRICE_DECIMALS'], '.', ' ');
                $back['DISCOUNT_DIFF_PERCENT'] = 0;
                if($discount>0 && $value>0) {
                    $back['DISCOUNT_DIFF_PERCENT'] = round( $discount/$value*100 );
                }
                $back['DISCOUNT_VALUE'] = $discount_price;
                $back['PRINT_DISCOUNT_DIFF'] = str_replace('#', $formated_discount, $arItem['PROPERTIES'][$params['RSMONOPOLY_PROP_CURRENCY']]['VALUE']);
                $back['PRINT_DISCOUNT_VALUE'] = str_replace('#', $formated_price, $arItem['PROPERTIES'][$params['RSMONOPOLY_PROP_CURRENCY']]['VALUE']);
        }
        return $back;
    }
}

if( !function_exists('RSMONOPOLY_installIBlocks') ) {
    function RSMONOPOLY_installIBlocks($iblockType='',$iblockCode='',$wisardServiceRelativePath='',$wizardSiteID='') {
        /************/
        if( isset($iblockType) && isset($iblockCode) && isset($wisardServiceRelativePath) && isset($wizardSiteID) ) {
            $iblockXMLFile = $wisardServiceRelativePath.'/xml/'.$iblockType.'/'.$iblockCode.((LANGUAGE_ID == 'en') ? '-en' : '').'.xml';
            $iblockCodeWizPrefix = '_redsign_monopoly';
            $iblockXmlID = $iblockCode.'_'.$wizardSiteID;
            $iblockID = false;

            $rsIBlock = CIBlock::GetList(array(), array('CODE' => $iblockCode, 'XML_ID' => $iblockXmlID, 'TYPE' => $iblockType));
            if($rsIBlock && $arIBlock = $rsIBlock->Fetch()) {
                $iblockID = $arIBlock['ID'];
            }

            if($iblockID==false) {
                $iblockID = WizardServices::ImportIBlockFromXML(
                    $iblockXMLFile,
                    $iblockCode.$iblockCodeWizPrefix,
                    $iblockType,
                    $wizardSiteID,
                    $permissions = array(
                        '1' => 'X',
                        '2' => 'R'
                    )
                );

                if($iblockID < 1) {
                    $rsIBlock = CIBlock::GetList(array(), array('TYPE' => $iblockType, 'CODE' => $iblockCode.$iblockCodeWizPrefix, 'XML_ID' => $iblockXmlID));
                    if($arIBlock = $rsIBlock->Fetch()) {
                        $arrIBlockIDs[$iblockCode] = $arIBlock['ID'];
                    }
                    $iblockID = $arrIBlockIDs[$iblockCode];
                }

                if($iblockID < 1)
                    return;

                //IBlock fields settings
                $iblock = new CIBlock;
                $arFields = array(
                    'ACTIVE' => 'Y',
                    'FIELDS' => array (
                        'IBLOCK_SECTION'        => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
                        'ACTIVE'                => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'Y', ),
                        'ACTIVE_FROM'           => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '=now', ),
                        'ACTIVE_TO'             => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
                        'SORT'                  => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
                        'NAME'                  => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => '', ),
                        'PREVIEW_PICTURE'       => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'FROM_DETAIL' => 'Y', 'SCALE' => 'Y', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', ), ),
                        'PREVIEW_TEXT_TYPE'     => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ),
                        'PREVIEW_TEXT'          => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
                        'DETAIL_PICTURE'        => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => array ( 'SCALE' => 'Y', 'WIDTH' => '', 'HEIGHT' => '', 'IGNORE_ERRORS' => 'N', ), ),
                        'DETAIL_TEXT_TYPE'      => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => 'text', ),
                        'DETAIL_TEXT'           => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
                        'XML_ID'                => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
                        'CODE'                  => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => array( 'UNIQUE' => 'Y', 'TRANSLITERATION' => 'Y', 'TRANS_LEN' => '100', 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '-', 'TRANS_OTHER' => '-', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'N' ), ),
                        'SECTION_CODE'          => array ( 'IS_REQUIRED' => 'Y', 'DEFAULT_VALUE' => array( 'UNIQUE' => 'Y', 'TRANSLITERATION' => 'Y', 'TRANS_LEN' => '100', 'TRANS_CASE' => 'L', 'TRANS_SPACE' => '-', 'TRANS_OTHER' => '-', 'TRANS_EAT' => 'Y', 'USE_GOOGLE' => 'N' ), ),
                        'TAGS'                  => array ( 'IS_REQUIRED' => 'N', 'DEFAULT_VALUE' => '', ),
                    ),
                    'CODE' => $iblockCode,
                    'XML_ID' => $iblockXmlID,
                    'WF_TYPE' => 'N',
                    'NAME' => $iblock->GetArrayByID($iblockID, 'NAME')
                );
                $iblock->Update($iblockID, $arFields);
            } else {
                $arSites = array();
                $db_res = CIBlock::GetSite($iblockID);
                while($res = $db_res->Fetch())
                    $arSites[] = $res['LID'];
                if(!in_array(WIZARD_SITE_ID, $arSites)) {
                    $arSites[] = WIZARD_SITE_ID;
                    $iblock = new CIBlock;
                    $iblock->Update($iblockID, array('LID' => $arSites));
                }
            }
        }
        /************/
    }
}