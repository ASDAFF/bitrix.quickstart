<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
$moduleId = 'esol.importxml';
CModule::IncludeModule('iblock');
CModule::IncludeModule($moduleId);
IncludeModuleLangFile(__FILE__);

$MODULE_RIGHT = $APPLICATION->GetGroupRight($moduleId);
if($MODULE_RIGHT < "W") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if(is_array($_POST['vars']) && is_array($_POST['values']))
{
	$arVars = array();
	foreach($_POST['vars'] as $k=>$v)
	{
		if(strlen($v) > 0 /*&& strlen($_POST['values'][$k]) > 0*/)
		{
			$arVars[$v] = $_POST['values'][$k];
		}
	}
	$_POST['AUTH_SETTINGS']['VARS'] = $AUTH_SETTINGS['VARS'] = $arVars;
}

if(is_array($_POST['headers']) && is_array($_POST['hvalues']))
{
	$arHeaders = array();
	foreach($_POST['headers'] as $k=>$v)
	{
		if(strlen($v) > 0 /*&& strlen($_POST['hvalues'][$k]) > 0*/)
		{
			$arHeaders[$v] = $_POST['hvalues'][$k];
		}
	}
	$_POST['AUTH_SETTINGS']['HEADERS'] = $AUTH_SETTINGS['HEADERS'] = $arHeaders;
}

if(strlen($_POST['AUTH_SETTINGS']['HANDLER_FOR_LINK']) > 0)
{
	$_POST['AUTH_SETTINGS']['HANDLER_FOR_LINK_BASE64'] = base64_encode($_POST['AUTH_SETTINGS']['HANDLER_FOR_LINK']);
}

if(is_array($_POST['AUTH_SETTINGS']) && (!defined('BX_UTF') || !BX_UTF)) 
{
	$_POST['AUTH_SETTINGS'] = $AUTH_SETTINGS = $APPLICATION->ConvertCharsetArray($_POST['AUTH_SETTINGS'], 'UTF-8', 'CP1251');
}

if($_POST['action']=='checkconnect')
{
	$sess = $_SESSION;
	session_write_close();
	$_SESSION = $sess;
	$APPLICATION->RestartBuffer();
	if(ob_get_contents()) ob_end_clean();
		
	$arFile = \Bitrix\EsolImportxml\Utils::MakeFileArray(CUtil::PhpToJSObject($_POST['AUTH_SETTINGS']));
	$res = ($arFile['size'] > 0 && $arFile['type']!='text/html');
	$arResult = array('result'=>($res ? 'success' : 'fail'), 'file'=>$arFile);
	echo \CUtil::PhpToJSObject($arResult);
	die();
}elseif($_POST['action']=='loadparams')
{
	$sess = $_SESSION;
	session_write_close();
	$_SESSION = $sess;
	$APPLICATION->RestartBuffer();
	ob_end_clean();
	
	$arVars = array();
	$formAction = '';
	$authLink = trim($_POST['AUTH_SETTINGS']['PAGEAUTH']);
	if(strlen($authLink) > 0)
	{
		$arCookies = array();
		$arHeaders = array('User-Agent' => 'BitrixSM HttpClient class');
		$redirectCount = 0;
		$location = $authLink;
		while(strlen($location)>0 && $redirectCount<=5)
		{
			$client = new \Bitrix\Main\Web\HttpClient(array('disableSslVerification'=>true, 'redirect'=>false));
			$client->setCookies($arCookies);
			foreach($arHeaders as $hk=>$hv) $client->setHeader($hk, $hv);
			$htmlPage = $client->get($location);
			$arHeaders['Referer'] = $location;
			\Bitrix\EsolImportxml\Utils::MergeCookie($arCookies, $client->getCookies()->toArray());
			\Bitrix\EsolImportxml\Utils::GetNewLocation($location, $client->getHeaders()->get("Location"));
			$status = $client->getStatus();
			if($status != 302 && $status != 303) $location = '';
			$redirectCount++;
		}

		if(strlen($htmlPage) > 0)
		{
			$findPass = false;
			while(!$findPass && ($htmlPageLower = ToLower($htmlPage)) && ($formPos = strpos($htmlPageLower, '<form '))!==false && ($formPosEnd = strpos($htmlPageLower, '</form>'))!==false)
			{
				$arVars = array();
				$htmlForm = substr($htmlPage, $formPos, $formPosEnd - $formPos + 7);
				$htmlPage = substr($htmlPage, $formPosEnd + 7);
				if(preg_match_all('/<input[^>]*>/Uis', $htmlForm, $m))
				{
					foreach($m[0] as $input)
					{
						$type = 'text';
						if(preg_match('/\stype\s*=\s*[\'"]?([^\'"]*)[\'"]?(\s|>)/Uis', $input, $m2)) $type = ToLower($m2[1]);
						if(in_array($type, array('hidden', 'text', 'password', 'submit')))
						{
							if(preg_match('/\sname\s*=\s*[\'"]?([^\'"]*)[\'"]?(\s|>)/Uis', $input, $m2))
							{
								$arVars[] = $m2[1];
							}
						}
						if($type=='password')
						{
							$findPass = true;
							if(preg_match('/<form[^>]*action\s*=\s*[\'"]?([^\'"]+)[\'"]?(\s|>)/Uis', $htmlForm, $m2))
							{
								$formAction = $authLink;
								\Bitrix\EsolImportxml\Utils::GetNewLocation($formAction, $m2[1]);
							}
						}
					}
				}
			}
		}
	}
	
	echo \CUtil::PhpToJSObject(array('VARS'=>$arVars, 'LOC'=>$formAction));
	die();
}

