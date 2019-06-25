<?
	global $MESS;
	
	$sPathToModule = substr(str_replace('\\', '/', __FILE__), 0, strlen($sPathToLang)-strlen("/install/index.php"));
	include(GetLangFileName($sPathToModule."/lang/", "/install/index.php"));
	include($sPathToModule."/install/version.php");
	
	Class intec_startshop extends CModule
	{
		var $MODULE_ID = "intec.startshop";
        var $MODULE_VERSION;
        var $MODULE_VERSION_DATE;
        var $MODULE_NAME;
        var $MODULE_DESCRIPTION;
		var $PARTNER_NAME;
		var $PARTNER_URI;
		var $MODE = "SILENCE";
		
		function intec_startshop () 
		{
			include('version.php');
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
			$this->MODULE_NAME = GetMessage("module.info.name");
			$this->MODULE_DESCRIPTION = GetMessage("module.info.description");
			$this->PARTNER_NAME = GetMessage('module.info.partner.name');
			$this->PARTNER_URI = GetMessage("module.info.partner.url");
		}
		
		function InstallDB()
		{
			global $DB;
			
			/* Создание таблицы каталогов */
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_catalog` (
					`IBLOCK` bigint(20) NOT NULL,
				  	`USE_QUANTITY` tinyint(1) NOT NULL DEFAULT '0',
				  	`OFFERS_IBLOCK` bigint(20) NOT NULL,
					`OFFERS_LINK_PROPERTY` bigint(20) NOT NULL DEFAULT '0',
					PRIMARY KEY (`IBLOCK`),
					UNIQUE KEY `OFFERS` (`OFFERS_IBLOCK`,`IBLOCK`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);
			$DB->Query(
					"CREATE TABLE IF NOT EXISTS `startshop_catalog_properties` (
  					`CATALOG` bigint(20) NOT NULL,
  					`PROPERTY` bigint(20) NOT NULL,
  					PRIMARY KEY (`CATALOG`,`PROPERTY`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;"
			);

			/* Создание таблицы прав */
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_rights` (
  					`GROUP` bigint(20) NOT NULL,
  					`ACTION` varchar(80) NOT NULL,
  					`RIGHT` varchar(20) NOT NULL,
					PRIMARY KEY (`GROUP`,`ACTION`,`RIGHT`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);

			/* Создание таблицы валют */
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_currency` (
  					`ID` int(10) NOT NULL AUTO_INCREMENT,
  					`CODE` varchar(255) NOT NULL,
  					`ACTIVE` varchar(1) NOT NULL DEFAULT 'Y',
  					`BASE` varchar(1) NOT NULL DEFAULT 'N',
  					`RATE` float NOT NULL DEFAULT '1',
					`RATING` int(10) NOT NULL DEFAULT '1',
  					`SORT` int(10) NOT NULL DEFAULT '500',
  					PRIMARY KEY (`ID`),
  					UNIQUE KEY `UNIQUE` (`CODE`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_currency_format` (
  					`CURRENCY` int(10) NOT NULL,
  					`LID` varchar(2) NOT NULL,
  					`FORMAT` varchar(255) DEFAULT NULL,
  					`DELIMITER_DECIMAL` varchar(1) DEFAULT NULL,
  					`DELIMITER_THOUSANDS` varchar(1) DEFAULT NULL,
  					`DECIMALS_COUNT` int(10) DEFAULT NULL,
  					`DECIMALS_DISPLAY_ZERO` varchar(1) DEFAULT 'Y',
  					PRIMARY KEY (`CURRENCY`,`LID`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_currency_language` (
  					`CURRENCY` int(10) NOT NULL,
  					`LID` varchar(2) NOT NULL,
  					`NAME` varchar(255) NOT NULL,
  					PRIMARY KEY (`CURRENCY`,`LID`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);

            /* Создание таблицы доставок */
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_delivery` (
  					`ID` int(10) NOT NULL AUTO_INCREMENT,
  					`NAME` varchar(255) NOT NULL,
  					`CODE` varchar(255) NOT NULL,
  					`ACTIVE` varchar(1) NOT NULL DEFAULT 'Y',
  					`PRICE` float DEFAULT '0',
  					`SID` varchar(2) NOT NULL,
  					`SORT` int(10) NOT NULL DEFAULT '500',
  					PRIMARY KEY (`ID`),
  					UNIQUE KEY `UNIQUE` (`CODE`,`SID`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_delivery_language` (
  					`DELIVERY` int(10) NOT NULL,
  					`LID` varchar(2) NOT NULL,
  					`NAME` varchar(255) DEFAULT NULL
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);
            $DB->Query(
                "CREATE TABLE IF NOT EXISTS `startshop_delivery_properties` (
                    `DELIVERY` int(10) NOT NULL,
                    `PROPERTY` int(10) NOT NULL,
                    PRIMARY KEY (`DELIVERY`, `PROPERTY`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8"
            );

            /* Создание таблицы форм */
            $DB->Query(
                "CREATE TABLE IF NOT EXISTS `startshop_form` (
                    `ID` bigint(20) NOT NULL AUTO_INCREMENT,
                    `CODE` varchar(255) NOT NULL,
                    `SORT` int(11) NOT NULL DEFAULT '500',
                    `USE_POST` varchar(1) NOT NULL DEFAULT 'N',
                    `USE_CAPTCHA` varchar(1) DEFAULT 'N',
                    PRIMARY KEY (`ID`),
                    UNIQUE KEY `UNIQUE` (`CODE`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8"
            );
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_form_site` (
  					`FORM` bigint(20) NOT NULL,
  					`SID` varchar(2) NOT NULL,
  					PRIMARY KEY (`FORM`,`SID`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8"
			);
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_form_language` (
  					`FORM` bigint(20) NOT NULL,
  					`LID` varchar(2) NOT NULL,
  					`NAME` varchar(255) NOT NULL,
  					`BUTTON` varchar(255) NOT NULL,
  					`SENT` text NOT NULL,
  					PRIMARY KEY (`FORM`,`LID`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_form_properties` (
  					`ID` bigint(20) NOT NULL AUTO_INCREMENT,
  					`CODE` varchar(255) NOT NULL,
  					`FORM` bigint(20) NOT NULL,
  					`SORT` int(11) NOT NULL DEFAULT '500',
  					`ACTIVE` varchar(1) NOT NULL DEFAULT 'Y',
  					`TYPE` tinyint(3) unsigned NOT NULL DEFAULT '0',
  					`REQUIRED` varchar(1) NOT NULL DEFAULT 'N',
  					`READONLY` varchar(1) NOT NULL DEFAULT 'N',
  					`DATA` text,
  					PRIMARY KEY (`ID`),
  					UNIQUE KEY `UNIQUE` (`CODE`,`FORM`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_form_properties_language` (
  					`PROPERTY` bigint(20) NOT NULL,
  					`LID` varchar(2) NOT NULL,
  					`NAME` varchar(255) NOT NULL,
  					PRIMARY KEY (`PROPERTY`,`LID`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_form_results` (
  					`ID` bigint(20) NOT NULL AUTO_INCREMENT,
  					`FORM` bigint(20) NOT NULL,
  					`DATE_CREATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  					`DATE_MODIFY` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  					PRIMARY KEY (`ID`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_form_results_values` (
  					`RESULT` bigint(20) NOT NULL,
  					`PROPERTY` bigint(20) NOT NULL,
  					`VALUE` text,
  					PRIMARY KEY (`RESULT`,`PROPERTY`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);

			// Создание таблицы заказов
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_order` (
  					`ID` bigint(20) NOT NULL AUTO_INCREMENT,
  					`USER` bigint(20) DEFAULT NULL,
  					`SID` varchar(2) NOT NULL,
  					`DELIVERY` bigint(20) NOT NULL,
  					`PAYMENT` bigint(20) NOT NULL,
  					`STATUS` bigint(20) NOT NULL,
  					`CURRENCY` varchar(255) NOT NULL,
  					`PAYED` varchar(255) NOT NULL DEFAULT 'N',
  					`DATE_CREATE` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  					PRIMARY KEY (`ID`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_order_items` (
  					`ORDER` bigint(20) NOT NULL,
  					`ITEM` bigint(20) NOT NULL,
  					`NAME` varchar(255) NOT NULL,
  					`QUANTITY` float NOT NULL,
  					`PRICE` float NOT NULL,
  					PRIMARY KEY (`ORDER`,`ITEM`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_order_properties` (
  					`ID` bigint(20) NOT NULL AUTO_INCREMENT,
  					`CODE` varchar(255) NOT NULL,
  					`SID` varchar(2) NOT NULL,
  					`SORT` int(11) NOT NULL,
  					`ACTIVE` varchar(1) DEFAULT 'Y',
  					`REQUIRED` varchar(1) NOT NULL DEFAULT 'N',
  					`TYPE` varchar(255) NOT NULL DEFAULT 'S',
  					`SUBTYPE` varchar(255) NOT NULL,
  					`DATA` text,
  					`USER_FIELD` varchar(255) NOT NULL,
  					PRIMARY KEY (`ID`),
  					UNIQUE KEY `UNIQUE` (`CODE`,`SID`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_order_properties_language` (
  					`PROPERTY` bigint(20) NOT NULL,
  					`LID` varchar(2) NOT NULL,
  					`NAME` varchar(255) NOT NULL,
  					`DESCRIPTION` text NOT NULL,
  					PRIMARY KEY (`PROPERTY`,`LID`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_order_properties_values` (
  					`ORDER` bigint(20) NOT NULL,
  					`PROPERTY` bigint(20) NOT NULL,
  					`VALUE` text,
  					PRIMARY KEY (`ORDER`,`PROPERTY`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_order_statuses` (
  					`ID` bigint(20) NOT NULL AUTO_INCREMENT,
  					`SID` varchar(2) NOT NULL,
  					`CODE` varchar(255) NOT NULL,
  					`SORT` int(11) NOT NULL DEFAULT '500',
  					`CAN_PAY` varchar(1) NOT NULL DEFAULT 'N',
  					`DEFAULT` varchar(1) NOT NULL DEFAULT 'N',
  					PRIMARY KEY (`ID`),
  					UNIQUE KEY `UNIQUE` (`SID`,`CODE`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_order_statuses_language` (
  					`STATUS` bigint(20) NOT NULL,
  					`LID` varchar(2) NOT NULL,
  					`NAME` varchar(255) NOT NULL,
  					PRIMARY KEY (`STATUS`,`LID`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);

			// Добавление таблицы способов оплаты
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_payment` (
  					`ID` bigint(20) NOT NULL AUTO_INCREMENT,
  					`CODE` varchar(255) NOT NULL,
  					`ACTIVE` varchar(1) NOT NULL DEFAULT 'Y',
  					`SORT` int(11) NOT NULL DEFAULT '500',
  					`HANDLER` varchar(255) NOT NULL,
  					`CURRENCY` varchar(255) NOT NULL,
  					PRIMARY KEY (`ID`),
  					UNIQUE KEY `UNIQUE` (`CODE`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_payment_language` (
  					`PAYMENT` int(10) NOT NULL,
  					`LID` varchar(2) NOT NULL,
  					`NAME` varchar(255) NOT NULL,
  					PRIMARY KEY (`PAYMENT`,`LID`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_payment_properties` (
  					`PAYMENT` int(10) NOT NULL,
  					`PROPERTY` varchar(255) NOT NULL,
  					`VALUE` text,
  					PRIMARY KEY (`PAYMENT`,`PROPERTY`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);

			// Добавление таблицы типов цен
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_price` (
  					`ID` int(10) NOT NULL AUTO_INCREMENT,
  					`CODE` varchar(255) NOT NULL,
  					`ACTIVE` varchar(1) NOT NULL DEFAULT 'Y',
  					`BASE` varchar(1) NOT NULL DEFAULT 'N',
  					`SORT` int(10) DEFAULT '500',
  					PRIMARY KEY (`ID`),
  					UNIQUE KEY `UNIQUE` (`CODE`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_price_language` (
  					`PRICE` int(10) NOT NULL,
  					`LID` varchar(2) NOT NULL,
  					`NAME` varchar(255) NOT NULL,
  					PRIMARY KEY (`PRICE`,`LID`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);
			$DB->Query(
				"CREATE TABLE IF NOT EXISTS `startshop_price_groups` (
  					`PRICE` int(10) NOT NULL,
  					`GROUP` int(10) NOT NULL,
  					PRIMARY KEY (`PRICE`,`GROUP`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8"
			);
		}
		
		function UnInstallDB()
		{
			global $DB;

			$DB->Query("DROP TABLE IF EXISTS `startshop_catalog`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_catalog_properties`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_rights`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_currency`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_currency_format`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_currency_language`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_delivery`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_delivery_language`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_delivery_properties`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_form`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_form_site`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_form_language`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_form_properties`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_form_properties_language`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_form_results`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_form_results_values`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_order`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_order_items`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_order_properties`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_order_properties_language`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_order_properties_values`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_order_statuses`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_order_statuses_language`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_payment`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_payment_language`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_payment_properties`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_price`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_price_language`");
			$DB->Query("DROP TABLE IF EXISTS `startshop_price_groups`");
		}
		
		function DoInstall() 
		{
			global $APPLICATION;
			require_once(dirname(__FILE__).'/../classes/CStartShopInstall.php');
			require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");

			$arVariables = array(
				'step' => intval($_REQUEST['step']) > 0 ? intval($_REQUEST['step']) : 0,
				'install' => $_REQUEST['install'] == 'Y' ? true : false,
			);

			if ($arVariables['install'] && $arVariables['step'] != 1 && $this->MODE == "NORMAL")
				$APPLICATION->IncludeAdminFile(
					GetMessage("module.install.form.caption"),
					$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intec.startshop/install/install.php"
				);

			if (($arVariables['install'] && $arVariables['step'] = 1) || $this->MODE == "SILENT") {
				$this->InstallDB();
				CopyDirFiles(
					$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intec.startshop/admin/include",
					$_SERVER["DOCUMENT_ROOT"]."/bitrix/admin",
					true,
					true
				);
				CopyDirFiles(
					$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intec.startshop/install/components",
					$_SERVER["DOCUMENT_ROOT"]."/bitrix/components",
					true,
					true
				);
				CopyDirFiles(
					$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intec.startshop/install/themes",
					$_SERVER["DOCUMENT_ROOT"]."/bitrix/themes",
					true,
					true
				);
				file_put_contents($_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/modules/".$this->MODULE_ID."/admin/startshop/wizard.php", 0);
				RegisterModule($this->MODULE_ID);

				include_once('install.manager.php');
				if ( $this->MODE !== "SILENT" ) {
					$APPLICATION->IncludeAdminFile(
						GetMessage("module.install.finish.form.caption"),
						$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intec.startshop/install/install.finish.php"
					);
				}
				return true;
			}
			
			return false;
		}
		
		function DoUninstall() 
		{
			global $APPLICATION;

			$arVariables = array(
				'step' => intval($_REQUEST['step']) > 0 ? intval($_REQUEST['step']) : 0,
				'install' => $_REQUEST['install'] == 'Y' ? true : false,
				'uninstall' => is_array($_REQUEST['startshopUninstall']) ? $_REQUEST['startshopUninstall'] : array()
			);

			if ($arVariables['step'] == 0 && $this->MODE == "NORMAL")
				$APPLICATION->IncludeAdminFile(
					GetMessage("module.uninstall.form.caption"),
					$_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/intec.startshop/install/uninstall.php"
				);

			if ($arVariables['step'] > 0 || $this->MODE == "SILENCE") {
				if ($arVariables['uninstall']['TABLES'] == 'Y') {
					$this->UnInstallDB();
				}

				if ($arVariables['uninstall']['SETTINGS'] == 'Y') {
					COption::RemoveOption('intec.startshop');
				}

				DeleteDirFiles(
					$_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intec.startshop/admin/include',
					$_SERVER['DOCUMENT_ROOT'].'/bitrix/admin'
				);

				DeleteDirFilesEx("/bitrix/themes/.default/intec.startshop.css");
				DeleteDirFilesEx("/bitrix/themes/.default/icons/intec.startshop");

				DeleteDirFilesEx("/bitrix/components/intec/startshop.basket"); // Удаление корзины
				DeleteDirFilesEx("/bitrix/components/intec/startshop.basket.basket"); // Удаление виджетов корзины
				DeleteDirFilesEx("/bitrix/components/intec/startshop.basket.basket.small"); // Удаление заказа
				DeleteDirFilesEx("/bitrix/components/intec/startshop.forms.result.new"); // Удаление списка заказов
				DeleteDirFilesEx("/bitrix/components/intec/startshop.order"); // Удаление оформления заказа
				DeleteDirFilesEx("/bitrix/components/intec/startshop.orders"); // Удаление заказов
				DeleteDirFilesEx("/bitrix/components/intec/startshop.orders.detail"); // Удаление детального заказа
				DeleteDirFilesEx("/bitrix/components/intec/startshop.orders.list"); // Удаление списка заказов
				DeleteDirFilesEx("/bitrix/components/intec/startshop.payment"); // Удаление оплаты
				DeleteDirFilesEx("/bitrix/components/intec/startshop.profile"); // Удаление личного кабинета

				DeleteDirFilesEx("/bitrix/components/bitrix/catalog/templates/startshop"); // Удаление шаблона каталога
				DeleteDirFilesEx("/bitrix/components/bitrix/main.profile/templates/startshop.personal"); // Удаление шаблона профиля
				DeleteDirFilesEx("/bitrix/components/bitrix/system.auth.authorize/templates/startshop"); // Удаление шаблона авторизации
				DeleteDirFilesEx("/bitrix/components/bitrix/system.auth.changepasswd/templates/startshop"); // Удаление шаблона смены пароля
				DeleteDirFilesEx("/bitrix/components/bitrix/system.auth.confirmation/templates/startshop"); // Удаление шаблона подтверждения регистрации
				DeleteDirFilesEx("/bitrix/components/bitrix/system.auth.forgotpasswd/templates/startshop"); // Удаление шаблона восстановления пароля
				DeleteDirFilesEx("/bitrix/components/bitrix/system.auth.form/templates/startshop"); // Удаление шаблона виджета формы
				DeleteDirFilesEx("/bitrix/components/bitrix/system.auth.registration/templates/startshop"); // Удаление шаблона регистрации
				DeleteDirFilesEx("/bitrix/components/bitrix/menu/templates/startshop.top.1"); // Удаляем меню

				$obEventType = new CEventType();
				$obEventType->Delete('STARTSHOP_NEW_ORDER_ADMIN');
				$obEventType->Delete('STARTSHOP_PAY_ORDER_ADMIN');
				$obEventType->Delete('STARTSHOP_NEW_ORDER');

				UnRegisterModule($this->MODULE_ID);
				return true;
			}

			return false;
		}
		
		function DELETE() 
		{
			global $APPLICATION;

			$arVariables = array(
				'step' => intval($_REQUEST['step']) > 0 ? intval($_REQUEST['step']) : 0,
				'install' => $_REQUEST['install'] == 'Y' ? true : false,
				'uninstall' => is_array($_REQUEST['startshopUninstall']) ? $_REQUEST['startshopUninstall'] : array()
			);

			if ($arVariables['step'] == 0 && $this->MODE == "NORMAL")
				

			if ($arVariables['step'] > 0 || $this->MODE == "SILENCE") {
				if ($arVariables['uninstall']['TABLES'] == 'Y') {
					$this->UnInstallDB();
				}

				if ($arVariables['uninstall']['SETTINGS'] == 'Y') {
					COption::RemoveOption('intec.startshop');
				}

				DeleteDirFiles(
					$_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/intec.startshop/admin/include',
					$_SERVER['DOCUMENT_ROOT'].'/bitrix/admin'
				);

				DeleteDirFilesEx("/bitrix/themes/.default/intec.startshop.css");
				DeleteDirFilesEx("/bitrix/themes/.default/icons/intec.startshop");

				DeleteDirFilesEx("/bitrix/components/intec/startshop.basket"); // Удаление корзины
				DeleteDirFilesEx("/bitrix/components/intec/startshop.basket.basket"); // Удаление виджетов корзины
				DeleteDirFilesEx("/bitrix/components/intec/startshop.basket.basket.small"); // Удаление заказа
				DeleteDirFilesEx("/bitrix/components/intec/startshop.forms.result.new"); // Удаление списка заказов
				DeleteDirFilesEx("/bitrix/components/intec/startshop.order"); // Удаление оформления заказа
				DeleteDirFilesEx("/bitrix/components/intec/startshop.orders"); // Удаление заказов
				DeleteDirFilesEx("/bitrix/components/intec/startshop.orders.detail"); // Удаление детального заказа
				DeleteDirFilesEx("/bitrix/components/intec/startshop.orders.list"); // Удаление списка заказов
				DeleteDirFilesEx("/bitrix/components/intec/startshop.payment"); // Удаление оплаты
				DeleteDirFilesEx("/bitrix/components/intec/startshop.profile"); // Удаление личного кабинета

				DeleteDirFilesEx("/bitrix/components/bitrix/catalog/templates/startshop"); // Удаление шаблона каталога
				DeleteDirFilesEx("/bitrix/components/bitrix/main.profile/templates/startshop.personal"); // Удаление шаблона профиля
				DeleteDirFilesEx("/bitrix/components/bitrix/system.auth.authorize/templates/startshop"); // Удаление шаблона авторизации
				DeleteDirFilesEx("/bitrix/components/bitrix/system.auth.changepasswd/templates/startshop"); // Удаление шаблона смены пароля
				DeleteDirFilesEx("/bitrix/components/bitrix/system.auth.confirmation/templates/startshop"); // Удаление шаблона подтверждения регистрации
				DeleteDirFilesEx("/bitrix/components/bitrix/system.auth.forgotpasswd/templates/startshop"); // Удаление шаблона восстановления пароля
				DeleteDirFilesEx("/bitrix/components/bitrix/system.auth.form/templates/startshop"); // Удаление шаблона виджета формы
				DeleteDirFilesEx("/bitrix/components/bitrix/system.auth.registration/templates/startshop"); // Удаление шаблона регистрации
				DeleteDirFilesEx("/bitrix/components/bitrix/menu/templates/startshop.top.1"); // Удаляем меню

				$obEventType = new CEventType();
				$obEventType->Delete('STARTSHOP_NEW_ORDER_ADMIN');
				$obEventType->Delete('STARTSHOP_PAY_ORDER_ADMIN');
				$obEventType->Delete('STARTSHOP_NEW_ORDER');

				UnRegisterModule($this->MODULE_ID);
				return true;
			}

			return false;
		}
	}
?>