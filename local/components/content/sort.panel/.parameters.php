<?
/**
 * Created by Alexey Panov.
 * Date: 24.12.2016
 * Time: 11:30
 *
 * @author    Alexey Panov <panov@codeblog.pro>
 * @copyright Copyright ? 2016, Alexey Panov
 * @git repository https://github.com/PanovAlexey/sort.panel
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;

Loader::includeModule('iblock');
Loader::includeModule('catalog');

/**
 * Getting iblock types list and iblocks list
 */
$iblockElementTypeList = \CIBlockParameters::GetIBlockTypes();

$iblockElementList        = array();
$iblockElementFilter      = (!empty($arCurrentValues['IBLOCK_TYPE']) ? [
    'TYPE'   => $arCurrentValues['IBLOCK_TYPE'],
    'ACTIVE' => 'Y',
] : ['ACTIVE' => 'Y']);
$iblockElementsCollection = \CIBlock::GetList(['SORT' => 'ASC'], $iblockElementFilter);
while ($iblockElement = $iblockElementsCollection->Fetch()) {
    $iblockElementList[$iblockElement['ID']] = '[' . $iblockElement['ID'] . '] ' . $iblockElement['NAME'];
}

/**
 *  Getting properties list for sorting
 */
$propertyList         = array();
$propertiesCollection = \CIBlockProperty::GetList(array(
    'sort' => 'asc',
    'name' => 'asc'
), array(
    'ACTIVE'    => 'Y',
    'MULTIPLE'  => 'N',
    'IBLOCK_ID' => (isset($arCurrentValues['IBLOCK_ID']) ? $arCurrentValues['IBLOCK_ID'] : $arCurrentValues['ID']),
));
while ($propertyElement = $propertiesCollection->Fetch()) {
    $arProperty[$propertyElement['CODE']] = '[' . $propertyElement['CODE'] . '] ' . $propertyElement['NAME'];
    if (in_array($propertyElement['PROPERTY_TYPE'], array(
        'N',
        'L',
        'S'
    ))) {
        $propertyList[$propertyElement['CODE']] = '[' . $propertyElement['CODE'] . '] ' . $propertyElement['NAME'];
    }
}

$priceList = [];

if (Loader::includeModule('catalog')) {
    /**
     * Getting priceslist for sorting
     */
    $priceTypeCollection = \CCatalogGroup::GetList(array('SORT' => 'ASC'), array());
    while ($priceType = $priceTypeCollection->Fetch()) {
        $priceList[$priceType['ID']] = '[' . 'catalog_PRICE_' . $priceType['ID'] . '] ' . $priceType['NAME_LANG'];
    }
}

include 'class.php';
$sortParametrs = CCodeblogProSortPanelComponent::getSortOrderList();

$fieldsList = array();
$fieldsDefaultList = $sortParametrs['FIELDS_DEFAULT_LIST'];

foreach ($sortParametrs['TYPES_LIST'] as $type) {
    $fieldsList[$type['CODE']] = '[' . $type['CODE'] . '] ' . $type['NAME'];
}

$sortOrdersList = $sortParametrs['ORDERS_LIST'];
$sortOrdersDefaultList = $sortParametrs['ORDERS_DEFAULT_LIST'];

$arComponentParameters = array(
    'GROUPS'     => array(),
    'PARAMETERS' => array(
        'IBLOCK_TYPE' => array(
            'PARENT'  => 'DATA_SOURCE',
            'NAME'    => GetMessage('SORT_PANEL_IBLOCK_TYPE_TITLE'),
            'TYPE'    => 'LIST',
            'VALUES'  => $iblockElementTypeList,
            'REFRESH' => 'Y',
        ),
        'IBLOCK_ID'             => array(
            'PARENT'            => 'DATA_SOURCE',
            'NAME'              => GetMessage('SORT_PANEL_IBLOCK_ID_TITLE'),
            'TYPE'              => 'LIST',
            'ADDITIONAL_VALUES' => 'Y',
            'VALUES'            => $iblockElementList,
            'REFRESH'           => 'Y',
        ),
        'PROPERTY_CODE'         => array(
            'PARENT'            => 'DATA_SOURCE',
            'NAME'              => GetMessage('SORT_PANEL_PROPERTY_CODE_TITLE'),
            'TYPE'              => 'LIST',
            'MULTIPLE'          => 'Y',
            'VALUES'            => $propertyList,
            'ADDITIONAL_VALUES' => 'N',
        ),
        'FIELDS_CODE'           => array(
            'PARENT'            => 'DATA_SOURCE',
            'NAME'              => GetMessage('SORT_PANEL_FIELD_CODE_TITLE'),
            'TYPE'              => 'LIST',
            'MULTIPLE'          => 'Y',
            'VALUES'            => $fieldsList,
            'DEFAULT'           => $fieldsDefaultList,
            'ADDITIONAL_VALUES' => 'N',
        ),
        'PRICE_CODE'            => array(
            'PARENT'            => 'DATA_SOURCE',
            'NAME'              => GetMessage('SORT_PANEL_PRICE_CODE_TITLE'),
            'TYPE'              => 'LIST',
            'MULTIPLE'          => 'Y',
            'VALUES'            => $priceList,
            'ADDITIONAL_VALUES' => 'N',
        ),
        'SORT_ORDER'            => array(
            'PARENT'            => 'DATA_SOURCE',
            'NAME'              => GetMessage('SORT_PANEL_SORT_ORDER_TITLE'),
            'TYPE'              => 'LIST',
            'MULTIPLE'          => 'Y',
            'VALUES'            => $sortOrdersList,
            'DEFAULT'           => $sortOrdersDefaultList,
            'ADDITIONAL_VALUES' => 'N',
        ),
        'INCLUDE_SORT_TO_SESSION' => array(
            'PARENT'  => 'ADDITIONAL_SETTINGS',
            'NAME'    => GetMessage('SORT_PANEL_INCLUDE_SORT_TO_SESSION'),
            'TYPE'    => 'CHECKBOX',
            'DEFAULT' => 'Y',
        ),
        'SORT_NAME'               => array(
            'PARENT'  => 'DATA_SOURCE',
            'NAME'    => GetMessage('SORT_PANEL_CODE_SORT_RETURN'),
            'TYPE'    => 'STRING',
            'DEFAULT' => 'SORT',
        ),
        'ORDER_NAME'              => array(
            'PARENT'  => 'DATA_SOURCE',
            'NAME'    => GetMessage('SORT_PANEL_CODE_ORDER_RETURN'),
            'TYPE'    => 'STRING',
            'DEFAULT' => 'ORDER',
        ),
        'CACHE_TIME'  => array('DEFAULT' => 36000000),
    ),
);