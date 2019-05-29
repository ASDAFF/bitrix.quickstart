<?
include_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mcart.vacancy/prolog.php");
include_once ($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/mcart.vacancy/classes/mysql/export_vacancies.php"); 

	AddEventHandler( 'iblock', 'OnAfterIBlockElementUpdate', Array("mcart_vacancy_export", 'mcart_vacancy_OnAfterIBlockElementAddOrUpdateOrDelete' ));
	AddEventHandler( 'iblock', 'OnAfterIBlockElementAdd', Array("mcart_vacancy_export", 'mcart_vacancy_OnAfterIBlockElementAddOrUpdateOrDelete' ));
	AddEventHandler( 'iblock', 'OnAfterIBlockElementDelete', Array("mcart_vacancy_export", 'mcart_vacancy_OnAfterIBlockElementAddOrUpdateOrDelete' ));
	
	class mcart_vacancy_export{
		
		function mcart_vacancy_OnAfterIBlockElementAddOrUpdateOrDelete($aFields ){
			$is_need_export = COption::GetOptionString("mcart.vacancy", "AUTOEXPORT");
			if ($is_need_export=="checked")
				{
				if ($aFields["IBLOCK_ID"]==VACANCY_IBLOCK_ID)
						{
							CExportVacancies::$VACANCIES_IBLOCK_ID = VACANCY_IBLOCK_ID; 
							CExportVacancies::$phone = COption::GetOptionString("mcart.vacancy", "PHONE");
							CExportVacancies::$email = COption::GetOptionString("mcart.vacancy", "MAIL");
							$CExportVacancies=new CExportVacancies;
							$CExportVacancies->export_for_yandex();
						}
				}
			}	
	}
?>