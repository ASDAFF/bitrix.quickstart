<?
if($arParams["FANCYBOX_SCRIPT_ON"]=="Y") {
	$APPLICATION->AddHeadScript($templateFolder."/js/jquery.fancybox.pack.js");
	$APPLICATION->SetAdditionalCSS($templateFolder."/js/jquery.fancybox.css");
}
?>


<script type="text/javascript"> jQuery(function($){
	var max_col_height = 0; // максимальная высота, первоначально 0
	$('.is-title').each(function(){ // цикл "для каждой из колонок"
		if ($(this).height() > max_col_height) { // если высота колонки больше значения максимальной высоты,
			max_col_height = $(this).height(); // то она сама становится новой максимальной высотой
		}
	});
	$('.is-title').height(max_col_height); // устанавливаем высоту каждой колонки равной значению максимальной высоты
});
</script>
