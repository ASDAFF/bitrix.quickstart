<?require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php");?>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.tools.min.js"></script>
<a href="#" class="close"><img src="#SITE_DIR#images/call-back/modal_close.png" /></a>
<h2>Как вам позвонить?</h2>
<?=CAjax::GetForm('name="ajaxform4" action="#SITE_DIR#include/phone-sbox-form.php" method="GET" ', 'addPH', '1', true, false)?>

	<span id="addPH">
		<?$APPLICATION->IncludeFile('#SITE_DIR#include/phone-sbox-form.php', FALSE, array('MODE'=>'php', 'SHOW_BORDER'=>'Y'));?>
	</span>
</form>

<script type="text/javascript">
	var triggers = $("a.modalInput").overlay({ 
	    expose: { 
	        color: '#111', 
	        loadSpeed: 200, 
	        opacity: 0.6 
	    }, 
	    closeOnClick: true 
	});
	$('#phone .close').click(function () {
		// close the overlay
		triggers.each(function (num) {
			if ($(this).attr('rel')=='#phone') {
				$(this).overlay();
			}
		});
		return false;
	});
</script>