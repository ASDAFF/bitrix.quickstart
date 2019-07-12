<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

?>
<?//=ShowError($arResult["strProfileError"]);?>
<?
if ($arResult["strProfileError"]) {
	?><div class="alert alert-error">
		<?
	echo $arResult["strProfileError"];
	?></div><?
}

if ($arResult['DATA_SAVED'] == 'Y') {
	?><div class="alert alert-success">
		<?
	echo GetMessage('PROFILE_DATA_SAVED');
	?></div><?
}	

?>

<form class="form-horizontal demo-personal-form" method="post" name="form1" action="<?=POST_FORM_ACTION_URI?>" enctype="multipart/form-data">
	<section class="demo-section">
		<div class="left-bl">
        <?php 
		if (strlen($arResult["arUser"]["TIMESTAMP_X"])>0) {
			$arr1 = explode(" ", $arResult["arUser"]["TIMESTAMP_X"]);
			//deb($arr1);
			?>
			<?=GetMessage('USER_UPDATE_DATE')?>: <span><b><?=$arr1[0]?></b> <?=$arr1[1]?></span>
			<?
		}
		?>
		
		</div>
        <div class="right-bl">
        <?php 
		if (strlen($arResult["arUser"]["LAST_LOGIN"])>0) {
			$arr2 = explode(" ", $arResult["arUser"]["LAST_LOGIN"]);
			?>
			 <?=GetMessage('USER_LAST_AUTH')?>: <span><b><?=$arr2[0]?></b> <?=$arr2[1]?></span>
			 <?php 
		}
		?>
       
		</div>
        <div class="clear"></div>
	</section>
	
	<?=$arResult["BX_SESSION_CHECK"]?>
	<input type="hidden" name="lang" value="<?=LANG?>" />
	<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
	<input type="hidden" name="LOGIN" value=<?=$arResult["arUser"]["LOGIN"]?> />
	<input type="hidden" name="EMAIL" value=<?=$arResult["arUser"]["EMAIL"]?> />

	<div class="control-group">
              				<label class="control-label"><?=GetMessage('NAME')?>:</label>
              					<div class="controls">
                					<input type="text" name="NAME" maxlength="50" class="input_text_style" value="<?=$arResult["arUser"]["NAME"]?>" />
              					</div>
     </div>
		
	<div class="control-group">
              				<label class="control-label"><?=GetMessage('LAST_NAME')?>:</label>
              					<div class="controls">
                					<input type="text" name="LAST_NAME" maxlength="50" class="input_text_style" value="<?=$arResult["arUser"]["LAST_NAME"]?>" />
              					</div>
	</div>
	<div class="control-group">
              				<label class="control-label"><?=GetMessage('SECOND_NAME')?>:</label>
              					<div class="controls">
                					<input type="text" name="SECOND_NAME" maxlength="50" class="input_text_style" value="<?=$arResult["arUser"]["SECOND_NAME"]?>" />
              					</div>
	</div>
    <div class="control-group">
              				<label class="control-label">Email:<span class="starrequired">*</span></label>
              					<div class="controls">
                					<input type="text" placeholder="Email" name="EMAIL" maxlength="50" value="<? echo $arResult["arUser"]["EMAIL"]?>" />
              					</div>
    </div>		

	
            			
            			<div class="control-group">
              				<label class="control-label"><?=GetMessage('NEW_PASSWORD_REQ')?>:</label>
              					<div class="controls">
                					<input type="password" placeholder="Password" name="NEW_PASSWORD" maxlength="50" class="input_text_style" value="" autocomplete="off" /> 

              					</div>
            			</div>
            			<div class="control-group">
              				<label class="control-label"><?=GetMessage('NEW_PASSWORD_CONFIRM')?>:</label>
              					<div class="controls">
              						<input type="password" placeholder="Password" name="NEW_PASSWORD_CONFIRM" maxlength="50" class="input_text_style" value="" autocomplete="off" />
              					</div>
            			</div>
 
                
                <div class="message-bl padd-l"><?=GetMessage('USER_PASS_LABEL')?>.*</div>
               <p class="padd-l"><input name="save" class="btn" type="submit" value="<?=GetMessage("MAIN_SAVE")?>">
            	<button class="btn" type="reset"><?=GetMessage('USER_RESET')?></button></p>
	

	</form>


<?
if($arResult["SOCSERV_ENABLED"])
{
	$APPLICATION->IncludeComponent("bitrix:socserv.auth.split", "demoshop", array(
			"SHOW_PROFILES" => "Y",
			"ALLOW_DELETE" => "Y"
		),
		$component
	);
}
?>
