<?
/**
 * Company developer: REASPEKT
 * Developer: adel yusupov
 * Site: http://www.reaspekt.ru
 * E-mail: adel@reaspekt.ru
 * @copyright (c) 2016 REASPEKT
 */
 
use \Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid())
	return;

if ($ex = $APPLICATION->GetException()){
	echo CAdminMessage::ShowMessage(array(
		"TYPE" => "ERROR",
		"MESSAGE" => Loc::getMessage("MOD_INST_ERR"),
		"DETAILS" => $ex->GetString(),
		"HTML" => true,
	));
}

if (!\Bitrix\Main\Loader::includeModule("highloadblock")) {
	return;
}
?>

<form action="<?=$APPLICATION->GetCurPage()?>">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>"/>
	<input type="hidden" name="id" value="reaspekt.geobase"/>
	<input type="hidden" name="install" value="Y"/>
	<input type="hidden" name="step" value="2"/>
	<?if(CheckUrlAvaible('http://ipgeobase.ru/files/db/Main/geo_files.tar.gz')){?>
		<input type="checkbox" name="LOAD_DATA" id="LOAD_DATA" value="Y" checked/>
		<label for="LOAD_DATA"><?=Loc::getMessage('INSTALL_GEOBASE_LOAD_DATA')?></label>
	<?} else {?>
		<input type="checkbox" name="LOAD_DATA" id="LOAD_DATA" value="N" disabled="true"/>
		<label for="LOAD_DATA" disabled="true"><?=Loc::getMessage('INSTALL_GEOBASE_LOAD_DATA')?></label>
	<?}
	?>
	<br/><br/>
	
	<input type="submit" name="" value="<?= Loc::getMessage("MOD_INSTALL")?>"/>
</form>
<?

function CheckUrlAvaible($url){
	if(function_exists('curl_init'))
		return CheckDomainAvailible($url);
	else
		return CheckFileHeaders($url);
}

function CheckFileHeaders($strUrl)
{
	stream_context_set_default(
		array (
			'http' => array (
				'method' => 'HEAD',
				'timeout' => 6
			)
		)
	);

	$headers = @get_headers($strUrl);
	if (preg_match("/(200 OK)$/", $headers[0]))
		return true;
	return false;
}

function CheckDomainAvailible($domain)
{
	if (!filter_var($domain, FILTER_VALIDATE_URL))
		return false;

	$curlInit = curl_init($domain);
	curl_setopt($curlInit, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($curlInit, CURLOPT_HEADER, true);
	curl_setopt($curlInit, CURLOPT_NOBODY, true);
	curl_setopt($curlInit, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($curlInit);
	curl_close($curlInit);

	if($response) 
		return true;
	return false;
}
?>