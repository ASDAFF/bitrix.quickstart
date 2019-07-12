<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
//echo "<pre>"; print_r($arResult); echo "</pre>";
//exit();
//echo "<pre>"; print_r($_SESSION); echo "</pre>";

?>
<?=ShowError($arResult["strProfileError"]);?>
<?
if ($arResult['DATA_SAVED'] == 'Y')
	echo ShowNote(GetMessage('PROFILE_DATA_SAVED'));
?>
<form method="post" name="form1" action="<?=$arResult["FORM_TARGET"]?>?" enctype="multipart/form-data">
<?=$arResult["BX_SESSION_CHECK"]?>
<input type="hidden" name="lang" value="<?=LANG?>" />
<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
<input type="hidden" name="LOGIN" value=<?=$arResult["arUser"]["LOGIN"]?> />
<input type="hidden" name="EMAIL" value=<?=$arResult["arUser"]["EMAIL"]?> />

<div class="content-form profile-form">
	<div class="fields">
		<div class="field field-firstname">
			<label class="field-title"><?=GetMessage('NAME')?></label>
			<div class="form-input"><input type="text" name="NAME" maxlength="50" value="<?if(isset($_REQUEST['NAME'])):?><?=htmlspecialchars($_REQUEST['NAME'])?><?else:?><?=$arResult["arUser"]["NAME"]?><?endif?>" /></div>
		</div>
		<div class="field field-lastname">	
			<label class="field-title"><?=GetMessage('LAST_NAME')?></label>
			<div class="form-input"><input type="text" name="LAST_NAME" maxlength="50" value="<?if(isset($_REQUEST['LAST_NAME'])):?><?=htmlspecialchars($_REQUEST['LAST_NAME'])?><?else:?><?=$arResult["arUser"]["LAST_NAME"]?><?endif?>" /></div>
			
		</div>
		<div class="field field-secondname">	
			<label class="field-title"><?=GetMessage('SECOND_NAME')?></label>
			<div class="form-input"><input type="text" name="SECOND_NAME" maxlength="50" value="<?if(isset($_REQUEST['SECOND_NAME'])):?><?=htmlspecialchars($_REQUEST['SECOND_NAME'])?><?else:?><?=$arResult["arUser"]["SECOND_NAME"]?><?endif?>" /></div>
		</div>
		<div class="field field-secondname">	
			<label class="field-title"><?=GetMessage('EMAIL')?></label>
			<div class="form-input">
				<input type="text" name="EMAIL" maxlength="50" value="<?if(isset($_REQUEST['EMAIL'])):?> <?=htmlspecialchars($_REQUEST['EMAIL'])?><?else:?><?=$arResult["arUser"]["EMAIL"]?><?endif?>" />
			</div>
		</div>
	</div>
</div>
<div class="content-form profile-form">
	<div class="legend"><?=GetMessage("MAIN_PSWD")?></div>
	<div class="fields">
		<div class="field field-password_new">	
			<label class="field-title"><?=GetMessage('NEW_PASSWORD_REQ')?></label>

			<div class="form-input"><input type="password" name="NEW_PASSWORD" maxlength="50" value="" autocomplete="off" /></div>
			
		</div>
		<div class="field field-password_confirm">	
			<label class="field-title"><?=GetMessage('NEW_PASSWORD_CONFIRM')?></label>
			<div class="form-input"><input type="password" name="NEW_PASSWORD_CONFIRM" maxlength="50" value="" autocomplete="off" /></div>
			
		</div>
	</div>
</div>

<div class="content-form profile-form">
	<div class="button"><input name="save" value="<?=GetMessage("MAIN_SAVE")?>"class="input-submit" type="submit"></div>
</div>
</form>