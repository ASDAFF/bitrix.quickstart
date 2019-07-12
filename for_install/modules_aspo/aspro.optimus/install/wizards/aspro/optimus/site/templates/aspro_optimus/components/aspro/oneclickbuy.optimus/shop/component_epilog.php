<?global $USER;?>
<?if($USER->IsAuthorized()):?>
	<script type="text/javascript">
	$(document).ready(function() {
		$('#one_click_buy_id_FIO').val('<?=$USER->GetFullName()?>');
		$('#one_click_buy_id_PHONE').val('<?=$arResult['USER_PHONE']?>');
		$('#one_click_buy_id_EMAIL').val('<?=$USER->GetEmail()?>');
	});
	</script>
<?endif;?>