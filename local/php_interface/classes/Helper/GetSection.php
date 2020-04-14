<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 05.11.2018
 * Time: 6:50
 */

namespace Helper;

class GetSection
{
    /**
     * @param $filter
     * @param $select
     * @return mixed
     * Получаем разделы и вложенные подразделы в иерархическом виде
     * Использование: GetSection::getSectionList(Array('IBLOCK_ID' => 'ID инфоблока'), Array('NAME','SECTION_PAGE_URL'));
     *
     */
    function getSectionList($filter, $select)
    {
        $dbSection = CIBlockSection::GetList(
            Array(
                'LEFT_MARGIN' => 'ASC',
            ),
            array_merge(
                Array(
                    'ACTIVE' => 'Y',
                    'GLOBAL_ACTIVE' => 'Y'
                ),
                is_array($filter) ? $filter : Array()
            ),
            false,
            array_merge(
                Array(
                    'ID',
                    'IBLOCK_SECTION_ID'
                ),
                is_array($select) ? $select : Array()
            )
        );

        while ($arSection = $dbSection->GetNext(true, false)) {
            $SID = $arSection['ID'];
            $PSID = (int)$arSection['IBLOCK_SECTION_ID'];
            $arLincs[$PSID]['CHILDS'][$SID] = $arSection;
            $arLincs[$SID] = &$arLincs[$PSID]['CHILDS'][$SID];
        }

        return array_shift($arLincs);
    }
}