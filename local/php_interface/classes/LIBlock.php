<?php

class LIblock
{
    /**
     * @var array
     */
    protected static $iblocks = array();

    /**
     * @param $code string IBlock symbolic code
     * @return bool|int
     */
    public static function getId($code)
    {
        if(empty(static::$iblocks))
            static::loadData();

        if(isset(static::$iblocks[$code]))
            return static::$iblocks[$code];

        return false;
    }

    /**
     * Fill static::$iblocks variable by iblocks data
     */
    protected static function loadData()
    {
        if(!\Bitrix\Main\Loader::includeModule('iblock'))
            return;

        $iblockList = array();

        $cache = new \CPHPCache();
        $cache_time = 86400;
        $cache_id = 'LIblock' . SITE_ID;
        $cache_path = '/LIblock/';

        if($cache_time > 0 && $cache->InitCache($cache_time, $cache_id, $cache_path))
        {
            $res = $cache->GetVars();
            if(is_array($res['iblocks']) && (count($res['iblocks']) > 0))
                $iblockList = $res['iblocks'];
        }

        if(empty($iblockList))
        {
            $rsIBlocks = \CIBlock::GetList(
                Array(),
                Array(
                    'SITE_ID' => SITE_ID,
                )
            );

            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache($cache_path);
            while($arIBlock = $rsIBlocks->Fetch())
            {
                $CACHE_MANAGER->RegisterTag('iblock_id_' . $arIBlock['ID']);
                $iblockList[$arIBlock['CODE']] = $arIBlock['ID'];
            }
            $CACHE_MANAGER->RegisterTag('iblock_id_new');
            $CACHE_MANAGER->EndTagCache();

            if($cache_time > 0)
            {
                $cache->StartDataCache($cache_time, $cache_id, $cache_path);
                $cache->EndDataCache(array('iblocks' => $iblockList));
            }
        }

        static::$iblocks = $iblockList;
    }

}