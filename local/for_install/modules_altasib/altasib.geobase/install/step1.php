<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: adumnov                          #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2014 ALTASIB             #
#################################################
?>

<form action="<?=$APPLICATION->GetCurPage()?>">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>"/>
	<input type="hidden" name="id" value="altasib.geobase"/>
	<input type="hidden" name="install" value="Y"/>
	<input type="hidden" name="step" value="2"/>
	<?if(CheckFileHeaders('http://ipgeobase.ru/files/db/Main/geo_files.tar.gz')){?>
		<input type="checkbox" name="LOAD_DATA" id="LOAD_DATA" value="Y" checked/>
		<label for="LOAD_DATA"><?=GetMessage('INSTALL_GEOBASE_LOAD_DATA')?></label>
		<br/>
		<input type="checkbox" name="GET_UPDATE" id="GET_UPDATE" value="Y" checked/>
	<?} else {?>
		<input type="checkbox" name="LOAD_DATA" id="LOAD_DATA" value="N" disabled="true"/>
		<label for="LOAD_DATA" disabled="true"><?=GetMessage('INSTALL_GEOBASE_LOAD_DATA')?></label>
		<br/>
		<input type="checkbox" name="GET_UPDATE" id="GET_UPDATE" value="N"/>
	<?}
	?>
	<label for="GET_UPDATE"><?=GetMessage('INSTALL_GEOBASE_AUTO_UPDATE')?></label>
	<br/><br/>
	<?if(CheckFileHeaders('http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz')){?>
		<input type="checkbox" name="LOAD_DATA_MM" id="LOAD_DATA_MM" value="Y" checked/>
		<label for="LOAD_DATA_MM"><?=GetMessage('INSTALL_GEOBASE_LOAD_MM_DATA')?></label>
		<br/>
		<input type="checkbox" name="MM_GET_UPDATE" id="MM_GET_UPDATE" value="Y" checked/>
	<?} else {?>
		<input type="checkbox" name="LOAD_DATA_MM" id="LOAD_DATA_MM" value="N" disabled="true"/>
		<label for="LOAD_DATA_MM" disabled="true"><?=GetMessage('INSTALL_GEOBASE_LOAD_MM_DATA')?></label>
		<br/>
		<input type="checkbox" name="MM_GET_UPDATE" id="MM_GET_UPDATE" value="N"/>
	<?}
	?>
	<label for="MM_GET_UPDATE"><?=GetMessage('INSTALL_GEOBASE_MM_AUTO_UPDATE')?></label>
	<br/><br/>
	<input type="submit" name="inst" value="<?= GetMessage("MOD_INSTALL")?>"/>
</form>
<?
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
	if (preg_match("/(200 OK)$/", $headers[0])) {
		return true;
	} else {
		return false;
	}
}
?>