<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

//переместить в bitrix/tools/ajax_gsa.php
if(CModule::IncludeModule("gsa.modul"))
{	
	switch($_GET['action'])
	{
		case 'WriteLog':
			COption::setOptionString("gsa.modul", "write_log",$_POST['is_log']);	
			$key = COption::getOptionString("gsa.modul", "write_log");	
			echo $key;
			///обнуляем файл логирования
			$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/gsa_main_log.txt', 'w');
			fclose($fp);
		break;
		case 'enterAuth':
			$user_name = $_POST['user_name'];
			$user_pass = $_POST['user_pass'];
			COption::SetOptionString("gsa.modul", "user_name",$user_name);
			COption::SetOptionString("gsa.modul", "user_pass",$user_pass);


			ob_start();$cookie = cGsa::getCookieString();ob_end_clean();
			echo $cookie;
	        if (!$cookie) {
	            ob_start();$cookie = cGsa::getNewCookieString();ob_end_clean();
	            if (!$cookie) {                
	            	COption::RemoveOption("gsa.modul", "user_name");
					COption::RemoveOption("gsa.modul", "user_pass");
	                exit("0");
	            }
	        }
	        //exit("1");
		break;
		case 'exitAuth':
			COption::RemoveOption("gsa.modul", "user_name");
			COption::RemoveOption("gsa.modul", "user_pass");
			COption::RemoveOption("gsa.modul", "cookie");			
		break;
	}
}



?>