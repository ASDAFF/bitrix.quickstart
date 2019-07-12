<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();$this->setFrameMode(true);
$postUrl = SITE_DIR . 'include/ajax/feedback.php';
?>
<div aria-hidden="true" aria-labelledby="myModalLabel7" role="dialog" tabindex="-1" class="modal hide fade feedback-my" id="feedBackModal">

<div class="modal-header">
	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
	<h3 id="myModalLabel7"><?=GetMessage("FORM_NAME_LABEL")?></h3>
</div>
<div class="modal-body">
	
	<form id="feedback_form" action="<?=$postUrl?>" name="FORM_FEEDBACK" method="POST">
	
	<input type="hidden" name="form_id" value="feedback">
	<?=bitrix_sessid_post('sessid2')?>
	<input type="hidden" name="WEB_FORM_ID" value="<?=$arParams["WEB_FORM_ID"]?>">
	<div></div>
	<p><?=GetMessage("FORM_LABEL_1")?></p>
	<table class="data-table bx-forgotpass-table" id="feedback-t">
	<tbody>	
	<tr>
	<td><?=GetMessage("FORM_FIELD_LABEL_1")?></td>
	<td>
	<input type="text" id="feedback_name" name="form_text_<?=$arResult["arQuestions"]["feedback_name"]["ID"]?>" value="" maxlength="50">
	</td>
	</tr>
	<tr> 
	<td><?=GetMessage("FORM_FIELD_LABEL_2")?></td>
	<td>
	<textarea id="feedback_message" name="form_textarea_<?=$arResult["arQuestions"]["feedback_message"]["ID"]?>" class="form-horizontal-my"></textarea>
	
	</td>
	</tr>
	<tr> 
	<td><?=GetMessage("FORM_FIELD_LABEL_3")?></td>
	<td>
	<input type="text" id="feedback_email" name="form_email_<?=$arResult["arQuestions"]["feedback_email"]["ID"]?>" value="" maxlength="255">
	
	</td>
	</tr>
	<tr> 
	<td colspan="2"><input type="submit" name="web_form_submit" class="btn btn-rl" value="<?=GetMessage("FORM_ASK_LABEL")?>"></td>
	</tr>
	</tbody>
	</table>
	
	</form>
</div>

</div><??>