<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>
<div id="md_ticket_create" class="md_form bx_order_make flLeft">
<?if(!empty($arResult["ERROR_MESSAGE"]["CUSTOM"]))
{
	foreach($arResult["ERROR_MESSAGE"]["CUSTOM"] as $v)
		ShowError($v);
}
/**
 * Copyright (c) 25/7/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if(strlen($arResult["OK_MESSAGE"]) > 0)
{
	?><div style='color:green; margin-bottom:15px;' class="mf-ok-text"><?=$arResult["OK_MESSAGE"]?></div><?
}
?>
<form action="" method="POST">
	<?=bitrix_sessid_post()?>
	<input type='hidden' name='MD_POST' value='Y'>
		<div>
		
			<!-- NAME -->
			<div class="bx_block r1x3 pt8"><?=GetMessage("MD_NAME")?>
				<?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])):?>
					<span class="bx_sof_req">*</span>
				<? endif;?>
			</div>
			<div class="bx_block r3x1 md_rel">
				<input type="text" name="MD_NAME" value="<?=$arResult["AUTHOR_NAME"]?>" size="40" maxlength="250">
				<p><font class='errortext'><?=$arResult["ERROR_MESSAGE"]["NAME"]?></font></p>
			</div>
			<div style="clear: both;"></div>
			<br>
			
			
			<!-- EMAIL -->
			<div class="bx_block r1x3 pt8"><?=GetMessage("MD_EMAIL")?> 
				<?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("EMAIL", $arParams["REQUIRED_FIELDS"])):?>
					<span class="bx_sof_req">*</span>
				<? endif;?>
			</div>
			<div class="bx_block r3x1 md_rel">
				<input type="text" name="MD_EMAIL" value="<?=$arResult["AUTHOR_EMAIL"]?>" size="40" maxlength="250">
				<p><font class='errortext'><?=$arResult["ERROR_MESSAGE"]["EMAIL"]?></font></p>
			</div>
			<div style="clear: both;"></div>
			<br>
		
			<?foreach($arParams["NEW_EXT_FIELDS"] as $fid=>$field):?>
						<!-- EXT FIELDS -->
			<div class="bx_block r1x3 pt8"><?=$field?> 
				<?if(in_array($fid, $arParams["REQUIRED_FIELDS"])):?>
					<span class="bx_sof_req">*</span>
				<? endif;?>
			</div>
			<div class="bx_block r3x1 md_rel">
				<input type="text" name="<?=$fid?>" value="<?=htmlspecialcharsEx($_POST[$fid])?>" size="40" maxlength="250">
				<p><font class='errortext'><?=$arResult["ERROR_MESSAGE"][$fid]?></font></p>
			</div>
			<div style="clear: both;"></div>
			<br>
			
			<? endforeach;?>
			
			<? if($arParams["SHOW_CATEGORY"]=="Y"):?>
					<!-- CATEGORY -->
			<div class="bx_block r1x3 pt8"><?=GetMessage("MD_CATEGORY")?> 
			<span class="bx_sof_req">*</span>
			</div>
			<div class="bx_block r3x1 md_rel">
				<select name="MD_CATEGORY">
					<?foreach($arResult["CATEGORY"] as $category):?>
					<option value="<?=$category["ID"]?>"  <?=($_POST['MD_CATEGORY']==$category['ID'])?('selected="selected"'):('')?>><?=$category["NAME"]?></option>
					<?endforeach;?>
				</select>
			</div>
			<div style="clear: both;"></div>
			<br>
		
			<? endif;?>
			
			<? if($arParams["SHOW_STATUS"]=="Y"):?>
					<!-- STATUS -->
			<div class="bx_block r1x3 pt8"><?=GetMessage("MD_STATUS")?>
			<span class="bx_sof_req">*</span> 
			</div>
			<div class="bx_block r3x1 md_rel">
				<select name="CRITICAL">
					<?foreach($arResult["CRITICAL"] as $status):?>
					<option value="<?=$status["ID"]?>"  <?=($_POST['CRITICAL']==$status['ID'])?('selected="selected"'):('')?>><?=$status["NAME"]?></option>
					<?endforeach;?>
				</select>
			</div>
			<div style="clear: both;"></div>
			<br>
		
			<? endif;?>
			
				<!-- MESSAGE -->
			<div class="bx_block r1x3 pt8"><?=GetMessage("MD_MESSAGE")?> 
				<?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("MESSAGE", $arParams["REQUIRED_FIELDS"])):?>
					<span class="bx_sof_req">*</span>
				<? endif;?>
			</div>
			<div class="bx_block r3x1 md_rel">
				<textarea   cols="30" rows="3" name="MD_MESSAGE"><?=htmlspecialcharsEx($_POST["MD_MESSAGE"])?></textarea>
				<p><font class='errortext'><?=$arResult["ERROR_MESSAGE"]["MESSAGE"]?></font></p>
			</div>
			<div style="clear: both;"></div>
			<br>
			
			<?if($arParams["USE_CAPTCHA"] == "Y"):?>
				<div class="bx_block r1x3 pt8"><?=GetMessage("MD_CAPTCHA_CODE")?>
					<span class="bx_sof_req">*</span>
				</div> 
				<div class="bx_block r3x1 md_rel">
				<input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>" /> 
            	<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="180" height="40" alt="CAPTCHA" />
            	<br />
            	<input type="text" name="captcha_word" maxlength="50" value="" />
            	<p><font class='errortext'><?=$arResult["ERROR_MESSAGE"]["CAPTCHA"]?></font></p>
				</div>
				<div style="clear: both;"></div>
				<br>
			<?endif;?>
			
		</div>
		<!-- SUBMIT -->
		<div class="bx_ordercart_order_pay_center flRight">
		 	<input class='checkout' type="submit" value="<?=GetMessage("MD_SEND")?>" name="submit">
	 	</div>


	
</form>


</div>