<?
/** @var array $arCurrentValues */

use Bitrix\Main\Loader,
	 Bitrix\Main\Localization\Loc,
	 Bitrix\Main\Config\Option;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

Loc::loadMessages(__FILE__);




use Bitrix\Iblock,
	 Bitrix\Catalog,
	 Bitrix\Sale\Location\LocationTable;


$sale_module=true;

if(!Loader::includeModule("sale")) {
    $sale_module=false;
}


$arComponentParameters['GROUPS'] = array(

	 'GROUP_BASE'    => array(
			'NAME' => Loc::GetMessage("webes_ic_parameters_BASE_PARAMS"),
			'SORT' => 110,
	 ),

);

$arComponentParameters['PARAMETERS'] = array(
      'VIEW_PARAMS'       => array(
        'PARENT'  => 'GROUP_BASE',
        'NAME'    => Loc::GetMessage("webes_ic_parameters_VIEW_TABLE"),
        'TYPE'    => 'CHECKBOX',
        'DEFAULT' => 'N',
    ),
    'ELEMENT_ID' => array(
        'PARENT'  => 'GROUP_BASE',
        'NAME'    => Loc::GetMessage("webes_ic_parameters_VAR_ID"),
        'TYPE'    => 'STRING',
        'DEFAULT' => '={$arResult[\'ID\']}',
    ),


);

if(!$sale_module)
    $arComponentParameters['PARAMETERS']['PRICE_PARAM_ID'] =
        array(
            'PARENT'  => 'GROUP_BASE',
            'NAME'    => Loc::GetMessage("webes_ic_parameters_ID_PRICE"),
            'TYPE'    => 'STRING',
            'DEFAULT' => '',
    );




?>