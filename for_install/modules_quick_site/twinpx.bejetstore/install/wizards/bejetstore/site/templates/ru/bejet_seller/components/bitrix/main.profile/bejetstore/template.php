<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
?>
<?=ShowError($arResult["strProfileError"]);?>
<?
if ($arResult['DATA_SAVED'] == 'Y')
	echo ShowNote(GetMessage('PROFILE_DATA_SAVED'));
?>
<div class="bx_profile bx-auth">
	<form method="post" name="form1" action="<?=$arResult["FORM_TARGET"]?>?" enctype="multipart/form-data">
	<?=$arResult["BX_SESSION_CHECK"]?>
	<input type="hidden" name="lang" value="<?=LANG?>" />
	<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
	<input type="hidden" name="LOGIN" value=<?=$arResult["arUser"]["LOGIN"]?> />
	<input type="hidden" name="EMAIL" value=<?=$arResult["arUser"]["EMAIL"]?> />

	<fieldset>
		<legend class="hidden"><?=GetMessage("LEGEND_PROFILE")?></legend>
		<h2><?=GetMessage("LEGEND_PROFILE")?></h2>
			
		<div class="form-group">
			<label><?=GetMessage('NAME')?></label>
			<input type="text" name="NAME" maxlength="50" value="<?=$arResult["arUser"]["NAME"]?>" class="form-control">
		</div>
			
		<div class="form-group">
			<label><?=GetMessage('LAST_NAME')?></label>
			<input type="text" name="LAST_NAME" maxlength="50" value="<?=$arResult["arUser"]["LAST_NAME"]?>" class="form-control">
		</div>
		
		<div class="form-group">
			<label><?=GetMessage('SECOND_NAME')?></label>
			<input type="text" name="SECOND_NAME" maxlength="50" value="<?=$arResult["arUser"]["SECOND_NAME"]?>" class="form-control">
		</div>
		
		
		<div class="form-group">
			<label for="exampleInputFile">Фото</label>
			<input type="file" id="exampleInputFile" name="PERSONAL_PHOTO">
		</div>
	  
	  
		<!--div class="form-group">
			<label><?=GetMessage('PERSONAL_PHOTO')?></label>
			<?$file = CFile::ResizeImageGet($arResult["arUser"]["PERSONAL_PHOTO"], array('width'=>150, 'height'=>150), BX_RESIZE_IMAGE_PROPORTIONAL, true);                
                $img = '<img src="'.$file['src'].'" width="'.$file['width'].'" height="'.$file['height'].'" />';
			?>
			<br>
			<?=$img?><br><br>

				<div class="file-field input-field">
				  <div class="btn">
					<span>File</span>
					<input type="file" name="PERSONAL_PHOTO">
				  </div>
				  <div class="file-path-wrapper">
					<input class="file-path validate" type="text">
				  </div>
				</div>
		</div-->
				
	</fieldset>

	<fieldset>
		<legend class="hidden"><?=GetMessage("MAIN_PSWD")?></legend>
		<h2><?=GetMessage("MAIN_PSWD")?></h2>
			
		<div class="form-group">
			<label><?=GetMessage('NEW_PASSWORD_REQ')?></label>
			<!-- new class form-control in input -->
			<input type="password" name="NEW_PASSWORD" maxlength="50" value="" autocomplete="off" class="form-control">
		</div>
			
		<div class="form-group">
			<label><?=GetMessage('NEW_PASSWORD_CONFIRM')?></label>
			<input type="password" name="NEW_PASSWORD_CONFIRM" maxlength="50" value="" autocomplete="off" class="form-control">
		</div>
				
	</fieldset>

	<hr>
			
	<div class="form-group">
		<input name="save" value="<?=GetMessage("MAIN_SAVE")?>" class="bt_blue big shadow btn btn-default" type="submit">
	</div>
</div>
<br>
<?
if($arResult["SOCSERV_ENABLED"])
{
	$APPLICATION->IncludeComponent("bitrix:socserv.auth.split", ".default", array(
			"SHOW_PROFILES" => "Y",
			"ALLOW_DELETE" => "Y"
		),
		false
	);
}
?>
