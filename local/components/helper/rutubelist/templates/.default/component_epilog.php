<?
if($arParams["FANCYBOX_SCRIPT_ON"]=="Y") {
	$APPLICATION->AddHeadScript($templateFolder."/js/jquery.fancybox.pack.js");
	$APPLICATION->SetAdditionalCSS($templateFolder."/js/jquery.fancybox.css");
}
?>


<script type="text/javascript"> jQuery(function($){
	var max_col_height = 0; // ������������ ������, ������������� 0
	$('.is-title').each(function(){ // ���� "��� ������ �� �������"
		if ($(this).height() > max_col_height) { // ���� ������ ������� ������ �������� ������������ ������,
			max_col_height = $(this).height(); // �� ��� ���� ���������� ����� ������������ �������
		}
	});
	$('.is-title').height(max_col_height); // ������������� ������ ������ ������� ������ �������� ������������ ������
});
</script>
