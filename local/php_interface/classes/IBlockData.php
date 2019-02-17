<?php
/**
 * Created by PhpStorm.
 * User: ASDAFF
 * Date: 16.05.2018
 * Time: 21:36
 *
 * Class name: IBlockData
 * Script URI: http://www.verstaem.com/examples/bitrix_start/IBlockData.php
 * Description: Class for getting iblock id by code
 * Version: 1.0
 * Author: Alorian
 * Author URI: http://www.verstaem.com/
 */

class IBlockData
{
    /**
     * @var array
     */
    protected static $byCode;

    /**
     * @param $code string IBlock symbolic code
     * @return bool|int
     */
    public static function getByCode($code)
    {
        if (empty(self::$byCode)) {
            self::getIBlocksData();
        }

        if (isset(self::$byCode[$code])) {
            return self::$byCode[$code];
        }

        return false;
    }

    /**
     * Fill self::$byCode variable by iblocks data
     */
    protected static function getIBlocksData()
    {
        if (!\Bitrix\Main\Loader::includeModule('iblock'))
            return;

        $iblocksByCode = array();

        $cache = new \CPHPCache();
        $cache_time = 86400;
        $cache_id = 'IBlockData' . SITE_ID;
        $cache_path = '/IBlockData/';

        if ($cache_time > 0 && $cache->InitCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->GetVars();
            if (is_array($res["iblocksByCode"]) && (count($res["iblocksByCode"]) > 0))
                $iblocksByCode = $res["iblocksByCode"];
        }

        if (empty($iblocksByCode)) {
            $rsIBlocks = \CIBlock::GetList(
                Array(),
                Array(
                    "SITE_ID" => SITE_ID,
                )
            );

            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache($cache_path);
            while ($arIBlock = $rsIBlocks->Fetch()) {
                $CACHE_MANAGER->RegisterTag("iblock_id_" . $arIBlock["ID"]);
                $iblocksByCode[$arIBlock['CODE']] = $arIBlock['ID'];
            }
            $CACHE_MANAGER->RegisterTag("iblock_id_new");
            $CACHE_MANAGER->EndTagCache();

            if ($cache_time > 0) {
                $cache->StartDataCache($cache_time, $cache_id, $cache_path);
                $cache->EndDataCache(array("iblocksByCode" => $iblocksByCode));
            }
        }

        self::$byCode = $iblocksByCode;
    }

}