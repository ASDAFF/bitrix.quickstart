<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 28.08.2018
 * Time: 15:57
 */

use Bitrix\Main\Web;

/**
 * класс для работы с короткими ссылками фильтра каталога
 * Class shortUrl
 */
class shortUrl
{

    const FIELD_NAME = 'UF_FILTER';


    /**Собираем цепочку навигации и выдаем в массиве
     *
     * @param $IBLOCK_ID
     * @param $CUR_SECTION_ID
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentNullException
     */
    public static function makeNavChain ($IBLOCK_ID, $CUR_SECTION_ID)
    {
        if ( ! $IBLOCK_ID) {
            throw new \Bitrix\Main\ArgumentNullException('IBLOCK_ID');
        }
        if ( ! $CUR_SECTION_ID) {
            throw new \Bitrix\Main\ArgumentNullException('CUR_SECTION ID');
        }

        $arPathAll = array();

        $res = CIBlock::GetByID($IBLOCK_ID);
        if ($arOldIblock = $res->GetNext()) {
            $arPathAll[] = array(
                'URL'  => $arOldIblock['LIST_PAGE_URL'],
                'NAME' => $arOldIblock['NAME'],
            );
        }

        $rsPath = CIBlockSection::GetNavChain(
            $IBLOCK_ID,
            $CUR_SECTION_ID,
            array(
                "ID",
                "NAME",
                "SECTION_PAGE_URL",
            )
        );
        $rsPath->SetUrlTemplates("", '#SECTION_CODE_PATH#/');
        while ($arPath = $rsPath->GetNext()) {
            $arPathAll[] = array(
                'URL'  => $arOldIblock['LIST_PAGE_URL'] . $arPath['SECTION_PAGE_URL'],
                'NAME' => $arPath['NAME'],
            );
        }

        return $arPathAll;

    }

    /**
     * Проверка наличия строки для вывода фильтра
     *
     * @param $IBLOCK_ID
     * @param $CUR_SECTION_ID
     *
     * @return array|bool|false|mixed
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public static function checkShortUrl ($IBLOCK_ID, $CUR_SECTION_ID)
    {

        if ( ! $IBLOCK_ID) {
            throw new \Bitrix\Main\ArgumentNullException('IBLOCK_ID');
        }
        if ( ! $CUR_SECTION_ID) {
            throw new \Bitrix\Main\ArgumentNullException('CUR_SECTION ID');
        }


        $con       = Bitrix\Main\Application::getConnection();
        //ищем поле с фильтром у нужного инфоблока
        $resEntity = $con->query(
            "SELECT ENTITY_ID FROM b_user_field WHERE FIELD_NAME = '" . self::FIELD_NAME . "' AND ENTITY_ID='IBLOCK_" . $IBLOCK_ID . "_SECTION'"
        );
        while ($arEntityID = $resEntity->fetch()) {
            //ищем значение поля
            $res = $con->query('SELECT VALUE_ID, ' . self::FIELD_NAME . ' FROM b_uts_iblock_' . $IBLOCK_ID . '_section WHERE ' . self::FIELD_NAME . ' IS NOT NULL AND VALUE_ID =' . $CUR_SECTION_ID);

            while ($arItem = $res->fetch()) {
                return $arItem;
            }
        }

        return false;
    }


    /**
     * формирует параметры компонентов и фильтров каталога из строки запроса
     *
     * @param $queryString
     */
    public static function setFilter (&$arResult, &$arParams, $queryString)
    {
        global $_GET;

        if ( ! $queryString) {
            throw new \Bitrix\Main\ArgumentNullException('queryString');
        }
        if ( ! $arResult['VARIABLES']['SECTION_ID']) {
            throw new \Bitrix\Main\ArgumentNullException(' arResult[VARIABLES][SECTION_ID]');
        }
        if ( ! $arParams['IBLOCK_ID']) {
            throw new \Bitrix\Main\ArgumentNullException('arParams[IBLOCK_ID]');
        }

        $uri = new \Bitrix\Main\Web\Uri($queryString);

        $path = str_replace('/catalog/', '/', $uri->getPath());

        $SECTION_CODE = array_pop(explode('/', trim($path, '/')));

        $arSection = \Bitrix\Iblock\SectionTable::getList(
            array(
                "select" => array("ID", "NAME", "IBLOCK_ID"),
                "filter" => array("ACTIVE" => "Y", "GLOBAL_ACTIVE" => "Y", "CODE" => $SECTION_CODE),
            )
        )->fetch();

        $arResult['VARIABLES']['SECTION_ID'] = $arSection['ID'];
        $arParams['IBLOCK_ID']               = $arSection['IBLOCK_ID'];

        parse_str($uri->getQuery(), $needGet);

        foreach ($needGet as $name => $value) {
            $_GET[$name] = $value;
        }

    }


}