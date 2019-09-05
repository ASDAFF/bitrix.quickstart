<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<div class="subscribe form">

	<h2><?=GetMessage("SUBSCR_NEW_TITLE")?></h2>
	<p><?=GetMessage("SUBSCR_NEW_NOTE")?></p>
	
	<form action="<?=$arResult["FORM_ACTION"]?>" method="get">
		
		<?foreach($arResult["RUBRICS"] as $itemID => $itemValue):?>
			<div class="field">
				<input type="checkbox" name="sf_RUB_ID[]" id="sf_RUB_ID_<?=$itemID?>" value="<?=$itemValue["ID"]?>" checked class="inputcheckbox" />
				<label for="sf_RUB_ID_<?=$itemID?>"><?=$itemValue["NAME"]?></label>
				
				<?if($itemValue["DESCRIPTION"]):?>
					<p><?=$itemValue["DESCRIPTION"]?></p>
				<?endif;?>
				
				<?if($arResult["SHOW_COUNT"]):?>
					<?=GetMessage("SUBSCR_CNT")?>:
					<?=$itemValue["SUBSCRIBER_COUNT"]?>
				<?endif?>
			</div>
		<?endforeach;?>
		
		<label><?=GetMessage("SUBSCR_ADDR")?></label>
		<div class="field">
			<input type="email" name="sf_EMAIL" value="<?=$arResult["EMAIL"]?>" title="<?=GetMessage("SUBSCR_EMAIL_TITLE")?>" class="inputtext" />
		</div>
		
		<div class="form_footer">
			<input type="submit" value="<?=GetMessage("SUBSCR_BUTTON")?>" class="btn" />
		</div>
		
	</form>
	
	

	<form action="<?=$arResult["FORM_ACTION"]?>" method="get">
		
		<?echo bitrix_sessid_post();?>

		<h2><?=GetMessage("SUBSCR_EDIT_TITLE")?></h2>
		<p><?=GetMessage("SUBSCR_EDIT_NOTE")?></p>
		
		<label>e-mail</label>
		<div class="field">
			<input type="email" name="sf_EMAIL" value="<?=$arResult["EMAIL"]?>" title="<?=GetMessage("SUBSCR_EMAIL_TITLE")?>" class="inputtext" />
		</div>
		
		<?if($arResult["SHOW_PASS"]=="Y"):?>
			<label><?=GetMessage("SUBSCR_EDIT_PASS")?><span class="starrequired">*</span></label>
			<div class="field">
				<input type="password" name="AUTH_PASS" value="" title="<?=GetMessage("SUBSCR_EDIT_PASS_TITLE")?>" class="inputtext" />
			</div>
		<?else:?>
			<div class="field">
				<?=GetMessage("SUBSCR_EDIT_PASS_ENTERED")?><span class="starrequired">*</span>
			</div>
		<?endif;?>

		<div class="form_footer">
			<input type="submit" value="<?=GetMessage("SUBSCR_EDIT_BUTTON")?>" class="btn" class="inputtext" />
			<input type="hidden" name="action" value="authorize" />
		</div>
		
	</form>


	
	<form action="<?=$arResult["FORM_ACTION"]?>" method="get">
		
		<?echo bitrix_sessid_post();?>

		<h2><?=GetMessage("SUBSCR_PASS_TITLE")?></h2>
		<p><?=GetMessage("SUBSCR_PASS_NOTE")?></p>
		
		<label>e-mail</label>
		<div class="field">
			<input type="email" name="sf_EMAIL" value="<?=$arResult["EMAIL"]?>" title="<?=GetMessage("SUBSCR_EMAIL_TITLE")?>" class="inputtext" />
		</div>
		
		<div class="form_footer">
			<input type="submit" value="<?=GetMessage("SUBSCR_PASS_BUTTON")?>" class="btn" />
			<input type="hidden" name="action" value="sendpassword" />
		</div>
		
	</form>


	<form action="<?=$arResult["FORM_ACTION"]?>" method="get">
		
		<?echo bitrix_sessid_post();?>

		<h2><?=GetMessage("SUBSCR_UNSUBSCRIBE_TITLE")?></h2>
		<p><?=GetMessage("SUBSCR_UNSUBSCRIBE_NOTE")?></p>
		
		<label>e-mail</label>
		<div class="field">
			<input type="email" name="sf_EMAIL" value="<?=$arResult["EMAIL"]?>" title="<?=GetMessage("SUBSCR_EMAIL_TITLE")?>" class="inputtext" />
		</div>
		
		<?if($arResult["SHOW_PASS"]=="Y"):?>
			<label><?=GetMessage("SUBSCR_EDIT_PASS")?><span class="starrequired">*</span></label>
			<div class="field">
				<input type="password" name="AUTH_PASS" value="" title="<?=GetMessage("SUBSCR_EDIT_PASS_TITLE")?>" class="inputtext" />
			</div>
		<?else:?>
			<div class="field">
				<?=GetMessage("SUBSCR_EDIT_PASS_ENTERED")?><span class="starrequired">*</span>
			</div>
		<?endif;?>

		<div class="form_footer">
			<input type="submit" value="<?=GetMessage("SUBSCR_EDIT_BUTTON")?>" class="btn" />
			<input type="hidden" name="action" value="authorize" />
		</div>
	
	</form>
	
	<div class="form_info">
		<span class="starrequired">*&nbsp;</span><?=GetMessage("SUBSCR_NOTE")?>
	</div>
	
</div>
