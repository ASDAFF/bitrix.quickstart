<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("������������ ������");
?>
<div class="bx_page">
	<p class="b-block-right">� ������ �������� �� ������ ��������� ������� ��������� �������, ��� ���������� ����� �������, ����������� ��� �������� ������ ����������, � ����� ����������� �� ������� � ������ �������������� ��������. </p>
	<div class="b-block">
		<div class="b-form__fieldset__caption">������ ����������</div>
		<a href="#SITE_DIR#personal/profile/">�������� ��������������� ������</a>
	</div>
	<div class="b-block">
		<div class="b-form__fieldset__caption">������</div>
		<a href="#SITE_DIR#personal/order/">������������ � ���������� �������</a><br/>
		<a href="#SITE_DIR#personal/cart/">���������� ���������� �������</a><br/>
		<a href="#SITE_DIR#personal/order/?filter_history=Y">���������� ������� �������</a><br/>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
