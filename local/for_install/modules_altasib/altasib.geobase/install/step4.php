<?
#################################################
#   Company developer: ALTASIB                  #
#   Developer: adumnov                          #
#   Site: http://www.altasib.ru                 #
#   E-mail: dev@altasib.ru                      #
#   Copyright (c) 2006-2014 ALTASIB             #
#################################################
?>
<?CUtil::InitJSCore(Array("jquery"));?>
<style>
	.alx-main{
		width: 600px;
		padding: 10px;
		border: none;
	}
	.alx-gbase-main-box {
		display: block;
		padding-bottom: 10px;
		margin-bottom: 5px;
		position: relative;
		width: 100%;
	}
	.alx-gbase-main-box span {
		display: inline-block;
	}
	.alx-gbase-main-box > div {
		margin: 10px 0 5px 0;
		text-align: right;
	}
	#altasib_geobase_imported, #altasib_geobase_imported_mm, #altasib_geobase_kladr_available, #altasib_geobase_kladr_available_mm, #altasib_geobase_import_err, #altasib_geobase_import_err_mm, #altasib_geobase_title_mm, #altasib_geobase_title_descr_mm, #altasib_geobase_wait, #altasib_geobase_wait_mm, #altasib_geobase_wait_title_mm {
		display:none;
	}
	.ag_waiting{
		top: 50%;
		left: 50%;
		padding: 30px;
		display: none;
		margin: 0 auto;
	}
</style>

<script language="JavaScript">
function altasib_geobase_import(){
	$('#altasib_geobase_wait').css({'display': 'block'});
	$('#altasib_geobase_wait_title').show();
	$.ajax({
		type: "POST",
		url: '/bitrix/admin/altasib_geobase_import_db.php',
		dataType: 'html',
		data: { 'action': 'import_csv',
				'dst': 'kladr',
				'sessid': BX.message('bitrix_sessid')
			},
		timeout: 420000, // 7 min
		success: function(data){
			if(data == 'available')
				$('#altasib_geobase_kladr_available').show();
			else if(data == '1' || data == '')
				$('#altasib_geobase_imported').show();
			else
				$('#altasib_geobase_import_err').show();
		},
		error: function(data){
			$('#altasib_geobase_import_err').show();

		},			
		complete: function(data){
			$('#altasib_geobase_wait').hide();
			$('#altasib_geobase_wait_title').hide();
			altasib_geobase_import_mm();
		},
	});
}

function altasib_geobase_import_mm(){
	$('#altasib_geobase_title_mm').show();
	$('#altasib_geobase_title_descr_mm').show();
	$('#altasib_geobase_wait_mm').css({'display': 'block'});
	$('#altasib_geobase_wait_title_mm').show();

	$.ajax({
		type: "POST",
		url: '/bitrix/admin/altasib_geobase_import_db.php',
		dataType: 'html',
		data: { 'action': 'import_csv',
				'dst': 'maxmind',
				'sessid': BX.message('bitrix_sessid')
			},
		timeout: 420000, // 7 min
		success: function(data){
			if(data == 'available')
				$('#altasib_geobase_kladr_available_mm').show();
			else if(data == '1' || data == '')
				$('#altasib_geobase_imported_mm').show();
			else
				$('#altasib_geobase_import_err_mm').show();
		},
		error: function(data){
			$('#altasib_geobase_import_err_mm').show();
		},			
		complete: function(data){
			$('#altasib_geobase_wait_mm').hide();
			$('#altasib_geobase_wait_title_mm').hide();
			$('#altasib_geobase_form').submit();
		},
	});
}
BX.ready(function(){
	altasib_geobase_import();
});
</script>

<div class="alx-main" style="text-align: center">
	<div class="alx-gbase-main-box" id="alxLoaderUI">
		<h3 id="altasib_geobase_title"><?=GetMessage("INSTALL_GEOBASE_IMPORT_KLADR")?></h3>
		<span id="altasib_geobase_title_descr"><?=GetMessage("INSTALL_GEOBASE_KLADR_DESCR")?></span>
		<img class="ag_waiting" id="altasib_geobase_wait" src="/bitrix/modules/altasib.geobase/images/wait.gif">
		<span id="altasib_geobase_wait_title"><?=GetMessage("INSTALL_GEOBASE_WHITING")?></span>
	</div>
	<div id="altasib_geobase_imported">
		<?echo CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("INSTALL_GEOBASE_IMPORTED"), "TYPE"=>"OK"));?>
	</div>
	<div id="altasib_geobase_kladr_available">
		<?echo CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("INSTALL_GEOBASE_KLADR_THERE_ARE"), "TYPE"=>"OK"));?>
	</div>
	<div id="altasib_geobase_import_err">
		<?echo CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("INSTALL_GEOBASE_IMPORTED_ERR"), "TYPE"=>"NO"));?>
	</div>
	
	<div class="alx-gbase-main-box" id="alxLoaderUI_MM">
		<h3 id="altasib_geobase_title_mm"><?=GetMessage("INSTALL_GEOBASE_IMPORT_MM")?></h3>
		<span id="altasib_geobase_title_descr_mm"><?=GetMessage("INSTALL_GEOBASE_MM_DESCR")?></span>
		<img class="ag_waiting" id="altasib_geobase_wait_mm" src="/bitrix/modules/altasib.geobase/images/wait.gif">
		<span id="altasib_geobase_wait_title_mm"><?=GetMessage("INSTALL_GEOBASE_WHITING")?></span>
	</div>
	<div id="altasib_geobase_imported_mm">
		<?echo CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("INSTALL_GEOBASE_IMPORTED_MM"), "TYPE"=>"OK"));?>
	</div>
	<div id="altasib_geobase_kladr_available_mm">
		<?echo CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("INSTALL_GEOBASE_KLADR_THERE_ARE_MM"), "TYPE"=>"OK"));?>
	</div>
	<div id="altasib_geobase_import_err_mm">
		<?echo CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("INSTALL_GEOBASE_IMPORTED_ERR_MM"), "TYPE"=>"NO"));?>
	</div>

	<form action="<?echo $APPLICATION->GetCurPage()?>" name="form1" id="altasib_geobase_form">
		<?=bitrix_sessid_post()?>
		<input type="hidden" name="lang" value="<?=LANG?>">
		<input type="hidden" name="id" value="altasib.geobase">
		<input type="hidden" name="install" value="Y">
		<input type="hidden" name="step" value="5">
	<?if ($_REQUEST['GET_UPDATE'] == 'Y' || $_REQUEST['GET_UPDATE'] == "N"):?>
		<input type="hidden" name="GET_UPDATE" value="<?=$_REQUEST['GET_UPDATE']?>"/>
	<?endif;?>
	<?if ($_REQUEST['MM_GET_UPDATE'] == 'Y' || $_REQUEST['MM_GET_UPDATE'] == "N"):?>
		<input type="hidden" name="MM_GET_UPDATE" value="<?=$_REQUEST['MM_GET_UPDATE']?>"/>
	<?endif;?>
	</form>
</div>