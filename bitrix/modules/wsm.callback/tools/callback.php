<?
define("STOP_STATISTICS", true);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

$MODULE_ID = 'wsm.callback';

$output = array(
	'ID' => 0,
	'ERROR' => false,
	'MESSAGE' => '',
	'CAPTCHA_RELOAD' => false,
	);

function retJSON($out, $message, $check_error = false)
{
	if(($out['ERROR'] && $check_error) || !$check_error)
	{
		if($out['ERROR'])
		{ 
			$message = $message == '' ? '' : '<b>'.$message.'</b>:<br/>' ;
			$out["MESSAGE"] = $message . $out["MESSAGE"];
		}
		//echo json_encode($out);
		echo CUtil::PhpToJSObject($out);
	}
}

CUtil::JSPostUnescape();

if(CModule::IncludeModule($MODULE_ID))
{
	$CALLBACK = $_REQUEST['CALLBACK'];
	
	$CAPTCHA = COption::GetOptionString($MODULE_ID, 'form_captcha', 'N');
	$output['CAPTCHA_RELOAD'] = $CAPTCHA == 'Y' ? true : false ;
		
	$mWSMCallback = new WSMCallback();

	if(WSMCallbackAdd($CALLBACK))
	{
		$ret = $mWSMCallback->Add($CALLBACK, SITE_ID);

		if($ret === false)
		{
			$output['ERROR'] = true;
			$output['MESSAGE'] = $mWSMCallback->last_error;
			$message = $mWSMCallback->message;
		}
		else
		{
			$output['MESSAGE'] = $mWSMCallback->message;
			$output['ID'] = $ret;
			$message = $mWSMCallback->message;
		}
	}
	else
	{
		$output['ERROR'] = true;
		$output['MESSAGE'] = 'Failed to access the module';
	}
}

retJSON($output, $message);
?>