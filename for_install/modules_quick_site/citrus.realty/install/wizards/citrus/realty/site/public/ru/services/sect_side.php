<?$APPLICATION->IncludeComponent(
	"citrus:realty.contacts",
	"block",
	array()
);?>
<div class="case">
	<ul>
		<li class="case-email"><a href="<?=SITE_DIR?>ajax/share_via_email.php" class="ajax-popup">Отправить на e-mail</a></li>
		<li class="case-print"><a href="javascript:window.print();" class="dotted">Версия для печати</a></li>
	</ul>
</div>