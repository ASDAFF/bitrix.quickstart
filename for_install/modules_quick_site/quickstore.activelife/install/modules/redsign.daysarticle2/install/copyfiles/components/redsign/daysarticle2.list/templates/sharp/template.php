<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if ($arParams['TEMPLATE_SIZE']=='big') {
	include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/big.php");
} else if ($arParams['TEMPLATE_SIZE']=='medium') {
	include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/medium.php");
} else {
	include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/small.php");
}
?>
<script>
	$(document).ready(function() {
		$(".daysarticle").each(function(i){
			if (!$(this).hasClass('inited')) {
				QB_timer($(this));
			}
		});
	});
</script>