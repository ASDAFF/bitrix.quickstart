<?
/**
 * Mail Attaching
 *
 * Copyright "Первый Интегратор", 2011-2015
 * http://www.1-integrator.com/
 * @author Sergey Leshchenko
 */

// !!! Внимание !!! Для сокращения инклудов все языковые файлы собраны в один
IncludeModuleLangFile(__FILE__);

// __autoloades
CModule::AddAutoloadClasses(
	'module.mailattaching',
	array(
		'CModuleMailAttaching' => 'classes/general/cmodulemailattaching.php',
		'CModuleMailAttachingAdmin' => 'classes/general/cmodulemailattachingadmin.php',
		'CModuleMailAttachingFieldsParser' => 'classes/general/cmodulemailattachingfieldsparser.php',
	)
);

//
// !!! custom_mail !!!
//
if(!defined('MODULE_MAILATTACHING_USE_CUSTOM_MAIL')) {
	if(function_exists('custom_mail')) {
		define('MODULE_MAILATTACHING_USE_CUSTOM_MAIL', false);

		// выведем уведомление
		if(class_exists('CAdminInformer')) {
			CAdminInformer::AddItem(
				array(
					'TITLE' => GetMessage('MODULE_MAILATTACHING_INFORMER_TITLE'),
					'ALERT' => true,
					'HTML' => GetMessage('MODULE_MAILATTACHING_INFORMER_HTML'),
					'COLOR' => 'red',
					//'FOOTER' => '',
					//'LINK' => ''
				)
			);
		}

	} else {
		define('MODULE_MAILATTACHING_USE_CUSTOM_MAIL', true);
	}
}

if(MODULE_MAILATTACHING_USE_CUSTOM_MAIL) {

	AddEventHandler('main', 'OnBeforeEventSend', array('CModuleMailAttaching', 'OnBeforeEventSendHandler'), 1);

	function custom_mail($sTo, $sSubject, $sMessage, $sAdditionalHeaders, $sAdditionalParameters) {
		return CModuleMailAttaching::ExecCustomMail($sTo, $sSubject, $sMessage, $sAdditionalHeaders, $sAdditionalParameters);
	}
}
