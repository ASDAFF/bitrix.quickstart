<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CJsCore::Init('fancybox');
?>
<script type="text/javascript">
	$(document).ready(function() {
		$(".popup[rel=news-detail-photo]").fancybox();
	});
</script>
