<?
/**
 * Company developer: REASPEKT
 * Developer: adel yusupov
 * Site: http://www.reaspekt.ru
 * E-mail: adel@reaspekt.ru
 * @copyright (c) 2016 REASPEKT
 */
 
use \Bitrix\Main\Localization\Loc;

$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();
?>
<style>
.reasp-main{
	width: 510px;
	padding: 10px;
	border: none;
}
.reasp-gbase-main-box {
	display: block;
	padding-bottom: 10px;
	margin-bottom: 5px;
	position: relative;
	width: 100%;
}
.reasp-gbase-main-box span {
	display: inline-block;
}
.reasp-gbase-main-box > div {
	margin: 10px 0 5px 0;
	text-align: right;
}
.reasp-gbase-progress-bar {
	width: 100%;
	height: 14px
}
.reasp-gbase-progress-bar span {
	position: absolute;
}
.reasp-gbase-progress-bar > span {
	border: 1px solid silver;
	width: 92%;
	left: 2px;
	height: 14px;
	text-align: left;
}
.reasp-gbase-progress-bar > span + span {
	padding-left: 2px;
	border: none;
	width: 7%;
	height: 14px;
	left: auto;
	right: 0;
	text-align: right
}
#progress {
	height: 14px;
	background: #637f9c;
}
</style>

<script language="JavaScript">
var timer, obData, updateMode;
reaspekt_geobase_obHandler = function (data) {
	console.log(data);
	var progress, value, title, send;
	updateMode = <?=($update_mode ? 'true' : 'false')?>;
	obData = JSON.parse(data);
	progress = document.getElementById('progress');
	value = document.getElementById('value');
	title = document.getElementById('title');
	if (obData.STATUS == 3){
		send = {
			"action": obData.NEXT_STEP,
			"timeout": 4
		};
		progress.style.width = obData.PROGRESS + '%';
		value.innerHTML = obData.PROGRESS + '%';
		BX.ajax.post('/bitrix/admin/reaspekt_geobase_update_ipgeobase.php', send, reaspekt_geobase_obHandler);
	}
	else if (obData.STATUS == 2) {
		send = {
			"action": obData.NEXT_STEP,
			"by_step": "Y",
			"filename": obData.FILENAME,
			"seek": obData.SEEK,
			"timeout": 4
		};
		progress.style.width = obData.PROGRESS + '%';
		value.innerHTML = obData.PROGRESS + '%';
		if (obData.PROGRESS == 100) {
			timer = setInterval(function () {
				title.innerHTML = "<?=Loc::getMessage("REASPEKT_TITLE_UNPACK_FILE")?>";
				progress.style.width = 0 + '%';
				value.innerHTML = 0 + '%';
				BX.ajax.post('/bitrix/admin/reaspekt_geobase_update_ipgeobase.php', send, reaspekt_geobase_obHandler);
				clearInterval(timer);
			}, 500);
		} else {
			BX.ajax.post('/bitrix/admin/reaspekt_geobase_update_ipgeobase.php', send, reaspekt_geobase_obHandler);
		}
	}
	else if (obData.STATUS == 1) {
		send = {
			"action": obData.NEXT_STEP,
			"filename": obData.FILENAME,
			"seek": obData.SEEK ? obData.SEEK : 0,
			"drop_t": obData.DROP_T,
			"TRUNCATE": obData.TRUNCATE,
			"timeout": 4
		};
		progress.style.width = obData.PROGRESS + '%';
		value.innerHTML = obData.PROGRESS + '%';
		if (obData.PROGRESS == 100) {
			timer = setInterval(function () {
				title.innerHTML = "<?=Loc::getMessage("REASPEKT_TITLE_DB_UPDATE")?>";
				progress.style.width = 0 + '%';
				value.innerHTML = 0 + '%';
				BX.ajax.post('/bitrix/admin/reaspekt_geobase_update_ipgeobase.php', send, reaspekt_geobase_obHandler);
				clearInterval(timer);
			}, 500);
		} else {
			BX.ajax.post('/bitrix/admin/reaspekt_geobase_update_ipgeobase.php', send, reaspekt_geobase_obHandler);
		}
	}
	else if (obData.STATUS == 0) {
		document.getElementById("form_setup").submit();
	}
};
function reaspekt_geobase_updateDB() {
	BX.ajax.post('/bitrix/admin/reaspekt_geobase_update_ipgeobase.php', {'action':'LOAD', "timeout":3}, reaspekt_geobase_obHandler);
}

BX.ready(function(){
	reaspekt_geobase_updateDB();
});
</script>

<div class="reasp-main" style="text-align: center">
	<div class="reasp-gbase-main-box" id="alxLoaderUI">
		<h3 id="title"><?=Loc::getMessage("REASPEKT_TITLE_LOAD_FILE")?></h3>
		<span class="reasp-gbase-progress-bar">
			<span>
				<span id="progress"></span>
			</span>
			<span id="value">0%</span>
		</span>
	</div>

	<form action="<?=$APPLICATION->GetCurPage()?>" id="form_setup">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="lang" value="<?=LANGUAGE_ID?>"/>
		<input type="hidden" name="id" value="reaspekt.geobase"/>
		<input type="hidden" name="install" value="N"/>
		<input type="hidden" name="step" value="3"/>
	<?if ($request['LOAD_DATA'] == 'Y' || $request['LOAD_DATA'] == "N") :?>
		<input type="hidden" name="LOAD_DATA" value="<?=$request['LOAD_DATA']?>"/>
	<?endif;?>
	</form>
</div>