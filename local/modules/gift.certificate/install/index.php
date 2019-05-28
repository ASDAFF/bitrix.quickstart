<?php

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\ModuleManager;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
use Bitrix\Main\Config\Option;
use Bitrix\Catalog\CatalogIblockTable;

// Получаем константы
include(str_replace('/install', '', dirname(__FILE__)) . '/prolog.php');


class gift_certificate extends CModule
{
    public $MODULE_ID;
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;
    public $MODULE_DIR;


    public function __construct ()
    {
        CModule::IncludeModule("highloadblock");
        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");

        $this->MODULE_ID          = basename(dirname(dirname(__FILE__)));
        $this->MODULE_NAME        = 'Подарочные сертификаты';
        $this->MODULE_DESCRIPTION = 'Продажа подарочных сертификатов с проверкой выполнения условий';
        $this->PARTNER_NAME       = 'ITsfera';
        $this->PARTNER_URI        = 'http://web.it-sfera.ru';
        $this->MODULE_DIR         = rtrim(preg_replace('~[\\\\/]+~', '/', dirname(dirname(__FILE__))), '/');

        // Получаем версию модуля из файла
        $arModuleVersion = array();
        include($this->MODULE_DIR . '/install/version.php');

        $this->MODULE_VERSION      = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];

    }

    public function doInstall ()
    {
        $this->installIBlock();

        $this->installRule();
        $this->installHLblock();
        $this->installEventType();
        ModuleManager::registerModule($this->MODULE_ID);

        RegisterModuleDependences("sale", "OnSaleStatusOrder", $this->MODULE_ID , "\GiftCertificate\Event",
            "OnSaleOrderSavedHandler");

    }

    public function installIBlock ()
    {

//создаем тип ИБ

        if ( ! CIBlockType::GetByID(ADMIN_MODULE_NAME)->fetch()) {
            $arFields = Array(
                'ID'       => ADMIN_MODULE_NAME,
                'SECTIONS' => 'N',
                'IN_RSS'   => 'N',
                'SORT'     => 100,
                'LANG'     => Array(
                    'ru' => Array(
                        'NAME'         => GetMessage(ADMIN_MODULE_LANG . "IBLOCKTYPE_NAME"),
                        'ELEMENT_NAME' => GetMessage(ADMIN_MODULE_LANG . "IBLOCKTYPE_ELEMENT_NAME"),
                    ),
                ),
            );

            $obBlocktype = new CIBlockType;
            $res         = $obBlocktype->Add($arFields);
            if ( ! $res) {
                $errors[] = $obBlocktype->LAST_ERROR;
            }
        }


        if ( ! CIBlock::GetList(array(), array('CODE' => ADMIN_MODULE_NAME))->fetch()) {

            //Создаем ИБ
            $ib       = new CIBlock;
            $arFields = Array(
                "ACTIVE"           => 'Y',
                "NAME"             => GetMessage(ADMIN_MODULE_LANG . "IBLOCK_NAME"),
                "CODE"             => ADMIN_MODULE_NAME,
                "LIST_PAGE_URL"    => '',
                "DETAIL_PAGE_URL"  => '',
                "IBLOCK_TYPE_ID"   => ADMIN_MODULE_NAME,
                "SITE_ID"          => Array("el", "mo"),
                "SORT"             => 100,
                "DESCRIPTION"      => GetMessage(ADMIN_MODULE_LANG . "IBLOCK_DESCRIPTION"),
                "DESCRIPTION_TYPE" => '',
                "GROUP_ID"         => Array("1" => "D", "2" => "R"),
            );

            if ($ID = $ib->Add($arFields)) {
                Option::set($this->MODULE_ID, "GiftCertificateIBlockID", $ID);

                $result = CatalogIblockTable::add(array(
                    'IBLOCK_ID'         => $ID,
                    'YANDEX_EXPORT'     => 'N',
                    'SUBSCRIPTION'      => 'N',
                    'VAT_ID'            => 0,
                    'PRODUCT_IBLOCK_ID' => 0,
                    'SKU_PROPERTY_ID'   => 0,
                ));

            }


        }

    }

    public function installRule ()
    {
        if ( ! CUrlRewriter::GetList(array("ID" => "local:" . $this->MODULE_ID))) {

            CUrlRewriter::Add(array(
                "CONDITION" => "#^/bitrix/admin/gift_certificate.php#",
                "PATH"      => '/local/modules/' . $this->MODULE_ID . '/admin/index.php',
                "ID"        => 'local:' . $this->MODULE_ID,
            ));

            // второе правило костылим в файле /admin/index.php
        }
    }

    public function installHLblock ()
    {

        //создаем HL блок для хранения успешных транзакций и очереди
        $arDataFields = array(
            0 => array(
                "NAME"       => "GiftCertificate",
                "TABLE_NAME" => "gift_sertificate_rule",
                "FIELDS"     => array(
                    array(
                        "NAME" => "PRODUCT_ID",
                        "TYPE" => "integer",
                    ),
                    array(
                        "NAME" => "DISCOUNT_ID",
                        "TYPE" => "integer",
                    ),
                    array(
                        "NAME" => "MAILTEMPLATE_ID",
                        "TYPE" => "integer",
                    ),
                    array(
                        "NAME" => "PRICE",
                        "TYPE" => "integer",
                    ),
                    array(
                        "NAME" => "DAYS",
                        "TYPE" => "integer",
                    ),

                ),
                "OPTION"     => "GiftCertificateTableID",
            ),

        );


        foreach ($arDataFields as $highloadBlockData) {

            $highLoadBlockId = null;

            if ($hlblock = HLBT::getList([
                'filter' => ['=NAME' => $highloadBlockData["NAME"]],
            ])->fetch()) {
                $highLoadBlockId = $hlblock['ID'];
            } else {
                $result = HLBT::add(
                    array(
                        "NAME"       => $highloadBlockData["NAME"],
                        "TABLE_NAME" => $highloadBlockData["TABLE_NAME"],
                    )
                );

                $highLoadBlockId = $result->getId();

                $userTypeEntity = new CUserTypeEntity();

                $typeArrs = $highloadBlockData['FIELDS'];

                foreach ($typeArrs as $typeArr) {

                    $userTypeData = array(
                        "ENTITY_ID"         => "HLBLOCK_" . $highLoadBlockId,
                        "FIELD_NAME"        => "UF_" . $typeArr["NAME"],
                        "USER_TYPE_ID"      => $typeArr["TYPE"],
                        "XML_ID"            => "XML_ID_" . $typeArr["NAME"],
                        "SORT"              => 100,
                        "MULTIPLE"          => "N",
                        "MANDATORY"         => "N",
                        "SHOW_FILTER"       => "N",
                        "SHOW_IN_LIST"      => "",
                        "EDIT_IN_LIST"      => "",
                        "IS_SEARCHABLE"     => "N",
                        "SETTINGS"          => array(
                            "DEFAULT_VALUE" => "",
                            "SIZE"          => "20",
                            "ROWS"          => "1",
                            "MIN_LENGTH"    => "0",
                            "MAX_LENGTH"    => "0",
                            "REGEXP"        => "",
                        ),
                        "EDIT_FORM_LABEL"   => array(
                            "ru" => "",
                            "en" => "",
                        ),
                        "LIST_COLUMN_LABEL" => array(
                            "ru" => "",
                            "en" => "",
                        ),
                        "LIST_FILTER_LABEL" => array(
                            "ru" => "",
                            "en" => "",
                        ),
                        "ERROR_MESSAGE"     => array(
                            "ru" => "",
                            "en" => "",
                        ),
                        "HELP_MESSAGE"      => array(
                            "ru" => "",
                            "en" => "",
                        ),
                    );
                    $userTypeId   = $userTypeEntity->Add($userTypeData);
                }

            }

            if ($highLoadBlockId) {
                Option::set($this->MODULE_ID, $highloadBlockData["OPTION"], $highLoadBlockId);
            }
        }

    }

    public function installEventType ()
    {
        $et          = new CEventType;
        $eventTypeID = $et->Add(array(
            "LID"         => SITE_ID,
            "EVENT_NAME"  => "ITSFERA_GIFT_CERTIFICATE",
            "NAME"        => "Подарочные сертификаты",
            "DESCRIPTION" => "Сертификат на скидку",
        ));

        if ($eventTypeID) {
            COption::SetOptionString($this->MODULE_ID, "GiftCertificateEventTypeID", $eventTypeID);

        }

    }

    public function doUninstall ()
    {
        $this->uninstallIBlock();
        $this->uninstallRule();
        $this->uninstallHLblock();
        $this->uninstallEventType();

        COption::RemoveOption($this->MODULE_ID);
        ModuleManager::unregisterModule($this->MODULE_ID);
    }

    public function uninstallIBlock ()
    {
        CIBlockType::Delete(ADMIN_MODULE_NAME);
    }

    public function uninstallRule ()
    {
        if (CUrlRewriter::GetList(array("ID" => "local:" . $this->MODULE_ID))) {
            CUrlRewriter::Delete(array(
                "ID" => 'local:' . $this->MODULE_ID,
            ));
        }
    }

    public function uninstallHLblock ()
    {
        if ($hlBlock = Option::get($this->MODULE_ID, "GiftCertificateTableID")) {
            \Bitrix\Highloadblock\HighloadBlockTable::delete($hlBlock);
        }

    }

    public function uninstallEventType ()
    {

        if ($eventTypeID = COption::GetOptionString($this->MODULE_ID, "GiftCertificateEventTypeID")) {
            $et = new CEventType;
            $et->Delete($eventTypeID);
        }
    }

}