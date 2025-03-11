<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

try
{
    if (!Main\Loader::includeModule('iblock')) {
        throw new Main\LoaderException(Loc::getMessage('MAIN_BLOCK_TEAM_PARAMETERS_IBLOCK_MODULE_NOT_INSTALLED'));
    }
    $iblockTypes = \CIBlockParameters::GetIBlockTypes(["-" => " "]);
    $iblocksCode = ["" => " "];
    if (isset($arCurrentValues['IBLOCK_TYPE']) && strlen($arCurrentValues['IBLOCK_TYPE'])) {
        $filter = [
            'TYPE' => $arCurrentValues['IBLOCK_TYPE'],
            'ACTIVE' => 'Y'
        ];
        $iterator = \CIBlock::GetList(['SORT' => 'ASC'], $filter);
        while ($iblock = $iterator->GetNext()) {
            $iblocksCode[$iblock['CODE']] = $iblock['NAME'];
        }
    }
    $sortFields = [
        'ID' => Loc::getMessage('MAIN_BLOCK_TEAM_PARAMETERS_SORT_ID'),
        'NAME' => Loc::getMessage('MAIN_BLOCK_TEAM_PARAMETERS_SORT_NAME'),
        'ACTIVE_FROM' => Loc::getMessage('MAIN_BLOCK_TEAM_PARAMETERS_SORT_ACTIVE_FROM'),
        'SORT' => Loc::getMessage('MAIN_BLOCK_TEAM_PARAMETERS_SORT_SORT')
    ];
    $sortDirection = [
        'ASC' => Loc::getMessage('MAIN_BLOCK_TEAM_PARAMETERS_SORT_ASC'),
        'DESC' => Loc::getMessage('MAIN_BLOCK_TEAM_PARAMETERS_SORT_DESC')
    ];

	$arComponentParameters = array(
		'GROUPS' => array(
		),
		'PARAMETERS' => array(
            'IBLOCK_TYPE' => [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('MAIN_BLOCK_TEAM_PARAMETERS_IBLOCK_TYPE'),
                'TYPE' => 'LIST',
                'VALUES' => $iblockTypes,
                'DEFAULT' => '',
                'REFRESH' => 'Y'
            ],
            'IBLOCK_CODE' => [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('MAIN_BLOCK_TEAM_PARAMETERS_IBLOCK_CODE'),
                'TYPE' => 'LIST',
                'VALUES' => $iblocksCode
            ],
            'SORT_FIELD1' => [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('MAIN_BLOCK_TEAM_PARAMETERS_SORT_FIELD1'),
                'TYPE' => 'LIST',
                'VALUES' => $sortFields
            ],
            'SORT_DIRECTION1' => [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('MAIN_BLOCK_TEAM_PARAMETERS_SORT_DIRECTION1'),
                'TYPE' => 'LIST',
                'VALUES' => $sortDirection
            ],
            'SORT_FIELD2' => [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('MAIN_BLOCK_TEAM_PARAMETERS_SORT_FIELD2'),
                'TYPE' => 'LIST',
                'VALUES' => $sortFields
            ],
            'SORT_DIRECTION2' => [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('MAIN_BLOCK_TEAM_PARAMETERS_SORT_DIRECTION2'),
                'TYPE' => 'LIST',
                'VALUES' => $sortDirection
            ],
            'TITLE' => Array(
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('MAIN_BLOCK_TEAM_PARAMETERS_TITLE'),
                'TYPE' => 'STRING',
            ),
            'TEXT' => Array(
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('MAIN_BLOCK_TEAM_PARAMETERS_TEXT'),
                'TYPE' => 'STRING',
            ),
            'CACHE_TIME' => [
                'DEFAULT' => 3600
            ],
		)
	);
}
catch (Main\LoaderException $e)
{
	ShowError($e->getMessage());
}
?>