if($_POST['action']=='save' && $_POST['AUTH_SETTINGS'])
{
	$APPLICATION->RestartBuffer();
	if(ob_get_contents()) ob_end_clean();
	
	echo '<script>';
	echo 'EProfile.SetLinkAuthParams('.CUtil::PhpToJSObject($_POST['AUTH_SETTINGS']).');';
	echo '</script>';
	die();
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");
?>
<form action="<?echo $APPLICATION->GetCurUri();?>" method="post" enctype="multipart/form-data" name="field_settings">
	<input type="hidden" name="action" value="save">
	<?//ShowPostData($_POST);?>
	<table width="100%" class="esol-ix-list-settings">
		<col width="50%">
		<col width="50%">
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("ESOL_IX_LAUTH_FILELINK");?>:</td>
			<td class="adm-detail-content-cell-r">
				<input type="text" size="50" name="AUTH_SETTINGS[FILELINK]" value="<?echo htmlspecialcharsbx($AUTH_SETTINGS['FILELINK'])?>">
			</td>
		</tr>
		<tr>
			<td class="esol-ix-email-checkparams" colspan="2">
				<a href="javascript:void(0)" onclick="EProfile.CheckLauthConnectData(this)"><?echo GetMessage("ESOL_IX_LAUTH_CHECK_SETTINGS");?></a> <div id="connect_result"></div>
				<div>&nbsp;</div>
			</td>
		</tr>
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("ESOL_IX_LAUTH_PAGEAUTH");?>:</td>
			<td class="adm-detail-content-cell-r">
				<input type="text" size="50" name="AUTH_SETTINGS[PAGEAUTH]" value="<?echo htmlspecialcharsbx($AUTH_SETTINGS['PAGEAUTH'])?>" onchange="EProfile.LauthLoadParams(this)">
			</td>
		</tr>
		<tr>
			<td class="adm-detail-content-cell-l"><?echo GetMessage("ESOL_IX_LAUTH_POSTPAGEAUTH");?>:</td>
			<td class="adm-detail-content-cell-r">
				<input type="text" size="50" name="AUTH_SETTINGS[POSTPAGEAUTH]" value="<?echo htmlspecialcharsbx($AUTH_SETTINGS['POSTPAGEAUTH'])?>">
			</td>
		</tr>
		
		
		<tr class="heading">
			<td colspan="2">
				<?echo GetMessage("ESOL_IX_LAUTH_VARS"); ?>
			</td>
		</tr>
		<?
		if(!is_array($AUTH_SETTINGS['VARS'])) $AUTH_SETTINGS['VARS'] = array();
		if(count($AUTH_SETTINGS['VARS']) < 1) $AUTH_SETTINGS['VARS'][''] = '';
		foreach($AUTH_SETTINGS['VARS'] as $var=>$value)
		{
			?>
			<tr class="esol-ix-lauth-var">
				<td class="adm-detail-content-cell-l">
					<?echo GetMessage("ESOL_IX_LAUTH_VAR");?>:
					<input type="text" name="vars[]" value="<?echo htmlspecialcharsbx($var)?>">
				</td>
				<td class="adm-detail-content-cell-r">
					<?echo GetMessage("ESOL_IX_LAUTH_VALUE");?>:
					<input type="text" name="values[]" value="<?echo htmlspecialcharsbx($value)?>">
				</td>
			</tr>
			<?
		}
		?>
		<tr>
			<td colspan="2" class="esol-ix-email-checkparams esol-ix-lauth-addvar">
				<a href="javascript:void(0)" onclick="EProfile.LauthAddVar(this)"><?echo GetMessage("ESOL_IX_LAUTH_ADD_VAR");?></a>
			</td>
		</tr>
		
		<tr class="heading">
			<td colspan="2">
				<?echo GetMessage("ESOL_IX_LAUTH_HEADERS"); ?>
			</td>
		</tr>
		<?
		if(!is_array($AUTH_SETTINGS['HEADERS'])) $AUTH_SETTINGS['HEADERS'] = array();
		if(count($AUTH_SETTINGS['HEADERS']) < 1) $AUTH_SETTINGS['HEADERS'][''] = '';
		foreach($AUTH_SETTINGS['HEADERS'] as $var=>$value)
		{
			?>
			<tr class="esol-ix-lauth-var">
				<td class="adm-detail-content-cell-l">
					<?echo GetMessage("ESOL_IX_LAUTH_HEADER");?>:
					<input type="text" name="headers[]" value="<?echo htmlspecialcharsbx($var)?>">
				</td>
				<td class="adm-detail-content-cell-r">
					<?echo GetMessage("ESOL_IX_LAUTH_VALUE");?>:
					<input type="text" name="hvalues[]" value="<?echo htmlspecialcharsbx($value)?>">
				</td>
			</tr>
			<?
		}
		?>
		<tr>
			<td colspan="2" class="esol-ix-email-checkparams">
				<a href="javascript:void(0)" onclick="EProfile.LauthAddVar(this)"><?echo GetMessage("ESOL_IX_LAUTH_ADD_HEADER");?></a>
			</td>
		</tr>
		
		<tr class="heading">
			<td colspan="2">
				<?echo GetMessage("ESOL_IX_LAUTH_OTHER_PARAMS"); ?>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="esol-ix-linkauth-handler">
				<p><?echo GetMessage("ESOL_IX_LAUTH_HANDLER_FOR_LINK"); ?>:</p>
				<textarea name="AUTH_SETTINGS[HANDLER_FOR_LINK]"><?echo $AUTH_SETTINGS['HANDLER_FOR_LINK']?></textarea>
			</td>
		</tr>
	</table>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");?>