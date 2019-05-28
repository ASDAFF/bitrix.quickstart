<?php

defined('B_PROLOG_INCLUDED') and (B_PROLOG_INCLUDED === true) or die();

use Bitrix\Main\ModuleManager;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;


if (class_exists('discount_updater')) {
    return;
}

class order_coupon extends CModule
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

        $this->MODULE_ID          = basename(dirname(dirname(__FILE__)));
        $this->MODULE_NAME        = 'Купон за заказ';
        $this->MODULE_DESCRIPTION = 'Создает персональный купон на следующую покупку при оплате заказа';
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
        $this->installRule();
        $this->installHLblock();
        $this->installEventType();
        ModuleManager::registerModule($this->MODULE_ID);

        RegisterModuleDependences("sale", "OnSaleStatusOrder", $this->MODULE_ID , "\OrderCoupon\Event",
            "OnSaleOrderSavedHandler");

    }

    public function doUninstall ()
    {
        $this->uninstallRule();
        $this->uninstallHLblock();
        $this->uninstallEventType();

        COption::RemoveOption($this->MODULE_ID);
        ModuleManager::unregisterModule($this->MODULE_ID);
    }

    public function installRule ()
    {
        if ( ! CUrlRewriter::GetList(array("ID" => "local:" . $this->MODULE_ID))) {

            CUrlRewriter::Add(array(
                "CONDITION" => "#^/bitrix/admin/order_coupon.php#",
                "PATH"      => '/local/modules/' . $this->MODULE_ID . '/admin/index.php',
                "ID"        => 'local:' . $this->MODULE_ID,
            ));

            // второе правило костылим в файле /admin/index.php
        }
    }

    public function uninstallRule ()
    {
        if (CUrlRewriter::GetList(array("ID" => "local:" . $this->MODULE_ID))) {
            CUrlRewriter::Delete(array(
                "ID" => 'local:' . $this->MODULE_ID,
            ));
        }
    }


    public function installHLblock ()
    {

        //создаем HL блок для хранения успешных транзакций и очереди
        $arDataFields = array(
            0 => array(
                "NAME"       => "OrderCoupon",
                "TABLE_NAME" => "order_coupon",
                "FIELDS"     => array(
                    array(
                        "NAME" => "DISCOUNT_ID",
                        "TYPE" => "integer",
                    ),
                    array(
                        "NAME" => "MAILTEMPLATE_ID",
                        "TYPE" => "integer",
                    ),
                    array(
                        "NAME" => "DAYS",
                        "TYPE" => "integer",
                    ),
                    array(
                        "NAME" => "CONDITIONS",
                        "TYPE" => "string",
                    ),

                ),
                "OPTION"     => "OrderCouponTableID",
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
                COption::SetOptionString($this->MODULE_ID, $highloadBlockData["OPTION"], $highLoadBlockId);

            }
        }

    }

    public function uninstallHLblock ()
    {

        if ($hlBlock = COption::GetOptionString($this->MODULE_ID, "OrderCouponTableID")) {
            \Bitrix\Highloadblock\HighloadBlockTable::delete($hlBlock);
        }

    }

    public function installEventType ()
    {
        $et          = new CEventType;
        $eventTypeID = $et->Add(array(
            "LID"         => SITE_ID,
            "EVENT_NAME"  => "ITSFERA_ORDER_COUPON",
            "NAME"        => "Купон за заказ",
            "DESCRIPTION" => "Персональный купон на следующую покупку",
        ));

        if ($eventTypeID) {
            COption::SetOptionString($this->MODULE_ID, "OrderCouponEventTypeID", $eventTypeID);

        }

    }

    public function uninstallEventType ()
    {

        if ($eventTypeID = COption::GetOptionString($this->MODULE_ID, "OrderCouponEventTypeID")) {
            $et = new CEventType;
            $et->Delete($eventTypeID);
        }
    }

}