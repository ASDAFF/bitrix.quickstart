<?
IncludeModuleLangFile(__FILE__);

global $MESS, $DOCUMENT_ROOT;

/** @noinspection PhpDynamicAsStaticMethodCallInspection */
CModule::AddAutoloadClasses(
	'defa.tools',
	array(
		'DefaTools' => 'classes/general/defa_tools.php',
		'DefaToolsException' => 'classes/general/defa_tools_exception.php',
		'DefaToolsController' => 'controller.php',
		'DefaToolsGetMenu' => 'interface/get_menu.php',

		'DefaToolsDemo' => 'classes/general/ib_demo.php',
		'DefaToolsCopy' => 'classes/general/ib_copy.php',
		'DefaTools_IBProp_MultipleFiles' => 'classes/general/ibprop_multiplefiles.php',
		'DefaTools_IBProp_FileManEx' => 'classes/general/ibprop_filemanex.php',
		'DefaTools_UserType_Auth' => 'classes/general/usertype_auth.php',
		'DefaTools_IBProp_ElemListDescr' => 'classes/general/ibprop_elemlistdescr.php',
		'DefaTools_IBProp_OptionsGrid' => 'classes/general/ibprop_optionsgrid.php',
		'DefaTools_IBProp_ElemCompleter' => 'classes/general/ibprop_elemcompleter.php',
		'DefaTools_Typograf' => 'classes/general/typograf.php',

	)
);

CJSCore::RegisterExt('defa_tools_autocomplete', array(
	'js' => '/bitrix/js/defa.tools/autocomplete.js',
	'css' => '/bitrix/js/defa.tools/css/autocomplete.css',
	'rel' => array("ajax")
));
