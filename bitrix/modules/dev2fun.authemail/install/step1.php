<?php
/**
 * 
 * @author dev2fun (darkfriend)
 * @copyright (c) 2016, darkfriend
 * @version 1.0.0
 * 
 */
if(!check_bitrix_sessid()) return;
IncludeModuleLangFile(__FILE__);

CModule::IncludeModule("main");
CModule::AddAutoloadClasses(
	'',
	array(
		"dev2fun_authemail" => '/bitrix/modules/dev2fun.authemail/install/index.php',
	)
);

$dev2fun_model = new dev2fun_authemail();

RegisterModuleDependences("main", "OnPageStart", $dev2fun_model->MODULE_ID, "dev2funModelAuthEmailClass", "auth");

RegisterModule($dev2fun_model->MODULE_ID);

echo CAdminMessage::ShowNote(GetMessage("INSTALL_SUCCESS"));

echo BeginNote();
	echo GetMessage("INSTALL_LAST_MSG");
EndNote();