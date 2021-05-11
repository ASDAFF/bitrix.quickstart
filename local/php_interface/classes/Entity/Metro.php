<?php
/**
 * Created by PhpStorm.
 * User: anastasiya.zayarnaya
 * Date: 16.08.2018
 * Time: 10:07
 */

namespace Entity;

use Defa\Core\Tools\CIblock,
    CIBlockElement,
    Bitrix\Main\Loader;

class Metro
{
    protected $intIblockLines = null;
    protected $intIblockStations = null;
    protected static $instance = null;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    private function __construct()
    {
        Loader::includeModule('iblock');
        $this->intIblockLines = CIblock::GetIDByCode('metro_line');
        $this->intIblockStations = CIblock::GetIDByCode('metro_stations');
    }

    public function getListById($id)
    {
        $arStations = $arLines = $arLinesId = [];

        if(empty($id))
            return $arStations;

        $rsStations = CIBlockElement::GetList(
            [],
            ['IBLOCK_ID' => $this->intIblockStations, 'ACTIVE' => 'Y', 'ID' => $id],
            false,
            false,
            ['ID', 'NAME', 'PROPERTY_LINE']
        );

        while($arStation = $rsStations->Fetch()){
            $arStations[(string) $arStation['ID']] = [
                'ID' => $arStation['ID'],
                'NAME' => $arStation['NAME'],
                'LINE_ID' => $arStation['PROPERTY_LINE_VALUE'],
            ];
            $arLinesId[] = $arStation['PROPERTY_LINE_VALUE'];
        }

        $arLinesId = array_unique(array_diff($arLinesId, ['']));

        if (count($arLinesId)){
            $rsLines = CIBlockElement::GetList(
                [],
                ['IBLOCK_ID' => $this->intIblockLines, 'ACTIVE' => 'Y', 'ID' => $arLinesId],
                false,
                false,
                ['ID', 'NAME', 'PROPERTY_COLOR']
            );

            while($arLine = $rsLines->Fetch()){
                $arLines[(string) $arLine['ID']] = [
                    'NAME' => $arLine['NAME'],
                    'COLOR' => $arLine['PROPERTY_COLOR_VALUE'],
                ];
            }
        }

        foreach($arStations as &$arStation){
            $arStation['LINE_NAME'] = $arLines[$arStation['LINE_ID']]['NAME'];
            $arStation['LINE_COLOR'] = $arLines[$arStation['LINE_ID']]['COLOR'];
        }

        return $arStations;

    }
}