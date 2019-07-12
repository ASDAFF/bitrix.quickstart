<?php
/**
 * Created by JetBrains PhpStorm.
 * User: anton
 * Date: 11.07.13
 * Time: 12:02
 * To change this template use File | Settings | File Templates.
 */

abstract class Novagroup_Classes_Abstract_Menu {

    static public function getTreeMenu($arResult,$MAX_LEVEL_DEPTH=4)
    {
        $MAX_LEVEL_DEPTH = ($MAX_LEVEL_DEPTH>1) ? $MAX_LEVEL_DEPTH:2;

        $arCatalogID = array();
        foreach($arResult as $arKey => $arItem)
        {
            $PARENT[$arItem['DEPTH_LEVEL']] = $arKey;
            //присваиваем ид родителя ноды
            if($arItem['DEPTH_LEVEL']>1)
            {
                $arResult[$arKey]['PARENT_ID'] = $PARENT[$arItem['DEPTH_LEVEL']-1];
            }
            // если попался каталог - запоминаем его ИД
            if($arItem['DEPTH_LEVEL']=="1" and $arItem['PARAMS']['FROM_IBLOCK']==1)
            {
                $arCatalogID[] = $arKey;
            }
        }

        for($LEVEL = $MAX_LEVEL_DEPTH; $LEVEL>1; $LEVEL--)
        {
            //привязываем детей с 4 по 2 уровни
            foreach($arResult as $arKey => $arItem)
            {
                if($arItem['DEPTH_LEVEL']==$LEVEL)
                {
                    //привязываем детей новому родителю
                    $arResult[$arItem['PARENT_ID']]['CHILDS'][$arKey]=$arItem;
                    //удаляем родные связи
                    unset($arResult[$arKey]);
                }
                if($arItem['DEPTH_LEVEL']>$MAX_LEVEL_DEPTH and $arItem['PARAMS']['FROM_IBLOCK']==1)
                {
                    unset($arResult[$arKey]);
                }
                if($arItem['DEPTH_LEVEL']>($MAX_LEVEL_DEPTH-1) and $arItem['PARAMS']['FROM_IBLOCK']!==1)
                {
                    unset($arResult[$arKey]);
                }
            }
        }

        //меню- каталог первого уровня
        if(count($arCatalogID)>0 )
        {
            foreach($arCatalogID as $ID)
            {
                if(is_array($arResult[$ID]['CHILDS']))
                {
                    foreach($arResult[$ID]['CHILDS'] as $arKey => $arItem)
                    {
                        $arResult[$arKey] = $arItem;
                    }
                    unset($arResult[$ID]);
                }
            }

        }

        ksort($arResult);
        return ($arResult);
    }

    static function getLeftMenuByCurrentPage()
    {
        global $APPLICATION;
        $lm = new CMenu("left");
        $lm->Init($APPLICATION->GetCurDir(), true);
        //return ($lm->arMenu);
        return self::getTreeMenu($lm->arMenu,2);
    }
}