<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CJsCore::Init('fancybox');
?>
<script type="text/javascript">
$(document).ready(function() {
	$(".b-photo-line-item .popup[rel=image]").fancybox();
});
</script>
