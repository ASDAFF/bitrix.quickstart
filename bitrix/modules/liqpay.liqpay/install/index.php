<?php
/**
 * Liqpay Payment Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category        Liqpay
 * @package         liqpay.liqpay
 * @version         0.0.1
 * @author          Liqpay
 * @copyright       Copyright (c) 2014 Liqpay
 * @license         http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 * EXTENSION INFORMATION
 *
 * 1C-Bitrix        14.0
 * LIQPAY API       https://www.liqpay.com/ru/doc
 *
 */

IncludeModuleLangFile(__FILE__);

class liqpay_liqpay extends CModule
{

    const MODULE_ID = 'liqpay.liqpay';
    const PARTNER_NAME = 'pb.web.develop';
    const PARTNER_URI = 'http://www.liqpay.com';

    var $MODULE_ID = 'liqpay.liqpay';
    var $PARTNER_NAME = 'pb.web.develop';
    var $PARTNER_URI = 'http://www.liqpay.com';

    public $MODULE_GROUP_RIGHTS = 'N';

    public function __construct()
    {
        require(dirname(__FILE__).'/version.php');
        $this->MODULE_NAME = GetMessage('LP_MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('LP_MODULE_DESC');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->PARTNER_NAME = 'pb.web.develop';
        $this->PARTNER_URI = 'http://www.liqpay.com';
    }

    public function DoInstall()
    {
        if (IsModuleInstalled('sale')) {
            global $APPLICATION;
            $this->InstallFiles();
            RegisterModule($this->MODULE_ID);
            return true;
        }

        $MODULE_ID = $this->MODULE_ID;
        $TAG = 'VWS';
        $MESSAGE = GetMessage('LP_ERR_MODULE_NOT_FOUND', array('#MODULE#'=>'sale'));
        $intID = CAdminNotify::Add(compact('MODULE_ID', 'TAG', 'MESSAGE'));

        return false;
    }

    public function DoUninstall()
    {
        global $APPLICATION;
        COption::RemoveOption($this->MODULE_ID);
        UnRegisterModule($this->MODULE_ID);
        $this->UnInstallFiles();
    }

    public function InstallFiles()
    {
        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$this->MODULE_ID.'/install/sale_payment',
            $_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/include/sale_payment',
            true, true
        );
    }

    public function UnInstallFiles()
    {
        return DeleteDirFilesEx($_SERVER['DOCUMENT_ROOT'].'/bitrix/php_interface/include/sale_payment/liqpay_liqpay');
    }
}