<?
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
set_time_limit(0);
define("SITE_ID", LANG);
ini_set('max_execution_time', 600);
ini_set('memory_limit', '512M');
ini_set('upload_max_filesize', '10M');
define("LANG", "ru");


if (isset ($_REQUEST['autorun']))
	{$_SERVER["DOCUMENT_ROOT"] = VACANCY_DOCUMENT_ROOT; 
	$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	}
else	
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); 

IncludeModuleLangFile( __FILE__);
$APPLICATION->SetTitle(GetMessage("MCART_VACANCY_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
//--------------------
include_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.vacancy/prolog.php");
//Подключение библиотеки  

require_once ($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/mcart.vacancy/classes/mysql/export_vacancies.php");  

if (($_REQUEST["export_go"])||($_REQUEST["autorun"]))
{

	CExportVacancies::$VACANCIES_IBLOCK_ID = VACANCY_IBLOCK_ID; 
	CExportVacancies::$phone = COption::GetOptionString("mcart.vacancy", "PHONE");
	CExportVacancies::$email = COption::GetOptionString("mcart.vacancy", "MAIL");

	$CExportVacancies=new CExportVacancies;
	$CExportVacancies->export_for_yandex();
	echo $CExportVacancies->getMessage();
}
else
{

?>
<form action="<?=$APPLICATION->GetCurPage()?>" method='post' name="form1" id="form1">
<input type = "hidden" name = "export_go" value = "Y">
<br>
<input type="submit" value="<?=GetMessage("VACANCY_START_EXPORT")?>" >
<?

}
//--------------------
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");

?>