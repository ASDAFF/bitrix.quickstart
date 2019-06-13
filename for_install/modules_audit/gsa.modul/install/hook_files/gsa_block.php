<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

if(CModule::IncludeModule("gsa.modul"))
{
	//cGsa::getHooks();exit;
	/*RegisterModuleDependences("sale", "OnOrderAdd", "gsa", "cGsa", "OrderCreateBitrix");
	exit;*/
	/*cGsa::HookDelete("238C686CF4B34005A955EA16AE994F67");
	cGsa::HookDelete("83A6DB249EBF4A4C8417A9BC9D78A577");
	cGsa::HookDelete("9E2F30FB95B444D9901E10F95B77C0BD");*/
	// cGsa::removeAllHooks();
	/*print_R(cGsa::getHooks());
	exit;*/
	/*cGsa::removeAllHooks();
	exit;*/

	//смотрим наличие ключа
	$gsa_key = COption::getOptionString("gsa.modul", "init_key");
	//COption::SetOptionString("gsa", "init_key", time());
	if(strlen($gsa_key)==0 || !isset($_GET['key'])) exit(json_encode(array("STATUS" => 1,"MESSAGE" => "WRONG GSA INIT KEY")));
	if(!file_exists($_SERVER['DOCUMENT_ROOT']."/gsa_key_".$gsa_key)) exit(json_encode(array("STATUS" => 1,"MESSAGE" => "GSA KEY FILE DOESNT EXIST")));



	$f = fopen('php://input', 'r');
	$data = stream_get_contents($f);
	$dec = json_decode($data);

	switch($_GET['action'])
	{
		case 'createOrder':
			$ans = cGsa::createOrder($dec);
			echo $ans;
			$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/gsa_orderCREATE.txt', 'a+');
			fwrite($fp, $data);
			fclose($fp);
			break;
		case 'updateOrder':
			$ans = cGsa::updateOrder($dec);
			echo $ans;
			$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/gsa_orderUPDATE.txt', 'a+');
			fwrite($fp, $data);
			fclose($fp);
			//print_r($data);
			break;
		case 'DeliveryCheck':
			$ans = cGsa::DeliveryCheck($dec);
			echo $ans;
			$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/gsa_deliveryCHECK.txt', 'a+');
			fwrite($fp, $data);
			fclose($fp);
			break;
		case 'updateOrder':			
			cGsa::UserRegister($dec);
			$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/gsa_user.txt', 'a+');
			fwrite($fp, $data);
			fclose($fp);
		break;
		case 'UserAuth':			
			$ans = cGsa::UserAuth($dec);
			echo $ans;
			$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/gsa_userAUTH.txt', 'a+');
			fwrite($fp, $data." ".$ans);
			fclose($fp);
		break;
		case 'UserUpdated':
			$ans = cGsa::UserUpdated($dec);
			echo $ans;
			$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/gsa_userUpdate.txt', 'a+');
			fwrite($fp, $data." ".$ans);
			fclose($fp);
		break;
		case 'UserRegister':
			$ans = cGsa::UserRegister($dec);
			echo $ans;
			$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/gsa_userREGISTER.txt', 'a+');
			fwrite($fp, $data." ".$ans);
			fclose($fp);
		break;
	}

	/*
	//обновление информации
	$handle = fopen($_SERVER['DOCUMENT_ROOT'].'/gsa_update.txt', "rb");
	$dec1 = stream_get_contents($handle);
	fclose($handle);
	$dec = json_decode($dec1);
	cGsa::updateOrder($dec);*/
}
?>