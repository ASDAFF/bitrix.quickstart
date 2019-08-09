<?php
include_once(dirname(__FILE__).'/install/demo.php');

if(!class_exists('CKDAExportExcelRunner'))
{
	class CKDAExportExcelRunner
	{
		protected static $moduleId = 'kda.exportexcel';
		
		static function GetModuleId()
		{
			return self::$moduleId;
		}
		
		static function DemoExpired()
		{
			$DemoMode = CModule::IncludeModuleEx(self::$moduleId);
			$cnstPrefix = str_replace('.', '_', self::$moduleId);
			if ($DemoMode==MODULE_DEMO) {
				$now=time();
				if (defined($cnstPrefix."_OLDSITEEXPIREDATE")) {
					if ($now>=constant($cnstPrefix.'_OLDSITEEXPIREDATE') || constant($cnstPrefix.'_OLDSITEEXPIREDATE')>$now+2000000 || $now - filectime(__FILE__)>2000000) {
						return true;
					}
				} else{ 
					return true;
				}
			} elseif ($DemoMode==MODULE_DEMO_EXPIRED) {
				return true;
			}
			return false;
		}
		
		static function ExportIblock($params=array(), $fparams=array(), $stepparams=false, $pid = false)
		{
			if(self::DemoExpired()) return array();
			$ee = new CKDAExportExcel($params, $fparams, $stepparams, $pid);
			return $ee->Export();
		}
		
		static function ExportHighloadblock($params=array(), $fparams=array(), $stepparams=false, $pid = false)
		{
			if(self::DemoExpired()) return array();
			$ee = new CKDAExportExcelHighload($params, $fparams, $stepparams, $pid);
			return $ee->Export();
		}
	}
}

$moduleId = CKDAExportExcelRunner::GetModuleId();
$moduleJsId = str_replace('.', '_', $moduleId);
$pathJS = '/bitrix/js/'.$moduleId;
$pathCSS = '/bitrix/panel/'.$moduleId;
$pathLang = BX_ROOT.'/modules/'.$moduleId.'/lang/'.LANGUAGE_ID;
CModule::AddAutoloadClasses(
	$moduleId,
	array(
		'CKDAEEFieldList' => 'classes/general/field_list.php',
		'CKDAExportProfile' => 'classes/general/profile.php',
		'CKDAExportProfileAll' => 'classes/general/profile.php',
		'CKDAExportProfileDB' => 'classes/general/profile_db.php',
		'CKDAExportProfileFS' => 'classes/general/profile_fs.php',
		'CKDAExportExcel' => 'classes/general/export.php',
		'CKDAExportExcelStatic' => 'classes/general/export.php',
		'CKDAExportExcelHighload' => 'classes/general/export_highload.php',
		'CKDAExportExcelWriterXlsx' => 'classes/general/export_writer_xlsx.php',
		'CKDAExportExcelWriterCsv' => 'classes/general/export_writer_csv.php',
		'CKDAExportExcelWriterDbf' => 'classes/general/export_writer_dbf.php',
		'CKDAExportExtraSettings' => 'classes/general/extrasettings.php',
		'CKDAExportUtils' => 'classes/general/utils.php',
		//'CKDAExportCondTree' => 'classes/general/cond_tree.php',
		'\Bitrix\KdaExportexcel\ProfileTable' => "lib/profile.php",
		'\Bitrix\KdaExportexcel\ProfileHlTable' => "lib/profile_hl.php"
	)
);

$initFile = $_SERVER["DOCUMENT_ROOT"].BX_ROOT.'/php_interface/include/'.$moduleId.'/init.php';
if(file_exists($initFile)) include_once($initFile);

$arJSKdaIBlockConfig = array(
	$moduleJsId => array(
		'js' => $pathJS.'/script.js',
		'css' => $pathCSS.'/styles.css',
		'rel' => array('jquery', $moduleJsId.'_chosen'/*, 'core_condtree'*/),
		'lang' => $pathLang.'/js_admin.php'
	),
	$moduleJsId.'_highload' => array(
		'js' => $pathJS.'/script_highload.js',
		'css' => $pathCSS.'/styles.css',
		'rel' => array('jquery', $moduleJsId.'_chosen'/*, 'core_condtree'*/),
		'lang' => $pathLang.'/js_admin_hlbl.php',
	),
	$moduleJsId.'_chosen' => array(
		'js' => $pathJS.'/chosen/chosen.jquery.min.js',
		'css' => $pathJS.'/chosen/chosen.min.css',
		'rel' => array('jquery')
	),
);

foreach ($arJSKdaIBlockConfig as $ext => $arExt) {
	CJSCore::RegisterExt($ext, $arExt);
}
?>