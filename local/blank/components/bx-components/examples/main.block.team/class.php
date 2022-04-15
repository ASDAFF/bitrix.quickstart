<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main;
use Bitrix\Main\Localization\Loc as Loc;

\CBitrixComponent::includeComponentClass("system:standard.elements.list");

class MainBlockTeamComponent extends StandardElementListComponent
{
    public function onPrepareComponentParams($params)
    {
        $result = parent::onPrepareComponentParams($params);
        $result = array_merge($result, [
            'TITLE' => trim($params['TITLE']),
            'TEXT' => trim($params['TEXT'])
        ]);
        return $result;
    }

    protected function getResult()
    {
        $sort = [
            $this->arParams['SORT_FIELD1'] => $this->arParams['SORT_DIRECTION1'],
            $this->arParams['SORT_FIELD2'] => $this->arParams['SORT_DIRECTION2'],
        ];
        $filter = [
            'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
            'ACTIVE' => 'Y'
        ];
        $select = [
            'ID',
            'NAME',
            'PREVIEW_PICTURE',
            'PROPERTY_POSITION'
        ];
        $iterator = \CIBlockElement::GetList($sort, $filter, false, false, $select);
        while ($element = $iterator->Fetch()) {
            $img = '';
            if ($element['PREVIEW_PICTURE'] > 0) {
                $img = \CFile::ResizeImageGet($element['PREVIEW_PICTURE'], ['widht' => 110, 'height' => 110], BX_RESIZE_IMAGE_EXACT);
                if ($img['src'])
                    $img = $img['src'];
                else
                    $img = \CFile::ResizeImageGet($element['PREVIEW_PICTURE']);
            }
            $this->arResult['ITEMS'][] = [
                'ID' => $element['ID'],
                'IMG' => $img,
                'NAME' => $element['NAME'],
                'POSITION' => $element['PROPERTY_POSITION_VALUE']
            ];
        }
    }
}
