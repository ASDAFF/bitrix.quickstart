<?require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");?>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.tools.min.js"></script>
<a href="#" class="close"><img src="#SITE_DIR#images/call-back/modal_close.png" /></a>
<h2>Ваш отзыв</h2>
<?=CAjax::GetForm('name="ajaxform3" action="#SITE_DIR#include/rating/rating-sbox-form.php" method="GET" ', 'addRaiting', '1', true, false)?>
	<span id="addRaiting">
		<?$APPLICATION->IncludeFile('#SITE_DIR#include/rating/rating-sbox-form.php', FALSE, array('MODE'=>'php', 'SHOW_BORDER'=>'Y'));?>
	</span>
</form>

<!--Скрипт обратного звонка-->
<script type="text/javascript">
	var triggers = $("a.modalInput").overlay({ 
	    expose: { 
	        color: '#111', 
	        loadSpeed: 200, 
	        opacity: 0.6 
	    }, 
	    closeOnClick: true 
	});
	$('#rating .close').click(function () {
		// close the overlay
		triggers.each(function (num) {
			if ($(this).attr('rel')=='#rating') {
				$(this).overlay();
			}
		});
		return false;
	});
</script>